<?php
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../models/database.php';

class AuthController {
    public function login() {
        require_once __DIR__ . '/../views/login.php'; // โหลดหน้า login
    }
    public function register() {
        require_once __DIR__ . '/../views/register.php';
    }

    public function store() {
        global $pdo; // ดึงตัวแปรฐานข้อมูล

        $name = $_POST["name"];
        $username = $_POST["username"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

        // ตรวจสอบว่าชื่อและชื่อผู้ใช้มีอยู่แล้วหรือไม่
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR name = ?");
        $stmt->execute([$username, $name]);
        $userExists = $stmt->fetchColumn();
        
        if ($userExists > 0) {
            echo "<script>alert('❌ ชื่อหรือชื่อผู้ใช้นี้มีอยู่แล้ว กรุณาใช้ข้อมูลอื่น'); window.history.back();</script>";
            exit;
        }
    
        // ถ้าไม่ซ้ำ ให้เพิ่มข้อมูลลงฐานข้อมูล
        $stmt = $pdo->prepare("INSERT INTO users (name, username, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $username, $password])) {
            echo "<script>alert('✅ สมัครสมาชิกสำเร็จ!'); window.location.href='/login';</script>";
        } else {
            echo "<script>alert('❌ เกิดข้อผิดพลาดในการสมัครสมาชิก'); window.history.back();</script>";
        }
    }
    
    public function authenticate() {
        global $pdo;
    
        $username = $_POST["username"];
        $password = $_POST["password"];
    
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
    
        if ($user && password_verify($password, $user["password"])) {
            session_start();
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
    
            header("Location: /menu"); // ✅ เปลี่ยนเส้นทางไปที่เมนู
            exit;
        } else {
            echo "<script>alert('❌ ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง กรุณาลองอีกครั้ง'); window.history.back();</script>";
        }
    }
}
?>