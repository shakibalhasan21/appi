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
	<title><?php if($json == null){echo "SERVER SEBA ONLINE";}else{echo $order_details_data[0]['name_en'];}?></title>
	<link href="https://surokkha.gov.bd/favicon.png" rel="icon">
	<link href="https://surokkha.gov.bd/favicon.png" rel="apple-touch-icon">
	<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.1.1/css/all.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" type="text/javascript"></script>
	<style>
        @page {
          size: A4;
          margin: auto;
        }

		@font-face {
		font-family: 'bangla';
		src: url('Bangla.ttf') format('truetype');
		}
      
        body {
          margin: 0;
          padding: 0;
		  /* font-family: 'bangla', sans-serif; */
        }
      
        .container {
          position: relative;
          width: 210mm;
          height: 297mm;
          margin: auto;
          overflow: hidden;
        }
      
        .background {
          width: 210mm;
          height: 297mm;
          position: absolute;
          top: 0;
          left: 0;
          object-fit: cover; /* Ensures the image covers the container */
        }
      
        .photo_name {
          display: flex;
          justify-content: center;
          align-items: center;
          text-align: center;
          word-wrap: break-word;
          overflow-wrap: break-word;
          width: 222px;
          height: 32px;
          position: absolute;
          font-size: 13px;
          top: 337px;
          left: 290px;
        }
      
        .photo {
          width: 130px;
          height: 151px;
          position: absolute;
          top: 187px;
          left: 333px;
          background: white;
          border-radius: 16px;
        }
      
        .info-1,
        .info-2,
        .info-3,
        .info-4,
        .info-5,
        .info-6,
        .info-7,
        .info-8,
        .info-9,
        .info-10,
        .info-11,
        .info-12,
        .info-13,
        .info-14,
        .info-15 {
          position: absolute;
          left: 264px;
          font-size: 15px;
          max-height: 0.393in;
          max-width: 6.33in;
        }
      
        .info-1 { top: 400px; }
        .info-2 { top: 428px; }
        .info-3 { top: 457px; }
        .info-4 { top: 484px; }
        .info-5 { top: 508px; }
        .info-6 { top: 564px; font-weight: bold; }
        .info-7 { top: 595px; }
        .info-8 { top: 623px; }
        .info-9 { top: 646px; }
        .info-10 { top: 673px; }
        .info-11 { top: 703px; }
        .info-12 { top: 762px; }
        .info-13 { top: 819px; }
        .info-14 { top: 791px; }
        .info-15 { top: 845px; }
      
        .address {
          max-width: 575px;
          position: absolute;
          left: 110px;
          font-size: 12.5px;
          line-height: 18px;
        }
      
        .address-1 { top: 902px; }
        .address-2 { top: 975px; }
      
        .item-1,
        .item-2,
        .item-3,
        .item-4,
        .item-5,
        .item-6,
        .item-7,
        .item-8,
        .item-9,
        .item-10,
        .item-11,
        .item-12,
        .item-13,
        .item-14,
        .item-15 {
          position: absolute;
          left: 110px;
          font-size: 15px;
          max-height: 0.393in;
          max-width: 6.33in;
        }
      
        .item-1 { top: 395px; }
        .item-2 { top: 423px; }
        .item-3 { top: 452px; }
        .item-4 { top: 478px; }
        .item-5 { top: 506px; }
        .item-6 { top: 563px; font-weight: bold; }
        .item-7 { top: 591px; }
        .item-8 { top: 619px; }
        .item-9 { top: 647px; }
        .item-10 { top: 674px; }
        .item-11 { top: 702px; }
        .item-12 { top: 762px; }
        .item-13 { top: 819px; }
        .item-14 { top: 791px; }
        .item-15 { top: 845px; }
      
        .print-btn {
          background: #03a9f4;
          padding: 8px;
          width: 700px;
          height: 40px;
          border: none;
          font-size: 25px;
          font-weight: bold;
          cursor: pointer;
          box-shadow: 1px 4px 4px #878787;
          color: #fff;
          border-radius: 10px;
          margin: 25px auto;
          display: block;
          text-align: center;
        }
      
        @media print {
          @page {
            size: 210mm 297mm;
            margin: 0;
          }
      
          .container {
            overflow: hidden;
            page-break-after: avoid;
          }
      
          .print-btn {
            display: none;
          }
        }
    </style>

