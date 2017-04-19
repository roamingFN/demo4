<?php
	$current_order_id = "";

	include 'connect.php';
	include 'session.php';
	include 'inc/php/functions_statusConvert.php';

	if($action = isset($_GET['action']) && $_GET['action'] != ''){ 
		if ($action == 'submit_order' && isset($_POST['address_name'])) {

			//ป้องการส่ง form ซ้ำ
			$select_cart = mysql_query("select * from shopping_cart c where c.customer_id='$user_id' and c.selected = '1'");
			if (mysql_num_rows($select_cart) == 0) {
				$location = "Location: order_list.php";
				header($location);
				return;
			}

			$address_name = $_POST['address_name'];
			$address_line1 = $_POST['address_line1'];
			$address_country = "ไทย";
			$address_zipcode = $_POST['address_zipcode'];
			$address_phone = $_POST['address_tel'];
			$address_id = '';
			$order_address_id = '';
			$order_amount = $_POST['order_amount'];
			$other_order_detail = $_POST['other_order_detail'];
			$order_transport_option = '';

			//new address function handle
			$province_id = $_POST['province'];
			$amphur_id   = $_POST['amphoe'];
			$district_id = $_POST['district'];

			$select_province = mysql_query("select * from tbl_province where province_id = '$province_id'");
			$province_row = mysql_fetch_array($select_province);
			$province_name = $province_row['PROVINCE_NAME'];

			$select_amphur = mysql_query("select * from tbl_amphur where amphur_id = '$amphur_id'");
			$amphur_row = mysql_fetch_array($select_amphur);
			$amphur_name = $amphur_row['AMPHUR_NAME'];

			$select_district = mysql_query("select * from tbl_district where district_id = '$district_id'");
			$district_row = mysql_fetch_array($select_district);
			$district_name = $district_row['DISTRICT_NAME'];

			//add detail to address line 1 -> (complatiably)
			$address_line1 .= " ".$district_name." ".$amphur_name;

			if (isset($_POST['exist_address_id'])) {
				$exist_address_id = $_POST['exist_address_id'];
			}

			
			//รับของด้วยตนเอง	
			if ($_POST['opt_send'] == 'by_self') {
				$order_transport_option = 'มารับสินค้าด้วยตัวเอง';
			} //รับของกับบริษัทขนส่ง
			else if ($_POST['opt_send'] == 'by_company') {
				if ($_POST['opt_company'] == 'other') {
					$order_transport_option = mysql_real_escape_string(stripcslashes($_POST['opt_company_other']));
				}else{
					$order_transport_option = mysql_real_escape_string(stripcslashes($_POST['opt_company']));
				}
				
				//เลือกที่อยู่เดิม
				if ($_POST['opt_address'] == 'exist_address') {
					$order_address_id = $exist_address_id;
				} //สร้างที่อยู่ใหม่
				else if ($_POST['opt_address'] == 'new_address') {
					$add_address = mysql_query("insert into customer_address(customer_id,address_name,line_1,city,country,zipcode,phone,district_id,amphur_id,province_id) 
						values('$user_id','$address_name','$address_line1','$province_name','$address_country','$address_zipcode','$address_phone','".$_POST['district']."','".$_POST['amphoe']."','".$_POST['province']."')");
					if ($add_address) {
						$order_address_id = mysql_insert_id();
					}else {
						//echo mysql_error();
					}
				}
			}

			//update quantity ก่อน
			$cartId     = $_POST['hidCartId'];
			$productId  = $_POST['hidProductId'];
			$itemQty    = $_POST['txtQty'];
			$numItem    = count($itemQty);
			$notice     = '';
			$customer_id = $_SESSION['CX_login_id'];
			
			for ($i = 0; $i < $numItem; $i++) {
				$newQty = (int)$itemQty[$i];
				// update product quantity
				$update_cart = mysql_query("update shopping_cart
						set cart_quantity = '$newQty'
						where cart_id = '$cartId[$i]'", $connection);
			}

			//echo "order transport = ".$order_transport_option;

			//ดึงข้อมูล rate & shipping rate
			$select_rate = mysql_query("select * from website_rate order by starting_date desc limit 1");
			$select_rate_row = mysql_fetch_array($select_rate);
			$rate_date = $select_rate_row['starting_date'];
			$rate = $select_rate_row['rate_cny'];
			$shipping_rate = $select_rate_row['shipping_rate_cny'];

			//select last order number --(2)
			$select_order_number = mysql_query("SELECT order_number 
																					FROM customer_order 
																					WHERE date_order_created > STR_TO_DATE('".date('m/d/Y')." 00:00:00','%c/%e/%Y %T') 
																					and date_order_created < STR_TO_DATE('".date('m/d/Y')." 23:59:59','%c/%e/%Y %T') 
																					ORDER BY order_id DESC");

			//สร้าง order ใหม่ -- (1)
			$add_order =  mysql_query("insert into customer_order(customer_id, order_status_code, 
				product_quantity, order_price, order_rate, order_payment_flag, 
				order_shipping_payment_flag, order_address_id, date_order_created, order_rate_date, customer_note)
				values('$user_id', '0', '$order_amount', '0', '$rate', '0', '0', 
				'$order_address_id', NOW(), NOW(), '$other_order_detail')");
			$order_id = mysql_insert_id();
			$current_order_id = $order_id;
			$add_order_shipping =  mysql_query("insert into customer_order_shipping (order_id, order_shipping_th_option, 
				order_shipping_th_ref_no, order_shipping_th_weight, order_shipping_th_cost, order_shipping_cn_cost)
				values ('$order_id', '$order_transport_option', '', '0', '0', '0')");

			//update order number -- (2)
			// echo "num row = ".mysql_num_rows($select_order_number);
			if (mysql_num_rows($select_order_number) > 0) {
				//เอา order_number เก่ามา +1
				$select_order_number_row = mysql_fetch_array($select_order_number);
				$old_order_number = $select_order_number_row[0];
				// echo "old_order_number=".$old_order_number;
				$number = (int)substr($old_order_number, 7);
				$order_number = "R".date("ymd").str_pad($number+1 ,5, "0", STR_PAD_LEFT);
				// echo "new_order_number=".$order_number;
				$update_number = mysql_query("update customer_order set order_number='$order_number' where order_id = '$order_id'");
			}else{
				//สร้าง order_number ใหม่
				$order_number = "R".date("ymd").str_pad(1 ,5, "0", STR_PAD_LEFT);
				// echo "create_new=".$order_number;
				$update_number = mysql_query("update customer_order set order_number='$order_number' where order_id = '$order_id'");
			}

			if($add_order && $add_order_shipping){ 

				//ย้ายสินค้าในตะกร้าไปใว้ใน customer_order_product
				$cart_item = mysql_query("select * from shopping_cart c, product p where c.customer_id='$user_id' and c.selected = '1' and c.product_id = p.product_id");
				while($row = mysql_fetch_array($cart_item)) {
					$cart_id = $row['cart_id'];
					$product_id = $row['product_id'];
					$product_price = $row['product_price'];
					$cart_quantity = $row['cart_quantity'];
					$add_customers_order_product =  mysql_query("insert into customer_order_product(order_id,product_id,quantity,first_unitquantity,unitprice,first_unitprice,unconfirmed_product_order) values($order_id,$product_id,'0',$cart_quantity,$product_price,$product_price,$cart_quantity)");
					//ย้ายเสร็จแล้วให้ลบสินค้าในตะกร้าทิ้ง
					if ($add_customers_order_product) {
						//echo "add product:".$product_id." into order:".$order_id." /";
						
						$delete_cart = mysql_query("delete from shopping_cart where cart_id = '$cart_id' and selected = '1'");
						if ($delete_cart) {
							//echo "[delete".$cart_id."] ";
						}
					}else{
						//echo mysql_error();
					}
				}

			}else{
				//echo mysql_error();
			}


			//########################
			//########################
			//update จำนวนร้านค้า จำนวนLink จำนวนสินค้า ยอดค่าสินค้า สถานะการสั่งซื้อ

			$total_shop = 0;
			$total_link = 0;
			$order_price_yuan = 0;
			$process_status = 0;

			$select_shop_group = mysql_query("	select shop_name
										from customer_order_product c, customer_order o, product p 
										where o.order_id = c.order_id 
										and c.product_id = p.product_id
										and o.customer_id = '$user_id'
										and o.order_id = '$current_order_id'
										group by p.shop_name", $connection);
			
			$total_shop = mysql_num_rows($select_shop_group);
	
			if (mysql_num_rows($select_shop_group) > 0) {
			    $sum_amount = 0;
			    $sum_price_cn = 0;
			    $sum_price_th = 0;
				while ($shop_row = mysql_fetch_array($select_shop_group)) {

					$shop_name = $shop_row['shop_name'];
					$select_item = mysql_query("select *
												from customer_order_product c, customer_order o, product p 
												where o.order_id = c.order_id 
												and c.product_id = p.product_id
												and o.customer_id = '$user_id'
												and o.order_id = '$current_order_id'
												and p.shop_name = '$shop_name'", $connection);

					if(mysql_num_rows($select_item) > 0){ 

					    
				        while($row = mysql_fetch_array($select_item)) {

				        	$sum_amount += $row['first_unitquantity'];
				        	$sum_price_cn += $row['product_price']*$row['first_unitquantity'];
				        	$sum_price_th += $row['product_price']*$row['first_unitquantity']*$row['order_rate'];

				        }
				    }
				}
			}

			$total_link = mysql_num_rows($cart_item);
			$order_price_yuan = $sum_price_cn;
			$process_status = 0;

			$update_order = mysql_query("update customer_order 
				set total_shop='$total_shop', total_link='$total_link', order_price='$sum_price_th', 
				order_price_yuan='$order_price_yuan', process_status='$process_status' 
				where order_id = '$current_order_id'");

			//end_update
			//########################
			//########################

			//insert new customer_request_payment
			// $insert_request_payment = mysql_query("insert into customer_request_payment(order_id,customer_id,
			// 	payment_request_type,payment_request_status,payment_request_amount,payment_for_order_status,
			// 	date_payment_created,date_payment_last_update) 
			// 	values('$order_id','$user_id',0,0,'$sum_price_th','0',NOW(),NOW())");
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
		<h2>สั่งซื้อสินค้าเรียบร้อย, หมายเลขการสั่งซื้อ : <?php echo $order_number; ?></h2>

	<?php
	$select_rate = mysql_query("select * from website_rate order by starting_date desc limit 1");
	$select_rate_row = mysql_fetch_array($select_rate);
	$rate_date = $select_rate_row['starting_date'];
	$rate = $select_rate_row['rate_cny'];
	$shipping_rate = $select_rate_row['shipping_rate_cny'];

	$select_shop_group = mysql_query("select p.shop_name, p.source
										from product p, customer_order_product cp, customer_order o 
										where o.customer_id = '$user_id' 
										and o.order_id = '$current_order_id'
										and o.order_id = cp.order_id
										and cp.product_id = p.product_id
										group by p.shop_name");

	if (mysql_num_rows($select_shop_group) > 0) {
		//echo "<table class='content-grid'><tr class='bg-primary'><th>2mepqjwprjkqp2ork<th></tr><tr><td>32lj4324kj3;lke;l<td></tr></table>";
		echo "<table class='content-grid' style='font-size:14px;'>";
	        echo "<tr class='bg-primary'>";
	            echo "<th class='text-center'>ลำดับ</th>";
	            echo "<th colspan='2'>สินค้า</th>";
	            echo "<th style='text-align:right; padding-right:1em;'>ราคา (หยวน)</th>";
	            echo "<th style='text-align:right; padding-right:1em;'>จำนวน</th>";
	            echo "<th style='text-align:right; padding-right:2em;'>ราคาทั้งหมด (หยวน)</th>";
	        echo "</tr>";

	    $sum_price = 0;
	    $sum_product = 0;
	    $sum_product_amount = 0;
	    $product_row = 1;
		while ($shop_row = mysql_fetch_array($select_shop_group)) {
			$shop_name = $shop_row['shop_name'];
			
			echo "	<tr>
						<td colspan='6' class='bg-info'>
							<b> ชื่อร้าน: ".$shop_name.", เว็บไซต์: ".$shop_row['source']." </b>
						</td>
					</tr>";

			$select_item = mysql_query("select * from customer_order_product cp, product p, customer_order o 
				where o.customer_id = '$user_id' and o.order_id='$current_order_id' and o.order_id = cp.order_id
				and cp.product_id=p.product_id and p.shop_name = '$shop_name'", $connection);

			if(mysql_num_rows($select_item) > 0){ 
				$sum_product += mysql_num_rows($select_item);

		        while($row = mysql_fetch_array($select_item)) {
		 			$sum_price += $row['product_price']*$row['first_unitquantity'];
		 			$sum_product_amount += $row['first_unitquantity'];
		            //creating new table row per record
		            echo "<tr>";
		            	echo "<td class='text-center'>".$product_row."</td>";
		                echo "<td>";
		                	echo "<img style='width:50px;' class='img-thumbnail' src='".$row['product_img']."''>";
		                echo "</td>";
		                echo "<td>";
		                	echo "<a href='".$row['product_url']."' target='_blank'>".$row['product_name']."</a><br />";
		                	echo "ขนาด:".$row['product_size']."<br />";
		                	echo "สี:".$row['product_color']."";
		                echo "</td>";
		                echo "<td style='text-align:right; padding-right:1em;'>";
		                	echo number_format($row['product_price'],2)." ¥";
		                echo "</td>";
		                echo "<td style='text-align:right; padding-right:1em;'>";
		                	echo $row['first_unitquantity'];
		                echo "</td>";
		                echo "<td class='text-right' style='padding-right:2em;'>";
		                	echo number_format($row['product_price']*$row['first_unitquantity'],2)." ¥";
		                echo "</td>";
		            echo "</tr>";
		            $product_row++;
		        }
			}
		}
	            $query_customer = mysql_query("select * from customer where customer_id = '$user_id'");
							$query_customer_row = mysql_fetch_array($query_customer);
							$customer_current_amount = $query_customer_row['current_amount'];

	            echo "
	            	  <tr>
	            	    <td class='bg-info'></td>
	            	    <td class='bg-info'><b>ยอดรวม</b></td>
	            	    <td class='bg-info'><b>".$sum_product." &nbsp&nbsp&nbspรายการ</b></td>
	            	    <td class='bg-info'></td>
	            	    <td class='bg-info' style='text-align:right; padding-right:1em;'><b>".$sum_product_amount."</b></td>
	            	    <td class='bg-info'></td>
 					  </tr>
	            	  <tr>
 					  </tr>
	            	  <tr>
	            		<td colspan='5' class='text-right'><b>ราคาสินค้ารวม (หยวน)</b></td>
	            		<td class='text-right' style='padding-right:2em;'><b id='total_price'>". number_format($sum_price, 2)."</b></td>
	            	  </tr>	
	            	  <tr>
	            		<td colspan='5' class='text-right'><b>Rate @ ".date("Y-m-d G:i")." (บาท)</b></td>
	            		<td class='text-right' style='padding-right:2em;'><b id='rate_cny'> ". number_format($rate, 2)."</b></td>
	            	  </tr>	
	            	  <tr>
	            		<td colspan='5' class='text-right'><b>ราคาสินค้ารวม (บาท)</b></td>
	            		<td class='text-right' style='padding-right:2em;'><b id='total_price_bath'>". number_format($sum_price*$rate,2)."</b></td>
	            	  </tr>";
	 				
	    echo 	"</table>";

	    $select_address = mysql_query("select * from customer_order c, customer_address a where c.order_id = '$current_order_id' and c.order_address_id = a.address_id");
	    $select_shopping_th_option = mysql_query("select * from customer_order_shipping s where s.order_id = '$current_order_id'");

			$row_address = mysql_fetch_array($select_address);
			$row_shopping_th_option = mysql_fetch_array($select_shopping_th_option);

			echo "<h4>วิธีการจัดส่งสินค้าในไทย : ".convertTransportName($row_shopping_th_option['order_shipping_th_option'])."</h4>";

			if (mysql_num_rows($select_address) > 0) {
				echo "<h4>ที่อยู่สำหรับจัดส่งสินค้า</h4>";
				echo "<div class='well'>";
				echo "<strong>ชื่อ ".$row_address['address_name']."</strong><br />";
				echo $row_address['line_1']."<br />".$row_address['city'].", ".$row_address['country']."<br />".$row_address['zipcode']."<br />Tel. ".$row_address['phone']; 
				echo "</div>";
			}

			echo "<h4>หมายเหตุ : ";
			if ($other_order_detail != "") {
				echo $other_order_detail;
			}
			echo "</h4>";
	}
?>
		<hr>
		<p style="color:red;"><i class='material-icons'>info</i> ขณะนี้ออเดอร์อยู่ระหว่างการตรวจสอบและอัพเดทราคาโดยพนักงาน</p><br />
		<p style="color:red;"><i class='material-icons'>info</i> ท่านสามารถกดดูสถานะออร์เดอร์ได้จากเมนู "รายการสั่งซื้อ" ด้านมุมซ้ายบน</p>
		<br /><br />
		<div class="col-md-6 text-left">
			<a href="order_list"><button>ไปยังหน้ารายการสั่งซื้อ</button></a>
		</div>
		<div class="col-md-6 text-right">
			<a href='#' onclick='gotoTop()'>กลับไปด้านบน</a>
		</div>
		<br /><br />
	</div>
</div>

<script type="text/javascript">

	function gotoTop(){
		$('html, body').animate({ scrollTop: 0 }, 'slow');
	}
	
	 $(document).ready(function(){
    
		swal({
			title: "",
			text: "<span style='color:black'>ขณะนี้ออร์เดอร์อยู่ระหว่างการตรวจสอบและอัพเดทราคาโดยพนักงาน<BR>ท่านสามารถกดดูสถานะออร์เดอร์ได้จากเมนู 'รายการสั่งซื้อ'<BR>ด้านซ้ายมือ</span>",
			type: "info",
			confirmButtonText: "ตกลง",
			html:true
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