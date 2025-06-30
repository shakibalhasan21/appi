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
        @font-face {
            font-family: 'bangla';
            src: url('Bangla.ttf') format('truetype');
        }
        
        body {
            font-family: 'bangla', Arial, sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
        }
        
        .container {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            position: relative;
            box-sizing: border-box;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #ccc;
            /* padding-bottom: 10px; */
        }
        
        .logo {
            width: 70px;
            height: 70px;
            /* margin-bottom: 5px; */
        }
        
        .profile-photo {
            text-align: center;
            margin: 5px 0 15px;
        }
        
        .photo {
            width: 100px;
            height: 120px;
            border: 2px solid #fbf8f8;
            object-fit: cover;
            background: white;
            border-radius: 16px;
        }
        
        .section {
            margin-bottom: 15px;
            background-color: #e0f2f7;
            border-radius: 5px;
        }
        
        .section-title {
            background-color: #a8d8e8;
            padding: 3px 10px;
            margin: 0;
            font-weight: bold;
            font-size: 20px;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        td {
            padding: 6px 10px;
            border: 1px solid #ccc;
            background-color: white;
            font-size: 16px;
        }
        
        td:first-child {
            width: 40%;
        }
        
        .footer {
            text-align: center;
            font-size: 14px;
            margin-top: 20px;
            /* border-top: 1px solid #ccc; */
            padding-top: 10px;
        }
        
        .footer-note {
            color: #444;
            /* font-style: italic; */
        }
        
        .print-btn {
            background: #03a9f4;
            padding: 8px;
            width: 210mm;
            height: 40px;
            border: none;
            font-size: 25px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 1px 4px 4px #878787;
            color: #fff;
            border-radius: 10px;
            margin: 15px auto;
            display: block;
            text-align: center;
        }
        
        @page {
            size: A4;
            margin: 0;
        }
        
        @media print {
            @page {
                size: 210mm 297mm;
                margin: 0;
            }
            
            .container {
                overflow: hidden;
                page-break-after: avoid;
                margin: 0;
                border: none;
            }
            
            .print-btn {
                display: none;
            }
            
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img class="logo" src="images/nc.png" alt="Bangladesh Election Commission Logo">
            <p>বাংলাদেশ নির্বাচন কমিশন<br>নির্বাচন কমিশন সচিবালয়<br>জাতীয় পরিচয় নিবন্ধন অনুবিভাগ</p>
        </div>

        <div class="profile-photo">
            <img class="photo" src="<?php echo htmlspecialchars($order_details_data[0]['photo']); ?>" alt="Profile Photo">
        </div>

        <div class="section">
            <div class="section-title">জাতীয় পরিচিতির তথ্য</div>
            <table>
                <tr>
                    <td>জাতীয় পরিচয় পত্র নম্বর</td>
                    <td><?php echo $order_details_data[0]['national_id']; ?></td>
                </tr>
                <tr>
                    <td>পিন নম্বর</td>
                    <td><?php echo  $order_details_data[0]['pin'] ?? '-'; ?></td>
                </tr>
                <tr>
                    <td>ভোটার এলাকা</td>
                    <td ><?php echo $order_details_data[0]['voterArea'] ?? '-'; ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">ব্যক্তিগত তথ্য</div>
            <table>
                <tr>
                    <td>নাম (বাংলা)</td>
                    <td><?php echo $order_details_data[0]['name']; ?></td>
                </tr>
                <tr>
                    <td>নাম (ইংরেজি)</td>
                    <td><?php echo $order_details_data[0]['name_en']; ?></td>
                </tr>
                <tr>
                    <td>জন্ম তারিখ</td>
                    <td>
                      <?php
                      $date = new DateTime($order_details_data[0]['date_of_birth']);
                      $formatted_date = $date->format('d M Y'); // Format to '11 Nov 1997'
                      echo isset($order_details_data[0]['date_of_birth']) ? htmlspecialchars($formatted_date) : '';
                      ?>
                    </td>
                </tr>
                <tr>
                    <td>পিতার নাম</td>
                    <td><?php echo $order_details_data[0]['father']; ?></td>
                </tr>
                <tr>
                    <td>মাতার নাম</td>
                    <td><?php echo $order_details_data[0]['mother']; ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">অন্যান্য তথ্য</div>
            <table>
                <tr>
                    <td>লিঙ্গ</td>
                    <td><?php echo $order_details_data[0]['gender_bn'] ?? "N/A"; ?></td>
                </tr>
                <tr>
                    <td>জন্মস্থান</td>
                    <td><?php echo $order_details_data[0]['birthPlace']?? 'N/A'; ?></td>
                </tr>
                <tr>
                    <td>ধর্ম</td>
                    <td><?php echo $order_details_data[0]['religion']?? "N/A"; ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">বর্তমান ঠিকানা</div>
            <table>
                <tr>
                    <td colspan="2"><?php echo $order_details_data[0]['present_full_address']; ?></td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">স্থায়ী ঠিকানা</div>
            <table>
                <tr>
                    <td colspan="2"> <?php echo $order_details_data[0]['permanent_full_address']; ?></td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p style="color: rgb(255, 0, 0);  font-weight: bold;">উপরে প্রদর্শিত তথ্যসমূহ জাতীয় পরিচয়পত্র সংশ্লিষ্ট, ভোটার তালিকার সাথে
        সরাসরি সম্পর্কযুক্ত নয়।</p>
            <p class="footer-note" style=" font-weight: bold;"> This is Software Generated Report From Bangladesh Election Commission,
        Signature &amp; Seal Aren't Required.</p>
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">Download PDF</button>
</body>






<?php } ?>


</html>


