<?php
session_start();
require_once __DIR__ . '/../models/database.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: /login");
    exit;
}

$user_id = $_SESSION["user_id"];

// ดึงเฉพาะกิจกรรมที่ผู้ใช้สร้าง
$stmt = $pdo->prepare("SELECT * FROM events WHERE created_by = ? ORDER BY date DESC");
$stmt->execute([$user_id]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>กิจกรรมของฉัน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    .background-radial {
        background: radial-gradient(circle, #ff0000, #800080, #000000);
    }
    .btn-group form {
        display: inline;
    }

    .btn-group button {
        width: 100%;
        gap: 10px;
    }
    @keyframes colorChange {
        0% { color: #ff0080; }   /* ชมพูนีออน */
        25% { color: #ffcc00; }  /* เหลืองทอง */
        50% { color: #00ffcc; }  /* เขียวนีออน */
        75% { color: #6600ff; }  /* ม่วงไฟนีออน */
        100% { color: #ff0080; } /* วนกลับมาชมพู */
    }
</style>
<body class="background-radial">
<div class="container mt-5">
    <h3 class="mb-4" style="animation: colorChange 3s infinite alternate;">📋 กิจกรรมที่สร้าง</h3>
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
                        <h5 class="card-title"><?= htmlspecialchars($event['name']) ?></h5>
                        <p class="card-textarea">รายละเอียดงาน: <?= htmlspecialchars($event['description']) ?></p>
                        <p class="card-text">งานเริ่มวันที่ 📅: <?= htmlspecialchars($event['date']) ?></p>
                        <p class="card-text">มีผู้เข้าร่วมแล้ว: <?= $participant_count ?></p>
                        <div class="btn-group d-flex flex-wrap gap-2" role="group">
                            <a href="/creator/edit?id=<?= htmlspecialchars($event['id']) ?>" class="btn btn-warning">✏️ แก้ไข</a>
                            <form action="/creator/delete" method="post" style="display:inline;" onsubmit="return confirm('คุณแน่ใจหรือไม่?');">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($event['id']) ?>">
                                <button type="submit" class="btn btn-danger">🗑 ลบ</button>
                            </form>
                            <a href="/creator/join_requests?id=<?= htmlspecialchars($event['id']) ?>" class="btn btn-primary">👥 จัดการผู้เข้าร่วม</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <hr style="color: #00eaff;">
    <div class="btn-group d-flex flex-wrap gap-2" role="group">
        <a href="/creator" class="btn btn-success">➕ สร้างกิจกรรม</a>
        <a href="/menu" class="btn btn-secondary">🔙 กลับไปที่เมนู</a>
        <a href="/logout" class="btn btn-danger">🚪 Logout</a>
    </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>
