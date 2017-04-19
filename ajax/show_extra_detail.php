<?php

include '../connect.php';
include '../session.php';
include '../inc/php/functions_statusConvert.php';

$order_product_id = $_GET['order_product_id'];

if (isset($order_product_id)&&$order_product_id >0) {
	$select_return = mysql_query("select * 
		from customer_order_product c, customer_order_paymore r, product p 
		where c.order_product_id = r.order_product_id 
		and c.product_id = p.product_id and r.paymore_status <> 2
		and c.order_product_id = '$order_product_id' 
		order by paymore_date");
	if (mysql_num_rows($select_return)>0) {
		$shopname = mysql_fetch_array($select_return);
		echo "<h3>เลขที่  " .$shopname['paymore_no']. "</h3><BR>";
		echo "<table class='table table-bordered'>
					<tr style='background-color:#3F51B5;color:white;'>
						<th>วันที่</th>
						<th>ร้าน ".$shopname['shop_name']."</th>
						<th>จำนวนที่สั่ง</th>
						<th>จำนวนที่สั่งได้</th>
						<th>ขาด</th>
						<th>ราคา/ชิ้น<BR>(หยวน)</th>
						<th>ราคา/ชิ้นที่ได้<BR>(หยวน)</th>
						<th>ค่ารถ</th>
						<th>ค่ารถ<BR>จริง</th>
						<th>ยอดรวม(หยวน)</th>
						<th>เรท</th>
						<th>ยอดเงินที่<BR>ต้องจ่ายเพิ่ม(บาท)</th
					</tr>
		";
		$running = 1;
		$total = 0;
		mysql_data_seek($select_return, 0);
		while ($row = mysql_fetch_array($select_return)) {
			echo "<tr>
							<td>".$row['paymore_date']."</td>
							<td><div><a href='".$row['product_url']."' target='_blank'><img class='img-thumb' style='width:50px;' src='".$row['product_img']."'></a></div></td>
							<td>".$row['first_unitquantity']."</td>
							<td>".$row['quantity']."</td>
							<td>".$row['loss_quantity']."</td>
							<td>".number_format($row['first_unitprice'],2)."</td>
							<td>".number_format($row['unitprice'],2)."</td>
							<td>".number_format($row['pay_transport'],2)."</td>
							<td>".number_format($row['transport'],2)."</td>
							<td>".number_format($row['total_yuan'],2)."</td>
							<td>".number_format($row['rate'],2)."</td>
							<td>".number_format($row['total_baht'],2)."</td>
						<tr>";
			$running++;
			$total += $row['total_baht'];
		}
		echo "<tr style='background-color:#dddddd;'>
						<td colspan='11'><b>ยอดรวม</b></td>
						<td  style='color:red'><b>".number_format($total,2)."</b></td>
					</tr>";
		echo "</table>";
	}else{
		echo "ไม่มีรายการที่ท่านค้นหาค่ะ";
	}
	
}


?>