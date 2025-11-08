<?php
session_start();
require_once __DIR__ . '/../models/database.php';

if (!isset($_GET["participant_id"]) || !isset($_GET["event_id"])) {
    echo "ข้อมูลไม่ถูกต้อง!";
    exit;
}

$participant_id = $_GET["participant_id"];
$event_id = $_GET["event_id"];

// ดึงข้อมูลผู้เข้าร่วม
$stmt = $pdo->prepare("SELECT u.name, p.otp FROM participants p JOIN users u ON p.user_id = u.id WHERE p.id = ?");
$stmt->execute([$participant_id]);
$participant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$participant) {
    echo "ไม่พบข้อมูลผู้เข้าร่วม!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เช็คชื่อผู้เข้าร่วม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    body{
        margin: 0;
        height: 100vh;
        background-image: url('/uploads/1740931274_bg_base.jpg');
        display: flex; /* ใช้ Flexbox */
        justify-content: center; /* จัดให้อยู่ตรงกลางแนวนอน */
        align-items: center; /* จัดให้อยู่ตรงกลางแนวตั้ง */
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }
    .card input, .card button {
        border-radius: 10px; /* ปุ่มและช่องกรอกข้อมูลมีมุมโค้ง */
      }
</style>
<body>
    <div class="card container" style="width: 400px; padding: 30px;">
        <h2>🔑 เช็คชื่อผู้เข้าร่วม: <?= htmlspecialchars($participant["name"]) ?></h2>

        <form method="POST" action="/participant/process_checkin">
        <div class="mb-3 mt-3">    
            <input type="hidden" name="participant_id" value="<?= $participant_id ?>">
        </div>    
            <input type="hidden" name="event_id" value="<?= $event_id ?>">
            <div class="mb-2">
                <label>🔑 กรอก OTP:</label>
                <input type="text" name="otp" required>
            </div>
            <div class="text-center mt-3">
                <button type="submit">✅ ยืนยัน</button>
            </div>
        </form>
    </div>
</body>
</html>
