<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('function.php');

if(!isset($_SESSION['uid'])){
	header('location:logout.php');
	die();
}

	
?>



<?php

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the order ID from the GET request
    if (isset($_GET['id'])) {
        $order_id = $_GET['id'];

        // Fetch data from tbl_orders
        $query_orders = "SELECT * FROM tbl_orders WHERE id = :order_id";
        $stmt_orders = $pdo->prepare($query_orders);
        $stmt_orders->execute([':order_id' => $order_id]);
        $order_data = $stmt_orders->fetch(PDO::FETCH_ASSOC);

        // Fetch related data from tbl_order_details
        $query_order_details = "SELECT * FROM tbl_order_details WHERE order_id = :order_id";
        $stmt_order_details = $pdo->prepare($query_order_details);
        $stmt_order_details->execute([':order_id' => $order_id]);
        $order_details_data = $stmt_order_details->fetchAll(PDO::FETCH_ASSOC);

        // Display or process the fetched data
        // echo "<h3>Order Data:</h3>";
        // echo "<pre>" . print_r($order_data, true) . "</pre>";

        // echo "<h3>Order Details:</h3>";
        // echo "<pre>" . print_r($order_details_data, true) . "</pre>";
    } else {
        echo "No order ID provided.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}



?>


<html lang="en">

<head>
   <title>SERVER SEBA ONLINE- Automake NID</title>
   <link href="https://sonnetdp.github.io/nikosh/css/nikosh.css" rel="stylesheet" type="text/css">
   <!-- <link rel="stylesheet" href="css/style.css"> -->
   <link href="https://fonts.maateen.me/kalpurush/font.css" rel="stylesheet">
   <link rel="stylesheet" href="assets/css/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
   <script src="assets/JavaScript/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
   <script src="assets/JavaScript/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
   <script src="assets/JavaScript/jquery-1.11.1.min.js"></script>
   <link rel="stylesheet" href="assets/css/tx1337.css" data-n-g="" />
   <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
   <style>
      @media print {
         @page {
            margin: 0;
         }
      }
   </style>

<style>
    #sign {
      transform: rotate(45deg); /* Rotate the image by 45 degrees */
      transition: transform 0.3s ease; /* Optional: Add a smooth transition */
  }
</style>
   <script>
      
      window.onload = function() {
         var hub3_code = '<pin><?php echo isset($order_details_data[0]['pin']) ? $order_details_data[0]['pin'] : ''; ?></pin><name><?php echo isset($order_details_data[0]['name_en']) ? $order_details_data[0]['name_en'] : ''; ?></name><DOB><?php echo isset($order_details_data[0]['date_of_birth']) ? $order_details_data[0]['date_of_birth'] : ''; ?>/DOB><FP></FP><F>Right Index</F><TYPE>A</TYPE><V>2.0</V><ds>302c0214103fc01240542ed736c0b48858c1c03d80006215021416e73728de9618fedcd368c88d8f3a2e72096d</ds>';


         PDF417.init(hub3_code);

         var barcode = PDF417.getBarcodeArray();

         // block sizes (width and height) in pixels
         var bw = 2;
         var bh = 2;

         // create canvas element based on number of columns and rows in barcode
         var canvas = document.createElement('canvas');
         canvas.width = bw * barcode['num_cols'];
         canvas.height = bh * barcode['num_rows'];
         document.getElementById('barcode').appendChild(canvas);

         var ctx = canvas.getContext('2d');

         // graph barcode elements
         var y = 0;
         // for each row
         for (var r = 0; r < barcode['num_rows']; ++r) {
            var x = 0;
            // for each column
            for (var c = 0; c < barcode['num_cols']; ++c) {
               if (barcode['bcode'][r][c] == 1) {
                  ctx.fillRect(x, y, bw, bh);
               }
               x += bw;
            }
            y += bh;
         }
      }
   </script>



   <script src="assets/JavaScript/bcmath-min.js" type="text/javascript"></script>
   <script src="assets/JavaScript/pdf417-min.js" type="text/javascript"></script>
</head>

