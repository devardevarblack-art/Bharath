<?php
session_start();

function require_login($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header("Location: ../index.php");
        exit();
    }
}
?>
