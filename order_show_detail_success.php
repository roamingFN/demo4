<?php
	include 'connect.php';
	include 'session.php';
	include 'inc/php/functions_statusConvert.php';

	$order_id = $_GET['order_id'];

	//redirect if status is not confirmed
	$select_order = mysql_query("select * from customer_order where customer_id = '$user_id' 
								and order_id = '$order_id'", $connection);

	if (mysql_num_rows($select_order) > 0) {
		$order_row = mysql_fetch_array($select_order);
		if ($order_row['order_status_code'] <= 7) {
			$location = "Location: order_show_detail_confirmed.php?order_id=".$order_id;
			header($location);
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

<?php

$select_rate = mysql_query("select rate_date,rate_cny, shipping_rate_cny from website_config");
$select_rate_row = mysql_fetch_array($select_rate);
$rate_date = $select_rate_row['rate_date'];
$rate = $select_rate_row['rate_cny'];
$shipping_rate = $select_rate_row['shipping_rate_cny'];

$select_order = mysql_query("select * from customer_order o, customer_order_shipping s 
								where o.customer_id = '$user_id' 
								and o.order_id = '$order_id'
								and o.order_id = s.order_id", $connection);

if (mysql_num_rows($select_order) > 0) {
	$order_row = mysql_fetch_array($select_order);

	echo "
		<h3><i class='material-icons'>content_copy</i> ออร์เดอร์ ".$order_row['order_number']."</h3>
		<div class='right'><a href='#' class='more'>พิมพ์หน้านี้</a></div>
		<table class='content-light center'>
			<tr>
			<td class='selected'><i class='material-icons'>check_circle</i><br>เลือกสินค้า</td><td>&#10095;</td>
			<td class='selected'><i class='material-icons'>check_circle</i><br>สั่งซื้อสินค้า</td><td>&#10095;</td>
			<td class='selected'><i class='material-icons'>check_circle</i><br>สินค้ารอตรวจสอบ</td><td>&#10095;</td>
			<td class='selected'><i class='material-icons'>check_circle</i><br>สินค้ารอชำระเงิน</td><td>&#10095;</td>
			<td class='selected'><i class='material-icons'>check_circle</i><br>ส่งมอบสินค้า</td><td>&#10095;</td>
			</tr>
		</table>
		<div class='content-line'></div>
		<h1>ตรวจสอบราคาเสร็จสิ้น ออเดอร์รอการชำระเงิน</h1>
		<div class='center'>
			<table class='content-bordered'>
				<tr><td>เลขที่ออร์เดอร์ : </td><td><b>".$order_row['order_number']."</b></td></tr>
				<tr><td>สถานะสินค้า : </td><td><b>".convertOrderStatus($order_row['order_status_code'])."</b></td></tr>
				<tr><td>บริการขนส่งในประเทศ : </td><td><b>".convertTransportName($order_row['order_shipping_th_option'])."</b></td></tr>
				<tr><td>เลขที่ขนส่งในประเทศ : </td><td><b>".$order_row['order_shipping_th_ref_no']."</b></td></tr>
			</table>
			<table class='content-bordered'>
				<tr><td>ยอดค่าสินค้า (confirm แล้ว) : &emsp;</td><td><b>".number_format($order_row['order_price'],2)."&emsp;THB</b></td></tr>";
				if ($order_row['order_status_code']<6) {
					echo "
						<tr><td>ยอดค่าขนส่ง : </td><td><b>ยังไม่มีค่าขนส่งในขณะนี้</b></td></tr>
						<tr><td>ยอดรวม (confirm แล้ว) : </td><td><b>".number_format($order_row['order_price'],2)."&emsp;THB</b></td></tr>";
				}else{
					echo "
						<tr><td>ยอดค่าขนส่ง (confirm แล้ว) : </td><td><b>".number_format($order_row['transfer_price'],2)." &emsp;THB</b></td></tr>
						<tr><td>ยอดรวม (confirm แล้ว) : </td><td><b>".number_format(($order_row['order_price']+$order_row['transfer_price']),2)."&emsp;THB</b></td></tr>";

				}
				echo "
			</table>
		</div>
		<br><br>";

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
		<form action='cart.php?action=update&cart_id=' method='post' name='formCart' id='formCart'>
			<table class='content-grid'>";

	    $sum_amount = 0;
	    $sum_price_cn = 0;
	    $sum_transfer_price_cn = 0;
	    $sum_transfer_price_cn_th = 0;
	    $sum_price_thb_all = 0;
	    $sum_received_amount = 0;
	    $sum_return_money = 0;

		while ($shop_row = mysql_fetch_array($select_shop_group)) {
			$shop_name = $shop_row['shop_name'];

			echo "
				<tr>
					<th>ร้าน $shop_name</th>
					<th>จำนวน</th>
					<th>ราคา<br />หยวน (¥)</th>
					<th>ค่าขนส่งในจีน<br />หยวน (¥)</th>
					<th>ค่าขนส่งจีนไทย<br />(บาท)</th>
					<th>ทั้งหมด<br />(บาท)</th>
					<th>จำนวนที่ได้รับ</th>
					<th>คืนเงิน</th>
				</tr>
				";


			$select_item = mysql_query("select *
										from customer_order_product c, customer_order o, product p 
										where o.order_id = c.order_id 
										and c.product_id = p.product_id
										and o.customer_id = '$user_id'
										and o.order_id = '$order_id'
										and p.shop_name = '$shop_name'", $connection);

			if(mysql_num_rows($select_item) > 0){ 

			    
		        while($row = mysql_fetch_array($select_item)) {
		        	echo "
		        <tr>
		        	<td><div><a href='".$row['product_url']."' target='_blank'><img class='img-thumb' style='width:50px;' src='".$row['product_img']."'></a></div></td>
							<td>".$row['quantity']."</td>
							<td>".number_format($row['product_price']*$row['quantity'],2)."</td>
							<td>".number_format($row['order_shipping_cn_cost'],2)."</td>
							<td>".number_format($row['order_shipping_cn_th_cost'],2)."</td>
							<td>".number_format($row['order_product_totalprice'],2)."</td>
							<td>".number_format($row['received_amount'],2)."<br />".convertOrderProductStatus($row['current_status'])."<br />".$row['current_updatetime']."</td>
							<td>".number_format($row['return_money'],2)."<br />".convertRecievedStatus($row['current_status'])."</td>
		        </tr>
		        	";
		        	$sum_amount += $row['quantity'];
					    $sum_price_cn += $row['product_price']*$row['quantity'];
					    $sum_transfer_price_cn += $row['order_shipping_cn_cost'];
					    $sum_transfer_price_cn_th += $row['order_shipping_cn_th_cost'];
					    $sum_price_thb_all += $row['order_product_totalprice'];
					    $sum_received_amount += $row['received_amount'];
					    $sum_return_money += $row['return_money'];
		        }
		    }
		}
		echo "
				<tr class='sub'>
					<td><b>ทั้งหมด</b></td>
					<td><b>".number_format($sum_amount,2)."</b></td>
					<td><b>".number_format($sum_price_cn,2)."</b></td>
					<td><b>".number_format($sum_transfer_price_cn,2)."</b></td>
					<td><b>".number_format($sum_transfer_price_cn_th,2)."</b></td>
					<td><b>".number_format($sum_price_thb_all,2)."</b></td>
					<td id='price'><b>".number_format($sum_received_amount,2)."</b></td>
					<td><b>".number_format($sum_return_money,2)."</b></td>
				</tr>
			</table><br />";

			$select_order = mysql_query("select *
										from customer_order o
										where o.customer_id = '$user_id'
										and o.order_id = '$order_id'", $connection);
		$row = mysql_fetch_array($select_order);

		echo "
			<table class='content-grid' style='font-size:16px;color:orangered;'>
				<tr>
					<td>ค่าตีลังไม้</td>
					<td><b>".number_format($row['box_price'],2)." บาท</b></td>
				</tr>
				<tr>
					<td>ค่าขนส่งภายในประเทศ</td>
					<td><b>".number_format($row['th_transfer_price'],2)." บาท</b></td>
				</tr>
				<tr>
					<td>ค่าใช้จ่ายอื่นๆ</td>
					<td><b>".number_format($row['other_price'],2)." บาท</b></td>
				</tr>
			</table>
			<h3>ประวัติการชำระเงิน</h3>
			<table class='content-grid'>
				<tr>
					<th>วันที่</th>
					<th>จำนวน</th>
					<th>ชนิด</th>
					<th>สถานะ</th>
					<th>หมายเหตุ</th>
				</tr>
				<tr>
					<td> </td>
					<td> </td>
					<td> </td>
					<td> </td>
					<td> </td>
				</tr>
			</table>";

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
					<td>".number_format($shop_row['order_shipping_cn_weight'],2)."</td>
					<td>".number_format($shop_row['order_shipping_rate'],2)."</td>
					<td>".number_format($shop_row['order_shipping_cn_weight']*$shop_row['order_shipping_rate'],2)."</td>
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
					<td>".number_format($sum_weight,2)."</td>
					<td>รวม</td>
					<td id='shipping_cn-th'>".number_format($sum_shipping_price,2)."</td>
				</tr>
			</table >

			<h3>ค่าขนส่งภายในไทย</h3>
			<table class='content-grid'>
				<tr class='bg-primary'>
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
					<td>".number_format($order_row['order_shipping_th_cost'],2)."</td>
				</tr>
				<tr class='sub'>
					<td></td>
					<td></td>
					<td></td>
					<td>รวม</td>
					<td id='shipping_th'>".number_format($order_row['order_shipping_th_cost'],2)."</td>
				</tr>
			</table>-->
		</form>";
	}
	echo "
		
	</div>";

} 

?>

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
