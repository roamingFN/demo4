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
	<h1>ประวัติการแจ้งถอนเงิน</h1>

	<form role="form" method="post" action="withdraw_history.php?action=search">
		<table class="content-light">
			<tr>
				<td class="pd_t16">วันที่</td>
				<td style="width:170px;">
					<div class="input-group input-append date" id="datePicker" >
		                <input type="text" class="form-control" name="withdraw_date" value="<?php //echo date("d/m/Y"); ?>" placeholder="วันที่แจ้งถอนเงิน" />
		                <span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
		            </div>
				</td>
				<td class="pd_t16">ธนาคาร</td>
				<td>
					<div class="form-group">
					  	<select name="bank_name" class="form-control">
							<option value="">ไม่เลือก</option>
						  	<option value="ธนาคารกรุงเทพ">ธนาคารกรุงเทพ</option>
						  	<option value="ธนาคารกสิกรไทย">ธนาคารกสิกรไทย</option>
						  	<option value="ธนาคารไทยพาณิชย์">ธนาคารไทยพาณิชย์</option>
						  	<option value="ธนาคารกรุงไทย">ธนาคารกรุงไทย</option>
						  	<option value="ธนาคารกรุงศรีอยุธยา">ธนาคารกรุงศรีอยุธยา</option>
						  	<option value="ธนาคารเกียรตินาคิน">ธนาคารเกียรตินาคิน</option>
						  	<option value="ธนาคารทหารไทย">ธนาคารทหารไทย</option>
						  	<option value="ธนาคารธนชาต">ธนาคารธนชาต</option>
						</select>
					</div>
				</td>
				<td class="pd_t16">เลขที่การถอนเงิน</td>
				<td><input type="text" name="withdraw_id" class="form-control" placeholder="เลขที่การถอนเงิน"></td>
				<td rowspan="2" class="text-center">
					<button type="submit" name="search" value="Submit" style="margin-bottom: 15px;"><i class="material-icons">search</i></button><br><br>
					<a href="withdraw_history.php" class="button">แสดงทั้งหมด</a>

				</td>
			</tr>
			<tr>
				<td class="pd_t16">ยอดเงิน</td>
				<td><input type="text" name="withdraw_amount" class="form-control" placeholder="จำนวนเงินที่ต้องการ" onkeypress="return ((event.charCode >= 48 && event.charCode <= 57)||event.charCode == 46)"></td>
				<td class="pd_t16">สถานะ</td> 
				<td>
					<div class="form-group">
					  	<select class="form-control" name="withdraw_status">
					  		<option value="">ไม่เลือก</option>
					  		<option value="0">รอตรวจสอบ</option>
					  		<option value="1">ตรวจสอบแล้ว</option>
					  		<option value="2">ยกเลิก</option>
					  	</select>
					</div>
				</td>
				<td class="pd_t16">เลขที่บัญชี</td>
				<td>
					<div class="form-group">
					  	<select class="form-control" name="account_no">
					  		<option value="">ไม่เลือก</option>
					  	<?php 
					  		$select_bank = mysql_query("select * from customer_bank_account where customer_id = '$user_id' group by account_no");
					  		if (mysql_num_rows($select_bank) > 0) {
					  			while($row = mysql_fetch_array($select_bank)){
					  				echo "<option value='".$row['account_no']."'>".$row['account_no']."</option>";
					  			}
					  		}

					  	?>

					  	</select>
					</div>
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

		if (isset($_POST['withdraw_date'])&& $_POST['withdraw_date']!="") 	{ 	
												$withdraw_date 	= $_POST['withdraw_date']; 
												$withdraw_date = str_replace('/', '-', $withdraw_date);
												$withdraw_date = date('m/d/Y', strtotime($withdraw_date));
											}
		if (isset($_POST['bank_name'])) 		{ $bank_name 		= $_POST['bank_name']; }
		if (isset($_POST['withdraw_id'])) 		{ $withdraw_id 	= $_POST['withdraw_id']; }
		if (isset($_POST['withdraw_amount'])) 	{ $withdraw_amount = $_POST['withdraw_amount']; }
		if (isset($_POST['withdraw_status'])) 	{ $withdraw_status	= $_POST['withdraw_status']; }
		if (isset($_POST['account_no'])) 	{ $account_no 	= $_POST['account_no']; }



		$query = "select * from customer_request_withdraw w, customer_bank_account b 
			where w.customer_id = '$user_id' and w.customer_bank_account_id = b.bank_account_id ";


		if($withdraw_date != ''){ $query .= " and w.withdraw_date > STR_TO_DATE('$withdraw_date 00:00:00','%c/%e/%Y %T')  
			and w.withdraw_date < STR_TO_DATE('$withdraw_date 23:59:59','%c/%e/%Y %T')"; }
		if($bank_name != ''){ $query .= " and b.bank_name = '$bank_name'"; } 
		if($withdraw_id != ''){ $query .= " and w.widthdraw_number like '%$withdraw_id%'"; }
		if($withdraw_amount != ''){ $query .= " and w.withdraw_amount = '$withdraw_amount'"; }
		if($withdraw_status != ''){ $query .= " and w.withdraw_status = '$withdraw_status'"; }
		if($account_no != ''){ $query .= " and b.account_no = '$account_no'"; }

		$query .= " order by w.withdraw_date desc ";

		//echo $query;

		$withdraw = mysql_query($query);

		if (mysql_num_rows($withdraw) > 0) {
			echo '
		<form role="form">
		<table class="content-grid">
			<tr class="bg-primary">
				<th>วันที่แจ้งถอนเงิน</th>
				<th>เลขที่การถอนเงิน</th>
				<th>ยอดเงิน</th>
				<th>ธนาคาร</th>
				<th>ชื่อบัญชี</th>
				<th>เลขบัญชี</th>
				<th>สถานะ</th>
				<th>วันที่คืนเงิน</th>
				<th>แก้ไข</th>
			</tr>
			';

			while ($row = mysql_fetch_array($withdraw)) {
				echo "
			<tr>
				<td>".date("d/m/Y", strtotime($row['withdraw_date']))."</td>
				<td>".$row['withdraw_number']."</td>
				<td>".number_format($row['withdraw_amount'],2)."</td>
				<td>".$row['bank_name']."</td>
				<td>".$row['account_name']."</td>
				<td>".$row['account_no']."</td>
				<td>".convertWithdarwStatus($row['withdraw_status'])."</td>
				<td>".formatDate($row['withdraw_payment_date'])."</td>
				<td>";
				if ($row['withdraw_status']==0) {
					echo "<a href='withdraw.php?withdrawid=".$row['withdraw_request_id']."&mode=edit&redirect=withdraw_history'><i class='material-icons'>edit</i></a>";
				}
				echo "</td>";
				echo "
			</tr>
			";
			}
		echo "
		</table>
		<br/><p>รายการทั้งหมด ".mysql_num_rows($withdraw) ." รายการ</p>
		";
		}else{
			echo "<div class='alert alert-danger'>ไม่พบข้อมูล</div>";
		}


	?>
		
		<br /><br />	
	</form>
	
</div>
	
</div><br /><br />
<script type="text/javascript">
	
$(document).ready(function() {
    $('#datePicker').datepicker({
            format: 'dd/mm/yyyy',
            todayHighlight: true
        })
        .on('changeDate', function(e) {
            // Revalidate the date field
            $('#eventForm').formValidation('revalidateField', 'transfer_date');
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