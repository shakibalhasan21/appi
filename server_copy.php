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
		$server_copy =  $row['server_copy'];
		$api_key =  $row['robi_token'];
	}

	$sql = $obj->get_balance($user_id);
	$balance = mysqli_fetch_array($sql);
	$diff = $balance['deposit_sum'] - $balance['withdraw_sum'];



	if (isset($_POST['submit'])) {
		if ($diff > $server_copy) {

			$nid = $_POST["nid"];
			$dob = $_POST["dob"];

			if ($obj->ServerCopyNew($nid, $user_id)) {
				$error_message = "This Card Data Already Exists";
			} else {

				include 'Apis/sv_api.php';

				// Instantiate the NID class and get the info
				$newNID = new NID($nid, $dob);
				$nidInfo = $newNID->info();

				$json = json_decode($nidInfo);

				if ($json && isset($json->status) && $json->status == 1) {
					$data = $json->data;

					if($obj->is_premium($user_id) != 1){
						$withdraw = $obj->get_withdraw($user_id, $server_copy);
					}

					try {
						$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
						$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						$pdo->beginTransaction();
					
						$order_type = "Server copy";
						$order_details = $nid;
						$dob = $data->dob;
					
						// Insert into tbl_orders
						$query_orders = "INSERT INTO tbl_orders (order_type, order_details, user_id, nid, dob) 
										 VALUES (:order_type, :order_details, :user_id, :nid, :dob)";
						$stmt_orders = $pdo->prepare($query_orders);
						$stmt_orders->execute([
							':order_type' => $order_type,
							':order_details' => $order_details,
							':user_id' => $user_id,
							':nid' => $nid,
							':dob' => $dob
						]);
					
						$order_id = $pdo->lastInsertId();
					
						// Main SQL query with placeholders
						$sql = "INSERT INTO tbl_order_details (
							request_id, order_id, name, name_en, gender, gender_bn, bloodGroup, date_of_birth, father, mother,
							national_id, pin, religion, occupation, occupation_en,
							permanent_division, permanent_district, permanent_upozila, permanent_post_office,
							permanent_village_or_road, permanent_house_holding, permanent_full_address, permanent_address_line,
							present_division, present_district, present_upozila, present_post_office,
							present_village_or_road, present_house_holding, present_full_address, present_address_line,
							photo, sl_no, voter_no, voterAreaCode, voterArea, spouse, fatherNid, motherNid, birthPlace, mobile, sign
						) VALUES (
							:request_id, :order_id, :name, :name_en, :gender, :gender_bn, :bloodGroup, :date_of_birth, :father, :mother,
							:national_id, :pin, :religion, :occupation, :occupation_en,
							:permanent_division, :permanent_district, :permanent_upozila, :permanent_post_office,
							:permanent_village_or_road, :permanent_house_holding, :permanent_full_address, :permanent_address_line,
							:present_division, :present_district, :present_upozila, :present_post_office,
							:present_village_or_road, :present_house_holding, :present_full_address, :present_address_line,
							:photo, :sl_no, :voter_no, :voterAreaCode, :voterArea, :spouse, :fatherNid, :motherNid, :birthPlace, :mobile, :sign
						)";
					
						$stmt = $pdo->prepare($sql);
					
						// Execute the query with parameters
						$execute_data = [
							':request_id' => $data->requestId,
							':order_id' => $order_id,
							':name' => $data->name,
							':name_en' => $data->nameEn,
							':national_id' => $data->nid,
							':pin' => $data->pin ?? null,
							':voter_no' => $data->voterNo ?? null,
							':sl_no' => $data->slNo ?? null,
							':voterArea' => $data->voterArea ?? null,
							':voterAreaCode' => $data->voterAreaCode ?? null,
							':spouse' => $data->spouse ?? null,
							':date_of_birth' => $data->dob,
							':father' => $data->father ?? null,
							':fatherNid' => $data->fatherNid ?? null,
							':mother' => $data->mother ?? null,
							':motherNid' => $data->motherNid ?? null,
							':bloodGroup' => $data->bloodGroup ?? null,
							':gender' => $data->gender,
							':gender_bn' => $data->genderBn ?? null,
							':religion' => $data->religion ?? null,
							':occupation' => $data->occupation ?? null,
							':mobile' => $data->mobile ?? null,
							':occupation_en' => $data->occupationEn ?? null,
							
							':permanent_division' => $data->perAddress->division ?? null,
							':permanent_district' => $data->perAddress->district ?? null,
							':permanent_upozila' => $data->perAddress->upazila ?? null,
							':permanent_post_office' => $data->perAddress->postOffice ?? null,
							':permanent_village_or_road' => $data->perAddress->villageOrRoad ?? null,
							':permanent_house_holding' => $data->perAddress->homeOrHolding ?? null,
							':permanent_full_address' => $data->perAddress->addressLine ?? $data->perAddress,
							':permanent_address_line' => $data->perAddress->for_nid_peraddress ?? null,
					
							':present_division' => $data->preAddress->division ?? null,
							':present_district' => $data->preAddress->district ?? null,
							':present_upozila' => $data->preAddress->upazila ?? null,
							':present_post_office' => $data->preAddress->postOffice ?? null,
							':present_village_or_road' => $data->preAddress->villageOrRoad ?? null,
							':present_house_holding' => $data->preAddress->homeOrHolding ?? null,
							':present_full_address' => $data->preAddress->addressLine ?? $data->preAddress,
							':present_address_line' => $data->preAddress->for_nid_preaddress ?? null,
					
							':photo' => $data->photo ?? null,
							':sign' => $data->sign ?? null,
							':birthPlace' => $data->birthPlace ?? null
						];
					
						// Debug: Print out the parameters to ensure they match the placeholders
						// print_r($execute_data);
					
						// Execute the query
						$stmt->execute($execute_data);
					
						$pdo->commit();
						$success_message = "Order Successfully Done";
					} catch (PDOException $e) {
						$pdo->rollBack();
						$error_message = $e->getMessage();
						echo $error_message;
					}
					
				} else {
					$error_message = "সার্ভারে সমস্যার কারণে ডাটা পাওয়া যায়নি.! অনুগ্রহ করে একটু পর আবার চেষ্টা করুন!";
				}
			}
		} else {
			$error_message = "Insufficient balance.";
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
	<?php include("includes/head.php");?>
	<main id="main" class="main mt-5">
		<section class="bg-diffrent">
			<div class="card mb-2">
				<marquee style="border:1px solid #05C3FB; padding:5px;margin:10px;border-radius:3px;">
					<?php echo $notice  ?>
				</marquee>        
			</div>

			<div id="inp" class="card px-4 py-4" style=" margin: 0 auto; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
				<h4 class="text-center mb-2" style="color: #495057; font-weight: 600;">Server Copy (New Version)</h4>
				
				<p><?php if ($diff > $server_copy) {echo " ";} else { $error_message = "You don't have enough balance"; } ?></p>
				
				<div class="card px-4 py-4">

					<form action="" method="post">
						<div class="row">
							<!-- NID Number Field -->
							<div class="col-12 mb-4">
								<div class="form-group">
									<label class="form-label" style="font-weight: 500; color: #495057; margin-bottom: 8px;">NID No :</label>
									<input type="text" 
										class="form-control" 
										id="nid" 
										placeholder="0123456789" 
										name="nid"
										style="padding: 12px 16px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 15px; background-color: #f8f9fa;">
								</div>
							</div>
							
							<!-- Date of Birth Field -->
							<div class="col-12 mb-4">
								<div class="form-group">
									<label class="form-label" style="font-weight: 500; color: #495057; margin-bottom: 8px;">DOB :</label>
									<input type="text" 
										class="form-control" 
										id="dob" 
										placeholder="1999-12-31" 
										name="dob"
										style="padding: 12px 16px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 15px; background-color: #f8f9fa;">
								</div>
							</div>
							
							<!-- Info Message -->
							<div class="col-12 mb-4">
								<div class="alert" style="background-color: #f8f4ff; border: 1px solid #e0d4fd; border-radius: 8px; padding: 12px 16px;">
									<div style="display: flex; align-items: center; color: #6c5ce7;">
										<i class="fas fa-info-circle" style="margin-right: 8px; font-size: 16px;"></i>
										<span class="text-center" style="font-size: 14px;">আপনার একাউন্ট থেকে <?php echo $server_copy; ?> টাকা কাটা হবে।</span>
									</div>
								</div>
							</div>
							
							<!-- Download Button -->
							<div class="col-12 mb-3">
								<input type="submit" 
									name="submit" 
									class="btn w-100" 
									onclick="submit()" 
									value="ডাউনলোড করুন"
									style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
											color: white; 
											border: none; 
											padding: 14px 20px; 
											border-radius: 8px; 
											font-size: 16px; 
											font-weight: 700;
											transition: all 0.3s ease;
											box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);"
									onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.6)';"
									onmouseout="this.style.transform='translateY(0px)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.4)';" />
							</div>
						</div>
					</form>
					
					<!-- Note: Hidden the Back Home button as it's not present in the target design -->
					<!-- If you need it, uncomment below: -->
					<div class="col-12">
						<a href="dashboard.php" class="btn btn-outline-secondary w-100" style="padding: 12px 20px; border-radius: 8px;">
							Back Home
						</a>
					</div>
				</div>
				
			</div>

			<style>
				/* Additional CSS for better styling */
				.form-control:focus {
					border-color: #667eea !important;
					box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
					background-color: #fff !important;
				}
				
				.form-label {
					font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
				}
				
				#inp {
					font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
				}
			</style>



			<style>


				/* Modern Table Styling */
				.modern-table-container {
					background: #ffffff;
					border-radius: 12px;
					overflow: hidden;
					box-shadow: 0 2px 10px rgba(0,0,0,0.08);
					border: 1px solid #e9ecef;
				}

				.modern-table-header {
					display: grid;
					grid-template-columns: 60px 1fr 1.5fr 180px;
					background: #343a40;
					color: white;
					font-weight: 600;
					font-size: 14px;
				}

				.header-item {
					padding: 18px 12px;
					text-align: center;
					border-right: 1px solid #495057;
					font-size: 13px;
				}

				.header-item:last-child {
					border-right: none;
				}

				.modern-table-body {
					background: #ffffff;
				}

				.table-row {
					display: grid;
					grid-template-columns: 60px 1fr 1.5fr 100px;
					border-bottom: 1px solid #e9ecef;
					transition: background-color 0.2s ease;
				}

				.table-row:hover {
					background-color: #f8f9fa;
				}

				.table-row:last-child {
					border-bottom: none;
				}

				.table-cell {
					padding: 16px 12px;
					display: flex;
					align-items: center;
					justify-content: center;
					text-align: center;
					font-size: 13px;
					color: #495057;
					border-right: 1px solid #e9ecef;
				}

				.table-cell:last-child {
					border-right: none;
				}

				/* Badge Styles */
				.time-badge {
					background: #6f42c1;
					color: white;
					padding: 6px 12px;
					border-radius: 20px;
					font-size: 12px;
					font-weight: 500;
				}

				.status-badge {
					padding: 6px 12px;
					border-radius: 20px;
					font-size: 12px;
					font-weight: 500;
					display: inline-flex;
					align-items: center;
					gap: 4px;
				}

				.status-badge.pending {
					background: #fff3cd;
					color: #856404;
					border: 1px solid #ffeaa7;
				}

				.status-badge.completed {
					background: #d1edff;
					color: #0c5460;
					border: 1px solid #b8daff;
				}

				.download-badge {
					padding: 6px 12px;
					border-radius: 20px;
					font-size: 12px;
					font-weight: 500;
					text-decoration: none;
					display: inline-flex;
					align-items: center;
					gap: 4px;
					transition: all 0.2s ease;
				}

				.download-badge.waiting {
					background: #fff3cd;
					color: #856404;
					border: 1px solid #ffeaa7;
				}

				.download-badge.ready {
					background: #d1edff;
					color: #0c5460;
					border: 1px solid #b8daff;
				}

				.download-badge.ready:hover {
					background: #0c5460;
					color: white;
					transform: translateY(-1px);
				}

				/* Revision Request Badge Styles */
				.revision-badge {
					padding: 6px 12px;
					border-radius: 20px;
					font-size: 11px;
					font-weight: 500;
					text-decoration: none;
					display: inline-flex;
					align-items: center;
					gap: 4px;
					transition: all 0.2s ease;
				}

				.revision-badge.send {
					background: #f8d7da;
					color: #721c24;
					border: 1px solid #f5c6cb;
				}

				.revision-badge.send:hover {
					background: #721c24;
					color: white;
					transform: translateY(-1px);
				}

				.revision-badge.waiting {
					background: #fff3cd;
					color: #856404;
					border: 1px solid #ffeaa7;
				}

				/* Action Button Styles */
				.action-buttons {
					display: flex;
					gap: 8px;
					align-items: center;
					justify-content: center;
					flex-wrap: nowrap; /* Prevent wrapping */
				}

				.action-btn {
					padding: 6px 10px;
					border-radius: 6px;
					font-size: 30px;
					display: inline-flex;
					align-items: center;
					justify-content: center;
					transition: all 0.2s ease;
					border: none;
					cursor: pointer;
					min-width: 32px;
					height: 32px;
					white-space: nowrap;
				}

				.action-btn.edit-btn {
					background: #28a745;
					color: white;
				}

				.action-btn.edit-btn:hover {
					background: #218838;
					transform: translateY(-1px);
				}

				.action-btn.delete-btn {
					background: #dc3545;
					color: white;
				}

				.action-btn.delete-btn:hover {
					background: #c82333;
					transform: translateY(-1px);
				}

				.page-title {
					font-weight: 700;
					color: #2c3e50;
					font-size: 28px;
				}

				/* Alert Styling */
				.alert {
					border-radius: 8px;
					padding: 12px 20px;
					margin-bottom: 20px;
				}

				.alert-success {
					background-color: #d4edda;
					border-color: #c3e6cb;
					color: #155724;
				}

				.alert-danger {
					background-color: #f8d7da;
					border-color: #f5c6cb;
					color: #721c24;
				}

				/* Responsive Design */
				@media (max-width: 1200px) {
					.modern-table-header,
					.table-row {
						grid-template-columns: 50px 1fr 1.2fr 90px;
					}

					.table-cell,
					.header-item {
						padding: 14px 8px;
						font-size: 12px;
					}
				}

				@media (max-width: 768px) {
					.modern-table-header {
						display: none;
					}

					.table-row {
						display: block;
						border: 1px solid #e9ecef;
						border-radius: 8px;
						margin-bottom: 16px;
						padding: 16px;
						background: white;
						box-shadow: 0 2px 4px rgba(0,0,0,0.1);
					}

					.table-cell {
						display: flex;
						justify-content: space-between;
						align-items: center;
						padding: 12px 0;
						border-right: none;
						border-bottom: 1px solid #f8f9fa;
						text-align: left;
					}

					.table-cell:last-child {
						border-bottom: none;
						padding-bottom: 0;
					}

					.table-cell:before {
						content: attr(data-label);
						font-weight: 600;
						color: #343a40;
						flex: 0 0 140px;
						text-align: left;
					}

					.action-buttons {
						justify-content: flex-end;
					}

					.time-badge,
					.status-badge,
					.download-badge,
					.revision-badge {
						font-size: 11px;
						padding: 4px 8px;
					}
				}

				/* Desktop View Restore */
				@media (min-width: 769px) {
					.modern-table-header {
						display: grid !important;
					}

					.table-row {
						display: grid;
						/* grid-template-columns: 60px 1fr 1.5fr 100px; */
						grid-template-columns: 60px 1fr 1.5fr 180px; /* Increased last column */
						padding: 0;
						margin-bottom: 0;
						border-radius: 0;
						box-shadow: none;
					}

					.table-cell {
						display: flex;
						justify-content: center;
						align-items: center;
						padding: 16px 12px;
						border-right: 1px solid #e9ecef;
						border-bottom: 1px solid #e9ecef;
						text-align: center;
					}

					.table-cell:before {
						content: none;
					}
				}

				/* Card styling */
				.card {
					border-radius: 15px;
					border: none;
					box-shadow: 0 4px 20px rgba(0,0,0,0.1);
				}

			</style>


			<div class="modern-table-container px-3 py-5 mt-4">
					<!-- Success/Error Messages -->
				<div class="text-center mb-3">
					<?php
					if (isset($_SESSION['success_message'])) {
						echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
						unset($_SESSION['success_message']);
					}

					if (isset($_SESSION['error_message'])) {
						echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
						unset($_SESSION['error_message']);
					}

					?>
				</div>
				<h2 class="text-center">Order History</h2>
				<div class="table-responsive">
					<!-- Table Header -->
					<div class="modern-table-header">
						<div class="header-item">আইডি</div>
						<div class="header-item">অর্ডার টাইম</div>
						<div class="header-item">অর্ডার ডিটেইলস</div>
						<div class="header-item">Action</div>
					</div>

					<!-- Table Body -->
					<div class="modern-table-body">
						<?php
						try {
							$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
							$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

							if ($user_id == 1) {
								$query = "SELECT * FROM tbl_orders WHERE order_type = 'Server copy' ORDER BY id DESC";
							} else {
								$query = "SELECT * FROM tbl_orders WHERE order_type = 'Server copy' AND user_id = $user_id ORDER BY id DESC";
							}

							$stmt = $pdo->prepare($query);
							$stmt->execute();
							$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
						} catch (PDOException $e) {
							echo "Error: " . $e->getMessage();
							exit;
						}
						?>

						<?php $serial_no = 1; ?>
						<?php foreach ($orders as $order): ?>
							<div class="table-row">
								<div class="table-cell" data-label="আইডি: "><?php echo htmlspecialchars($serial_no); ?></div>
								<div class="table-cell" data-label="অর্ডার টাইম: ">
									<?php 
										$datetime = new DateTime($order['order_date']);
										echo $datetime->format('d M y, h:i A');
									?>
								</div>
								<div class="table-cell" data-label="অর্ডার ডিটেইলস: ">
									<?php echo htmlspecialchars($order['order_details']); ?>
								</div>
								<div class="table-cell" data-label="Action: ">
									<div class="action-buttons">
										<?php if ($user_id == 1) { ?>
											<button class="action-btn edit-btn" 
													type="button" 
													title="Edit"
													data-id="<?php echo $order['id']; ?>"
													data-api-id="<?php echo $order['user_id']; ?>"
													data-bs-toggle="modal" 
													data-bs-target="#editModal">
												<i class="bi bi-pencil-square"></i>
											</button>
										<?php } ?>

										<a href="server_copy_v1.php?id=<?php echo urlencode($order['id']); ?>" class="action-btn download-btn">
											<i class="fas fa-download"></i>
										</a>

										<?php if ($user_id == 1) { ?>
											<a href="delete_sv.php?id=<?php echo urlencode($order['id']); ?>" class="action-btn delete-btn">
												<i class="fas fa-trash"></i>
											</a>
										<?php } ?>
									</div>
								</div>
							</div>
							<?php $serial_no++; ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>

		</section>

	<!-- <div class="card px-3 py-5"> 

	</div> -->
		
	</main>

	<style>
		#user_info {
			display: inline-block; /* Makes it behave like a badge */
			padding: 8px 16px; /* Padding for size */
			font-size: 14px; /* Font size */
			font-weight: bold; /* Bold text */
			color: #fff; /* White text */
			background-color:rgb(27, 116, 15); /* Blue background (Bootstrap primary color) */
			border-radius: 20px; /* Rounded edges like a badge */
			text-align: center; /* Text centered inside */
			margin: 10px auto; /* Center the badge vertically */
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Optional shadow */
		}

		.text-center {
			text-align: center; /
		}

	</style>
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
				<div class="text-center">
					<label for="user" id="user_info" class="badge">User Info</label>
				</div>
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

		// $(document).ready(function () {
		// 	$('#myTable').DataTable({
		// 		responsive: true,
		// 	});
		// });



		$(document).ready(function () {
	 
	   $('#orderTable').DataTable({
		   "info": false,
		   "ordering": false,
		   "language": {
			   "search": '<i class="fa-solid fa-magnifying-glass"></i>',
			   'searchPlaceholder': "search here..."
		   }
	   });
   });


	var b = <?php echo $diff; ?>, c = <?php echo $server_copy ?>;
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
			// Get the ID and API ID
			const id = button.getAttribute('data-id');
			const apiId = button.getAttribute('data-api-id'); // Get API ID

			// Set values in the modal
			document.getElementById('editDataId').value = id; // Set editDataId
			document.getElementById('updatedID').value = apiId; // Set updatedID

			// Fetch FullName via AJAX and pass apiId dynamically
			fetch(`get_user.php?apiId=${apiId}`) // Send apiId in query string
				.then(response => response.json())
				.then(data => {
					if (data && data.FullName) {
						// Set FullName to the user_info label
						document.getElementById('user_info').innerText = data.FullName;
					} else {
						document.getElementById('user_info').innerText = 'User not found!';
					}
				})
				.catch(error => {
					console.error('Error:', error);
					document.getElementById('user_info').innerText = 'Error fetching data!';
				});
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


