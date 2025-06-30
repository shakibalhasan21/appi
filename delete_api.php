<?php 
include_once('function.php');
$obj=new DB_con();
$id = $_GET['id'];
// if($id == "1"){
//     die("ADMIN ACCOUNT");
// }
$result = $obj->delete_api($id);
if($result){
//  echo "<script>alert('Data deleted');</script>"; 
 $success_message = "Data deleted";
echo "<script>window.location.href = 'apis.php'</script>";     
}

?>