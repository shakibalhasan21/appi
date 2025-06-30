<?php
    session_start();
    include_once('function.php');
    $obj = new DB_con();


  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; // Original ID
    $updated_id = $_POST['updated_id']; // New ID


    if ($obj->update_sv_ids($id, $updated_id)) {
        // Return success response as JSON
        echo json_encode(["success" => true, "message" => "User ID updated successfully!"]);
    } else {
        // Return error response as JSON
        echo json_encode(["success" => false, "message" => "Failed to update"]);
    }
}
