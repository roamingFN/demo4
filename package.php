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
			.table_clickable{
				cursor: pointer;
			}
			.details { display: none; }
		</style>
	</head>
	<body>
		<?php include 'nav_bar.php';  ?>
		<?php
				 //show page message
			if (isset($_GET['message'])) {
				if ($_GET['message']!="") {
					echo '<div class="alert alert-success container" role="alert"><label>'.$_GET['message'].'</label></div>';
				}
			}
			if (isset($_GET['error'])) {
				if ($_GET['error']!="") {
					echo '<div class="alert alert-danger container" role="alert"><label>'.$_GET['error'].'</div>';
				}
			}
		?>
	<div class="content manage">
		<?php include 'left_menu.php' ?>
		<div class="menu-content col3">

	<div class="row">
	<div class="col-md-8">
		<h1>กล่อง</h1>
	</div>
	</div>
	
	<form role="form" method="post" action="package.php?action=search">
		<table class="content-light">
			<tr>
				<td>เลขที่กล่อง</td>
				<td>
					<input type="text" name="package_no" class="form-control" placeholder="เลขที่กล่อง" style="width:300px;">
				</td>
				<td class='space'> </td>
				<td>เลขที่ Order</td>
				<td>
					<input type="text" name="order_no" class="form-control" placeholder="เลขที่ Order" style="width:300px;">
				</td>
			</tr>
			<tr>
				<td>วันที่ปิดกล่อง</td>
				<td>
					<div class="input-group input-append date" style="width:300px;">
						<input type="text" class="form-control" id="mdate1" name="package_date" placeholder="วันที่ปิดกล่อง" />
						<span class="input-group-addon add-on"><span class="glyphicon glyphicon-calendar"></span></span>
					</div>
				</td>
				<td class='space'> </td>
				<td>เลขที่ Tracking</td>
				<td>
					<input type="text" name="tracking_no" class="form-control" placeholder="เลขที่ Tracking" style="width:300px;">
				</td>
			</tr>
			<tr>
				<td>สถานะ</td> 
				<td>
					<div class="form-group" style="width:300px;">
						<select class="form-control" name="package_status">
							<option value="-">ไม่เลือก</option>
							<?php
								$query_package_status = mysql_query("select * from package_status");
								while ($row = mysql_fetch_array($query_package_status)) {
									echo "<option value=".$row['packagestatusid'].">".$row['packagestatusname']."</option>";
								}
							?>
						</select>
					</div>
				</td>
				<td class='space'> </td>
				<td class="text-center" colspan="2">
					<button type="submit" name="search" value="Submit" ><i class="material-icons">search</i> ค้นหา</button> &nbsp
					<button type="button" onclick="document.location.href = 'package.php'">แสดงทั้งหมด</button>
				</td>
			</tr>
		</table>
	</form>

	<?php

		function checkTransport($transport_th_name){
			if ($transport_th_name==null) {
				return "มารับสินค้าด้วยตัวเอง";			
			}else{
				return $transport_th_name;
			}
		}

		$package_no = '';
		$tracking_no = '';
		$order_no = '';
		$package_date = '';
		$package_status = '';

		if (isset($_POST['package_no'])) 		{ $package_no 		= $_POST['package_no']; }
		if (isset($_POST['tracking_no'])) 		{ $tracking_no 		= $_POST['tracking_no']; }
		if (isset($_POST['order_no'])) 		{ $order_no 	= $_POST['order_no']; }
		if (isset($_POST['package_date']) && $_POST['package_date']!='') 	{ 
			$package_date = $_POST['package_date']; 
			$package_date = str_replace('/', '-', $package_date);
			$package_date = date('m/d/Y', strtotime($package_date));
		}
		if (isset($_POST['package_status'])) 	{ $package_status	= $_POST['package_status']; }


		$query = "select *, p.packageid as packageid,p.statusid as statusid , p.total as totalall from package p 
		left join website_transport t on p.shippingid = t.transport_id 
		left join package_detail d on p.packageid = d.packageid 
		left join customer_order_product c on d.order_product_id = c.order_product_id 
		left join customer_order o on c.order_id = o.order_id 
		left join customer_order_product_tracking r on d.order_product_tracking_id = r.order_product_tracking_id
		where p.customerid = '$user_id' ";

		if($package_no != ''){ $query .= " and p.packageno = '$package_no'"; } 
		if($tracking_no != ''){ $query .= " and r.tracking_no = '$tracking_no'"; } 
		if($order_no != ''){ $query .= " and o.order_no = '$order_no'"; }
		if($package_date != ''){  $query .= " and p.createdate > STR_TO_DATE('$package_date 00:00:00','%c/%e/%Y %T') 
																					and p.createdate < STR_TO_DATE('$package_date 23:59:59','%c/%e/%Y %T') "; }
		if($package_status != ''){ 
			$query .= " and p.statusid = '$package_status'"; 
		}

		$query .= " group by p.packageid ";
		$query .= " order by p.packageid desc ";

		//echo $query;

		$package = mysql_query($query);

		if (mysql_num_rows($package) > 0) {
			echo '
		<form role="form">
		<table class="content-grid" id="topupHistotyTable">
			<tr class="bg-primary">
				<th>ลำดับ</th>
				<th>เลขที่กล่อง</th>
				<th>วันที่ปิดกล่อง</th>
				<th>จำนวนชิ้น</th>
				<th>น้ำหนัก(Kg)</th>
				<th>ขนาด(คิว)</th>
				<th>สถานะ</th>
				<th>ตัวเลือก</th>
			</tr>
			';

			$count = 1;
			while ($row = mysql_fetch_array($package)) {
				echo "
				<tr>
				  <td rowspan='3' class='text-center' >".$count."</td>
					<td><a href='package_detail.php?packageid=".$row['packageid']."'>".$row['packageno']."</a></td>
					<td>".date("d/m/Y", strtotime($row['createdate']))."</td>
					<td>".$row['total_quantity']."</td>
					<td>".$row['total_weight']."</td>
					<td>".$row['total_m3']."</td>
					<td>".formatPackageNo($row['statusid'])."</td>
					<td>";
					if ($row['used']==0 && $row['topup_status']==0) {
						echo "<a href='package_detail.php?packageid=".$row['packageid']."'>ชำระเงิน</a>";
					}
					echo "</td>
				</tr>
				<tr style='background-color:#f4f4f4'>
					<td colspan='6' rowspan='2'>";

					// $select_product_image = mysql_query("select product_img 
					// 	from package_detail d, customer_order_product c, product p 
					// 	where d.order_product_id = c.order_product_id
					// 	and c.product_id = p.product_id
					// 	and d.packageid = ".$row['packageid']." 
					// 	group by product_img ");
					$select_product_image = mysql_query("select product_img 
						from package_product pp, product p 
						where pp.product_id = p.product_id
						and pp.packageid = ".$row['packageid']." 
						group by product_img");
					if (mysql_num_rows($select_product_image) > 0) {
						while ($row_img = mysql_fetch_array($select_product_image)) {
							echo "<img style='height:60px;' class='img-thumbnail' src='".$row_img['product_img']."'> ";
						}
					}

					echo "</td>
					<td>THB ".number_format($row['totalall'],2)."
					</td>
				</tr>
				<tr style='background-color:#f4f4f4'>
					<td><a href='package_detail.php?packageid=".$row['packageid']."'>รายละเอียด <span class='glyphicon glyphicon-circle-arrow-right'></span></a></td>
				</tr>";
				
				// $select_package_detail = mysql_query("select * from package_detail d,customer_order_product_tracking t
				// 	where d.packageid = '".$row['packageid']."' 
				// 	and d.order_product_tracking_id = t.order_product_tracking_id 
				// 	order by packageorder");
				// if (mysql_num_rows($select_package_detail)>0) {
				// 	echo "<tr class='details'>
				// 					<td colspan='8'>
				// 					<table class='content-grid'>
				// 						<tr style='background-color:#CFCFCF'>
				// 							<td>ลำดับที่</td>
				// 							<td>Order No.</td>
				// 							<td>Tracking No.</td>
				// 							<td>M3</td>
				// 							<td>Wg.</td>
				// 							<td>Rate</td>
				// 							<td>ราคา</td>
				// 						</tr>";
				// 		while ($row = mysql_fetch_array($select_package_detail)) {
				// 			echo "
				// 						<tr>
				// 							<td>".$row['packageorder']."</td>
				// 							<td>".$row['order_id']."</td>
				// 							<td>".$row['tracking_no']."</td>
				// 							<td>".$row['m3']."</td>
				// 							<td>".$row['weight']."</td>
				// 							<td>".$row['rate']."</td>
				// 							<td>".$row['total']."</td>
				// 						</tr>";
				// 		}
				// 	echo 	 "</table>
				// 					</td>
				// 				</tr>";
				// }


				$count++;

			}
		echo "
		</table><br />
		<div class='col-md-6'>
			<p><b>รายการทั้งหมด ".mysql_num_rows($package)." รายการ</b></p>
		</div>
		<div class='col-md-6 text-right'>
			<a href='#' onclick='gotoTop()'>กลับไปด้านบน</a>
		</div>
		<div class='col-md-12 text-center'>
			<ul class='pagination pagination-lg pager' id='myPager'></ul>
		</div>
		<br /><br /><br /><br />
		
		";
		}else{
			echo "<div class='alert alert-danger'>ไม่พบข้อมูล</div>";
		}


	?>
		
		<br />
	</form>
	
</div>
	
</div><br /><br />
<script type="text/javascript">

$(document).ready(function() {
	$('#mdate1').datepicker({
			dateFormat: 'dd/mm/yy',
			todayHighlight: true,
	})
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

$(document).ready(function(){
		
	$('#topupHistotyTable').pageMe({pagerSelector:'#myPager',showPrevNext:true,hidePageNumbers:false,perPage:2});
		
});
	
$(document).ready(function() {
		$('#mdate1').datepicker({
				dateFormat: 'dd/mm/yy',
				maxDate: new Date(),
				todayHighlight: true,
		})
});

function gotoTop(){
	$('html, body').animate({ scrollTop: 0 }, 'slow');
}

// jQuery(document).ready(function($) {
// 		$(".table_clickable").click(function() {
// 			$(this).parents("tr").next().slideToggle();
// 			// also tried this...
// 			// $(this).parents("table").nextAll(".details").slideToggle();
// 			return false;
// 		});
// });

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
				<link href="css/jquery-ui.css" rel="stylesheet"/>
<script charset="utf-8" src="js/jquery-ui.js"></script>
<link href="css/jquery-ui-timepicker-addon.css" rel="stylesheet"/>
<script charset="utf-8" src="js/jquery-ui-timepicker-addon.js"></script>
		</body>
</html>