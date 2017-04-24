	<?php
	include 'connect.php';
	include 'session.php';

	$order_id = $_GET['order_id'];

	//redirect if status is not confirmed
	$select_order = mysql_query("select * from customer_order where customer_id = '$user_id' 
								and order_id = '$order_id'", $connection);

	if (mysql_num_rows($select_order) > 0) {
		$order_row = mysql_fetch_array($select_order);
		if ($order_row['order_status_code'] == 0 || $order_row['order_status_code'] == 99) {
			/* ยังไม่ได้ชำระเงิน ให้ไปหน้า Step 3 */
			$location = "Location: order_show_detail.php?order_id=".$order_id;
			header($location);
		}
		// else if ($order_row['order_status_code'] >7) {
		// 	/* ชำระค่าขนส่งเรียบร้อยแล้ว ให้ไปหน้า Step 5 */
		// 	$location = "Location: order_show_detail_success.php?order_id=".$order_id;
		// 	header($location);
		// }
	}

	include 'inc/php/functions_statusConvert.php';
	/*
	 * select messge with customer_id and order_id from total_message_log
	 * */
	//$totalMessgeLog = mysql_query("select tml.*,u.email from total_message_log tml,user u where tml.customer_id = '$user_id'
	//		and tml.order_id = '$order_id' and u.userid=tml.user_id  order by tml.message_date", $connection);	
	$sqlTotalMessgeLog='select tml.*,u.email from total_message_log tml LEFT JOIN user u on u.userid = tml.user_id where tml.customer_id = '.$user_id.'
			and tml.order_id = '.$order_id.' order by tml.message_date';
	$totalMessgeLog = mysql_query($sqlTotalMessgeLog, $connection);

	$totalMessageLogData=array();
	if (mysql_num_rows($totalMessgeLog) > 0) {
		while($row=mysql_fetch_array($totalMessgeLog)){
			$totalMessageLogData[]=$row;
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<?php include 'page_script.php';  ?>
		<link rel="stylesheet" href="./css/chatstyle.css" />

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
				 //show page message
				if (isset($_GET['message'])) {
					if ($_GET['message']!="") {
						echo '<div class="alert alert-success container" role="alert"><label>'.$_GET['message'].'</label></div>';
					}
				}
				if (isset($_GET['error'])) {
					if ($_GET['error']!="") {
						if ($_GET['error_id']=="1") {
							$query_site_url = mysql_query("select * from website_config");
							$site_url_row = mysql_fetch_array($query_site_url);
							$site_url = $site_url_row['SITE_URL'];
							echo "<script type='text/javascript'>
											$(document).ready(function(){
												swal({
													title: 'เกิดข้อผิดพลาด!',
													text: 'ยอดเงินที่มีอยู่ไม่พอจ่าย กรุณาเติมเงินค่ะ',
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
						}else if ($_GET['error']=="รายการนี้ได้ถูกกดชำระเงินแล้ว"){
							echo '<div class="alert alert-info container" role="alert"><label>'.$_GET['error'].'</div>';
						}else {
							//echo '<div class="alert alert-danger container" role="alert"><label>'.$_GET['error'].'</div>';
							echo '<script type="text/javascript">
								$(document).ready(function(){
									swal({
										title: "'.$_GET['error'].'",
										text: "",
										width:530,
										type: "info",
										confirmButtonText: "ตกลง",
										closeOnConfirm: true
									})
								})
							</script>';
						}
					}
				}
			 ?>
	<div class="content manage">
		<?php include 'left_menu.php' ?>
		<div class="menu-content col3">

<?php

$select_order = mysql_query("select * ,
								(SELECT count(a.packageid) FROM package a , package_detail b where a.packageid = b.packageid and a.statusid = 3 and b.order_id = o.order_id) as fpay
								from customer_order o, customer_order_shipping s 
								where o.customer_id = '$user_id' 
								and o.order_id = '$order_id'
								and o.order_id = s.order_id", $connection);

if (mysql_num_rows($select_order) > 0) {
	$order_row = mysql_fetch_array($select_order);
	$order_status_code = $order_row['order_status_code'];
	$customer_note = $order_row['customer_note'];
	$show_rate = $order_row['order_rate'];
	// echo "
	// 	<table class='content-light center'>
	// 		<tr>
	// 		<td class='selected'><i class='material-icons'>check_circle</i><br>เลือกสินค้า</td><td>&#10095;</td>
	// 		<td class='selected'><i class='material-icons'>check_circle</i><br>สั่งซื้อสินค้า</td><td>&#10095;</td>
	// 		<td class='selected'><i class='material-icons'>check_circle</i><br>สินค้ารอตรวจสอบ</td><td>&#10095;</td>
	// 		<td class='selected'><i class='material-icons'>check_circle</i><br>สินค้ารอชำระเงิน</td><td>&#10095;</td>
	// 		<td><i class='material-icons'>check_circle</i><br>ส่งมอบสินค้า</td>
	// 		</tr>
	// 	</table>";

	//14/04/2017 Pratchaya Ch. add courousel for status code
	echo "<div>
	<div class='carousel slide' id='myCarousel'>
		<div class='carousel-inner'>";

		$activeClass=''; if (($order_row['order_status_code']<=3 && $order_row['order_status_code']>=0) && $order_row['order_status_code']!=99) $activeClass=' active';
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
			<table style='width:100%' >
				<tr>
					<td style='width:450px;vertical-align: text-top;'>
						<h3>เลขที่ออร์เดอร์ : ".$order_row['order_number']."</h3><br />
						<h3>สถานะออร์เดอร์ : ".convertOrderStatus($order_row['order_status_code'])."</h3><br />
						<h3>บริการขนส่งในประเทศ : ".convertTransportName($order_row['order_shipping_th_option'])."</h3>

					</td>
					<td style='vertical-align: text-top;'>";
					if ($order_status_code < 3) {
						echo "<h3 style='padding:0px 0px 7px 0px;'>ค่าสินค้า : ".number_format($order_row['order_price'], 2)." บาท</h3><br />";
					} else {
						echo "<h3>&nbsp</h3><br />";
					}

					switch ($order_status_code) {
						case 1: //ให้แสดงเฉพาะตรวจสอบแล้วรอชำระเงิน 
							echo "<span style='padding:10px;'><a href='payment.php?action=payment_request&order_id=".$order_id."&redirect=1&show_rate=".$show_rate."'>
							<button>&nbsp&nbsp<i class='material-icons'>payment</i>&nbspชำระเงิน&nbsp&nbsp</button></a></span>";
							break; 
						default:
							if ($order_row['fpay'] > 0 ) {
								echo "<span style='padding:10px;'><a href='package.php'><button>&nbsp&nbsp<i class='material-icons'>payment</i>&nbspชำระค่าขนส่ง&nbsp&nbsp</button></a></span>";
							} else {
										echo "<h3>&nbsp</h3>";
							}
							break;			
					}
				 
				  echo "<br/>
						<h4 style='padding:6px 0px 0px 9px;'>Rate : ".number_format($order_row['order_rate'],2)." @".date("d/m/Y G:i:s", strtotime($order_row['order_rate_date']))."</h4>
					</td>
					<td style='vertical-align: bottom;text-align:right;'>
						<!-- 16/05/2017 Pratchaya Ch. Add print page function -->
						<div class='printMenu'><i class='material-icons' onclick='window.open(\"order_print_table.php?order_id=".$order_id."\");' title='Print'>&#xE8AD;</i></div>
					</td>
					<td style='vertical-align: text-top;text-align:right; '>"; ?>
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
					<?php
					echo "
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
										group by p.shop_name
										order by p.shop_name", $connection);

	if (mysql_num_rows($select_shop_group) > 0) {
		echo "
		<form action='cart.php?action=update&cart_id=' method='post' name='formCart' id='formCart'>
			<table class='content-grid'>";

			$sum_amount = 0;
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

							if ($row['order_status_code'] >= 3) {
								$quantity = $row['backshop_quantity'];
								$price = $row['unitprice'];
								$shipping_cost = $row['order_shipping_cn_cost'];
								$total_price = $row['order_product_totalprice'];
							}else{
								$quantity = $row['quantity'];
								$price = $row['backshop_price'];
								$shipping_cost = $row['backshop_shipping_cost'];
								$total_price = $row['backshop_total_price'];
							}
							
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
							<td style='text-align: center;'>".$row['first_unitquantity']."</td>
							<td style='text-align: center;'>".$quantity."</td>
							<td style='text-align: center;'>".number_format($price,2)."</td>
							<td style='text-align: center;'>".number_format($shipping_cost,2)."</td>
							<td style='text-align: center;'>".number_format($total_price,2)."</td>
							<td>".number_format($row['current_received'],0)."<br />".$detail."<br />".$row['current_updatetime']."</td>
							<td>".number_format($row['return_baht'],2)."<br />".convertRecievedStatus($row['return_baht'],$row['return_status'],$row['order_status_code']);
							if ($row['return_baht']>0) {
								echo " <br /><span text='text-right'><a onclick='showReturnDetail(".$row['order_product_id'].")'><img src='images/more.png'></span></a>";
							}else if ($row['return_baht']<0) {
								echo " <br /><span text='text-right'><a onclick='showExtraDetail(".$row['order_product_id'].")'><img src='images/more.png'></span></a>";
							}
							echo "</td>
						</tr>
							";

							$sum_amount += $row['first_unitquantity'];
							$sum_amount_success += $quantity;
							$sum_price_cn += $price*$quantity;
							$sum_transfer_price_cn += $shipping_cost;
							$sum_price_thb_all += $total_price;
							$sum_received_amount += $row['current_received'];
							$sum_return_money += $row['return_baht'];

							//find order detail สินค้ามาถึงไทยแล้ว
							if ($row['current_status'] == 7) {
								$isDelivered = true;
							}
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
					<td id='price'><b>".$sum_received_amount."</b></td>
					<td><b>".number_format($sum_return_money,2)."</b></td>
				</tr>
			</table>
			<br>";

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
		// 	</table>";
	if ($order_status_code == 0 || $order_status_code == 1) {
		echo "
		<center><a onclick='cancelOrder()'><button type='button' name='cancelorder'><h3>ยกเลิก&nbsp</h3></button></a></center>";
	}

	if ($isDelivered) {
		echo "
		<center><button type='button' name='createPackage' onclick='showCreatePackage($order_id)' ><h3>แจ้งปิดกล่อง&nbsp</h3></button></center>";
	}

$select_address = mysql_query("select * from customer_order c, customer_address a where c.order_id = '$order_id' and c.order_address_id = a.address_id");
	$select_shopping_th_option = mysql_query("select * from customer_order_shipping s where s.order_id = '$order_id'");
	if (mysql_num_rows($select_address) > 0) {
	$row_address = mysql_fetch_array($select_address);
	$row_shopping_th_option = mysql_fetch_array($select_shopping_th_option);

		echo "<br /><h4>วิธีการจัดส่งสินค้าในไทย : ".convertTransportName($row_shopping_th_option['order_shipping_th_option'])."</h4>";

		echo "<h4>ที่อยู่สำหรับจัดส่งสินค้า</h4>";
		echo "<div class='well'>";
		echo "<strong>ชื่อ ".$row_address['address_name']."</strong><br />";
		echo $row_address['line_1']."<br />".$row_address['city'].", ".$row_address['country']."<br />".$row_address['zipcode']."<br />Tel. ".$row_address['phone']; 
		echo "</div>";
		echo "<h4>หมายเหตุ : ".formatNotEmthyValue($customer_note)."</h4><br />";
	}

	
	echo "</table></form>";
		
	}?>
	<!-- message box -->
	
	<style type="text/css">
		
	</style>
	<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
	<script>
	tinymce.init({
		  selector: 'textarea',
		  height: 85,
		  menubar:false,
		  statusbar: false,
		  forced_root_block : "",
		  toolbar: ' styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
		  content_css: '//www.tinymce.com/css/codepen.min.css'
		});

		$(document).ready(function(){
			
			$('#msgSendBtn').on('click',function(){
				/**
				 check data is not empty
				 send data with ajax (order_id,customer_id,txtMessage)
				 return data when select and redirect class "msgBox-send"
				*/
				var content = tinyMCE.get('txtMessage').getContent();
				var orderId = $('#msgOrderId').val();
				if($.trim(content) != ''){
				   // editor is empty ...
					$.post("./message-do.php",{frmMsgSend:$.trim(content),orderId:orderId},function(res){						
						var json = $.parseJSON(res);
						console.log(json);
						var html='<div class="msgBox-send-right">';
						    html+='<div class="msgBox-send-content">';
				        	html+='<div class="triangle-isosceles right">'+json[0].content+'</div>';
				    		html+='</div>';
				    		html+='<div class="msgBox-send-info">';
				            html+='<div class="msgBox-username">Me</div>';
				        	html+='<div class="msgBox-date">'+json[0].message_date+'</div>';
				    		html+='</div>';
							html+='</div>';

							$('.msgBox-send').append(html);
							$(".msgBox-send").animate({ scrollTop: 20000}, 1000);
							
							var tinymce_editor_id = 'txtMessage'; 
							tinymce.get(tinymce_editor_id).setContent('');
				  	});
				}
				
			});
	
		});
	  
	
	</script>
	
	<?php 

?>
<div class="msgBox">
		<div class="msgBox-header">
			<h3>รายการข้อความ</h3>
</div>

<div class="msgBox-send" id="msgBox_Send">
			<?php if(count($totalMessageLogData)>0){ ?>
			<?php foreach($totalMessageLogData as $val){?>
			<?php if($val['user_id']!=0){?>
		
			<div class="msgBox-send-left">
				<div class="msgBox-send-info">
						<div class="msgBox-username"><?php echo $val['email'];?></div>
						<div class="msgBox-date"><?php echo $val['message_date'];?></div>
				</div>
				<div class="msgBox-send-content">
					<div class="triangle-isosceles left"><?php echo $val['content'];?></div>
				</div>
			</div>
			
			<?php 
				}else{
			?>
			<div class="msgBox-send-right">
				<div class="msgBox-send-content">
					<div class="triangle-isosceles right"><?php echo $val['content'];?></div>
				</div>
				<div class="msgBox-send-info">
					<div class="msgBox-username">Me</div>
					<div class="msgBox-date"><?php echo $val['message_date'];?></div>
				</div>
				
			</div>
			<?php }//end if-else check userId!=0?>
			<?php }//end foreach?>
			<?php }//end if?>
		</div>
		
		
	</div>
	
	<form action="" onSubmit="return false;" id="frmMsgSend">
		<div class="msgSend">
				<div class="msgSend-message"><textarea name="txtMessage" id="txtMessage"></textarea></div>
		</div>
		
		<input type="hidden" name="msgOrderId" id="msgOrderId" value="<?php echo $order_id;?>"/>
		<div class="msgSend-btn"><button id="msgSendBtn">Send</button></div>
	</form>
	<!-- end message box -->
	
	<!-- footer detail-->
	<div>
		<form>
		<center><a href='javascript:location.reload(true)'><h3>โหลดหน้าใหม่</h3></a></center>
			<table style='width:100%;' >
			<tr>
			<td>
			<a href='topup.php'><button type='button' class='button'><i class='material-icons'>local_atm</i><h3>เติมเงิน</h3></button></a>
			<a href='payment.php?action=payment_request&order_id=<?php echo $order_id;?>&redirect=1&show_rate=<?php echo $show_rate;?>'><button type='button' class='button'><i class='material-icons'>payment</i><h3>ชำระเงิน</h3></button></a>
			</td>
			<td style='text-align:right;'>
			<a href='#' onclick='gotoTop()'>กลับไปด้านบน</a>
			</td>
			</tr>
			</table>
		</form>
	</div>
	
	</div>
	
	
	
	
		
		
		
	
	
	
	
<?php } ?>

</div>

</div><br /><br />
<!-- Insert Chat by Hack -->

<!-- End insert Chat by Hack -->
<div class="modal" id="createPackage" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">สินค้าที่มาถึงไทยแล้ว</h4>
			</div>
			<div class="modal-body">
				<div id="order_detail_result">
					<?php
						if ($isDelivered) {
							//start table
							$select_shop_group = mysql_query("select *
from customer_order_product
left join customer_order on customer_order_product.order_id = customer_order.order_id 
left join product on customer_order_product.product_id = product.product_id 
left join customer_address on customer_order.order_address_id = customer_address.address_id 
left join customer_order_shipping on customer_order.order_id = customer_order_shipping.order_id
where customer_order.customer_id = '$user_id' 
and customer_order_product.current_status = '7'
group by product.shop_name 
order by customer_order_product.order_id", $connection);


							if (mysql_num_rows($select_shop_group) > 0) {
								echo "
									<table style='border: none;'>";
									$sum_amount = 0;
									$count_shop_group = 0;
									$total_quantity = 0;
									$total_boxed_amount = 0;
									$total_unbox_amount = 0;

								while ($shop_row = mysql_fetch_array($select_shop_group)) {
									$count_shop_group++;
									$shop_name = $shop_row['shop_name'];
									$order_id = $shop_row['order_id'];
									$order_number = $shop_row['order_number'];
									$order_address_id = $shop_row['order_address_id'];
									$order_shipping_option = $shop_row['order_shipping_th_option'];
									$order_address_text = $shop_row['address_name']."<br />".$shop_row['line_1']."<br />".$shop_row['city'].", ".$shop_row['country']."<br />".$shop_row['zipcode']."<br />Tel. ".$shop_row['phone']; 
									$isShowAddress = false;
									$isShowOrderNumber = false;

									echo "
										<tr>
										<td align='center' ";

									if ($isShowSelectAll == false) {
										echo " valign='top' ><a onclick='selectAllItem()'> เลือกทั้งหมด</a> <br /><br />";
										$isShowSelectAll = true;
									}else{
										echo " valign='middle' >";
									}

									echo "
										<input type='checkbox' name='select_delivered[]' data-order_id='$order_id' data-order_address_id='$order_address_id' data-shopname='$shop_name' data-order_shipping_option='$order_shipping_option'>
										</td>
										<td>
										<table class='table table-bordered'>
										<tr style='background-color:#3F51B5;color:#ffffff;'>
											<th><small>เลขที่ออร์เดอร์</small></th>
											<th><small>ร้าน $shop_name</small></th>
											<th><small>จำนวนที่สั่ง</small></th>
											<th><small>ปิดกล่องแล้ว</small></th>
											<th><small>รอปิดกล่อง</small></th>
											<th><small>ที่อยู่จัดส่ง</small></th>
										</tr>
										";


									$select_item = mysql_query("select * 
																from customer_order_product c, customer_order o, product p 
																where c.order_id = o.order_id 
																and c.product_id = p.product_id
																and o.customer_id = '$user_id'
																and o.order_id = '$order_id'
																and c.current_status = '7'
																and p.shop_name = '$shop_name'", $connection);

									if(mysql_num_rows($select_item) > 0){ 
											
												while($row = mysql_fetch_array($select_item)) {

													$order_product_id = $row['order_product_id'];

													$select_boxed = mysql_query("select sum(received_amount) from customer_order_product_tracking t 
														where t.order_product_id = '$order_product_id' 
														and t.packageid > 0");
													
													$boxed = mysql_fetch_array($select_boxed);

													if ($unbox['sum(received_amount)']!=null) {
														$boxed_amount = $boxed['sum(received_amount)'];
													}else{
														$boxed_amount = 0;
													}
													

													$select_unbox = mysql_query("select sum(received_amount) from customer_order_product_tracking t 
														where t.order_product_id = '$order_product_id' 
														and t.packageid = 0");
													
													$unbox = mysql_fetch_array($select_unbox);

													if ($unbox['sum(received_amount)']!=null) {
														$unbox_amount = $unbox['sum(received_amount)'];
													}else{
														$unbox_amount = 0;
													}
													
													if (!$isShowOrderNumber) {
														echo "<tr>
																	<td rowspan='".mysql_num_rows($select_item)."'>";
														if(!$isShowOrderNumber){ 
															echo $order_number;
															$isShowOrderNumber = true;
														}
														echo "</td>";
													}

													if ($row['order_status_code'] >= 3) {
														$quantity = $row['backshop_quantity'];
													}else{
														$quantity = $row['quantity'];
													}
													

													echo "
																	<td><div><a href='".$row['product_url']."' target='_blank'><img class='img-thumb' style='width:50px;' src='".$row['product_img']."'></a></div></td>
																	<td>".$quantity."</td>
																	<td>".$boxed_amount."</td>
																	<td>".$unbox_amount."</td>";

																	$total_quantity += $quantity;
																	$total_boxed_amount += $boxed_amount;
																	$total_unbox_amount += $unbox_amount;

													if (!$isShowAddress) {
														
														echo " <td rowspan='".mysql_num_rows($select_item)."'>";
														if(!$isShowAddress){ 
															if ($row['order_address_id']!=0) {
																echo $order_address_text;
															}else{
																echo "-";
															}
															$isShowAddress = true;

															echo "</td>";
														}
														
													}

													echo "</tr>";
												}
									}

									if (mysql_num_rows($select_shop_group) == $count_shop_group) {
										echo "
													<tr class='bg bg-warning'>
														<td><b>ทั้งหมด</b></td>
														<td><b></b></td>
														<td><b>".$total_quantity."</b></td>
														<td><b>".$total_boxed_amount."</b></td>
														<td><b>".$total_unbox_amount."</b></td>
														<td><b></b></td>
													</tr>";
									}

									echo "</table>
												</td>
											</tr>";
								}
								echo "
								</table>";
							}else{
								echo "ไม่มีรายการสินค้าที่ต้องปิดกล่องค่ะ";
							}
						}
					?>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btn-checkAddress" onclick="checkAddress()" 
					<?php if (mysql_num_rows($select_shop_group) == 0) {
						echo " disabled ";
					} ?> >
					<span class="glyphicon glyphicon-shopping-cart"></span> ปิดกล่อง
				</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">กลับ</button>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="selectAddress" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">สินค้าที่คุณเลื่อกมีที่อยู่ไม่ตรงกัน กรุณาระบุที่อยู่สำหรับจัดส่ง</h4>
			</div>
			<div class="modal-body">
				<div id="select_address_result">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btncheckAddress" onclick="verifyField()">
					<span class="glyphicon glyphicon-shopping-cart"></span> ตกลง
				</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="createPackageResult" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">สร้างกล่อง</h4>
			</div>
			<div class="modal-body">
				<div id="createPackageResult_result">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btncheckAddress" onclick="location.reload();">
					ตกลง
				</button>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="showReturnDetailModal" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">รายการคืนเงิน</h4>
			</div>
			<div class="modal-body">
				<div id="showReturnDetailModal_result">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
			</div>
		</div>
	</div>
</div>
<div class="modal" id="showExtraDetailModal" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">รายการที่ต้องชำระเงินเพิ่ม</h4>
			</div>
			<div class="modal-body">
				<div id="showExtraDetailModal_result">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
			</div>
		</div>
	</div>
</div>

				<?php include 'modal.php';  ?>
				<?php include 'footer.php';  ?>

				<script src="js/core.js"></script>
				<script type="text/javascript">

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
								}
						function gotoTop(){
							$('html, body').animate({ scrollTop: 0 }, 'slow');
						}

						function runScript(e) {
								if (e.keyCode == 13) {
										searchURL();
								}
						}

						function selectAllItem(){
							// if($('input[name="select_delivered[]"]').is(':checked')) {
							//     $('input[name="select_delivered[]"]').removeAttr('checked');
							// } else {
							    $('input[name="select_delivered[]"]').attr('checked','checked');
							// }
							
						}

						function showCreatePackage(order_id){
							$('#createPackage').modal('show');
							// var req;
							// if (window.XMLHttpRequest) {
							// 	req = new XMLHttpRequest();
							// }
							// else if (window.ActiveXObject) {
							// 	req = new ActiveXObject("Microsoft.XMLHTTP"); 
							// }
							// else{
							// 	alert("Browser error");
							// 	return false;
							// }
							// req.onreadystatechange = function()
							// {
							// 	if (req.readyState == 4) {
							// 		var resultarea = document.getElementById('order_detail_result');
							// 		resultarea.innerHTML = req.responseText;
							// 	}
							// 	else
							// 	{
							// 		var resultarea = document.getElementById('order_detail_result');
							// 		resultarea.innerHTML = "<center><img src=progress_bar.gif><br /><br /><small>กำลังค้นหาข้อมูล กรุณารอสักครู่ค่ะ</small></center>";

							// 	}
							// }
							// var searchValue = document.getElementById('searchText').value;

							// req.open("GET", "search_delivered_item.php", true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
							// req.send(null); 
						}

						var array_order_id = [];
						var array_shopname = [];
						var array_address_id = [];
						var array_shipping_option = [];

						function checkAddress(){
							array_order_id = [];
							array_shopname = [];
							array_address_id = [];
							array_shipping_option = [];

							$('input[name="select_delivered[]"]').each(function () {
								if (this.checked == "1") {
									array_order_id.push(this.getAttribute("data-order_id"));
									array_shopname.push(this.getAttribute("data-shopname"));
									array_address_id.push(this.getAttribute("data-order_address_id"));
									array_shipping_option.push(this.getAttribute("data-order_shipping_option"));
								}
							});

							//check address conflit
							var address_conflit = false;
							var first_address_id = array_address_id[0];
							for (i = 0; i < array_address_id.length; i++) {
						    if (parseInt(first_address_id) != parseInt(array_address_id[i])) {
									address_conflit = true;
								}
							}
							var shipping_conflit = false;
							var first_shipping_id = array_shipping_option[0];
							for (i = 0; i < array_shipping_option.length; i++) {
						    if (parseInt(first_shipping_id) != parseInt(array_shipping_option[i])) {
									shipping_conflit = true;
								}
							}

							//alert(array_order_id);
							//alert(array_shopname);
							//alert(array_address_id);

							if (address_conflit || shipping_conflit) {
								//alert("address are conflit");
								showSelectAddress(array_order_id,array_shopname,array_address_id,shipping_conflit,array_shipping_option);
							}else{
								//alert("--go to create package-");
								createNewPackageRequest(array_shipping_option[0],array_address_id[0]);
							}
						}

						function showSelectAddress(array_order_id,array_shopname,array_address_id,shipping_conflit,array_shipping_option){
							$('#createPackage').modal('hide');
							$('#selectAddress').modal('show');
							var req;
							if (window.XMLHttpRequest) {
								req = new XMLHttpRequest();
							}
							else if (window.ActiveXObject) {
								req = new ActiveXObject("Microsoft.XMLHTTP"); 
							}
							else{
								alert("Browser error");
								return false;
							}
							req.onreadystatechange = function()
							{
								if (req.readyState == 4) {
									var resultarea = document.getElementById('select_address_result');
									resultarea.innerHTML = req.responseText;
								}
								else
								{
									var resultarea = document.getElementById('select_address_result');
									resultarea.innerHTML = "<center><img src=progress_bar.gif><br /><br /><small>กำลังโหลดข้อมูล กรุณารอสักครู่ค่ะ</small></center>";
								}
							}

							req.open("GET", "ajax/show-address.php?array_address_id="+JSON.stringify(array_address_id)+"&shipping_conflit="+shipping_conflit+"&array_shipping_option="+JSON.stringify(array_shipping_option), true);
							req.send(null); 
						}

						function toggleAddress(radio){
							if(radio.value == "มารับสินค้าด้วยตัวเอง"){
								document.getElementById("select_address_content").style.display = 'none';
								//$('#select_address_content').hide();
							}else{
								document.getElementById("select_address_content").style.display = 'block';
								//$('#select_address_content').show();
							}
							
						}

						function verifyField(){

							var shipping_option = $('input[name="select_shipping_option"]:checked').val();
							var address_id = $('input[name="select_address_id"]:checked').val();

							if (shipping_option == null){
								alert('กรุณาเลือกวิธีการจัดส่งสินค้าค่ะ');
							}else if(shipping_option == 'มารับสินค้าด้วยตัวเอง'){
								createNewPackageRequest(shipping_option,0);
							}else if(shipping_option != 'มารับสินค้าด้วยตัวเอง'){
								if (address_id == null) {
									alert('กรุณาเลือกที่อยู่สำหรับจัดส่งสินค้าค่ะ');
								}else{
									createNewPackageRequest(shipping_option,address_id);
								}
							}

						}

						function createNewPackageRequest(shipping_option,address_id){
							//alert("createNewPackageRequest = "+shipping_option+","+address_id+","+array_order_id+","+array_shopname);
							$('#createPackageResult').modal('show');
							$('#selectAddress').modal('hide');
							$('#createPackage').modal('hide');
							var req;
							if (window.XMLHttpRequest) {
								req = new XMLHttpRequest();
							}
							else if (window.ActiveXObject) {
								req = new ActiveXObject("Microsoft.XMLHTTP"); 
							}
							else{
								alert("Browser error");
								return false;
							}
							req.onreadystatechange = function()
							{
								if (req.readyState == 4) {
									var resultarea = document.getElementById('createPackageResult_result');
									resultarea.innerHTML = req.responseText;
								}
								else{
									var resultarea = document.getElementById('createPackageResult_result');
									resultarea.innerHTML = "<center><img src=progress_bar.gif><br /><br /><small>กำลังโหลดข้อมูล กรุณารอสักครู่ค่ะ</small></center>";
								}
							}

							req.open("GET", "ajax/create-package.php?array_order_id="+JSON.stringify(array_order_id)+"&address_id="+address_id+"&shipping_option="+shipping_option+"&array_shopname="+JSON.stringify(array_shopname), true);
							req.send(null);
						}

						function showReturnDetail(order_product_id){
							$('#showReturnDetailModal').modal('show');
							var req;
							if (window.XMLHttpRequest) {
								req = new XMLHttpRequest();
							}
							else if (window.ActiveXObject) {
								req = new ActiveXObject("Microsoft.XMLHTTP"); 
							}
							else{
								alert("Browser error");
								return false;
							}
							req.onreadystatechange = function()
							{
								if (req.readyState == 4) {
									var resultarea = document.getElementById('showReturnDetailModal_result');
									resultarea.innerHTML = req.responseText;
								}
								else{
									var resultarea = document.getElementById('showReturnDetailModal_result');
									resultarea.innerHTML = "<center><img src=progress_bar.gif><br /><br /><small>กำลังโหลดข้อมูล กรุณารอสักครู่ค่ะ</small></center>";
								}
							}

							req.open("GET", "ajax/show_return_detail.php?order_product_id="+order_product_id, true);
							req.send(null);
						}

						function showExtraDetail(order_product_id){
							$('#showExtraDetailModal').modal('show');
							var req;
							if (window.XMLHttpRequest) {
								req = new XMLHttpRequest();
							}
							else if (window.ActiveXObject) {
								req = new ActiveXObject("Microsoft.XMLHTTP"); 
							}
							else{
								alert("Browser error");
								return false;
							}
							req.onreadystatechange = function()
							{
								if (req.readyState == 4) {
									var resultarea = document.getElementById('showExtraDetailModal_result');
									resultarea.innerHTML = req.responseText;
								}
								else{
									var resultarea = document.getElementById('showExtraDetailModal_result');
									resultarea.innerHTML = "<center><img src=progress_bar.gif><br /><br /><small>กำลังโหลดข้อมูล กรุณารอสักครู่ค่ะ</small></center>";
								}
							}

							req.open("GET", "ajax/show_extra_detail.php?order_product_id="+order_product_id, true);
							req.send(null);
						}

				
				$(document).ready(function(){
					<?php if (isset($_GET['msg'])) {?>
					  $("html,body").animate(
						{
							scrollTop: $('body').prop('scrollHeight')-940
						}, 500);
					 
					  $("#msgBox_Send").animate(
						{
							scrollTop: $("#msgBox_Send").prop('scrollHeight')
						}, 500);
					  <?php }  ?>
					 
				});

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
