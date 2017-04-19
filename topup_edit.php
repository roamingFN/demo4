<?php
	include 'connect.php';
	include 'session.php';
	include 'inc/php/functions_statusConvert.php';

$error = '';
if($action = isset($_GET['action']) && $_GET['action'] != ''){
	//create new topup requset
	if (!empty($_POST['update-topup'])) {

		$topup_id 			= $_POST['topup_id'];
		$topup_method 		= $_POST['topup_method'];
		$balance 			= $_POST['balance'];
		$transfer_date 		= $_POST['transfer_date'];
		$transfer_date_text 		= $_POST['transfer_date'];
		$transfer_time 		= $_POST['transfer_time'];
		$transfer_time_text 		= $_POST['transfer_time'];
		$transfer_channel 	= $_POST['transfer_channel'];
		$transfer_bill 		= $_FILES["transfer_bill"]["name"];
		$notes 				= $_POST['notes'];

		if(empty($topup_method)){ $error .= '<li>คุณยังไม่ได้เลือกบัญชีที่โอนเงินเข้า</li>';}
		if(empty($balance)){ $error .= '<li>คุณยังไม่ได้กรอกจำนวนเงิน</li>';}
		if(empty($transfer_date)){ $error .= '<li>คุณยังไม่ได้กรอกวันที่โอนเงิน</li>';}
		if(empty($transfer_time)){ $error .= '<li>คุณยังไม่ได้กรอกเวลาโอนเงิน</li>';}
		if(empty($transfer_channel)){ $error .= '<li>คุณยังไม่ได้กรอกช่องทางการโอนเงิน</li>';}

		$topup_method 		= stripcslashes($topup_method);
		$balance 			= stripcslashes($balance);
		$transfer_date 		= stripcslashes($transfer_date);
		$transfer_time 		= stripcslashes($transfer_time);
		$transfer_channel 	= stripcslashes($transfer_channel);
		$transfer_bill 		= stripcslashes($transfer_bill);
		$notes 				= stripcslashes($notes);

		$topup_method 		= mysql_real_escape_string($topup_method);
		$balance 			= mysql_real_escape_string($balance);
		$transfer_date 		= mysql_real_escape_string($transfer_date);
		$transfer_time 		= mysql_real_escape_string($transfer_time);
		$transfer_channel 	= mysql_real_escape_string($transfer_channel);
		$transfer_bill 		= mysql_real_escape_string($transfer_bill);
		$notes 				= mysql_real_escape_string($notes);

		$transfer_date = str_replace('/', '-', $transfer_date);
		$transfer_date = date('m/d/Y', strtotime($transfer_date));
		$transfer_datetime 	= $transfer_date." ".$transfer_time;
		//echo $transfer_datetime;

		//remove comma
		$balance 			= str_replace(',', '', $balance);

		if ($error == '') {

			if ($transfer_bill!=null) {

				$target_dir = "uploads/".$user_id;
				if (!is_dir($target_dir)&& strlen($target_dir)>0) {
					mkdir($target_dir, "0777");
					chmod($target_dir, 0777);
				}
				$target_file = $target_dir . basename($_FILES["transfer_bill"]["name"]);
				$uploadOk = 1;
				$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
				// Check if image file is a actual image or fake image
				if(isset($_POST["topup-request-submit"])) {
				    $check = getimagesize($_FILES["transfer_bill"]["tmp_name"]);
				    if($check !== false) {
				        //echo "File is an image - " . $check["mime"] . ".";
				        $uploadOk = 1;
				    } else {
				        $error_text = "File is not an image.";
				        $uploadOk = 0;
				    }
				}
				// Check if file already exists
				$target_file = file_newname($target_dir,basename($_FILES["transfer_bill"]["name"]));

				// Check file size
				if ($_FILES["transfer_bill"]["size"] > 500000) {
				    $error_text = "Sorry, your file is too large.";
				    $uploadOk = 0;
				}
				// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
				&& $imageFileType != "gif" ) {
				    $error_text = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
				    $uploadOk = 0;
				}
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
				    $error_text = "Sorry, your file was not uploaded.";
				// if everything is ok, try to upload file
				} else {
				    if (move_uploaded_file($_FILES["transfer_bill"]["tmp_name"], $target_file)) {
				        //echo "The file ". basename( $_FILES["transfer_bill"]["name"]). " has been uploaded.";
				    } else {
				        $error_text = "Sorry, there was an error uploading your file.";
				    }
				}

				//## update topup with upload file ##

				//get customer_request_topup amount
				$query_topup = mysql_query("select * from customer_request_topup where 
					customer_id = '$user_id' and topup_id = '$topup_id'");
				$query_topup_row = mysql_fetch_array($query_topup);
				
				if ($query_topup_row['topup_status']==0 && $query_topup_row['used']==0) {

					//change customer current_amount, wait_amount
					$old_topup_amount = $query_topup_row['topup_amount'];
					$query_customer = mysql_query("select * from customer where customer_id = '$user_id'");
					$query_customer_row = mysql_fetch_array($query_customer);
					$customer_current_amount = $query_customer_row['current_amount'];
					$customer_wait_amount = $query_customer_row['wait_amount'];

					$new_customer_current_amount =  $customer_current_amount - $old_topup_amount + $balance;
					$new_customer_wait_amount = $customer_wait_amount - $old_topup_amount + $balance;

					$update_customer = mysql_query("update customer set current_amount = '$new_customer_current_amount',
						wait_amount = '$new_customer_wait_amount' where customer_id = '$user_id'");


					//update customer_request_topup
					$update_topup_req = mysql_query("update customer_request_topup
													set topup_bank = '$topup_method',
													topup_amount = '$balance', usable_amout = '$balance', 
													topup_date = STR_TO_DATE('$transfer_datetime','%c/%e/%Y %T'),
													transfer_method = '$transfer_channel',
													bill_file_directory = '$target_file',
													customer_notes = '$notes'
													where topup_id = '$topup_id'
													and customer_id = '$user_id'");

					//update statement
					$update_statement = mysql_query("update customer_statement set debit = '$balance' 
						where customer_id = '$user_id' and topup_id = '$topup_id'");

				}else{
					$error_text = '<label>เกิดข้อผิดพลาด</label> ไม่สามารถแก้ไขข้อมูลได้เนื่องจากยอดเงินถูกใช้งาน หรือผ่านการอนุมัติจากเจ้าหน้าที่แล้ว</div>';

				}

				if ($update_topup_req&&$update_customer&&$update_statement) {
					if ($_POST['redirect'] == "topup") {
						$location = "Location: topup.php?show_topup_id=$topup_id&transfer_date_text=$transfer_date_text&transfer_time_text=$transfer_time_text&balance=$balance&transfer_channel=$transfer_channel&notes=$notes&topup_method=$topup_method";
						header($location);
					}
					$message_text = '<div class="alert alert-success container" role="alert"><label>แก้ไขข้อมูลการเติมเงินเรียบร้อย</label>  </div>';
					$isEditSuccess = true;

				}else{
					//echo "fail".mysql_error();
				}

			}else{

				//## update topup without upload file ##

				//get customer_request_topup amount
				$query_topup = mysql_query("select * from customer_request_topup where 
					customer_id = '$user_id' and topup_id = '$topup_id'");
				$query_topup_row = mysql_fetch_array($query_topup);

				if ($query_topup_row['topup_status']==0 && $query_topup_row['used']==0) {

					//change customer current_amount, wait_amount
					$old_topup_amount = $query_topup_row['topup_amount'];
					$query_customer = mysql_query("select * from customer where customer_id = '$user_id'");
					$query_customer_row = mysql_fetch_array($query_customer);
					$customer_current_amount = $query_customer_row['current_amount'];
					$customer_wait_amount = $query_customer_row['wait_amount'];

					$new_customer_current_amount =  $customer_current_amount - $old_topup_amount + $balance;
					$new_customer_wait_amount = $customer_wait_amount - $old_topup_amount + $balance;

					$update_customer = mysql_query("update customer set current_amount = '$new_customer_current_amount',
						wait_amount = '$new_customer_wait_amount' where customer_id = '$user_id'");

					//update customer_request_topup
					$update_topup_req = mysql_query("update customer_request_topup
												set topup_bank = '$topup_method',
												topup_amount = '$balance', usable_amout = '$balance', 
												topup_date = STR_TO_DATE('$transfer_datetime','%c/%e/%Y %T'),
												transfer_method = '$transfer_channel',
												customer_notes = '$notes'
												where topup_id = '$topup_id'
												and customer_id = '$user_id'");

					//update statement
					$update_statement = mysql_query("update customer_statement set debit = '$balance', 
						statement_date = STR_TO_DATE('$transfer_datetime','%c/%e/%Y %T') 
						where customer_id = '$user_id' and topup_id = '$topup_id'");

				}else{
					$error_text = '<label>เกิดข้อผิดพลาด</label> ไม่สามารถแก้ไขข้อมูลได้เนื่องจากยอดเงินถูกใช้งาน หรือผ่านการอนุมัติจากเจ้าหน้าที่แล้ว';
				}

				if ($update_topup_req&&$update_customer&&$update_statement) {
					if ($_POST['redirect'] == "topup") {
						$location = "Location: topup.php?show_topup_id=$topup_id&transfer_date_text=$transfer_date_text&transfer_time_text=$transfer_time_text&balance=$balance&transfer_channel=$transfer_channel&notes=$notes&topup_method=$topup_method";
						header($location);
					}
					$message_text = '<label>แก้ไขข้อมูลการเติมเงินเรียบร้อย</label>';
					$isEditSuccess = true;
				}else{
					echo "fail".mysql_error();
				}
			}
		}else{
			$error_text = '<label>เกิดข้อผิดพลาด</label>'.$error.'</div>';
		}
	}
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include 'page_script.php';  ?>
    <style>
    	thead{
			color:#000;
		}
    </style>
  </head>
  <body>
    <?php include 'nav_bar.php';  ?>
	<div class="content manage">
		<?php include 'left_menu.php' ?>
		<div class="menu-content col3">
	<h1>แก้ไขรายการแจ้งโอนเงิน</h1>
		<?php
	$topup_id = $_GET['topup_id'];
	$redirect = $_GET['redirect'];
	$topup_id = stripcslashes($topup_id);
	$topup_id = mysql_real_escape_string($topup_id);
	$select_topup = mysql_query("select * from customer_request_topup where topup_id = '$topup_id'
		and customer_id = '$user_id' and used = 0 and topup_status = 0");
	if (mysql_num_rows($select_topup)>0) {
		$select_topup_row = mysql_fetch_array($select_topup);
	
	?>
	<h4>&nbsp เลขที่การเติมเงิน : <?php echo $select_topup_row['topup_number']; ?></h4>
	<form role="form" action="topup_edit.php?action=edit<?php echo "&topup_id=".$select_topup_row['topup_id']; ?>" method="post" name="from_topup_req" id="from_topup_req"  enctype="multipart/form-data">
	<table class='content-grid'>
		<tr>
			<th colspan="6" class="bg-primary">เลือกบัญชีที่โอนเงินเข้า</th>
		</tr>
	<?php 
	$select_payment_method = mysql_query("select * from bank_payment");
	if (mysql_num_rows($select_payment_method)>0) {
		while ($row = mysql_fetch_array($select_payment_method)) {
			echo "
			<tr>
				<td><input type='radio' name='topup_method' value='".$row['bank_id']."' ";
				if ($select_topup_row['topup_bank'] == $row['bank_id']) {
					echo "checked";
				}
				echo"></td>
				<td><img style='height:50px;' src='img/".$row['bank_img']."'></td>
				<td>".$row['bank_name_th']."</td>
				<td>".formatBankAccNo($row['account_no'])."</td>
				<td>".$row['account_name']."</td>
				<td>".$row['bank_branch']."</td>
			</tr>
			";
		}
	}
	?>
	</table>
	<br />

	<table class="content-light">
		<tr>
			<th>ยอดเงิน</th>
			<td><input type="text" value="<?php echo $select_topup_row['topup_amount']; ?>" class="form-control" placeholder="จำนวนเงินที่โอน" name="balance" 
			onkeypress="return isNumber(event)" /></td>
		</tr>
		<tr>
			<th>วันที่โอนเงิน</th>
			<td>
				<div class="input-group input-append date" >
                	<input type="text" id="mdate1" value="<?php echo date('d/m/Y', strtotime($select_topup_row['topup_date'])); ?>" class="form-control" name="transfer_date" placeholder="วันที่โอนเงิน" />
                	<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
            	</div>
			</td>
			<th>เวลาที่โอน</th>
			<td>
				<div class="input-group input-append date" id="timePicker">
                	<input type="text" id="time1" value="<?php echo date('G:i:s', strtotime($select_topup_row['topup_date'])); ?>" class="form-control" name="transfer_time" placeholder="เวลาที่โอนเงิน" 
                	onkeypress="return isTime(event)" />
                	<span class="input-group-addon add-on"><span class="glyphicon glyphicon-time"></span></span>
            	</div>
			</td>
		</tr>
		<tr>
			<th>ช่องทางการโอน</th>
			<td>
				<?php 
					$select_bank_payment_method = mysql_query("select * from bank_payment_method");
					if (mysql_num_rows($select_bank_payment_method)>0) {
						echo "<select name='transfer_channel' class='form-control'>";
						echo "<option value=''>ไม่เลือก</option>";
						while ($row = mysql_fetch_array($select_bank_payment_method)) {
							echo "<option value=".$row['bank_payment_method_name']." ";
							if ($select_topup_row['transfer_method'] == $row['bank_payment_method_name']) {
								echo " selected ";
							}
							echo ">".$row['bank_payment_method_name']."</option>";
						}
						echo "</select>";
					}

					?>
			</td>
		</tr>
		<tr>
			<th>หลักฐานการชำระเงิน</th>
			<td>
				<p>
					<?php
						if ($select_topup_row['bill_file_directory']!='') {
							echo "<img class='img img-thumbnail' src='".$select_topup_row['bill_file_directory']."' style='height:200px;'>";
						}
					?>
				</p>
				<input type="file" class="form-control" name="transfer_bill" id="transfer_bill">
			</td>
		</tr>
		<tr>
			<th>หมายเหตุ</th>
			<td><textarea placeholder="หมายเหตุ" name="notes"><?php echo $select_topup_row['customer_notes']; ?></textarea></td>
			<td colspan="2"><i class="material-icons">info</i><span style="color:red"> หลังจากท่านเติมเงินแล้ว กรุณาสั่งชำระเพื่อเป็นการยืนยันระบบจะได้ดำเนินการสั่งซื้อ หรือส่งสินค้า</span><br /> 
			<i class="material-icons">info</i><span style="color:red"> กรุณาแจ้งข้อมูล ยอดเงิน วันที่ ธนาคาร ให้ถูกต้องถ้าท่านแจ้งข้อมูลไม่ถูกต้องระบบ จะทำการยกเลิกให้ท่านตรวจสอบอีกครั้ง</span></td>
		</tr>
		<tr>
			<th></th>
			<td colspan="2">
			<input type="hidden" name="topup_id" value="<?php echo $select_topup_row['topup_id'];?>">
			<?php 
			if ($redirect == "topup") {
				echo '<button value="Submit" name="update-topup">ตกลง</button></a></td>
							<input type="hidden" name="redirect" value="topup">';
			}else{
				echo '<button value="Submit" name="update-topup">อัพเดทข้อมูล</button>&emsp;<a href="topup_history.php">&#10094; กลับไปหน้าประวัติการเติมเงิน</a></td>';
				if ($redirect != "") {
					echo '<input type="hidden" name="redirect" value="'.$redirect.'">';
				}
			}
			?>
			
		</tr>
	</table>
	
	
	</form>

	<?php 
	}else{
		echo '<div class="alert alert-danger container" role="alert"><label>เกิดข้อผิดพลาด</label> : ไม่สามารถแก้ไขรายการนี้ได้</div>';
	}
	?>
</div>

<script>
$(document).ready(function() {
    $('#mdate1').datepicker({
            dateFormat: 'dd/mm/yy',
            maxDate: new Date(),
            todayHighlight: true,
        })

    $('#time1').timepicker();

});

function isNumber(evt) {
	//Enable arrow for firefox.
	if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
	    if (evt.keyCode == 8 || evt.keyCode == 46 || evt.keyCode == 37 || evt.keyCode == 39) {
		    return true;
		}
	}

    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;

    //Enable dot.
    if (charCode == 46) { return true; };

    if (charCode > 31 && (charCode < 48 || charCode > 57 )) {
        return false;
    }
    return true;
}

function isTime(evt) {
	//Enable arrow for firefox.
	if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
	    if (evt.keyCode == 8 || evt.keyCode == 46 || evt.keyCode == 37 || evt.keyCode == 39) {
		    return true;
		}
	}

    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;

    //Enable semicolon.
    if (charCode == 58) { return true; };

    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

<?php
//$topup_id = 1;
if ($isEditSuccess) { 

	$bank_detail = mysql_query("select * from bank_payment where bank_id = '$topup_method' ");
	$row = mysql_fetch_array($bank_detail);

?>
$(document).ready(function() {
	swal({   
		title: "ยอดเงินที่ลูกค้าต้องการแจ้งเติมเงิน",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "แก้ไข",
		cancelButtonText: "ตกลง",
		closeOnConfirm: false,
		closeOnCancel: false,
		text: "<table width='100%'><tr><td class='text-left'>วันที่โอนเงิน : <?php echo $transfer_date_text; ?></td><td class='text-left'>เวลาที่โอนเงิน : <?php echo $transfer_time_text; ?></td></tr><tr><td class='text-left'>ยอดเงิน : <?php echo number_format($balance,2); ?></td><td class='text-left'>ช่องทางการโอน : <?php echo $transfer_channel; ?></td></tr><tr><td colspan='2' class='text-left'>หมายเหตุ : <?php echo "<br><textarea rows='2' cols='20' class='form-control' disabled>".$notes."</textarea>"; ?></td></tr></table><hr /><table width='100%'><tr><td colspan='3'><span style='color:#1874CD'>โดยโอนเข้าบัญชี</span></td></tr><tr><td rowspan='2'><img style='height:50px;border-radius:3' src='img/<?php echo $row["bank_img"]; ?>'></td><td class='text-left'><small><?php echo $row["account_name"]; ?></small></td class='text-left'><td><small><?php echo $row["bank_name_th"]; ?></small></td></tr><tr><td class='text-left'><small><?php echo $row["account_no"]; ?></small></td><td class='text-left'><small><?php echo $row["bank_branch"]; ?></small></td></tr></table>",   
		html: true },
		function(isConfirm){   
			if (isConfirm) {     
				document.location.href = 'topup_edit.php?topup_id=<?php echo $topup_id; ?>&redirect=<?php echo $_POST['redirect'];?>';
			}else {
				<?php
				if (isset($_POST['redirect'])) {
					echo "document.location.href = '".$_POST['redirect'].".php'";
				}else{
					echo "document.location.href = 'topup_edit.php?topup_id=".$topup_id."'";
				}

				?>
				
			} 
		});
});

<?php
}
?>

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

<link href="css/jquery-ui.css" rel="stylesheet"/>
<script charset="utf-8" src="js/jquery-ui.js"></script>
<link href="css/jquery-ui-timepicker-addon.css" rel="stylesheet"/>
<script charset="utf-8" src="js/jquery-ui-timepicker-addon.js"></script>

    </body>
</html>