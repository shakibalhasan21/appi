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
$notice = $row['notice'];
$notice_two = $row['notice_two'];
$approval = $row['approval'];
$login = $row['login'];
$register = $row['register'];
$sv = $row['charge'];
$sign_copy = $row['server_copy'];
$pdf_copy = $row['pdf_copy'];
$sign_copy = $row['sign_copy'];
$nidmake = $row['log_channel'];
$bio = $row['bot_token'];

}




$pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get Server 1 
if ($user_id == 1) {
  $s1_query = "SELECT COUNT(*) as total_count 
            FROM tbl_orders 
            WHERE order_type = 'Server Old'";
} else {
  // Count data based on the specific user_id if not 1
  $s1_query = "SELECT COUNT(*) as total_count 
            FROM tbl_orders 
            WHERE order_type = 'Server Old' AND user_id = :user_id";
}

$s1_stmt = $pdo->prepare($s1_query);

if ($user_id != 1) {
  $s1_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
}

$s1_stmt->execute();

$total_s1 = $s1_stmt->fetchColumn();




// Get Server 2 
if ($user_id == 1) {
  $s2_query = "SELECT COUNT(*) as total_count 
            FROM tbl_orders 
            WHERE order_type = 'Server copy'";
} else {
  // Count data based on the specific user_id if not 1
  $s2_query = "SELECT COUNT(*) as total_count 
            FROM tbl_orders 
            WHERE order_type = 'Server copy' AND user_id = :user_id";
}

$s2_stmt = $pdo->prepare($s2_query);

if ($user_id != 1) {
  $s2_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
}

$s2_stmt->execute();

$total_s2 = $s2_stmt->fetchColumn();





// Get Total Pin
if ($user_id == 1) {
    $pin_query = "SELECT COUNT(*) as total_count 
              FROM tbl_orders 
              WHERE order_type = 'Server Pin'";
} else {
    // Count data based on the specific user_id if not 1
    $pin_query = "SELECT COUNT(*) as total_count 
              FROM tbl_orders 
              WHERE order_type = 'Server Pin' AND user_id = :user_id";
}

$pin_stmt = $pdo->prepare($pin_query);

if ($user_id != 1) {
    $pin_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
}

$pin_stmt->execute();

$total_pin = $pin_stmt->fetchColumn();



// Get Total Pin
if ($user_id == 1) {
  $nid_auto_query = "SELECT COUNT(*) as total_count 
            FROM tbl_orders 
            WHERE order_type = 'Make NID'";
} else {
  // Count data based on the specific user_id if not 1
  $nid_auto_query = "SELECT COUNT(*) as total_count 
            FROM tbl_orders 
            WHERE order_type = 'Make NID' AND user_id = :user_id";
}

$nid_auto_stmt = $pdo->prepare($nid_auto_query);

if ($user_id != 1) {
  $nid_auto_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
}

$nid_auto_stmt->execute();

$total_nid_auto = $nid_auto_stmt->fetchColumn();







// Get Nid Pdf 
if ($user_id == 1) {
  $nid_pdf_query = "SELECT COUNT(*) as total_count 
            FROM tbl_file_orders 
            WHERE order_type = 'NID PDF'";
} else {
  // Count data based on the specific user_id if not 1
  $nid_pdf_query = "SELECT COUNT(*) as total_count 
            FROM tbl_file_orders 
            WHERE order_type = 'NID PDF' AND user_id = :user_id";
}

$nid_pdf_stmt = $pdo->prepare($nid_pdf_query);

if ($user_id != 1) {
  $nid_pdf_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
}

$nid_pdf_stmt->execute();

$total_nid_pdf = $nid_pdf_stmt->fetchColumn();





// Get Sign Copy 
if ($user_id == 1) {
  $sign_copy_query = "SELECT COUNT(*) as total_count 
            FROM tbl_file_orders 
            WHERE order_type = 'Sign Copy'";
} else {
  // Count data based on the specific user_id if not 1
  $sign_copy_query = "SELECT COUNT(*) as total_count 
            FROM tbl_file_orders 
            WHERE order_type = 'Sign Copy' AND user_id = :user_id";
}

$sign_copy_stmt = $pdo->prepare($sign_copy_query);

if ($user_id != 1) {
  $sign_copy_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
}

$sign_copy_stmt->execute();

$total_sign_copy = $sign_copy_stmt->fetchColumn();



