<?php 
include_once('function.php');
$obj=new DB_con();
$id = $_GET['id'];
if($id == "1"){
    die("ADMIN ACCOUNT");
}
$result = $obj->delete_user($id);
if($result){
    $_SESSION['success_message'] = "Data deleted";

    echo "<script>alert('Data deleted');</script>"; 
    echo "<script>window.location.href = 'users.php'</script>";     
}

?>