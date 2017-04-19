<?php
	include 'connect.php';
	include 'session.php';
	include 'inc/php/functions_statusConvert.php';
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

	<table style='width:100%' >
	<tr>
	<td style='vertical-align: text-top;width:460px;'>
		<h3><i class="material-icons">content_copy</i> รายการบัญชีลูกค้า</h3>
			<a class="button" href="excel/statement_generate.php" >ไฟล์ Excel</a>
			<a href="#" class="button" onclick='window.print()'>พิมพ์หน้านี้</a>
		<h1>รายละเอียดบัญชี</h1>
	</td>
	<td style='vertical-align: text-top;text-align:right;'>
		<div class="right">
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
			<table class="content-bordered">
				<tr><td>ยอดเงินที่เหลืออยู่ : </td><td><b><?php echo number_format($aproved_amount,2); ?></b></td></tr>
				<tr><td>ยอดเงินที่รอตรวจสอบ : </td><td><b><?php echo number_format($unapprove_amount,2); ?></b></td></tr>
				<tr><td>ยอดรวม : </td><td><b><?php echo number_format($aproved_amount+$unapprove_amount,2); ?></b></td></tr>
			</table>
		</div>
	</td>
	</tr>
	</table>
	
	<form role="form">
	<table class="content-grid">
		<tr class="bg-primary">
			<th style='width:100px;'>วันที่</th>
			<th>รายการ</th>
			<th>เลขที่ออเดอร์</th>
			<th>ประเภท</th>
			<th>ยอดเข้า</th>
			<th>ยอดออก</th>
			<th>คงเหลือ</th>
		</tr>
		
<?php
/**
 * 
 * select * from config_path
 */
$sqlConfigPath='select site_url from website_config';
$currURI='';
$sizeURL=mysql_query($sqlConfigPath);
if(mysql_num_rows($sizeURL)>0){
	while($row = mysql_fetch_array($sizeURL)){
		$currURI=$row['site_url'];
	}
	
}


$total = 0;
$shownumber = '';
$package_status = '';
//$select_statement = mysql_query("select * from customer_statement s LEFT OUTER JOIN customer_order o ON s.order_id=o.order_id where s.customer_id = '$user_id' order by s.statement_date , s.statement_id asc");
//$select_statement = mysql_query("select * from customer_statement s LEFT OUTER JOIN customer_order o ON s.order_id=o.order_id where s.customer_id = '$user_id' order by s.statement_id asc");
$select_statement = mysql_query(" select s.* , o.order_number , p.packageno , (select b.packagestatusname from package a , package_status b where a.statusid = b.packagestatusid and a.packageid = s.packageid ) packagestatus from customer_statement s LEFT OUTER JOIN customer_order o ON s.order_id=o.order_id LEFT OUTER JOIN package p on s.packageid = p.packageid where s.customer_id = '$user_id'  order by s.statement_date , statement_id asc");
if (mysql_num_rows($select_statement) > 0) {
	while ($row = mysql_fetch_array($select_statement)) {
		$total += $row['debit'];
		$total -= $row['credit'];

		if (isset($row['packageno'])) {  //package 
			$uri=$currURI.'package_detail.php?packageid='.$row['packageid'] ;
			$package_status = ' - (' . $row['packagestatus'] . ')';
			$shownumber = $row['packageno'];
		}
		else { // order
			$uri=$currURI.'order_show_detail_confirmed.php?order_id='.$row['order_id'] ;
			$package_status = '';
			$shownumber = $row['order_number'];
		}
		
		//old
		//<td>".$row['statement_name']." ".$package_status."</td>
		//<td><a href='".$uri."'>".$row['order_number']."</a></td>
		
		echo "
		<tr>
			<td>".date("d/m/Y G:i:s", strtotime($row['statement_date']))."</td>
			<td>".$row['statement_name']." ".$package_status."</td>
			<td><a href='".$uri."'>".$shownumber."</a></td>
			<td>".$row['statement_detail']."</td>
			<td>".convertStatementZero(number_format($row['debit'],2))."</td>
			<td>".convertStatementZero(number_format($row['credit'],2))."</td>
			<td>".number_format($total,2)."</td>
		</tr>
		";
	}
}
?>

		<tr class="sub">
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td colspan="2"  style='text-align:center'>ยอดเงินคงเหลือ</td>
			<td><?php echo number_format($total,2); ?></td>
		</tr>
	</table>
	</form>
	<br />
	<p><small>- รายการเติมเงินที่เป็นสถานะ "รอตรวจสอบ" จะไม่ขึ้นในบัญชีลูกค้า จนกระทั่งเปลี่ยนเป็นสถานะ "ตรวจสอบแล้ว"</small></p><br />
	<p><small>- ค่าสินค้าที่เป็นสถานะ "ยังไม่ได้Confirm" อาจมีการเปลี่ยนแปลงยอดเงินหลังจาก ที่มีการสั่งซื้อจริงกับทางร้านค้า</small></p><br />
	<p><small>- ในกรณีกดถอนเงิน เมื่อเจ้าหน้าที่ตรวจสอบผ่านแล้ว ระบบจะตัดเงินคงเหลือทันที แต่เงินจะเข้าบัญชีลูกค้าในช่วงระยะเวลาดำเนินการ 7-10 วัน</small></p>
	
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