<?php
session_start();
require_once __DIR__ . '/../models/database.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: /login");
    exit;
}

$userId = $_SESSION["user_id"];
$search = $_GET["search"] ?? "";

// ค้นหาผู้ใช้จากชื่อหรือ username +ดึงจำนวนกิจกรรมที่สร้าง
$query = "
    SELECT u.id, u.name, u.username, 
           (SELECT COUNT(*) FROM events e WHERE e.created_by = u.id) AS event_count
    FROM users u
    WHERE u.id != ?
";
$params = [$userId];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR username LIKE ? )";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    .card a {
        color: #3333aa;
        text-decoration: none;
        font-size: 0.85rem;
        display: inline-block;
        text-align: center;
    }
    .card a:hover {
        text-decoration: underline;
    }
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
        background-color: rgba(0, 0, 0, 0.8); /* ทำให้โปร่งแสงเล็กน้อย */
        color: white;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5); /* ใส่เงาให้ดูโดดเด่น */
        border-radius: 10px;
        padding: 20px;
    }
</style>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title mb-4">รายชื่อผู้ใช้ทั้งหมด</h2>
                <form method="GET" action="/user-list" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="🔍 ค้นหาผู้ใช้..." value="<?= htmlspecialchars($search) ?>">
                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ลำดับที่</th>
                                <th>ชื่อ</th>
                                <th>ชื่อผู้ใช้</th>
                                <th>จำนวนกิจกรรมที่สร้าง</th>
                                <th>กิจกรรมของผู้ใช้</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $index => $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($index + 1); ?></td>
                                    <td><?= htmlspecialchars($user['name']); ?></td>
                                    <td><?= htmlspecialchars($user['username']); ?></td>
                                    <td>
                                        <span class="badge bg-success"><?= $user['event_count'] ?></span>
                                    </td>
                                    <td>
                                        <a href="/user_events?id=<?= htmlspecialchars($user['id']) ?>" class="btn btn-info btn-sm">ดูรายละเอียด</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-auto d-flex gap-4 justify-content-center mt-4">
        <a href="/menu" class="text-white">🔙 กลับไปที่เมนู</a>
        <a href="/logout" class="text-white">🚪 Logout</a>
    </div>
</body>
</html>

