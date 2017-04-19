<?php
	include 'connect.php';
	include 'session.php';
	include 'inc/php/functions_statusConvert.php';

	if (!empty($_POST['change_password_request'])) {

		$old_password 			= $_POST['old_password'];
		$new_password 			= $_POST['new_password'];
		$confirm_new_password 	= $_POST['confirm_new_password'];

		if(empty($old_password)){ $error_text .= '<li>คุณยังไม่ได้กรอกรหัสผ่านปัจจุบัน</li>';}
		if(empty($new_password)){ $error_text .= '<li>คุณยังไม่ได้กรอกรหัสผ่านใหม่</li>';}
		if(empty($confirm_new_password)){ $error_text .= '<li>คุณยังไม่ได้กรอกยืนยันรหัสผ่านใหม่</li>';}

		$old_password 			= stripcslashes($old_password);
		$new_password 			= stripcslashes($new_password);
		$confirm_new_password 	= stripcslashes($confirm_new_password);

		$old_password 			= mysql_real_escape_string($old_password);
		$new_password 			= mysql_real_escape_string($new_password);
		$confirm_new_password 	= mysql_real_escape_string($confirm_new_password);

		if ($error_text == '') {

			if ($new_password == $confirm_new_password) {

				$qry_pwd = "SELECT passwd FROM customer WHERE customer_id = '$user_id'";

				if ($qry_pwd_res = $db2->query($qry_pwd)) {

						if ($pwd_row = $qry_pwd_res->fetch_assoc()) {

								if (sha1($old_password) == $pwd_row['passwd']) {

									$pass = sha1($new_password);
									$update = $db2->query("UPDATE customer SET passwd='$pass' WHERE customer_id = '$user_id'");

									if($update) {
										$message_text = '	เปลี่ยนรหัสผ่านเรียบร้อย';
									}else{
										$error_text = "fail".mysql_error();
									}

								}else{
									$error_text = '<li>รหัสผ่านปัจจุบันไม่ถูกต้อง</li>';
								}
						}
						$qry_pwd_res->close();
				}
			}else{
				$error_text = '<li>รหัสผ่านใหม่ไม่ตรงกัน</li>';
			}
		}

	}
?>
<!DOCTYPE html>
<html>
	<head>
		<?php include 'page_script.php';  ?>
	</head>
	<body>
		<?php include 'nav_bar.php';  ?>
	
	<div class="content manage">
		<?php include 'left_menu.php' ?>
		<div class="menu-content col3">
			<h1>เปลี่ยนรหัสผ่าน</h1>
			<form method="post" action="change_password.php" class="form-horizontal">

				<div class="form-group">
					<label for="old_password" class="col-md-3 control-label">รหัสผ่านปัจจุบัน</label>
									<div class="col-md-8">
											<input type="password" class="form-control" name="old_password" id="old_password" >
									</div>
							</div>

								<div class="form-group">
					<label for="new_password" class="col-md-3 control-label">รหัสผ่านใหม่</label>
									<div class="col-md-8">
											<input type="password" class="form-control" name="new_password" id="new_password" >
									</div>
							</div>

								<div class="form-group">
					<label for="confirm_new_password" class="col-md-3 control-label">ยืนยันรหัสผ่านใหม่</label>
									<div class="col-md-8">
											<input type="password" class="form-control" name="confirm_new_password" id="confirm_new_password" >
									</div>
							</div>

								<div class="form-group">
									<label for="email" class="col-md-3 control-label"></label>
									<div class="col-md-8">
										<button type="submit" class="button" name="change_password_request" value="Submit">บันทึก</button>
									</div>
								</div>

			</for

			m>
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

