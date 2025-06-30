<?php
session_start();
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


  $id = $_GET['id'];
  if (isset($_POST['submit'])) {
    if (!empty($_POST['deposit'])) {
      $deposit = $_POST['deposit'];
      $result = $obj->insert_deposit($deposit, $id);
      if ($result) {
        $success_message = "Deposit Successful";
      } else {
        $error_message = "Something went wrong.";
      }
    } elseif (!empty($_POST['decrement'])) {
      $decrement = $_POST['decrement'];
      $result = $obj->decrement_balance($decrement, $id);
      if ($result) {
        $success_message = "Decrement Successful";
      } else {
        $error_message = "Something went wrong.";
      }
    } elseif (!empty($_POST['premium'])) {
      $premium = $_POST['premium'];
      $result = $obj->premium_update($premium, $id);
      if ($result) {
        $success_message = "Membership Update Successfully";
      } else {
        $error_message = "Something went wrong.";
      }
    }
  }

  ?>
  <?php include('includes/head.php');
  ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Users List</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Balance Deposit</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-12">
             
              <div class="card mb-3 text-center">
                <h5 style="border:1px solid #05C3FB; padding:5px;margin:10px;border-radius:3px;">Deposit List</h5>    
              </div>
              <table class="table table-bordered">
                <?php
                if ($user_id == 1) { ?>
                  <div class="container mt-3 card px-3 py-5">
                    <!-- Add Balance Form -->
                    <form action="" method="post" class="mb-4">
                      <div class="row justify-content-center">
                        <div class="col-md-6 col-sm-12">
                          <h4 class="text-center">Add Balance</h4>
                          <div class="mb-3">
                            <label for="deposit" class="form-label">Deposit Balance:</label>
                            <input type="number" class="form-control" name="deposit" id="deposit" required>
                          </div>
                          <div class="text-center">
                            <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                          </div>
                        </div>
                      </div>
                    </form>

                    <!-- Decrement Balance Form -->
                    <form action="" method="post">
                      <div class="row justify-content-center mt-4">
                        <div class="col-md-6 col-sm-12">
                          <h4 class="text-center">Decrement Balance</h4>
                          <div class="mb-3">
                            <label for="decrement" class="form-label">Decrement Balance:</label>
                            <input type="number" class="form-control" name="decrement" id="decrement" required>
                          </div>
                          <div class="text-center">
                            <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                          </div>
                        </div>
                      </div>
                    </form>
                     <!-- Decrement Balance Form -->
                     <form action="" method="post">
                      <div class="row justify-content-center mt-4">
                        <div class="col-md-6 col-sm-12">
                          <h4 class="text-center">Premium Membership</h4>
                          <div class="mb-3">
                            <input type="number" class="form-control" name="premium" id="premium" value="" required>
                          </div>
                          <div class="text-center">
                            <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>


                <?php
                } else { ?>


                <?php  }

                ?>

              </table>


              <div class="container mt-4 card px-3 py-5">
                <div class="row">
                  <div class="col-md-12 col-sm-12">
                  <h4 class="text-center">Deposit History</h4>
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th> ID </th>
                          <th> User ID </th>
                          <th> Username</th>
                          <th> Deposit</th>
                          <th class="text-center"> Date</th>

                        </tr>
                      </thead>
                      <tbody>
                        <?php
                        $sql = $obj->get_deposit($id);
                        $cnt = 1;
                        while ($row = mysqli_fetch_array($sql)) {
                        ?>
                          <tr>
                            <td> <?php echo $row['id']; ?> </td>
                            <td><?php echo $row['user_id']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td ><?php echo $row['deposit']; ?></td>
                            <td class="text-center"><?php echo $row['date']; ?></td>

                          </tr>
                        <?php
                          $cnt = $cnt + 1;
                        } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
        </div>
      </div>
    </section>
  </main>

  <?php include('includes/footer.php');
  ?>
<?php } ?>