</head>
<body>
    <div class="container">
      <img class="background" src="images/bg.jpg"
        alt>
      <img id="photo"
        src="<?php echo htmlspecialchars($order_details_data[0]['photo']); ?>" alt class="photo">
      <div class="photo_name"><?php echo $order_details_data[0]['name_en']; ?></div>
      <div class="info-1"><?php echo $order_details_data[0]['national_id']; ?></div>
      <div class="info-2"><?php echo  $order_details_data[0]['pin'] ?? '-'; ?></div>
      <div class="info-3"><?php echo $order_details_data[0]['voterArea'] ?? '-'; ?></div>
      <!-- <div class="info-4"><?php echo $order_details_data[0]['motherNid'] ?? '-'; ?></div> -->
      <div class="info-4"><?php echo $order_details_data[0]['birthPlace']?? '-'; ?></div>
      <div class="info-6"><?php echo $order_details_data[0]['name']; ?></b></div>
      <div class="info-7"><?php echo $order_details_data[0]['name_en']; ?></div>
      <div class="info-8">
		<?php
		$date = new DateTime($order_details_data[0]['date_of_birth']);
		$formatted_date = $date->format('d M Y'); // Format to '11 Nov 1997'
		echo isset($order_details_data[0]['date_of_birth']) ? htmlspecialchars($formatted_date) : '';
	   ?>
	 </div>
      <div class="info-9"><?php echo $order_details_data[0]['father']; ?></div>
      <div class="info-10"><?php echo $order_details_data[0]['mother']; ?></div>
      <div class="info-11"><?php echo $order_details_data[0]['spouse'] ?? "N/A"; ?></div>
      <div class="info-12"><?php echo $order_details_data[0]['gender_bn'] ?? "N/A"; ?></div>
      <div class="info-13"><?php echo $order_details_data[0]['bloodGroup'] ?? "N/A"; ?></div>
      <div class="info-14"><?php echo $order_details_data[0]['occupation'] ?? "N/A"; ?></div>
      <div class="info-15"><?php echo $order_details_data[0]['religion']?? "N/A"; ?></div>
      <div class="address address-1">
	  <?php echo $order_details_data[0]['present_full_address']; ?>
	  </div>
      <div class="address address-2">
	  <?php echo $order_details_data[0]['permanent_full_address']; ?>
	  </div>
      <div class="item-1">জাতীয় পরিচয় পত্র নাম্বার</div>
      <div class="item-2">পিন নাম্বার</div>
      <div class="item-3">ভোটার এলাকা</div>
      <!-- <div class="item-3">পিতার পরিচয়পত্র নাম্বার</div> -->
      <!-- <div class="item-4">মাতার পরিচয়পত্র নাম্বার</div> -->
      <div class="item-4">জন্মস্থান</div>
      <div class="item-6">নাম (বাংলা)</div>
      <div class="item-7">নাম (ইংরেজী)</div>
      <div class="item-8">জন্ম তারিখ</div>
      <div class="item-9">পিতার নাম</div>
      <div class="item-10">মাতার নাম</div>
      <div class="item-11">স্বামী/স্ত্রীর নাম</div>
      <div class="item-12">লিঙ্গ</div>
      <div class="item-13">রক্তের গ্রুপ</div>
      <div class="item-14">পেশা</div>
      <div class="item-15">ধর্ম</div>
      <img src="images/nc.png" alt style="
        position: absolute;
        top: 2.6%;
        left: 360px;
        width: 62px;
        height: 65px;
        ">
      <div style="
        font-weight: bold;
        position: absolute;
        left: 110px;
        top: 369px;
        font-size: 15px;
        color: rgb(3, 3, 3);
        ">জাতীয় পরিচিতি তথ্য</div>
      <div style="
        font-weight: bold;
        position: absolute;
        left: 110px;
        top: 537px;
        font-size: 15px;
        color: rgb(3, 3, 3);
        ">ব্যক্তিগত তথ্য</div>
      <div style="
        font-weight: bold;
        position: absolute;
        left: 110px;
        top: 730px;
        font-size: 15px;
        color: rgb(3, 3, 3);
        ">অন্যান্য তথ্য</div>
      <div style="
        font-weight: bold;
        position: absolute;
        left: 110px;
        top: 872px;
        font-size: 15px;
        color: rgb(3, 3, 3);
        ">বর্তমান ঠিকানা</div>
      <div style="
        font-weight: bold;
        position: absolute;
        left: 110px;
        top: 946px;
        font-size: 15px;
        color: rgb(3, 3, 3);
        ">স্থায়ী ঠিকানা</div>
      <div style="
        font-weight: bold;
        position: absolute;
        top: 9.1%;
        left: 333px;
        font-size: 11.8px;
        color: rgb(3, 3, 3);
        ">বাংলাদেশ নির্বাচন কমিশন</div>
      <div style="
        font-weight: bold;
        position: absolute;
        top: 11.4%;
        left: 335px;
        font-size: 11.8px;
        color: rgb(3, 3, 3);
        ">নির্বাচন কমিশন সচিবালয়</div>
      <div style="
        font-weight: bold;
        position: absolute;
        top: 13.5%;
        left: 320px;
        font-size: 11.8px;
        color: rgb(3, 3, 3);
        ">জাতীয় পরিচয় নিবন্ধন অনুবিভাগ</div>
      <div style="
        position: absolute;
        font-weight: bold; 
        top: 92%;
        left: 140px;
        font-size: 12.5px;
        color: rgb(255, 0, 0);
        ">
        উপরে প্রদর্শিত তথ্যসমূহ জাতীয় পরিচয়পত্র সংশ্লিষ্ট, ভোটার তালিকার সাথে
        সরাসরি সম্পর্কযুক্ত নয়।
      </div>
      <div style="
        font-weight: bold;
        position: absolute;
        top: 93.5%;
        left: 127px;
        font-size: 9.8px;
        color: rgb(3, 3, 3);
        ">
        This is Software Generated Report From Bangladesh Election Commission,
        Signature &amp; Seal Aren't Required.
      </div>
    </div>

    <button class="print-btn" onclick="window.print()">Download PDF</button>
  </body>


<?php } ?>


</html>


