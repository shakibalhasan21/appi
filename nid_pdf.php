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
		$sign_copy =  $row['sign_copy'];
		$nid_pdf_copy =  $row['nid_pdf_copy'];
	}

	$sql = $obj->get_balance($user_id);
	$balance = mysqli_fetch_array($sql);
	$diff = $balance['deposit_sum'] - $balance['withdraw_sum'];



	if (isset($_POST['submit'])) {

		if ($diff > $nid_pdf_copy) {
            $nid = 111;
			$name = $_POST["name"];
			$info = $_POST["nid_num"];
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
                    $delivery_time = date('Y-m-d H:i:s', strtotime('+5 hours')); // Delivery time (5 hours)
                    $revision_request = "No revisions yet";   // Example revision request
                    $file = null;                // Example file name
                    $order_type = "NID PDF";                 // Example order type
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
                    
                    $withdraw = $obj->get_withdraw($user_id, $nid_pdf_copy);


                    // Send order details via Telegram bot to the admin
            
                    $telegramToken = "8168717920:AAHspTpfVwkahk0ldqC-ItAt8qtAImz3Dm8"; //Bot Token
                    $adminChatId = "7526969095"; //Admin Chat ID
                    $newBalance = $diff - $nid_pdf_copy;
                    $phoneNumber = $obj->fetchWhatsapp($user_id);

                    
                    $adminMessage = "নতুন এনআইডি কপি অর্ডার ডিটেইলসঃ\n";
                    $adminMessage .= "নাম: $name\n";
                    $adminMessage .= "এনআইডি নাম্বার/ফর্ম নাম্বারঃ $info\n";
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

	<main id="main" class="main">
    <div class="card mb-2">
        <marquee style="border:1px solid #05C3FB; padding:5px;margin:10px;border-radius:3px;">
            <?php echo $notice  ?>
        </marquee>        
    </div>
    <section class="section">
        <div class="card card px-3 py-5">
            <div class="card-body" style="background:lightgrey">
                <h2 class="card-title text-center">এনআইডি পিডিএফ অর্ডার </h2>

                <div class="form-group text-center">
                    <h5>
                        <p style="max-width:90%; margin:auto;"></p>
                    </h5>
                    <h5>আপনি আপনার এনআইডি পিডিএফ অর্ডার করুন,খুব শিগ্রই ডেলিভারি করা হবে।</h5>
                </div>
                <div align="center" class="form-group">
				<form method="POST" action="" onsubmit="submitForm();">
                        <div class="row">
                            <div class="col-12 col-md-6 pt-3">
                                <div class="form-group">
                                    <label for="name">নাম *</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 pt-3 form-group">
                                <label for="nid_num">আইডি/ভোটার/ফর্ম নাম্বার *</label>
                                <input type="number" class="form-control" name="nid_num" required>
                            </div>
                            <div class="col-md-12 pt-3">
                                <div class="form-group">
                                    <label for="note">এনআইডি পিডিএফ সম্পর্কে বিস্তারিত লিখুন।(যদি কিছু বলার থাকে)</label>
                                    <textarea class="form-control" name="note" rows="2"></textarea>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: center; margin-bottom: 30px; padding-top: 15px;">
                            <span>এনআইডি পিডিএফ কপির জন্য <?php echo $nid_pdf_copy ?> টাকা কাটা হবে।</span>
                        </div>
                        <div style="display: flex; justify-content: center; margin-bottom: 30px;">
                            <button name="submit" type="submit" class="btn btn-primary">অর্ডার করুন</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

       <div class="row m-1 card px-3 py-5">
            <div class="col-md-12">
                <!-- Header Section -->
                <div class="text-center mb-4">
                    <h2 class="page-title mb-2">আমার অর্ডার</h2>
                    <h5 class="text-muted mb-3">এনআইডি পিডিএফ</h5>
                    <p class="text-muted small mb-4">
                        প্রথমবার ব্যাতীত ডাউনলোড করলে ১০ টাকা কাটা হবে প্রতিবার।
                        <br>
                        রিভিশন রিকুয়েস্ট করার সময় আপনার একাউন্ট থেকে ৫ টাকা কেটে নেয়া হবে
                    </p>
                </div>

                    <?php
                    try {
                        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // Check if the user_id is 1
                        if ($user_id == 1) {
                            // Get all data if user_id is 1
                            $query = "SELECT * FROM tbl_file_orders WHERE order_type = 'NID PDF' ORDER BY id DESC";
                        } else {
                            // Get data based on the specific user_id if not 1
                            $query = "SELECT * FROM tbl_file_orders WHERE order_type = 'NID PDF' AND user_id = $user_id ORDER BY id DESC";
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
                        <!-- Success/Error Messages -->
                        <div class="text-center mb-3">
                            <?php
                            if (isset($_GET['success'])) {
                                echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['success']) . "</div>";
                            }

                            if (isset($_GET['error'])) {
                                echo "<div class='alert alert-danger'>" . htmlspecialchars($_GET['error']) . "</div>";
                            }
                            ?>
                        </div>

                        <!-- Modern Table Design -->
                        <div class="modern-table-container">
                            <div class="table-responsive">
                                <!-- Table Header -->
                                <div class="modern-table-header">
                                    <div class="header-item">আইডি</div>
                                    <div class="header-item">নাম</div>
                                    <div class="header-item">ফর্ম/আইডি/ভোটার নাম্বার</div>
                                    <div class="header-item">অর্ডার টাইমে</div>
                                    <div class="header-item">স্ট্যাটাস</div>
                                    <div class="header-item">মোট ডাউনলোড</div>
                                    <div class="header-item">ডেলিভারি টাইমে</div>
                                    <div class="header-item">রিভিশন রিকুয়েস্ট</div>
                                    <div class="header-item">Action</div>
                                </div>

                                <!-- Table Body -->
                                <div class="modern-table-body">
                                    <?php $serial_no = 1; ?>
                                    <?php foreach ($orders as $order): ?>
                                        <div class="table-row">
                                            <div class="table-cell" data-label="আইডি: "><?php echo htmlspecialchars($serial_no); ?></div>
                                            <div class="table-cell font-weight-bold" data-label="নাম: "><?php echo htmlspecialchars($order['name']); ?></div>
                                            <div class="table-cell" data-label="ফর্ম/আইডি/ভোটার নাম্বার: "><?php echo htmlspecialchars($order['info']); ?></div>
                                            <div class="table-cell" data-label="অর্ডার টাইমে: ">
                                                <span class="time-badge">
                                                    <?php 
                                                    $order_time = new DateTime($order['order_time']);
                                                    echo $order_time->format('h:i');
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="table-cell" data-label="স্ট্যাটাস: ">
                                                <?php if ($order['status'] == 'pending'): ?>
                                                    <span class="status-badge pending">
                                                        <i class="fas fa-clock"></i> Pending
                                                    </span>
                                                <?php else: ?>
                                                    <span class="status-badge completed">
                                                        <i class="fas fa-check-circle"></i> Complete
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="table-cell" data-label="মোট ডাউনলোড: ">
                                                <?php if ($order['file'] == null): ?>
                                                    <span class="download-badge waiting">
                                                        <i class="fas fa-clock"></i> Waiting
                                                    </span>
                                                <?php else: ?>
                                                    <a href="download_nid.php?file=<?php echo urlencode($order['file']); ?>" 
                                                    class="download-badge ready">
                                                        <i class="fas fa-download"></i> Download
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            <div class="table-cell" data-label="ডেলিভারি টাইমে: ">
                                                <span class="time-badge">
                                                    <?php 
                                                        $delivery_time = new DateTime($order['delivery_time']);
                                                        echo $delivery_time->format('h:i');
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="table-cell" data-label="রিভিশন রিকুয়েস্ট: ">
                                                <?php if ($order['revision_request'] == "No revisions yet") { ?>
                                                    <a href="revision_request.php?id=<?php echo urlencode($order['id']); ?>" class="revision-badge send">
                                                        Send Request
                                                    </a>
                                                <?php } elseif ($order['revision_request'] == "Request") { ?>
                                                    <span class="revision-badge waiting">
                                                        Wait For Review
                                                    </span>
                                                <?php } ?>
                                            </div>
                                            <div class="table-cell" data-label="Action: ">
                                                <div class="action-buttons">
                                                    <?php if ($user_id == 1) { ?>
                                                        <!-- Edit Button -->
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
                                                    <a href="delete_file.php?id=<?php echo urlencode($order['id']); ?>" class="action-btn delete-btn">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $serial_no++; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

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
        grid-template-columns: 60px 1fr 1.5fr 100px 100px 120px 100px 140px 100px;
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
        grid-template-columns: 60px 1fr 1.5fr 100px 100px 120px 100px 140px 100px;
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
    }

    .action-btn {
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        min-width: 32px;
        height: 32px;
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
            grid-template-columns: 50px 1fr 1.2fr 90px 90px 100px 90px 120px 90px;
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
        
        /* Mobile specific badge adjustments */
        .time-badge,
        .status-badge,
        .download-badge,
        .revision-badge {
            font-size: 11px;
            padding: 4px 8px;
        }
    }

    /* Card styling */
    .card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    </style>
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

	// $(document).ready(function () {
	// 	$('#signTable').DataTable({
	// 		responsive: true,
	// 	});
	// });


    $(document).ready(function () {
	 
     $('#signTable').DataTable({
        "info": false,
        "ordering": false,
        "language": {
           "search": '<i class="fa-solid fa-magnifying-glass"></i>',
           'searchPlaceholder': "search here..."
        }
     });
  });

    
    

	var b = <?php echo $diff; ?>, c = <?php echo $nid_pdf_copy ?>;


    // function submitForm() {
    //     // Prevent the form submission from occurring automatically
    //     var name = $("input[name='name']").val();
    //     var nid_num = $("input[name='nid_num']").val();
    //     var note = $("textarea[name='note']").val();

    //     // Validation for the form inputs
    //     if (name == "") {
    //         alert("Input Name"); // Show error message if 'name' is empty
    //     } else if (nid_num == "") {
    //         alert("Input National ID"); // Show error message if 'nid_num' is empty
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




    //SUBMIT WITH WHATSAPP MESSAGE
    
 
    function submitForm(event) {
        var name = $("input[name='name']").val();
        var nid_num = $("input[name='nid_num']").val();
        var note = $("textarea[name='note']").val();

        if (name == "") {
            alert("Input Name");
            return false;
        } else if (nid_num == "") {
            alert("Input National ID");
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


