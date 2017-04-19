<?php
	include 'connect.php';
	include 'session.php';
	include 'inc/php/functions_statusConvert.php';
?>
<!DOCTYPE html>
<html>
	<head>
		<?php include 'page_script.php';  ?>
		<?php 
			function format_product_available($amout){
				if ($amout == 0) {
					return "-";
				}else{
					return $amout;
				}
			}
		?>
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
			<div class="col-md-4">
				<h1>รายการสั่งซื้อ</h1>
			</div>
			<div class="col-md-8"> 
			</div>
			<div class="col-md-12">
				<form action="order_list.php" method="post"  class="form-inline">
						<div>
							<label>จากวันที่ : </label>
								<div class="input-group input-append date" id="datePicker1">
									<input type="text" class="form-control" name="order_start_time" placeholder="จากวันที่" />
									<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
							</div>
							<label>&nbsp&nbsp&nbspถึงวันที่ : </label>
							<div class="input-group input-append date" id="datePicker2">
									<input type="text" class="form-control" name="order_end_time" placeholder="ถึงวันที่" />
									<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
							</div>
							<label>&nbsp&nbsp&nbspสถานะ : </label>
							<select class="form-control" name="select_status" style="width:200px;">
								<option value="-">ไม่เลือก</option>
								<?php
								$query_order_status = mysql_query("select * from order_status_code");
								while ($row = mysql_fetch_array($query_order_status)) {
									echo "<option value=".$row['status_id'].">".$row['des']."</option>";
								}
								?>
							</select>
							&nbsp&nbsp
							<button type="submit" value="Submit" name="search_order_list">ค้นหา</button>
						</div>
						<br />
				</form>
				<div class="alert alert-danger" role="alert">
					<u><span style="font-size:large">เพื่อผลประโยชน์ของลูกค้า</span></u><br />
					<p>รบกวนลูกค้าตรวจสอบความถูกต้องทุกครั้งก่อนชำระเงิน เนื่องจากราคาสินค้าของร้านค้ามีการปรับเปลี่ยนอยู่ตลอดเวลา </p><br />
					<small>*** หาก พบราคาสินค้าไม่ถูกต้องภายหลังสามารถแจ้งเจ้าหน้าที่ตรวจสอบกับทางร้านค้า ได้ แต่หากร้านค้าไม่ยอมรับเรื่องราคาผิด ก็จะไม่สามารถขอเงินคืนได้</small>
				</div>
				<div class="col-md-12">
					<?php
						if (isset($_POST['search_order_list'])) {
							echo "<b>ผลการค้นหาของออร์เดอร์&nbsp&nbsp&nbsp วันที่: ".formatNotEmthyValue($_POST['order_start_time'])."&nbsp&nbsp&nbsp ถึงวันที่: ".formatNotEmthyValue($_POST['order_end_time'])."&nbsp&nbsp&nbsp สถานะ: ".formatNotEmthyValue(convertOrderStatus($_POST['select_status']))." </b><br /><br />";
						}
					?>
				</div>
			</div>
		</div>
		
		
				
		<?php
			$query_status = "";
			if (isset($_POST['select_status']) && $_POST['select_status']!="-") {
				$query_status .= " and o.order_status_code = ".$_POST['select_status']." ";
			}

			if (isset($_POST['order_start_time']) && isset($_POST['order_end_time'])) {
				if ($_POST['order_start_time']!="" && $_POST['order_end_time']!="") {
					$date1 = $_POST['order_start_time'];
					$date1 = str_replace('/', '-', $date1);
					$date1 = date('Y-m-d', strtotime($date1));
					$date2 = $_POST['order_end_time'];
					$date2 = str_replace('/', '-', $date2);
					$date2 = date('Y-m-d', strtotime($date2));
					$query_status .= " and ( o.date_order_created between '".$date1." 00:00:00' and '".$date2." 23:59:59' ) ";
				}
			}

			$select_orders = mysql_query("select * from customer_order o, customer_order_shipping s 
				where o.customer_id = '$user_id' and o.order_id = s.order_id ".$query_status." order by o.order_id desc");

			// echo "select * from customer_order o, customer_order_shipping s 
			// 	where o.customer_id = '$user_id' and o.order_id = s.order_id ".$query_status." order by o.order_id desc";

			if (mysql_num_rows($select_orders) > 0) {
				echo "
				<div class='table-responsive'>
				<table class='content-grid' id='orderTable'>
					<thead>
					<tr class='bg-primary' >
						<th style='width:50px;'>ลำดับ</th>
						<th>เลขที่ออร์เดอร์</th>
						<th style='width:100px;'>วันที่</th>
						<th style='width:100px;'>ปรับปรุงล่าสุด</th>
						<th style='width:250px;'>สถานะ</th>
						<th style='text-align:right; padding-right:2em;'>จำนวนแบบ</th>
						<th style='text-align:right; padding-right:2em;'>จำนวนชิ้น</th>
						<th style='text-align:right; padding-right:2em;'>จำนวนที่สั่งได้</th>
						<th colspan='2'>ตัวเลือก</th>
					</tr>
					</thead>
					<tbody id='myTable'>
				";
				$product_row = 1;
				while ($row = mysql_fetch_array($select_orders)) {
					$order_id = $row['order_id'];
					$amount = mysql_query("select count(order_product_id) from customer_order_product where order_id = '$order_id'");
					// echo "select count(order_product_id) from 'customer_order_product' where order_id = '$order_id'";
					$amount_row = mysql_fetch_array($amount);
					echo "
					<tr>
						<td rowspan='3'  style='text-align:center'>".$product_row."</td>
						<td><a href='order_show_detail.php?order_id=".$row['order_id']."'>".$row['order_number']."</a></td>
						<td>".$row['date_order_created']."</td>
						<td>"; if($row['date_order_last_update']!=""){echo $row['date_order_last_update'];}else{ echo "-";} echo "</td>
						<td>".convertOrderStatus($row['order_status_code'])."</td>
						<td style='text-align:right; padding-right:2em;'>".$amount_row['count(order_product_id)']."</td>
						<td style='text-align:right; padding-right:2em;'>".$row['product_quantity']."</td>
						<td style='text-align:right; padding-right:2em;'>".format_product_available($row['product_available'])."</td>
						<td>";
					if ($row['order_status_code'] != 99) {
						echo "<a href='order_show_detail.php?order_id=".$row['order_id']."' title='ชำระเงิน' >ชำระเงิน</a>";
					}
					echo "</td>
						<td>"."<a onclick='confirm_renew(".$row['order_id'].")' title='สั่งซื้อสินค้าอีกครั้ง' ><i class='material-icons'>repeat</i></a>"."</td>
					</tr>
					<tr>
						<td colspan='7' rowspan='2' style='background-color:#f4f4f4'>";
						$order_id = $row['order_id'];
						$select_img = mysql_query("select product_img 
							from customer_order_product c, product p
							where c.product_id = p.product_id
							and c.order_id = '$order_id'
							group by product_img");		
							if (mysql_num_rows($select_img) > 0) {
								while ($row_img = mysql_fetch_array($select_img)) {
									echo "<img style='height:60px;' class='img-thumbnail' src='".$row_img['product_img']."'> ";
								}
							}

						echo "
						</td>
						<td style='background-color:#f4f4f4'>THB</td>
						<td style='background-color:#f4f4f4'>".number_format($row['order_price'],2)."</td>
					</tr>
					<tr style='background-color:#f4f4f4'>
						<td colspan='2'><a href='order_show_detail.php?order_id=".$row['order_id']."'>รายละเอียด <span class='glyphicon glyphicon-circle-arrow-right'></span></a></td>
					</tr>
					";
					$product_row++;
				}
				echo "
				</tbody>
				</table>
						</div>
				<br />
				<div class='col-md-6'>
					<p><b>รายการทั้งหมด ".mysql_num_rows($select_orders)." รายการ</b></p>
				</div>
				<div class='col-md-6 text-right'>
					<a href='#' onclick='gotoTop()'>กลับไปด้านบน</a>
				</div>

						<div class='col-md-12 text-center'>
							<ul class='pagination pagination-lg pager' id='myPager'></ul>
					</div>";

			}else {
				echo "ไม่มีรายการสั่งซื้อในขณะนี้";
			}
		?>
	</div>
</div>


<script type="text/javascript">
	
$(document).ready(function(){
		
	$('#myTable').pageMe({pagerSelector:'#myPager',showPrevNext:true,hidePageNumbers:false,perPage:15});
		
});

$(document).ready(function(){

		$('.renew_order').on('click', function () {
				return confirm('คุณต้องการสั่งซื้อสินค้าในออร์เดอร์นี้อีกรอบหรือไม่');
		});
		
});

$(document).ready(function() {
	$('#datePicker1').datepicker({
			format: 'dd/mm/yyyy',
			todayHighlight: true
	})
	.on('changeDate', function(e) {
			// Revalidate the date field
			$('#eventForm').formValidation('revalidateField', 'order_start_time');
	});

	$('#datePicker2').datepicker({
			format: 'dd/mm/yyyy',
			todayHighlight: true
	})
	.on('changeDate', function(e) {
			// Revalidate the date field
			$('#eventForm').formValidation('revalidateField', 'order_end_time');
	});

	<?php
		if (isset($_GET['message'])) {
			$message = $_GET['message'];
			$message = str_replace("-", "", $message);
			$message = str_replace("%20", "", $message);
			$message = str_replace("<br>", "", $message);

			if (stristr($message, "ชำระเงินออร์เดอร์เลขที่")){
				echo '
				swal({   
					title: "'.$message.'",   
					text: "",   
					type: "success",  
					confirmButtonText: "ตกลง",   
					closeOnConfirm: true 
				});
				';
			}else if ($_GET['message']!="") {
				echo '
				swal({   
					title: "'.$_GET['message'].'",   
					text: "",   
					type: "info",  
					confirmButtonText: "ตกลง",   
					closeOnConfirm: true 
				});
				';
			}
		}
	?>

});

function confirm_renew(renewId){

	var ask = confirm('คุณต้องการสั่งซื้อสินค้าในออร์เดอร์นี้อีกรอบหรือไม่');
		if (ask){
				window.location.href = 'renew_order.php?order_id='+renewId;
		}
}

function gotoTop(){
	$('html, body').animate({ scrollTop: 0 }, 'slow');
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