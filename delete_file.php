<?php 
include_once('function.php');
$obj = new DB_con();
$id = $_GET['id'];

// Prevent deleting admin account (Optional)
if ($id == "1") {
    die("ADMIN ACCOUNT CANNOT BE DELETED");
}

// Perform delete operation
$result = $obj->delete_file($id);

// Check result
if ($result) {
    header("Location: " . $_SERVER['HTTP_REFERER'] . "?success=1");
    exit(); // Stop further execution
} else {
    echo "<script>alert('Failed to delete data');</script>";
}
?>
