<?php
require_once __DIR__ . '/../db.php';

class SupportModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function createTicket($customer_id, $subject, $message) {
        $stmt = $this->conn->prepare("INSERT INTO support_tickets (customer_id, subject, message) VALUES (?, ?, ?)");
        $stmt->bind_param('iss', $customer_id, $subject, $message);
        $stmt->execute();
        $id = $this->conn->insert_id;
        $stmt->close();
        return $id;
    }

    public function listOpen() {
        $res = $this->conn->query("SELECT * FROM support_tickets ORDER BY created_at DESC");
        return $res->fetch_all(MYSQLI_ASSOC);
    }
}
