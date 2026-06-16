<?php
require_once __DIR__ . '/../db.php';

class InventoryModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function getStock($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM inventory WHERE product_id = ? LIMIT 1");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $res;
    }

    public function updateStock($product_id, $stock) {
        $stmt = $this->conn->prepare("INSERT INTO inventory (product_id, stock) VALUES (?, ?) ON DUPLICATE KEY UPDATE stock = ?");
        $stmt->bind_param('iii', $product_id, $stock, $stock);
        $stmt->execute();
        $ok = $stmt->affected_rows >= 0;
        $stmt->close();
        return $ok;
    }
}
