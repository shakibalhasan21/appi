<?php
    session_start();
    include_once('function.php');
    $obj = new DB_con();


  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $api_name = $_POST['api_name'];
    $api_url = $_POST['api_url'];
    $api_key = $_POST['api_key'];


    if ($obj->update_api($id, $api_name, $api_url, $api_key)) {
        // Return success response as JSON
        echo json_encode(["success" => true, "message" => "API updated successfully!"]);
    } else {
        // Return error response as JSON
        echo json_encode(["success" => false, "message" => "Failed to update API"]);
    }
}
