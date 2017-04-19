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
	<div class="content manage">
		<?php include 'left_menu.php' ?>
		<div class="menu-content col3">

	<div class="row">
	<div class="col-md-8">
		<h1>ประวัติการเติมเงิน</h1>
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

	<form role="form" method="post" action="topup_history.php?action=search">
		<table class="content-light">
			<tr>
				<td>ตั้งแต่วันที่</td>
				<td>
					<div class="input-group input-append date" id="datePicker" style="width:170px;">
						<input type="text" class="form-control" name="topup_date_start" value="<?php echo date('01/m/Y', strtotime('-90 days')) ?>" placeholder="วันที่แจ้งเติมเงิน" />
						<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
					</div>
				</td>
				<td>ถึงวันที่</td>
				<td>
					<div class="input-group input-append date" id="datePicker2" style="width:170px;">
						<input type="text" class="form-control" name="topup_date_end" value="<?php echo date("d/m/Y"); ?>" placeholder="วันที่แจ้งเติมเงิน" />
						<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
					</div>
				</td>
				<td>ธนาคาร</td>
				<td>
					<div class="form-group">
							<select class="form-control" name="bank_id">
								<option value="">ไม่เลือก</option>
							<?php 
								$select_bank = mysql_query("select * from bank_payment");
								if (mysql_num_rows($select_bank) > 0) {
									while($row = mysql_fetch_array($select_bank)){
										echo "<option value='".$row['bank_id']."'>".$row['bank_name_th']." (".$row['account_no'].")</option>";
									}
								}

							?>

							</select>
					</div>
				</td>
				
				
			</tr>
			<tr>
				<td>ยอดเงิน</td>
				<td><input type="text" name="topup_amount" class="form-control" placeholder="จำนวนเงินที่โอน" onkeypress="return isNumber(event)" ></td>
				<td>สถานะ</td> 
				<td>
					<div class="form-group">
							<select class="form-control" name="topup_status">
								<option value="">ไม่เลือก</option>
								<option value="0">รอตรวจสอบ</option>
								<option value="1">ตรวจสอบแล้ว</option>
								<option value="2">ยกเลิก</option>
							</select>
					</div>
				</td>
				<td class="text-center" colspan="2">
					<button type="submit" name="search" value="Submit" ><i class="material-icons">search</i> ค้นหา</button> &nbsp
					<button onclick="document.location.href = 'topup_history.php'" type="button">แสดงทั้งหมด</button>
				</td>
				
			</tr>
		</table>
	</form>

	<?php

		$topup_date = '';
		$bank_id = '';
		$topup_id = '';
		$topup_amount = '';
		$topup_status = '';
		$account_no = '';

		if (isset($_POST['topup_date_start']) && $_POST['topup_date_start']!="" &&
				isset($_POST['topup_date_end'])   && $_POST['topup_date_end']!="" ) 	{ 	
												$topup_date_start = $_POST['topup_date_start']; 
												$topup_date_start = str_replace('/', '-', $topup_date_start);
												$topup_date_start = date('m/d/Y', strtotime($topup_date_start));
												$topup_date_end   = $_POST['topup_date_end']; 
												$topup_date_end   = str_replace('/', '-', $topup_date_end);
												$topup_date_end   = date('m/d/Y', strtotime($topup_date_end));
											}
		if (isset($_POST['bank_id'])) 		{ $bank_id 		= $_POST['bank_id']; }
		if (isset($_POST['topup_id'])) 		{ $topup_id 	= $_POST['topup_id']; }
		if (isset($_POST['topup_amount'])) 	{ $topup_amount = $_POST['topup_amount']; }
		if (isset($_POST['topup_status'])) 	{ $topup_status	= $_POST['topup_status']; }
		if (isset($_POST['account_no'])) 	{ $account_no 	= $_POST['account_no']; }



		$query = "select * from customer_request_topup t, bank_payment b 
			where customer_id = '$user_id' and t.topup_bank = b.bank_id ";


		if( $topup_date_start != '' && $topup_date_end != ''){ 
			$query .= " and t.topup_date > STR_TO_DATE('$topup_date_start 00:00:00','%c/%e/%Y %T')  
			and t.topup_date < STR_TO_DATE('$topup_date_end 23:59:59','%c/%e/%Y %T')"; }
		if($bank_id != ''){ $query .= " and t.topup_bank = '$bank_id'"; } 
		if($topup_id != ''){ $query .= " and t.topup_number like '%$topup_id%'"; }
		if($topup_amount != ''){ $query .= " and t.topup_amount = '$topup_amount'"; }
		if($topup_status != ''){ $query .= " and t.topup_status = '$topup_status'"; }
		if($account_no != ''){ $query .= " and b.account_no = '$account_no'"; }

		$query .= " order by t.created_dt desc ";

		//echo $query;

		$topup = mysql_query($query);

		if (mysql_num_rows($topup) > 0) {
			echo '
		<form role="form">
		<table class="content-grid" id="topupHistotyTable">
			<tr class="bg-primary">
				<th>เลขที่การเติมเงิน</th>
				<th>วันที่โอน</th>
				<th>ยอดเงินที่เติม</th>
				<th>ยอดเงินที่ใช้ได้</th>
				<th>ช่องทาง</th>
				<th>ธนาคาร</th>
				<th style="width:150px;">เลขบัญชี/ชื่อบัญชี</th>
				<th>สถานะยอดเงิน</th>
				<th>สถานะการใช้งาน</th>
				<th>แก้ไข</th>
			</tr>
			';

			while ($row = mysql_fetch_array($topup)) {
				echo "
			<tr>
				<td>".$row['topup_number']."</td>
				<td>".date("d/m/Y", strtotime($row['topup_date']))." ".date("G:i:s", strtotime($row['topup_date']))."</td>
				<td>".number_format($row['topup_amount'],2)."</td>
				<td>".number_format($row['usable_amout'],2)."</td>
				<td>".$row['transfer_method']."</td>
				<td><img style='height:50px' src='img/".$row['bank_img']."'>
				<input type='hidden' name='topup_bank' value='".$row['topup_bank']."'>
				</td>
				<td>".formatBankAccNo($row['account_no'])."<br />".$row['account_name']."</td>
				<td>".convertTopupStatus($row['topup_status'])."</td>
				<td>".convertTopupUsedStatus($row['used'])."</td>
				<td>";
				if ($row['used']==0 && $row['topup_status']==0) {
					echo "<a href='topup_edit.php?topup_id=".$row['topup_id']."&redirect=topup_history'><i class='material-icons'>edit</i></a>";
				}
				

				echo "</td>
			</tr>
			";
			}
		echo "
		</table><br />
		<div class='col-md-12 text-center'>
			<ul class='pagination pagination-lg pager' id='myPager'></ul>
		</div>
		<br /><br /><br /><br />
		<br/><p><strong>รายการทั้งหมด ".mysql_num_rows($topup) ." รายการ</strong></p>
		";
		}else{
			echo "<div class='alert alert-danger'>ไม่พบข้อมูล</div>";
		}


	?>
		
		<br /><br /><p><i class="material-icons">info</i><span style="color:red"> ลูกค้าสามารถแก้ไข รายการเติมเงิน ในกรณีที่รายการเติมเงินนั้น รอตรวจสอบ และยังไม่ถูกใช้งาน เท่านั้น </span></p>	
	</form>
	
</div>
	
</div><br /><br />
<script type="text/javascript">

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
				<script src="dist/sweetalert.min.js"></script>
				<link rel="stylesheet" href="dist/sweetalert.css">
		</body>
</html>