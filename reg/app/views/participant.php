<?php
session_start();
require_once __DIR__ . '/../models/database.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: /login");
    exit;
}

$user_id = $_SESSION["user_id"];
$search = $_GET["search"] ?? "";

// ดึงข้อมูลกิจกรรม + OTP + เวลาหมดอายุ
$query = "SELECT e.id, e.name, e.description, e.date, e.images, u.username AS creator,
    (SELECT status FROM participants p WHERE p.event_id = e.id AND p.user_id = ?) AS status,
    (SELECT otp FROM participants p WHERE p.event_id = e.id AND p.user_id = ?) AS otp,
    (SELECT otp_expiry FROM participants p WHERE p.event_id = e.id AND p.user_id = ?) AS otp_expiry
    FROM events e
    JOIN users u ON e.created_by = u.id";

$params = [$user_id, $user_id, $user_id]; // ใช้ user_id สามครั้งสำหรับ OTP, Status, Expiry

if (!empty($search)) {
    $query .= " WHERE e.name LIKE ? OR e.date LIKE ?";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY e.date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ผู้เข้าร่วมกิจกรรม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function startCountdown(expiryTimestamp, eventId) {
            let countdownElement = document.getElementById(`countdown-${eventId}`);
            let buttonElement = document.getElementById(`otp-button-${eventId}`);
            let now = Math.floor(Date.now() / 1000); // เวลาปัจจุบันในหน่วยวินาที
            let remaining = expiryTimestamp - now;

            function updateCountdown() {
                let minutes = Math.floor(remaining / 60);
                let seconds = remaining % 60;
                countdownElement.innerText = `⌛ หมดอายุใน ${minutes}:${seconds < 10 ? '0' : ''}${seconds} นาที`;

                if (remaining <= 0) {
                    clearInterval(timer);
                    countdownElement.innerText = "❌ OTP หมดอายุ!";
                    buttonElement.innerText = "🔄ขอ OTP ใหม่อีกครั้ง";
                    buttonElement.onclick = () => createNewOTP(eventId);
                } else {
                    remaining--;
                }
            }

            let timer = setInterval(updateCountdown, 1000);
            updateCountdown();
        }

        function showOTP(otp) {
            alert(`🔑 OTP ของคุณคือ: ${otp}`);
        }

        function createNewOTP(eventId) {
            if (confirm("คุณต้องการสร้าง OTP ใหม่หรือไม่?")) {
                window.location.href = `/participant/generate_otp?event_id=${eventId}`;
            }
        }
    </script>
</head>
<style>
    .background-radial {
        background: radial-gradient(circle, #0f0c29, #302b63, #24243e);
    }
    @keyframes colorChange {
        0% { color: #ff0080; }   /* ชมพูนีออน */
        25% { color: #ffcc00; }  /* เหลืองทอง */
        50% { color: #00ffcc; }  /* เขียวนีออน */
        75% { color: #6600ff; }  /* ม่วงไฟนีออน */
        100% { color: #ff0080; } /* วนกลับมาชมพู */
    }
    .btn-group form {
        display: inline;
    }

    .btn-group button {
        width: 100%;
        gap: 10px;
    }
</style>
<body class="background-radial">
    <div class="container mt-5">
        <h3 class="mb-4" style="animation: colorChange 3s infinite alternate;">🎉 กิจกรรมที่เข้าร่วม</h3>
        <form method="GET" action="/participant" class="mb-3">
            <div class="input-group mb-5">
                <input type="text" name="search" class="form-control" placeholder="🔍 ค้นหากิจกรรม..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit" class="btn btn-primary">ค้นหา</button>
            </div>
        </form>
        <div class="row">
        <?php foreach ($events as $event): ?>
            <?php
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM participants WHERE event_id = ? AND status = 'approved'");
            $stmt->execute([$event['id']]);
            $participant_count = $stmt->fetchColumn();

            $images = json_decode($event['images'], true);
            if (empty($images) || !is_array($images)) {
                $images = ['default.jpg']; // ใช้รูปภาพเริ่มต้นหากไม่มีภาพ
            }
            ?>

            <div class="col-md-4 mb-4">
                <div class="card" style="background-color: #2a0d4a; border-radius: 20px; color: white;">
                    <!-- Bootstrap Carousel -->
                    <div id="carousel-<?= $event['id'] ?>" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner" style="border-top-left-radius: 20px; border-top-right-radius: 20px; height: 250px; object-fit: cover;">
                            <?php foreach ($images as $index => $img): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                    <img src="<?= htmlspecialchars('/uploads/' . $img) ?>" class="d-block w-100" style="height: 250px; object-fit: cover;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- ปุ่มเลื่อนไปภาพถัดไป -->
                        <?php if (count($images) > 1): ?>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?= $event['id'] ?>" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?= $event['id'] ?>" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="card-body">
                        <h5 class="card-title" style="animation: colorChange 5s infinite alternate;"><?= htmlspecialchars($event['name']) ?></h5>
                        <p class="card-text">รายละเอียดงาน: <?= htmlspecialchars($event['description']) ?></p>
                        <p class="card-text">งานเริ่มวันที่: <?= htmlspecialchars($event['date']) ?></p>
                        <p class="card-text">ชื่อผู้จัดงาน: <?= htmlspecialchars($event['creator']) ?></p>
                        <p class="card-text">มีผู้เข้าร่วมแล้ว: <?= $participant_count ?></p>
                        <p class="card-text">
                            สถานะ: 
                            <?php if ($event['status'] == "approved"): ?>
                                ✅ อนุมัติแล้ว
                            <?php elseif ($event['status'] == "pending"): ?>
                                ⏳ รออนุมัติ
                            <?php elseif ($event['status'] == "rejected"): ?>
                                ❌ ไม่ได้รับการอนุมัติ
                            <?php else: ?>
                                ‼️ ยังไม่ได้สมัคร
                            <?php endif; ?>
                        </p>

                        <?php if (!$event["status"]): ?>
                            <form action="/participant/join" method="post">
                                <input type="hidden" name="event_id" value="<?= $event["id"] ?>">
                                <button type="submit" class="btn btn-success">✍️ สมัครเข้าร่วม</button>
                            </form>
                        <?php elseif ($event["status"] == "pending"): ?>
                            <?php if (!empty($event["otp"]) && time() < $event["otp_expiry"]): ?>
                                <button id="otp-button-<?= $event["id"] ?>" onclick="showOTP('<?= $event["otp"] ?>')" class="btn btn-warning">🔑 แสดง OTP</button>
                                <p id="countdown-<?= $event["id"] ?>"></p>
                                <script>startCountdown(<?= $event["otp_expiry"] ?>, <?= $event["id"] ?>);</script>
                            <?php else: ?>
                                <button id="otp-button-<?= $event["id"] ?>" onclick="createNewOTP(<?= $event["id"] ?>)" class="btn btn-danger">🔄 ขอ OTP ใหม่</button>
                                <p id="countdown-<?= $event["id"] ?>">❌ OTP หมดอายุ!</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <hr style="color: #00eaff;">
    <div class="btn-group d-flex flex-wrap gap-2 mb-3" role="group">
        <a href="/menu" class="btn btn-secondary">🔙 กลับไปที่เมนู</a>
        <a href="/logout" class="btn btn-danger">🚪 Logout</a>
    </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>
