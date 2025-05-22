<?php
require_once '../db_connect.php';
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("SELECT image_filename FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetchColumn();
    if ($image && file_exists("../uploads/" . $image)) {
        unlink("../uploads/" . $image);
    }
    $deleteStmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    if ($deleteStmt->execute([$id])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Delete failed.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
<script src="https://cdn.tailwindcss.com"></script>
