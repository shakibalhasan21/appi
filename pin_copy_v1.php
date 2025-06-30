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
    /* Print button styling */
    #print {
        background: #03a9f4;
        padding: 8px 16px;
        height: 50px;
        border: none;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        box-shadow: 1px 4px 4px #878787;
        color: #fff;
        border-radius: 10px;
        margin: 80px 0;
        display: block;
    }

    body, td, th, h3, p {
        font-family: 'SolaimanLipi', serif !important;
    }

    .info-body {
        border: 3px solid gray;
        border-radius: 6px;
        padding: 15px;
        margin: 10px auto;
        background-color: #f9f9f9;
        background-image: url('img/lol.png');
        background-size: 255px;
        background-repeat: no-repeat;
        background-position: center;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table td, .table th {
        padding: 8px;
        font-size: 14px;
        text-align: left;
        word-break: break-word;
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
            padding: 10px;
            background-size: 150px;
        }

        .table td, .table th {
            font-size: 12px;
        }
    }

    /* Print styles */
    @media print {
        @page {
            size: A4;
            margin: 0;
        }

        html, body {
            /* width: 210mm; */
            /* height: 297mm; */
            margin:0 !important;
            padding: 0 !important;
            background-color: #fff !important;
        }

        #print {
            display: none !important;
        }

        /* Make container and column full width */
        .container,
        .row,
        .col-md-8,
        .col-sm-12 {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .info-body {
            border: none !important;
            border-radius: 0 !important;
            margin: 0 !important;
            padding: 10mm !important;
            box-shadow: none !important;
            background-color: #fff !important;
            /* background-size: contain; */
            background-position: center;
        }

        .table td, .table th {
            font-size: 12pt;
        }
    }
</style>
</style>

</head>
<body style="text-align: center;">

		<!-- ----------------- -->
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
		<link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="assets/css/stylev2.css" rel="stylesheet">

		<div class="container mt-4">
			<div class="row justify-content-center">
				<div class="col-md-8 col-sm-12">
					<div class="card info-body shadow-lg border-0">
						<h3 class="text-center py-3 bg-primary text-white rounded-top" style="font-weight: 800;">
							<i class="fa fa-id-card"></i> জাতীয় পরিচিতি তথ্য
						</h3>
						<table class="table table-striped table-bordered">
							<tbody>
								<tr><th style="width: 30%;">নাম</th><td class="text-center">:</td><td><?= htmlspecialchars($order_details_data[0]['name']) ?></td></tr>
								<tr><th>Name</th><td class="text-center">:</td><td><?= htmlspecialchars($order_details_data[0]['name_en']) ?></td></tr>
								<tr><th>Date of Birth</th><td class="text-center">:</td><td><?= htmlspecialchars($order_details_data[0]['date_of_birth']) ?></td></tr>
								<tr><th>NID No</th><td class="text-center">:</td><td><?= htmlspecialchars($order_details_data[0]['national_id']) ?></td></tr>
								<tr><th>Pin No</th><td class="text-center">:</td><td><?= htmlspecialchars($order_details_data[0]['pin']) ?></td></tr>
								<tr><th>স্থায়ী ঠিকানা</th><td class="text-center">:</td><td><?= htmlspecialchars($order_details_data[0]['permanent_full_address']) ?></td></tr>
								<tr><th>বর্তমান ঠিকানা</th><td class="text-center">:</td><td><?= htmlspecialchars($order_details_data[0]['present_full_address']) ?></td></tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<button class="btn btn-info d-block mx-auto mt-4 px-5 py-2" id="print" onclick="window.print()">
			<i class="fa fa-print" style="font-size:20px"></i> PRINT
		</button>


</body>
<?php } ?>


</html>


