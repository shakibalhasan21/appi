<?php
session_start();
if(!isset($_SESSION['uid'])) {
header('location:logout.php');
}else{
$id = $_SESSION['uid'];
$username = $_SESSION['username'];
}

include 'function.php';

define('BKS_URL', 'https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized');
define('BKS_USER', '01815700085');
define('BKS_PASS', '?*oxXo{:gq3');
define('BKS_KEY', '2GaQkl1LbRlwY7qPlSn5IzMwtc');
define('BKS_SEC', 'aqEizirw92pyF5O1yi0u088eqbgS482KuTAyhz1T2RC5pJxgYRlq');

$conn = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
if(!$conn){
header("Location:recharge-msg.php?msg=Database connection lost");
}else{
$fetchdata = new DB_con();
$data = $fetchdata->get_control();
$row = mysqli_fetch_array($data);
$recharge_msg = $row['rg_msg'];
$approval = $row['robi_token'];
}

function getProtocolServer() {
if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS']
== 1)) ||
(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']
== 'https')) {
$protocol = 'https://';
} else {
$protocol = 'http://';
}

$server = $_SERVER['SERVER_NAME'];
return $protocol . $server;
}

function getAuthToken(){
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, BKS_URL.'/checkout/token/grant');
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(['app_key' =>
BKS_KEY,'app_secret' => BKS_SEC]));
curl_setopt($curl, CURLOPT_RETURNTRANSFER , 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_HTTPHEADER,
['Content-Type:application/json','username:'. BKS_USER,'password:'. BKS_PASS]);
$content = curl_exec($curl);
curl_close($curl);
$response = json_decode($content, true);
if($response['statusCode'] == "0000"){
$_SESSION['id_token'] = $response['id_token'];
return $response['id_token'];
}else{
return null;
}
}

function createPaymentLink($ammount){
$domain = getProtocolServer();
$authToken = getAuthToken();
if($authToken != null){
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, BKS_URL.'/checkout/create');
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
'mode' => '0011',
'amount' => $ammount,
'payerReference' => "Recharge",
'callbackURL' => "http://localhost/SERVER_COPY" ."/recharge.php",
'currency' => 'BDT',
'intent' => 'sale',
'merchantInvoiceNumber' => 'Inv'.rand()
]));
curl_setopt($curl, CURLOPT_RETURNTRANSFER , 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_HTTPHEADER,
['Content-Type:application/json','Authorization:'. $authToken,'X-APP-Key:'.
BKS_KEY]);
$content = curl_exec($curl);
curl_close($curl);
$response = json_decode($content, true);
if($response['statusCode'] == "0000"){
return $response['bkashURL'];
}else{
return null;
}
}else{
return null;
}
}

function getPaymentDetils($paymentID){
$authToken = getAuthToken();
if(isset($_SESSION['id_token'])){
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, BKS_URL.'/checkout/execute');
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(['paymentID' =>
$paymentID]));
curl_setopt($curl, CURLOPT_RETURNTRANSFER , 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_HTTPHEADER,
['Content-Type:application/json','Authorization:'.$_SESSION['id_token'],'X-APP-Key:'.
BKS_KEY]);
$content = curl_exec($curl);
curl_close($curl);
$response = json_decode($content, true);
if($response['statusCode'] == "0000"){
$result = [];
$result['success'] = true;
$result['payerReference'] = $response['payerReference'];
$result['customerMsisdn'] = $response['customerMsisdn'];
$result['trxID'] = $response['trxID'];
$result['amount'] = $response['amount'];
$result['merchantInvoiceNumber'] = $response['merchantInvoiceNumber'];
$result['paymentExecuteTime'] = $response['paymentExecuteTime'];
return $result;
}else{
$result = [];
$result['success'] = false;
$result['statusMessage'] = $response['statusMessage'];
return $result;
}
}else{
return null;
}
}

function checkNewPaymentID($paymentID,$trxID,$amount){
global $conn;
$checkPaymentList = mysqli_query($conn,
"SELECT * FROM bkash_pay WHERE paymentID = '$paymentID', trxID = '$trxID', amount = '$amount'");
$totalPaymentList = mysqli_num_rows($checkPaymentList);
if($totalPaymentList > 0){
return false;
}else{
return true;
}
}

