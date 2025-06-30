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


  
  if (isset($_POST['submit'])) {

    $name = $_POST['api_name'];
    $url = $_POST['api_url'];
    $key = $_POST['api_key'];

    $result = $obj->insert_apis($name, $url, $key);
    if ($result) {
      // echo "<script>alert('Deposit successfull.');</script>";
      $success_message = "Api Successfully Added";
      
    } else {
      // echo "something went wrong ";
      $error_message = "Something went wrong";
    }
  }

  ?>
  <?php include('includes/head.php');
  ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Add New Api</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active"><a href="apis.php">Apis</a></li>
          
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
          <div class="col-lg-12">
              <!-- <div class="card">
                  <div class="card-body"> -->
                      <!-- <h5 class="card-title text-center">Add New API</h5> -->

                      <div class="container">
                          <div class="row justify-content-center">
                              
                              <div class="col-sm-12">
                                <div class="card">
                                  <div class="card-body">
                                    <h5 class="card-title text-center">Create New API</h5>

                                    <!-- Vertical Form -->
                                    <form class="row g-3" action="" method="post">
                                      <div class="col-12">
                                        <label for="inputApiName" class="form-label">Api Name</label>
                                        <input type="text" class="form-control" id="inputApiName" name="api_name" placeholder="Input Api Name">
                                      </div>
                                      <div class="col-12">
                                        <label for="inputApiUrl" class="form-label">Input Api Url</label>
                                        <input type="text" class="form-control" id="inputApiUrl" placeholder="Input Api Url" name="api_url">
                                      </div>
                                      <div class="col-12">
                                        <label for="inputApiKey" class="form-label">Input Api Key</label>
                                        <input type="text" class="form-control" id="inputApiKey" name="api_key" placeholder="Input Api Key">
                                      </div>
                                      <div class="text-center">
                                        <button type="reset" class="btn btn-secondary">Reset</button>
                                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                      </div>
                                    </form><!-- Vertical Form -->

                                  </div>
                                </div>

                              </div>
                          </div>
                      </div>
                  <!-- </div>
              </div> -->
          </div>
      </div>
  </section>

  </main>

  <?php include('includes/footer.php');
  ?>
<?php } ?>