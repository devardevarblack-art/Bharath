<?php
require_once '../includes/db.php';

unset($_SESSION['customer_id']);
unset($_SESSION['customer_name']);
unset($_SESSION['customer_email']);

header("Location: ../index.php");
exit();
?>
