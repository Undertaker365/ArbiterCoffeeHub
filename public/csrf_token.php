<?php
require_once '../includes/csrf.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
header('Content-Type: application/json');
echo json_encode(['token' => csrf_token()]);
