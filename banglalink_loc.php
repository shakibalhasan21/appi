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
		$rebot_token_msg = $row['rg_msg'];
		$notice =  $row['notice'];
		$approval = $row['approval'];
		$login =  $row['login'];
		$register = $row['register'];
		$bot_token =  $row['bot_token'];
		$bl_location =  $row['bl_location'];
	}

	$sql = $obj->get_balance($user_id);
	$balance = mysqli_fetch_array($sql);
	$diff = $balance['deposit_sum'] - $balance['withdraw_sum'];


	if(isset($_POST['submit'])){
		if ($diff > $bl_location) {
			$number = $_POST["number"];
			
			include 'Apis/numbl.php';
			$newNID = new Number($number);
			$nidInfo = $newNID->info();
			$json = json_decode($nidInfo, true);

			if ($json){
				$withdraw = $obj->get_withdraw($user_id, $bl_location);
				$showFrom = false;
			}else{
				echo '<script>alert("Not Found..!")</script>';
			}
            
		}else{
			echo "<script>alert(' You don't have enough balance );</script>";
		}
	}

	
?>
<?php if($showFrom){ ?>
<html lang="en">
<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<meta content="" name="description"><meta content="" name="keywords">
	<title>BANGLALINK NUMBER TO LOCATION</title>
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
	<?php include("includes/head.php");?>

	<?php include('includes/sidebar.php'); ?>
	<main id="main" class="main">
		<section class="section profile">
			<div id="inp" class="container card px-3 py-5 mt-6 col-md-12 mb-5">
				<marquee style="padding: 10px;background: white;border-radius: 5px;border: 1px solid #0d6efd;"><?php echo $notice ?></marquee>
				<p><?php if ($diff > $bl_location) {echo " ";} else {echo ' <div class="alert alert-danger"><strong>Sorry !</strong> You  do not have enough balance.</div>';} ?></p>

				<form action="" method="POST">
					<div class="row">
						<div class="mb-3 myDiv" id="showOne">
							<label>NUMBER :</label>
							<input type="text" class="form-control" id="number" placeholder="019********" name="number" required="">
						</div>
						
						<div class="mb-3 myDiv" id="showOne">
                            </select>
						</div>
						<span style="font-size: 12px;padding: 10px;margin: auto;text-align: center;color: red;">
						<b>Note:</b> আপনার ফাইলের জন্য <?php echo $bl_location ?> টাকা চার্জ কাটা হবে .</span>
						<!-- <input type="submit" name="submit" class="btn btn-danger" onclick="submit()" value="Submit" /> -->
						<div style="text-align: center;">
							<input type="submit" name="submit" class="btn btn-outline-success" onclick="submit()" style="font-weight: bold;" value="Submit" />
						</div>
					</div>
				</form>
			</div>	
		</section>
	</main>
	<script>
	var b = <?php echo $diff; ?>, c = <?php echo $bl_location ?>;
    function submit(){
    var nid = $("#number").val();
    if(nid == ""){
         alert("Input Number");
    }else{
        if(b > c){
           $("form").submit();
        }else{
            alert("You don't have enough balance");
        }
    }
    };
	</script>
<?php include('includes/footer.php'); ?>
<?php }else{ ?>
 
 
 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BANGLALINK NUMBER TO LOCATION</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 100%;
            margin: 20px;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            text-align: center;
            overflow-x: auto;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
            word-break: break-word;
        }
        th {
            background-color: #f4f4f4;
        }
        a {
            color: blue;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
        }
        button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .print-btn {
            background-color: #4CAF50;
            color: white;
        }
        .back-btn {
            background-color: #f44336;
            color: white;
        }

		@media print {
            .footer {
                display: none;
            }
        }
    </style>

</head>
<body>
    <div class="card">
		<div class="container">
			<h1>Your Number Information</h1>
			<table id="data-table">
				<tr><th>Name</th><th>Information</th></tr>
			</table>
			<p id="dev-by"><strong>DEV BY: SERVERSEBA.XYZ</strong></p>
		</div>

		<div class="footer">
			<button class="back-btn" onclick="window.location.href='banglalink_loc.php'">Back</button>
			<button class="print-btn" onclick="window.print()">Print</button>
		</div>

		<script>
			const response = <?php echo json_encode($json); ?>;

			const table = document.getElementById('data-table');
			const data = response.data;

			for (let key in data) {
				let value = data[key];
				if (key === 'Google_Maps_URL') {
					value = `<a href="${value}" target="_blank">View Location</a>`;
				}
				table.innerHTML += `<tr><td>${key}</td><td>${value}</td></tr>`;
			}
		</script>
    </div>
</body>
</html>


 
 
<?php } ?>
<?php } ?>