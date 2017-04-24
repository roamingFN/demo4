<?php
	include 'connect.php';
	include 'session.php';

	$order_id = $_GET['order_id'];

	//redirect if status is confirmed
	$select_order = mysql_query("select * from customer_order where customer_id = '$user_id' 
								and order_id = '$order_id'", $connection);

	if (mysql_num_rows($select_order) > 0) {
		$order_row = mysql_fetch_array($select_order);
		$order_number = $order_row['order_number'];
		$order_status_code = $order_row['order_status_code'];
		$order_price = $order_row['order_price'];
		$order_create_date = $order_row['date_order_created'];
		
		if(isset($_GET['cancelorder']) && $order_status_code != 99 &&
		   ($order_status_code == 0 || $order_status_code == 1) ) {
			$customer_code = $_SESSION['CX_login_code'];
			$update = mysql_query("update customer_order set order_status_code = '99', cancel_date = now(), cancel_by = '$customer_code' 
				where order_id = '$order_id'");
			$insert_credit = mysql_query("insert customer_statement(customer_id,order_id,statement_name,statement_date,statement_detail,credit) 
				values('$user_id','$order_id','ค่าสินค้า เลขที่สั่งซื้อ ".$order_number."','".$order_create_date."','','$order_price')");
			$insert_debit = mysql_query("insert customer_statement(customer_id,order_id,statement_name,statement_date,statement_detail,debit) 
				values('$user_id','$order_id','ลูกค้ายกเลิก เลขที่สั่งซื้อ ".$order_number."',NOW(),'','$order_price')");
			
			//if ($update && $insert_credit && $insert_debit) {
				//echo '<div class="alert alert-success container" role="alert"><label>ท่านได้ทำการยกเลิกออร์เดอร์เรียบร้อย</label></div>';						
			//}
		}else if ($order_status_code > 0 && $order_status_code != 99) {
			$location = "Location: order_show_detail_confirmed.php?order_id=".$order_id;
			header($location);
		}

	}

	include 'inc/php/functions_statusConvert.php';

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

<!DOCTYPE html>
<html>
  <head>
    <?php include 'page_script.php';  ?>

    <!-- 14/04/2017 Pratchaya Ch. style for corousel -->
    <style type="text/css">
    	<style type="text/css">
		.carousel-inner .active.left { left: -25%; }
		.carousel-inner .next        { left:  25%; }
		.carousel-inner .prev		 { left: -25%; }
		.carousel-control 			 { width:  4%; }
		.carousel-control.left,.carousel-control.right {margin-left:15px;background-image:none;}
		.printMenu i {
			font-size:42px;
			margin:3px;
			padding:5px;
			transition:0.2s;
			color: #3f51b5;
		}
		.printMenu i:hover {
			color:#fff;
			background:#3f51b5;
			cursor:pointer;
			border-radius:50%;
		}
    </style>
  </head>
  <body>
   <?php include 'nav_bar.php';  ?>
   <?php
    if ($update && $insert_credit && $insert_debit) {
		echo "<script type='text/javascript'>
						$(document).ready(function(){
							swal({
								title: 'แจ้งผลการยกเลิก',
								text: 'ท่านได้ทำการยกเลิกออร์เดอร์เรียบร้อยแล้ว',
								type: 'success',
								confirmButtonText: 'ตกลง',
							})
						})	
			</script>";							
	}
   ?>
	<div class="content manage">
		<?php include 'left_menu.php' ?>
		<div class="menu-content col3">


<?php

$select_order = mysql_query("select * from customer_order o, customer_order_shipping s 
								where o.customer_id = '$user_id' 
								and o.order_id = '$order_id'
								and o.order_id = s.order_id ", $connection);

if (mysql_num_rows($select_order) > 0) {
	$order_row = mysql_fetch_array($select_order);
	$order_status_code = $order_row['order_status_code'];
	$customer_note = $order_row['customer_note'];
	// echo "<table class='content-light center'>
	// 		<tr>
	// 		<td class='selected'><i class='material-icons'>check_circle</i><br>เลือกสินค้า</td><td>&#10095;</td>
	// 		<td class='selected'><i class='material-icons'>check_circle</i><br>สั่งซื้อสินค้า</td><td>&#10095;</td>
	// 		<td class='selected'><i class='material-icons'>check_circle</i><br>สินค้ารอตรวจสอบ</td><td>&#10095;</td>
	// 		<td><i class='material-icons'>check_circle</i><br>สินค้ารอชำระเงิน</td><td>&#10095;</td>
	// 		<td><i class='material-icons'>check_circle</i><br>ส่งมอบสินค้า</td>
	// 		</tr>
	// 	</table>";

	//14/04/2017 Pratchaya Ch. add courousel for status code
	echo "<div>
	<div class='carousel slide' id='myCarousel'>
		<div class='carousel-inner'>";

		$activeClass=''; if (($order_row['order_status_code']<=3 && $order_row['order_status_code']>=0) || $order_row['order_status_code']==99) $activeClass=' active';
		echo	"<div class='item".$activeClass."' style='margin-left: 10%;margin-right: 10%;'>
				<table class='content-light center'>
					<tr>";
						$selectClass = ''; if ($order_row['order_status_code']>=0 && $order_row['order_status_code']!=99) $selectClass= 'selected';
						echo "<td class='".$selectClass."' style='width: 25%'><i class='material-icons'>check_circle</i><br>รอตรวจสอบ</td><td>&#10095;</td>";
						$selectClass = ''; if ($order_row['order_status_code']>=1 && $order_row['order_status_code']!=99) $selectClass= 'selected';
						echo "<td class='".$selectClass."' style='width: 25%'><i class='material-icons'>check_circle</i><br>ตรวจสอบแล้วรอชำระเงิน</td><td>&#10095;</td>";
						$selectClass = ''; if ($order_row['order_status_code']>=2 && $order_row['order_status_code']!=99) $selectClass= 'selected';
						echo "<td class='".$selectClass."' style='width: 25%'><i class='material-icons'>check_circle</i><br>รอตรวจสอบการโอนเงินค่าสินค้า</td><td>&#10095;</td>";
						$selectClass = ''; if ($order_row['order_status_code']>=3 && $order_row['order_status_code']!=99) $selectClass= 'selected';
						echo "<td class='".$selectClass."' style='width: 25%'><i class='material-icons'>check_circle</i><br>ชำระเงินแล้ว ดำเนินการสั่งซื้อ</td><td>&#10095;</td>
					</tr>
				</table>
			</div>";

			$activeClass=''; if (($order_row['order_status_code']<=7 && $order_row['order_status_code']>=4) && $order_row['order_status_code']!=99) $activeClass=' active';
			echo "<div class='item".$activeClass."' style='margin-left: 10%;margin-right: 10%;'>
				<table class='content-light center'>
					<tr>";
						$selectClass = ''; if ($order_row['order_status_code']>=4 && $order_row['order_status_code']!=99) $selectClass= 'selected';
						echo "<td class='".$selectClass."'><i class='material-icons'>check_circle</i><br>ร้านค้ากำลังส่งสินค้ามาโกดังจีน</td><td>&#10095;</td>";
						$selectClass = ''; if ($order_row['order_status_code']>=5 && $order_row['order_status_code']!=99) $selectClass= 'selected';
						echo "<td class='".$selectClass."'><i class='material-icons'>check_circle</i><br>โกดังจีนรับของแล้ว</td><td>&#10095;</td>";
						$selectClass = ''; if ($order_row['order_status_code']>=6 && $order_row['order_status_code']!=99) $selectClass= 'selected';
						echo "<td class='".$selectClass."'><i class='material-icons'>check_circle</i><br>สินค้าอยู่ระหว่างมาไทย</td><td>&#10095;</td>";
						$selectClass = ''; if ($order_row['order_status_code']>=7 && $order_row['order_status_code']!=99) $selectClass= 'selected';
						echo "<td class='".$selectClass."'><i class='material-icons'>check_circle</i><br>สินค้าถึงไทยแล้ว รอจัดกล่อง</td><td>&#10095;</td>
					</tr>
				</table>
			</div>";

			$activeClass=''; if (($order_row['order_status_code']<=11 && $order_row['order_status_code']>=8) && $order_row['order_status_code']!=99) $activeClass=' active';
			echo "<div class='item".$activeClass."' style='margin-left: 10%;margin-right: 10%;'>
				<table class='content-light center'>
					<tr>";
						$selectClass = ''; if ($order_row['order_status_code']>=8 && $order_row['order_status_code']!=99) $selectClass= 'selected';
						echo "<td class='".$selectClass."'><i class='material-icons'>check_circle</i><br>ลงกล่องแล้ว รอชำระค่าขนส่ง</td><td>&#10095;</td>";
						$selectClass = ''; if ($order_row['order_status_code']>=9 && $order_row['order_status_code']!=99) $selectClass= 'selected';
						echo "<td class='".$selectClass."'><i class='material-icons'>check_circle</i><br>รอตรวจสอบการโอนเงินค่าขนส่ง</td><td>&#10095;</td>";
						$selectClass = ''; if ($order_row['order_status_code']>=10 && $order_row['order_status_code']!=99) $selectClass= 'selected';
						echo "<td class='".$selectClass."'><i class='material-icons'>check_circle</i><br>ชำระค่าขนส่งแล้วรอจัดส่งสินค้า</td><td>&#10095;</td>";
						$selectClass = ''; if ($order_row['order_status_code']>=11 && $order_row['order_status_code']!=99) $selectClass= 'selected';
						echo "<td class='".$selectClass."'><i class='material-icons'>check_circle</i><br>สินค้าจัดส่งให้ลูกค้าแล้ว</td><td></td>
					</tr>
				</table>
			</div>
		</div>
	  <a class='left carousel-control' href='#myCarousel' data-slide='prev'><i class='glyphicon glyphicon-chevron-left'></i></a>
	  <a class='right carousel-control' href='#myCarousel' data-slide='next'><i class='glyphicon glyphicon-chevron-right'></i></a>
	</div>
	</div>";

	echo	"<div class='content-line'></div>
		<br />
		<div>
			<table style='width:100%;'>
				<tr>
					<td style='width:400px;vertical-align: text-top;'>
						<h3>เลขที่ออร์เดอร์ : ".$order_row['order_number']."</h3><br />
						<h3>สถานะออร์เดอร์ : ".convertOrderStatus($order_row['order_status_code'])."</h3><br />
						<h3>บริการขนส่งในประเทศ : ".convertTransportName($order_row['order_shipping_th_option'])."</h3><br />
					</td>
					<td style='vertical-align: text-top;'>
						<h3>&nbsp</h3><br />
						<h3>&nbsp</h3><br />
						<h4 style='padding:3px 0px 0px 0px;'>Rate : ".number_format($order_row['order_rate'],2)." @ ".date("d/m/Y G:i:s", strtotime($order_row['order_rate_date']))."</h4>
					</td>
					<td style='vertical-align: bottom;text-align:right;'>
						<!-- 16/05/2017 Pratchaya Ch. Add print page function -->
						<div class='printMenu'><i class='material-icons' onclick='window.open(\"order_print_table.php?order_id=".$order_id."\");' title='Print'>&#xE8AD;</i></div>
					</td>
					<td style='vertical-align: text-top;text-align:right;'>"; ?>
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
					<?php echo"
					</td>
				</tr>
			</table>
		</div>";




	//start table
	$select_shop_group = mysql_query("	select shop_name
										from customer_order_product c, customer_order o, product p 
										where o.order_id = c.order_id 
										and c.product_id = p.product_id
										and o.customer_id = '$user_id'
										and o.order_id = '$order_id'
										group by p.shop_name", $connection);

	if (mysql_num_rows($select_shop_group) > 0) {
		echo "
		<form action='topup.php' method='post' name='formCart' id='formCart'>
			<table class='content-grid'>";

	    $sum_amount = 0;
	    $sum_amount_success = 0;
	    $sum_price_cn = 0;
	    $sum_transfer_price_cn = 0;
	    $sum_transfer_price_cn_th = 0;
	    $sum_price_thb_all = 0;
	    $sum_received_amount = 0;
	    $sum_return_money = 0;
	    $i = 0;
		while ($shop_row = mysql_fetch_array($select_shop_group)) {
			$shop_name = $shop_row['shop_name'];

			echo "
				<tr>
					<th style='text-align: center;'>ลำดับ</th>
					<th style='text-align: center;'>ร้าน $shop_name</th>
					<th style='text-align: center;'>จำนวนที่สั่ง</th>
					<th style='text-align: center;'>จำนวนที่สั่งได้</th>
					<th style='text-align: center;'>ราคา<br />หยวน (¥)</th>
					<th style='text-align: center;'>ค่าขนส่งในจีน<br />หยวน (¥)</th>
					<th style='text-align: center;'>ทั้งหมด<br />(บาท)</th>
					<th style='text-align: center;'>จำนวนที่ได้รับ</th>
					<th style='text-align: center;'>คืนเงิน</th>
				</tr>
				";


			$select_item = mysql_query("select * , c.received_amount as current_received
										from customer_order_product c, customer_order o, product p, order_remark r 
										where o.order_id = c.order_id 
										and c.product_id = p.product_id
										and c.remark_id = r.remark_id 
										and o.customer_id = '$user_id'
										and o.order_id = '$order_id'
										and p.shop_name = '$shop_name'", $connection);

			if(mysql_num_rows($select_item) > 0){ 

			    
		        while($row = mysql_fetch_array($select_item)) {
		        	
		        	if ($row['order_status']=="2") {
		        		$detail = "<span style='color:red'>สั่งไม่ได้ : ".$row["remark_tha"]."</span>";
		        		$table_style = "style='background-color:lightgray'";
		        	}else{
		        		$detail = convertOrderProductStatus($row['current_status']);
		        		$table_style = "";
		        	}

		        	echo "
		        <tr ".$table_style." >
		        	<td style='text-align: center;'>".++$i."</td>
		        	<td><div><a href='".$row['product_url']."' target='_blank'><img class='img-thumb' style='width:50px;' src='".$row['product_img']."' title='".$row['product_size']." ".$row['product_color']."'></a></div></td>
							<td style='text-align: center;'>".convertStatementZero($row['first_unitquantity'])."</td>
							<td style='text-align: center;'>".convertStatementZero($row['quantity'])."</td>
							<td style='text-align: center;'>".number_format($row['unitprice'],2)."</td>
							<td style='text-align: center;'>".number_format($row['order_shipping_cn_cost'],2)."</td>
							<td style='text-align: center;'>".number_format($row['order_product_totalprice'],2)."</td>
							<td>".number_format($row['current_received'],0)."<br />".$detail."<br />".$row['current_updatetime']."</td>
							<td>".number_format($row['return_baht'],2)."<br />".convertRecievedStatus($row['return_baht'],$row['return_status'],$row['order_status_code'])."</td>
		        </tr>
		        	";

		        	$sum_amount += $row['first_unitquantity'];
		        	$sum_amount_success += $row['quantity'];
					$sum_price_cn += $row['unitprice']*$row['quantity'];
					$sum_transfer_price_cn += $row['order_shipping_cn_cost'];
					$sum_price_thb_all += $row['order_product_totalprice'];
					$sum_received_amount += $row['current_received'];
					$sum_return_money += $row['return_baht'];
		        }
		    }
		}
		echo "
				<tr class='sub'>
					<td><b>ทั้งหมด</b></td>
					<td></td>
					<td style='text-align: center;'><b>".$sum_amount."</b></td>
					<td style='text-align: center;'><b>".$sum_amount_success."</b></td>
					<td><b></b></td>
					<td style='text-align: center;'><b>".number_format($sum_transfer_price_cn,2)."</b></td>
					<td style='text-align: center;'><b>".number_format($sum_price_thb_all,2)."</b></td>
					<td id='price'><b>".number_format($sum_received_amount,0)."</b></td>
					<td><b>".number_format($sum_return_money,2)."</b></td>
				</tr>
			</table>
			<br />";

		// $select_order = mysql_query("select *
		// 								from customer_order o
		// 								where o.customer_id = '$user_id'
		// 								and o.order_id = '$order_id'", $connection);
		// $row = mysql_fetch_array($select_order);

		// echo "
		// 	<table class='content-grid' style='font-size:16px;color:orangered;'>
		// 		<tr>
		// 			<td style='text-align:right; padding-right:2em;'>ค่าตีลังไม้ (บาท)</td>
		// 			<td style='text-align:right; padding-right:2em; width:17%;'><b>".number_format($row['box_price'],2)."</b></td>
		// 		</tr>
		// 		<tr>
		// 			<td style='text-align:right; padding-right:2em;'>ค่าขนส่งภายในประเทศ (บาท)</td>
		// 			<td style='text-align:right; padding-right:2em; width:17%;'><b>".number_format($row['th_transfer_price'],2)."</b></td>
		// 		</tr>
		// 		<tr>
		// 			<td style='text-align:right; padding-right:2em;'>ค่าใช้จ่ายอื่นๆ (บาท)</td>
		// 			<td style='text-align:right; padding-right:2em; width:17%;'><b>".number_format($row['other_price'],2)."</b></td>
		// 		</tr>
		// 	</table>
		// 	";
	if ($order_status_code == 0 || $order_status_code == 1) {
		echo "
		<center><a onclick='cancelOrder()'><button type='button' name='cancelorder'><h3>ยกเลิก&nbsp</h3></button></a></center><br />";
	}
	

	$select_address = mysql_query("select * from customer_order c, customer_address a where c.order_id = '$order_id' and c.order_address_id = a.address_id");
  $select_shopping_th_option = mysql_query("select * from customer_order_shipping s where s.order_id = '$order_id'");
  if (mysql_num_rows($select_address) > 0) {
	$row_address = mysql_fetch_array($select_address);
	$row_shopping_th_option = mysql_fetch_array($select_shopping_th_option);

		echo "<h4>วิธีการจัดส่งสินค้าในไทย : ".convertTransportName($row_shopping_th_option['order_shipping_th_option'])."</h4>";

		echo "<h4>ที่อยู่สำหรับจัดส่งสินค้า</h4>";
		echo "<div class='well'>";
		echo "<strong>ชื่อ ".$row_address['address_name']."</strong><br />";
		echo $row_address['line_1']."<br />".$row_address['city'].", ".$row_address['country']."<br />".$row_address['zipcode']."<br />Tel. ".$row_address['phone']; 
		echo "</div>";
		echo "<h4>หมายเหตุ : ".formatNotEmthyValue($customer_note)."</h4>"; 
	}
    
	
	
			echo "
			<!--<h3>ค่าขนส่ง จีน-ไทย</h3>
			<table class='content-grid'>
				<tr >
					<th>ร้านค้า</th>
					<th>Tracking จีน</th>
					<th>ขนาด M3</th>
					<th>น้ำหนัก Kg</th>
					<th>Rate</th>
					<th>ยอดรวม</th>
				</tr>";

		$select_shop_group_2 = mysql_query("	select *
										from customer_order_product c, customer_order o, product p 
										where o.order_id = c.order_id 
										and c.product_id = p.product_id
										and o.customer_id = '$user_id'
										and o.order_id = '$order_id'
										group by p.shop_name", $connection);

		$sum_m3 = 0;
		$sum_weight =0;
		$sum_shipping_price = 0;

		while ($shop_row = mysql_fetch_array($select_shop_group_2)) {
			$shop_name = $shop_row['shop_name'];

			echo "
				<tr>
					<td>".$shop_name."</td>
					<td>".$shop_row['order_shipping_cn_ref_no']."</td>
					<td>".$shop_row['order_shipping_cn_m3_size']."</td>
					<td>".$shop_row['order_shipping_cn_weight']."</td>
					<td></td>
					<td>".$shop_row['order_shipping_cn_weight']*$shop_row['order_shipping_rate']."</td>
				</tr>";
				$sum_m3 += $shop_row['order_shipping_cn_m3_size'];
				$sum_weight += $shop_row['order_shipping_cn_weight'];
				$sum_shipping_price += ($shop_row['order_shipping_cn_weight']*$shop_row['order_shipping_rate']);
			}

			$update_weight = mysql_query("update customer_order 
				set order_shipping_weight='$sum_weight' where order_id='$order_id'");

			echo "
				<tr class='sub'>
					<td></td>
					<td>ยอดรวม</td>
					<td>".$sum_m3."</td>
					<td>".$sum_weight."</td>
					<td>รวม</td>
					<td id='shipping_cn-td'>".$sum_shipping_price."</td>
				</tr>
			</table >

			<h3>ค่าขนส่งภายในไทย</h3>
			<table class='content-grid'>
				<tr >
					<th>บริการขนส่งในประเทศ</th>
					<th>วันที่ส่งสินค้า/วันที่ลูกค้ามารับสินค้า</th>
					<th>Tracking ไทย/เลขที่บิล</th>
					<th>น้ำหนัก KG</th>
					<th>ค่าขนส่ง (บาท)</th>
				</tr>
				<tr>
					<td>".convertTransportName($order_row['order_shipping_th_option'])."</td>
					<td></td>
					<td>".$order_row['order_shipping_th_ref_no']."</td>
					<td>".$order_row['order_shipping_th_weight']."</td>
					<td>".$order_row['order_shipping_th_cost']."</td>
				</tr>
				<tr  class='sub'>
					<td></td>
					<td></td>
					<td></td>
					<td>รวม</td>
					<td id='shipping_th'>".$order_row['order_shipping_th_cost']."</td>
				</tr>
			</table><br />-->

			<center><a href='javascript:location.reload(true)'><h3>โหลดหน้าใหม่</h3></a></center><br />&emsp;
			<table style='width:100%;'>
			<tr>
			<td>
			<a href='topup.php'><button type='button' class='button'><i class='material-icons'>local_atm</i><h3>เติมเงิน</h3></button></a>
			<button type='button' class='button' disabled ><i class='material-icons'>payment</i><h3>ชำระเงิน</h3></button>
			</td>
			<td style='text-align:right;'>
			<a href='#' onclick='gotoTop()'>กลับไปด้านบน</a>
			</td>
			</tr>
			</table>
			<br /><br />
		</form>";
	}
	echo "
		
	</div>";

} 

?>

</div>
</div><br /><br />

<?php if ($order_status_code ==0) { ?>

<script type="text/javascript">
$(document).ready(function() {
	swal({
		title: "",
		text: "ขณะนี้ออร์เดอร์เลขที่ <span style='color:#DC8909'><?php echo $order_number; ?></span> อยู่ในสถานะรอตรวจสอบ กรุณารอเจ้าหน้าที่ตรวจสอบสินค้าก่อนถึงจะชำระค่าสินค้าได้ค่ะ",
		type: "info",
		confirmButtonText: "ตกลง"
	});
});
</script>

<?php } ?>


        <?php include 'modal.php';  ?>
        <?php include 'footer.php';  ?>

        <script src="js/core.js"></script>
        <script type="text/javascript">
        	function gotoTop(){
				$('html, body').animate({ scrollTop: 0 }, 'slow');
			}

            function runScript(e) {
                if (e.keyCode == 13) {
                    searchURL();
                }
            }

            function cancelOrder(){
            	swal({   
            		title: "ยกเลิกออร์เดอร์",   
            		text: "คุณต้องการยกเลิกออร์เดอร์นี้หรือไม่",   
            		type: "warning",   
            		showCancelButton: true,   
            		confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
            		confirmButtonText: "ยกเลิก",   
            		cancelButtonText: "ไม่ยกเลิก"
				}).then(function(isConfirm) {
					if (isConfirm) {
						document.location.href = 'order_show_detail.php?order_id='+<?php echo $order_id; ?>+'&cancelorder=1';
					}
				});	
            	//}, function(isConfirm){   
            	//	if (isConfirm) {     
            	//		document.location.href = 'order_show_detail.php?order_id='+<?php echo $order_id; ?>+'&cancelorder=1';
            	//	}
            	//});
            }

            //14/04/2017 Pratchaya Ch. add courousel for status code
            $('#myCarousel').carousel({
				interval: 99999999
			});
			checkitem = function() {
				var $this;
				$this = $("#myCarousel");
				if ($("#myCarousel .carousel-inner .item:first").hasClass("active")) {
					$this.children(".left").hide();
					$this.children(".right").show();
				} else if ($("#myCarousel .carousel-inner .item:last").hasClass("active")) {
					$this.children(".right").hide();
					$this.children(".left").show();
				} else {
					$this.children(".carousel-control").show();
				}
			};
			checkitem();
			$("#myCarousel").on("slid.bs.carousel", "", checkitem);

        </script>
        <script src="dist/sweetalert2.min.js"></script>
        <link rel="stylesheet" href="dist/sweetalert2.css">
    </body>
</html>