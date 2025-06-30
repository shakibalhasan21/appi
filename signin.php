<?php
session_start();
error_reporting(0);

if (isset($_COOKIE['creation'])) {
  $_SESSION['uid'] = base64_decode($_COOKIE['creation']);
}

if (isset($_SESSION['uid'])) {
  header('location:dashboard.php');
}


include_once('function.php');
$usercredentials = new DB_con();
$sql = $usercredentials->get_control();
while ($row = mysqli_fetch_array($sql)) {
  $recharge_msg = $row['rg_msg'];
  $notice =  $row['notice'];
  $approval = $row['approval'];
  $login =  $row['login'];
  $register = $row['register'];
  $bot_token =  $row['bot_token'];
  $log_channel = $row['log_channel'];
  $charge =  $row['charge'];
}

// if (isset($_POST['signin'])) {

//   if ($login == 1) {
//     $uname = $_POST['username'];
//   }

//   $password = md5($_POST['password']);
//   $ret = $usercredentials->signin($uname, $password);
//   if (count($ret) > 0) {
//     $num = $ret[0];
//     $_SESSION['uid'] = $num[0];
//     $_SESSION['fname'] = $num[1];
//     $_SESSION['username'] = $uname;
//     setcookie('creation', base64_encode($num['id']), time() + 60 * 60 * 24 * 30);
//     echo "<script>window.location.href='dashboard.php'</script>";
//   } else {
//     // Message for unsuccessfull login
//     echo "<script>alert('Invalid details. Please try again');</script>";
//     echo "<script>window.location.href='signin.php'</script>";
//   }
// }


if (isset($_POST['signin'])) {
  // Get username and password from POST
  $uname = $_POST['username'];
  $password = md5($_POST['password']);
  
  
  $ret = $usercredentials->signin($uname, $password);
  
  if (count($ret) > 0) {
      $num = $ret[0];
      $userId = $num[0]; // Assuming the user ID is in the first column
      
      if ($login == 1 || ($login == 0 && $userId == 1)) {
          // Allow login
          $_SESSION['uid'] = $userId;
          $_SESSION['fname'] = $num[1];
          $_SESSION['username'] = $uname;
          
          echo "<script>window.location.href='dashboard.php'</script>";
      } else {
          // Restrict access for non-admin users when $login == 0
          echo "<script>alert('Access restricted. Please try again later.');</script>";
          echo "<script>window.location.href='signin.php'</script>";
      }
  } else {
      // Message for unsuccessful login
      echo "<script>alert('Invalid details. Please try again');</script>";
      echo "<script>window.location.href='signin.php'</script>";
  }
}

include('includes/head2.php');
?>

<style>
        body {
            margin: 0;
            background: linear-gradient(135deg, #ff6f61, #ff9671, #ffd371);
            font-family: 'bangla', sans-serif;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .bg-gradient {
            background: linear-gradient(135deg, #ff6f61, #ff9671, #ffd371);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            padding: 20px 25px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.15);
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .logo img {
            border-radius: 10%;
            transition: transform 0.3s ease;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.3);
        }

        .logo img:hover {
            transform: scale(1.1);
        }

        h3 {
            font-weight: bold;
            color: #ffffff;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }

        p {
            text-align: center;
            color: #f7f7f7;
            font-size: 14px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
            transition: border 0.3s;
        }

        .input-group input:focus {
            border: 1px solid #ff9671;
            outline: none;
        }

        .btn-gradient {
            display: inline-block;
            width: 100%;
            padding: 12px 15px;
            background: linear-gradient(135deg, #ff6f61, #ff9671);
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            border: none;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .btn-gradient:hover {
            background: linear-gradient(135deg, #ff9671, #ff6f61);
            transform: translateY(-3px);
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.3);
        }

        .small {
            color: #ff6f61;
            font-size: 12px;
        }

        .small:hover {
            text-decoration: underline;
        }

        .alert-danger {
            text-align: center;
            background-color: #ff6f61;
            color: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            font-weight: bold;
            margin-top: 15px;
        }
</style>

<main>
    <div class="container">
        <section class="bg-gradient">
            <div class="logo text-center">
                <img src="images/nc.png" alt="Logo">
            </div>
            <h3 class="mt-3">Digital Service Point</h3>
            <b style="color: #333; text-align: center; display: block;">নাগরিক সেবা</b>

            <div class="card">
                <?php if ($login == 1) { ?>
                    <h5 class="text-center" style="color: #333;">Login to Your Account</h5>
                    <form action="" method="POST" novalidate>
                        <div class="input-group">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="username" placeholder="Enter your username" required>
                        </div>
                        <div class="input-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        <div class="text-end mb-3">
                            <a href="forgot.php" class="small">Forgot Password?</a>
                        </div>
                        <button type="submit" name="signin" class="btn-gradient">লগইন</button>
                    </form>
                <?php } else { ?>
                    <div class="alert alert-danger" role="alert">
                        User login is turned off by admin. Please try again later.
                    </div>

                    <form action="" method="POST" novalidate>
                        <div class="input-group">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" id="username" name="username" placeholder="Enter your username" required>
                        </div>
                        <div class="input-group">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        <div class="text-end mb-3">
                            <a href="forgot.php" class="small">Forgot Password?</a>
                        </div>
                        <button type="submit" name="signin" class="btn-gradient">লগইন</button>
                    </form>
                <?php } ?>
            </div>

            <div class="text-center mt-4">
                <a href="register.php" class="btn-gradient" style="background: #4CAF50;">নতুন একাউন্ট খুলুন</a>
            </div>
        </section>
    </div>
</main>

<?php include('includes/footer.php');
?>