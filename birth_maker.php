<?php
session_start();

if (!isset($_SESSION['uid'])) {
   header('location:logout.php');
   die();
} else {
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
      $log_channel = $row['log_channel'];
      $make_birth = $row['make_birth'];
   }

   $sql = $obj->get_balance($user_id);
   $balance = mysqli_fetch_array($sql);
   $diff = $balance['deposit_sum'] - $balance['withdraw_sum'];

   // $diff >= $log_channel
}
?>

   <!DOCTYPE html>
   <html lang="en">

   <head>
      <meta charset="utf-8">
      <meta content="width=device-width, initial-scale=1.0" name="viewport">
      <meta content="" name="description">
      <meta content="" name="keywords">
      <title>Birth Make</title>
      <link href="https://surokkha.gov.bd/favicon.png" rel="icon">
      <link href="https://surokkha.gov.bd/favicon.png" rel="apple-touch-icon">
      <link href="https://fonts.gstatic.com" rel="preconnect">
      <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.1.1/css/all.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" type="text/javascript"></script>
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
      <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
      <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
      <!-- <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
      <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
      <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
      <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
      <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet"> -->
      <link href="assets/css/style.css" rel="stylesheet">
   </head>

   <body>
      <?php include('includes/head.php'); ?>
      <?php include('includes/sidebar.php'); ?>
      <main id="main" class="main">
         <section class="section profile">
            <div id="inp" class="container card px-3 py-5 mt-6 col-md-12 mb-5">
               <!-- <marquee style="padding: 10px;background: white;border-radius: 5px;border: 1px solid #0d6efd;"><?php echo $notice ?></marquee> -->
               
               <marquee class="alert alert-dark text-dark" role="alert" onmouseover="this.stop()" onmouseout="this.start()" style="font-size: 1rem; border-radius: 5px;border: 1px solid #0d6efd;">
                <?php echo $notice; ?>
               </marquee>
                <!-- notice end -->
               <div class="card mt-2">
                  <div class="card-body">
                     <form method="POST" action="birth-bn.php" id="BrnBN" target="_blank" enctype="multipart/form-data">
                        <div class="row">
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Register Office Address</label>
                                 <input type="text" name="officeAddressFirst" id="officeAddressFirst" class="form-control" placeholder="রেজিস্টার অফিসের ঠিকানা" value="" required>
                              </div>
                           </div>
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Upazila/Pourashava/City Corporation, Zila</label>
                                 <input type="text" name="officeAddressSecond" id="officeAddressSecond" class="form-control" placeholder="উপজেলা/পৌরসভা/সিটি কর্পোরেশন, জেলা" value="" required>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Birth Registration Number</label>
                                 <input type="text" name="birthRegistrationNumber" id="birthRegistrationNumber" class="form-control" placeholder="XXXXXXXXXXXXXXXXX" value="" required>
                              </div>
                           </div>
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Gender </label>
                                 <select name="gender" id="gender" class="form-control">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Date of Registration </label>
                                 <input type="text" class="form-control" id="dateOfRegistration" name="dateOfRegistration" placeholder="DD/MM/YYYY" value="" required>
                              </div>
                           </div>
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Date of Issuance </label>
                                 <input type="text" class="form-control" name="dateOfIssuance" id="dateOfIssuance" placeholder="DD/MM/YYYY" value="" required>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Left Bar Code</label><br>
                                 <input type="checkbox" id="barCodeInput" name="barCode" value="" onchange="handleBarcodeInputChange()"> <label for="barCodeInput">Input</label>
                                 <input type="checkbox" id="barCode1" name="barCode" value="DZIH" onclick="autoSelect(1)"> <label for="barCode1">1</label>
                                 <input type="checkbox" id="barCode2" name="barCode" value="DMIQ" onclick="autoSelect(2)"> <label for="barCode2">2</label>
                                 <input type="checkbox" id="barCode3" name="barCode" value="AEMA" onclick="autoSelect(3)"> <label for="barCode3">3</label>
                                 <input type="checkbox" id="barCode4" name="barCode" value="RQHQ" onclick="autoSelect(4)"> <label for="barCode4">4</label>
                                 <input type="checkbox" id="barCode5" name="barCode" value="IRIA" onclick="autoSelect(5)"> <label for="barCode5">5</label>
                                 <input type="checkbox" id="barCode6" name="barCode" value="ERRD" onclick="autoSelect(6)"> <label for="barCode6">6</label>
                                 <input type="text" id="barcodeInputField" name="barcodeInputField" class="form-control" readonly>
                              </div>
                           </div>
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>QR Link</label><br>
                                 <input type="checkbox" id="qrLinkInput" name="qrLink" value="" onchange="handleQrLinkInputChange()"> <label for="qrLinkInput">Input</label>
                                 <input type="checkbox" id="qrLink1" name="qrLink" value="https://bdris.gov.bd/certificate/verify?key=Y2p7chu0b0iI8aWymonnhfkpKoOKE3J5kRsiGb0ZVOe0SqgyoCnMtFT+wx+Ikn/O" onclick="autoSelect(1)"> <label for="qrLink1">1</label>
                                 <input type="checkbox" id="qrLink2" name="qrLink" value="https://bdris.gov.bd/certificate/verify?key=5gsdKUqk6MwdcGXYQrJJ3oFDeWtXYcK8sjPw9HTPyimw/CAb4Mia41mwDYBdwU/z" onclick="autoSelect(2)"> <label for="qrLink2">2</label>
                                 <input type="checkbox" id="qrLink3" name="qrLink" value="https://bdris.gov.bd/certificate/verify?key=NstWvr0h0rOGf84T/VVLyG/k4N0SH4MULThJN9NKQzrcZXdnNjfrHy3f6R3cKkcP" onclick="autoSelect(3)"> <label for="qrLink3">3</label>
                                 <input type="checkbox" id="qrLink4" name="qrLink" value="https://bdris.gov.bd/certificate/verify?key=KU32mvimW7KmtS/jr0hNN1VZHRwISZmcLnrzJYmadhEkpmcxFDEqLIy0exp3OVbG" onclick="autoSelect(4)"> <label for="qrLink4">4</label>
                                 <input type="checkbox" id="qrLink5" name="qrLink" value="https://bdris.gov.bd/certificate/verify?key=znKwFj1Z1NAmN1Pgt4f/wcjTogYsu7aOytY8p2pk3OvTRDJkegjZjefRlv1GgJ+u" onclick="autoSelect(5)"> <label for="qrLink5">5</label>
                                 <input type="checkbox" id="qrLink6" name="qrLink" value="https://bdris.gov.bd/certificate/verify?key=S/m6tkNVXq24lDXol5XRdF6zC/FRDZq327Fk7Ur9UJX94kJHLkB5tA2nX83QsVYF" onclick="autoSelect(6)"> <label for="qrLink6">6</label>
                                 <input type="text" id="qrLinkInputField" name="qrLinkInputField" class="form-control" readonly>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Date of Birth </label>
                                 <input type="text" class="form-control" name="dateOfBirth" id="dateOfBirth" placeholder="DD/MM/YYYY" value="" required>
                              </div>
                           </div>
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Date of Birth in Word</label>
                                 <input type="text" class="form-control" id="dateOfBirthText" name="dateOfBirthText" placeholder="Eleventh August two thousand three" value="" required>
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>নাম </label>
                                 <input type="text" name="nameBangla" id="nameBangla" class="form-control" placeholder="সম্পুর্ন নাম বাংলায়" value="" required>
                              </div>
                           </div>
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Name</label>
                                 <input type="text" id="nameEnglish" name="nameEnglish" class="form-control" placeholder="Full Name in English" value="">
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>পিতার নাম </label>
                                 <input type="text" id="fatherNameBangla" name="fatherNameBangla" class="form-control" placeholder="পিতার নাম বাংলায়" value="" required>
                              </div>
                           </div>
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Father Name</label>
                                 <input type="text" id="fatherNameEnglish" name="fatherNameEnglish" class="form-control" placeholder="Father Name in English" value="">
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>পিতার জাতীয়তা</label>
                                 <input type="text" class="form-control" name="fatherNationalityBangla" id="fatherNationalityBangla" placeholder="পিতার জাতীয়তা বাংলায়" value="বাংলাদেশী">
                              </div>
                           </div>
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Father Nationality</label>
                                 <input type="text" class="form-control" id="fatherNationalityEnglish" name="fatherNationalityEnglish" placeholder="Father Nationality in English" value="Bangladeshi">
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>মাতার নাম </label>
                                 <input type="text" id="motherNameBangla" name="motherNameBangla" class="form-control" placeholder="মাতার নাম বাংলায়" value="" required>
                              </div>
                           </div>
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Mother Name</label>
                                 <input type="text" id="motherNameEnglish" name="motherNameEnglish" class="form-control" placeholder="Mother Name in English" value="">
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>মাতার জাতীয়তা</label>
                                 <input type="text" class="form-control" id="motherNationalityBangla" name="motherNationalityBangla" placeholder="মাতার জাতীয়তা বাংলায়" value="বাংলাদেশী">
                              </div>
                           </div>
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Mother Nationality</label>
                                 <input type="text" class="form-control" id="motherNationalityEnglish" name="motherNationalityEnglish" placeholder=">Mother Nationality in English" value="Bangladeshi">
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>জন্মস্থান </label>
                                 <input type="text" class="form-control" id="birthplaceBangla" name="birthplaceBangla" placeholder="জন্মস্থান বাংলায়" value="">
                              </div>
                           </div>
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Place of Birth</label>
                                 <input type="text" class="form-control" id="birthplaceEnglish" name="birthplaceEnglish" placeholder="Place of Birth in English" value="">
                              </div>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>স্থায়ী ঠিকানা </label>
                                 <textarea id="permanentAddressBangla" name="permanentAddressBangla" rows="4" class="form-control" placeholder="স্থায়ী ঠিকানা বাংলায়"></textarea>
                              </div>
                           </div>
                           <div class="col-md-6 mb-3">
                              <div class="form-group">
                                 <label>Permanent Address</label>
                                 <textarea id="permanentAddressEnglish" name="permanentAddressEnglish" rows="4" class="form-control" placeholder="Permanent Address in English"></textarea>
                              </div>
                           </div>
                        </div>
                        <div class="alert alert-info" role="alert">
                              আপনার একাউন্ট থেকে 
                              <?php echo $make_birth; ?> 
                              টাকা কাটা হবে।
                           </div>
                        <button type="submit" class="btn btn-primary text-center d-block mx-auto">ডাউনলোড করুন</button>
                     </form>
                           <script>
                                 const userBalance = <?php echo $diff; ?>;
                                 const requiredBalance = <?php echo $make_birth; ?>;

                                 const checkboxes = document.getElementsByName('barCode');
                                 document.getElementById('BrnBN').addEventListener('submit', function (e) {
                                    e.preventDefault(); 
                                    let isChecked = false;
                                    for (let i = 0; i < checkboxes.length; i++) {
                                       if (checkboxes[i].checked) {
                                          isChecked = true;
                                          break;
                                       }
                                    }

                                    if (!isChecked) {
                                       alert('Select a Left Bar Code or QR Link');
                                       $error_message = "Select a Left Bar Code or QR Link";
                                    } else if (userBalance < requiredBalance) {
                                       alert("You don't have enough balance");
                                       $error_message = "You don't have enough balance";
                                    } else {
                                       this.submit();
                                    }
                                 });

                              function handleBarcodeInputChange() {
                                 var barcodeInputCheckbox = document.getElementById('barCodeInput');
                                 var barcodeInputField = document.getElementById('barcodeInputField');
                                 if (barcodeInputCheckbox.checked) {
                                    barcodeInputField.value = "";
                                    barcodeInputField.readOnly = false;
                                    uncheckBarcodeCheckboxes(barcodeInputCheckbox);
                                 } else {
                                    barcodeInputField.value = "";
                                    barcodeInputField.readOnly = true;
                                 }
                              }

                              function handleBarcodeCheckChange(checkbox) {
                                 if (checkbox.checked) {
                                    document.getElementById('barCodeInput').checked = false;
                                    document.getElementById('barcodeInputField').value = checkbox.value;
                                    document.getElementById('barcodeInputField').readOnly = true;
                                    uncheckOtherBarcodeCheckboxes(checkbox);
                                 }
                              }

                              function uncheckBarcodeCheckboxes(checkboxToKeep) {
                                 var checkboxes = document.getElementsByName('barCode');
                                 checkboxes.forEach(function(checkbox) {
                                    if (checkbox !== checkboxToKeep) {
                                       checkbox.checked = false;
                                    }
                                 });
                              }

                              function uncheckOtherBarcodeCheckboxes(checkboxToKeep) {
                                 var checkboxes = document.getElementsByName('barCode');
                                 checkboxes.forEach(function(checkbox) {
                                    if (checkbox !== checkboxToKeep && checkbox.checked) {
                                       checkbox.checked = false;
                                    }
                                 });
                              }

                              function handleQrLinkInputChange() {
                                 var qrLinkInputCheckbox = document.getElementById('qrLinkInput');
                                 var qrLinkInputField = document.getElementById('qrLinkInputField');
                                 if (qrLinkInputCheckbox.checked) {
                                    qrLinkInputField.value = "";
                                    qrLinkInputField.readOnly = false;
                                    uncheckQrLinkCheckboxes(qrLinkInputCheckbox);
                                 } else {
                                    qrLinkInputField.value = "";
                                    qrLinkInputField.readOnly = true;
                                 }
                              }

                              function handleQrLinkCheckChange(checkbox) {
                                 if (checkbox.checked) {
                                    document.getElementById('qrLinkInput').checked = false;
                                    document.getElementById('qrLinkInputField').value = checkbox.value;
                                    document.getElementById('qrLinkInputField').readOnly = true;
                                    uncheckOtherQrLinkCheckboxes(checkbox);
                                 }
                              }

                              function uncheckQrLinkCheckboxes(checkboxToKeep) {
                                 var checkboxes = document.getElementsByName('qrLink');
                                 checkboxes.forEach(function(checkbox) {
                                    if (checkbox !== checkboxToKeep) {
                                       checkbox.checked = false;
                                    }
                                 });
                              }

                              function uncheckOtherQrLinkCheckboxes(checkboxToKeep) {
                                 var checkboxes = document.getElementsByName('qrLink');
                                 checkboxes.forEach(function(checkbox) {
                                    if (checkbox !== checkboxToKeep && checkbox.checked) {
                                       checkbox.checked = false;
                                    }
                                 });
                              }

                              function autoSelect(value) {
                                 // Check the corresponding barcode checkbox
                                 var barcodeCheckbox = document.getElementById('barCode' + value);
                                 if (barcodeCheckbox) {
                                    barcodeCheckbox.checked = true;
                                    handleBarcodeCheckChange(barcodeCheckbox);
                                 }
                                 
                                 var qrLinkCheckbox = document.getElementById('qrLink' + value);
                                 if (qrLinkCheckbox) {
                                    qrLinkCheckbox.checked = true;
                                    handleQrLinkCheckChange(qrLinkCheckbox);
                                 }
                              }	
                           </script>
                     </div>
                  </div>
               </div>
         </section>
      </main>
      <?php include('includes/footer.php'); ?>


