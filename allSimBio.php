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
		$robi_bio =  $row['robi_bio'];
	}

	$sql = $obj->get_balance($user_id);
	$balance = mysqli_fetch_array($sql);
	$diff = $balance['deposit_sum'] - $balance['withdraw_sum'];



	if (isset($_POST['submit'])) {

		if ($diff > $bot_token) {
            $nid = 111;
			$name = $_POST["name"];
			$info = $_POST["bio_num"];
			$note = $_POST["note"];
			$user_id = $_SESSION['uid'] ?? null;
		
			if (!$user_id) {
				$error_message = "User ID is missing. Please log in.";
				header("Location: login.php");
				exit();
			}
		
			if ($obj->ServerCopyPin($nid, $user_id)) {
				$error_message = "This Card Data Already Exists";
			} else {
                try {
                    // Database connection
                    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                    // Begin transaction
                    $pdo->beginTransaction();
                
                    $order_time = date('Y-m-d H:i:s');         // Current time for order_time
                    $status = "pending";                       // Default status
                    $downloads = 0;                            // Default downloads count
                    $delivery_time = date('Y-m-d H:i:s', strtotime('+5 hours')); // Delivery time (3 days later)
                    $revision_request = "No revisions yet";   // Example revision request
                    $file = null;                // Example file name
                    $order_type = $name;                 // Example order type
                    $created_at = date('Y-m-d H:i:s');         // Current timestamp for created_at
                    $updated_at = $created_at;                 // Same as created_at initially
                
                    // SQL query to insert all fields
                    $query_orders = "INSERT INTO tbl_file_orders 
                        (name, info, note, order_time, status, downloads, delivery_time, revision_request, file, order_type, user_id, created_at, updated_at) 
                        VALUES 
                        (:name, :info, :note, :order_time, :status, :downloads, :delivery_time, :revision_request, :file, :order_type, :user_id, :created_at, :updated_at)";
                
                    // Prepare query
                    $stmt_orders = $pdo->prepare($query_orders);
                
                    // Execute query with parameters
                    $stmt_orders->execute([
                        ':name' => $name,
                        ':info' => $info,
                        ':note' => $note,
                        ':order_time' => $order_time,
                        ':status' => $status,
                        ':downloads' => $downloads,
                        ':delivery_time' => $delivery_time,
                        ':revision_request' => $revision_request,
                        ':file' => $file,
                        ':order_type' => $order_type,
                        ':user_id' => $user_id,
                        ':created_at' => $created_at,
                        ':updated_at' => $updated_at
                    ]);
                
                    // Commit transaction
                    $pdo->commit();
                    
                    $withdraw = $obj->get_withdraw($user_id, $bot_token);


                           // Send order details via Telegram bot to the admin
            
                    $telegramToken = "8168717920:AAHspTpfVwkahk0ldqC-ItAt8qtAImz3Dm8"; //Bot Token
                    $adminChatId = "7526969095"; //Admin Chat ID
                    $newBalance = $diff - $bot_token;
                    $phoneNumber = $obj->fetchWhatsapp($user_id);

                    
                    $adminMessage = "নতুন BIO অর্ডার ডিটেইলসঃ\n";
                    $adminMessage .= "অপারেটর: $name\n";
                    $adminMessage .= "BIO নাম্বারঃ $info\n";
                    $adminMessage .= "নোট: $note\n";
                    $adminMessage .= "WhatsApp নম্বর: $phoneNumber\n";
                    $adminMessage .= "সময়: $order_time\n";
                    $adminMessage .= "অর্ডারকারী বর্তমান ব্যালেন্স: $newBalance টাকা।";

                    $telegramUrl = "https://api.telegram.org/bot$telegramToken/sendMessage";
                    $telegramData = [
                        'chat_id' => $adminChatId,
                        'text' => $adminMessage,
                        'parse_mode' => 'HTML'
                    ];

                    function sendTelegramMessage($url, $data) {
                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                        $response = curl_exec($ch);

                        if (curl_errno($ch)) {

                            $error_message = "Could not send message to ADMIN";
                        }

                        curl_close($ch);
                        return $response;
                    }

                    // Send to admin
                    $adminResponse = sendTelegramMessage($telegramUrl, http_build_query($telegramData));


                    // Success message
                    $success_message = "অর্ডার সফল হয়েছে। শিগ্রই ডেলিভারি করা হবে।";
                
                } catch (PDOException $e) {
                    // Rollback transaction on error
                    $pdo->rollBack();
                    $error_message = "Error: " . $e->getMessage();
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
	<?php include("includes/head.php");?>

    <style>
	  body {
                font-family: 'Segoe UI', sans-serif;
                background-color: #f4f6f9;
                color: #000;
                margin: 0;
                padding: 0;
            }

            input, textarea, select {
                color: #000 !important;
                font-size: 16px;
            }

            textarea {
                height: 100px !important;
            }

            /* .card {
                border-radius: 12px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
                background-color: #ffffff;
                padding: 30px;
                margin: 20px auto;
                max-width: 100%;
                box-sizing: border-box;
                overflow: hidden;
            } */

            .card-body {
                background: lightgrey;
                padding: 20px;
                border-radius: 8px;
            }

            .card-title {
                font-size: 35px;
                font-weight: bold;
                color: #333;
                margin-bottom: 20px;
            }

            label {
                font-weight: 500;
                color: #333;
                margin-bottom: 5px;
            }

            .form-control, .form-select {
                border-radius: 8px;
                border: 1px solid #ccc;
                padding: 10px 15px;
                background-color: #fefefe;
                width: 100%;
                box-sizing: border-box;
            }

            .btn-primary {
                background-color: #0066cc;
                border: none;
                padding: 10px 25px;
                font-weight: bold;
                font-size: 16px;
                border-radius: 8px;
                transition: 0.3s;
                color: #fff;
            }

            .btn-primary:hover {
                background-color: #004d99;
            }

            .btn {
                font-size: 14px;
                border-radius: 8px;
                padding: 6px 12px;
                cursor: pointer;
            }

            .table {
                font-size: 14px;
                background: #fff;
                border-radius: 8px;
                overflow: hidden;
                width: 100%;
                border-collapse: collapse;
            }

            .table thead {
                background-color: #0066cc;
                color: #fff;
                text-align: center;
            }

            .table th, .table td {
                vertical-align: middle !important;
                padding: 10px;
                border: 1px solid #ddd;
            }

            .table tbody tr:hover {
                background-color: #f1f1f1;
            }

            .text-center h5 {
                margin-bottom: 10px;
                color: #444;
            }

            .section {
                padding: 5px;
            }

            .page-title {
                font-size: 22px;
                margin-bottom: 15px;
                font-weight: 600;
                text-align: center;
            }

            .table-responsive {
                margin-top: 20px;
                overflow-x: auto;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .btn-success, .btn-warning, .btn-danger {
                font-size: 13px;
                padding: 6px 10px;
                border: none;
                color: #fff;
            }

            .btn-success {
                background-color: #28a745;
            }

            .btn-warning {
                background-color: #ffc107;
            }

            .btn-danger {
                background-color: #dc3545;
            }

            @media (max-width: 768px) {
                /* .card {
                    padding: 20px;
                } */

                .form-control, .form-select {
                    font-size: 14px;
                }

                .btn-primary {
                    font-size: 14px;
                    padding: 8px 20px;
                }

                .table th, .table td {
                    font-size: 14px;
                    padding: 8px;
                }
            }

	</style>
	<main id="main" class="main">
    <section class="section">
        <div class="card">
        <div style="background:lightgrey">
                <h2 class="card-title text-center">বায়োমেট্রিক অর্ডার </h2>


                <div class="form-group text-center">
                    <h5>
                        <p style="max-width:90%; margin:auto;">

                        </p></h5><h5>আপনি আপনার বায়োমেট্রিক অর্ডার করুন,খুব শিগ্রই ডেলিভারি করা হবে। </h5><h5>
                </h5></div>
                <div align="center" class="form-group">
				<form method="POST" action="" onsubmit=" submitForm();">
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-6 form-group"><label for="company">নাম্বার দিন *</label>
                                <input type="number" class="form-control" name="bio_num" required="">
                            </div><!--  col-md-6   -->
                        </div>
                         <div class="row justify-content-center">
                         <div class="mb-3 col-12 col-md-6 form-group">
                            <label class="form-label">Select Type:</label>
                            <select name="name" id="" class="form-select" required>
                                <option value="" selected>Select</option>
                                <option value="ROBI_AIR_BIO">রবি / এয়ারটেল</option>
                                <option value="GP_BIO">গ্রামীন</option>
                                <option value="BL_BIO">বাংলালিংক</option>
                                <option value="TELE_BIO">টেলিটক</option>
                                
                            </select>
                        </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-6 pt-3">
                                <div class="form-group">
                                    <label for="mobile">বায়োমেট্রিক সম্পর্কে বিস্তারিত লিখুন।(যদি কিছু বলার থাকে)</label>
                                    <textarea class="form-control" name="note" rows="2"></textarea>
                                </div>
                            </div>
                        </div><!--  row   -->
                        <div style="display: flex;justify-content: center; margin-bottom: 30px; padding-top: 15px;">
                            <span>বায়োমেট্রিকর জন্য <?php echo $bot_token ?> টাকা কাটা হবে।</span><br>
                        </div>
                            <div style="display: flex;justify-content: center; margin-bottom: 30px;">
                                <button name="submit" type="submit" class="btn btn-primary">অর্ডার করুন</button>
                            </div>
                        </form>
                    </div>
                    
                </div>
        </div>
    </section>

    <div class="row m-2">
        <div class="col-md-12">
            <h2 class="page-title text-center">অর্ডার হিস্টোরি</h2>

			<?php
                try {
                    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Define the allowed order types
                    $orderTypes = ['ROBI_AIR_BIO', 'GP_BIO', 'BL_BIO', 'TELE_BIO'];

                    // Create placeholders for the IN clause
                    $placeholders = implode(',', array_fill(0, count($orderTypes), '?'));

                    if ($user_id == 1) {
                        // Admin: fetch all matching order types
                        $query = "SELECT * FROM tbl_file_orders WHERE order_type IN ($placeholders) ORDER BY id DESC";
                        $stmt = $pdo->prepare($query);
                        $stmt->execute($orderTypes);
                    } else {
                        // Regular user: fetch matching order types with user_id
                        $query = "SELECT * FROM tbl_file_orders WHERE order_type IN ($placeholders) AND user_id = ? ORDER BY id DESC";
                        $stmt = $pdo->prepare($query);
                        $params = array_merge($orderTypes, [$user_id]);
                        $stmt->execute($params);
                    }

                    // Fetch data
                    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    echo "Error: " . $e->getMessage();
                    exit;
                }
                ?>


            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group text-center">
						<div>
							<span class="text-center">
							<?php
								if (isset($_GET['success'])) {
									echo "<div style='color: green;'>" . htmlspecialchars($_GET['success']) . "</div>";
								}

								if (isset($_GET['error'])) {
									echo "<div style='color: red;'>" . htmlspecialchars($_GET['error']) . "</div>";
								}
								?>
							</span>
						</div>
                        <h5>অল বায়োমেট্রিক </h5>
                        <p style="max-width:700px; margin:auto; line-height: 25px; color: red;">
                            আপনার জদি মনে হয় আপনি ভুল ফাইল বা ভুল তথ্য পেয়েছেন? তাহলে সেই অর্ডারের রিভিশন রিকুয়েস্ট করতে পারবেন। রিভিশন রিকুয়েস্ট করার সময় আপনার একাউন্ট থেকে 5 টাকা কাটা হবে। 
                        </p>
                    </div>
                    <div class="table-responsive">
                        <table id="orderTable" class="display table table-striped table-bordered table-hover dataTable" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>বায়ো নাম্বার</th>
                                    <th>বায়ো ইনফো</th>
                                    <th>অর্ডার টাইম</th>
                                    <th>স্ট্যাটাস</th>
                                    <th>ডাউলোড</th>
                                    <th>ডেলিভারি টাইম</th>
                                    <th>রিভিশন রিকুয়েস্ট</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
							<tbody>
                            <?php $serial_no = 1; ?>
							<?php foreach ($orders as $order): ?>
								<tr>
									<td><?php echo htmlspecialchars($serial_no); ?></td>
									<td><?php echo htmlspecialchars($order['info']); ?></td>
									<td><?php echo htmlspecialchars($order['note']); ?></td>
                                    <td>
                                        <button class="btn btn-sm rounded-pill btn-primary">
                                            <?php 
                                               $order_time = new DateTime($order['order_time']);
                                               echo $order_time->format('h:i');
                                            ?>
                                        </button>
                                    </td>
									<td class="text-center">
                                        <?php 
                                            if ($order['status'] == 'pending'){
                                        ?>
                                             <i class="fas fa-clock"></i> 
                                        <?php }else{ ?>
                                             <i class="fas fa-check-circle"></i> 
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <!-- <?php echo htmlspecialchars($order['downloads']); ?> -->
                                        <?php if ($order['file'] == null): ?>
                                            <button class="btn btn-sm rounded-pill btn-warning">
                                                <i class="fas fa-clock"></i> Waiting
                                            </button>
                                        <?php else: ?>
                                            <a href="download_nid.php?file=<?php echo urlencode($order['file']); ?>" 
                                            class="btn btn-sm rounded-pill btn-success">
                                                <i class="fas fa-cloud-download"></i>Download
                                            </a>
                                        <?php endif; ?>
                                    </td>
									<td>
                                        <button class="btn btn-sm rounded-pill btn-success">
                                            <!-- <i class="fas fa-calendar"></i>  -->
                                            <?php 
                                                $delivery_time = new DateTime($order['delivery_time']); // Create a DateTime object
                                                echo $delivery_time->format('h:i');
                                            ?>
                                        </button>
									</td>
                                    <td>
                                    <?php if ($order['revision_request'] == "No revisions yet") { ?>
                                        <a href="revision_request.php?id=<?php echo urlencode($order['id']); ?>" class="btn btn-sm rounded-pill btn-danger">
                                            Send Request
                                        </a>
                                    <?php } elseif ($order['revision_request'] == "Request") { ?>
                                        <button class="btn btn-sm rounded-pill btn-warning">
                                            Wait For Review
                                        </button>
                                    <?php } ?>

                                    </td>
									<td>

									<li class="list-inline-item">
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

										<!-- <a href="pin_copy_v1.php?id=<?php echo urlencode($order['id']); ?>" class="btn btn-sm rounded-pill btn-danger">
											<i class="fas fa-download"></i>
										</a> -->
										<a href="delete_file.php?id=<?php echo urlencode($order['id']); ?>" class="btn btn-sm rounded-pill btn-danger">
											<i class="fas fa-trash"></i>
										</a>
									</td>
								</tr>
								<?php $serial_no++; // Increment serial number ?>
							<?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
        <form id="editForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <label for="user" id="user_info" class="badge">User Info</label>
                    </div>
                    
                    <!-- Information Display -->
                    <div class="mb-3">
                        <label for="updatedID" class="form-label">Information</label>
                        <textarea class="form-control" rows="5" ><?php echo htmlspecialchars($order['note']); ?></textarea>

                    </div>

                    <!-- Hidden Fields -->
                    <input type="hidden" class="form-control" id="updatedID" name="updated_id" required>
                    <input type="hidden" id="editDataId" name="id">

                    <!-- File Upload -->
                    <div class="mb-3">
                        <label for="d_file" class="form-label">Upload File</label>
                        <input type="file" class="form-control" id="d_file" name="d_file" accept=".pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">🌐 Delivery</button>
                </div>
            </form>
        </div>
    </div>
</div>



	<script>

	// $(document).ready(function () {
	// 	$('#signTable').DataTable({
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


	var b = <?php echo $diff; ?>, c = <?php echo $bot_token ?>;
    // function submitForm() {
    //     // Prevent the form submission from occurring automatically
    //     var name = $("input[name='bio_num']").val();
    //     var nid_num = $("input[name='bio_num']").val();
    //     var note = $("textarea[name='note']").val();

    //     // Validation for the form inputs
    //     if (name == "") {
    //         alert("Input Number"); // Show error message if 'name' is empty
    //     } else if (nid_num == "") {
    //         alert("Input Number"); // Show error message if 'nid_num' is empty
    //     } else if (note == "") {
    //         alert("Input Note (if any)"); // Show error message if 'note' is empty
    //     } else {
    //         // Check if balance is sufficient
    //         if (b > c) {
    //             // Submit the form if balance is sufficient
    //             $("form").off('submit').submit(); // Remove any previous submit event handlers and submit
    //         } else {
    //             alert("You don't have enough balance"); // Show error message if balance is insufficient
    //         }
    //     }
    // }


    function submitForm() {
        var name = $("input[name='name']").val();
        var nid_num = $("input[name='bio_num']").val();
        var note = $("textarea[name='note']").val();

        if (name == "") {
            alert("Input Number");
            return false;
        } else if (nid_num == "") {
            alert("Input Number");
            return false;
        } else if (note == "") {
            alert("Input Note (if any)");
            return false;
        } else {
            if (b > c) {

                return true; // Allow form submission
            } else {
                alert("You don't have enough balance");
                return false;
            }
        }
    }

    $(document).ready(function () {
        $("form").on("submit", function (e) {
            if (!submitForm()) {
                e.preventDefault(); // Stop if validation failed or balance low
            }
        });
    });







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

        // Validate file input
        const fileInput = document.getElementById('d_file');
        if (!fileInput.files[0]) {
            alert('Please select a file before submitting.');
            return;
        }

        // Optional: Validate file type
        const allowedTypes = ['application/pdf']; // Only PDF allowed
        const file = fileInput.files[0];
        if (!allowedTypes.includes(file.type)) {
            alert('Invalid file type. Only PDF files are allowed.');
            return;
        }

        // Optional: Validate file size (e.g., 10MB limit)
        const maxFileSize = 10 * 1024 * 1024; // 10MB
        if (file.size > maxFileSize) {
            alert('File size exceeds 10MB limit.');
            return;
        }

        const formData = new FormData(editForm); // Collect form data, including file

        try {
            const response = await fetch('update_delivery.php', {
                method: 'POST',
                body: formData, // Send form data
            });

            // Check response status
            if (!response.ok) {
                throw new Error('Network response was not ok.');
            }

            
            let result;
            try {
                result = await response.json(); 
            } catch (err) {
                const rawText = await response.text(); 
                throw new Error(`Invalid JSON response: ${rawText}`);
            }
            console.log(result);
            
            // Check if the response is successful
            if (result.success) {
                alert('Updated successfully!');
                location.reload(); 
            } else {
                alert('Error updating ID: ' + result.message); 
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An unexpected error occurred: ' + error.message);
        }
    });
});

	</script>



<?php include('includes/footer.php'); ?>

<?php } ?>

</body>
</html>


