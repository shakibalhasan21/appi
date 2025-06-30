<?php
session_start();

if (!isset($_SESSION['uid'])) {
   header('location:logout.php');
   die();
} else {
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
      $log_channel = $row['log_channel'];
      $server_pin = $row['server_pin'];
      $server_copy =  $row['server_copy'];
		$api_key =  $row['robi_token'];
		$pdf_copy =  $row['pdf_copy'];
   }

   $sql = $obj->get_balance($user_id);
   $balance = mysqli_fetch_array($sql);
   $diff = $balance['deposit_sum'] - $balance['withdraw_sum'];



	if (isset($_POST['submit'])) {
		if ($diff > $pdf_copy) {

			$nid = $_POST["nid"];
			$dob = $_POST["dob"];

		if ($obj->ServerMakeNID($nid, $user_id)) {
			$error_message = "This Card Data Already Exists";
		} else {
				
			// include 'Apis/sv.php';
         include 'Apis/sv_api.php';
	
			// Instantiate the NID class and get the info
			$newNID = new NID($nid, $dob);
			$nidInfo = $newNID->info();
	
			// Decode the JSON response
			$json = json_decode($nidInfo);
	
			// Check if the JSON response is valid and contains a 'status' of 1
			if ($json && isset($json->status) && $json->status == 1) {
				// Access the data within the 'data' object
				$data = $json->data;

				
				$image_url = $data->photo;
				$national_id = $_POST["nid"];
				


				try {
               $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
               $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
               $pdo->beginTransaction();

               $order_type = "Make NID"; 
               $order_details = $national_id;  // Replace with actual order details
               $nid = $national_id;  // National ID from $data
               $dob = $data->dob;  // Date of Birth from $data


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
                  ':birthPlace' => $data->birthPlace ?? null,
                  
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
                  
               ];
            
               $stmt->execute($execute_data);
            
               $pdo->commit();


               $success_message = "Order Successfully Done";

               $name_bn = $data->name ?? '';
               $name_en = $data->nameEn ?? '';
               $father = $data->father ?? '';
               $mother = $data->mother ?? '';
               $pin = $data->pin ?? '';
               $address = $data->preAddress->addressLine ?? $data->preAddress;
               $blood = $data->bloodGroup ?? '';
               $birth = $data->birthPlace ?? '';

               $nid_image = $image_url;
               
               $showFrom = false;

            } catch (PDOException $e) {
               $pdo->rollBack();
               $error_message = $e->getMessage();
            }
				
	
				// Example: Extracting values from the 'data' object
				$name = $data->nameEn;  
				$photo = $data->photo;
	
				// Proceed with the processing, for example, you could check the NID
				if ($data->nameEn) {
					// You can use the data here, for example:
                  if($obj->is_premium($user_id) != 1){
                  $withdraw = $obj->get_withdraw($user_id, $pdf_copy);
					}
					$showFrom = false;
				} else {
					$error_message = "Not Found";
				}
	
				// Generate the QR code URL with the available data
				$qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode("Name: $name\nDate of Birth: $dob\nNID: $nid");
	
			} else {
				// If the status is not 1 or the data is not found
				// echo '<script>alert("Data not found or invalid response")</script>';
				$error_message = "Data not found or invalid response";
			}
		}
		} else {
			// Handle insufficient balance
			$error_message = "You don't have enough balance";
		}
	}







 
	
}
?>

