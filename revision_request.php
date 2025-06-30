<?php
    session_start();
    include_once('function.php');
    $obj = new DB_con();
    $user_id = $_SESSION['uid'];


    
// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Validate and sanitize input
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT); // Validate ID as an integer

    // Check if ID is valid
    if ($id === false || $id === null) {
        // Redirect back with an error message
        header("Location: {$_SERVER['HTTP_REFERER']}?error=Invalid ID");
        exit; // Stop further execution
    }


    
    // Process the revision request
    if ($obj->revision_request($id)) {
        
        $withdraw = $obj->get_withdraw($user_id, 5);
        // Redirect back with success message
        header("Location: {$_SERVER['HTTP_REFERER']}?success=Revision Request successfully Send!");
        exit; // Stop further execution
    } else {
        // Redirect back with failure message
        header("Location: {$_SERVER['HTTP_REFERER']}?error=Failed to Request");
        exit; // Stop further execution
    }
} else {
    // Handle invalid request methods
    header("Location: {$_SERVER['HTTP_REFERER']}?error=Invalid request method");
    exit; // Stop further execution
}

