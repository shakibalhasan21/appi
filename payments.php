<?php
session_start();
if ($_SESSION['uid'] != "1") {
    header('location:logout.php');
} else {

    $user_id = $_SESSION['uid'];
    include_once("function.php");
    $fetchdata = new DB_con();
    $obj = new DB_con();

?>
    <?php include('includes/head.php'); ?>

    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background-color: #007bff;
            border: 1px solid #007bff;
            color: white;
            padding: 8px 12px;
            margin: 0 5px;
            cursor: pointer;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background-color: #0056b3;
        }

        .dataTables_length select {
            width: auto;
            padding: 5px 10px;
            font-size: 14px;
        }
    </style>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Payments</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                    <li class="breadcrumb-item active">payments</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title text-center">All Payment History</h5>
                            <table class="table table-bordered" id="orderTable">
                                <?php
                                if ($user_id == 1) { ?>
                                    <thead>
                                        <tr>
                                            <th scope="col">SL</th>
                                            <th scope="col">User ID</th>
                                            <th scope="col">Username</th>
                                            <!-- <th scope="col">paymentID</th> -->
                                            <!-- <th scope="col">payerReference</th> -->
                                            <th scope="col">customerMsisdn</th>
                                            <th scope="col">trxID</th>
                                            <th scope="col">amount</th>
                                            <!-- <th scope="col">merchantInvoiceNumber</th> -->
                                            <th scope="col">paymentExecuteTime</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $sql = $obj->fetch_bkash_pay();
                                        $cnt = 1;
                                        $total = 0;
                                        $today_total = 0;
                                        $seven_day_total = 0;
                                        $monthly_total = 0;

                                        $today = date('Y-m-d');
                                        $seven_days_ago = date('Y-m-d', strtotime('-6 days')); // including today
                                        $this_month = date('Y-m');

                                        while ($row = mysqli_fetch_array($sql)) {
                                            $amount = $row['amount'];
                                            $total += $amount;

                                            $paymentExecuteTime = $row['paymentExecuteTime'];
                                            $formattedDate = "";

                                            $cleanedTime = preg_replace('/:\d{3}/', '', $paymentExecuteTime);
                                            $cleanedTime = str_replace('GMT', '', $cleanedTime);

                                            $date = DateTime::createFromFormat('Y-m-d\TH:i:s O', $cleanedTime);

                                            if ($date) {
                                                $formattedDate = $date->format('m/d/Y h:i A');
                                                $paymentDate = $date->format('Y-m-d');
                                                $paymentMonth = $date->format('Y-m');

                                                if ($paymentDate === $today) {
                                                    $today_total += $amount;
                                                }

                                                if ($paymentDate >= $seven_days_ago && $paymentDate <= $today) {
                                                    $seven_day_total += $amount;
                                                }

                                                if ($paymentMonth === $this_month) {
                                                    $monthly_total += $amount;
                                                }
                                            } else {
                                                $formattedDate = "Invalid Date";
                                            }
                                        ?>
                                            <tr>
                                            <th scope="row"><?php echo $cnt; ?></th>
                                                <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                <!-- <td><?php echo htmlspecialchars($row['paymentID']); ?></td> -->
                                                <!-- <td><?php echo htmlspecialchars($row['payerReference']); ?></td> -->
                                                <td><?php echo htmlspecialchars($row['customerMsisdn']); ?></td>
                                                <td><?php echo htmlspecialchars($row['trxID']); ?></td>
                                                <td><?php echo htmlspecialchars(number_format($amount, 2)); ?></td>
                                                <!-- <td><?php echo htmlspecialchars($row['merchantInvoiceNumber']); ?></td> -->
                                                <td><?php echo $formattedDate; ?></td>
                                            </tr>
                                        <?php
                                            $cnt++;
                                        } ?>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="5" style="text-align: right; font-weight: bold;">Total Amount</td>
                                            <td colspan="2" style="font-weight: bold;"><?php echo number_format($total, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" style="text-align: right; font-weight: bold;">Today's Amount</td>
                                            <td colspan="2" style="font-weight: bold; color: green;"><?php echo number_format($today_total, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" style="text-align: right; font-weight: bold;">Last 7 Days Amount</td>
                                            <td colspan="2" style="font-weight: bold; color: #6c63ff;"><?php echo number_format($seven_day_total, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="5" style="text-align: right; font-weight: bold;">This Month's Amount</td>
                                            <td colspan="2" style="font-weight: bold; color: #ff8c00;"><?php echo number_format($monthly_total, 2); ?></td>
                                        </tr>
                                    </tfoot>

                                <?php } else { ?>

                                    <thead>
                                        <tr>
                                            <th scope="col"> ID </th>
                                            <th scope="col"> FullName</th>
                                            <th scope="col">Username</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Reg Date</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php
                                        $sql = $obj->fetch_users();
                                        while ($row = mysqli_fetch_array($sql)) {
                                        ?>
                                            <tr>
                                                <th scope="row"><?php echo $row['id']; ?></th>
                                                <td><?php echo $row['FullName']; ?></td>
                                                <td><?php echo $row['Username']; ?></td>
                                                <td><?php echo $row['UserEmail']; ?></td>
                                                <td><?php echo $row['RegDate']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>

                                <?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        $(document).ready(function () {
            $('#orderTable').DataTable({
                responsive: true,
            });
        });
    </script>

    <?php include('includes/footer.php'); ?>
<?php } ?>
