<?php
	include 'connect.php';
	include 'session.php';
	include 'inc/php/functions.php';

	//create a random key
	$ref = '';
	$key = '';

	for($i=0;$i<4;$i++){
    	$ref = $ref.chr(rand(65,90)); 
	}

	for($i=0;$i<6;$i++){
    	$key = $key.chr(rand(48,57)); 
	}
	
	//add confirm row
	$confirm = mysql_query("insert into otp(customer_id,customer_email,otp_ref,otp_key,otp_date) values('$user_id','$login_session','$ref','$key',NOW())");	

	if ($confirm) {
		
		//include the swift class
		include_once 'inc/php/swift/swift_required.php';
		$name = $_SESSION['CX_login_name'];
	
		//put info into an array to send to the function
		$info = array(
			'username' => $name,
			'email' => $login_session,
			'ref' => $ref,
			'key' => $key);

		//print_r($info);
	
		//send the email
		if(send_email_otp($info)){
						
			//email sent
			echo "รหัสถูกส่งไปยังอีเมล์เรียบร้อย<br />";
			echo "รหัสอ้างอิง = <span id='otp_ref_source'>".$ref."</span>";
		
		}else{

			$info = array(
				'username' => '',
				'email' => $login_session,
				'ref' => $ref,
				'key' => $key);

			if(send_email_otp($info)){
						
				//email sent withputname
				echo "รหัสถูกส่งไปยังอีเมล์เรียบร้อย<br />";
				echo "รหัสอ้างอิง = <span id='otp_ref_source'>".$ref."</span>";
			
			}else{
				echo 'เกิดข้อผิดพลาดในการส่งอีเมล์<br />';
				echo "รหัสอ้างอิง = <span id='otp_ref_source'>".$ref."</span>";
			}
		
		}

	}else{
		
		echo 'OTP row was not added to the database. Reason: ' . mysql_error();
		
	}
?>