<?php
require_once '../db_connect.php';
if (isset($_POST['id'], $_POST['action'])) {
    $id = intval($_POST['id']);
    $action = $_POST['action'] === 'deactivate' ? 'inactive' : 'active';
    $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id = ?");
    if ($stmt->execute([$action, $id])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Update failed.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
