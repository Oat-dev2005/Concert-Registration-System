<?php
require_once __DIR__ . '/../models/database.php';
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: /login");
    exit;
}

$user_id = $_SESSION["user_id"];
$event_id = $_GET["id"] ?? null;

if (!$event_id) {
    echo "ไม่พบกิจกรรมที่ต้องการจัดการ!";
    exit;
}

// ดึงข้อมูลกิจกรรม
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND created_by = ?");
$stmt->execute([$event_id, $user_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    echo "คุณไม่มีสิทธิ์จัดการกิจกรรมนี้!";
    exit;
}

// ดึงรายชื่อผู้เข้าร่วมกิจกรรมที่ยังไม่อนุมัติ
$stmt = $pdo->prepare("
    SELECT p.id as participant_id, u.name as user_name, p.status
    FROM participants p
    JOIN users u ON p.user_id = u.id
    WHERE p.event_id = ? AND p.status = 'pending'
");
$stmt->execute([$event_id]);
$pending_participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการผู้เข้าร่วม - <?= htmlspecialchars($event["name"]) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <style>
        @keyframes moveGradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    body {
        background: linear-gradient(-45deg, #ff0080, #8000ff, #0000ff, #ff0000);
        background-size: 400% 400%;
        animation: moveGradient 10s ease infinite;
        color: white;
    }
    .card {
        background-color: rgba(250, 250, 250, 0.8); /* ทำให้โปร่งแสงเล็กน้อย */
        color: white;
        box-shadow: 0px 4px 10px rgba(252, 248, 248, 0.5); /* ใส่เงาให้ดูโดดเด่น */
        border-radius: 10px;
        padding: 20px;
    }
    </style>
<div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3>👥 จัดการผู้เข้าร่วมกิจกรรม: <?= htmlspecialchars($event["name"]) ?></h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ชื่อผู้เข้าร่วม</th>
                            <th>สถานะ</th>
                            <th>การจัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_participants as $participant): ?>
                        <tr>
                            <td><?= htmlspecialchars($participant["user_name"]) ?></td>
                            <td>⏳ รออนุมัติ</td>
                            <td>
                                <a href="/participant/checkin?participant_id=<?= $participant["participant_id"] ?>&event_id=<?= $event_id ?>" class="btn btn-success btn-sm">✅ อนุมัติ</a>
                                <a href="/creator/reject?id=<?= $participant["participant_id"] ?>&event_id=<?= $event_id ?>" class="btn btn-danger btn-sm">❌ ปฏิเสธ</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-center">
                <a href="/creator/events" class="btn btn-secondary">🔙 กลับไปที่ดูกิจกรรม</a>
                <a href="/menu" class="btn btn-primary">🏠 เมนูหลัก</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>