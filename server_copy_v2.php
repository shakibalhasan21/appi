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
	}

	
?>



<?php
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the order ID from the GET request
    if (isset($_GET['id'])) {
        $order_id = $_GET['id'];

        // Fetch data from tbl_orders
        $query_orders = "SELECT * FROM tbl_orders WHERE id = :order_id";
        $stmt_orders = $pdo->prepare($query_orders);
        $stmt_orders->execute([':order_id' => $order_id]);
        $order_data = $stmt_orders->fetch(PDO::FETCH_ASSOC);

        // Fetch related data from tbl_order_details
        $query_order_details = "SELECT * FROM tbl_order_details WHERE order_id = :order_id";
        $stmt_order_details = $pdo->prepare($query_order_details);
        $stmt_order_details->execute([':order_id' => $order_id]);
        $order_details_data = $stmt_order_details->fetchAll(PDO::FETCH_ASSOC);

        // Display or process the fetched data
        // echo "<h3>Order Data:</h3>";
        // echo "<pre>" . print_r($order_data, true) . "</pre>";

        // echo "<h3>Order Details:</h3>";
        // echo "<pre>" . print_r($order_details_data, true) . "</pre>";
    } else {
        echo "No order ID provided.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<title><?php if($json == null){echo "SERVER SEBA ONLINE";}else{echo $order_details_data->name_en;}?></title>
	<link href="https://surokkha.gov.bd/favicon.png" rel="icon">
	<link href="https://surokkha.gov.bd/favicon.png" rel="apple-touch-icon">
	<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.1.1/css/all.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" type="text/javascript"></script>
	<style>
    /* Define page size and margins for printing */
    @page {
        size: A4;
        margin: 0;
    }

    /* Reset body margins */
    body {
        margin: 0;
        padding: 0;
    }

    /* Background styling to fit the page */
    .background {
        background-color: lightgrey;
        position: relative;
        width: 750px;
        height: 1065px;
        margin: auto;
        transform: scale(1.08);
        text-align: left;
        margin-top: 40px;
    }

    /* Crane image scaling */
    .crane {
        max-width: 100%;
        height: 100%;
    }

    /* Styling for the title */
    .topTitle {
        position: absolute;
        left: 21%;
        top: 8%;
        width: auto;
        font-size: 42px;
        color: rgb(255, 182, 47);
    }

    /* Print button styling */
    #print {
        background: #03a9f4;
        padding: 8px;
        width: 200px;
        height: 50px;
        border: 0;
        font-size: 25px;
        font-weight: bold;
        cursor: pointer;
        box-shadow: 1px 4px 4px #878787;
        color: #fff;
        border-radius: 10px;
        margin: 80px 0;
        display: block;
    }

    /* Hide the print button when printing */
    @media print {
        /* Set dimensions for the printed page */
        /* html, body {
            width: 210mm !important;
            height: 297mm !important;
            background-color: #fff !important;
            margin: 0;
            padding: 0;
        } */

        /* Make the background fill the entire page */
        .background {
            height: 100% !important;
            width: 100% !important;
            padding: 10px;
            box-sizing: border-box;
        }

        /* Hide the print button */
        #print {
            display: none !important;
        }

        /* Prevent page breaks within content */
        .background {
            page-break-before: always;
        }

        /* Scaling to fit on the A4 page */
        .background {
            transform: scale(1);
            width: 100%;
            height: 100%;
        }
    }
</style>

</head>
<body style="text-align: center;">

		<!-- ----------------- -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
		<link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="assets/css/stylev2.css" rel="stylesheet">
		<style>
			body, td, th, h3, p {
				font-family: 'SolaimanLipi', serif !important;
			}

			.info-body {
				border: 3px solid gray;
				border-radius: 6px;
				padding: 15px;
				margin: 10px auto;
				max-width: 500px;
				background-color: #f9f9f9;
				background-image: url('img/lol.png'); /* Old background image */
				background-size: 244px;
				background-repeat: no-repeat;
				background-position: center;
			}

			.table td, .table th {
				padding: 8px;
				font-size: 14px;
			}

			.table td {
				font-weight: 400;
			}

			.profile-card img {
				width: 80px;
				height: 80px;
				border-radius: 8% !important;
			}

			@media (max-width: 768px) {
				.info-body {
					width: 100%;
					padding: 10px;
					background-size: 150px; /* Adjust background size for smaller screens */
				}

				h3 {
					font-size: 16px;
				}

				.table td, .table th {
					font-size: 12px;
				}

				.profile-card img {
					width: 60px;
					height: 60px;
				}
			}
		</style>

		<div class="container mt-4">
			<div class="row justify-content-center">
				<div class="col-md-8 col-sm-12">
					<div class="card info-body">
						<h3 class="text-center py-3 bg-success text-dark rounded-top border border-dark py-2 mb-4" style="font-weight: 800;">
							<i class="fa fa-id-card"></i> জাতীয় পরিচিতি তথ্য
						</h3>
						<div class="row justify-content-center mb-4">
							<div class="col-4 text-center">
								<img style="border-radius: 8%!important; width: 80px;" src="<?php echo htmlspecialchars($order_details_data[0]['photo']); ?>" alt="Profile" class="rounded-circle">
							</div>
						</div>
						<table class="table">
							<tbody>
								<tr>
									<th>নাম</th>
									<td class="dots">:</td>
									<td><?php echo $order_details_data[0]['name']; ?></td>
								</tr>
								<tr>
									<th>Name</th>
									<td class="dots">:</td>
									<td><?php echo $order_details_data[0]['name_en']; ?></td>
								</tr>
								<tr>
									<th>পিতা</th>
									<td class="dots">:</td>
									<td><?php echo $order_details_data[0]['father']; ?></td>
								</tr>
								<tr>
									<th>মাতা</th>
									<td class="dots">:</td>
									<td><?php echo $order_details_data[0]['mother']; ?></td>
								</tr>
								<tr>
									<th>লিঙ্গ</th>
									<td class="dots">:</td>
									<td><?php echo $order_details_data[0]['gender_bn']; ?></td>
								</tr>
								<tr>
									<th>Date of Birth</th>
									<td class="dots">:</td>
									<td><?php echo $order_details_data[0]['date_of_birth']; ?></td>
								</tr>
								<tr>
									<th>NID No</th>
									<td class="dots">:</td>
									<td><?php echo $order_details_data[0]['national_id']; ?></td>
								</tr>
								<tr>
									<th>Pin No</th>
									<td class="dots">:</td>
									<td><?php echo $order_details_data[0]['pin']; ?></td>
								</tr>
								<tr>
									<th>স্থায়ী ঠিকানা</th>
									<td class="dots">:</td>
									<td><?php echo $order_details_data[0]['permanent_full_address']; ?></td>
								</tr>
								<tr>
									<th>বর্তমান ঠিকানা</th>
									<td class="dots">:</td>
									<td><?php echo $order_details_data[0]['present_full_address']; ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<button class="btn btn-info d-block mx-auto mt-3" id="print" onclick="window.print()"><i class="fa fa-print" style="font-size:24px"></i> PRINT</button>


<?php } ?>


</html>