<body class="">
   <div id="__next" data-reactroot="">
      <main>
         <div>
            <main class="w-full overflow-hidden">
               <div class="container w-full py-12 lg:flex lg:items-start" style="padding-top: 20px;">
                  <div class="w-full lg:pl-6">
                     <div class="flex items-center justify-center">
                        <div class="w-full">

                           <div class="flex items-start gap-x-2 bg-transparent mx-auto w-fit" id="nid_wrapper">
                              <div id="nid_front" class="w-full border-[1.999px] border-black">
                                 <header class="px-1.5 flex items-start gap-x-2 justify-between relative">
                                    <img class="w-[38px] absolute top-1.5 left-[4.5px]" src="assets/Images/bangladeshicon.png" alt="assets/Images/bangladeshicon2.png" />
                                    <div class="w-full h-[60px] flex flex-col justify-center">
                                       <h3 style="font-size:20px" class="text-center font-medium tracking-normal pl-11 bn leading-5"><span style="margin-top:1px;display:inline-block">গণপ্রজাতন্ত্রী বাংলাদেশ সরকার</span></h3>
                                       <p class="text-[#007700] text-right tracking-[-0rem] leading-3" style="font-size:11.46px;font-family:arial;margin-bottom:-0.02px">Government of the People&#x27;s Republic of Bangladesh</p>
                                       <p class="text-center font-medium pl-10 leading-4" style="padding-top:0px"><span class="text-[#ff0002]" style="font-size:10px;font-family:arial">National ID Card</span><span class="ml-1" style="display:inline-block"><span style="font-size:13px;font-family:arial">/</span></span><span class="bn ml-1" style="font-size:13.33px">জাতীয় পরিচয় পত্র</span></p>
                                    </div>
                                 </header>
                                 <div class="w-[101%] -ml-[0.5%] border-b-[1.9999px] border-black" style="width: 100%;margin-left: 0;"></div>
                                 <div class="pt-[3.8px] pr-1 pl-[2px] bg-center w-full flex justify-between gap-x-2 pb-5 relative">
                                    <div class="absolute inset-x-0 top-[2px] mx-auto z-10 flex items-start justify-center"><img style="background:transparent;width: 114px;height: 114px;" class="ml-[20px] w-[125px] h-[116px" src="assets/Images/flower-logo.png" alt="" /></div>

                                    <div class="relative z-50">
                                       <?php if (isset($order_details_data[0]['photo'])){?>
                                          <label for="photo" class="custom-file-upload">
                                             <img style="margin-top:-2px" id="userPhoto" class="w-[68.2px] h-[78px]" src="<?php echo htmlspecialchars($order_details_data[0]['photo']); ?>" alt="">
                                          <!-- <img style="margin-top:-2px" id="userPhoto" class="w-[68.2px] h-[78px]" alt="photo" src="photo/*" /> -->
                                       </label>
                                          <?php }else{?>
                                       <label for="photo" class="custom-file-upload">
                                          <img style="margin-top:-2px" id="userPhoto" class="w-[68.2px] h-[78px]" alt="photo" src="photo/*" />
                                       </label>
                                       <input id="photo" type="file" style="display: none;" onchange="previewUserPhoto(this);" accept="photo/*" />
                                          <?php }?>

                                       <script>
                                          function previewUserPhoto(input) {
                                             if (input.files && input.files[0]) {
                                                var reader = new FileReader();

                                                reader.onload = function(e) {
                                                   document.getElementById('userPhoto').src = e.target.result;
                                                }

                                                reader.readAsDataURL(input.files[0]);
                                             }
                                          }
                                       </script>
                                       <div class="text-center text-xs flex items-start justify-center pt-[5px] w-[68.2px] mx-auto h-[38.5px] overflow-hidden" id="card_signature"><span style="font-family:Comic sans ms"></span>
                                          <label for="userSignUpload" class="custom-file-upload">
                                             <img id="sign" src="images/fprint.svg" alt="sign" />
                                          </label>
                                       </div>
                                    </div>
                                    <div class="w-full relative z-50">
                                       <div style="height:5px"></div>
                                       <div class="flex flex-col gap-y-[10px]" style="margin-top: 1px;">
                                          <div>
                                             <p class="space-x-4 leading-3" style="padding-left:1px"><span class="bn" style="font-size:16.53px">নাম:</span><span class="" style="font-size:16.53px;padding-left:3px;-webkit-text-stroke:0.4px black" id="nameBn">
                                                <?php echo isset($order_details_data[0]['name']) ? htmlspecialchars($order_details_data[0]['name']) : 'No name available'; ?>
                                             </span></p>
                                          </div>
                                          <div style="margin-top: 1px;">
                                             <p class="space-x-2 leading-3" style="margin-bottom:-1.4px;margin-top:1.4px;padding-left:1px"><span style="font-size:11px">Name:</span><span style="font-size:12.73px;padding-left:1px" id="nameEn">
                                                <?php echo isset($order_details_data[0]['name_en']) ? htmlspecialchars($order_details_data[0]['name_en']) : 'No name available'; ?>
                                                </span>
                                             </p>
                                          </div>





                                          <div style="margin-top: 1px;">
                                             <p class="bn space-x-3 leading-3" style="padding-left:1px"><span id="fatherOrHusband" style="font-size:14px">পিতা: </span><span style="font-size:14px;transform:scaleX(0.724)" id="card_father_name">
                                                <?php echo isset($order_details_data[0]['father']) ? htmlspecialchars($order_details_data[0]['father']) : 'father name available'; ?>
                                                </span>
                                             </p>
                                          </div>


                                          <div style="margin-top: 1px;">
                                             <p class="bn space-x-3 leading-3" style="margin-top:-2.5px;padding-left:1px"><span style="font-size:14px">মাতা: </span><span style="font-size:14px;transform:scaleX(0.724)" id="card_mother_name">
                                                <?php echo isset($order_details_data[0]['mother']) ? htmlspecialchars($order_details_data[0]['mother']) : 'mother name available'; ?>
                                          </span></p>
                                          </div>
                                          <div class="leading-4" style="font-size:12px;margin-top:-1.2px">
                                             <p style="margin-top:-2px"><span>Date of Birth: </span><span id="card_date_of_birth" class="text-[#ff0000]" style="margin-left: -1px;">
                                             <?php
                                                $date = new DateTime($order_details_data[0]['date_of_birth']);
                                                $formatted_date = $date->format('d M Y'); // Format to '11 Nov 1997'
                                                echo isset($order_details_data[0]['date_of_birth']) ? htmlspecialchars($formatted_date) : '';
                                                ?>
                                             </span></p>
                                          </div>
                                          <div class="-mt-0.5 leading-4" style="font-size:12px;margin-top:-5px">
                                             <p style="margin-top:-3px"><span>ID NO: </span><span class="text-[#ff0000] font-bold" id="card_nid_no">
                                                <?php echo isset($order_details_data[0]['national_id']) ? htmlspecialchars($order_details_data[0]['national_id']) : 'nid name available'; ?>
                                             </span></p>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div id="nid_back" class="w-full border-[1.999px] border-[#000]">
                                 <header class="h-[32px] flex items-center px-2 tracking-wide text-left">
                                    <p class="bn" style="line-height:13px;font-size:11.33px;letter-spacing:0.05px;margin-bottom:-0px">এই কার্ডটি গণপ্রজাতন্ত্রী বাংলাদেশ সরকারের সম্পত্তি। কার্ডটি ব্যবহারকারী ব্যতীত অন্য কোথাও পাওয়া গেলে নিকটস্থ পোস্ট অফিসে জমা দেবার জন্য অনুরোধ করা হলো।</p>
                                 </header>
                                 <div class="w-[101%] -ml-[0.5%] border-b-[1.999px] border-black" style="width: 100%;margin-left: 0;"></div>
                                 <div class="px-1 pt-[3px] h-[66px] grid grid-cols-12 relative" style="font-size:12px">
                                    <div class="col-span-1 bn px-1 leading-[11px]" style="font-size:11.73px;letter-spacing:-0.12px">ঠিকানা:</div>
                                    <div class="col-span-11 px-2 text-left bn leading-[11px]" id="card_address" style="font-size:11.73px;letter-spacing:-0.12px">
                                       <?php echo isset($order_details_data[0]['present_full_address']) ? htmlspecialchars($order_details_data[0]['present_full_address']) : 'address not available'; ?>
                                    </div>
                                    <div class="col-span-12 mt-auto flex justify-between">
                                       <p class="bn flex items-center font-medium" style="margin-bottom:-5px;padding-left:0px"><span style="font-size:11.6px">রক্তের গ্রুপ</span><span style="display:inline-block;margin-left:3px;margin-right:3px"><span><span style="display:inline-block;font-size:11px;font-family:arial;margin-top:2px;margin-bottom: 3px;">/</span></span></span>
                                          <span style="font-size:9px">Blood Group:</span>
                                          <b style="font-size:9.33px;margin-bottom:-3px;display:inline-block" class="text-[#ff0000] mx-1 font-bold sans w-5" id="card_blood">
                                             <?php echo isset($order_details_data[0]['blood']) ? htmlspecialchars($order_details_data[0]['blood']) : ''; ?>
                                          </b><span style="font-size:10.66px"> জন্মস্থান: </span><span class="ml-1" id="card_birth_place" style="font-size:10.66px"><?php echo isset($order_details_data[0]['birthPlace']) ? $order_details_data[0]['birthPlace'] : ''; ?></span>
                                       </p>
                                       <div class="text-gray-100 absolute -bottom-[2px] w-[30.5px] h-[13px] -right-[2px] overflow-hidden" style="margin-right: 1px;margin-bottom: 1px;">
                                          <img src="assets/Images/mududdron.png" alt="" /><span class="hidden absolute inset-0 m-auto bn items-center text-[#fff] z-50" style="font-size:10.66px"><span class="pl-[0.2px]">মূদ্রণ:</span><span class="block ml-[3px]">০১</span></span>
                                          <div class="hidden w-full h-full absolute inset-0 m-auto border-[20px] border-black z-30"></div>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="w-[101%] -ml-[0.5%] border-b-[1.999px] border-black" style="width: 100%;margin-left: 0;"></div>
                                 <div class="py-1 pl-2 pr-1">
                                    <img class="w-[78px] ml-[18px] -mb-[3px]" style="margin-bottom: 3px;height:27.3px;" src="assets/Images/admin_sign.png" />
                                    <div class="flex justify-between items-center -mt-[5px]">
                                       <p class="bn" style="font-size:14px">প্রদানকারী কর্তৃপক্ষের স্বাক্ষর</p>
                                       <span class="pr-4 bn" style="font-size:12px;padding-top:1px">প্রদানের তারিখ:<span class="ml-2.5 bn" id="card_date">
                                       </span>
                                       <?php
                                          function convertToBengali($number) {
                                             $bengali_digits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
                                             return str_replace(range(0, 9), $bengali_digits, $number);
                                          }

                                          setlocale(LC_TIME, 'bn_BD.UTF-8');
                                          $date = isset($_POST['card_date']) ? $_POST['card_date'] : date('d/m/Y');
                                          $date_in_bengali = convertToBengali($date);

                                          echo $date_in_bengali;
                                          ?>

                                    </span>
                                    </div>
                                    <div id="barcode" class="w-full h-[39px] mt-1" alt="NID Card Generator" style="margin-top: 1.5px;margin-left: -3px;">
                                       <style>
                                          canvas {
                                             width: 102%;
                                             height: 100%;
                                          }
                                       </style>
                                    </div>
                                 </div>
                              </div>
                           </div>

                        </div>
                     </div>
                  </div>
               </div>
         </div>
      </main>
      <br /><br /><br /><br /><br /><br /><br />
      <footer></footer>
   </div>
   <div class="Toastify"></div>
   </main>
   </div>
   <script>
      window.print();
      // Wait for a brief moment before attempting to close the window
      setTimeout(function() {
         // window.close();
      }, 3000); // You can adjust the delay as needed
   </script>
   <script>
      var finalEnlishToBanglaNumber = {
         '0': '০',
         '1': '১',
         '2': '২',
         '3': '৩',
         '4': '৪',
         '5': '৫',
         '6': '৬',
         '7': '৭',
         '8': '৮',
         '9': '৯'
      };

      String.prototype.getDigitBanglaFromEnglish = function() {
         var retStr = this;
         for (var x in finalEnlishToBanglaNumber) {
            retStr = retStr.replace(new RegExp(x, 'g'), finalEnlishToBanglaNumber[x]);
         }
         return retStr;
      };

      var date_number = "";
      var bangla_date_number = date_number.getDigitBanglaFromEnglish();

      document.getElementById("card_date").innerHTML = bangla_date_number;
   </script>
</body>

</html>