<?php
session_start();

if (!isset($_SESSION['uid'])) {
   header('location:logout.php');
   die();
} else {
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
      $make_birth = $row['make_birth'];
   }

   $sql = $obj->get_balance($user_id);
   $balance = mysqli_fetch_array($sql);
   $diff = $balance['deposit_sum'] - $balance['withdraw_sum'];

   if ($diff > $make_birth) {
      $withdraw = $obj->get_withdraw($user_id, $make_birth);
      $birthRegistrationNumber = $_POST['birthRegistrationNumber'];
      $officeAddressFirst = ucwords(strtolower($_POST['officeAddressFirst']));
      $officeAddressSecond = ucwords(strtolower($_POST['officeAddressSecond']));
      $gender = $_POST['gender'];
      $dateOfRegistration = $_POST['dateOfRegistration'];
      $dateOfIssuance = $_POST['dateOfIssuance'];
      $barCode = $_POST['barCode'];
      $qrLink = $_POST['qrLink'];
      $dateOfBirth = $_POST['dateOfBirth'];
      $dateOfBirthText = $_POST['dateOfBirthText'];
      $nameBangla = $_POST['nameBangla'];
      $nameEnglish = ucwords(strtolower($_POST['nameEnglish']));
      $fatherNameBangla = $_POST['fatherNameBangla'];
      $fatherNameEnglish = ucwords(strtolower($_POST['fatherNameEnglish']));
      $fatherNationalityBangla = $_POST['fatherNationalityBangla'];
      $fatherNationalityEnglish = ucwords(strtolower($_POST['fatherNationalityEnglish']));
      $motherNameBangla = $_POST['motherNameBangla'];
      $motherNameEnglish = ucwords(strtolower($_POST['motherNameEnglish']));
      $motherNationalityBangla = $_POST['motherNationalityBangla'];
      $motherNationalityEnglish = ucwords(strtolower($_POST['motherNationalityEnglish']));
      $birthplaceBangla = $_POST['birthplaceBangla'];
      $birthplaceEnglish = ucwords(strtolower($_POST['birthplaceEnglish']));
      $permanentAddressBangla = $_POST['permanentAddressBangla'];
      $permanentAddressEnglish = ucwords(strtolower($_POST['permanentAddressEnglish']));

   }else {
      $error_message = "You don't have enough balance";
   }
}
?>

   <!DOCTYPE html>
   <html lang="en">

   <head> 
	<meta charset="UTF-8">
	<title>Birth-<?php echo $birthRegistrationNumber; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="assets/birth-card-files/barcode.js"></script>
	<link rel="shortcut icon" href="logo.png">
	<meta property="og:image" content="icon.png">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<link rel="stylesheet" href="https://cdn.rawgit.com/sh4hids/bangla-web-fonts/solaimanlipi/stylesheet.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.0.0/css/bootstrap.min.css" integrity="sha512-NZ19NrT58XPK5sXqXnnvtf9T5kLXSzGQlVZL9taZWeTBtXoN3xIfTdxbkQh6QSoJfJgpojRqMfhyqBAAEeiXcA==" crossorigin="anonymous" referrerpolicy="no-referrer">
	<script>var birthRegistrationNumber = "<?php echo $birthRegistrationNumber; ?>"; document.addEventListener('contextmenu', function (e) {e.preventDefault();});document.addEventListener('keydown', function (e) {if(e.ctrlKey){e.preventDefault();}});</script>
	<link rel="stylesheet" href="assets/birth-card-files/card.css">
	<style>@page {margin: 0;size: A4;}.bmarg{margin-top: -1px;} .bangla{font-family: SolaimanLipi!important;font-size: 17px!important;}</style>
	<style>
        body .a4_page .main_wrapper .mr_body .middle .new_td p,
        body .a4_page .main_wrapper .mr_body .middle .new_td_2 p,
        body .a4_page .main_wrapper .mr_body .middle .td p,
        body .a4_page .main_wrapper .mr_body .top_part1 .left p,
        body .a4_page .main_wrapper .mr_body .top_part1 .right p,
        body .a4_page .main_wrapper .mr_footer .top .left h2,
        body .a4_page .main_wrapper .mr_footer .top .left p,
        body .a4_page .main_wrapper .mr_footer .top .right h2,
        body .a4_page .main_wrapper .mr_footer .top .right p{
            color: #2f2f2f !important
        }
        
        @font-face {
            font-family: 'DejaVu';
            src: url('fonts/DejaVu.ttf') format('truetype');
        }

	</style>
