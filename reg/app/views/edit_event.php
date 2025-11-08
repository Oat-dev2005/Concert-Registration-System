<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขกิจกรรม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<style>
    body {
        margin: 0;
        height: 100vh;
        
        background-image: url('/bg/edit_bg.jpg');
        
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }
    .card input, .card button {
        border-radius: 10px; /* ปุ่มและช่องกรอกข้อมูลมีมุมโค้ง */
      }
      .card button {
        color: white;
        font-weight: bold;
        border-color:rgba(15, 15, 15, 0);
        transition: transform 0.2s ease-out; 
      }
      .card button:hover {
        
        transform: scale(1.1);
        
        text-shadow: 2px 2px 5px rgba(255, 255, 255, 0.6); /* เพิ่มเอฟเฟกต์แสงเงา */
      }
    .card a {
        color: #3333aa;
        text-decoration: none;
        font-size: 0.85rem;
        margin-top: 15px;
        display: inline-block;
      }
      .card a:hover {
        text-decoration: underline;
      }
  </style>
<body>
    <div class="card container" style="width: 400px; padding: 30px; margin: top 300px;">
        <h2 class="text-center">🔄 แก้ไขกิจกรรม</h2>
        <form action="/creator/update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
            <div class=" mt-4">
                <label class="form-label">ชื่อกิจกรรม:</label>
                <input type="text" name="event_name" class="form-control" value="<?php echo htmlspecialchars($event['name']); ?>" required>
            </div>
                <label class="form-label">รายละเอียดกิจกรรม:</label>
                <textarea name="event_description" class="form-control" required><?php echo htmlspecialchars($event['description']); ?></textarea>
            <div class="mb-3">
                <label class="form-label">วันที่จัดกิจกรรม:</label>
                <input type="date" name="event_date" class="form-control" value="<?php echo htmlspecialchars($event['date']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">รูปภาพปัจจุบัน:</label><br>
            <?php
            $images = json_decode($event['images'], true);
            if (!empty($images)) {
                foreach ($images as $img) {
                    echo "<div style='display:inline-block; text-align:center; margin:5px;'>
                        <img src='/uploads/$img' width='100' height='100' style='display:block;'>
                        <input type='checkbox' name='delete_images[]' value='$img'> ลบ
                    </div>";
                }
            } else {
                echo "ไม่มีรูปภาพ";
            }
            ?>
            </div>
            <div class="mb-3">
                <input type="hidden" name="existing_images" value='<?php echo json_encode($images); ?>'>
            </div>
            <div class="mb-3">
                <label class="form-label">อัปโหลดรูปภาพใหม่:</label>
                <input type="file" name="event_images[]" class="form-control" multiple><br>
            </div>
            <button type="submit" class="btn btn-primary w-100">💾 บันทึกการเปลี่ยนแปลง</button>
            <div class="text-center mt-3">
                <a href="/creator/events">⬅ กลับ</a>
            </div>
        </form>
    </div>
</body>
</html>
