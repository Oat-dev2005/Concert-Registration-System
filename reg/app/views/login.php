<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    body{
        margin: 0;
        height: 100vh;
        background-image: url('/bg/concert_login.jpg');
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
      .card button {
        background-color: white;
        color: #4c4ca7;
        font-weight: bold;
      }
      .card button:hover {
        background-color: #3333aa;
        color: white;
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
    <?php if (isset($_SESSION["error"])): ?>
        <p style="color:red;"><?php echo $_SESSION["error"]; unset($_SESSION["error"]); ?></p>
    <?php endif; ?>
    <div class="card container" style="width: 400px; padding: 30px;">
        <h2>🥳 เข้าสู่ระบบ</h2>
        <form method="POST" action="/login/authenticate">
            <div class="mb-3 mt-3">
                <label class="form-label">Username:</label>
                <input type="text" class="form-control" placeholder="Enter username" name="username" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Password:</label>
                <input type="password" class="form-control" placeholder="Enter password" name="password" required>
            </div>
            <div class="mb-3">
                <a href="/register">ยังไม่ได้สมัครสมาชิก?</a> 
            </div>
            <button type="submit" class="btn btn-primary w-100">เข้าสู่ระบบ 🎟️</button>
        </form>
    </div>
</body>
</html>
