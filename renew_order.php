<?php
	include 'connect.php';
	include 'session.php';

	if(isset($_GET['order_id']) && $_GET['order_id'] != '') {

		$order_id = $_GET['order_id'];

		$select_order = mysql_query("select * from customer_order o, customer_order_shipping s 
								where o.customer_id = '$user_id' 
								and o.order_id = '$order_id'
								and o.order_id = s.order_id ", $connection);
		if (mysql_num_rows($select_order) > 0) {
			$order_row = mysql_fetch_array($select_order);

			$select_shop_group = mysql_query("	select shop_name, source
										from customer_order_product c, customer_order o, product p 
										where o.order_id = c.order_id 
										and c.product_id = p.product_id
										and o.customer_id = '$user_id'
										and o.order_id = '$order_id'
										group by p.shop_name", $connection);

			if (mysql_num_rows($select_shop_group) > 0) {
				while ($shop_row = mysql_fetch_array($select_shop_group)) {
					$shop_name = $shop_row['shop_name'];
					$select_item = mysql_query("select *
										from customer_order_product c, customer_order o, product p 
										where o.order_id = c.order_id 
										and c.product_id = p.product_id
										and o.customer_id = '$user_id'
										and o.order_id = '$order_id'
										and p.shop_name = '$shop_name'", $connection);

					if(mysql_num_rows($select_item) > 0){ 
						while($row = mysql_fetch_array($select_item)) {

							//add to cart na jaaaa.
							$product_url = $row['product_url'];
							$product_img = $row['product_img'];
							$product_name = $row['product_name'];
							$product_price = $row['product_price'];
							$product_size = $row['product_size'];
							$product_color = $row['product_color'];
							$product_quentity = $row['first_unitquantity'];
							$shop_name = $shop_row['shop_name'];
							$source = $shop_row['source'];

							//ตรวจว่าสินค้ามีหรือยัง
							$item_exist = mysql_query("select * from product p, shopping_cart s 
														where p.product_url='$product_url' 
														and p.product_size='$product_size' 
														and p.product_color='$product_color'
														and p.product_id = s.product_id
														and s.customer_id = '$user_id'", $connection);
							$item_exist_row = mysql_fetch_array($item_exist);
							$product_id = $item_exist_row['product_id'];

							if (mysql_num_rows($item_exist)==0) {
								//เพิ่มสินค้าใหม่
								$add_product = mysql_query("insert into product(product_url,product_img,product_name,product_color,product_size,product_price,shop_name,source)
															values('$product_url','$product_img','$product_name','$product_color','$product_size','$product_price','$shop_name','$source')");
								//เพิ่มสินค้าในต้กร้า
								$product_id = mysql_insert_id();;
								$add_cart_item = mysql_query("insert into shopping_cart(product_id,customer_id,cart_quantity,cart_date)
																values('$product_id','$user_id','$product_quentity',now())");
								// echo "เพิ่มสินค้าลงในตะกร้าเรียบร้อย <br />";
								//header('Location: cart.php'); 

							}else if (mysql_num_rows($item_exist)==1) {
								//สินค้ามีอยู่แล้ว อัพเดทข้อมูลสินค้าในตะกร้า
								$update_product_id = $item_exist_row['product_id'];
								$product_in_cart = mysql_query("select cart_quantity from shopping_cart where product_id='$update_product_id'");
								$product_in_cart_row = mysql_fetch_array($product_in_cart);
								$current_product_qty = $product_in_cart_row['cart_quantity'];
								$update_product_qty = $current_product_qty + $product_quentity;

								if (mysql_num_rows($product_in_cart) > 0) {
									$update_cart_item = mysql_query("update shopping_cart set cart_quantity = $update_product_qty, cart_date = now() 
																	where product_id = '$update_product_id' and customer_id = '$user_id' ");
									// echo "สินค้ามีอยู่แล้วในตะกร้า ระบบได้เพิ่มจำนวนสินค้าจากที่มีอยู่<br />";
									// header('Location: cart.php'); 
								}else{
									$add_cart_item = mysql_query("insert into shopping_cart(product_id,customer_id,cart_quantity,cart_date)
																	values('$product_id','$user_id','$product_quentity',now())");
									// echo "เพิ่มสินค้าลงในตะกร้าเรียบร้อย <br />";
									// header('Location: cart.php'); 
								}
							}
						}
						header('Location: cart.php'); 
					}
				}
			}
		}
	}

?>