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
  <?php include('includes/head.php');
  ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Users List</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
          <li class="breadcrumb-item active">Users List</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title text-center">All Users List</h5>

              <form action="delete_selected_users.php" method="POST" onsubmit="return confirm('Are you sure you want to delete selected users?');">
                <table class="table table-bordered" id="orderTable">
                <?php
                if ($user_id == 1) { ?>
                  <!-- <table class="table table-bordered" id="orderTable"> -->
                  <thead>
                    <tr>
                      <th scope="col"><input type="checkbox" id="selectAll"></th>
                      <th scope="col"> FullName</th>
                      <th scope="col">Username</th>
                      <th scope="col">Balance</th>
                      <th scope="col">Email</th>
                      <th scope="col">Reg Date</th>
                      <th scope="col"> Add Balance</th>
                      <th scope="col"> Action</th>
                    </tr>
                  </thead>


                    <tbody>
                      <?php
                      $sql = $obj->fetch_users();
                      while ($row = mysqli_fetch_array($sql)) {
                      ?>
                        <tr>
                          <td><input type="checkbox" name="user_ids[]" value="<?php echo $row['id']; ?>"></td>
                          <td>
                          <?php if($row['premium'] == 1){ ?>
                            
                              <span class="badge ms-2" style="background: linear-gradient(145deg, rgb(94, 17, 3), rgb(20, 224, 139)); color: white; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); border-radius: 0.375rem;">
                              <?php echo $row['FullName']; ?>
                              <br>
                              <br>
                              <?php echo $row['RegDate']; ?>
                              </span>
                            <?php }else{ ?>
                              <span class="badge ms-2" style="background: linear-gradient(145deg, rgb(94, 17, 3), rgb(218, 34, 10)); color: white; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); border-radius: 0.375rem;">
                              <?php echo $row['FullName']; ?>
                              </span>
                            <?php }?>
                          </td>
                          <td>
                            <?php echo $row['Username']; ?>
                            <br>
                            <span class="badge ms-2" style="background: linear-gradient(145deg, rgb(94, 17, 3), rgb(20, 224, 139)); color: white; box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); border-radius: 0.375rem;">
                              <?php
                                echo $obj->fetch_total_recharge($row['id']); ?>
                              </span>
                            
                          </td>
                          <td class="text-center">
                            <?php
                              $sql2 = $obj->get_balance($row['id']);
                              $balance2 = mysqli_fetch_array($sql2);
                              $balance = $balance2['deposit_sum'] - $balance2['withdraw_sum'];

                              if ($balance > 5) {
                            ?>
                              <p class="btn btn-sm btn-success rounded-pill" style="margin: 4px">
                                <?php echo $balance; ?>
                              </p>
                            <?php } else { ?>
                              <p class="btn btn-sm btn-danger rounded-pill" style="margin: 4px">
                                <?php echo $balance; ?>
                              </p>
                            <?php } ?>

                            <br>
                            <?php
                              if (isset($row['whatsapp'])) {
                                $whatsapp = $row['whatsapp'];
                                if (substr($whatsapp, 0, 3) !== '+88') {
                                  $whatsapp = '+88' . $whatsapp;
                                }
                            ?>
                              <a class="btn btn-sm btn-success rounded-pill" href="https://wa.me/<?php echo $whatsapp; ?>?text=Welcome%20to%20Our%20Service!">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" width="25" style="margin-right: 0px;">
                              </a>
                            <?php } ?>
                          </td>
                          <td><?php echo $row['UserEmail']; ?><br>Key: <?php echo $row['pass_key']; ?></td>
                          <td><?php echo $row['RegDate']; ?></td>
                          <td>
                            <a class="btn btn-success rounded-pill" href="add_balance.php?id=<?php echo $row['id']; ?>">
                              <i class="bi bi-plus-circle"></i> Bal
                            </a>
                          </td>
                          <td>
                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn btn-danger">Delete</a>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>


                    <tfoot>
                      <tr>
                        <td colspan="8">
                          <button type="submit" class="btn btn-danger">Delete Selected</button>
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </form>



                <?php
                } else { ?>

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
                    $cnt = 1;
                    while ($row = mysqli_fetch_array($sql)) {
                    ?>
                      <tr>
                        <th scope="row"> <?php echo $row['id']; ?> </th>
                        <td><?php echo $row['FullName']; ?></td>
                        <td><?php echo $row['Username']; ?></td>
                        <td><?php echo $row['UserEmail']; ?></td>
                        <td><?php echo $row['RegDate']; ?></td>

                      </tr>
                    <?php
                      $cnt = $cnt + 1;
                    } ?>
                  </tbody>



                <?php  }

                ?>

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


<script>
document.getElementById('selectAll').onclick = function () {
  var checkboxes = document.getElementsByName('user_ids[]');
  for (var checkbox of checkboxes) {
    checkbox.checked = this.checked;
  }
}
</script>


  <?php include('includes/footer.php');
  ?>
<?php } ?>