<?php
// logout.php
require_once __DIR__ . '/includes/config.php';
session_destroy();
header("Location: /listeners_lounge/index.php");
exit();
?>
