<?php
session_start();
error_reporting(0);

if ($_SESSION['uid'] != "1") {
  header('location:logout.php');
} else {

?>
  <?php
  include_once('function.php');
  $obj = new DB_con();
  $fetchdata = new DB_con();
  $user_id = $_SESSION['uid'];

  $sql = $obj->get_balance($user_id);
  $balance = mysqli_fetch_array($sql);
  $diff = $balance['deposit_sum'] - $balance['withdraw_sum'];

  if (isset($_POST['login_control'])) {
    $in_login = $_POST['login_control'];
    $result = $obj->in_login($in_login);
    if ($result) {
      // echo "<script>alert('login setting updated.');</script>";
      $success_message = "login setting updated.";
    }
  }

  if (isset($_POST['notice_control'])) {
    $in_notice = $_POST['notice_control'];
    $result = $obj->in_notice($in_notice);
    if ($result) {
      // echo "<script>alert('Notice updated.');</script>";
      $success_message = "Notice updated.";
    }
  }  
  if (isset($_POST['notice_two'])) {
    $in_notice_two = $_POST['notice_two'];
    $result = $obj->in_notice_two($in_notice_two);
    if ($result) {
      // echo "<script>alert('Notice updated.');</script>";
      $success_message = "Notice Two updated.";
    }
  }
  
  
  if (isset($_POST['register_control'])) {
    $in_register = $_POST['register_control'];
    $result = $obj->in_register($in_register);
    if ($result) {
      // echo "<script>alert('register setting updated.');</script>";
      $success_message = "Register setting updated.";

    }
  }
  
  if (isset($_POST['approval_control'])) {
    $in_approval = $_POST['approval_control'];
    $result = $obj->in_approval($in_approval);
    if ($result) {
      // echo "<script>alert('approval setting updated.');</script>";
      $success_message = "approval setting updated.";

    }
  }
  
  if (isset($_POST['charge_control'])) {
    $in_charge = $_POST['charge_control'];
    $result = $obj->in_charge($in_charge);
    if ($result) {
      // echo "<script>alert('Charge updated.');</script>";
      $success_message = "Charge updated.";
    }
  }  
  
  if (isset($_POST['server_copy'])) {
    $in_server_copy = $_POST['server_copy'];
    $result = $obj->in_server_copy($in_server_copy);
    if ($result) {
      // echo "<script>alert('Charge updated.');</script>";
      $success_message = "Server Copy updated.";
    }
  }  
  
  if (isset($_POST['pdf_copy'])) {
    $in_pdf_copy = $_POST['pdf_copy'];
    $result = $obj->in_pdf_copy($in_pdf_copy);
    if ($result) {
      // echo "<script>alert('Charge updated.');</script>";
      $success_message = "Pdf Copy updated.";
    }
  }  
  
  if (isset($_POST['sign_copy'])) {
    $in_sign_copy = $_POST['sign_copy'];
    $result = $obj->in_sign_copy($in_sign_copy);
    if ($result) {
      // echo "<script>alert('Charge updated.');</script>";
      $success_message = "Sign Copy updated.";
    }
  }   
  
  
  if (isset($_POST['sign_to_nid'])) {
    $in_sign_to_nid = $_POST['sign_to_nid'];
    $result = $obj->in_sign_to_nid($in_sign_to_nid);
    if ($result) {
      // echo "<script>alert('Charge updated.');</script>";
      $success_message = "Sign To Nid updated.";
    }
  }  


  if (isset($_POST['nid_pdf_copy'])) {
    $in_nid_pdf_copy = $_POST['nid_pdf_copy'];
    $result = $obj->in_nid_pdf_copy($in_nid_pdf_copy);
    if ($result) {
      // echo "<script>alert('Charge updated.');</script>";
      $success_message = "NID PDF Copy updated.";
    }
  }
  
  if (isset($_POST['server_pin'])) {
    $in_server_pin = $_POST['server_pin'];
    $result = $obj->in_server_pin($in_server_pin);
    if ($result) {
      // echo "<script>alert('Charge updated.');</script>";
      $success_message = "Server Pin updated.";
    }
  }


  if (isset($_POST['recharge_control'])) {
    $in_rg_msg = $_POST['recharge_control'];
    $result = $obj->in_rg_msg($in_rg_msg);
    if ($result) {
      // echo "<script>alert('Recharge msg updated.');</script>";
      $success_message = "Charge updated.";

    }
  }
  if (isset($_POST['bot_control'])) {
    $in_bot = $_POST['bot_control'];
    $result = $obj->in_bot($in_bot);
    if ($result) {
      // echo "<script>alert('Bot token updated.');</script>";
      $success_message = "Bot token updated.";

    }
  }
  if (isset($_POST['log_control'])) {
    $in_log = $_POST['log_control'];
    $result = $obj->in_log($in_log);
    if ($result) {
      // echo "<script>alert('Log channel updated.');</script>";
      $success_message = "Log channel updated";
    }
  }  
  
  if (isset($_POST['log_channel'])) {
    $log_channel = $_POST['log_channel'];
    $result = $obj->log_channel($log_channel);
    if ($result) {
      // echo "<script>alert('Log channel updated.');</script>";
      $success_message = "NID Make Updated";
    }
  }

  if (isset($_POST['robi_token'])) {
    $in_robi_token = $_POST['robi_token'];
    $result = $obj->in_robi_token($in_robi_token);
    if ($result) {
      // echo "<script>alert('Minimum Recharge updated.');</script>";
      $success_message = "Minimum Recharge updated.";

    }
  }
  if (isset($_POST['bl_user'])) {
    $in_bl_id = $_POST['bl_user'];
    $result = $obj->in_bl_id($in_bl_id);
    if ($result) {
      // echo "<script>alert('BL user updated.');</script>";
      $success_message = "BL user updated.";
    }
  }
  if (isset($_POST['bl_token'])) {
    $in_bl_token = $_POST['bl_token'];
    $result = $obj->in_bl_token($in_bl_token);
    if ($result) {
      // echo "<script>alert('BL token updated.');</script>";
      $success_message = "BL token updated.";
    }
  }  
  
  if (isset($_POST['bot_token'])) {

    $in_bot_token = $_POST['bot_token'];
    $result = $obj->in_bot_token($in_bot_token);
    if ($result) {
      $success_message = "Number To Bio Info Price updated.";
    }
  }  


  if (isset($_POST['bl_location'])) {

    $in_bl_location = $_POST['bl_location'];
    $result = $obj->in_bl_location($in_bl_location);
    if ($result) {
      $success_message = "Banglalink Location Price updated.";
    }
  }
  
  if (isset($_POST['make_birth'])) {

    $in_make_birth = $_POST['make_birth'];
    $result = $obj->in_make_birth($in_make_birth);
    if ($result) {
      $success_message = "Make Birth Price updated.";
    }
  }

  
  
  
  

  //BIO FUNCTION
  if (isset($_POST['robi_bio'])) {

    $in_robi_bio = $_POST['robi_bio'];
    $result = $obj->in_robi_bio($in_robi_bio);
    if ($result) {
      $success_message = "Robi Bio Price updated.";
    }
  }  
  
  if (isset($_POST['bl_bio'])) {

    $in_bl_bio = $_POST['bl_bio'];
    $result = $obj->in_bl_bio($in_bl_bio);
    if ($result) {
      $success_message = "Bl Bio Price updated.";
    }
  }  

  if (isset($_POST['airtel_bio'])) {

    $in_airtel_bio = $_POST['airtel_bio'];
    $result = $obj->in_airtel_bio($in_airtel_bio);
    if ($result) {
      $success_message = "Airtel Bio Price updated.";
    }
  }
 
  if (isset($_POST['gp_bio'])) {

    $in_gp_bio = $_POST['gp_bio'];
    $result = $obj->in_gp_bio($in_gp_bio);
    if ($result) {
      $success_message = "GP Bio Price updated.";
    }
  } 
  
  if (isset($_POST['teletalk_bio'])) {

    $in_teletalk_bio = $_POST['teletalk_bio'];
    $result = $obj->in_teletalk_bio($in_teletalk_bio);
    if ($result) {
      $success_message = "Teletalk Bio Price updated.";
    }
  }


  ?>

  <?php include('includes/head.php');
  ?>
  <style>
    .card-body {
      overflow: auto;
    }
  </style>
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Control Panel</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">controls</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">


        <div class="card-body pt-0">
          <div class="card mb-3">
            <div class="card-header">
              <div class="row flex-between-end">
                <div class="col-auto align-self-center">
                  <h5 class="mb-0" data-anchor="data-anchor">Control Panel</h5>
                </div>
              </div>
            </div>
            <div class="card-body bg-light">
              <div class="row light">


                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-info">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">Login</div>
                        <select name="login_control" class="form-select" aria-label="select on or off">
                          <?php if ($login == 1) { ?>
                            <option value="1" selected>ON</option>
                            <option value="0">OFF</option>
                          <?php } else { ?>
                            <option value="1">ON</option>
                            <option value="0" selected>OFF</option>
                          <?php }

                          ?>
                        </select>
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-success">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">Register</div>
                        <select name="register_control" class="form-select" aria-label="select on or off">
                          <?php if ($register == 1) { ?>
                            <option value="1" selected>ON</option>
                            <option value="0">OFF</option>
                          <?php } else { ?>
                            <option value="1">ON</option>
                            <option value="0" selected>OFF</option>
                          <?php }

                          ?>

                        </select>
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" name="register">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>



                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-danger">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">SERVER COPY</div>
                        <input value="<?php echo $charge; ?>" class="form-control" type="text" name="charge_control" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-info" type="submit" name="charge">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>


                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-danger">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">SERVER COPY NEW</div>
                        <input value="<?php echo $server_copy; ?>" class="form-control" type="text" name="server_copy" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-info" type="submit" >Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>


                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-success">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">PDF COPY</div>
                        <input value="<?php echo $pdf_copy; ?>" class="form-control" type="text" name="pdf_copy" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" >Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-danger">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">NID MAKE</div>
                        <input value="<?php echo $log_channel; ?>" class="form-control" type="text" name="log_channel" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-info" type="submit">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-info">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">NUMBER TO INFO</div>
                        <input value="<?php echo $bot_token; ?>" class="form-control" type="text" name="bot_token" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>  
                
                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-success">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">SERVER PIN INFO</div>
                        <input value="<?php echo $server_pin; ?>" class="form-control" type="text" name="server_pin" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-danger">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">Minimum Recharge</div>
                        <input value="<?php echo $robi_token; ?>" class="form-control" type="text" name="robi_token" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" name="log-channel">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-primary">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">Banglalink Location</div>
                        <input value="<?php echo $bl_location; ?>" class="form-control" type="text" name="bl_location" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" >Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-info">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">SIGN COPY</div>
                        <input value="<?php echo $sign_copy; ?>" class="form-control" type="text" name="sign_copy" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" >Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>                
                
                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-success">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">SIGN TO NID</div>
                        <input value="<?php echo $sign_to_nid; ?>" class="form-control" type="text" name="sign_to_nid" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" >Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-success">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">NID PDF COPY</div>
                        <input value="<?php echo $nid_pdf_copy; ?>" class="form-control" type="text" name="nid_pdf_copy" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" >Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-primary">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">Birth Make</div>
                        <input value="<?php echo $make_birth; ?>" class="form-control" type="text" name="make_birth" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" >Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div> 
                
                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-danger">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">ROBI BIO</div>
                        <input value="<?php echo $robi_bio; ?>" class="form-control" type="text" name="robi_bio" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" >Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-info">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">BL BIO</div>
                        <input value="<?php echo $bl_bio; ?>" class="form-control" type="text" name="bl_bio" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" >Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-success">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">AIRTEL BIO</div>
                        <input value="<?php echo $airtel_bio; ?>" class="form-control" type="text" name="airtel_bio" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" >Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-primary">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">GP BIO</div>
                        <input value="<?php echo $gp_bio; ?>" class="form-control" type="text" name="gp_bio" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" >Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-success">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">TEL BIO</div>
                        <input value="<?php echo $teletalk_bio; ?>" class="form-control" type="text" name="teletalk_bio" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" >Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                 <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-danger">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">BIOMETRIC</div>
                        <input value="<?php echo $bot_token; ?>" class="form-control" type="text" name="bot_token" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-primary" type="submit" >Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>





                <br>

                <div class="col-sm-6 mb-4">
                  <div class="card text-white bg-info">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">Recharge msg</div>
                        <textarea class="form-control" style="min-height: 200px;" type="text" name="recharge_control" required=""><?php echo $recharge_msg; ?></textarea>
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" name="recharge">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 mb-4">
                  <div class="card text-white bg-info">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title text-dark">Notice</div>
                        <textarea style="min-height: 200px;" class="form-control" type="text" name="notice_control" required=""><?php echo $notice; ?></textarea>
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" name="notice">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 mb-4">
                  <div class="card text-white bg-info">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title text-dark">Notice Two</div>
                        <textarea style="min-height: 200px;" class="form-control" type="text" name="notice_two" required=""><?php echo $notice_two; ?></textarea>
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>


                <!-- <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-info">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">Bot Token</div>
                        <input class="form-control" type="text" name="bot_control" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" name="bot">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-dark">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title text-white">log channel</div>
                        <input class="form-control" type="text" name="log_control" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" name="log-channel">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-primary">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">Robi Api user id</div>
                        <input class="form-control" type="text" name="robi_user" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" name="log-channel">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-secondary">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">Robi Token</div>
                        <input class="form-control" type="text" name="robi_token" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" name="log-channel">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-success">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">Bl user number</div>
                        <input class="form-control" type="text" name="bl_user" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" name="log-channel">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 col-lg-4 mb-4">
                  <div class="card text-white bg-danger">
                    <div class="card-body">
                      <form method="post" action="">
                        <div class="card-title">BL Token</div>
                        <input class="form-control" type="text" name="bl_token" required="">
                        <div class="col-12 mt-3 text-end">
                          <button class="btn btn-danger" type="submit" name="log-channel">Submit</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div> -->

              </div>
            </div>
            <!-- <div class="col-12  text-center">
              MADE WITH LOVE BY <a href="https://fb.me/teamX">
                <p class="text-bold">TEAM X</p>
              </a>
            </div> -->
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include('includes/footer.php');
  ?>
<?php } ?>