<?php
session_start();

if(!isset($_SESSION['uid'])){
	header('location:logout.php');
	die();
}else{
	$json = null;
	$showFrom = true;
	$user_id = $_SESSION['uid'];
	include_once('function.php');
	include('phpqrcode/qrlib.php');

	$obj = new DB_con();
	$fetchdata = new DB_con();
	$sql = $obj->get_control();

	while ($row = mysqli_fetch_array($sql)) {
		$recharge_msg = $row['rg_msg'];
		$notice =  $row['notice'];
		$approval = $row['approval'];
		$login =  $row['login'];
		$register = $row['register'];
		$bot_token =  $row['bot_token'];
		$log_channel = $row['log_channel'];
		$charge =  $row['charge'];
		$api_key =  $row['robi_token'];
		$server_pin =  $row['server_pin'];
	}

	$sql = $obj->get_balance($user_id);
	$balance = mysqli_fetch_array($sql);
	$diff = $balance['deposit_sum'] - $balance['withdraw_sum'];



	if (isset($_POST['submit'])) {

		if ($diff > $server_pin) {
			$nid = $_POST["nid"];
			$dob = $_POST["dob"];
			$user_id = $_SESSION['uid'] ?? null; 
		
			if (!$user_id) {
				$error_message = "User ID is missing. Please log in.";
				header("Location: login.php");
				exit();
			}
		
			if ($obj->ServerCopyPin($nid, $user_id)) {
				$error_message = "This Card Data Already Exists";
			} else {
				include 'Apis/pin.php';
		
				$newNID = new NID($nid, $dob);
				$nidInfo = $newNID->info();
				$json = json_decode($nidInfo);
		
				if ($json && isset($json->nid) && $json->status === "success") {

					$data = $json;
					
					try {
						$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
						$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
						$pdo->beginTransaction();
		
						$order_type = "Server Pin"; 
						$order_details = $data->nid; 
		
						$query_orders = "INSERT INTO tbl_orders (order_type, order_details, user_id, nid, dob) 
										 VALUES (:order_type, :order_details, :user_id, :nid, :dob)";
						$stmt_orders = $pdo->prepare($query_orders);
						$stmt_orders->execute([
							':order_type' => $order_type,
							':order_details' => $order_details,
							':user_id' => $user_id,
							':nid' => $data->nid,
							':dob' => $dob,
						]);
		
						$order_id = $pdo->lastInsertId();
		
						$query_order_details = "INSERT INTO tbl_order_details (
							order_id, name, name_en, date_of_birth, national_id, pin, 
							permanent_full_address, present_full_address
						) VALUES (
							:order_id, :name, :name_en, :date_of_birth, :national_id, :pin, 
							:permanent_full_address, :present_full_address
						)";
		
						$stmt_order_details = $pdo->prepare($query_order_details);
						$stmt_order_details->execute([
							':order_id' => $order_id,
							':name' => $data->name ?? '',
							':name_en' => $data->nameEn ?? '',
							':date_of_birth' => $dob,
							':national_id' => $data->nid,
							':pin' => $data->pin,
							':permanent_full_address' => $data->perAddress ?? '',
							':present_full_address' => $data->preAddress ?? '',
						]);
		
						$pdo->commit();
		

						if ($user_id){
							$withdraw = $obj->get_withdraw($user_id, $server_pin);
						}
			
						$success_message = "Order Successfully Processed";

					} catch (PDOException $e) {
						$pdo->rollBack();
						$error_message = "Error: " . $e->getMessage();
					}
				} else {
					$error_message = "Data not found or invalid response";
				}
			}
		} else {
			$error_message = "You don't have enough balance";
		}
		
		
	}
	
	

	
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<meta content="" name="description"><meta content="" name="keywords">
	<title><?php if($json == null){echo "SERVER SEBA ONLINE";}else{echo $json->data->nameEn;}?></title>
	<link href="https://surokkha.gov.bd/favicon.png" rel="icon">
	<link href="https://surokkha.gov.bd/favicon.png" rel="apple-touch-icon">
	<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.1.1/css/all.css">

	
	<!-- Core CSS -->
      <link rel="stylesheet" href="asset/vendor/css/core.css" class="template-customizer-core-css" />
      <link rel="stylesheet" href="asset/vendor/css/theme-default.css" class="template-customizer-theme-css" />
      <!-- <link rel="stylesheet" href="asset/css/demo.css" />
      <link rel="stylesheet" href="css/new.css"/> -->
      <!-- Vendors CSS -->
      <link rel="stylesheet" href="asset/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
      <link rel="stylesheet" href="asset/vendor/libs/apex-charts/apex-charts.css" />
      <!-- Page CSS -->
