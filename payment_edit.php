<?php
	include 'connect.php';
	include 'session.php';
	include 'inc/php/functions_statusConvert.php';

$error = '';
if($action = isset($_GET['action']) && $_GET['action'] != ''){
	//create new topup requset
	if ($action == 'delete') {
		$order_id 		= $_GET['order_id'];

		if(empty($order_id)){ $error .= 'you forgot order_id';}

		$order_id 		= stripcslashes($order_id);
		$order_id 		= mysql_real_escape_string($order_id);

		if ($error == '') {
			$del_payment_req = mysql_query("delete from customer_request_payment
				where order_id = '$order_id' and customer_id = '$user_id' and payment_request_status = '0'");



			if ($del_payment_req) {
				echo '
				<div class="container">
					<div class="alert alert-success" role="alert">
					  	<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
					  	<span class="sr-only">Success:</span>
					  	Payment request has been deleted.
					</div>
				</div>';
			}else{
				echo '
				<div class="container">
					<div class="alert alert-danger" role="alert">
					  	<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
					  	<span class="sr-only">Error:</span>
					  	Error : '.mysql_error().'
					</div>
				</div>';
			}
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
	<h1>แก้ไขการชำระเงิน</h1>

	<form role="form" method="post" action="payment_edit.php">
		<table class="content-light" style="border: 0px solid white;">
			<tr>
				<td>เลขที่ออเดอร์</td>
				<td><input type="text" name="order_id" class="form-control" placeholder="เลขที่ออเดอร์"></td>
				<td>ประเภท</td>
				<td>
					<div class="form-group">
					  	<select class="form-control" name="payment_request_type">
					  		<option value="">ไม่เลือก</option>
					  		<option value="1">ค่าสินค้า</option>
					  		<option value="2">ค่าขนส่ง</option>
					  	</select>
					</div>
				</td>
				<td>เลขที่การชำระเงิน</td>
				<td><input type="text" name="payment_request_id" class="form-control" placeholder="เลขที่การชำระเงิน"></td>
				<td rowspan="2" class="text-center">
					<button type="submit" name="search" value="Submit" ><i class="material-icons">search</i></button><br><br>
					<button href="payment_edit.php" class="button">แสดงทั้งหมด</button>
				</td>
			</tr>
			<tr>
				<td>ยอดเงิน</td>
				<td><input type="text" name="payment_request_amount" class="form-control" placeholder="ยอดเงินที่โอน"></td>
				<td>สถานะออเดอร์</td>
				<td>
					<div class="form-group">
					  	<select class="form-control" name="payment_for_order_status">
					  		<option value="">ไม่เลือก</option>
					  		<option value="0">รอตรวจสอบ</option>
					  		<option value="1">ตรวจสอบแล้ว</option>
					  		<option value="2">ยกเลิก Order</option>
					  		<option value="3">จ่ายค่าสินค้าเรียบร้อย</option>
					  		<option value="4">จ่ายค่าขนส่งเรียบร้อย</option>
					  		<option value="5">สินค้าอยู่ในโกดัง</option>
					  		<option value="6">ส่งสินค้นเรียบร้อย</option>
					  	</select>
					</div>
				</td>
				<td>สถานะการชำระเงิน</td>
				<td>
					<div class="form-group">
					  	<select class="form-control" name="payment_request_status">
					  		<option value="">ไม่เลือก</option>
					  		<option value="0">รอตรวจสอบยอด</option>
					  		<option value="1">ยอดไม่พอ</option>
					  		<option value="2">ดำเนินการแล้ว</option>
					  	</select>
					</div>
				</td>
				
			</tr>
		</table>
	</form>
	<br />
	<?php

		$order_id = '';
		$payment_request_type = '';
		$payment_request_id = '';
		$payment_request_amount = '';
		$payment_for_order_status = '';
		$payment_request_status = '';    

		if (isset($_POST['order_id'])) 					{ $order_id 				= $_POST['order_id']; }
		if (isset($_POST['payment_request_type'])) 		{ $payment_request_type 	= $_POST['payment_request_type']; }
		if (isset($_POST['payment_request_id'])) 		{ $payment_request_id 		= $_POST['payment_request_id']; }
		if (isset($_POST['payment_request_amount'])) 	{ $payment_request_amount 	= $_POST['payment_request_amount']; }
		if (isset($_POST['payment_for_order_status'])) 	{ $payment_for_order_status	= $_POST['payment_for_order_status']; }
		if (isset($_POST['payment_request_status'])) 	{ $payment_request_status 	= $_POST['payment_request_status']; }

		$query = "select * from customer_request_payment p, customer_order o 
			where p.customer_id = '$user_id' and p.order_id = o.order_id ";

		if($order_id != ''){ $query .= " and p.order_id = '$order_id'"; }
		if($payment_request_type != ''){ $query .= " and p.payment_request_type = '$payment_request_type'"; } 
		if($payment_request_id != ''){ $query .= " and p.payment_request_id = '$payment_request_id'"; }
		if($payment_request_amount != ''){ $query .= " and p.payment_request_amount = '$payment_request_amount'"; }
		if($payment_for_order_status != ''){ $query .= " and p.payment_for_order_status = '$payment_for_order_status'"; }
		if($payment_request_status != ''){ $query .= " and p.payment_request_status = '$payment_request_status'"; }

		$payment = mysql_query($query);
		if (mysql_num_rows($payment) > 0) {
			echo '
			<form role="form">
			<table class="content-grid">
				<tr>
					<th>เลขที่การชำระเงิน</th>
					<th>เลขที่ออเดอร์</th>
					<th>ประเภท</th>
					<th>ปรับปรุงล่าสุด</th>
					<th>สถานะออเดอร์</th>
					<th>ยอดเงิน (บาท)</th>
					<th>สถานะการชำระเงิน</th>
					<th>ยกเลิก</th>
				</tr>
			';

			while ($row = mysql_fetch_array($payment)) {
				$lastupdate = $row['date_payment_last_update'];
				if ($lastupdate == '0000-00-00 00:00:00') {
					$lastupdate = '-';
				}
				echo "
				<tr>
					<td>".$row['payment_request_id']."</td>
					<td><a href='order_show_detail.php?order_id=".$row['order_id']."'>".$row['order_number']."</a></td>
					<td>".convertRequestType($row['payment_request_type'])."</td>
					<td>".$lastupdate."</td>
					<td>".convertOrderStatus($row['payment_for_order_status'])."</td>
					<td>".$row['payment_request_amount']."</td>
					<td>".convertPaymentStatus($row['payment_request_status'])."</td>
					<td>";
					if ($row['payment_request_status'] == '0') {
						echo "<a href='payment_edit.php?action=delete&order_id=".$row['order_id']."'>ยกเลิกการชำระเงิน</a>";
					}

					echo"</td>
				</tr>
				";
			}
			echo "
			</table>
			<br />
			<p>รายการทั้งหมด ".mysql_num_rows($payment)." รายการ</p>
			";
		}else{
			echo "<ul class='error'>
					<li>ไม่พบข้อมูล</li>
				</ul>";
		}

	?>

		

		<br><br>&emsp;<a href="log_payment.html">&#10094; กลับไปหน้าชำระเงิน</a>
	</form>
	
</div>
	
</div><br /><br />
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