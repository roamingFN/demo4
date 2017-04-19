 <?php
	include 'connect.php';
	include 'session.php';
	include 'inc/php/functions_statusConvert.php';

$error = '';
if($action = isset($_GET['action']) && $_GET['action'] != ''){
	//create new topup requset
	if (!empty($_POST['topup-request-submit'])) {

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
								echo "File is not an image.";
								$uploadOk = 0;
						}
				}
				// Check if file already exists
				$target_file = file_newname($target_dir,basename($_FILES["transfer_bill"]["name"]));

				// Check file size
				if ($_FILES["transfer_bill"]["size"] > 500000) {
						echo "Sorry, your file is too large.";
						$uploadOk = 0;
				}
				// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
				&& $imageFileType != "gif" ) {
						echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
						$uploadOk = 0;
				}
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
						echo "Sorry, your file was not uploaded.";
				// if everything is ok, try to upload file
				} else {
						if (move_uploaded_file($_FILES["transfer_bill"]["tmp_name"], $target_file)) {
								//echo "The file ". basename( $_FILES["transfer_bill"]["name"]). " has been uploaded.";
						} else {
								echo "Sorry, there was an error uploading your file.";
						}
				}

				//select last topup number --(2)
				$select_topup_number = mysql_query("SELECT topup_number 
					FROM customer_request_topup 
					WHERE created_dt > STR_TO_DATE('".date('m/d/Y')." 00:00:00','%c/%e/%Y %T') 
					and created_dt < STR_TO_DATE('".date('m/d/Y')." 23:59:59','%c/%e/%Y %T') 
					ORDER BY topup_id DESC");

				//สร้าง topup ใหม่ --(1)
				$add_topup_req = mysql_query("insert into customer_request_topup(customer_id,topup_bank,topup_amount,
					usable_amout,topup_status,topup_date,transfer_method,bill_file_directory,customer_notes)
					values('$user_id','$topup_method','$balance','$balance','0',STR_TO_DATE('$transfer_datetime','%c/%e/%Y %T'),
					'$transfer_channel','$target_file','$notes')");
				$topup_id = mysql_insert_id();
				
				//update topup number -- (2)
				// echo "num row = ".mysql_num_rows($select_topup_number);
				if (mysql_num_rows($select_topup_number) > 0) {
					//เอา order_number เก่ามา +1
					$select_topup_number_row = mysql_fetch_array($select_topup_number);
					$old_topup_number = $select_topup_number_row[0];
					// echo "old_topup_number=".$old_topup_number;
					$number = (int)substr($old_topup_number, 7);
					$topup_number = "A".date("ymd").str_pad($number+1 ,5, "0", STR_PAD_LEFT);
					// echo "new_topup_number=".$topup_number;
					$update_number = mysql_query("update customer_request_topup set topup_number='$topup_number' where topup_id = '$topup_id'");
				}else{
					//สร้าง topup_number ใหม่
					$topup_number = "A".date("ymd").str_pad(1 ,5, "0", STR_PAD_LEFT);
					// echo "create_new=".$topup_number;
					$update_number = mysql_query("update customer_request_topup set topup_number='$topup_number' where topup_id = '$topup_id'");
				}

				//update customer current amount, waiting amout
				$query_customer = mysql_query("select * from customer where customer_id = '$user_id'");
				$query_customer_row = mysql_fetch_array($query_customer);
				$customer_current_amount = $query_customer_row['current_amount'];
				$customer_wait_amount = $query_customer_row['wait_amount'];
				$customer_current_amount += $balance;
				$customer_wait_amount += $balance;
				mysql_query("update customer set current_amount='$customer_current_amount',wait_amount='$customer_wait_amount' where customer_id = '$user_id'");

				//insert statement 
				mysql_query("insert into customer_statement(customer_id,statement_name,statement_date,debit,topup_id) 
											values('$user_id','เติมเงิน ".$topup_number." (รอตรวจสอบ)',STR_TO_DATE('$transfer_datetime','%c/%e/%Y %T'),'$balance','$topup_id')");

				if ($add_topup_req) {
					echo '<div class="alert alert-success container" role="alert"><label>แจ้งข้อมูลการเติมเงินเรียบร้อย ลูกค้าสามารถตัดชำระบิลได้ทันที</label></div>';
					//clear data
					$_POST = array();
				}else{
					echo '<div class="alert alert-danger container" role="alert">
					<label>เกิดข้อผิดพลาดในการบันทึกข้อมูล</label> โปรดตรวจสอบความถูกต้องของข้อมูล '.mysql_error().'</div>';
				}

			}else{

				//select last topup number --(2)
				$select_topup_number = mysql_query("SELECT topup_number 
					FROM customer_request_topup 
					WHERE created_dt > STR_TO_DATE('".date('m/d/Y')." 00:00:00','%c/%e/%Y %T') 
					and created_dt < STR_TO_DATE('".date('m/d/Y')." 23:59:59','%c/%e/%Y %T') 
					ORDER BY topup_id DESC");

				//สร้าง topup ใหม่ --(1)
				$add_topup_req = mysql_query("insert into customer_request_topup(customer_id,topup_bank,topup_amount,
					usable_amout,topup_status,topup_date,transfer_method,customer_notes)
					values('$user_id','$topup_method','$balance','$balance','0',STR_TO_DATE('$transfer_datetime','%c/%e/%Y %T'),
					'$transfer_channel','$notes')");
				$topup_id = mysql_insert_id();

				//update topup number -- (2)
				// echo "num row = ".mysql_num_rows($select_topup_number);
				if (mysql_num_rows($select_topup_number) > 0) {
					//เอา order_number เก่ามา +1
					$select_topup_number_row = mysql_fetch_array($select_topup_number);
					$old_topup_number = $select_topup_number_row[0];
					// echo "old_topup_number=".$old_topup_number;
					$number = (int)substr($old_topup_number, 7);
					$topup_number = "A".date("ymd").str_pad($number+1 ,5, "0", STR_PAD_LEFT);
					// echo "new_topup_number=".$topup_number;
					$update_number = mysql_query("update customer_request_topup set topup_number='$topup_number' where topup_id = '$topup_id'");
				}else{
					//สร้าง topup_number ใหม่
					$topup_number = "A".date("ymd").str_pad(1 ,5, "0", STR_PAD_LEFT);
					// echo "create_new=".$topup_number;
					$update_number = mysql_query("update customer_request_topup set topup_number='$topup_number' where topup_id = '$topup_id'");
				}

				//update customer current amount, waiting amout
				$query_customer = mysql_query("select * from customer where customer_id = '$user_id'");
				$query_customer_row = mysql_fetch_array($query_customer);
				$customer_current_amount = $query_customer_row['current_amount'];
				$customer_wait_amount = $query_customer_row['wait_amount'];
				$customer_current_amount += $balance;
				$customer_wait_amount += $balance;
				mysql_query("update customer set current_amount='$customer_current_amount',wait_amount='$customer_wait_amount' where customer_id = '$user_id'");

				//insert statement 
				mysql_query("insert into customer_statement(customer_id,statement_name,statement_date,debit,topup_id) 
											values('$user_id','เติมเงิน ".$topup_number." (รอตรวจสอบ)',STR_TO_DATE('$transfer_datetime','%c/%e/%Y %T'),'$balance','$topup_id')");

				if ($add_topup_req) {
					echo '<div class="alert alert-success container" role="alert"><label>แจ้งข้อมูลการเติมเงินเรียบร้อย ลูกค้าสามารถตัดชำระบิลได้ทันที</label></div>';

					//clear data
					$_POST = array();
				}else{
					echo '<div class="alert alert-danger container" role="alert">
					<label>เกิดข้อผิดพลาดในการบันทึกข้อมูล</label> โปรดตรวจสอบความถูกต้องของข้อมูล '.mysql_error().'</div>';
				}
			}
		}else{

			echo '<div class="alert alert-danger container" role="alert"><label>เกิดข้อผิดพลาด</label>'.$error.'</div>';

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

	<div class="row">
	<div class="col-md-10"><h1>เติมเงิน</h1></div>
	<div class="col-md-2" style="text-align: right; padding-top:10px;"><a href="order_list.php">
		<i class='material-icons'>payment</i><h3>ชำระเงิน</h3></a>
	</div>
	</div>
	<center><h3><i class="material-icons">info</i> <span style="color:red">เติมเงิน เป็นการเติมเงินเข้าระบบของลูกค้าเท่านั้น ไม่ได้มีการตัดจ่ายบิลใดๆ <br />หากลูกค้าต้องการชำระบิล กรุณา กดเมนู“ชำระเงิน” ที่หน้ารายการสั่งซื้อคะ</span></h3></center><br>
	</ul>
	<div class="row">
	<div class="col-md-2"></div>
	<div class="col-md-8">
		<table class="table borderless table-condensed ">
			<tr>
				<td style='border-top: none !important;'><b>เลขที่ออร์เดอร์ / เลขที่ใบส่งสินค้า</b></td>
				<td style='border-top: none !important;'><b>สถานะ</b></td>
				<td style='border-top: none !important;' class='text-right'><b>ยอดเงิน</b></td>
				<td style='border-top: none !important;'></td>
			</tr>

			<?php
			$select_order = mysql_query("select * from customer_order o, customer_order_shipping s 
			where o.customer_id='$user_id' and o.order_id = s.order_id 
			and (order_status_code = '1' or order_status_code = '6') ");

			$total_order_price = 0;
			if (mysql_num_rows($select_order) > 0) {
				while ($row = mysql_fetch_array($select_order)) {
					if ($row['order_status_code'] == 1) {
						$total_order_price += $row['order_price'];
						echo "
						<tr>
							<td style='border-top: none !important;'>".$row['order_number']." (ค่าสินค้า)</td>
							<td style='border-top: none !important;'>".convertOrderStatus($row['order_status_code'])."</td>
							<td style='border-top: none !important;' class='text-right'><b>".number_format($row['order_price'],2)."</b></td>
							<td style='border-top: none !important;'>THB</td>
						</tr>
						";
					}
				}
			}

			$select_package = mysql_query("select * from package p 
			where p.customerid='$user_id' and p.statusid = '3' ");

			$total_package_price = 0;
			if (mysql_num_rows($select_package) > 0) {
				while ($row = mysql_fetch_array($select_package)) {
					if ($row['statusid'] == 3) {
						$total_package_price += $row['total'];
						echo "
						<tr>
							<td style='border-top: none !important;'>".$row['packageno']." (ค่าขนส่ง)</td>
							<td style='border-top: none !important;'>".formatPackageNo($row['statusid'])."</td>
							<td style='border-top: none !important;' class='text-right'><b>".number_format($row['total'],2)."</b></td>
							<td style='border-top: none !important;'>THB</td>
						</tr>
						";
					}
				}
			}

			$total_payment = $total_order_price + $total_package_price;

			?>

			<tr>
				<td style="border-top:2pt solid #dddddd;"></td>
				<td style="border-top:2pt solid #dddddd;">ยอดรวม (รอชำระ)</td>
				<td style="border-top:2pt solid #dddddd;"  class='text-right'>
					<b><?php echo number_format($total_payment,2) ?></b>
				</td>
				<td style="border-top:2pt solid #dddddd;">THB</td>
			</tr>
			<tr>
				<td style='border-top: none !important;'></td>
				<td style='border-top: none !important;'></td>
				<td style='border-top: none !important;'></td>
				<td style='border-top: none !important;'></td>
			</tr>
			<?php 
				$query_customer = mysql_query("select * from customer where customer_id = '$user_id'");
				$query_customer_row = mysql_fetch_array($query_customer);
				$customer_current_amount = $query_customer_row['current_amount'];

				$query_topup = mysql_query("select sum(usable_amout) usable_amout from customer_request_topup 
					where topup_status = '0' and customer_id = '$user_id'");
				$query_topup_row = mysql_fetch_array($query_topup);
				$customer_topup = $query_topup_row['usable_amout'];

				$query_verified_topup = mysql_query("select sum(usable_amout) usable_amout from customer_request_topup 
					where topup_status = '1' and customer_id = '$user_id'");
				$query_verified_topup_row = mysql_fetch_array($query_verified_topup);
				$customer_verified_topup = $query_verified_topup_row['usable_amout'];
			?>
			<tr>
				<td style="border-top: none !important;">ยอดเงินคงเหลือ (รอตรวจสอบ)</td>
				<td style="border-top: none !important;"></td>
				<td style="border-top: none !important;" class='text-right'>
					<b><?php echo number_format($customer_topup,2); ?></b>
				</td>
				<td style="border-top: none !important;">THB</td>
			</tr>
			<tr>
				<td style="border-top: none !important;">ยอดเงินคงเหลือ (ตรวจสอบแล้ว)</td>
				<td style="border-top: none !important;"></td>
				<td style="border-top: none !important;" class='text-right'>
					<b><?php echo number_format($customer_verified_topup,2); ?></b>
				</td>
				<td style="border-top: none !important;">THB</td>
			</tr>
			<tr>
				<td style="border-top:2pt solid #dddddd;"></td>
				<td style="border-top:2pt solid #dddddd;">ยอดรวมทั้งหมด</td>
				<td style="border-top:2pt solid #dddddd;" class='text-right'>
					<b><?php echo number_format($customer_topup+$customer_verified_topup,2); ?></b>
				</td>
				<td style="border-top:2pt solid #dddddd;">THB</td>
			</tr>
			<?php if($customer_topup+$customer_verified_topup-$total_payment < 0){ ?>
			<tr>
				<td style='border-top: none !important;'></td>
				<td style='border-top: none !important;'></td>
				<td style='border-top: none !important;'></td>
				<td style='border-top: none !important;'></td>
			</tr>
			<tr>
				<td style='border-top: none !important;' colspan="2">
					<span style="color:red">ต้องโอนเงินเพิ่ม เพื่อที่จะพอกับค่าใช้จ่ายทั้งหมด เป็นยอดเงิน</span>
				</td>
				<td style='border-top: none !important;' class='text-right'>
					<b>
						<span style="color:red">
							<?php echo number_format($customer_topup+$customer_verified_topup-$total_payment,2); ?>
						</span>
					</b>
				</td>
				<td style='border-top: none !important;'><span style="color:red">THB</span></td>
			</tr>
			<?php } ?>
		</table>
	</div>
	</div>
	<br />
	<form role="form" action="topup.php?action=topup-request-submit" method="post" name="from_topup_req" 
		id="from_topup_req" enctype="multipart/form-data">
	<table class="content-grid">
		<tr>
			<th colspan="6" >เลือกบัญชีที่โอนเงินเข้า</th>
		</tr>

<?php 
$select_payment_method = mysql_query("select * from bank_payment");
if (mysql_num_rows($select_payment_method)>0) {
	while ($row = mysql_fetch_array($select_payment_method)) {
		echo "
		<tr>
			<td><input type='radio' name='topup_method' value='".$row['bank_id']."' ";
			if ($row['bank_id'] == $_POST['topup_method']) {
				echo " checked ";
			}
			echo" /></td>
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
	<br>
	<table class="content-light">
		<tr>
			<th>ยอดเงิน</th>
			<td><input type="text" class="form-control" placeholder="จำนวนเงินที่โอน" name="balance" id="balance" 
			onkeypress="return isNumber2(event)" 
			value="<?php if (isset($_POST['balance'])) echo $_POST['balance']; ?>" /></td>
		</tr>
		<tr>
			<th>วันที่โอนเงิน</th>
			<td>
				<div class="input-group input-append date" >
									<input type="text" class="form-control" id="mdate1" name="transfer_date" placeholder="วันที่โอนเงิน" 
										value="<?php if (isset($_POST['transfer_date'])) echo $_POST['transfer_date']; else echo date('d/m/Y'); ?>" />
									<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
							</div>
			</td>
			<th>เวลาที่โอน</th>
			<td>
				<div class="input-group">
									<input type="text" class="form-control" id="time1" name="transfer_time" placeholder="เวลาที่โอนเงิน (ชั่วโมง:นาที)" 
										onkeypress="return isTime(event)" 
										value="<?php if (isset($_POST['transfer_time'])) echo $_POST['transfer_time']; ?>" />
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
							echo "<option value=".$row['bank_payment_method_name'].">".$row['bank_payment_method_name']."</option>";
						}
						echo "</select>";
					}

					?>
			</td>
		</tr>
		<tr>
			<th>หลักฐานการชำระเงิน</th>
			<td><input type="file" class="form-control" name="transfer_bill" id="transfer_bill"></td>
		</tr>
		<tr>
			<th>หมายเหตุ</th>
			<td><textarea placeholder="หมายเหตุ" name="notes"
				value="<?php if (isset($_POST['notes'])) echo $_POST['notes']; ?>"></textarea></td>
			<td colspan="2"><i class="material-icons">info</i><span style="color:red"> หลังจากท่านเติมเงินแล้ว กรุณาสั่งชำระเพื่อเป็นการยืนยันระบบจะได้ดำเนินการสั่งซื้อ หรือส่งสินค้า</span><br />  
			<i class="material-icons">info</i><span style="color:red"> กรุณาแจ้งข้อมูล ยอดเงิน วันที่ ธนาคาร ให้ถูกต้องถ้าท่านแจ้งข้อมูลไม่ถูกต้องระบบ จะทำการยกเลิกให้ท่านตรวจสอบอีกครั้ง</span></td>
		</tr>
		<tr>
			<th></th>
			<td><button type="submit" name="topup-request-submit" id="topup-request-submit" value="Submit"><h3>ตกลง&nbsp</h3></button>
			<img src="images/loading.gif" id="topup-request-submit-loading"></td>
		</tr>
	</table>

	</form>

	</div></div><br /><br />

<script>

$("form").submit(function(){

		if (!$('[name="topup_method"]').is(':checked')){
			swal('กรุณาเลือกบัญชีที่โอนเงินเข้าด้วยค่ะ');
				$('html, body').animate({ scrollTop: 0 }, 'slow');
			return false;
		}
		if($('[name="balance"]').val() == ''){
			swal({   
				title: 'กรุณากรอกจำนวนเงินที่โอนเข้าด้วยค่ะ',   
			}, 
		function() {   
			$('html, body').animate({ scrollTop: $('[name="balance"]').offset().top-100 }, 'slow');
					$('[name="balance"]').focus();
			});
			return false;
		}
		if($('[name="transfer_date"]').val() == ''){
			swal({   
				title: 'กรุณากรอกวันที่โอนเงินด้วยค่ะ',   
			}, 
			function() {   
				$('html, body').animate({ scrollTop: $('[name="transfer_date"]').offset().top-100 }, 'slow');
						$('[name="transfer_date"]').focus();
			});
			return false;
		}
		if($('[name="transfer_time"]').val() == ''){
			swal({   
				title: 'กรุณากรอกเวลาที่โอนเงินด้วยค่ะ',   
			}, 
			function() {   
				$('html, body').animate({ scrollTop: $('[name="transfer_time"]').offset().top-100 }, 'slow');
						$('[name="transfer_time"]').focus();
			});
			return false;
		}
		//check time valid
		re = /^\d{1,2}:\d{2}([ap]m)?$/;
		if(!$('[name="transfer_time"]').val().match(re)) {
			swal({   
				title: 'กรุณากรอกเวลาที่โอนเงินให้ถูกต้องค่ะ',   
			}, 
			function() {   
				$('html, body').animate({ scrollTop: $('[name="transfer_time"]').offset().top-100 }, 'slow');
						$('[name="transfer_time"]').focus();
			});
			return false;
		}
		if($('[name="transfer_channel"]').val() == ''){
			swal({
				title: 'กรุณากรอกช่องทางการโอนเงินด้วยค่ะ'
			}, 
			function() {   
				$('html, body').animate({ scrollTop: $('[name="transfer_channel"]').offset().top-100 }, 'slow');
						$('[name="transfer_channel"]').focus();
			});
			return false;
		}

		$("#topup-request-submit").hide();
		$("#topup-request-submit-loading").show();
		return true;

});


$(document).ready(function() {
		$('#mdate1').datepicker({
				dateFormat: 'dd/mm/yy',
				maxDate: new Date(),
				todayHighlight: true,
		})

		$('#time1').timepicker({
			timeInput:true,
			altRedirectFocus:false,
		})

		$("#topup-request-submit-loading").hide();

		<?php
		//$topup_id = 1;
		if (isset($topup_id) || isset($_GET['show_topup_id'])) { 
			if (isset($_GET['show_topup_id'])) {
				$topup_id = $_GET['show_topup_id'];
				$transfer_date_text = $_GET['transfer_date_text'];
				$transfer_time_text = $_GET['transfer_time_text'];
				$balance = $_GET['balance'];
				$transfer_channel = $_GET['transfer_channel'];
				$topup_method = $_GET['topup_method'];
				$notes = $_GET['notes'];
			}
			$bank_detail = mysql_query("select * from bank_payment where bank_id = '$topup_method' ");
			$row = mysql_fetch_array($bank_detail);
		?>
			
			swal({   
				title: "ยอดเงินที่ลูกค้าต้องการแจ้งเติมเงิน",
				showCancelButton: true,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "แก้ไข",
				cancelButtonText: "ตกลง",
				closeOnConfirm: false,
				closeOnCancel: false,
				text: "<table width='100%'><tr><td class='text-left'>วันที่โอนเงิน : <?php echo $transfer_date_text; ?></td><td class='text-left'>เวลาที่โอนเงิน : <?php echo $transfer_time_text; ?></td></tr><tr><td class='text-left'>ยอดเงิน : <?php echo number_format($balance,2); ?></td><td class='text-left'>ช่องทางการโอน : <?php echo $transfer_channel; ?></td></tr><tr><td colspan='2'>หมายเหตุ : <?php echo "<br><textarea rows='2' cols='20' class='form-control' disabled>".$notes."</textarea>"; ?></td></tr></table><hr /><table width='100%'><tr><td colspan='3'><span style='color:#1874CD'>โดยโอนเข้าบัญชี</span></td></tr><tr><td rowspan='2'><img style='height:50px;border-radius:3' src='img/<?php echo $row["bank_img"]; ?>'></td><td class='text-left'><small><?php echo $row["account_name"]; ?></small></td><td class='text-left'><small><?php echo $row["bank_name_th"]; ?></small></td></tr><tr><td class='text-left'><small><?php echo $row["account_no"]; ?></small></td><td class='text-left'><small><?php echo $row["bank_branch"]; ?></small></td></tr></table>",   
				html: true },
				function(isConfirm){   
					if (isConfirm) {     
						document.location.href = 'topup_edit.php?topup_id='+<?php echo $topup_id; ?>+'&redirect=topup';
					}else {     
						swal({
						 title: "แจ้งข้อมูลการเติมเงินเรียบร้อยแล้ว",
						 text: "กรุณาไปยังหน้ารายการสั่งซื้อเพื่อกดปุ่มชำระเงิน",
						 type: "success",
						 showCancelButton: true,
						 confirmButtonColor: "#DD6B55",
						 confirmButtonText: "กลับไปหน้าเติมเงิน",
						 cancelButtonText: "ชำระเงิน",
						 closeOnConfirm: false,
						 closeOnCancel: false
						}, function(isConfirm) {
						 if (isConfirm) {
						  document.location.href = 'topup.php';
						 } else {
						  document.location.href = 'payment_list.php';
						 }
						});
					} 
				});

		<?php
		}
		?>

});



function isNumber2(evt) {
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
				<style type="text/css">
					.sweet-alert button.cancel{
						background-color: #1874CD;
					}
					.sweet-alert button.cancel:hover{
						background-color: #1874CD;
					}
				</style>

<link href="css/jquery-ui.css" rel="stylesheet"/>
<script charset="utf-8" src="js/jquery-ui.js"></script>
<link href="css/jquery-ui-timepicker-addon.css" rel="stylesheet"/>
<script charset="utf-8" src="js/jquery-ui-timepicker-addon.js"></script>

		</body>
</html>

