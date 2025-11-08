<?php
require_once __DIR__ . '/../models/database.php';

class EventController {
    public function store() {
        global $pdo;
        session_start();
        if (!isset($_SESSION["user_id"])) {
            header("Location: /login");
            exit;
        }

        $event_name = $_POST["event_name"];
        $event_description = $_POST["event_description"];
        $event_date = $_POST["event_date"];

        // จัดการอัปโหลดรูปภาพ
        $imageNames = [];
        if (!empty($_FILES['event_images']['name'][0])) {
            foreach ($_FILES['event_images']['tmp_name'] as $index => $tmp_name) {
                $fileName = time() . "_" . basename($_FILES['event_images']['name'][$index]);
                $targetPath = "uploads/" . $fileName;
                
                if (move_uploaded_file($tmp_name, $targetPath)) {
                    $imageNames[] = $fileName;
                }
            }
        }

        // แปลงชื่อไฟล์เป็น JSON เก็บในฐานข้อมูล
        $imageData = json_encode($imageNames);

        // บันทึกข้อมูลกิจกรรมลงฐานข้อมูล
        $stmt = $pdo->prepare("INSERT INTO events (name, description, date, images, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$event_name, $event_description, $event_date, $imageData, $_SESSION["user_id"]]);

        header("Location: /creator/events");
        exit;
    }

    public function listEvents() {   
        global $pdo;   
        require_once __DIR__ . "/../views/events.php";
    }
    

    public function edit() {
        global $pdo;
        session_start();
        
        if (!isset($_SESSION["user_id"])) {
            header("Location: /login");
            exit;
        }

        $event_id = $_GET["id"];

        // ตรวจสอบว่าผู้ใช้มีสิทธิ์แก้ไขกิจกรรมนี้หรือไม่
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND created_by = ?");
        $stmt->execute([$event_id, $_SESSION["user_id"]]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            echo "กิจกรรมนี้ไม่มีอยู่ หรือคุณไม่มีสิทธิ์แก้ไข";
            exit;
        }

        // แสดงฟอร์มแก้ไข
        require_once __DIR__ . '/../views/edit_event.php';
    }

    // ✅ 2. ฟังก์ชันอัปเดตข้อมูลกิจกรรม
    public function update() {
        global $pdo;
        session_start();

        if (!isset($_SESSION["user_id"])) {
            header("Location: /login");
            exit;
        }

        $event_id = $_POST["event_id"];
        $event_name = $_POST["event_name"];
        $event_description = $_POST["event_description"];
        $event_date = $_POST["event_date"];
        $existing_images = json_decode($_POST["existing_images"], true) ?? [];

        // ตรวจสอบว่าผู้ใช้มีสิทธิ์แก้ไขกิจกรรมนี้หรือไม่
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND created_by = ?");
        $stmt->execute([$event_id, $_SESSION["user_id"]]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            echo "กิจกรรมนี้ไม่มีอยู่ หรือคุณไม่มีสิทธิ์แก้ไข";
            exit;
        }

        // ✅ ลบรูปภาพที่ถูกเลือก
        if (!empty($_POST["delete_images"])) {
            foreach ($_POST["delete_images"] as $delete_img) {
                $file_path = __DIR__ . "/../uploads/$delete_img";
                if (file_exists($file_path)) {
                    unlink($file_path); // ลบไฟล์ออกจากเซิร์ฟเวอร์
                }
                // เอารูปออกจากอาร์เรย์
                $existing_images = array_filter($existing_images, fn($img) => $img !== $delete_img);
            }
        }

        // อัปโหลดรูปภาพใหม่
        if (!empty($_FILES['event_images']['name'][0])) {
            foreach ($_FILES['event_images']['tmp_name'] as $index => $tmp_name) {
                $fileName = time() . "_" . basename($_FILES['event_images']['name'][$index]);
                $targetPath = "uploads/" . $fileName;
                
                if (move_uploaded_file($tmp_name, $targetPath)) {
                    $existing_images[] = $fileName;
                }
            }
        }

        // แปลงชื่อไฟล์เป็น JSON เพื่อเก็บในฐานข้อมูล
        $imageData = json_encode($existing_images);

        // อัปเดตข้อมูลกิจกรรม
        $stmt = $pdo->prepare("UPDATE events SET name = ?, description = ?, date = ?, images = ? WHERE id = ? AND created_by = ?");
        $stmt->execute([$event_name, $event_description, $event_date, $imageData, $event_id, $_SESSION["user_id"]]);

        header("Location: /creator/events");
        exit;
    }

    public function delete() {
        session_start();
        if (!isset($_SESSION["user_id"])) {
            header("Location: /login");
            exit;
        }

        global $pdo;

        // ตรวจสอบว่ามี ID ถูกส่งมาหรือไม่
        if (!isset($_POST["id"])) {
            echo "❌ ไม่พบ ID ของกิจกรรม";
            exit;
        }
        

        $event_id = $_POST["id"];
        $user_id = $_SESSION["user_id"];

        // ตรวจสอบว่ากิจกรรมเป็นของผู้ใช้หรือไม่
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND created_by = ?");
        $stmt->execute([$event_id, $user_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$event) {
            echo "❌ คุณไม่มีสิทธิ์ลบกิจกรรมนี้";
            exit;
        }

        // ลบรูปภาพจากเซิร์ฟเวอร์
        $images = json_decode($event["images"], true);
        if (!empty($images)) {
            foreach ($images as $img) {
                $file_path = __DIR__ . "/../uploads/$img";
                if (file_exists($file_path)) {
                    unlink($file_path); // ลบไฟล์
                }
            }
        }

        // ลบข้อมูลผู้เข้าร่วมก่อน
        $stmt = $pdo->prepare("DELETE FROM participants WHERE event_id = ?");
        $stmt->execute([$event_id]);

        // ลบกิจกรรม
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$event_id]);

        // Redirect กลับไปหน้าหลัก
        header("Location: /creator/events");
        exit;
    }    
}
?>
