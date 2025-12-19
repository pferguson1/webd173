<?php
/**
 * Logout Script
 */
require_once 'config.php';

// Destroy session
session_destroy();

// Clear session variables
$_SESSION = [];

setFlashMessage('You have been logged out successfully', 'info');
redirect('login.php');
?>
