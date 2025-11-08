<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: /login");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    body{
        margin: 0;
        height: 100vh;        
        display: flex; /* ใช้ Flexbox */
        justify-content: center; /* จัดให้อยู่ตรงกลางแนวนอน */
        align-items: center; /* จัดให้อยู่ตรงกลางแนวตั้ง */
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }
    @keyframes gradientBG {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .animated-bg {
        background: linear-gradient(45deg, #ff0080, #ffcc00, #00ffcc, #6600ff);
        background-size: 400% 400%;
        animation: gradientBG 8s ease infinite;
        height: 100vh;
        width: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: -1;
    }
     .card a {
     background: rgba(255, 255, 255, 0.8); /* เพิ่มความโปร่งแสง */
     backdrop-filter: blur(10px); /* ทำให้พื้นหลังเบลอ */
     color: #4c4ca7;
     font-weight: bold;
     text-decoration: none;
     font-size: 0.85rem;
     margin-top: 15px;
     border-radius: 10px;
     display: inline-block;
     text-align: center;
     transition: transform 0.2s ease-out;        
      }
      .card a:hover {
        background-color: #3333aa;
        color: white;
        transform: scale(1.2);
      }
      .custom-btn {
        width: 235px; /* ปรับขนาดปุ่มให้กว้างขึ้น */
        font-size: 2rem; /* ปรับขนาดตัวอักษรให้ใหญ่ขึ้น */
        padding: 15px; /* ปรับพื้นที่รอบตัวอักษรให้ใหญ่ขึ้น */
    }
    @keyframes colorChange {
        0% { color: #ff0080; }   /* ชมพูนีออน */
        25% { color: #ffcc00; }  /* เหลืองทอง */
        50% { color: #00ffcc; }  /* เขียวนีออน */
        75% { color: #6600ff; }  /* ม่วงไฟนีออน */
        100% { color: #ff0080; } /* วนกลับมาชมพู */
    }
    .status-text {
        font-weight: bold;
        font-size: 1.2rem;
        animation: colorChange 3s infinite alternate;
        text-shadow: 2px 2px 5px rgba(255, 255, 255, 0.6); /* เพิ่มเอฟเฟกต์แสงเงา */
    }
  </style>
<body>
<div class="animated-bg"></div>
<!--<video class="video-bg" autoplay muted loop>
        <source src="/uploads/.mp4" type="video/mp4">
    </video>-->
<div class="card container" style="width: 600px; padding: 45px;">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>🌟</h2>
    <div class="mb-3 mt-4">
      <p class="status-text">กรุณาเลือกสถานะของคุณ:</p>  
    </div>   
    <div class="row mb-2">
        <div class="col">
            <a href="/creator" class="btn btn-primary mb-2 btn-lg custom-btn">👷🛠️ ผู้จัดงาน</a>
        </div>
        <div class="col">
            <a href="/participant" class="btn btn-primary mb-2 btn-lg custom-btn">🧑‍🤝‍🧑 ผู้เข้าร่วมงาน</a>
        </div>        
    </div>
      
    <hr style="color: #00eaff;">
        <div class="col-auto d-flex justify-content-center">
            <a href="/logout" class="btn btn-primary">🚪 Logout</a>
        </div>
    </div>
</body>
</html>
