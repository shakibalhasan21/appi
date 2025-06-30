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




// Qr Code
 // Set the output file path
 $qr_file = 'qrcode.png';
    
 // Set the data to encode
 $qr_data = "NID: " . $order_details_data[0]['national_id'] . " " . "NAME: ". $order_details_data[0]['name_en'];

 // Generate the QR code
 QRcode::png($qr_data, $qr_file, QR_ECLEVEL_L, 10, 2);

?>




<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<title><?php if($json == null){echo "Nid Info";}else{echo $order_details_data->name_en;}?></title>
	<link href="https://surokkha.gov.bd/favicon.png" rel="icon">
	<link href="https://surokkha.gov.bd/favicon.png" rel="apple-touch-icon">
	<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.1.1/css/all.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" type="text/javascript"></script>
	<style>
    @page {size: A4;margin: 0;}body {margin: 0;}.background {background-color: lightgrey;position: relative;width: 750px;height: 1065px;margin: auto;transform: scale(1.08);text-align: left;margin-top: 40px;}.crane {max-width: 100%;height: 100%;}.topTitle {position: absolute;left: 21%;top: 8%;width: auto;font-size: 42px;color: rgb(255, 182, 47);}
	
	#loadMe {visibility: hidden;}@media print {html,body {width: 210mm !important;height: 297mm !important;background-color: #fff !important;}.print {display: none !important;}}#print {background: #03a9f4;padding: 8px;width: 750px;height: 50px;border: 0px;font-size: 25px;font-weight: bold;cursor: pointer;box-shadow: 1px 4px 4px #878787;color: #fff;border-radius: 10px;margin: 80px 0;display: none;}
	</style>

<!-- <style>
    /* General Styling */
    @page {
        size: A4;
        margin: 0;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    /* Background and image styling */
    .background {
        background-color: lightgrey;
        position: relative;
        width: 100%;
        max-width: 750px;
        margin: auto;
        transform: scale(1.08);
        text-align: left;
        margin-top: 40px;
    }

    .crane {
        max-width: 100%;
        height: auto;
    }

    .topTitle {
        position: absolute;
        left: 30%;
        top: 8%;
        width: auto;
        font-size: 16px;
        color: rgb(255, 224, 0);
        text-align: center;
    }

    .print {
        position: absolute;
        top: 92%;
        left: 50%;
        transform: translateX(-50%);
    }

    /* Image and QR Code positioning */
    #photo, #qr {
        border-radius: 10px;
    }

    /* Hide the print button during print */
    @media print {
        #print {
            display: none !important;
        }

        /* Ensure print content is centered and fills the page */
        .background {
            transform: scale(1);
            width: 100%;
            height: 100%;
        }
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        /* Adjust font sizes and positions for smaller screens */
        .topTitle {
            font-size: 14px;
            left: 25%;
            top: 5%;
        }

        .crane {
            width: 100%;
            height: auto;
        }

        /* Adjust image size */
        #photo {
            width: 100px;
            height: 120px;
        }

        #qr {
            width: 100px;
            height: 100px;
        }

        /* Adjust text sizes */
        .background div {
            font-size: 12px;
        }

        .background {
            margin-top: 20px;
            width: 100%;
        }

        #print {
            font-size: 14px;
            width: 80%;
            padding: 10px;
            margin-top: 20px;
        }

        /* Adjust title positions */
        .topTitle, .topTitle + div {
            width: 100%;
            left: 0;
            text-align: center;
        }

        /* Fix positioning for text and adjust padding */
        .background div {
            left: 10%;
            top: auto;
            position: relative;
            width: 80%;
            font-size: 12px;
            text-align: left;
        }

        #nid_no, #nid_father, #nid_mother, #spouse, #voter_area, #name_bn, #name_en, #dob, #fathers_name, #mothers_name, #gender, #mobile_no, #blood_grp, #birth_place, #present_addr, #permanent_addr {
            left: 10%;
            font-size: 12px;
            width: 80%;
        }

        /* Adjusting images for mobile */
        #photo {
            width: 80px;
            height: 100px;
        }

        #qr {
            width: 80px;
            height: 80px;
        }

        /* Hide overflow and adjust margins for mobile */
        .background div {
            word-wrap: break-word;
            margin-top: 5px;
        }
    }

    /* Tablet Responsiveness */
    @media (min-width: 768px) and (max-width: 1024px) {
        .topTitle {
            font-size: 16px;
        }

        #photo {
            width: 120px;
            height: 140px;
        }

        #qr {
            width: 120px;
            height: 120px;
        }

        .background {
            width: 80%;
            margin-top: 30px;
        }

        /* Adjust the print button */
        #print {
            width: 70%;
            font-size: 18px;
        }
    }

