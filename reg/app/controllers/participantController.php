<?php
require_once __DIR__ . '/../models/database.php';

class ParticipantController {
    public function join() {
        session_start();
        global $pdo;

        if (!isset($_SESSION["user_id"])) {
            header("Location: /login");
            exit;
        }

        $event_id = $_POST["event_id"];
        $user_id = $_SESSION["user_id"];

        // ตรวจสอบว่าผู้ใช้สมัครไปแล้วหรือยัง
        $stmt = $pdo->prepare("SELECT * FROM participants WHERE event_id = ? AND user_id = ?");
        $stmt->execute([$event_id, $user_id]);
        if ($stmt->fetch()) {
            echo "คุณสมัครเข้าร่วมกิจกรรมนี้แล้ว!";
            exit;
        }

        // สร้าง OTP 6 หลักแบบสุ่ม
        $otp = rand(100000, 999999);
        $otp_expiry = time() + 60; // ✅ หมดอายุใน 1 นาที

        // ✅ สมัครเข้าร่วมกิจกรรมและบันทึก OTP + เวลาหมดอายุ
        $stmt = $pdo->prepare("INSERT INTO participants (event_id, user_id, status, otp, otp_expiry) VALUES (?, ?, 'pending', ?, ?)");
        $stmt->execute([$event_id, $user_id, $otp, $otp_expiry]);

        header("Location: /participant");
        exit;
    }

    public function approve() {
        session_start();
        global $pdo;

        if (!isset($_SESSION["user_id"])) {
            header("Location: /login");
            exit;
        }

        $participant_id = $_GET["id"];
        // อนุมัติและล้าง OTP
        $stmt = $pdo->prepare("UPDATE participants SET status = 'approved', otp = NULL WHERE id = ?");
        $stmt->execute([$participant_id]);

        header("Location: /creator");
        exit;
    }

    public function reject() {
        session_start();
        global $pdo;

        if (!isset($_SESSION["user_id"])) {
            header("Location: /login");
            exit;
        }

        $participant_id = $_GET["id"];
        $stmt = $pdo->prepare("UPDATE participants SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$participant_id]);

        header("Location: /creator/events");
        exit;
    }

    public function checkIn() {
        session_start();
        global $pdo;
    
        if (!isset($_SESSION["user_id"])) {
            header("Location: /login");
            exit;
        }
    
        $event_id = $_POST["event_id"] ?? null;
        $user_id = $_SESSION["user_id"];
        $input_otp = $_POST["otp"] ?? "";
    
        if (!$event_id || empty($input_otp)) {
            echo "❌ ข้อมูลไม่ครบถ้วน";
            exit;
        }
    
        // ตรวจสอบ OTP จากฐานข้อมูล
        $stmt = $pdo->prepare("SELECT otp, expiry FROM event_otps WHERE event_id = ?");
        $stmt->execute([$event_id]);
        $otpData = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$otpData || time() > $otpData["expiry"]) {
            echo "❌ OTP หมดอายุหรือไม่ถูกต้อง";
            exit;
        }
    }

    public function processCheckin()
    {
        session_start();
        global $pdo;
    
        $participant_id = $_POST["participant_id"] ?? null;
        $event_id = $_POST["event_id"] ?? null;
        $otp = $_POST["otp"] ?? null;
    
        if (!$participant_id || !$event_id || !$otp) {
            echo "<script>alert('❌ ข้อมูลไม่ครบถ้วน!'); window.history.back();</script>";
            exit;
        }
    
        // ✅ ตรวจสอบ OTP, สถานะ และ เวลาหมดอายุ
        $stmt = $pdo->prepare("SELECT otp, otp_expiry, status FROM participants WHERE id = ?");
        $stmt->execute([$participant_id]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$participant) {
            echo "<script>alert('❌ ไม่พบข้อมูลผู้เข้าร่วม!'); window.history.back();</script>";
            exit;
        }
    
        if ($participant["status"] !== "pending") {
            echo "<script>alert('❌ ไม่สามารถใช้ OTP ได้ สถานะไม่ถูกต้อง!'); window.history.back();</script>";
            exit;
        }

        // ✅ เช็คว่า OTP หมดอายุหรือยัง
        if (time() > $participant["otp_expiry"]) {
            echo "<script>alert('❌ OTP หมดอายุแล้ว!'); window.history.back();</script>";
            exit;
        }
    
        if ($participant["otp"] === $otp) {
            // อัปเดตสถานะเป็น "approved" และล้าง OTP
            $updateStmt = $pdo->prepare("UPDATE participants SET status = 'approved', otp = NULL WHERE id = ?");
            $updateStmt->execute([$participant_id]);
    
            $_SESSION["message"] = "✅ เช็คชื่อสำเร็จ! อนุมัติเรียบร้อย";
            header("Location: /creator/join_requests?id=" . $event_id);
            exit;
        } else {
            $_SESSION["message"] = "❌ OTP ไม่ถูกต้อง!";
            header("Location: /participant/checkin?participant_id=$participant_id&event_id=$event_id");
            exit;
        }
    }

    public function generateOtp() {
        session_start();
        global $pdo;
    
        if (!isset($_SESSION["user_id"])) {
            header("Location: /login");
            exit;
        }
    
        $event_id = $_GET["event_id"];
        $user_id = $_SESSION["user_id"];
        
        // สร้าง OTP ใหม่
        $otp = rand(100000, 999999);
        $expiry = time() + 60; // OTP มีอายุ 1 นาที
    
        // อัปเดต OTP ในฐานข้อมูล
        $stmt = $pdo->prepare("UPDATE participants SET otp = ?, otp_expiry = ? WHERE event_id = ? AND user_id = ?");
        $stmt->execute([$otp, $expiry, $event_id, $user_id]);
    
        header("Location: /participant");
        exit;
    }
    
}
?>
