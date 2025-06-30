<?php
include('includes/database.php');

date_default_timezone_set('Asia/Dhaka');


	class DB_con{
		function __construct(){
			$con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
			$this->dbh = $con;
			if (mysqli_connect_errno()) {
				echo "Failed to connect to MySQL: " . mysqli_connect_error();
			}
		}

		// for username availblty
		public function usernameavailblty($uname)
		{
			$result = mysqli_query($this->dbh, "SELECT Username FROM tblusers WHERE Username='$uname'");
			return $result;
		}

		// Function for registration recharge_approve
		public function registration($fname, $uname, $uemail, $whatsapp, $type, $pasword, $pass_key)
		{
			$ret = mysqli_query($this->dbh, "insert into tblusers(FullName,Username,whatsapp,UserEmail,account_type,Password, pass_key) values('$fname','$uname','$whatsapp','$uemail','$type','$pasword','$pass_key')");
			return $ret;
		}

		// Function for signin
		public function signin($uname, $password)
		{
			// $result = mysqli_query($this->dbh, "select id,FullName from tblusers where Username='$uname' and Password='$password'");
			// // die(var_dump($result));
			// return $result;
			$query = "select * from tblusers where Username=? and Password=?";
			$stmt = $this->dbh->prepare($query);
			$stmt->bind_param('ss',$uname,$password);
			$stmt->execute();
			$result = $stmt->get_result()->fetch_all();
			return $result;
		}


		public function insert_submission($certi_no, $type, $national_id, $passport_no, $nationality, $name, $gender, $date_birth, $doseone_date, $doseone_name, $dosetwo_date, $dosetwo_name, $dosethree_date, $dosethree_name, $vacc_center, $vacc_by, $total_dose, $file, $user_id)
		{
			$ret = mysqli_query($this->dbh, "insert into tbl_submission(certi_no,type,national_id,passport_no,nationality,name,gender,date_birth,doseone_date,doseone_name,dosetwo_date,dosetwo_name,dosethree_date,dosethree_name,vacc_center,vacc_by,total_dose,qr_code,submitted_by) values('$certi_no','$type','$national_id','$passport_no','$nationality','$name','$gender','$date_birth','$doseone_date','$doseone_name','$dosetwo_date','$dosetwo_name','$dosethree_date','$dosethree_name','$vacc_center','$vacc_by','$total_dose','$file','$user_id')");

			return $ret;
		}

		// update submission 	
		public function update_submission($certi_no, $type, $national_id, $passport_no, $nationality, $name, $gender, $date_birth, $doseone_date, $doseone_name, $dosetwo_date, $dosetwo_name, $dosethree_date, $dosethree_name, $vacc_center, $vacc_by, $total_dose, $id)
		{
			$ret = mysqli_query($this->dbh, "update tbl_submission set certi_no='$certi_no',type='$type',national_id='$national_id',passport_no='$passport_no',nationality='$nationality',name='$name',gender='$gender',date_birth='$date_birth',doseone_date='$doseone_date',doseone_name='$doseone_name',dosetwo_date='$dosetwo_date',dosetwo_name='$dosetwo_name',dosethree_date='$dosethree_date',dosethree_name='$dosethree_name',vacc_center='$vacc_center',vacc_by='$vacc_by',total_dose='$total_dose' where id='$id' ");

			return $ret;
		}

		public function fetchdata($user_id)
		{
			$result = mysqli_query($this->dbh, "select * from tbl_submission where submitted_by=$user_id order by id desc");
			return $result;
		}


		public function fetchWhatsapp($user_id)
		{
			// Ensure the user ID is an integer
			$user_id = (int) $user_id;

			// Prepare and execute the statement
			$stmt = $this->dbh->prepare("SELECT whatsapp FROM tblusers WHERE id = ?");
			if ($stmt) {
				$stmt->bind_param("i", $user_id);
				$stmt->execute();
				$result = $stmt->get_result();

				if ($result && $row = $result->fetch_assoc()) {
					return $row['whatsapp'] ?? '';
				}
			}

			return null;
		}



		public function fetch_users()
		{
			$result = mysqli_query($this->dbh, "select * from tblusers order by id desc");
			return $result;
		}
		
		public function fetch_bkash_pay()
		{
			$result = mysqli_query($this->dbh, "select * from bkash_pay order by id desc");
			return $result;
		}


		public function fetch_total_recharge($user_id)
		{
			$user_id = intval($user_id); // Sanitize input
			$query = "SELECT SUM(amount) AS total FROM bkash_pay WHERE user_id = $user_id";
			$result = mysqli_query($this->dbh, $query);

			if ($result && $row = mysqli_fetch_assoc($result)) {
				return intval($row['total'] ?? 0); // Cast to int
			}

			return 0;
		}


		
		public function fetch_apis()
		{
			$result = mysqli_query($this->dbh, "select * from api_list order by id desc");
			return $result;
		}

		// for ServerCopyNew
		public function fetch_Cookie()
		{
			// Prepare the query to fetch only the required column
			$query = "SELECT api_key FROM api_list WHERE api_name = 'Cookie' LIMIT 1";
			
			// Execute the query
			$result = mysqli_query($this->dbh, $query);
		
			if ($result && $row = mysqli_fetch_assoc($result)) {
				return $row['api_key']; // Return the api_key
			}
		
			return null;
		}
		

		// for user id 
		public function user_id($id)
		{
			$result = mysqli_query($this->dbh, "SELECT * FROM tbl_submission WHERE id=$id");
			return $result;
		}
		
		// for ServerCopyNew
		public function ServerCopyNew($nid, $user_id)
		{
			$result = mysqli_query($this->dbh, "SELECT * FROM tbl_orders WHERE order_type = 'Server copy' AND nid = $nid AND user_id = $user_id");

			// return $result;
			if ($result && mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				return $row['nid']; // Return the account type
			} else {
				return null; // Return null if no data found
			}
		}	
		
		// for ServerCopyNew
		public function ServerCopyOld($nid, $user_id)
		{
			$result = mysqli_query($this->dbh, "SELECT * FROM tbl_orders WHERE order_type = 'Server Old' AND nid = $nid AND user_id = $user_id");

			// return $result;
			if ($result && mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				return $row['nid']; // Return the account type
			} else {
				return null; // Return null if no data found
			}
		}	
		
		// for ServerCopyNew
		public function ServerCopyPin($nid, $user_id)
		{
			$result = mysqli_query($this->dbh, "SELECT * FROM tbl_orders WHERE order_type = 'Server Pin' AND nid = $nid AND user_id = $user_id");

			// return $result;
			if ($result && mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				return $row['nid']; // Return the account type
			} else {
				return null; // Return null if no data found
			}
		}		
		
		// for ServerCopyNew
		public function ServerMakeNID($nid, $user_id)
		{
			$result = mysqli_query($this->dbh, "SELECT * FROM tbl_orders WHERE order_type = 'Make NID' AND nid = $nid AND user_id = $user_id");

			// return $result;
			if ($result && mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				return $row['nid']; // Return the account type
			} else {
				return null; // Return null if no data found
			}
		}


		// delete user id 
		public function delete_submission($id)
		{
			$result = mysqli_query($this->dbh, "DELETE FROM tbl_submission WHERE id=$id");
			return $result;
		}

		// delete user id 
		public function delete_bks($id)
		{
			$result = mysqli_query($this->dbh, "DELETE FROM bkash_pay WHERE id=$id");
			return $result;
		}


		// delete user id 
		public function delete_user($id)
		{
			$result = mysqli_query($this->dbh, "DELETE FROM tblusers WHERE id=$id");
			return $result;
		}
		// delete user id 
		public function delete_users($id)
		{
			$result = mysqli_query($this->dbh, "DELETE FROM tblusers WHERE id=$id");
			return $result;
		}


		// for user id 
		public function user_certi_no($id)
		{
			$result = mysqli_query($this->dbh, "SELECT * FROM tbl_submission WHERE certi_no='$id'");
			return $result;
		}


		// for deposit balance
		public function insert_deposit($deposit, $id)
		{
			$result = mysqli_query($this->dbh, "insert into tbl_balance(deposit,user_id) values('$deposit','$id' )");
			return $result;
		}	
		
		// for deposit balance
		public function decrement_balance($decrement, $id)
		{
			$result = mysqli_query($this->dbh, "insert into tbl_balance(withdraw,user_id) values('$decrement','$id' )");
			return $result;
		}

		public function get_deposit($id)
		{
			$result = mysqli_query($this->dbh, "select * from tbl_balance where user_id=$id  and deposit > 0 order by id desc");
			return $result;
		}

		// for recharge balance
		public function request_deposit($number, $txn_id, $deposit, $id, $username)
		{
			$result = mysqli_query($this->dbh, "insert into tbl_request(number,txn_id,deposit,user_id,username) values('$number','$txn_id','$deposit','$id','$username' )");
			return $result;
		}

		// for fetching recharge requests
		public function get_recharge()
		{
			$result = mysqli_query($this->dbh, "select * from tbl_request where deposit > 0 order by id desc");
			return $result;
		}

		// for delet recharge history
		public function delete_recharge($i)
		{
			$result = mysqli_query($this->dbh, "DELETE FROM tbl_request WHERE id=$i");
			return $result;
		}

		// for balance 
		public function get_balance($user_id)
		{
			$result = mysqli_query($this->dbh, "SELECT SUM(deposit) AS deposit_sum, SUM(withdraw) AS withdraw_sum  FROM tbl_balance WHERE user_id=$user_id");
			return $result;
		}

		// for withdraw
		public function get_withdraw($user_id, $chargee)
		{
			$result = mysqli_query($this->dbh, "insert into tbl_balance(user_id,withdraw) values('$user_id','$chargee')");
			return $result;
		}
		

		public function user_type($id)
		{
			$result = mysqli_query($this->dbh, "SELECT account_type FROM tblusers WHERE id=$id");
			if ($result && mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				return $row['account_type']; // Return the account type
			} else {
				return null; // Return null if no data found
			}
		}

		// Function for Api Add
		public function insert_apis($name, $url, $key)
		{
			$ret = mysqli_query($this->dbh, "insert into api_list(api_name,api_url,api_key) values('$name','$url','$key')");
			return $ret;
		}
		
		// Function to update API
		function update_api($id, $api_name, $api_url, $api_key) {

			// $result = mysqli_query($this->dbh, "UPDATE api_list SET api_name='$api_name', api_url='$api_url', api_key='$api_key', updated_at=NOW() WHERE id='$id'");

			$update_api = mysqli_query(
				$this->dbh,
				"UPDATE api_list 
				 SET api_name='$api_name', api_url='$api_url', api_key='$api_key', updated_at=NOW() 
				 WHERE id='$id'"
			);
			return $update_api;
		}





		// Function to update API
		public function update_sv_ids($id, $updated_id) {
			// Prepare the query
			$stmt = mysqli_prepare($this->dbh, 
				"UPDATE tbl_orders 
				SET user_id = ? 
				WHERE id = ?"
			);

			// Bind parameters
			mysqli_stmt_bind_param($stmt, "ii", $updated_id, $id); // 'ii' = 2 integers

			// Execute query
			$result = mysqli_stmt_execute($stmt);

			// Check for errors
			if (!$result) {
				return false; // Query failed
			}

			// Close the statement
			mysqli_stmt_close($stmt);

			return true; // Success
		}



		// public function update_delivery($id, $target_file) {
		// 	// Prepare the query
		// 	$stmt = mysqli_prepare($this->dbh, 
		// 		"UPDATE tbl_file_orders 
		// 		 SET file = ?, status = 'delivered', revision_request = 'No revisions yet' , delivery_time = now(),
		// 		 WHERE id = ?"
		// 	);
			
		// 	// Check if statement preparation failed
		// 	if (!$stmt) {
		// 		return false; // Statement preparation failed
		// 	}
		
		// 	// Bind parameters (file as string 's', id as integer 'i')
		// 	if (!mysqli_stmt_bind_param($stmt, "si", $target_file, $id)) {
		// 		mysqli_stmt_close($stmt);
		// 		return false; // Binding parameters failed
		// 	}
		
		// 	// Execute the query
		// 	$result = mysqli_stmt_execute($stmt);
		
		// 	// Check for errors during execution
		// 	if (!$result) {
		// 		mysqli_stmt_close($stmt); // Close statement before returning
		// 		return false; // Query execution failed
		// 	}
		
		// 	// Close the statement
		// 	mysqli_stmt_close($stmt);
		
		// 	return true; // Success
		// }

		public function update_delivery($id, $target_file) {
			// Prepare the query
			$stmt = mysqli_prepare($this->dbh, 
				"UPDATE tbl_file_orders 
				 SET file = ?, status = 'delivered', revision_request = 'No revisions yet', delivery_time = NOW()
				 WHERE id = ?"
			);
		
			// Check if statement preparation failed
			if (!$stmt) {
				return false; // Statement preparation failed
			}
		
			// Bind parameters (file as string 's', id as integer 'i')
			if (!mysqli_stmt_bind_param($stmt, "si", $target_file, $id)) {
				mysqli_stmt_close($stmt);
				return false; // Binding parameters failed
			}
		
			// Execute the query
			$result = mysqli_stmt_execute($stmt);
		
			// Check for errors during execution
			if (!$result) {
				mysqli_stmt_close($stmt); // Close statement before returning
				return false; // Query execution failed
			}
		
			// Close the statement
			mysqli_stmt_close($stmt);
		
			return true; // Success
		}
		
		
			
		// Function to update API
		public function revision_request($id) {
			// Prepare the query
			$stmt = mysqli_prepare($this->dbh, 
				"UPDATE tbl_file_orders 
				SET revision_request = 'Request' 
				WHERE id = ?"
			);
		
			// Check if the statement was prepared successfully
			if (!$stmt) {
				return false; // Failed to prepare statement
			}
		
			// Bind parameters (only 1 integer, so use "i")
			mysqli_stmt_bind_param($stmt, "i", $id); // 'i' = single integer parameter
		
			// Execute query
			$result = mysqli_stmt_execute($stmt);
		
			// Check for errors
			if (!$result) {
				mysqli_stmt_close($stmt); // Close statement
				return false; // Query failed
			}
		
			// Close the statement
			mysqli_stmt_close($stmt);
		
			return true; // Success
		}
		


		// delete Api id 
		public function delete_api($id)
		{
			$result = mysqli_query($this->dbh, "DELETE FROM api_list WHERE id=$id");
			return $result;
		}
				
		
		public function delete_sv($id)
		{
			// Start transaction
			mysqli_begin_transaction($this->dbh);

			try {
				// Delete from tbl_orders
				$stmt1 = mysqli_prepare($this->dbh, "DELETE FROM tbl_orders WHERE id = ?");
				mysqli_stmt_bind_param($stmt1, 'i', $id);
				mysqli_stmt_execute($stmt1);

				// Delete from tbl_order_details
				$stmt2 = mysqli_prepare($this->dbh, "DELETE FROM tbl_order_details WHERE order_id = ?");
				mysqli_stmt_bind_param($stmt2, 'i', $id);
				mysqli_stmt_execute($stmt2);

				// Commit transaction if both queries succeed
				mysqli_commit($this->dbh);

				// Close statements
				mysqli_stmt_close($stmt1);
				mysqli_stmt_close($stmt2);

				return true; // Success
			} catch (Exception $e) {
				// Rollback on error
				mysqli_rollback($this->dbh);

				return false; // Failure
			}
		}
		

		public function delete_file($id)
		{
			// Start transaction
			mysqli_begin_transaction($this->dbh);

			try {
				// Delete from tbl_file_orders
				$stmt1 = mysqli_prepare($this->dbh, "DELETE FROM tbl_file_orders WHERE id = ?");
				mysqli_stmt_bind_param($stmt1, 'i', $id);
				mysqli_stmt_execute($stmt1);

				// Commit transaction if both queries succeed
				mysqli_commit($this->dbh);

				// Close statements
				mysqli_stmt_close($stmt1);

				return true; // Success
			} catch (Exception $e) {
				// Rollback on error
				mysqli_rollback($this->dbh);

				return false; // Failure
			}
		}



		public function pin_api()
		{
			$result = mysqli_query($this->dbh, "SELECT api_url FROM api_list WHERE api_name='pin'");
			if ($result && mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				return $row['api_url']; // Return the account type
			} else {
				return null; // Return null if no data found
			}
		}



		//Premium 
		public function is_premium($id)
		{
			$id = intval($id); // Sanitize ID
			$result = mysqli_query($this->dbh, "SELECT premium FROM tblusers WHERE id = $id");
			if ($result && mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_assoc($result);
				return intval($row['premium']); // Ensure it returns 0 or 1 as integer
			}
			return 0; // Return 0 by default if not found
		}



		// Function to update Premium
		public function premium_update($premium, $id)
			{
				$premium = intval($premium); // Ensure it's an integer (0 or 1)
				$id = intval($id);           // Sanitize ID
				$update_premium = mysqli_query(
					$this->dbh,
					"UPDATE tblusers 
					SET premium = $premium
					WHERE id = $id"
				);
				return $update_premium;
			}


		
		
			// insert control
		public function in_login($in_login)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `login` = '$in_login' WHERE `control`.`id` = 1;");
			return $result;
		}
		public function in_register($in_register)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `register` = '$in_register' WHERE `control`.`id` = 1;");
			return $result;
		}
		public function in_approval($in_approval)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `approval` = '$in_approval' WHERE `control`.`id` = 1;");
			return $result;
		}
		public function in_charge($in_charge)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `charge` = '$in_charge' WHERE `control`.`id` = 1;");
			return $result;
		}
		public function in_server_copy($in_server_copy)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `server_copy` = '$in_server_copy' WHERE `control`.`id` = 1;");
			return $result;
		}
		public function in_pdf_copy($in_pdf_copy)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `pdf_copy` = '$in_pdf_copy' WHERE `control`.`id` = 1;");
			return $result;
		}
		public function in_sign_copy($in_sign_copy)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `sign_copy` = '$in_sign_copy' WHERE `control`.`id` = 1;");
			return $result;
		}			
		public function in_sign_to_nid($in_sign_to_nid)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `sign_to_nid` = '$in_sign_to_nid' WHERE `control`.`id` = 1;");
			return $result;
		}			
		public function in_nid_pdf_copy($in_nid_pdf_copy)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `nid_pdf_copy` = '$in_nid_pdf_copy' WHERE `control`.`id` = 1;");
			return $result;
		}		
		public function in_server_pin($in_server_pin)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `server_pin` = '$in_server_pin' WHERE `control`.`id` = 1;");
			return $result;
		}
		public function in_rg_msg($in_rg_msg)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `rg_msg` = '$in_rg_msg' WHERE `control`.`id` = 1;");
			return $result;
		}
		public function in_bot($in_bot)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `bot_token` = '$in_bot' WHERE `control`.`id` = 1;");
			return $result;
		}
		public function in_log($in_log)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `log_channel` = '$in_log' WHERE `control`.`id` = 1;");
			return $result;
		}		
		public function log_channel($log_channel)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `log_channel` = '$log_channel' WHERE `control`.`id` = 1;");
			return $result;
		}
		public function in_notice($in_notice)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `notice` = '$in_notice' WHERE `control`.`id` = 1;");
			return $result;
		}		
		public function in_notice_two($in_notice_two)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `notice_two` = '$in_notice_two' WHERE `control`.`id` = 1;");
			return $result;
		}
		public function in_robi_id($in_robi_id)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `robi_user` = '$in_robi_id' WHERE `control`.`id` = 1;");
			return $result;
		}
		public function in_robi_token($in_robi_token)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `robi_token` = '$in_robi_token' WHERE `control`.`id` = 1;");
			return $result;
		}
		public function in_bl_id($in_bl_id)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `bl_user` = '$in_bl_id' WHERE `control`.`id` = 1;");
			return $result;
		}
		public function in_bl_token($in_bl_token)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `bl_token` = '$in_bl_token' WHERE `control`.`id` = 1;");
			return $result;
		}		
		public function in_bot_token($in_bot_token)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `bot_token` = '$in_bot_token' WHERE `control`.`id` = 1;");
			return $result;
		}	
		
		public function in_bl_location($in_bl_location)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `bl_location` = '$in_bl_location' WHERE `control`.`id` = 1;");
			return $result;
		}		
		
		public function in_make_birth($in_make_birth)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `make_birth` = '$in_make_birth' WHERE `control`.`id` = 1;");
			return $result;
		}


		




		//BIO FUNCTION

		public function in_robi_bio($in_robi_bio)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `robi_bio` = '$in_robi_bio' WHERE `control`.`id` = 1;");
			return $result;
		}

		public function in_bl_bio($in_bl_bio)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `bl_bio` = '$in_bl_bio' WHERE `control`.`id` = 1;");
			return $result;
		}

		public function in_airtel_bio($in_airtel_bio)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `airtel_bio` = '$in_airtel_bio' WHERE `control`.`id` = 1;");
			return $result;
		}	

		public function in_gp_bio($in_gp_bio)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `gp_bio` = '$in_gp_bio' WHERE `control`.`id` = 1;");
			return $result;
		}

		public function in_teletalk_bio($in_teletalk_bio)
		{
			$result = mysqli_query($this->dbh, "UPDATE `control` SET `teletalk_bio` = '$in_teletalk_bio' WHERE `control`.`id` = 1;");
			return $result;
		}







	
		// GET control_value
		public function get_control()
		{
			$result = mysqli_query($this->dbh, "SELECT * FROM control WHERE id=1 LIMIT 1");
			return $result;
		}
		
		// bkash history
		public function bkash_pay($user_id, $username, $paymentID, $payerReference, $customerMsisdn, $trxID, $amount, $merchantInvoiceNumber, $paymentExecuteTime )
		{
			$result = mysqli_query($this->dbh, "insert into bkash_pay(user_id,username,paymentID,payerReference,customerMsisdn,trxID,amount,merchantInvoiceNumber,paymentExecuteTime) values('$user_id','$username','$paymentID','$payerReference','$customerMsisdn','$trxID','$amount','$merchantInvoiceNumber','$paymentExecuteTime' )");
			return $result;
		}
		
		
		
		
	}
	
	function curl($url,$headData,$postData){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER , 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headData);
		$content = curl_exec($curl);
		curl_close($curl);
		return $content;
	}

	function getNidInfo($nid,$dob){
		$result = [];
		$url = 'https://gd.police.gov.bd/api/NationalIdentityInfo/GetNationalIdentityInfo';
		$headData = ['content-type: application/json'];
		$postData = json_encode(["nid" => $nid, "dob" => $dob, "callName" => 'NIDVerifyAlok']);
		$nidInfo = json_decode(curl($url,$headData,$postData),true);
		if(isset($nidInfo['status'], $nidInfo['nidInfo']['status'])){
			if($nidInfo['status'] && $nidInfo['nidInfo']['status'] == "OK"){
				$result['success'] = true;
				$result['nidData'] = $nidInfo['nidInfo']['success']['data'];
			}else{
				$result['success'] = false;
			}
		}else{
			$result['success'] = false;
		}
		return $result;
	}
		
	function getNidModInfo($nid,$dob){
		$nidInfo = getNidInfo($nid,$dob);
		if($nidInfo['success']){
			$result['success'] = true;
			$result['nidData']['name'] = $nidInfo['nidData']['name'];
			$result['nidData']['nameEn'] = $nidInfo['nidData']['nameEn'];
			$result['nidData']['nationalId'] = $nidInfo['nidData']['nationalId'];
			$result['nidData']['gender'] = $nidInfo['nidData']['gender'];
			$result['nidData']['bloodGroup'] = $nidInfo['nidData']['bloodGroup'];
			$result['nidData']['dateOfBirth'] = $nidInfo['nidData']['dateOfBirth'];
			$result['nidData']['birthPlace'] = $nidInfo['nidData']['permanentAddress']['district'];
			$result['nidData']['father'] = $nidInfo['nidData']['father'];
			$result['nidData']['nidFather'] = $nidInfo['nidData']['nidFather'];
			$result['nidData']['mother'] = $nidInfo['nidData']['mother'];
			$result['nidData']['nidMother'] = $nidInfo['nidData']['nidMother'];
			$result['nidData']['spouse'] = $nidInfo['nidData']['spouse'];
			$result['nidData']['voterArea'] = $nidInfo['nidData']['voterArea'];
			$result['nidData']['voterAreaCode'] = $nidInfo['nidData']['voterAreaCode'];
			$result['nidData']['mobile'] = $nidInfo['nidData']['mobile'];
			$result['nidData']['religion'] = $nidInfo['nidData']['religion'];
			$result['nidData']['photo'] = $nidInfo['nidData']['photo'];
			$result['nidData']['permanentAddress'] = $nidInfo['nidData']['permanentAddress'];
			$result['nidData']['presentAddress'] = $nidInfo['nidData']['presentAddress'];
			$explodeVoterArea = explode(' (',$nidInfo['nidData']['voterArea']);
			$uniqueVillageOrRoad = $explodeVoterArea[0];
			if($result['nidData']['permanentAddress']['additionalVillageOrRoad'] == ""){
				$result['nidData']['permanentAddress']['additionalVillageOrRoad'] = $uniqueVillageOrRoad;
			}
			if($result['nidData']['presentAddress']['additionalVillageOrRoad'] == "" && $result['nidData']['presentAddress']['region'] == $result['nidData']['permanentAddress']['region']){
				$result['nidData']['presentAddress']['additionalVillageOrRoad'] = $uniqueVillageOrRoad;
			}
			$result['msg'] = 'Developed by ErrorX Ltd!';
		}else{
			$result['success'] = false;
			$result['msg'] = 'Info not found!';
		}
		return json_encode($result);
	}
?>