?>
<?php if($showFrom){ ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content name="description"><meta content name="keywords">
    <title><?php if($json == null){echo "Dashboard";}else{echo
      $json->nameEn;}?></title>
    <link href="https://surokkha.gov.bd/favicon.png" rel="icon">
    <link href="https://surokkha.gov.bd/favicon.png" rel="apple-touch-icon">
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link rel="stylesheet"
      href="https://site-assets.fontawesome.com/releases/v6.1.1/css/all.css">
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
      type="text/javascript"></script>
    <link
      href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
      rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css"
      rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="asset/css/demo.css" rel="stylesheet">
    <!-- <link href="asset/css/bootstrap.min.css" rel="stylesheet"> -->
  </head>
  <body>
    <!-- <header id="header" class="header fixed-top d-flex align-items-center">
		<div class="d-flex align-items-center justify-content-between">
			<a href="index.php" class="logo d-flex align-items-center">
      <img src="assets/Images/bangladeshicon.png" alt="" style="width: 38px; margin: 13px;">
      </a>
			<i class="bi bi-list toggle-sidebar-btn"></i>
		</div>
		<nav class="header-nav ms-auto">
			<ul class="d-flex align-items-center">
				<li class="nav-item dropdown"><?php $sql = $fetchdata->get_balance($user_id); $balance = mysqli_fetch_array($sql); ?>
					<button type="button" class="btn btn-danger mb-2"> <i class="bi bi-currency-dolla me-1"></i> Balance: <span class="badge bg-white text-primary"><?php echo ($balance['deposit_sum'] - $balance['withdraw_sum']); ?></span></button>
				</li>
				<li class="nav-item dropdown pe-3">
					<a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
						<span class="d-none d-md-block dropdown-toggle ps-2"><?php echo  $_SESSION['username']; ?></span>
					</a>
					<ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
						<li class="dropdown-header">
							<h6><?php echo  $_SESSION['fname']; ?></h6>
							<span><?php echo  $_SESSION['username']; ?></span>
						</li>
						<li>
							<hr class="dropdown-divider">
						</li>
						<li>
							<hr class="dropdown-divider">
						</li>
						<li>
							<a class="dropdown-item d-flex align-items-center" href="logout.php">
								<i class="bi bi-box-arrow-right"></i>
								<span>Sign Out</span>
							</a>
						</li>
					</ul>
				</li>
			</ul>
		</nav>
	</header> -->

    <?php include("includes/head.php");?>
    <?php include('includes/sidebar.php'); ?>
    <main id="main" class="main">
      <section class="section profile">
        <div id="inp" class="container mt-6 col-md-12 mb-5">
        <style>
          @keyframes rgbBorder {
            0% { border-color: rgb(255, 0, 0); }    /* Red */
            33% { border-color: rgb(0, 255, 0); }   /* Green */
            66% { border-color: rgb(0, 0, 255); }   /* Blue */
            100% { border-color: rgb(255, 0, 0); }  /* Back to Red */
          }

          .rgb-marquee {
            padding: 10px;
            background: white;
            border-radius: 5px;
            border: 3px solid; /* Set border size */
            animation: rgbBorder 3s infinite; /* Animation name and duration */
            color: #0d6efd;
            font-weight: bold;
            text-align: center;
          }
        </style>

        <marquee class="rgb-marquee" onmouseover="this.stop();" 
        onmouseout="this.start();">
          <?php echo $notice; ?>
        </marquee>


            <marquee 
              style="
                  padding: 10px; 
                  background: linear-gradient(to bottom, #ffffff, #e6e6e6); 
                  border-radius: 8px; 
                  border: 1px solid #0d6efd; 
                  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2), 
                              0px 10px 15px rgba(0, 0, 0, 0.1); 
                  color: #0d6efd; 
                  font-weight: bold; 
                  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
                  transform: perspective(500px) rotateX(1deg);"
                  onmouseover="this.stop();" 
                  onmouseout="this.start();"
                  >
              <?php echo $notice_two; ?>
          </marquee>


          <!DOCTYPE html>
          <html lang="en">
            <head>
              <meta charset="UTF-8">
              <meta name="viewport"
                content="width=device-width, initial-scale=1.0">
              <title>Dashboard</title>
              <style>
                /* Global body background with gradient animation */
                body {
                  background: linear-gradient(-45deg, #4571d8, #21586f, #666b7a, #2b5ea5);
                  background-size: 400% 400%;
                  animation: bg-anim 7s ease infinite;
                  margin: 0;
                  font-family: Arial, sans-serif;
                }
              
                @keyframes bg-anim {
                  0% {
                    background-position: 0% 50%;
                  }
                  50% {
                    background-position: 100% 50%;
                  }
                  100% {
                    background-position: 0% 50%;
                  }
                }
              
                /* Card Styles */
                .card {
                  background: #fff;
                  margin: 1rem auto;
                  border: 1px solid #ddd;
                  border-radius: 8px;
                  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                  overflow: hidden;
                  /* max-width: 600px; */
                }
              
                .card-title {
                  font-size: 1.5rem;
                  font-weight: bold;
                  margin-bottom: 1rem;
                }
              
                .card img {
                  width: 50px;
                  height: auto;
                  margin-right: 1rem;
                }
              
                .card-body {
                  display: flex;
                  align-items: center;
                  justify-content: space-between;
                  padding: 1rem;
                }
              
                .card-text {
                  margin: 0;
                  font-size: 1rem;
                }
              
                /* Button Styles */
                .btn-enter {
                  font-size: 1rem;
                  font-family: 'SolaimanLipi', sans-serif;
                  background: #23a6d5;
                  color: #fff;
                  border: none;
                  padding: 0.5rem 1rem;
                  cursor: pointer;
                  border-radius: 5px;
                  text-transform: uppercase;
                }
              
                .btn-enter:hover {
                  background: #1c8bb5;
                }
              
                .btn-price {
                  font-size: 1rem;
                  padding: 0.5rem 1rem;
                  color: #fff;
                  background: #e73c7e;
                  border: none;
                  cursor: pointer;
                  border-radius: 5px;
                  width: 100px; /* Ensure uniform button size */
                  text-align: center;
                }
              
                .btn-price:hover {
                  background: #d5316e;
                }
              
                /* File drop zone styles */
                .file-drop-zone.clickable {
                  background: aliceblue;
                  border: 2px dashed #4571d8;
                  padding: 1rem;
                  text-align: center;
                  font-size: 1rem;
                  color: #21586f;
                }
              
                /* Gradient effect for specific sections */
                .gran {
                  background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
                  background-size: 400% 400%;
                  animation: gradient 7s ease infinite;
                }
              
                @keyframes gradient {
                  0% {
                    background-position: 0% 50%;
                  }
                  50% {
                    background-position: 100% 50%;
                  }
                  100% {
                    background-position: 0% 50%;
                  }
                }

              </style>

              <style>
              .panel {
                  -webkit-box-shadow: none;
                  box-shadow: none
              }

              .panel .panel-heading,.panel .panel-title {
                  font-size: 11px;
                  line-height: 22px;
                  font-weight: 500;
                  text-transform: uppercase;
                  color: #fff
              }

              .panel .panel-footer {
                  font-size: 11px;
                  line-height: 22px;
                  font-weight: 500;
                  text-transform: uppercase
              }

              .panel-default .panel-heading,.panel-default .panel-title,.panel-default .panel-footer {
                  color: #98978b;
              }

              .panel-footer {
                  padding: 10px 15px;
                  background-color: #f8f5f0;
                  border-top: 1px solid #dfd7ca;
                  border-bottom-right-radius: 3px;
                  border-bottom-left-radius: 3px;
              }
</style>

            </head>
            <body>

              <div class="container">
                <div class="row">

                  <div class="card">
                    <div class="col-sm-12">

                      <div class="ts-main-content">
                        <div class="content-wrapper">
                          <div class="container-fluid">
                            <div class="row">
                              <div class="col-md-12">
                                <div class="row">
                                  <div
                                    class="col-12 col-xl-6 col-md-6 col-sm-6">
                                    <div
                                      class="panel panel-default my-3 text-white"
                                      style=" background: #365E32;">
                                      <div
                                        class="panel-body bk-info text-light">
                                        <div class="stat-panel text-center">
                                          <div
                                            class="stat-panel-number h1 text-white"><?php echo $total_s1 ?></div>
                                          <div
                                            class="stat-panel-title text-uppercase text-white">Server
                                            Copy Type 1 Orders</div>
                                        </div>
                                      </div>
                                      <a href="server_copy_old.php"
                                        class="block-anchor panel-footer text-center"
                                        style="padding: 7px 15px; bottom: 16px; left: 13px; text-decoration:none;">Order
                                        Server Copy Type 1 &nbsp; <i
                                          class="fa fa-arrow-right"></i></a>
                                    </div>
                                  </div>
                                  <div
                                    class="col-12 col-xl-6 col-md-6 col-sm-6">
                                    <div
                                      class="panel panel-default my-3 text-white"
                                      style=" background: #803D3B;">
                                      <div
                                        class="panel-body bk-info text-light">
                                        <div class="stat-panel text-center">
                                          <div
                                            class="stat-panel-number h1 text-white"><?php echo $total_s2 ?></div>
                                          <div
                                            class="stat-panel-title text-uppercase text-white">Server
                                            Copy Type 2 Orders</div>
                                        </div>
                                      </div>
                                      <a href="server_copy.php"
                                        class="block-anchor panel-footer text-center"
                                        style="padding: 7px 15px;  bottom: 16px; left: 13px; text-decoration:none;">Order
                                        Server Copy Type 2 &nbsp; <i
                                          class="fa fa-arrow-right"></i></a>
                                    </div>
                                  </div>
                               
                                  <div
                                    class="col-12 col-xl-6 col-md-6 col-sm-6">
                                    <div
                                      class="panel panel-default my-3 text-white"
                                      style=" background: #55679C;">
                                      <div
                                        class="panel-body bk-info text-light">
                                        <div class="stat-panel text-center">
                                          <div
                                            class="stat-panel-number h1 text-white"><?php echo $total_pin ?></div>
                                          <div
                                            class="stat-panel-title text-uppercase text-white">
                                            NID Info Verify
                                            Copy Orders</div>
                                        </div>
                                      </div>
                                      <a href="server_pin_v1.php"
                                        class="block-anchor panel-footer text-center"
                                        style="padding: 7px 15px;  bottom: 16px; left: 13px; text-decoration:none;">Full
                                        Detail &nbsp; <i
                                          class="fa fa-arrow-right"></i></a>
                                    </div>
                                  </div>
                                  <div
                                    class="col-12 col-xl-6 col-md-6 col-sm-6">
                                    <div
                                      class="panel panel-default my-3 text-white"
                                      style=" background: #088395;">
                                      <div
                                        class="panel-body bk-info text-light">
                                        <div class="stat-panel text-center">
                                          <div
                                            class="stat-panel-number h1 text-white"><?php echo $total_nid_auto ?></div>
                                          <div
                                            class="stat-panel-title text-uppercase text-white">MAKE NID PDF Orders</div>
                                        </div>
                                      </div>
                                      <a href="make_nid_auto.php"
                                        class="block-anchor panel-footer text-center"
                                        style="padding: 7px 15px;  bottom: 16px; left: 13px; text-decoration:none;">Full
                                        Detail &nbsp; <i
                                          class="fa fa-arrow-right"></i></a>
                                    </div>
                                  </div>     
                                  
                                  <div
                                    class="col-12 col-xl-6 col-md-6 col-sm-6">
                                    <div
                                      class="panel panel-default my-3 text-white"
                                      style=" background:rgb(8, 86, 149);">
                                      <div
                                        class="panel-body bk-info text-light">
                                        <div class="stat-panel text-center">
                                          <div
                                            class="stat-panel-number h1 text-white"><?php echo $total_nid_pdf ?></div>
                                          <div
                                            class="stat-panel-title text-uppercase text-white">NID PDF Orders</div>
                                        </div>
                                      </div>
                                      <a href="nid_pdf.php"
                                        class="block-anchor panel-footer text-center"
                                        style="padding: 7px 15px;  bottom: 16px; left: 13px; text-decoration:none;">Full
                                        Detail &nbsp; <i
                                          class="fa fa-arrow-right"></i></a>
                                    </div>
                                  </div>    
                                  
                                  <div
                                    class="col-12 col-xl-6 col-md-6 col-sm-6">
                                    <div
                                      class="panel panel-default my-3 text-white"
                                      style=" background:rgb(59, 6, 90);">
                                      <div
                                        class="panel-body bk-info text-light">
                                        <div class="stat-panel text-center">
                                          <div
                                            class="stat-panel-number h1 text-white"><?php echo $total_sign_copy ?></div>
                                          <div
                                            class="stat-panel-title text-uppercase text-white">Sign Copy Orders</div>
                                        </div>
                                      </div>
                                      <a href="sign_copy.php"
                                        class="block-anchor panel-footer text-center"
                                        style="padding: 7px 15px;  bottom: 16px; left: 13px; text-decoration:none;">Full
                                        Detail &nbsp; <i
                                          class="fa fa-arrow-right"></i></a>
                                    </div>
                                  </div>

                                  <div
                                  class="col-12 col-xl-6 col-md-6 col-sm-6">
                                  <div
                                    class="panel panel-default my-3 text-white"
                                    style=" background: #0D7C66;">
                                    <div
                                      class="panel-body bk-info text-light">
                                      <div class="stat-panel text-center">

                                        <?php
                                        $sql = $fetchdata->get_balance($user_id);
                                        $balance = mysqli_fetch_array($sql);
                                        ?>
                                        <div
                                          class="stat-panel-number h1 text-white"> <?php echo ($balance['deposit_sum'] - $balance['withdraw_sum']); ?>৳</div>
                                        <div
                                          class="stat-panel-title text-uppercase text-white">Balance</div>
                                      </div>
                                    </div>
                                    <a href="recharge.php"
                                      class="block-anchor panel-footer text-center"
                                      style="padding: 7px 15px;  bottom: 16px; left: 13px; text-decoration:none;">Click
                                      to recharge &nbsp; <i
                                        class="fa fa-arrow-right"></i></a>
                                  </div>
                                </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Loading Scripts -->

                    </div>
                  </div>

                  <!-- <div class="col-12">
      <div class="text-center my-4">
        <h1 class="card-title">DASHBOARD</h1>
      </div>
    </div> -->

                  <!-- <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <img src="assets/logo-icon/nid.png" alt="Logo" class="mt-3">
            <div class="ms-3">
           <a href="nid_make.php" class="btn btn-enter mt-3">এন আইডি মেক</a>
              </div>
          </div>
          <button href="nid_make.php" class="btn btn-danger mt-3 btn-price"><?php echo $nidmake; ?> ৳</button>
        </div>
      </div>
    </div> 

    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <img src="assets/logo-icon/sv.png" alt="Logo" class="mt-3">
            <div class="ms-3">
                <a href="server_pin.php" class="btn btn-enter mt-3"> ১৭ ডিজিট পিন</a>  
            </div>
          </div>
          <button href="server_pin.php"  class="btn btn-danger mt-3 btn-price"><?php echo $server_pin; ?> ৳</button>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <img src="assets/logo-icon/sv.png" alt="Logo" class="mt-3">
            <div class="ms-3">
                <a href="server_copy_old.php" class="btn btn-enter mt-3">সার্ভার কপি (OLD)</a>  
            </div>
          </div>
          <button href="server_copy_old.php" class="btn btn-danger mt-3 btn-price"><?php echo $sv; ?> ৳</button>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <img src="assets/logo-icon/sv.png" alt="Logo" class="mt-3">
            <div class="ms-3">
                <a href="server_copy.php" class="btn btn-enter mt-3">সার্ভার কপি (New)</a>  
            </div>
          </div>
          <button href="server_copy.php" class="btn btn-danger mt-3 btn-price"><?php echo $server_copy; ?> ৳</button>
        </div>
      </div>
    </div>


    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <img src="assets/logo-icon/bio.png" alt="Logo" class="mt-3">
            <div class="ms-3">
              <a href="biometric.php" class="btn btn-enter mt-3">বায়োমেট্রিক</a>
            </div>
          </div>
          <button href="biometric.php"  class="btn btn-danger mt-3 btn-price"><?php echo $bio; ?> ৳</button>
        </div>
      </div>
    </div> -->

                  <!--   <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <img src="assets/logo-icon/surakkha.png" alt="Logo">
            <div class="ms-3">
              <a href="new-card.php" class="btn btn-enter">সুরক্ষা ক্লোন</a>
            </div>
          </div>
          <button type="button" class="btn btn-danger btn-price">20 ৳</button>
        </div>
      </div>
    </div>   -->

                  <!--  <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <img src="assets/logo-icon/birth.png" alt="Logo">
            <div class="ms-3">
           <a href="birth.php" class="btn btn-enter">জন্মনিবন্ধন মেক</a>
            </div>
          </div>
          <button href="birth.php" class="btn btn-danger btn-price">150 ৳</button>
        </div>
      </div>
    </div>  -->

                  <!--  <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <img src="assets/logo-icon/police.png" alt="Logo">
            <div class="ms-3">
              <a href="police_clearance.php" class="btn btn-enter">পুলিশ ক্লিয়ারেন্স</a>
            </div>
          </div>
          <button type="button" class="btn btn-danger btn-price">50 ৳</button>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <img src="assets/logo-icon/amiprobashi.png" alt="Logo" style="width: 80px;">
            <div class="ms-3">
              <a href="pdo.php" class="btn btn-enter">পিডিও ক্লোন</a>
            </div>
          </div>
          <button type="button" class="btn btn-danger btn-price">20 ৳</button> -->
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Loading Scripts -->
        <!-- <script src="js/jquery.min.js"></script> -->
        <script src="js/bootstrap-select.min.js"></script>
        <script src="asset/js/bootstrap.min.js"></script>
        <script src="asset/js/main2.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
            setTimeout(function() {
            $('.succWrap').slideUp("slow");
            }, 3000);
            });
        </script>
        <script
          src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
      </body>
    </html>

    <?php include('includes/footer.php'); ?>
    <?php }else{ ?>
    <?php } ?>
    <?php } ?>