function savePaymentID($paymentID, $paymentDetils) {
global $conn, $id, $username;

$payerReference = $paymentDetils['payerReference'];
$customerMsisdn = $paymentDetils['customerMsisdn'];
$trxID = $paymentDetils['trxID'];
$amount = $paymentDetils['amount'];
$merchantInvoiceNumber = $paymentDetils['merchantInvoiceNumber'];
$paymentExecuteTime = $paymentDetils['paymentExecuteTime'];

$query = "INSERT INTO bkash_pay (user_id, username, paymentID, payerReference,
customerMsisdn, trxID, amount, merchantInvoiceNumber, paymentExecuteTime)
VALUES ('$id', '$username', '$paymentID', '$payerReference', '$customerMsisdn',
'$trxID', '$amount', '$merchantInvoiceNumber', '$paymentExecuteTime')";

$result = mysqli_query($conn, $query);

if (!$result) {
// Log the SQL error for debugging
error_log("Error inserting payment: " . mysqli_error($conn));
return false;
}

return true;
}

function addBalance($amount, $paymentDetils = null) {
global $conn, $id, $username;

$number = $paymentDetils['customerMsisdn'];
$txn_id = $paymentDetils['trxID'];
// Insert into tbl_balance
$balanceQuery = "INSERT INTO tbl_balance (user_id, username, deposit, withdraw)
VALUES ('$id', '$username', '$amount', '0')";
$balanceResult = mysqli_query($conn, $balanceQuery);

if (!$balanceResult) {
// Log the SQL error for debugging
error_log("Error adding balance in tbl_balance: " . mysqli_error($conn));
return false;
}

// Insert into tbl_request
$date = date('Y-m-d H:i:s'); // Current date and time
$requestQuery = "INSERT INTO tbl_request (user_id, username, deposit, number,
txn_id, withdraw)
VALUES ('$id', '$username', '$amount', '$number', '$txn_id', '0')";
$requestResult = mysqli_query($conn, $requestQuery);

if (!$requestResult) {
// Log the SQL error for debugging
error_log("Error adding request in tbl_request: " . mysqli_error($conn));
return false;
}

return true;
}

if(isset($_POST['amount'])){
$amount = $_POST['amount'];
if($amount >= $approval){
$bkashURL = createPaymentLink($amount);
if($bkashURL != null){
header("Location: ".$bkashURL);
}else{
header("Location:recharge-msg.php?msg=Something went wrong");
}
}else{
echo '<script>alert("Minimum recharge limit '.$approval.' tk");</script>';
showHtml();
}
}else if(isset($_GET['status'],$_GET['paymentID'])){
$status = $_GET['status'];
$paymentID = $_GET['paymentID'];
if($status == 'success'){
$paymentDetils = getPaymentDetils($paymentID);
if($paymentDetils != null){
if($paymentDetils['success']){
$savePaymentID = savePaymentID($paymentID,$paymentDetils);
if($savePaymentID){
$addBalance = addBalance($paymentDetils['amount'], $paymentDetils);
if($addBalance){
header("Location:recharge-msg.php?trxid=".$paymentDetils['trxID']);
}else{
header("Location:recharge-msg.php?msg=Failed to add balance");
}
}else{
header("Location:recharge-msg.php?msg=Failed to save payment");
}
}else{
header("Location:recharge-msg.php?msg=".$paymentDetils['statusMessage']);
}
}else{
header("Location:recharge-msg.php?msg=AuthToken not found");
}
}else if($status == 'cancel'){
header("Location:recharge-msg.php?cancel=Payment cancelled by user");
}else{
header("Location:recharge-msg.php?msg=Something went wrong");
}
}else{
showHtml();
}

