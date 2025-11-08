<?php
require_once __DIR__ . "/app/controllers/authController.php";
require_once __DIR__ . "/app/controllers/eventController.php";
require_once __DIR__ . "/app/controllers/participantController.php";

$routes = [
    "" => ["controller" => "AuthController", "method" => "login"],
    "login" => ["controller" => "AuthController", "method" => "login"],
    "login/authenticate" => ["controller" => "AuthController", "method" => "authenticate"],
    "menu" => ["view" => "menu"],
    "creator" => ["view" => "creator"],
    "creator/events" => ["controller" => "EventController", "method" => "listEvents"], // หน้าดูรายการกิจกรรม
    "creator/store" => ["controller" => "EventController", "method" => "store", "method_type" => "POST"],
    "creator/edit" => ["controller" => "EventController", "method" => "edit", "params" => ["id"]],
    "creator/update" => ["controller" => "EventController", "method" => "update", "method_type" => "POST"],
    "creator/delete" => ["controller" => "EventController", "method" => "delete", "method_type" => "POST"],
    "creator/join_requests" => ["view" => "join_requests", "params" => ["id"]],
    "participant" => ["view" => "participant"],
    "participant/join" => ["controller" => "ParticipantController", "method" => "join", "method_type" => "POST"],
    "participant/checkin" => ["view" => "checkin", "params" => ["participant_id", "event_id"]],
    "participant/process_checkin" => ["controller" => "ParticipantController", "method" => "processCheckin", "method_type" => "POST"],
    "participant/generate_otp" => ["controller" => "ParticipantController", "method" => "generateOTP"],
    "creator/approve" => ["controller" => "ParticipantController", "method" => "approve", "params" => ["id"]],
    "creator/reject" => ["controller" => "ParticipantController", "method" => "reject", "params" => ["id"]],
    "register" => ["controller" => "AuthController", "method" => "register"],
    "user-list" => ["view" => "user-list"],
    "user_events" => ["view" => "user_events"],
    "register/store" => ["controller" => "AuthController", "method" => "store"],
    "logout" => ["file" => "logout.php"],
];

$uri = trim($_SERVER["REQUEST_URI"], "/");

// แยก path ออกจาก query string
$parsed_url = parse_url($uri);
$path = $parsed_url["path"] ?? "";
$query_params = [];
parse_str($parsed_url["query"] ?? "", $query_params);

// ตรวจสอบว่าเส้นทางมีอยู่ใน Routing Table หรือไม่
if (isset($routes[$path])) {
    $route = $routes[$path];

    // ตรวจสอบ Method Type (GET หรือ POST)
    if (isset($route["method_type"]) && $_SERVER["REQUEST_METHOD"] !== $route["method_type"]) {
        http_response_code(405);
        echo "405 Method Not Allowed";
        exit;
    }

    // โหลด View
    if (isset($route["view"])) {
        require_once __DIR__ . "/app/views/{$route["view"]}.php";
        exit;
    }

    // เรียกใช้งาน Controller
    if (isset($route["controller"])) {
        $controllerName = $route["controller"];
        $method = $route["method"];
        $controller = new $controllerName();

        // ตรวจสอบว่าต้องการพารามิเตอร์หรือไม่
        if (isset($route["params"])) {
            $params = [];
            foreach ($route["params"] as $param) {
                if (!isset($query_params[$param])) {
                    http_response_code(400);
                    echo "400 Bad Request - Missing parameter: $param";
                    exit;
                }
                $params[] = $query_params[$param];
            }
            call_user_func_array([$controller, $method], $params);
        } else {
            $controller->$method();
        }
        exit;
    }

    // โหลดไฟล์ตรงๆ (เช่น logout.php)
    if (isset($route["file"])) {
        require_once __DIR__ . "/{$route["file"]}";
        exit;
    }
}

// หากไม่พบเส้นทาง
http_response_code(404);
echo "404 Not Found";
?>