</head>

<body>
	<div class="a4_page" id="a4_page">
		<div class="main_wrapper">
			<img src="assets/birth-card-files/ri_1.png" class="main_logo" alt="">
			<span style="z-index: 10;">
			<div class="mr_header">
					<div class="left_part_hidden"></div>
					<div class="left_part">
						<img style="height:110px; width:110px;" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&amp;data=<?php echo $qrLink; ?>" alt="">
						<h2><?php echo $barCode; ?></h2>
					</div>
					<div class="middle_part">
						<img src="assets/birth-card-files/bd_logo.png" alt="" class="main_logo_r">
						<img src="assets/birth-card-files/bd_logo.png" alt="" style="opacity: 0;">
						<h2>Government of the People’s Republic of Bangladesh</h2>
						<p class="office">Office of the Registrar, Birth and Death Registration</p>
						<p class="address1" style="text-transform: capitalize;"><?php echo $officeAddressFirst; ?></p>
						<p class="address2" style="text-transform: capitalize;"><?php echo $officeAddressSecond; ?></p>
						<p class="rule_y">(Rule 9, 10)</p>
						<h1><span class="bn" style="font-family: SolaimanLipi!important;font-size: 23px!important;">জন্ম নিবন্ধন সনদ /</span> <span class="en">Birth Registration Certificate</span></h1>
					</div>
					<div class="right_part_hidden"></div>
					<div class="right_part">
<canvas style="height: 26px; width:220px;" id="barcode" width="310" height="120"></canvas>
					</div>
				</div>
				<div class="mr_body">
					<div class="top_part1">
						<div class="left">
							<p>Date of Registration</p>
							<p><?php echo $dateOfRegistration; ?></p>
						</div>
						<div class="middle" style="font-family: 'Times New Roman', Times, serif;">
    <h2>Birth Registration Number</h2>
    <h1 style="font-family: 'DejaVu', sans-serif; font-weight: 600;font-size: 20px;"><?php echo $birthRegistrationNumber; ?></h1>
