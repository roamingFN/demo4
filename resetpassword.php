<?php

include_once 'connect.php';
include_once 'inc/php/functions.php';
session_start();

function crypto_rand_secure($min, $max) {
	$range = $max - $min;
	if ($range < 0) return $min; // not so random...
	$log = log($range, 2);
	$bytes = (int) ($log / 8) + 1; // length in bytes
	$bits = (int) $log + 1; // length in bits
	$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
	do {
			$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rnd = $rnd & $filter; // discard irrelevant bits
	} while ($rnd >= $range);
	return $min + $rnd;
}

function getToken($length=32){
	$token = "";
	$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
	$codeAlphabet.= "0123456789";
	for($i=0;$i<$length;$i++){
			$token .= $codeAlphabet[crypto_rand_secure(0,strlen($codeAlphabet))];
	}
	return $token;
}

//setup some variables
$action = array();
$action['result'] = null;

if (isset($_GET['request_reset_password'])) {
	if (isset($_GET['email'])) {
		$email = mysql_real_escape_string($_GET['email']);
		$key = getToken();

		$check_email = mysql_query("select * from customer where customer_email = '$email' LIMIT 1");

		if (mysql_num_rows($check_email) != 0) {

			$user_row = mysql_fetch_assoc($check_email);

			//add request reset row
			$insert = mysql_query("insert into customer_reset_password(customer_id,customer_email,reset_key) 
				values('".$user_row['customer_id']."','$email','$key')"); 

			if ($insert) {
				//include the swift class
				include_once 'inc/php/swift/swift_required.php';
			
				//put info into an array to send to the function
				$info = array(
					'username' => $user_row['customer_firstname']." ".$user_row['customer_lastname'],
					'email' => $email,
					'key' => $key);
			
				//send the email
				if(send_email_reset_pass($info)){
								
					//email sent
					echo "SUCCESS";
				
				}else{
					
					//firstname mayby thai lang. it's incorrent to make subject info name
					$info = array(
					'username' =>  $user_row['customer_code'],
					'email' => $email,
					'key' => $key);
			
					//send the email
					if(send_email_reset_pass($info)){
									
						//email sent
						echo "SUCCESS";
					
					}else{
						echo "ไม่สามารถส่งอีเมล์ได้ กรุณาลองอีกครั้งหรือติดต่อเจ้าหน้าที่ค่ะ";
					}
				
				}
			}else{
				echo "ไม่สามารถดำเนินการได้ :".mysql_error();
			}
			return;

		}else{
			echo "อีเมล์ ".$email." ไม่มีอยู่ในระบบค่ะ";
			return;
		}
	}
	echo "ข้อมูลผิดพลาด";
	return;
}

if (isset($_POST['submit_reset_password_form'])) {

	if(empty($_POST['email']) || empty($_POST['key']) || empty($_POST['newpass']) || empty($_POST['confirm_newpass'])){
		$action['result'] = 'error';
		$action['text'] = 'กรุณากรอกข้อมูลให้ครบทุกช่องด้วยค่ะ';      
	}else if($_POST['newpass'] != $_POST['confirm_newpass']){
		$action['result'] = 'error';
		$action['text'] = 'ช่องรหัสผ่านและยืนยันรหัสผ่านไม่ตรงกันค่ะ';      
	}
			
	if($action['result'] != 'error'){

		//cleanup the variables
		$email = mysql_real_escape_string($_POST['email']);
		$key = mysql_real_escape_string($_POST['key']);
		$newpass = mysql_real_escape_string($_POST['newpass']);
		$confirm_newpass = mysql_real_escape_string($_POST['confirm_newpass']);
		
		//check if the key is in the database
		$check_key = mysql_query("select * from customer_reset_password 
			where customer_email = '$email' AND reset_key = '$key' order by id desc LIMIT 1") or die(mysql_error());
		
		if(mysql_num_rows($check_key) != 0){
					
			//get the confirm info
			$confirm_info = mysql_fetch_assoc($check_key);

			//encode password
			$newpass = sha1($newpass);
			
			//update new password
			$update_users = mysql_query("update customer SET passwd = '$newpass' 
				where customer_id = '".$confirm_info['customer_id']."' LIMIT 1") or die(mysql_error());

			//delete the key row
			$delete = mysql_query("delete from customer_reset_password 
				where customer_id = '".$confirm_info['customer_id']."'") or die(mysql_error());
			
			if($update_users){
							
				$action['result'] = 'success';
				$action['text'] = 'เปลี่ยนรหัสผ่านเรียบร้อยค่ะ';
			
			}else{

				$action['result'] = 'error';
				$action['text'] = 'The user could not be updated Reason: '.mysql_error();
			
			}
		
		}else{
		
			$action['result'] = 'error';
			$action['text'] = 'ลิงค์ของคุณหมดอายุแล้วค่ะ กรุณากรอกอีเมล์เพื่อขอรับลิงค์ในการเปลี่ยนรหัสผ่านใหม่ค่ะ.';
		
		}
	}
}else if (empty($_GET['email']) || empty($_GET['key']) || isset($_SESSION['CX_login_user'])) {
	header( "location: index.php"); 

}else{

	$email = mysql_real_escape_string($_GET['email']);
	$key = mysql_real_escape_string($_GET['key']);

	//check if the key is in the database
	$check_key = mysql_query("select * from customer_reset_password 
		where customer_email = '$email' AND reset_key = '$key' order by id desc LIMIT 1") or die(mysql_error());
	
	if(mysql_num_rows($check_key) > 0){
		//Status OK
	}else{
		$action['result'] = 'error';
		$action['text'] = 'ลิงค์ของคุณหมดอายุแล้วค่ะ กรุณากรอกอีเมล์เพื่อขอรับลิงค์ในการเปลี่ยนรหัสผ่านใหม่ค่ะ.';
	}
}

?>

<html>
	<head>
		<?php include 'page_script.php';  ?>
	</head>
	<body>
		<?php include 'nav_bar.php';  
		if ($action['result'] == 'error') {
			echo '<div class="alert alert-danger container" role="alert"><label>'.$action['text'].'</label></div>';
		}else if ($action['result'] == 'success'){
			echo '
			<script>
				$(document).ready(function() {
					swal({   
						title: "เปลี่ยนรหัสผ่านเรียบร้อยค่ะ",   
						text: "",   
						type: "success",  
						confirmButtonText: "ตกลง",   
						closeOnConfirm: false 
					}, function(){   
						document.location.href = "index.php"; 
					});
				});
			</script>
			';
		}
		?>
	<div class="container" style="padding-top:50px;">
	<div class="jumbotron">
		<center><h2>เปลี่ยนรหัสผ่าน</h2></center><br />
		 <form class="form-horizontal" method="post">
		 	<input type="hidden" name="email" value="<?php echo $_GET['email']; ?>">
		 	<input type="hidden" name="key" value="<?php echo $_GET['key']; ?>">
			<div class="form-group">
				<label class="col-sm-2 control-label">รหัสผ่านใหม่</label>
				<div class="col-sm-10">
					<input type="password" class="form-control" name="newpass" placeholder="">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">ยืนยันรหัสผ่านใหม่</label>
				<div class="col-sm-10">
					<input type="password" class="form-control" name="confirm_newpass" placeholder="">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-primary" name="submit_reset_password_form">เปลี่ยนรหัสผ่าน</button>
				</div>
			</div>
		</form>
	</div>
</div>


		<?php include 'modal.php';  ?>
				<?php include 'footer.php';  ?>

				<script src="js/core.js"></script>
				<script type="text/javascript">

						function runScript(e) {
								if (e.keyCode == 13) {
										searchURL();
								}
						}

				</script>
				<script src="dist/sweetalert.min.js"></script>
				<link rel="stylesheet" href="dist/sweetalert.css">
		</body>
</html>

