<?php
/**
 * Logout Page
 */

require_once '../../config/constants.php';
require_once '../../config/session.php';

session_destroy();
header('Location: ' . BASE_URL . 'index.php?logout=true');
exit();
?>