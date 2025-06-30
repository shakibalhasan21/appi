<?php 
session_start();
include_once('function.php');
$obj = new DB_con();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Prevent deleting admin account (Optional)
if ($id === 1) {
    die("ADMIN ACCOUNT CANNOT BE DELETED");
}

// Perform delete
$result = $obj->delete_sv($id);

if ($result) {
    $_SESSION['success_message'] = "Successfully Deleted";
} else {
    $_SESSION['error_message'] = "Failed to delete data";
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>
