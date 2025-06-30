<?php
session_start();
header('Content-Type: application/json');

include_once('function.php');
$obj = new DB_con();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $upload_dir = 'server/';  // This should be the correct directory for file uploads

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
        exit;
    }

    if (isset($_FILES['d_file']) && $_FILES['d_file']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['d_file']['tmp_name'];
        $file_name = basename($_FILES['d_file']['name']);
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if ($file_ext !== 'pdf') {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only PDFs are allowed.']);
            exit;
        }

        // Generate a unique file name or keep original (for simplicity, we use the original file name here)
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $target_file)) {
            if ($obj->update_delivery($id, $file_name)) {
                echo json_encode(['success' => true, 'message' => 'Delivery updated successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database update failed.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'File upload failed.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded or an error occurred.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

?>
