<?php
require_once __DIR__ . '/../db.php';

class OrderModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function create($customer_id, $items, $total, $shipping_address, $vendor_id = null) {
        $stmt = $this->conn->prepare("INSERT INTO orders (customer_id, vendor_id, total, shipping_address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('iiss', $customer_id, $vendor_id, $total, $shipping_address);
        $stmt->execute();
        $order_id = $this->conn->insert_id;
        $stmt->close();

        $itemStmt = $this->conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($items as $it) {
            $itemStmt->bind_param('iiid', $order_id, $it['product_id'], $it['quantity'], $it['price']);
            $itemStmt->execute();
        }
        $itemStmt->close();
        return $order_id;
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res;
    }

    public function listByCustomer($customer_id) {
        $stmt = $this->conn->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC");
        $stmt->bind_param('i', $customer_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res;
    }

    public function updateStatus($id, $status) {
        $stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }
}
