<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: /login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>สร้างกิจกรรม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    body {
        margin: 0;
        height: 100vh;
        background-image: url('/bg/BIGBANG OT4.jpg');
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
    @keyframes colorChange {
        0% { background-color: #ff0080; }   /* ชมพูนีออน */
        25% { background-color: #ffcc00; }  /* เหลืองทอง */
        50% { background-color: #00ffcc; }  /* เขียวนีออน */
        75% { background-color: #6600ff; }  /* ม่วงไฟนีออน */
        100% { background-color: #ff0080; } /* วนกลับมาชมพู */
    }
      .card button {
        background-color: rgb(34, 231, 27);
        color: white;
        font-weight: bold;
        border-color:rgb(6, 94, 3);
        transition: transform 0.2s ease-out; 
      }
      .card button:hover {
        color: white;
        transform: scale(1.1);
        animation: colorChange 3s infinite alternate;
        text-shadow: 2px 2px 5px rgba(255, 255, 255, 0.6); /* เพิ่มเอฟเฟกต์แสงเงา */
      }
    .card a {
        color: #3333aa;
        text-decoration: none;
        font-size: 0.85rem;
        margin-top: 15px;
        display: inline-block;
        text-align: center;
        
      }
      .card a:hover {
        text-decoration: underline;
      }
  </style>
<body>
    <div class="card container" style="width: 400px; padding: 30px;">
        <h2 class="text-center">🛠️🏰 สร้างกิจกรรม</h2>
        <form action="/creator/store" method="post" enctype="multipart/form-data">
            <div class=" mt-4">
                <label class="form-label">ชื่อกิจกรรม:</label>
                <input type="text" name="event_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">รายละเอียด:</label>
                <textarea name="event_description" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">งานเริ่มวันที่:</label>
                <input type="date" name="event_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">รูปภาพกิจกรรม:</label>
                <input type="file" name="event_images[]" multiple accept="image/*" class="form-control"><br>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary w-100">✅ บันทึกกิจกรรม</button>
            </div>
        </form>

        <hr>
        <div class="row">
            <div class="col-auto" style="margin-left: 50px;"> 
                <a href="/creator/events">📋 ดูกิจกรรมของฉัน</a></div>
            <div class="col-auto"> 
                <a href="/menu">🔙 กลับไปที่เมนู</a>
            </div>
        </div>
        <a href="/logout">🚪 Logout</a>
    </div>
</body>
</html>