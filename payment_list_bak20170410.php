<?php
	include 'connect.php';
	include 'session.php';
	include 'inc/php/functions_statusConvert.php';
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
		<?php
			//show page message
			if (isset($_GET['message'])||isset($_GET['error'])){
				echo "<br />";
			}
			if (isset($_GET['message'])) {
				if ($_GET['message']!="") {
					//echo '<div class="alert alert-success container" role="alert"><label>'.$_GET['message'].'</label></div>';
					echo "<script type='text/javascript'>
											$(document).ready(function(){
												swal({
													title: 'แจ้งการตัดชำระ',
													text: '".$_GET['message']."',
													type: 'success',
													confirmButtonText: 'ตกลง',
												})
											})	
										</script>";				
				}
			}
			if (isset($_GET['error'])) {
				if ($_GET['error']!="") {
					
					$query_site_url = mysql_query("select * from website_config");
					$site_url_row = mysql_fetch_array($query_site_url);
					$site_url = $site_url_row['SITE_URL'];
					echo "<script type='text/javascript'>
											$(document).ready(function(){
												swal({
													title: 'เกิดข้อผิดพลาด!',
													text: '".$_GET['error']."',
													type: 'warning',
													showCancelButton: true,
													confirmButtonColor: '#3085d6',
													cancelButtonColor: '#d33',
													confirmButtonText: 'เติมเงิน',
													cancelButtonText: 'ยกเลิก'
												}).then(function(isConfirm) {
													if (isConfirm) {
														document.location.href = '".$site_url."topup';
													}
												})
											})
										</script>";				
					//text: '".$_GET['error']."<br>',					
					//echo '<div class="alert alert-danger container" role="alert"><label>'.$_GET['error'].'</div>';
				}
			}
		?>
	<div class="content manage">
		<?php include 'left_menu.php' ?>
		<div class="menu-content col3">

	<div class="row">
	<div class="col-md-8">
		<h1>ชำระเงิน</h1>
	</div>
	<div class="col-md-4">
		<?php 
				// $query_customer = mysql_query("select * from customer where customer_id = '$user_id'");
				// $query_customer_row = mysql_fetch_array($query_customer);
				// $customer_current_amount = $query_customer_row['current_amount'];
				$query_topup = mysql_query("select sum(usable_amout) usable_amout from customer_request_topup 
					where (topup_status = '0' or topup_status = '1') and customer_id = '$user_id'");
				$query_topup_row = mysql_fetch_array($query_topup);
				$customer_topup = $query_topup_row['usable_amout'];
		?>
		<div class="text-right"><h3 style="color:#3e54af;">ยอดเงินคงเหลือ : <?php echo number_format($customer_topup,2); ?> THB</h3></div>
	</div>
	</div>

	<form role="form" method="post" action="">
		<table class="content-light">
			<tr>
				<td>เลขที่กล่อง</td>
				<td><input type="text" name="package_no" class="form-control" placeholder="ระบุเลขที่กล่อง"></td>
				<td></td>
				<td></td>
				<td rowspan="3">
					<?php 
						$aproved_amount = 0;
						$unapprove_amount = 0;
						$select_topup = mysql_query("select * from customer_request_topup where customer_id = '$user_id'");
						while ($row = mysql_fetch_array($select_topup)) {
							if ($row['topup_status']==0) {
								$unapprove_amount += $row['usable_amout'];
							}else if ($row['topup_status']==1) {
								$aproved_amount += $row['usable_amout'];
							}
						}
					?>
					<table class="content-bordered" >
						<tr><td>ยอดเงินที่เหลืออยู่ : </td><td><b><?php echo number_format($aproved_amount,2); ?></b></td></tr>
						<tr><td>ยอดเงินที่รอตรวจสอบ : </td><td><b><?php echo number_format($unapprove_amount,2); ?></b></td></tr>
						<tr><td>ยอดรวม : </td><td><b><?php echo number_format($aproved_amount+$unapprove_amount,2); ?></b></td></tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>เลขที่ออร์เดอร์</td>
				<td><input type="text" name="order_no" class="form-control" placeholder="ระบุเลขที่ออร์เดอร์"></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td>รายการ</td>
				<td>
					<div class="form-group">
							<select class="form-control" name="category">
								<option value="">ไม่เลือก</option>
								<option value="1">กล่อง</option>
								<option value="2">ออร์เดอร์</option>
							</select>
					</div>
				</td>
				<td>สถานะ</td>
				<td>
					<div class="form-group">
							<select class="form-control" name="status">
								<option value="">ไม่เลือก</option>
								<option value="1">รอชำระค่าสินค้า</option>
								<option value="2">รอชำระค่าขนส่ง</option>
								<option value="3">รอตรวจสอบการโอนเงินค่าสินค้า</option>
								<option value="4">รอตรวจสอบการโอนค่าขนส่ง</option>
								<option value="5">ชำระค่าสินค้าแล้ว</option>
								<option value="6">ชำระค่าขนส่งแล้ว</option>
							</select>
					</div>
				</td>
			</tr>
			<tr>
				<td>จากวันที่</td>
				<td>
					<div class="input-group input-append date" id="datePicker" style="width:170px;">
						<input type="text" class="form-control" name="payment_date_start" value="" placeholder="ระบุวันที่" />
						<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
					</div>
				</td>
				<td>ถึงวันที่</td>
				<td>
					<div class="input-group input-append date" id="datePicker2" style="width:170px;">
						<input type="text" class="form-control" name="payment_date_end" value="" placeholder="ระบุวันที่" />
						<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
					</div>
				</td>
				<td>
					&nbsp<button type="submit" name="search" value="Submit" ><i class="material-icons">search</i> ค้นหา</button> &nbsp
					<button href="#" tpye="button">แสดงทั้งหมด</button>
				</td>
				
				
			</tr>
		</table>
	</form>
	<br />
	<?php

		//search order handle
		$query_order = "select * from customer_order where customer_id = '$user_id' ";

		if ($_POST['order_no'] != '') {
			$query_order .= " and order_number = '".$_POST['order_no']."' ";
			$isOnlyOrder = true;
		}

		if ($_POST['status'] == 1) {
			$query_order .= " and order_status_code = 1 ";
			$isOnlyOrder = true;
		}else if($_POST['status'] == 3){
			$query_order .= " and order_status_code = 2 ";
			$isOnlyOrder = true;
		}else if($_POST['status'] == 5){
			$query_order .= " and order_status_code = 3 ";
			$isOnlyOrder = true;
		}

		if ($_POST['payment_date_start'] != '') { 	
			$payment_date_start = $_POST['payment_date_start']; 
			$payment_date_start = str_replace('/', '-', $payment_date_start);
			$payment_date_start = date('m/d/Y', strtotime($payment_date_start));
			$query_order .= " and date_order_created > STR_TO_DATE('$payment_date_start 00:00:00','%c/%e/%Y %T') ";
		}

		if ($_POST['payment_date_end'] != '') {
			$payment_date_end   = $_POST['payment_date_end']; 
			$payment_date_end   = str_replace('/', '-', $payment_date_end);
			$payment_date_end   = date('m/d/Y', strtotime($payment_date_end));
			$query_order .= " and date_order_created < STR_TO_DATE('$payment_date_end 23:59:59','%c/%e/%Y %T')";
		}


		//search package handle
		$query_package = "select * from package where customerid = '$user_id' ";

		if ($_POST['package_no'] != '') {
			$query_package .= " and packageno = '".$_POST['package_no']."' ";
			$isOnlyPackage = true;
		}

		if ($_POST['status'] == 2) {
			$query_package .= " and statusid = 2 ";
			$isOnlyPackage = true;
		}else if($_POST['status'] == 4){
			$query_package .= " and statusid = 3 ";
			$isOnlyPackage = true;
		}else if($_POST['status'] == 6){
			$query_package .= " and statusid = 4 ";
			$isOnlyPackage = true;
		}

		if ($_POST['payment_date_start'] != '') { 	
			$payment_date_start = $_POST['payment_date_start']; 
			$payment_date_start = str_replace('/', '-', $payment_date_start);
			$payment_date_start = date('m/d/Y', strtotime($payment_date_start));
			$query_package .= " and createdate > STR_TO_DATE('$payment_date_start 00:00:00','%c/%e/%Y %T') ";
		}

		if ($_POST['payment_date_end'] != '') {
			$payment_date_end   = $_POST['payment_date_end']; 
			$payment_date_end   = str_replace('/', '-', $payment_date_end);
			$payment_date_end   = date('m/d/Y', strtotime($payment_date_end));
			$query_package .= " and createdate < STR_TO_DATE('$payment_date_end 23:59:59','%c/%e/%Y %T')";
		}


		//search all handle
		if ($_POST['category'] == 1 || $isOnlyPackage) {
			$query_order .= ' and 0';
		}else if ($_POST['category'] == 2 || $isOnlyOrder) {
			$query_package .= ' and 0';
		}


		//query data
		$select_order = mysql_query($query_order);
		$select_package = mysql_query($query_package);


		if (mysql_num_rows($select_order) > 0 || mysql_num_rows($select_package) > 0) {
			echo '
		<form role="form" method="post" action="payment.php">
		<table class="content-grid" >
			<tr class="bg-primary">
				<th><input type="checkbox" onClick="toggle(this)" name="checkall"/> เลือกทั้งหมด</th>
				<th>รายการ</th>
				<th>เลขที่</th>
				<th>วันที่สร้าง</th>
				<th>วันที่ตัดชำระ</th>
				<th>สถานะ</th>
				<th>ยอดเงิน (บาท)</th>
			</tr>
			';

			$order_num_rows = mysql_num_rows($select_order);
			$package_num_rows = mysql_num_rows($select_package);

			$order_row = mysql_fetch_array($select_order);
			$package_row = mysql_fetch_array($select_package);

			$total_baht = 0;

			while ($order_num_rows > 0 || $package_num_rows > 0) {

				if (strtotime($order_row['date_order_created']) > strtotime($package_row['createdate'])) {
					echo "
					<tr>
						<td>";

						if ( ($order_row['order_status_code']  == 1) and ($order_row['order_price'] > 0)  ) {
							echo "<input type='checkbox' name='order_id[]' value='".$order_row['order_id']."' data-price='".$order_row['order_price']."'>";
						}

						echo "</td>
						<td>เลขที่ออร์เดอร์</td>
						<td><a href='order_show_detail.php?order_id=".$order_row['order_id']."'>".$order_row['order_number']."</a></td>
						<td>".formatDate($order_row['date_order_created'])."</td>
						<td>".formatDate($order_row['date_order_paid'])."</td>
						<td>".convertOrderStatus($order_row['order_status_code'])."</td>
						<td>".number_format($order_row['order_price'],2)."</td>
					</tr>";
					$total_baht += $order_row['order_price'];
					$order_row = mysql_fetch_array($select_order);
					$order_num_rows--;
				}else{
					echo "
					<tr>
						<td>";

						if ($package_row['statusid'] == 2) {
							echo "<input type='checkbox' name='package_id[]' value='".$package_row['packageid']."' data-price='".$package_row['total']."' >";
						}

						echo "</td>
						<td>เลขที่กล่อง</td>
						<td><a href='package_detail.php?packageid=".$package_row['packageid']."'>".$package_row['packageno']."</a></td>
						<td>".formatDate($package_row['createdate'])."</td>
						<td>".formatDate($package_row['paydate'])."</td>
						<td>".formatPackageNo($package_row['statusid'])."</td>
						<td>".number_format($package_row['total'],2)."</td>
					</tr>";
					$total_baht += $package_row['total'];
					$package_row = mysql_fetch_array($select_package);
					$package_num_rows--;
				}
			}

		$check_rate = mysql_query("select * from website_rate order by starting_date desc limit 1");
		$check_rate_row = mysql_fetch_array($check_rate);
		$check_rate_date = $check_rate_row['starting_date'];
		$check_rate = $check_rate_row['rate_cny'];

		echo "
			<tr class='bg bg-info'>
				<td colspan='5' class='text-left'><b>ทั้งหมด <span id='all_item_count'>0</span> รายการ</b></td>
				<td colspan='2' class='text-center'><b>รวม : <span id='total_price'>".number_format($total_baht,2)."</span> บาท</b></td>
			</tr>
		</table><br />
		<br/>
		<center>
			<a href='topup.php'><button type='button' class='button'><i class='material-icons'>local_atm</i><h3>เติมเงิน</h3></button></a>
			<input type='hidden' name='redirect' value='2'>
			<input type='hidden' name='show_rate' value='$check_rate'>
			<button type='submit' name='payment_list' class='button' value='Submit'><i class='material-icons'>payment</i><h3>ชำระเงิน</h3></button>
		</center>
		<br />
		";

		}else{
			echo "<div class='alert alert-danger'>คุณยังไม่มีรายการที่ต้องชำระเงินค่ะ</div>";
		}


	?>

	</form>
	
</div>
	
</div><br /><br />
<script type="text/javascript">

function toggle(source) {

	var sum_order_price = 0;
	var sum_package_price = 0;
	var count_order = 0;
	var count_package = 0;

	order_id = document.getElementsByName('order_id[]');
	for(var i=0, n=order_id.length;i<n;i++) {
			order_id[i].checked = source.checked;
			sum_order_price += parseFloat(order_id[i].getAttribute("data-price"));
	}

	package_id = document.getElementsByName('package_id[]');
	for(var i=0, n=package_id.length;i<n;i++) {
			package_id[i].checked = source.checked;
			sum_package_price += parseFloat(package_id[i].getAttribute("data-price"));
	}

	if (source.checked) {
		document.getElementById('all_item_count').innerHTML = order_id.length+package_id.length;
		document.getElementById('total_price').innerHTML = (sum_order_price+sum_package_price).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
	}else{
		document.getElementById('all_item_count').innerHTML = "0";
		document.getElementById('total_price').innerHTML = "0.00";
	}

}

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

$(document).ready(function(){
		
	$('#topupHistotyTable').pageMe({pagerSelector:'#myPager',showPrevNext:true,hidePageNumbers:false,perPage:2});
		
});
	
$(document).ready(function() {
		$('#datePicker').datepicker({
			format: 'dd/mm/yyyy',
			todayHighlight: true
		})
		.on('changeDate', function(e) {
			// Revalidate the date field
			$('#eventForm').formValidation('revalidateField', 'transfer_date_start');
		});

		$('#datePicker2').datepicker({
			format: 'dd/mm/yyyy',
			todayHighlight: true
		})
		.on('changeDate', function(e) {
			// Revalidate the date field
			$('#eventForm').formValidation('revalidateField', 'transfer_date_end');
		});

		$('input:checkbox').change(function() {
			var sum_order_price = 0;
			var sum_package_price = 0;
			var count_order = 0;
			var count_package = 0;

			order_id = document.getElementsByName('order_id[]');
			for(var i=0, n=order_id.length;i<n;i++) {
					if (order_id[i].checked) {
						sum_order_price += parseFloat(order_id[i].getAttribute("data-price"));
						count_order++;
					}
			}

			package_id = document.getElementsByName('package_id[]');
			for(var i=0, n=package_id.length;i<n;i++) {
					if (package_id[i].checked) {
						sum_package_price += parseFloat(package_id[i].getAttribute("data-price"));
						count_package++;
					}
			}

			document.getElementById('all_item_count').innerHTML = count_order+count_package;
			document.getElementById('total_price').innerHTML = (sum_order_price+sum_package_price).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');

	  });
});
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
				<script src="dist/sweetalert2.min.js"></script>
				<link rel="stylesheet" href="dist/sweetalert2.css">
		</body>
</html>