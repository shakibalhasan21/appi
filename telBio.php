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
		$teletalk_bio =  $row['teletalk_bio'];
	}

	$sql = $obj->get_balance($user_id);
	$balance = mysqli_fetch_array($sql);
	$diff = $balance['deposit_sum'] - $balance['withdraw_sum'];



	if (isset($_POST['submit'])) {

		if ($diff > $teletalk_bio) {
            $nid = 111;
			$name = $_POST["bio_num"];
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
                    $order_type = "TEL BIO";                 // Example order type
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
                    
                    $withdraw = $obj->get_withdraw($user_id, $teletalk_bio);
                    // Success message
                    $success_message = "অর্ডার সফলভাবে সম্পন্ন হয়েছে!";
                
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
	  input {
        color: #000 !important;
        }
            textarea {
        color: #000 !important;
        }
            select {
        color: #000 !important;
        }

        textarea {
            height:100px!important;
        }
	  </style>
	<main id="main" class="main">
    <section class="section">
        <div class="card">
        <div class="card-body" style="background:lightgrey">
                <h2 class="card-title text-center">টেলিটক বায়োমেট্রিক অর্ডার </h2>


                <div class="form-group text-center">
                    <h5>
                        <p style="max-width:90%; margin:auto;">

                        </p></h5><h5>আপনি আপনার টেলিটক বায়োমেট্রিক অর্ডার করুন,খুব শিগ্রই ডেলিভারি করা হবে।</h5><h5>
                </h5></div>
                <div align="center" class="form-group">
                    <form method="POST" action="telBio.php">
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-6 form-group"><label for="company">টেলিটক নাম্বার দিন *</label>
                                <input type="number" class="form-control" name="bio_num" required="">
                            </div><!--  col-md-6   -->
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-6 pt-3">
                                <div class="form-group">
                                    <label for="mobile">টেলিটক বায়োমেট্রিক সম্পর্কে বিস্তারিত লিখুন।(যদি কিছু বলার থাকে)</label>
                                    <textarea class="form-control" name="note" rows="2"></textarea>
                                </div>
                            </div>
                        </div><!--  row   -->
                        <div style="display: flex;justify-content: center; margin-bottom: 30px; padding-top: 15px;">
                            <span>টেলিটক বায়োমেট্রিকর জন্য <?php echo $teletalk_bio ?> টাকা কাটা হবে।</span><br>
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

						// Check if the user_id is 1
						if ($user_id == 1) {
							// Get all data if user_id is 1
							$query = "SELECT * FROM tbl_file_orders WHERE order_type = 'TEL BIO' ORDER BY id DESC";
						} else {
							// Get data based on the specific user_id if not 1
							$query = "SELECT * FROM tbl_file_orders WHERE order_type = 'TEL BIO' AND user_id = $user_id ORDER BY id DESC";
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
                        <h5>টেলিটক বায়োমেট্রিক</h5>
                        <p style="max-width:700px; margin:auto; line-height: 25px; color: red;">
                            আপনার জদি মনে হয় আপনি ভুল ফাইল বা ভুল তথ্য পেয়েছেন? তাহলে সেই অর্ডারের রিভিশন রিকুয়েস্ট করতে পারবেন। রিভিশন রিকুয়েস্ট করার সময় আপনার একাউন্ট থেকে 5 টাকা কাটা হবে। 
                        </p>
                    </div>
                    <div class="table-responsive">
                    <table id="signTable" class="display table table-striped table-bordered table-hover dataTable" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>আইডি</th>
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
									<td><?php echo htmlspecialchars($order['name']); ?></td>
									<td><?php echo htmlspecialchars($order['note']); ?></td>
                                    <td>
                                        <button class="btn btn-sm rounded-pill btn-primary">
                                            <?php 
                                               $order_time = new DateTime($order['order_time']);
                                               echo $order_time->format('h:i');
                                            ?>
                                        </button>
                                    </td>
									<td>
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
                        <textarea class="form-control" rows="5" readonly><?php echo htmlspecialchars($order['note']); ?></textarea>

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

	$(document).ready(function () {
		$('#signTable').DataTable({
			responsive: true,
		});
	});


	var b = <?php echo $diff; ?>, c = <?php echo $teletalk_bio ?>;
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
        var name = $("input[name='bio_num']").val();
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
                // Open WhatsApp before submit
                var message = `📥 New Submission:\n 🆔 AIR_BIO: ${nid_num}\n📝 Note: ${note}`;
                var phoneNumber = "8801956784485";
                var whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
                window.open(whatsappUrl, '_blank');

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


