<?php
session_start();
include_once('function.php');
$obj = new DB_con();

if (isset($_POST['user_ids']) && is_array($_POST['user_ids'])) {
    $error = false;

    foreach ($_POST['user_ids'] as $id) {
        if ($id == "1") {
            $error = true;
            continue; // Skip admin account
        }

        $obj->delete_users($id);
    }

    if ($error) {
        $_SESSION['error_message'] = "Admin account cannot be deleted.";
        echo "<script>alert('Some accounts (like admin) were not deleted.');</script>";
    } else {
        $_SESSION['success_message'] = "Selected users deleted successfully.";
        echo "<script>alert('Selected users deleted');</script>";
    }

    echo "<script>window.location.href = 'users.php'</script>";
} else {
    echo "<script>alert('No users selected.');</script>";
    echo "<script>window.location.href = 'users.php'</script>";
}
?>
