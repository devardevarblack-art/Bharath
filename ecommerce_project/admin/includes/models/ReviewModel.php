<?php
require_once __DIR__ . '/../db.php';

class ReviewModel {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function addReview($product_id, $customer_id, $rating, $title, $comment) {
        $stmt = $this->conn->prepare("INSERT INTO reviews (product_id, customer_id, rating, title, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('iiiss', $product_id, $customer_id, $rating, $title, $comment);
        $stmt->execute();
        $id = $this->conn->insert_id;
        $stmt->close();
        return $id;
    }

    public function listByProduct($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $res;
    }
}
