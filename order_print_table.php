<!DOCTYPE html>
<html>
  	<head>
	    <?php
			include 'connect.php';
			include 'session.php';

			include 'page_script.php';
			include 'inc/php/functions_statusConvert.php';
		?>
	</head>
	<body style="margin: 0">
	<?php

	$order_id = $_GET['order_id'];

	//header
	$select_order = mysql_query("select * from customer_order o, customer_order_shipping s, customer c 
								where o.customer_id = '$user_id'
								and c.customer_id = o.customer_id
								and o.order_id = '$order_id'
								and o.order_id = s.order_id ", $connection);
	if (mysql_num_rows($select_order) > 0) {
	$order_row = mysql_fetch_array($select_order);
	$order_status_code = $order_row['order_status_code'];
	$customer_note = $order_row['customer_note'];
	$time = strtotime($order_row['date_order_created']);
	$newFormat = date('d-m-Y',$time);
	$now = date('d-m-Y');
	echo '<span style="text-align: center;"><h2>ใบรายการสั่งซื้อ</h2></span>';
	echo	"<div class='content-line'></div>
		<div>
			<table style='width:100%;'>
				<tr style='height: 1'>
					<td>
						<h4>เลขที่ออร์เดอร์ : ".$order_row['order_number']."</h4>
						<h4>รหัสลูกค้า : ".$order_row['customer_code']."</h4>
						<h4>อีเมลล์ : ".$order_row['customer_email']."</h4>
					</td>
					<td>
						<h4>บริการขนส่งในประเทศ : ".convertTransportName($order_row['order_shipping_th_option'])."</h4>
						<h4>Rate : ".number_format($order_row['order_rate'],2)." @ ".date("d/m/Y G:i:s", strtotime($order_row['order_rate_date']))."</h4>
						<h4>ค่าสินค้า : ".number_format($order_row['order_price'],2)."</h4>
					</td>
					<td>
						<h4>สั่งซื้อวันที่ : ".$newFormat."</h4>
						<h4>พิมพ์วันที่ : ".$now."</h4>
						<h4>&nbsp;</h4>
					</td>
				</tr>
			</table>
		</div>";
	}

	//start table
	$select_shop_group = mysql_query("	select shop_name
										from customer_order_product c, customer_order o, product p 
										where o.order_id = c.order_id 
										and c.product_id = p.product_id
										and o.customer_id = '$user_id'
										and o.order_id = '$order_id'
										group by p.shop_name", $connection);

	if (mysql_num_rows($select_shop_group) > 0) {
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
			echo "<table class='content-grid'>
				<tr>
					<th style='width: 2%;text-align: center;'>ลำดับ</th>
					<th style='width: 10%;text-align: center;'>ร้าน $shop_name</th>
					<th style='width: 10%;text-align: center;'><div>ไซด์</div><div>สี</div></th>
					<th style='width: 10%;text-align: center;'>จำนวนที่สั่ง</th>
					<th style='width: 10%;text-align: center;'>จำนวนที่สั่งได้</th>
					<th style='width: 10%;text-align: center;'>ราคา<br />หยวน (¥)</th>
					<th style='width: 10%;text-align: center;'>ค่าขนส่งในจีน<br />หยวน (¥)</th>
					<th style='width: 10%;text-align: center;'>ทั้งหมด<br />(บาท)</th>
					<th style='width: 10%;text-align: center;'>จำนวนที่ได้รับ</th>
					<th style='width: 10%;text-align: center;'>คืนเงิน</th>
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
		        	<td><div><img class='img-thumb' style='width:50px;' src='".$row['product_img']."'></div></td>
		        			<td style='text-align: center;'><div>".$row['product_size']."</div><div>".$row['product_color']."</div></td>
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

		if ($order_status_code == 0 || $order_status_code == 1) {
			echo "
			<center><a onclick='javascript:window.close();'><button type='button' name='cancelorder'><h3>ปิดหน้านี้&nbsp</h3></button></a></center><br />";
		}
	}
	?>
	<script type="text/javascript">
		window.print();
	</script>
	</body>