</div>

						
						
						
						<div class="right">
							<p>Date of Issuance</p>
							<p><?php echo $dateOfIssuance; ?></p>
						</div>
					</div>
					<div class="middle">
						<div style="margin-top: 2px;margin-bottom: 5px;" class="new_td_2">
							<div class="left">
								<div class="part1">
									<p class="bn">Date of Birth<span style="margin-left: 42px;" class="clone">:</span></p>
								</div>
								<div class="part2">
									<p><span class="bn"><?php echo $dateOfBirth; ?></span></p>
								</div>
							</div>
							<div class="right">
								<div class="part1">
									<p><span style="margin-left: 95px;" class="clone">Sex :</span></p>
								</div>
								<div class="part2">
									<p><span><?php echo $gender; ?></span></p>
								</div>
							</div>
						</div>
						<div style="margin-top: 5px;margin-bottom: 24px !important;" class="td">
							<div class="left">
								<div style="width: 130px;" class="part1">
									<p>In Word<span>:</span></p>
								</div>
								<div class="part2" style="width: 400px;">

									<p><span style="margin-left:5px"><?php echo $dateOfBirthText; ?></span></p>
								</div>
							</div>
						</div>
						<div style="margin-top: 7px;" class="new_td">
							<div class="left">
								<div class="part1">
									<p class="bn" style="font-family: SolaimanLipi!important;font-size: 17px!important;margin-top: -2.5px;">নাম<span style="margin-left: 103px;" class="clone">:</span></p>
								</div>
								<div class="part2" id="name_data_bn">
									<p style="margin-top: -2.5px;"><span class="bn" style="font-family: SolaimanLipi!important;font-size: 17px !important;"><?php echo $nameBangla; ?></span></p>
								</div>
							</div>
							<div class="right">
								<div class="part1">
									<p style="font-weight:500">Name<span style="margin-left: 95px;" class="clone">:</span></p>
								</div>
								<div class="part2">
									<p><span style="font-weight:500;text-transform: capitalize;"><?php echo $nameEnglish; ?></span></p>
								</div>
							</div>
						</div>
						<div id="mother_content" style="margin-top: 17px;" class="new_td">
							<div class="left">
								<div class="part1">
									<p class="bn" style="font-family: SolaimanLipi!important;font-size: 17px!important;margin-top: -2.5px;">মাতা<span style="margin-left: 98px;" class="clone">:</span></p>
								</div>
								<div class="part2" id="motherName_data_bn">
									<p style="margin-top: -2.5px;"><span class="bn" style="font-family: SolaimanLipi!important;font-size: 17px !important;"><?php echo $motherNameBangla; ?></span></p>
								</div>
							</div>
							<div class="right">
								<div class="part1">
									<p style="font-weight:500">Mother<span style="margin-left: 87px;" class="clone">:</span></p>
								</div>
								<div class="part2">
									<p><span style="font-weight:500;text-transform: capitalize;"><?php echo $motherNameEnglish; ?></span></p>
								</div>
							</div>
						</div>
						<div id="motherNanality_content" style="margin-top: 17px;" class="new_td">
							<div class="left">
								<div class="part1">
									<p class="bn" style="font-family: SolaimanLipi!important;font-size: 17px!important;margin-top: -2.5px;">মাতার জাতীয়তা<span style="margin-left: 26px;" class="clone">:</span></p>
								</div>
								<div class="part2">
									<p style="margin-top: -2.5px;"><span class="bn" style="font-family: SolaimanLipi!important;font-size: 17px !important;"><?php echo $motherNationalityBangla; ?></span></p>
								</div>
							</div>
							<div class="right">
								<div class="part1">
									<p style="font-weight:500">Nationality<span style="margin-left: 64px;" class="clone">:</span></p>
								</div>
								<div class="part2">
									<p><span style="font-weight:500;text-transform: capitalize;"><?php echo $motherNationalityEnglish; ?></span></p>
								</div>
							</div>
						</div>
						<div style="margin-top: 16px;" class="new_td">
							<div class="left">
								<div class="part1">
									<p class="bn" style="font-family: SolaimanLipi!important;font-size: 17px!important;margin-top: -2.5px;">পিতা<span style="margin-left: 96px;" class="clone">:</span></p>
								</div>
								<div class="part2" id="fatherName_data_bn">
									<p style="margin-top: -2.5px;"><span class="bn" style="font-family: SolaimanLipi!important;font-size: 17px !important;"><?php echo $fatherNameBangla; ?></span></p>
								</div>
							</div>
							<div class="right">
								<div class="part1">
									<p style="font-weight:500">Father<span style="margin-left: 91px;" class="clone">:</span></p>
								</div>
								<div class="part2">
									<p><span style="font-weight:500;text-transform: capitalize;"><?php echo $fatherNameEnglish; ?></span></p>
								</div>
							</div>
						</div>
						<div id="fatherNanality_content" style="margin-top: 17px;" class="new_td">
							<div class="left">
								<div class="part1">
									<p class="bn" style="font-family: SolaimanLipi!important;font-size: 17px!important;margin-top: -2.5px;">পিতার জাতীয়তা<span style="margin-left: 26px;" class="clone">:</span></p>
								</div>
								<div class="part2">
									<p style="margin-top: -2.5px;"><span class="bn" style="font-family: SolaimanLipi!important;font-size: 17px !important;"><?php echo $fatherNationalityBangla; ?></span></p>
								</div>
							</div>
							<div class="right">
								<div class="part1">
									<p style="font-weight:500">Nationality<span style="margin-left: 65px;" class="clone">:</span></p>
								</div>
								<div class="part2">
									<p><span style="font-weight:500;text-transform: capitalize;"><?php echo $fatherNationalityEnglish; ?></span></p>
								</div>
							</div>
						</div>
						<div style="margin-top: 17px;" class="new_td">
							<div class="left">
								<div class="part1">
									<p class="bn" style="font-family: SolaimanLipi!important;font-size: 17px!important;margin-top: -2.5px;">জন্মস্থান<span style="margin-left: 78px;" class="clone">:</span></p>
								</div>
								<div class="part2">
									<p style="margin-top: -2.5px;"><span class="bn" style="font-family: SolaimanLipi!important;font-size: 17px !important;"><?php echo $birthplaceBangla; ?></span></p>
								</div>
							</div>
							<div class="right">
								<div class="part1">
									<p style="width: 153px; font-weight:500">Place of Birth<span style="margin-left: 46px;margin-right: 0;" class="clone">:</span></p>
								</div>
								<div class="part2">
									<p><span style="font-weight:500;text-transform: capitalize;"><?php echo $birthplaceEnglish; ?></span></p>
								</div>
							</div>
						</div>
						<div style="margin-top: 30px;" class="new_td">
							<div class="left">
								<div class="part1">
									<p class="bn" style="width: 146px;font-family: SolaimanLipi!important;font-size: 17px!important;margin-top: -2.5px;">স্থায়ী ঠিকানা<span style="margin-left:53px;margin-right: 0;" class="clone">:</span></p>
								</div>
								<div class="part2">
									<p style="margin-top: -2.5px;"><span class="bn" style="font-family: SolaimanLipi!important;font-size: 17px !important;"><?php echo $permanentAddressBangla; ?></span></p>
								</div>
							</div>
							<div class="right">
								<div class="part1">
									<p style="display:flex; width:154px; font-weight:500">Permanent<br>Address<span style="margin-left: 64px;" class="clone">:</span></p>
								</div>
								<div class="part2">
									<p><span style="font-weight:500;text-transform: capitalize;"><?php echo $permanentAddressEnglish; ?></span></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</span>
			<div class="mr_footer">
				<div class="top" style="margin-bottom: 57.5px;">
					<div class="left">
						<h2 style="width:10rem; margin-top: 0px;">Seal &amp; Signature</h2>
						<p style="margin-top: 0px;">Assistant to Registrar</p>
						<p style="margin-top: 0px;">(Preparation, Verification)</p>
					</div>
					<div class="right">
						<h2 style="width:10rem">Seal &amp; Signature</h2>
						<h2>
							<p>Registrar</p>
						</h2>
					</div>
				</div>
				<div class="bottom">
					<p>This certificate is generated from bdris.gov.bd, and to verify this certificate, please scan the above QR Code &amp; Bar Code.</p>
				</div>
			</div>
		</div>
	</div>
	<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>

	<script>
		window.onload = function() {
        setBarcode();
        // Assuming setSetting() and wp() functions are defined elsewhere
        setSetting();
        setTimeout(wp, 500);
    }

    function setBarcode() {
        var birthRegistrationNumber = "<?php echo $birthRegistrationNumber; ?>"; // Replace with the actual registration number
        JsBarcode("#barcode", birthRegistrationNumber, {
            format: "CODE128",
            displayValue: false,
        });
    }
		function setSetting(){
			var elementWidth = $('#name_data_bn').height();
			if (Number(Math.floor(elementWidth)) > 23) {
				$('#mother_content').css("margin-top", "0px");
			}

			var elementWidth = $('#motherName_data_bn').height();
			if (Number(Math.floor(elementWidth)) > 23) {
				$('#motherNanality_content').css("margin-top", "0px");
			}

			var elementWidth = $('#fatherName_data_bn').height();
			if (Number(Math.floor(elementWidth)) > 23) {
				$('#fatherNanality_content').css("margin-top", "0px");
			}
		}
		
		function wp(){
			window.print();
		}
		
		window.addEventListener('click', function(){
			window.print();
		});
	</script>
</body>
</html>