function showHtml(){
global $fetchdata,$id,$username;
$obj = $fetchdata; $user_id = $id;
$data = $fetchdata->get_control();
$row = mysqli_fetch_array($data);
$recharge_msg = $row['rg_msg'];
// $approval = $row['approval'];
include('includes/head.php');
?>
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
    crossorigin="anonymous">
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Recharge</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a
                        href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active">Recharge</li>
            </ol>
        </nav>
    </div>
    <section class="section">
    <div class="card mb-4">
    <marquee style="border:1px solid #05C3FB; padding:5px;margin:10px;border-radius:3px;">যত খুশি নিজের মন মতো রিচার্জ করতে পারবেন</marquee>    </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card px-3 py-5">
                    <!-- <div class="card-body"> -->
                        <h5
                            class="card-title text-center card-title col-sm-10 col-md-8 mx-auto align-center text-center"><?php
                            echo $recharge_msg; ?></h5>
                        <table class="table table-bordered">
                            <!-- <form action method="post">
                                
                                <div class=" col-sm-8 mx-auto">
                                    <h3 style="text-align:center"> রির্চাজ করুন </h3>


                                    <div class="input-group col-xs-12">
                                        <input type="number" id="amount" name="amount" class="form-control file-upload-info" min="<?php echo $robi_token; ?>" placeholder="সর্ব নিম্ন রিচার্জ <?php echo $robi_token; ?> টাকা">
                                        <span class="input-group-append">
                                        <button type="submit" class="btn btn-success" name="submit">Recharge</button>
                                        </span>
                                    </div>
                                            
                                </div>
                            </form> -->

                             <div class="col-sm-8 mx-auto mt-5">
                                <h3 style="text-align:center">রিচার্জ করুন</h3>

                                <div class="input-group">
                                    <input type="number" id="amount" name="amount" class="form-control file-upload-info"
                                    min="<?php echo $robi_token; ?>" placeholder="সর্ব নিম্ন রিচার্জ <?php echo $robi_token; ?> টাকা">
                                    <button type="button" class="btn btn-success" onclick="openRechargeModal()">Recharge</button>
                                </div>
                            </div>
                                            
                        </table>
                    <!-- </div> -->
                </div>
                <div class="card px-3 py-3">
                    <div class="col-sm-12 mx-auto">
                    <div class="card mb-3 text-center">
                        <h5 style="border:1px solid #05C3FB; padding:5px;margin:10px;border-radius:3px;">ব্যালান্স যোগ করুন </h5>    
                    </div>
                    <div class="row">
                        <!-- <h5 class="card-title">Add balance</h5> -->
                        <ol class="list-group list-group-numbered">
                            <li
                                class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">100 BDT</div>
                                </div>
                                <form action method="POST">
                                    <input class="fw-bold" type="text"
                                        id="amount" name="amount" value="100"
                                        hidden>
                                    <button type="submit"
                                        class="btn btn-primary roounded-pill">Recharge</button>
                                </form>
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">200 BDT</div>
                                </div>
                                <form action method="POST">
                                    <input class="fw-bold" type="text"
                                        id="amount" name="amount" value="200"
                                        hidden>
                                    <button type="submit"
                                        class="btn btn-primary roounded-pill">Recharge</button>
                                </form>
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">300 BDT</div>
                                </div>
                                <form action method="POST">
                                    <input class="fw-bold" type="text"
                                        id="amount" name="amount" value="300"
                                        hidden>
                                    <button type="submit"
                                        class="btn btn-primary roounded-pill">Recharge</button>
                                </form>
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">500 BDT</div>
                                </div>
                                <form action method="POST">
                                    <input class="fw-bold" type="text"
                                        id="amount" name="amount" value="500"
                                        hidden>
                                    <button type="submit"
                                        class="btn btn-primary roounded-pill">Recharge</button>
                                </form>
                            </li>
                            <li
                                class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">1000 BDT</div>
                                </div>
                                <form action method="POST">
                                    <input class="fw-bold" type="text"
                                        id="amount" name="amount" value="1000"
                                        hidden>
                                    <button type="submit"
                                        class="btn btn-primary roounded-pill">Recharge</button>
                                </form>
                            </li>

                            <li
                                class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">2000 BDT</div>
                                </div>
                                <form action method="POST">
                                    <input class="fw-bold" type="text"
                                        id="amount" name="amount" value="2000"
                                        hidden>
                                    <button type="submit"
                                        class="btn btn-primary roounded-pill">Recharge</button>

                                </form>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="card px-3 py-5">
                <h3 class="text-center mt-4">আপনার সকল রিচার্জ হিস্টোরি</h3>
                <div class="table-responsive mt-4">
                <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                    <tr>
                        <th>SL</th>
                        <th>স্ট্যাটাস</th>
                        <th>পেমেন্ট আইডি</th>
                        <th>টাকার পরিমান</th>
                        <th>রিচার্জের সময়</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sql = $obj->get_deposit($id);
                    $user_type = $obj->user_type($id);
                    if ($sql) {
                        $cnt = 1;
                        while ($row = mysqli_fetch_assoc($sql)) { ?>
                        <tr>
                        <td><?php echo htmlspecialchars($cnt); ?></td>
                        <td>
                            <?php echo $user_type == 0 ? '<span class="badge bg-info">Prepaid</span>' : '<span class="badge bg-success">Premium</span>'; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['deposit']); ?></td>
                        <td>
                            <?php 
                                $datetime = new DateTime($order['date']); // Create a DateTime object
                                echo $datetime->format('d M, h:i A');
                            ?>
                            <!-- <?php echo htmlspecialchars($row['date']); ?> -->
                        </td>
                        </tr>
                        <?php $cnt++; }
                    } else {
                        echo "<tr><td colspan='5'>No records found</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
                </div>
        </div>
    </section>



    <!-- Recharge Modal -->
<!-- Recharge Modal -->
<div class="modal fade" id="rechargeModal" tabindex="-1" aria-labelledby="rechargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow border-0 rounded-4">
      
      <div class="modal-header bg-danger text-white rounded-top">
        <h5 class="modal-title mx-auto fw-bold" id="rechargeModalLabel">🔔 রিচার্জ নির্দেশনা</h5>
      </div>

      <div class="modal-body text-center p-4">
        <p class="text-danger fw-semibold mb-2" style="font-size: 16px;">
          ⚠️ অটোমেটিক রিচার্জ বন্ধ আছে
        </p>
        <p class="text-secondary mb-3" style="font-size: 15px;">
          অনুগ্রহ করে ম্যানুয়াল রিচার্জ করুন। নিচের বিকাশ নাম্বারে সেন্ডমানি করে আপনার Username ও Txn ID পাঠান:
        </p>

        <p class="bg-light border rounded py-2 px-3 fw-bold text-dark mb-4" style="font-size: 17px;">
          📱 bKash SendMoney Only: <span class="text-danger">01935350668</span>
        </p>

        <form id="rechargeForm" class="text-start">
          <div class="mb-3">
            <label for="username" class="form-label fw-medium">👤 Username</label>
            <input type="text" id="username" class="form-control" placeholder="আপনার ইউজারনেম দিন" required>
          </div>
          <div class="mb-3">
            <label for="rechargeAmount" class="form-label fw-medium">💰 Recharge Amount</label>
            <input type="number" id="rechargeAmount" class="form-control" placeholder="টাকার পরিমাণ লিখুন" required>
          </div>
          <div class="mb-3">
            <label for="txnId" class="form-label fw-medium">🧾 Txn ID / Bkash Number</label>
            <input type="text" id="txnId" class="form-control" placeholder="ট্রানজেকশন আইডি বা বিকাশ নাম্বার" required>
          </div>
        </form>
      </div>

      <div class="modal-footer justify-content-center pb-4">
        <a id="whatsappSend" href="#" target="_blank" class="btn btn-success px-4 py-2 fw-bold">
          📤 রিচার্জের রিকুয়েস্ট পাঠান
        </a>
      </div>
    </div>
  </div>
</div>


<!-- JavaScript -->
<script>
function openRechargeModal() {
  var modal = new bootstrap.Modal(document.getElementById('rechargeModal'));
  modal.show();
}

document.getElementById('whatsappSend').addEventListener('click', function (e) {
  const username = document.getElementById('username').value;
  const amount = document.getElementById('rechargeAmount').value;
  const txnId = document.getElementById('txnId').value;

  if (!username || !amount || !txnId) {
    alert("সব ফিল্ড পূরণ করুন।");
    e.preventDefault();
    return;
  }

  const message = `ম্যানুয়াল রিচার্জের অনুরোধ:\n\nUsername: ${username}\nAmount: ${amount} টাকা\nTxn ID / Bkash Number: ${txnId}`;
  const phone = "8801935350668"; // Replace with your number
  const whatsappURL = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;

  this.href = whatsappURL;
});

</script>



</main>
<?php include('includes/footer.php'); } ?>