</head>
<body>
	<!-- <header id="header" class="header fixed-top d-flex align-items-center">
		<div class="d-flex align-items-center justify-content-between">
			<a href="index.php" class="logo d-flex align-items-center"></a>
			<i class="bi bi-list toggle-sidebar-btn"></i>
		</div>
		<nav class="header-nav ms-auto">
			<ul class="d-flex align-items-center">
				</li>
				<li class="nav-item dropdown pe-3">
					
				
				</li>
			</ul>
		</nav>
	</header> -->
	<?php include("includes/head.php");?>
	<main id="main" class="main mt-5">
		<section class="bg-diffrent">
			<div id="inp" class="container">
				<p><?php if ($diff > $server_pin) {echo " ";} else {$error_message = "You don't have enough balance";} ?></p>

                
				<form action="" method="post">
					<div class="row">
						<div class="mb-3 myDiv" id="showOne">
							<label>National ID :</label>
							<input type="text" class="form-control" id="nid" placeholder="825218****" name="nid">
						</div>
						<div class="mb-3 myDiv" id="showOne">
							<label>Date of Birth :</label>
							<input type="text" class="form-control" id="dob" placeholder="1997-03-17 " name="dob">
						</div>
						<!-- <div class="mb-3 myDiv" id="showOne">
							<label>Server Copy :</label>
							<select name="server" id="server" class="form-control text-center">
                                <option selected value="old">Old Server Copy</option>
                            </select>
						</div> -->
						<span style="font-size: 12px;padding: 10px;margin: auto;text-align: center;color: red;">
						<b>Note:</b> আপনার ফাইলের জন্য <?php echo $server_pin ?> টাকা চার্জ কাটা হবে .</span>
						<input type="submit" name="submit" class="btn btn-primary" onclick="submit()" value="Get 17Digit Pin" />
					
						<!-- <a href="dashboard.php" align="center>
							<button type="button" class="btn btn-info text-black mt-2">
							Download Server Copy
							</button>
						</a> -->
						<a href="dashboard.php" align="center>
							<button type="button" class="btn btn-warning text-black mt-2">
							Back Home
							</button>
						</a>

					</div>
				</form>


			</div>	

			<div class="container mt-4">
				<h2 class="text-center">Order History</h2>
				<?php

					try {
						$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
						$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

						// Check if the user_id is 1
						if ($user_id == 1) {
							// Get all data if user_id is 1
							$query = "SELECT * FROM tbl_orders WHERE order_type = 'Server Pin' ORDER BY id DESC";
						} else {
							// Get data based on the specific user_id if not 1
							$query = "SELECT * FROM tbl_orders WHERE order_type = 'Server Pin' AND user_id = $user_id ORDER BY id DESC";
						}

						$stmt = $pdo->prepare($query);
						$stmt->execute();

						// Fetch data
						$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
					} catch (PDOException $e) {
						echo "Error: " . $e->getMessage();
						exit;
					}
					?>

					<table id="orderTable" class="table table-striped" style="width:100%">
						<thead>
							<tr>
								<th>ID</th>
								<th>Order Time</th>
								<th>Order Details</th>
								<th>Download</th>
							</tr>
						</thead>
						<tbody>
							<?php $serial_no = 1; ?>
							<?php foreach ($orders as $order): ?>
								<tr>
									<td><?php echo htmlspecialchars($serial_no); ?></td>
									<td>
										<?php 
											$datetime = new DateTime($order['order_date']); // Create a DateTime object
											echo $datetime->format('d M y, h:i A');
										?>
									</td>
									<td><?php echo htmlspecialchars($order['order_details']); ?></td>
									<td>
									<?php if ($user_id == 1) { ?>
										
										<!-- Edit Button -->
											<button class="btn btn-success btn-sm rounded-3 edit-btn" 
														type="button" 
														title="Edit"
														data-id="<?php echo $order['id']; ?>"
														data-api-id="<?php echo $order['user_id']; ?>"
														data-bs-toggle="modal" 
														data-bs-target="#editModal">
													<i class="bi bi-pencil-square"></i>
											</button>
									<?php } ?>
										<a href="pin_copy_v1.php?id=<?php echo urlencode($order['id']); ?>" class="btn btn-sm rounded-pill btn-danger">
											<i class="fas fa-download"></i>
										</a>
										<a href="delete_sv.php?id=<?php echo urlencode($order['id']); ?>" class="btn btn-sm rounded-pill btn-danger">
											<i class="fas fa-trash"></i>
										</a>
									</td>
								</tr>
								<?php $serial_no++; // Increment serial number ?>
							<?php endforeach; ?>
						</tbody>
					</table>

			</div>
		</section>
	</main>

	<!-- Edit Modal -->
	<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Update ID</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="updatedID" class="form-label">Updated ID</label>
                        <input type="text" class="form-control" id="updatedID" name="updated_id" required>
                    </div>
                    <input type="hidden" id="editDataId" name="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>



	<script>

	$(document).ready(function () {
		$('#orderTable').DataTable({
			responsive: true,
		});
	});


	var b = <?php echo $diff; ?>, c = <?php echo $charge ?>;
    function submit(){
    var nid = $("#nid").val();
    var dob = $("#dob").val();
    var server = $("#server").val();
    if(nid == ""){
        //  alert("Input National ID");
		 $error_message = "Input National ID";
    }else if(dob == ""){
        //  alert("Date of Birth");
		 $error_message = "Date of Birth";
    }else{
        if(b > c){
           $("form").submit();
        }else{
            // alert("You don't have enough balance");
			$error_message = "You don't have enough balance";
        }
    }
    };




	document.addEventListener('DOMContentLoaded', () => {
    const editButtons = document.querySelectorAll('.edit-btn');
    const editForm = document.getElementById('editForm');

    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Get the ID and populate the modal
            const id = button.getAttribute('data-id');
            const apiId = button.getAttribute('data-api-id'); // Optional if different

			console.log(id, apiId);

            // Populate modal inputs
            document.getElementById('editDataId').value = id;
            document.getElementById('updatedID').value = apiId; // Optional
        });
    });

    // Handle form submission
    editForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(editForm);

        try {
            const response = await fetch('update_sv_id.php', {
                method: 'POST',
                body: formData,
            });

            const result = await response.json(); // Expect JSON response from the server

            if (result.success) {
                alert('Updated successfully!');
                location.reload(); // Reload the page to reflect changes
            } else {
                alert('Error updating ID: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An unexpected error occurred.');
        }
    });
});

	</script>



<?php include('includes/footer.php'); ?>

<?php } ?>

</body>
</html>


