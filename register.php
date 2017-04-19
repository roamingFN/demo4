<?php
include 'connect.php';
include 'inc/php/functions.php';

//setup some variables/arrays
$action = array();
$action['result'] = null;

$text = array();

//check if the form has been submitted
if(isset($_POST['signup'])){

	$email=$_POST['email'];
	$password=$_POST['password'];
	$firstname=$_POST['firstname'];
	$lastname=$_POST['lastname'];
	$phone=$_POST['phone'];

	if(empty($email)){ $action['result'] = 'error'; array_push($text,'You forgot your email'); }
	if(empty($password)){ $action['result'] = 'error'; array_push($text,'You forgot your password'); }
	if(empty($firstname)){ $action['result'] = 'error'; array_push($text,'You forgot your firstname'); }
	if(empty($lastname)){ $action['result'] = 'error'; array_push($text,'You forgot your lastname'); }
	if(empty($phone)){ $action['result'] = 'error'; array_push($text,'You forgot your phone'); }

	// To protect MySQL injection for Security purpose
	$email = stripslashes($email);
	$password = stripslashes($password);
	$firstname = stripslashes($firstname);
	$lastname = stripslashes($lastname);
	$phone = stripslashes($phone);

	$email = mysql_real_escape_string($email);
	$password = mysql_real_escape_string($password);
	$firstname = mysql_real_escape_string($firstname);
	$lastname = mysql_real_escape_string($lastname);
	$phone = mysql_real_escape_string($phone);

	if($action['result'] != 'error'){

		//encode password
		$password = sha1($password);

		$duplicate_email = mysql_num_rows(mysql_query("select * from customer where customer_email = '$email'"));

		if ($duplicate_email > 0) {

			$action['result'] = 'error';
			array_push($text,'Email '.$email.' is already exist, Please try to use another email.');
		
		}else{ 

			$add = mysql_query("insert into customer (customer_firstname,customer_lastname,customer_phone,customer_email,passwd,add_datetime) 
					  values ('$firstname','$lastname','$phone','$email','$password',NOW())");
		
			if ($add) {

				//get the new user id
				$userid = mysql_insert_id();

				//update customer code
				$cus_code = "O2E".str_pad($userid, 4, '0', STR_PAD_LEFT);
				$update_customer_code = mysql_query("update customer 
					set customer_code = '$cus_code', add_user_id = '".$cus_code."' 
					where customer_id = '$userid'");
				
				//create a random key
				$key = $firstname . $email . date('mY');
				$key = md5($key);
				
				//add confirm row
				$confirm = mysql_query("insert into `customer_confirm` values(NULL,'$userid','$key','$email')");	

				if ($confirm) {
					
					//include the swift class
					include_once 'inc/php/swift/swift_required.php';
				
					//put info into an array to send to the function
					$info = array(
						'username' => $firstname,
						'email' => $email,
						'key' => $key);
				
					//send the email
					if(send_email($info)){
									
						//email sent
						$action['result'] = 'success';
						array_push($text,'ขอบคุณสำหรับการสมัครสมาชิก กรุณาตรวจสอบอีเมลของลูกค้าที่ทำการสมัครสมาชิก เพื่อทำการเข้าสู่ระบบ <BR><BR>หมายเหตุ อีเมลที่ทำการเข้าสู่ระบบอาจจะอยู่ใน ช่องถังขยะ');
					
					}else{
						
						//firstname mayby thai lang. it's incorrent to make subject info name
						$info = array(
						'username' => $email,
						'email' => $email,
						'key' => $key);
				
						//send the email
						if(send_email($info)){
										
							//email sent
							$action['result'] = 'success';
							array_push($text,'ขอบคุณสำหรับการสมัครสมาชิก กรุณาตรวจสอบอีเมลของลูกค้าที่ทำการสมัครสมาชิก เพื่อทำการเข้าสู่ระบบ <BR><BR>หมายเหตุ อีเมลที่ทำการเข้าสู่ระบบอาจจะอยู่ใน ช่องถังขยะ');
						
						}else{
							$action['result'] = 'error';
							array_push($text,'ไม่สามารถส่งข้อความไปยังอีเมลที่สมัครได้');
						}
					
					}

				}else{
					
					$action['result'] = 'error';
					array_push($text,'Confirm row was not added to the database. Reason: ' . mysql_error());
					
				}

				//session_start(); 
				//$_SESSION['login_user']=$email;
				//header("Location: index.php");
			}else{

				$action['result'] = 'error';
				array_push($text,'User could not be added to the database. Reason: ' . mysql_error());
			}	
		}

	}

	$action['text'] = $text;
}

?>

<!DOCTYPE html>
<html>
  <head>
    <?php include 'page_script.php';  ?>
  </head>
  <body>
    <?php include 'nav_bar.php';  ?>

<div class="container" style="padding-top:50px;">
	<div class="jumbotron">
	    <p><?php echo show_errors($action); ?></p> 
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

