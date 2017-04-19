<?php
	include 'connect.php';
	include 'session.php';
	include 'inc/php/functions_statusConvert.php';

$error = '';
if($action = isset($_GET['action']) && $_GET['action'] != '' && isset($_POST['withdraw_submit'])){
	//create new topup requset
	if ($action == 'withdraw_requset') {
		//$withdraw_date 		= date("m/d/Y h:i:s a", time());
		$withdraw_account 	= $_POST['withdraw_account'];
		$withdraw_amount 	= $_POST['withdraw_amount'];
		$otp_num 			= $_POST['otp_num'];
		$otp_ref 			= $_POST['otp_ref'];
		$notes 				= $_POST['notes'];

 
		if(empty($withdraw_account)){ $error .= 'คุณยังไม่ได้เลือกบัญชี ';}
		if(empty($withdraw_amount)){ $error .= 'คุณยังไม่ได้กรอกจำนวนเงิน ';}
		if(empty($otp_num)){ $error .= 'คุณยังไม่ได้กรอกหมายเลข OTP ';}


		$withdraw_account 	= stripcslashes($withdraw_account);
		$withdraw_amount 	= stripcslashes($withdraw_amount);
		$otp_num 			= stripcslashes($otp_num);
		$otp_ref 			= stripcslashes($otp_ref);
		$notes 				= stripcslashes($notes);

		$withdraw_account 	= mysql_real_escape_string($withdraw_account);
		$withdraw_amount 	= mysql_real_escape_string($withdraw_amount);
		$otp_num 			= mysql_real_escape_string($otp_num);
		$otp_ref 			= mysql_real_escape_string($otp_ref);
		$notes 				= mysql_real_escape_string($notes);


		if ($error == '') {
			$select_otp = mysql_query("select * from otp where customer_id = '$user_id' and otp_ref = '$otp_ref'
				and otp_key = '$otp_num'");

			if (mysql_num_rows($select_otp) > 0) {
				$otp_id = mysql_fetch_array($select_otp);
				$otp_id_row = $otp_id['otp_id'];
				$delete_otp = mysql_query("delete from otp where otp_id = '$otp_id_row'");

				//select last withdraw number --(2)
				$select_withdraw_number = mysql_query("SELECT withdraw_number
																					FROM customer_request_withdraw 
																					WHERE YEAR(withdraw_date) = '".date('Y')."' 
																					ORDER BY withdraw_request_id DESC");

				//สร้าง withdraw ใหม่ --(1)
				$add_withdraw_req = mysql_query("insert into 
					customer_request_withdraw(customer_id,customer_bank_account_id,withdraw_amount,withdraw_date,comment)
					values('$user_id','$withdraw_account','$withdraw_amount',NOW(),'$notes')");
				$withdraw_id = mysql_insert_id();

				//update withdraw number -- (2)
				// echo "num row = ".mysql_num_rows($select_withdraw_number);
				if (mysql_num_rows($select_withdraw_number) > 0) {
					//เอา order_number เก่ามา +1
					$select_withdraw_number_row = mysql_fetch_array($select_withdraw_number);
					$old_withdraw_number = $select_withdraw_number_row[0];
					// echo "old_withdraw_number=".$old_withdraw_number;
					$number = (int)substr($old_withdraw_number, 3);
					$withdraw_number = "W".date("y").str_pad($number+1 ,7, "0", STR_PAD_LEFT);
					// echo "new_withdraw_number=".$withdraw_number;
					$update_number = mysql_query("update customer_request_withdraw set withdraw_number='$withdraw_number' where withdraw_request_id = '$withdraw_id'");
				}else{
					//สร้าง withdraw_number ใหม่
					$withdraw_number = "W".date("y").str_pad(1 ,7, "0", STR_PAD_LEFT);
					// echo "create_new=".$withdraw_number;
					$update_number = mysql_query("update customer_request_withdraw set withdraw_number='$withdraw_number' where withdraw_request_id = '$withdraw_id'");
				}

				if ($add_withdraw_req) {
					$message_text = "<label>แจ้งถอนเงินเรียบร้อย</label> การดำเนินการจะใช้เวลาประมาน 7-10 วัน";
				}else{
					$error_text = "fail".mysql_error();
				}
			}else
			{
				$error_text = '<label>เกิดข้อผิดพลาด : </label>หมายเลข OTP ไม่ถูกต้อง';
			}

		}else{
			$error_text = $error;
		}
	}

}

if (isset($_GET['mode']) && $_GET['mode'] == 'edit') {

	$id = $_GET['withdrawid'];
	$mode = 'edit';

	//update data
	if (isset($_POST['withdraw_submit'])) {
		$withdraw_account = $_POST['withdraw_account'];
		$withdraw_amount 	= $_POST['withdraw_amount'];
		$otp_num 			= $_POST['otp_num'];
		$otp_ref 			= $_POST['otp_ref'];
		$notes 				= $_POST['notes'];

		if(empty($withdraw_account)){ $error .= 'คุณยังไม่ได้เลือกบัญชี ';}
		if(empty($withdraw_amount)){ $error .= 'คุณยังไม่ได้กรอกจำนวนเงิน ';}
		if(empty($otp_num)){ $error .= 'คุณยังไม่ได้กรอกหมายเลข OTP ';}

		$withdraw_account 	= stripcslashes($withdraw_account);
		$withdraw_amount 	= stripcslashes($withdraw_amount);
		$otp_num 			= stripcslashes($otp_num);
		$otp_ref 			= stripcslashes($otp_ref);
		$notes 				= stripcslashes($notes);

		$withdraw_account 	= mysql_real_escape_string($withdraw_account);
		$withdraw_amount 	= mysql_real_escape_string($withdraw_amount);
		$otp_num 			= mysql_real_escape_string($otp_num);
		$otp_ref 			= mysql_real_escape_string($otp_ref);
		$notes 				= mysql_real_escape_string($notes);

		if ($error == '') {
			$select_otp = mysql_query("select * from otp where customer_id = '$user_id' and otp_ref = '$otp_ref'
				and otp_key = '$otp_num'");

			if (mysql_num_rows($select_otp) > 0) {
				$otp_id = mysql_fetch_array($select_otp);
				$otp_id_row = $otp_id['otp_id'];
				$delete_otp = mysql_query("delete from otp where otp_id = '$otp_id_row'");

				//update in database
				$update_withdraw_req = mysql_query("update customer_request_withdraw 
					set customer_bank_account_id = '$withdraw_account',
					withdraw_amount = '$withdraw_amount',
					comment = '$notes'
					where withdraw_request_id = '$id'
					and customer_id = '$user_id'
					and withdraw_status = 0 
					");

				if ($update_withdraw_req) {
					$message_text = "แก้ไขข้อมูลเรียบร้อย";
				}else{
					$error_text = "ไม่สามารถแก้ไขข้อมูลได้ - ".mysql_error();
				}
			}else{
				$error_text = '<label>เกิดข้อผิดพลาด : </label>หมายเลข OTP ไม่ถูกต้อง';
			}

		}else{
			$error_text = $error;
		}

	}

	//preparing data
	$select = mysql_query("select * from customer_request_withdraw 
		where withdraw_request_id = '$id' and customer_id = '$user_id' and withdraw_status = 0");
	if (mysql_num_rows($select) > 0) {
		$row = mysql_fetch_array($select);
		$edit_id = $row['withdraw_request_id'];
		$edit_time = $row['withdraw_date'];
		$edit_account_id = $row['customer_bank_account_id'];
		$edit_amount = $row['withdraw_amount'];
		$edit_note = $row['comment'];
	}else {
		$error_text = "รายการที่คุณเลือกไม่มีอยู่หรือไม่สามารถแก้ไขได้ค่ะ";
		header("Location: withdraw_history.php");
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
	<h1>ถอนเงิน</h1>

	<?php $action=$mode=="edit"?"":"withdraw.php?action=withdraw_requset" ?>
	<form role="form" action="<?php echo $action; ?>" method="post">
		<table class="content-light">
			<tr><th>วัน/เวลา</th><td style="width:300px;"><input type="text" class="form-control" name="withdraw_date" value=
			"<?php 
				if ($mode == 'edit') {
					echo date("d/m/Y h:i:s a", strtotime($edit_time));
				}else{
					echo date("d/m/Y h:i:s a", time()); 
				}
			?>" disabled>
			</td><th></th>
				<td rowspan="3">
					<div class="right">
						<?php 
						$aproved_amount = 0;
						$unapprove_amount = 0;
						$select_topup = mysql_query("select * from customer_request_topup where customer_id = '$user_id'");
						while ($row = mysql_fetch_array($select_topup)) {
							if ($row['topup_status']==0) {
								$unapprove_amount += $row['topup_amount'];
							}else if ($row['topup_status']==1) {
								$aproved_amount += $row['usable_amout'];
							}
						}
						?>
						<div class="content-bordered">
						ยอดเงินคงเหลือ : <b><?php echo number_format($aproved_amount,2); ?> THB</b>
						</div>
					</div>
					<br>
					<i class="material-icons">info</i>
					ในกรณีกดถอนเงิน เมื่อเจ้าหน้าที่ตรวจสอบผ่านแล้ว ระบบจะตัดเงินคงเหลือทันที แต่เงินจะเข้าบัญชีลูกค้าในช่วงระยะเวลาดำเนินการ 7- 10 วัน
				</td>
			</tr>
			<tr><th>เลขที่บัญชี</th><td><a href="add_account.php"><i class="material-icons">add_circle</i> เพิ่มบัญชี</a></td></tr>
			<tr><th></th><td>
				<?php 
			  		$select_cus_bank = mysql_query("select * from customer_bank_account where customer_id = '$user_id'");
			  		if (mysql_num_rows($select_cus_bank) > 0) {
			  			echo '<select class="form-control" name="withdraw_account">';
			  			while($row = mysql_fetch_array($select_cus_bank)){
			  				//formatBankAccNo($row['account_no'])
			  				echo "<option value='".$row['bank_account_id']."' ";
			  				if ($edit_account_id == $row['bank_account_id']) {
									echo " selected ";
								}
			  				echo " >".$row['account_no']."</option>";
			  			}
			  			echo '</select>';
			  		}else{
			  			echo "<ul class='error'><li>กรุณาเพิ่มเลขบัญชี</li></ul>";
			  		}

			  	?>
			</td></tr>
			<tr><th>ยอดเงินที่ต้องการถอน</th><td><input type="text" class="form-control" name="withdraw_amount"
							 placeholder="จำนวนเงิน" aria-describedby="basic-addon2" onkeypress="return ((event.charCode >= 48 && event.charCode <= 57)||event.charCode == 46)" value="<?php echo $edit_amount; ?>"></td></tr>
			<tr><td>ขอรหัสรักษาความปลอดภัย (OTP)</td><td><a href="#" class="button" onclick="requestOTP()">ส่งรหัส OTP</a></td></tr>
			<tr><th>รหัส OTP</th><td><input type="text" name="otp_num" class="form-control" placeholder="รหัสรักษาความปลอดภัย OTP"/>
			<input type="hidden" class="form-control" name="otp_ref" id="otp_ref" value="">
			</td></tr>
			<tr><th></th><td>รหัสอ้างอิง : <span id="txt_otp_ref">-</span><br />(OTP จะส่งเข้า Email ที่ใช้ Login)</td></tr>
			<tr><th>หมายเหตุ</th><td colspan="2"><textarea placeholder="หมายเหตุ" name="notes"><?php echo $edit_note; ?></textarea>
			</td></tr>
			<tr><th></th><td><button type="submit" value="Submit" name="withdraw_submit" >
				<?php
					if ($mode == 'edit') {
					 	echo "แก้ไขข้อมูล";
					}else{
						echo "ตกลง";
					}
				?>
			</button></td></tr>
		</table>
	</form>
	
</div>

<!-- Modal OTP REQUEST -->
<div class="modal fade" id="request_otp" role="dialog">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title">ส่งรหัสรักษาความปลอดภัย (OTP)</h4>
    </div>
    <div class="modal-body">
      <form id="loginform" class="form-horizontal" role="form" action="" method="post" > 
        <span id="show_otp_request" name="show_otp_request"></span>
      </form> 
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
    </div>
  </div>
</div>
</div>
	
</div>

<script type="text/javascript">
	function requestOTP(){
		$('#request_otp').modal('show');
		var req;
		if (window.XMLHttpRequest) {
			req = new XMLHttpRequest();
		}
		else if (window.ActiveXObject) {
			req = new ActiveXObject("Microsoft.XMLHTTP"); 
		}
		else{
			alert("Browser error");
			return false;
		}
		req.onreadystatechange = function()
		{
			if (req.readyState == 4) {
				var resultarea = document.getElementById('show_otp_request');
				resultarea.innerHTML = req.responseText;
				var ref_num =  document.getElementById('otp_ref_source').innerHTML;
				document.getElementById('txt_otp_ref').innerHTML = ref_num;
				document.getElementById('otp_ref').value = ref_num;
			}
			else
			{
				var resultarea = document.getElementById('show_otp_request');
				resultarea.innerHTML = "<img src=progress_bar.gif>";

			}
		}

		req.open("GET", "request_otp.php", true);
		req.send(null); 

	}
</script>
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