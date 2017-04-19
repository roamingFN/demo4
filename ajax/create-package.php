<?php

include '../connect.php';
include '../session.php';
include '../inc/php/functions_statusConvert.php';

$array_order_id = json_decode($_GET['array_order_id']);
$array_shopname = json_decode($_GET['array_shopname']);
$shipping_id = $_GET['shipping_option'];
$address_id = $_GET['address_id'];
$total_ordernumber = count(array_unique($array_order_id));

if (count($array_order_id) != count($array_shopname)) {
	echo "เกิดข้อผิดพลาดในการจัดเก็บข้อมูล กรุณาลองใหม่อีกครั้งค่ะ";
	return;
}

if (count($array_order_id)>0 && count($array_shopname)>0 && count($array_order_id)==count($array_shopname)  && $address_id != "") {

	$str_order_id = "";
	for ($i=0; $i < count($array_order_id); $i++) { 
		if ($i != 0) {
			$str_order_id .= " or ";
		}
		$str_order_id .= " ( o.order_id = '".$array_order_id[$i]."' and p.shop_name = '".$array_shopname[$i]."' ) ";
	}

	// echo "select * 
	// 								from customer_order_product c
	// 								left join customer_order o on c.order_id = o.order_id
	// 								left join product p on c.product_id = p.product_id
	// 								left join customer_order_product_tracking t on c.order_product_id = t.order_product_id 
	// 								where o.customer_id = '$user_id' 
	// 								and c.current_status = '7' 
	// 								and ( ".$str_order_id." ) ";

	$select_order_detail = mysql_query("select *, c.order_product_id as order_product_id, o.order_id as order_id
									from customer_order_product c
									left join customer_order o on c.order_id = o.order_id
									left join product p on c.product_id = p.product_id
									left join customer_order_product_tracking t on c.order_product_id = t.order_product_id 
									where o.customer_id = '$user_id' 
									and c.current_status = '7' 
									and ( ".$str_order_id." ) ");

	if (mysql_num_rows($select_order_detail)>0) {

		$select_package_number = mysql_query("SELECT packageno 
																	FROM package 
																	WHERE createdate > STR_TO_DATE('".date('01/01/Y')." 00:00:00','%c/%e/%Y %T') 
																	and createdate < STR_TO_DATE('".date('12/31/Y')." 23:59:59','%c/%e/%Y %T') 
																	ORDER BY packageid DESC");

		//สร้าง package ใหม่
		$insert_package = mysql_query("insert into package(customerid,createdate,total_tracking,
			shippingid,shipping_address,statusid) 
			values('$user_id',NOW(),'".mysql_num_rows($select_order_detail)."','$shipping_id','$address_id','1')");
		$package_id = mysql_insert_id();

		//update topup number -- (2)
		// echo "num row = ".mysql_num_rows($select_package_number);
		if (mysql_num_rows($select_package_number) > 0) {
			//เอา order_number เก่ามา +1
			$select_package_number_row = mysql_fetch_array($select_package_number);
			$old_package_number = $select_package_number_row[0];
			// echo "old_package_number=".$old_package_number;
			$number = (int)substr($old_package_number, 3);
			$package_number = "P".date("y").str_pad($number+1 ,6, "0", STR_PAD_LEFT);
			// echo "new_package_number=".$package_number;
			$update_number = mysql_query("update package set packageno='$package_number' where packageid = '$package_id'");
		}else{
			//สร้าง package_number ใหม่
			$package_number = "P".date("y").str_pad(1 ,6, "0", STR_PAD_LEFT);
			// echo "create_new=".$package_number;
			$update_number = mysql_query("update package set packageno='$package_number' where packageid = '$package_id'");
		}

		//ยิงข้อมูลเข้า package detail
		$running = 1;
		$total_package_amount = 0;
		$total_want = 0;
		$total_quantity = 0;
		while ($row = mysql_fetch_array($select_order_detail)) {

			$insert_package_detail = mysql_query("insert into package_detail(packageid,packageorder,order_id,order_product_id,order_product_tracking_id) values('$package_id','$running','".$row['order_id']."','".$row['order_product_id']."','".$row['order_product_tracking_id']."')");

			//เปลี่ยนสถานะ order number
			$update_order_product = mysql_query("update customer_order_product set current_status = 8 where order_product_id = ".$row['order_product_id']);

			$total_package_amount += $row['total'];
			$total_want += $row['confirmed_quantity'];
			$total_quantity += $row['received_amount'];

			$running++;
		}

		$update_package_amount = mysql_query("update package 
					set amount = '$total_package_amount', 
					total = '$total_package_amount', 
					total_ordernumber = '".$total_ordernumber."', 
					total_want = '".$total_want."', 
					total_quantity = '".$total_quantity."', 
					total_miss = '".($total_want-$total_quantity)."' 
					where packageid = ".$package_id." ");

		if ($insert_package && $update_package_amount) {
			echo "รายการของคุณได้ถูกแจ้งปิดกล่องเรียบร้อยแล้ว<br />กรุณารอเจ้าหน้าที่สรุปค่าใช้จ่ายเพื่อจะได้ทำการจัดส่งต่อไปค่ะ";
		}else{
			echo mysql_error();
		}
		
	}else{
		echo "Seem you are invalid progress <br /> - maybe order product tracking data not found";
	}

	//create new package
	//select last topup number --(2)
	// $select_package_number = mysql_query("SELECT packageno 
	// 																	FROM package 
	// 																	WHERE createdate > STR_TO_DATE('".date('m/d/Y')." 00:00:00','%c/%e/%Y %T') 
	// 																	and createdate < STR_TO_DATE('".date('m/d/Y')." 23:59:59','%c/%e/%Y %T') 
	// 																	ORDER BY packageid DESC");
	
	// $insert_package = mysql_query("insert into package(customerid,createdate,total_tracking,
	// 	shippingid,shipping_address,statusid) 
	// 	values('$user_id',NOW(),'0','$shipping_id','$address_id','1')");
}

?>