<?php if ($showFrom) { ?>
   <!DOCTYPE html>
   <html lang="en">

   <head>
      <meta charset="utf-8">
      <meta content="width=device-width, initial-scale=1.0" name="viewport">
      <meta content="" name="description">
      <meta content="" name="keywords">
      <title>SERVER SEBA ONLINE- Automake NID</title>
      <link href="https://surokkha.gov.bd/favicon.png" rel="icon">
      <link href="https://surokkha.gov.bd/favicon.png" rel="apple-touch-icon">
      <link href="https://fonts.gstatic.com" rel="preconnect">
      <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.1.1/css/all.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" type="text/javascript"></script>
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
      <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
      <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
      <link href="assets/css/style.css" rel="stylesheet">
   </head>

   <body>
    
      <?php include('includes/head.php'); ?>
      <?php include('includes/sidebar.php'); ?>
         <main id="main" class="main mt-5">
         <div class="card mb-2">
            <marquee style="border:1px solid #05C3FB; padding:5px;margin:10px;border-radius:3px;">
               <?php echo $notice  ?>
            </marquee>        
         </div>
         <section class="bg-diffrent">

            <div id="inp" class="container card px-4 py-4">
               <p><?php if ($diff > $pdf_copy) {echo " ";} else {$error_message = "You don't have enough balance";} ?></p>

               <!-- Header Title -->
               <div class="text-center mb-4">
                  <h5 class="text-dark">জাতীয় পরিচয়পত্র তথ্য অনুসন্ধান</h5>
                  <hr class="stylish-hr hr-dashed">
               </div>

               <style>
                  .stylish-hr {
                     border: 0;
                     /* For dashed lines, it's often better to use the border-top property */
                     border-top: 2px dashed #adb5bd; /* Adjust color and thickness */
                     height: 0; /* Reset height as border-top provides the visual */
                     margin: 1.5rem 0;
                  }
               </style>

               
               <form action="" method="post">
                  <div class="row">
                     <!-- Two column layout for inputs -->
                     <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">আইডি নাম্বার</label>
                        <input type="text" class="form-control form-control-lg" id="nid" placeholder="0123456789" name="nid">
                     </div>
                     <div class="col-md-6 mb-3">
                        <label class="form-label text-muted small">জন্ম তারিখ</label>
                        <input type="text" class="form-control form-control-lg" id="dob" placeholder="1999-12-31" name="dob">
                     </div>
                  </div>

                  <!-- Info Alert -->
                  <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                     <i class="bi bi-info-circle-fill me-2"></i>
                     <div>
                        একাউন্ট থেকে <?php echo $pdf_copy; ?> টাকা কাটা হবে
                     </div>
                  </div>

                  <!-- Submit Button -->
                  <div class="d-grid gap-2 mb-3">
                     <button type="submit" name="submit" class="btn btn-primary btn-lg py-3" onclick="submit()">
                        <i class="bi bi-download me-2"></i>ডাউনলোড করুন
                     </button>
                  </div>

                  <!-- Back Home Button -->
                  <div class="d-grid">
                     <a href="dashboard.php" class="btn btn-warning btn-lg py-3 text-decoration-none">
                        <i class="bi bi-house-door me-2"></i>Back Home
                     </a>
                  </div>
               </form>

               
            </div>

            <style>
            /* Additional CSS for better styling */
            .form-control-lg {
               padding: 12px 16px;
               font-size: 16px;
               border-radius: 8px;
               border: 1px solid #dee2e6;
            }

            .form-control-lg:focus {
               border-color: #86b7fe;
               box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            }

            .form-label {
               font-weight: 500;
               margin-bottom: 8px;
            }

            .btn-lg {
               font-size: 16px;
               font-weight: 500;
               border-radius: 8px;
            }

            .alert-info {
               background-color: #cff4fc;
               border-color: #b6effb;
               color: #055160;
               border-radius: 8px;
               padding: 16px;
            }

            .card {
               /* border-radius: 12px; */
               /* border: 1px solid #e9ecef; */
               box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }

            h5 {
               font-weight: 700;
               color: #495057;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
               .col-md-6 {
                  margin-bottom: 1rem;
               }
               
               .btn-lg {
                  padding: 16px 20px;
               }
            }
            </style>	

            <div class="container card px-3 py-5 mt-4">
               <h2 class="text-center">Order History</h2>
               <?php

                  try {
                     $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
                     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                     // Check if the user_id is 1
                     if ($user_id == 1) {
                        // Get all data if user_id is 1
                        $query = "SELECT * FROM tbl_orders WHERE order_type = 'Make NID' ORDER BY id DESC";
                     } else {
                        // Get data based on the specific user_id if not 1
                        $query = "SELECT * FROM tbl_orders WHERE order_type = 'Make NID' AND user_id = $user_id ORDER BY id DESC";
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

                  <table id="orderTable" class="table table-striped table-bordered align-middle text-center table-hover" style="width:100%">
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
                          
                                 <a href="nid_copy.php?id=<?php echo urlencode($order['id']); ?>" class="btn btn-sm rounded-pill btn-danger">
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
         //    $('#orderTable').DataTable({
         //       responsive: true,
         //    });
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
   <?php } else { ?>
      <html lang="en">

      <head>
         <title>SERVER SEBA ONLINE- Automake NID</title>
         <link href="https://sonnetdp.github.io/nikosh/css/nikosh.css" rel="stylesheet" type="text/css">
         <!-- <link rel="stylesheet" href="css/style.css"> -->
         <link href="https://fonts.maateen.me/kalpurush/font.css" rel="stylesheet">
         <link rel="stylesheet" href="assets/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
         <script src="assets/JavaScript/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
         <script src="assets/JavaScript/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
         <script src="assets/JavaScript/jquery-1.11.1.min.js"></script>
         <link rel="stylesheet" href="assets/css/tx1337.css" data-n-g="" />
         <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
         <style>
            @media print {
               @page {
                  margin: 0;
               }
            }
         </style>

      <style>
          #sign {
            transform: rotate(45deg); /* Rotate the image by 45 degrees */
            transition: transform 0.3s ease; /* Optional: Add a smooth transition */
        }
      </style>
         <script>
            
            window.onload = function() {
               var hub3_code = '<pin><?php echo isset($pin) ? $pin : ''; ?></pin><name><?php echo isset($name_en) ? $name_en : ''; ?></name><DOB><?php echo isset($dob) ? $dob : ''; ?>/DOB><FP></FP><F>Right Index</F><TYPE>A</TYPE><V>2.0</V><ds>302c0214103fc01240542ed736c0b48858c1c03d80006215021416e73728de9618fedcd368c88d8f3a2e72096d</ds>';

               console.log(hub3_code);

               PDF417.init(hub3_code);

               var barcode = PDF417.getBarcodeArray();

               // block sizes (width and height) in pixels
               var bw = 2;
               var bh = 2;

               // create canvas element based on number of columns and rows in barcode
               var canvas = document.createElement('canvas');
               canvas.width = bw * barcode['num_cols'];
               canvas.height = bh * barcode['num_rows'];
               document.getElementById('barcode').appendChild(canvas);

               var ctx = canvas.getContext('2d');

               // graph barcode elements
               var y = 0;
               // for each row
               for (var r = 0; r < barcode['num_rows']; ++r) {
                  var x = 0;
                  // for each column
                  for (var c = 0; c < barcode['num_cols']; ++c) {
                     if (barcode['bcode'][r][c] == 1) {
                        ctx.fillRect(x, y, bw, bh);
                     }
                     x += bw;
                  }
                  y += bh;
               }
            }
         </script>


     
         <script src="assets/JavaScript/bcmath-min.js" type="text/javascript"></script>
         <script src="assets/JavaScript/pdf417-min.js" type="text/javascript"></script>
      </head>

      <body class="">
         <div id="__next" data-reactroot="">
            <main>
               <div>
                  <main class="w-full overflow-hidden">
                     <div class="container w-full py-12 lg:flex lg:items-start" style="padding-top: 20px;">
                        <div class="w-full lg:pl-6">
                           <div class="flex items-center justify-center">
                              <div class="w-full">

                                 <div class="flex items-start gap-x-2 bg-transparent mx-auto w-fit" id="nid_wrapper">
                                    <div id="nid_front" class="w-full border-[1.999px] border-black">
                                       <header class="px-1.5 flex items-start gap-x-2 justify-between relative">
                                          <img class="w-[38px] absolute top-1.5 left-[4.5px]" src="assets/Images/bangladeshicon.png" alt="assets/Images/bangladeshicon2.png" />
                                          <div class="w-full h-[60px] flex flex-col justify-center">
                                             <h3 style="font-size:20px" class="text-center font-medium tracking-normal pl-11 bn leading-5"><span style="margin-top:1px;display:inline-block">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</span></h3>
                                             <p class="text-[#007700] text-right tracking-[-0rem] leading-3" style="font-size:11.46px;font-family:arial;margin-bottom:-0.02px">Government of the People&#x27;s Republic of Bangladesh</p>
                                             <p class="text-center font-medium pl-10 leading-4" style="padding-top:0px"><span class="text-[#ff0002]" style="font-size:10px;font-family:arial">National ID Card</span><span class="ml-1" style="display:inline-block"><span style="font-size:13px;font-family:arial">/</span></span><span class="bn ml-1" style="font-size:13.33px">জাতীয় পরিচয় পত্র</span></p>
                                          </div>
                                       </header>
                                       <div class="w-[101%] -ml-[0.5%] border-b-[1.9999px] border-black" style="width: 100%;margin-left: 0;"></div>
                                       <div class="pt-[3.8px] pr-1 pl-[2px] bg-center w-full flex justify-between gap-x-2 pb-5 relative">
                                          <div class="absolute inset-x-0 top-[2px] mx-auto z-10 flex items-start justify-center"><img style="background:transparent;width: 114px;height: 114px;" class="ml-[20px] w-[125px] h-[116px" src="assets/Images/flower-logo.png" alt="" /></div>

                                          <div class="relative z-50">
                                             <?php if (isset($nid_image)){?>
                                                <label for="photo" class="custom-file-upload">
                                                   <img style="margin-top:-2px" id="userPhoto" class="w-[68.2px] h-[78px]" src="<?php echo htmlspecialchars($nid_image); ?>" alt="">
                                                <!-- <img style="margin-top:-2px" id="userPhoto" class="w-[68.2px] h-[78px]" alt="photo" src="photo/*" /> -->
                                             </label>
                                                <?php }else{?>
                                             <label for="photo" class="custom-file-upload">
                                                <img style="margin-top:-2px" id="userPhoto" class="w-[68.2px] h-[78px]" alt="photo" src="photo/*" />
                                             </label>
                                             <input id="photo" type="file" style="display: none;" onchange="previewUserPhoto(this);" accept="photo/*" />
                                                <?php }?>

                                             <script>
                                                function previewUserPhoto(input) {
                                                   if (input.files && input.files[0]) {
                                                      var reader = new FileReader();

                                                      reader.onload = function(e) {
                                                         document.getElementById('userPhoto').src = e.target.result;
                                                      }

                                                      reader.readAsDataURL(input.files[0]);
                                                   }
                                                }
                                             </script>
                                             <div class="text-center text-xs flex items-start justify-center pt-[5px] w-[68.2px] mx-auto h-[38.5px] overflow-hidden" id="card_signature"><span style="font-family:Comic sans ms"></span>
                                                <label for="userSignUpload" class="custom-file-upload">
                                                   <img id="sign" src="images/fprint.svg" alt="sign" />
                                                </label>
                                                <!-- <input id="userSignUpload" type="file" style="display: none;" onchange="previewUserSign(this);" accept="photo/*" />

                                                <script>
                                                   function previewUserSign(input) {
                                                      if (input.files && input.files[0]) {
                                                         var reader = new FileReader();

                                                         reader.onload = function(e) {
                                                            document.getElementById('sign').src = e.target.result;
                                                         }

                                                         reader.readAsDataURL(input.files[0]);
                                                      }
                                                   }
                                                </script> -->
                                             </div>
                                          </div>
                                          <div class="w-full relative z-50">
                                             <div style="height:5px"></div>
                                             <div class="flex flex-col gap-y-[10px]" style="margin-top: 1px;">
                                                <div>
                                                   <p class="space-x-4 leading-3" style="padding-left:1px"><span class="bn" style="font-size:16.53px">নাম:</span><span class="" style="font-size:16.53px;padding-left:3px;-webkit-text-stroke:0.4px black" id="nameBn">
                                                      <?php echo isset($name_bn) ? htmlspecialchars($name_bn) : 'No name available'; ?>
                                                   </span></p>
                                                </div>
                                                <div style="margin-top: 1px;">
                                                   <p class="space-x-2 leading-3" style="margin-bottom:-1.4px;margin-top:1.4px;padding-left:1px"><span style="font-size:11px">Name:</span><span style="font-size:12.73px;padding-left:1px" id="nameEn">
                                                      <?php echo isset($name_en) ? htmlspecialchars($name_en) : 'No name available'; ?>
                                                      </span>
                                                   </p>
                                                </div>





                                                <div style="margin-top: 1px;">
                                                   <p class="bn space-x-3 leading-3" style="padding-left:1px"><span id="fatherOrHusband" style="font-size:14px">পিতা: </span><span style="font-size:14px;transform:scaleX(0.724)" id="card_father_name">
                                                      <?php echo isset($father) ? htmlspecialchars($father) : 'father name available'; ?>
                                                      </span>
                                                   </p>
                                                </div>


                                                <div style="margin-top: 1px;">
                                                   <p class="bn space-x-3 leading-3" style="margin-top:-2.5px;padding-left:1px"><span style="font-size:14px">মাতা: </span><span style="font-size:14px;transform:scaleX(0.724)" id="card_mother_name">
                                                      <?php echo isset($mother) ? htmlspecialchars($mother) : 'mother name available'; ?>
                                                </span></p>
                                                </div>
                                                <div class="leading-4" style="font-size:12px;margin-top:-1.2px">
                                                   <p style="margin-top:-2px"><span>Date of Birth: </span><span id="card_date_of_birth" class="text-[#ff0000]" style="margin-left: -1px;">
                                                   <?php
                                                      $date = new DateTime($dob);
                                                      $formatted_date = $date->format('d M Y'); // Format to '11 Nov 1997'
                                                      echo isset($dob) ? htmlspecialchars($formatted_date) : '';
                                                      ?>
                                                   </span></p>
                                                </div>
                                                <div class="-mt-0.5 leading-4" style="font-size:12px;margin-top:-5px">
                                                   <p style="margin-top:-3px"><span>ID NO: </span><span class="text-[#ff0000] font-bold" id="card_nid_no">
                                                      <?php echo isset($nid) ? htmlspecialchars($nid) : 'nid name available'; ?>
                                                   </span></p>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div id="nid_back" class="w-full border-[1.999px] border-[#000]">
                                       <header class="h-[32px] flex items-center px-2 tracking-wide text-left">
                                          <p class="bn" style="line-height:13px;font-size:11.33px;letter-spacing:0.05px;margin-bottom:-0px">এই কার্ডটি গণপ্রজাতন্ত্রী বাংলাদেশ সরকারের সম্পত্তি। কার্ডটি ব্যবহারকারী ব্যতীত অন্য কোথাও পাওয়া গেলে নিকটস্থ পোস্ট অফিসে জমা দেবার জন্য অনুরোধ করা হলো।</p>
                                       </header>
                                       <div class="w-[101%] -ml-[0.5%] border-b-[1.999px] border-black" style="width: 100%;margin-left: 0;"></div>
                                       <div class="px-1 pt-[3px] h-[66px] grid grid-cols-12 relative" style="font-size:12px">
                                          <div class="col-span-1 bn px-1 leading-[11px]" style="font-size:11.73px;letter-spacing:-0.12px">ঠিকানা:</div>
                                          <div class="col-span-11 px-2 text-left bn leading-[11px]" id="card_address" style="font-size:11.73px;letter-spacing:-0.12px">
                                             <?php echo isset($address) ? htmlspecialchars($address) : 'address not available'; ?>
                                          </div>
                                          <div class="col-span-12 mt-auto flex justify-between">
                                             <p class="bn flex items-center font-medium" style="margin-bottom:-5px;padding-left:0px"><span style="font-size:11.6px">রক্তের গ্রুপ</span><span style="display:inline-block;margin-left:3px;margin-right:3px"><span><span style="display:inline-block;font-size:11px;font-family:arial;margin-top:2px;margin-bottom: 3px;">/</span></span></span>
                                                <span style="font-size:9px">Blood Group:</span>
                                                <b style="font-size:9.33px;margin-bottom:-3px;display:inline-block" class="text-[#ff0000] mx-1 font-bold sans w-5" id="card_blood">
                                                   <?php echo isset($blood) ? htmlspecialchars($blood) : ''; ?>
                                                </b><span style="font-size:10.66px"> জন্মস্থান: </span><span class="ml-1" id="card_birth_place" style="font-size:10.66px"><?php echo isset($birth) ? $birth : ''; ?></span>
                                             </p>
                                             <div class="text-gray-100 absolute -bottom-[2px] w-[30.5px] h-[13px] -right-[2px] overflow-hidden" style="margin-right: 1px;margin-bottom: 1px;">
                                                <img src="assets/Images/mududdron.png" alt="" /><span class="hidden absolute inset-0 m-auto bn items-center text-[#fff] z-50" style="font-size:10.66px"><span class="pl-[0.2px]">মূদ্রণ:</span><span class="block ml-[3px]">০১</span></span>
                                                <div class="hidden w-full h-full absolute inset-0 m-auto border-[20px] border-black z-30"></div>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="w-[101%] -ml-[0.5%] border-b-[1.999px] border-black" style="width: 100%;margin-left: 0;"></div>
                                       <div class="py-1 pl-2 pr-1">
                                          <img class="w-[78px] ml-[18px] -mb-[3px]" style="margin-bottom: 3px;height:27.3px;" src="assets/Images/adminsign.jpg" />
                                          <div class="flex justify-between items-center -mt-[5px]">
                                             <p class="bn" style="font-size:14px">প্রদানকারী কর্তৃপক্ষের স্বাক্ষর</p>
                                             <span class="pr-4 bn" style="font-size:12px;padding-top:1px">প্রদানের তারিখ:<span class="ml-2.5 bn" id="card_date">
                                             </span>
                                             <?php
                                                function convertToBengali($number) {
                                                   $bengali_digits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
                                                   return str_replace(range(0, 9), $bengali_digits, $number);
                                                }

                                                setlocale(LC_TIME, 'bn_BD.UTF-8');
                                                $date = isset($_POST['card_date']) ? $_POST['card_date'] : date('d/m/Y');
                                                $date_in_bengali = convertToBengali($date);

                                                echo $date_in_bengali;
                                                ?>

                                          </span>
                                          </div>
                                          <div id="barcode" class="w-full h-[39px] mt-1" alt="NID Card Generator" style="margin-top: 1.5px;margin-left: -3px;">
                                             <style>
                                                canvas {
                                                   width: 102%;
                                                   height: 100%;
                                                }
                                             </style>
                                             <!---<img id="card_qr_code" class="w-full h-[39px] mt-1" alt="NID Card Generator" 
src="assets/Images/notfound.png"/>--->
                                          </div>
                                       </div>
                                    </div>
                                 </div>

                              </div>
                           </div>
                        </div>
                     </div>
               </div>
            </main>
            <br /><br /><br /><br /><br /><br /><br />
            <footer></footer>
         </div>
         <div class="Toastify"></div>
         </main>
         </div>
         <script>
            window.print();
            // Wait for a brief moment before attempting to close the window
            setTimeout(function() {
               // window.close();
            }, 3000); // You can adjust the delay as needed
         </script>
         <script>
            var finalEnlishToBanglaNumber = {
               '0': '০',
               '1': '১',
               '2': '২',
               '3': '৩',
               '4': '৪',
               '5': '৫',
               '6': '৬',
               '7': '৭',
               '8': '৮',
               '9': '৯'
            };

            String.prototype.getDigitBanglaFromEnglish = function() {
               var retStr = this;
               for (var x in finalEnlishToBanglaNumber) {
                  retStr = retStr.replace(new RegExp(x, 'g'), finalEnlishToBanglaNumber[x]);
               }
               return retStr;
            };

            var date_number = "";
            var bangla_date_number = date_number.getDigitBanglaFromEnglish();

            document.getElementById("card_date").innerHTML = bangla_date_number;
         </script>
      </body>

      </html>


   <?php } ?>