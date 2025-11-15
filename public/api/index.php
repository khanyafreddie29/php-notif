<?php
// Fix the path to db.php - adjust based on your actual file structure
require_once __DIR__ . '/../../app/includes/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Simple routing - check if this is a notifications request
$isNotificationsEndpoint = false;

// Check the request URI for notifications endpoint
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
if (strpos($request_uri, 'notifications') !== false || strpos($request_uri, 'getNotifications') !== false) {
    $isNotificationsEndpoint = true;
}

// Also check query string
if (isset($_GET['employee_id'])) {
    $isNotificationsEndpoint = true;
}

if ($isNotificationsEndpoint) {
    handleGetNotifications();
} else {
    echo json_encode(['error' => 'Invalid endpoint. Available: /notifications/getNotifications']);
    exit;
}

function handleGetNotifications() {
    $employee_id = $_GET['employee_id'] ?? null;
    
    if (!$employee_id) {
        echo json_encode(['error' => 'Employee ID required']);
        exit;
    }
    
    try {
        $conn = db();
        
        // Simple query without joins for now
        // $query = "SELECT * FROM notifications_records WHERE employee_id = ? OR is_broadcast = 1 ORDER BY date_created DESC LIMIT 10";
        $query = "SELECT nr.*, e.first_name, e.last_name 
          FROM notifications_records nr 
          JOIN employees e ON nr.employee_id = e.employee_id 
          WHERE (nr.employee_id = ? OR nr.is_broadcast = 1)
          ORDER BY nr.date_created DESC, nr.notification_id DESC 
          LIMIT 15";
        
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $employee_id);
        
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        $notifications = [];
        
        while ($row = $result->fetch_assoc()) {
            $notifications[] = [
                'notification_id' => $row['notification_id'],
                'title' => $row['title'],
                'message' => $row['message'],
                'date_created' => $row['date_created'],
                'is_broadcast' => (bool)$row['is_broadcast'],
                'employee_id' => $row['employee_id'],
                'read' => false
            ];
        }
        
        echo json_encode([
            'success' => true,
            'notifications' => $notifications,
            'count' => count($notifications)
        ]);
        
        $stmt->close();
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'notifications' => [] // Return empty array on error
        ]);
    }
}
?>