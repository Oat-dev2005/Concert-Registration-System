<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body{
            margin: 0;
            height: 100vh;
            background-image: url('/bg/reg_bg.jpg');
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
</head>
<body>
    <div class="card container" style="width: 400px; padding: 30px;">
    <h2>🎧 สมัครสมาชิก</h2>
        <form method="POST" action="/register/store">
            <div class="mb-3 mt-4">
                <label class="form-label">Name:</label>
                <input type="text" class="form-control" placeholder="ชื่อของผู้ใช้" name="name"  required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username:</label>
                <input type="text" class="form-control"  placeholder="username" name="username" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password:</label>
                <input type="password" class="form-control"  placeholder="Password" name="password" required><br>
            </div>                
            <button type="submit" class="btn btn-primary w-100">สมัครสมาชิก 🎶</button>
            <div class="mt-2 text-center">
                <a href="/login">เป็นสมาชิกอยู่แล้ว</a> 
            </div>
        </form>
    </div>
</body>
</html>
