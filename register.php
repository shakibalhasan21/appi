<?php
// include Function  file
include_once('function.php');
// Object creation
$userdata = new DB_con();

  $data = $userdata->get_control();
	while ($row = mysqli_fetch_array($data)) {
		$recharge_msg = $row['rg_msg'];
		$notice =  $row['notice'];
		$approval = $row['approval'];
		$login =  $row['login'];
		$register = $row['register'];
		$bot_token =  $row['bot_token'];
		$log_channel = $row['log_channel'];
		$charge =  $row['charge'];
	}
	
	if($register == '0'){
	  include "maintenance.html";
      die();
	}

  if (isset($_POST['submit'])) {
    // Posted Values
    $fname = trim($_POST['fullname']);
    $uname = trim($_POST['username']);
    $whatsapp = $_POST['whatsapp'];
    $uemail = trim($_POST['email']);
    $pasword = $_POST['password'];
    $type = $_POST['account_type'];

    // Validation
    $errors = [];

    // Check if full name is empty
    if (empty($fname)) {
        $errors[] = "Full name is required.";
    }

    // Check if username is empty
    if (empty($whatsapp)) {
        $errors[] = "WhatsApp Number is required.";
    } elseif (strlen($whatsapp) < 10) {
      $errors[] = "Input Valid WhatsApp Number.";
  }   
    
    // Check if username is empty
    if (empty($uname)) {
        $errors[] = "Username is required.";
    }

    // Validate email
    if (empty($uemail)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Validate password
    if (empty($pasword)) {
        $errors[] = "Password is required.";
    } elseif (strlen($pasword) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    }

    // Display errors or proceed with registration
    if (!empty($errors)) {
        // Display error messages
        foreach ($errors as $error) {
            $error_message =  $error;
        }
    } else {
        // Hash the password
        $hashed_password = md5($pasword);

        $pass_key = $pasword;

        // Function Calling
        $sql = $userdata->registration($fname, $uname, $uemail, $whatsapp, $type, $hashed_password, $pass_key);

        if ($sql) {
          
            $success_message = "অ্যাকাউন্ট সফলভাবে তৈরি করা হয়েছে লগইন করুন";

            echo "<script>window.location.href='signin.php?reg_success=1'</script>";

        } else {
          
            $error_message = "Something went wrong. Please try again.";
        }
    }
}

?>
<?php include('includes/head2.php');
?>


<style>
  body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #ff6f61, #ff9671, #ffd371);
    /* display: flex; */
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    /* overflow: hidden; */
  }

  .container {
    width: 90%;
    /* max-width: 500px; */
    padding: 20px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
    animation: fadeInUp 0.8s ease-in-out;
  }

  .logo-container {
    text-align: center;
    margin-bottom: 20px;
  }

  .logo img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
  }

  .welcome-text {
    font-size: 1.5rem;
    color: #fff;
    font-weight: bold;
    margin-top: 10px;
    text-shadow: 0 2px 5px rgba(0, 0, 0, 0.5);
  }

  .card {
    background: rgba(255, 255, 255, 0.8);
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
  }

  .card-title {
    color: #ff6f61;
    text-align: center;
    font-weight: 600;
    margin-bottom: 10px;
  }

  .small {
    color: #ff9671;
    text-align: center;
    font-weight: 500;
    margin-bottom: 20px;
  }

  .form-label {
    font-size: 0.9rem;
    color: #555;
  }

  .input-3d {
    background: #f8f9fa;
    box-shadow: inset 2px 2px 8px rgba(0, 0, 0, 0.1),
      inset -2px -2px 8px rgba(255, 255, 255, 0.7);
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
  }

  .input-3d:focus {
    outline: none;
    border-color: #ff9671;
    box-shadow: 0 2px 10px rgba(255, 150, 113, 0.5);
  }

  .btn-gradient {
    background: linear-gradient(135deg, #ff6f61, #ff9671);
    color: #fff;
    padding: 12px;
    border: none;
    border-radius: 12px;
    font-weight: bold;
    width: 100%;
    margin-top: 10px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
  }

  .btn-gradient:hover {
    transform: translateY(-3px);
    box-shadow: 0px 5px 15px rgba(255, 150, 113, 0.4);
  }

  .btn-gradient-secondary {
    background: #fff;
    color: #555;
    font-weight: bold;
    width: 100%;
    margin-top: 10px;
    border: 1px solid #ddd;
    padding: 12px;
    border-radius: 12px;
    transition: background-color 0.3s ease, color 0.3s ease;
  }

  .btn-gradient-secondary:hover {
    background-color: #ff9671;
    color: #fff;
  }

  .membership {
    text-align: center;
    font-size: 0.9rem;
    color: #555;
    margin-top: 10px;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @media (max-width: 768px) {
    .container {
      width: 95%;
    }

    .welcome-text {
      font-size: 1.2rem;
    }
  }
</style>

<main>
  <div class="container">
    <div class="logo-container">
      <img style="height:30px; border-radius: 50%;" src="https://i.ibb.co/WPC2Qwg/photo-2023-05-10-17-14-36.jpg" alt="Logo">
      <div class="welcome-text">WELCOME</div>
    </div>
    <div class="card">
      <h3 class="card-title">Create an Account</h3>
      <p class="small">DIGITAL SERVICE POINT</p>
      <form action="" method="POST">
        <div class="mb-3">
          <label for="yourName" class="form-label">Name</label>
          <input type="text" id="yourName" name="fullname" class="form-control input-3d" placeholder="Your Name" required>
        </div>
        <div class="mb-3">
          <label for="yourEmail" class="form-label">Email</label>
          <input type="email" id="yourEmail" name="email" class="form-control input-3d" placeholder="email@gmail.com" required>
        </div>
        <div class="mb-3">
          <label for="yourWhatsapp" class="form-label">Whatsapp Number</label>
          <input type="text" id="yourWhatsapp" name="whatsapp" class="form-control input-3d" placeholder="+88017*******0" required>
        </div>
        <div class="mb-3">
          <label for="username" class="form-label">Username</label>
          <input type="text" id="username" name="username" class="form-control input-3d" placeholder="Username" required>
        </div>
        <div class="mb-3">
          <label for="acType" class="form-label">Account Type</label>
          <select id="acType" name="account_type" class="form-control input-3d" onchange="updateMembershipMessage()">
            <option value="0">Prepaid</option>
            <option value="1">Premium</option>
          </select>
          <div class="text-center mt-2">
              <span id="membership" style="font-weight: bold;">প্রিপেইড মেম্বারদের প্রত্যেক রিকোয়েস্ট এ চার্জ কাটবে</span>
          </div>
          <!-- <div class="membership">প্রিপেইড মেম্বারদের প্রত্যেক রিকোয়েস্ট এ চার্জ কাটবে</div> -->
        </div>
        <div class="mb-3">
          <label for="yourPassword" class="form-label">Password</label>
          <input type="password" id="yourPassword" name="password" class="form-control input-3d" placeholder="Password" required>
        </div>
        <button type="submit" name="submit" class="btn-gradient">নতুন একাউন্ট খুলুন</button>
        <a href="signin.php">
          <button type="button" class="btn-gradient-secondary">লগইন</button>
        </a>
      </form>
    </div>
  </div>
</main>


<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- <script src="assets/vendor/chart.js/chart.min.js"></script> -->
<!-- <script src="assets/vendor/echarts/echarts.min.js"></script> -->
<!-- <script src="assets/vendor/quill/quill.min.js"></script> -->
<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<!-- <script src="assets/vendor/tinymce/tinymce.min.js"></script> -->
<script src="assets/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
<script src="assets/js/main.js"></script>

<script src="assets/jquery-1.11.1.min.js"></script>
<script src="assets/bootstrap.min.js"></script>

<script>
  function checkusername(va) {
    $.ajax({
      type: "POST",
      url: "check_availability.php",
      data: 'username=' + va,
      success: function(data) {
        $("#usernameavailblty").html(data);
      }
    });
  }

</script>

<script>
function updateMembershipMessage() {
    // Get the selected value
    const selectElement = document.getElementById('acType');
    const selectedValue = selectElement.value;

    // Get the span element
    const membershipSpan = document.getElementById('membership');

    // Update the span content and style based on the selected value
    if (selectedValue === '0') {
        membershipSpan.textContent = 'প্রিপেইড মেম্বারদের প্রত্যেক রিকোয়েস্ট এ চার্জ কাটবে';
        membershipSpan.style.color = 'green'; // Default color
    } else if (selectedValue === '1') {
        membershipSpan.innerHTML = `
            মাসিক প্রিমিয়াম মেম্বারশিপ নিতে এডমিন এর সাথে যোগাযোগ করুন
            <a href="https://wa.me/8801935350668" target="_blank" style="color: white; background-color: green; padding: 5px 10px; border-radius: 5px; text-decoration: none;">
                WhatsApp
            </a>
        `;
        membershipSpan.style.color = 'green'; // Green for Premium
    }
}

</script>


    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script>
        var errorMessage = "<?php echo addslashes($error_message); ?>";
        if (errorMessage) {
            toastr.error(errorMessage);
        }

		var successMessage = "<?php echo addslashes($success_message); ?>";
        if (successMessage) {
            toastr.success(successMessage);
        }
    </script>

</body>

</html> 