<?php
session_start();
require_once __DIR__ . '/../models/database.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: /login");
    exit;
}

$user_id = $_GET["id"] ?? null;

if (!$user_id) {
    echo "ไม่พบข้อมูลผู้ใช้";
    exit;
}

// ดึงกิจกรรมที่ผู้ใช้สร้าง
$stmt = $pdo->prepare("
    SELECT e.*, u.name AS creator_name 
    FROM events e
    JOIN users u ON e.created_by = u.id
    WHERE e.created_by = ?
    ORDER BY e.date DESC
");
$stmt->execute([$user_id]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>กิจกรรมของผู้ใช้</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    @keyframes flicker {
        0% { opacity: 0.2; }
        50% { opacity: 1; }
        100% { opacity: 0.2; }
    }

    @keyframes colorChange {
        0% { color: #ff0080; }   
        25% { color: #ffcc00; }  
        50% { color: #00ffcc; }  
        75% { color: #6600ff; }  
        100% { color: #ff0080; }
    }

    /* เอฟเฟกต์ไฟกระพริบ */
    .light {
        position: fixed;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255, 0, 150, 0.8);
        animation: flicker 2s infinite alternate;
        filter: blur(10px);
        z-index: -1;
    }

    /* สร้างไฟหลายดวง */
    .light:nth-child(1) { top: 10%; left: 20%; }
    .light:nth-child(2) { top: 50%; left: 80%; background: rgba(0, 255, 150, 0.8); }
    .light:nth-child(3) { top: 70%; left: 30%; background: rgba(0, 150, 255, 0.8); }
    .light:nth-child(4) { top: 20%; left: 60%; background: rgba(255, 255, 0, 0.8); }

    /* จัดสไตล์หน้าเว็บ */
    body {
        background: black;
        color: white;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .card-body{
        background-color: rgba(240, 24, 186, 0.85);
    }
    .card {
        background-color: rgba(0, 0, 0, 0.85);
        color: white;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
        border-radius: 12px;
        padding: 20px;
        max-width: 800px; 
        width: 100%;
    }
</style>
<body>
    <!-- ไฟกระพริบที่แสดงบนพื้นหลัง -->
    <div class="light"></div>
    <div class="light"></div>
    <div class="light"></div>
    <div class="light"></div>

    <div class="container">
        <h3 class="mb-4 mt-3 text-center">📋 กิจกรรมที่ผู้ใช้สร้าง</h3>
        <div class="row">
            <?php foreach ($events as $event): ?>
                <?php
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM participants WHERE event_id = ? AND status = 'approved'");
                $stmt->execute([$event['id']]);
                $participant_count = $stmt->fetchColumn();

                $images = json_decode($event['images'], true);
                if (empty($images) || !is_array($images)) {
                    $images = ['default.jpg'];
                }
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <!-- Bootstrap Carousel -->
                        <div id="carousel-<?= $event['id'] ?>" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php foreach ($images as $index => $img): ?>
                                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                        <img src="<?= htmlspecialchars('/uploads/' . $img) ?>" class="d-block w-100" style="height: 250px; object-fit: cover;">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($images) > 1): ?>
                                <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?= $event['id'] ?>" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?= $event['id'] ?>" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title" style="animation: colorChange 3s infinite alternate;"><?= htmlspecialchars($event['name']) ?></h5>
                            <p class="card-text"><strong>ผู้สร้าง: </strong>👤 <?= htmlspecialchars($event['creator_name']) ?></p>
                            <p class="card-textarea">รายละเอียดงาน:<?= htmlspecialchars($event['description']) ?></p>
                            <p class="card-text">งานเริ่มวันที่ 📅: <?= htmlspecialchars($event['date']) ?></p>
                            <p class="card-text">มีผู้เข้าร่วมแล้ว: <?= $participant_count ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div> 

        <div class="btn-group d-flex flex-wrap gap-2 mb-3" role="group"> 
            <a href="/user-list" class="btn btn-secondary">🔙 กลับไปที่รายชื่อผู้ใช้</a>
            <a href="/logout" class="btn btn-danger">🚪 Logout</a>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>

