<?php
require_once __DIR__ . '/../db.php';

class NotificationModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function create($user_type, $user_id, $type, $message) {
        $stmt = $this->conn->prepare("INSERT INTO notifications (user_type, user_id, type, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('siss', $user_type, $user_id, $type, $message);
        $stmt->execute();
        $id = $this->conn->insert_id;
        $stmt->close();
        return $id;
    }

    public function listForUser($user_type, $user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM notifications WHERE user_type = ? AND user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param('si', $user_type, $user_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res;
    }
}
