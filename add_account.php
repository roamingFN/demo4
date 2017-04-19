<?php
	include 'connect.php';
	include 'session.php';

$error = '';
if($action = isset($_GET['action']) && $_GET['action'] != ''){
	//create new topup requset
	if ($action == 'add') {
		$bank_name 		= $_POST['bank_name'];
		$account_name 	= $_POST['account_name'];
		$account_no 	= $_POST['account_no'];
		$branch 		= $_POST['branch'];
		$note 			= $_POST['note'];
		$otp_ref 		= $_POST['otp_ref'];
		$otp_num 		= $_POST['otp_num'];
		$countAdd    	= count($account_no);

		$havedata = false;
		$i = 0;
		while (($havedata == false) && ($i < $countAdd)) {
			
			if ($bank_name[$i] != '' && $account_name[$i] != '' && $account_no[$i] != '' && $branch[$i] != '') {
				$havedata = true;
				$error = '';
			}
			$i++;
		}

		if ($havedata == false) {
			
			if(empty($bank_name))	{ $error .= 'you forgot bank_name on line ';}
			if(empty($account_name)){ $error .= 'you forgot account_nameon line ';}
			if(empty($account_no))	{ $error .= 'you forgot account_noon line ';}
			if(empty($branch))		{ $error .= 'you forgot branchon line ';}
			if(empty($otp_num))		{ $error .= 'you forgot otp_num ';}
			
		}

		for ($i = 0; $i < $countAdd; $i++) {

			$bank_name[$i] 		= stripcslashes($bank_name[$i]);
			$account_name[$i] 	= stripcslashes($account_name[$i]);
			$account_no[$i] 	= stripcslashes($account_no[$i]);
			$branch[$i] 		= stripcslashes($branch[$i]);
			$note[$i] 			= stripcslashes($note[$i]);

			$bank_name[$i] 		= mysql_real_escape_string($bank_name[$i]);
			$account_name[$i] 	= mysql_real_escape_string($account_name[$i]);
			$account_no[$i] 	= mysql_real_escape_string($account_no[$i]);
			$branch[$i] 		= mysql_real_escape_string($branch[$i]);
			$note[$i] 			= mysql_real_escape_string($note[$i]);
		}


		if ($error == '') {
			$select_otp = mysql_query("select * from otp where customer_id = '$user_id' and otp_ref = '$otp_ref'
				and otp_key = '$otp_num'");

			if (mysql_num_rows($select_otp) > 0) {
				$otp_id = mysql_fetch_array($select_otp);
				$otp_id_row = $otp_id['otp_id'];

				$delete_otp = mysql_query("delete from otp where otp_id = '$otp_id_row'");

				for ($i = 0; $i < $countAdd; $i++) {

					if ($account_no[$i] != '') {
						$add_bank = mysql_query("insert into 
							customer_bank_account (bank_account_status, customer_id, bank_name, bank_branch, 
									account_no, account_name, user_notes)
							values('0','$user_id','$bank_name[$i]','$branch[$i]',
									'$account_no[$i]','$account_name[$i]','$note[$i]')", $connection);
					
						if ($add_bank) {
							echo '<div class="alert alert-success container" role="alert"><label>เพิ่มบัญชีใหม่เรียบร้อย</label></div>';
						}else{
							echo "fail".mysql_error();
						}
					}

				}

			}else{
				echo '<div class="alert alert-danger container" role="alert"><label>เกิดข้อผิดพลาด</label> หมายเลข OTP ไม่ถูกต้อง</div>';
			}

		}else{
			echo $error;
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
	<h1>เพิ่มเลขที่บัญชี</h1>

	<form role="form" method="post" action="add_account.php?action=add">
	<table class='content-grid'>
		<tr>
			<th>ลำดับ</th>
			<th>ธนาคาร</th>
			<th>ชื่อบัญชี</th>
			<th>เลขบัญชี (ใส่เฉพาะตัวเลข)</th>
			<th>สาขา</th>
			<th>หมายเหตุ</th>
		</tr>
		

	<?php for ($i=0; $i < 5 ; $i++) { ?>
		<tr>
			<td><?php echo $i+1; ?></td>
			<td>
				<select name="bank_name[]" class="form-control">
					<option value="   ">ไม่เลือก</option>
				  	<option value="ธนาคารกรุงเทพ">ธนาคารกรุงเทพ</option>
				  	<option value="ธนาคารกสิกรไทย">ธนาคารกสิกรไทย</option>
				  	<option value="ธนาคารไทยพาณิชย์">ธนาคารไทยพาณิชย์</option>
				  	<option value="ธนาคารกรุงไทย">ธนาคารกรุงไทย</option>
				  	<option value="ธนาคารกรุงศรีอยุธยา">ธนาคารกรุงศรีอยุธยา</option>
				  	<option value="ธนาคารเกียรตินาคิน">ธนาคารเกียรตินาคิน</option>
				  	<option value="ธนาคารทหารไทย">ธนาคารทหารไทย</option>
				  	<option value="ธนาคารธนชาต">ธนาคารธนชาต</option>
				</select>
			</td>

			<td><input type="text" name="account_name[]" class="form-control"></td>
			<td><input type="text" name="account_no[]" class="form-control" 
				onkeypress="return isAccount(event)" ></td>
			<td><input type="text" name="branch[]" class="form-control"></td>
			<td><input type="text" name="note[]" class="form-control"></td>
		</tr>
	<?php } ?>
	</table>
	
	<hr>
	<p><button type="button" id="btn_next" onclick="requestOTP()">ขอรหัส OTP</button></p>
	<small>(รหัส OTP จะส่งไปยัง Email ที่ใช้ Login)</small>
	<br /><br />
	<div id="otp_section">
		
			<table class='content-grid'>
				<tr>
					<th colspan="2">กรุณาระบุรหัสรักษาความปลอดภัย OTP เพื่อยืนยันการทำรายการ</th>
				</tr>
				<tr>
					<td>Ref. Code</td>
					<td>
						<span id="txt_otp_ref">-</span>
						<input type="hidden" class="form-control" name="otp_ref" id="otp_ref" value="">
					</td>
				</tr>
				<tr>
					<td>รหัสรักษาความปลอดภัย OTP</td>
					<td><input type="text" class="form-control" name="otp_num"></td>
				</tr>
			</table>
			<br />
			<button type="submit" name="add_account" value="Submit">ยืนยัน</button>
		
	</div>
	</form>

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

	function isAccount(evt) {
		//Enable arrow for firefox.
		if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
		    if (evt.keyCode == 8 || evt.keyCode == 46 || evt.keyCode == 37 || evt.keyCode == 39) {
			    return true;
			}
		}

	    evt = (evt) ? evt : window.event;
	    var charCode = (evt.which) ? evt.which : evt.keyCode;

	    if (charCode > 31 && (charCode < 48 || charCode > 57 )) {
	        return false;
	    }
	    return true;
	}
</script>
	
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
