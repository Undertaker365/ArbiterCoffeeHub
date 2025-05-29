<?php
require_once '../includes/db_util.php';
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $row = db_fetch_one("SELECT image_filename FROM products WHERE id = ?", [$id]);
    $image = $row ? $row['image_filename'] : null;
    if ($image && file_exists("../uploads/" . $image)) {
        unlink("../uploads/" . $image);
    }
    if (db_execute("DELETE FROM products WHERE id = ?", [$id])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Delete failed.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