</style> -->

</head>
<body style="text-align: center; font-weight: bold;">
		<div class="background">
			<img class="crane" src="https://i.postimg.cc/zff4mDrk/server.jpg" height="1000px" width="750px">
			<div style="position: absolute; left: 30%; top: 8%;width: auto;font-size: 16px; color: rgb(255 224 0);"><b>National Identity Registration Wing (NIDW)</b></div>
			<div style="position: absolute; left: 37%; top: 11%;width: auto;font-size: 14px; color: rgb(255, 47, 161);"><b>Select Your Search Category</b></div>
			<div style="position: absolute; left: 45%; top: 12.8%;width: auto;font-size: 12px; color: rgb(8, 121, 4);">Search By NID / Voter No.</div>
			<div style="position: absolute; left: 45%; top: 14.3%;width: auto;font-size: 12px; color: rgb(7, 119, 184);">Search By Form No.</div>
			<div style="position: absolute; left: 30%; top: 16.9%;width: auto;font-size: 12px; color: rgb(252, 0, 0);"><b>NID or Voter No*</b></div>
			<div style="position: absolute; left: 45%; top: 16.9%; width: auto; font-size: 12px; color: rgb(143, 143, 143);"><?php echo $order_details_data[0]['national_id']; ?></div>
			<div style="position: absolute;left: 62.9%;top: 17.1%;width: auto;font-size: 11px; color: rgb(255 255 255);">Submit</div>
			<a href="dashboard.php">
			<div style="position: absolute;left: 89%;top: 11.55%;width: auto;font-size: 11px;color: #fff;">
				Home
			</div>
		</a>
			<!-- <div style="position: absolute; left: 37%; top: 27%; width: auto; font-size: 16px; color: rgb(7, 7, 7);"><b>জাতীয় পরিচিতি তথ্য</b></div>
			<div style="position: absolute; left: 37%; top: 29.7%; width: auto; font-size: 13px; color: rgb(7, 7, 7);">জাতীয় পরিচয় পত্র নম্বর</div>
			<div id="nid_no"style="position: absolute; left: 55%; top: 29.7%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo $order_details_data[0]['national_id']; ?></div>
			<div style="position: absolute; left: 37%; top: 32.5%; width: auto; font-size: 13px; color: rgb(7, 7, 7);">পিন নাম্বার</div>
			<div id="nid_father" style="position: absolute; left: 55%; top: 32.5%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo  $order_details_data[0]['pin']; ?></div>
			<div style="position: absolute; left: 37%; top: 35.2%; width: auto; font-size: 14px; color: rgb(7, 7, 7);">জন্মস্থান</div>
			<div id="voter_area" style="position: absolute; left: 55%; top: 35.2%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo $order_details_data[0]['permanent_division']; ?></div> -->

            <div style="position: absolute; left: 37%; top: 27%; width: auto; font-size: 16px; color: rgb(7, 7, 7);"><b>জাতীয় পরিচিতি তথ্য</b></div>
			<div style="position: absolute; left: 37%; top: 29.7%; width: auto; font-size: 13px; color: rgb(7, 7, 7);">জাতীয় পরিচয় পত্র নম্বর</div>
			<div id="nid_no"style="position: absolute; left: 55%; top: 29.7%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo $order_details_data[0]['national_id']; ?></div>
			<div style="position: absolute; left: 37%; top: 32.5%; width: auto; font-size: 13px; color: rgb(7, 7, 7);">পিন নাম্বার</div>
			<div id="nid_father" style="position: absolute; left: 55%; top: 32.5%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo  $order_details_data[0]['pin']; ?></div>
			<div style="position: absolute; left: 37%; top: 35%; width: auto; font-size: 13px; color: rgb(7, 7, 7);">মাতার পরিচয় পত্র নম্বর</div>
			<div id="nid_mother" style="position: absolute; left: 55%; top: 35%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo $order_details_data[0]['mother_nid'] ?? '-'; ?></div>
			<div style="position: absolute; left: 37%; top: 37.5%; width: auto; font-size: 14px; color: rgb(7, 7, 7);">স্বামী/স্ত্রীর নাম</div>
			<div id="spouse" style="position: absolute; left: 55%; top: 37.5%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo $order_details_data[0]['spouse'] ?? '-'; ?></div>
			<div style="position: absolute; left: 37%; top: 40.2%; width: auto; font-size: 14px; color: rgb(7, 7, 7);">জন্মস্থান</div>
			<div id="voter_area" style="position: absolute; left: 55%; top: 40.2%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo $order_details_data[0]['permanent_division']; ?></div>


			<div style="position: absolute; left: 37%; top: 43%; width: auto; font-size: 16px; color: rgb(7, 7, 7);"><b>ব্যক্তিগত তথ্য</b></div>
			<div style="position: absolute; left: 37%; top: 45.6%; width: auto; font-size: 14px; color: rgb(7, 7, 7);">নাম (বাংলা)</div>
			<div id="name_bn"style="position: absolute; font-weight: bold; left: 55%; top: 45.6%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><b><?php echo $order_details_data[0]['name']; ?></b></div>
			<div style="position: absolute; left: 37%; top: 48.5%; width: auto; font-size: 14px; color: rgb(7, 7, 7);">নাম (ইংরেজি)</div>
			<div id="name_en"style="position: absolute; left: 55%; top:48.5%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo $order_details_data[0]['name_en']; ?></div>
			<div style="position: absolute; left: 37%; top: 51%; width: auto; font-size: 14px; color: rgb(7, 7, 7);">জন্ম তারিখ</div>
			<div id="dob"style="position: absolute; left: 55%; top: 51%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo $order_details_data[0]['date_of_birth'];; ?></div>
			<div style="position: absolute; left: 37%; top: 53.7%; width: auto; font-size: 14px; color: rgb(7, 7, 7);">পিতার নাম</div>
			<div id="fathers_name"style="position: absolute; left: 55%; top: 53.7%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo $order_details_data[0]['father']; ?></div>
			<div style="position: absolute; left: 37%; top: 56.2%; width: auto; font-size: 14px; color: rgb(7, 7, 7);">মাতার নাম</div>
			<div id="mothers_name"style="position: absolute; left: 55%; top: 56.2%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo $order_details_data[0]['mother']; ?></div>


			<div style="position: absolute; left: 37%; top: 59%; width: auto; font-size: 16px; color: rgb(7, 7, 7);"><b>অন্যান্য তথ্য</b></div>
			<div style="position: absolute; left: 37%; top: 62.2%; width: auto; font-size: 14px; color: rgb(7, 7, 7);">লিঙ্গ</div>
			<div id="gender"style="position: absolute; left: 55%; top: 62.2%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo $order_details_data[0]['gender_bn']; ?></div>
			<div style="position: absolute; left: 37%; top: 64.8%; width: auto; font-size: 14px; color: rgb(7, 7, 7);">মোবাইল নম্বর</div>
			<div id="mobile_no"style="position: absolute; left: 55%; top: 64.8%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo $order_details_data[0]['mobile'] ?? "N/A"; ?></div>
			<div style="position: absolute; left: 37%; top: 67.5%; width: auto; font-size: 14px; color: rgb(7, 7, 7);">রক্তের গ্রুপ</div>
			<div id="blood_grp"style="position: absolute; left: 55%; top: 67.5%; width: auto; font-size: 14px; color: rgb(255, 0, 0);"><?php echo $order_details_data[0]['bloodGroup'] ?? "N/A"; ?></div>
			<div style="position: absolute; left: 37%; top: 70%; width: auto; font-size: 14px; color: rgb(7, 7, 7);">ধর্ম</div>
			<div id="birth_place"style="position: absolute; left: 55%; top: 70%; width: auto; font-size: 14px; color: rgb(7, 7, 7);"><?php echo $order_details_data[0]['religion']; ?></div>


			<div style="position: absolute; left: 37%; top: 72.8%; width: auto; font-size: 16px; color: rgb(7, 7, 7);"><b>বর্তমান ঠিকানা</b></div>
			<div id="present_addr"style="position: absolute; left: 37%; top: 75.2%; width: 48%; font-size: 12px; color: rgb(7, 7, 7);"> <?php echo $order_details_data[0]['present_full_address']; ?>,</div>
			<div style="position: absolute; left: 37%; top: 81.5%; width: auto; font-size: 16px; color: rgb(7, 7, 7);"><b>স্থায়ী ঠিকানা</b></div>
			<div id="permanent_addr"style="position: absolute; left: 37%; top: 84%; width: 48%; font-size: 12px; color: rgb(7, 7, 7);"> <?php echo $order_details_data[0]['permanent_full_address']; ?>,
			</div>
            
			<div style="position: absolute;top: 92%;width: 100%;font-size: 12px;text-align: center;color: rgb(255, 0, 0);">উপরে প্রদর্শিত তথ্যসমূহ জাতীয় পরিচয়পত্র সংশ্লিষ্ট, ভোটার তালিকার সাথে সরাসরি সম্পর্কযুক্ত নয়।</div>
			<div style="position: absolute;top: 93.5%;width: 100%;text-align: center;font-size: 12px;color: rgb(3, 3, 3);">This is Software Generated Report From Bangladesh Election Commission, Signature &amp; Seal Arent Required </div>
			
			<!-- Print Button Section (Fixed at the bottom) -->
			<<button class="print btn btn-primary" 
                style="position: absolute; left: 50%; bottom: -111px; transform: translateX(-50%); 
                    background: #03a9f4; padding: 8px; width: 220px; height: 50px; border: 0; 
                    font-size: 25px; font-weight: bold; cursor: pointer; box-shadow: 1px 4px 4px #878787; 
                    color: #fff; border-radius: 10px; margin: 80px 0; display: block;" 
                onclick="window.print()">SAVE</button>


			
			<div style="position: absolute;left: 7%;top: 96.5%;width: auto;font-size: 12px;color: rgb(3, 3, 3);height: 9.5px;overflow: hidden;"></div>
			
			<div style="position: absolute;  left: 16%; top: 25.7%; width: auto; font-size: 12px; color: rgb(3, 3, 3);">
			<img 
				id="photo" 
				src="img/images/nid_copy/<?php echo htmlspecialchars($order_details_data[0]['photo']); ?>" 
				height="140px" 
				width="121px" 
				style="border-radius: 10px"
			/>
			</div>
			<div style="position: absolute;  left: 16.25%; top: 43%; width: auto; font-size: 12px; color: rgb(3, 3, 3);">
				<img id="qr" src="<?php echo $qr_file; ?>" height="120px" width="120px" /></div>
				<div id="name_en2" style="position: absolute;display: flex;font-weight: bold;left: 15.5%;top: 39.6%;height: 32px;width: 130px;font-size: 15px;color: rgb(7, 7, 7);margin: auto;align-items: center;" align="center"><div style="flex: 1;"><?php echo $order_details_data[0]['name_en']; ?></div></div>
			</div>
			
</div>

		
		<button class="print btn btn-sm" id="print" onclick="window.print()">SAVE</button>
		<script>function showprint(){$("#print").show(500);}</script>

<?php } ?>


</html>


