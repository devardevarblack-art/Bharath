<?php
require_once __DIR__ . '/../db.php';

class PaymentModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function create($order_id, $method, $amount, $transaction_id = null) {
        $stmt = $this->conn->prepare("INSERT INTO payments (order_id, method, amount, transaction_id, status) VALUES (?, ?, ?, ?, 'completed')");
        $stmt->bind_param('isds', $order_id, $method, $amount, $transaction_id);
        $stmt->execute();
        $id = $this->conn->insert_id;
        $stmt->close();
        return $id;
    }

    public function getByOrder($order_id) {
        $stmt = $this->conn->prepare("SELECT * FROM payments WHERE order_id = ?");
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res;
    }
}
