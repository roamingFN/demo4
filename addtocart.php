<?php
	include 'connect.php';
	
	session_start();
	if (isset($_SESSION['CX_login_user'])) {
		// Storing Session
		$email_check = $_SESSION['CX_login_user'];
		// SQL Query To Fetch Complete Information Of User
		$ses_sql = mysql_query("select customer_email, customer_id from customer where customer_email='$email_check'", $connection);
		$row = mysql_fetch_assoc($ses_sql);
		$login_session =$row['customer_email'];
		$user_id =$row['customer_id'];
		
		if(!isset($login_session)){
			echo('กรุณาเข้าสู่ระบบก่อนค่ะ'); // Redirecting To Home Page
		}
		
	}else{ 

		//set item detail in cookie
		$product_url = $_POST['product_url'];
		$product_img = $_POST['product_img'];
		$product_name = $_POST['product_name'];
		$product_price = $_POST['product_price'];
		$product_size = $_POST["product_size"];
		$product_color = $_POST["product_color"];
		$product_quentity = $_POST["product_quentity"];
		$shop_name = $_POST["shop_name"];
		$source = $_POST["source"];

		setcookie("product_url", $product_url, time() + (86400 * 30), "/");
		setcookie("product_img", $product_img, time() + (86400 * 30), "/");
		setcookie("product_name", $product_name, time() + (86400 * 30), "/");
		setcookie("product_price", $product_price, time() + (86400 * 30), "/");
		setcookie("product_size", $product_size, time() + (86400 * 30), "/");
		setcookie("product_color", $product_color, time() + (86400 * 30), "/");
		setcookie("product_quentity", $product_quentity, time() + (86400 * 30), "/");
		setcookie("shop_name", $shop_name, time() + (86400 * 30), "/");
		setcookie("source", $source, time() + (86400 * 30), "/");

		// if(!isset($_COOKIE['product_url'])) {
		//   echo "Cookie named '" . 'product_url' . "' is not set!";
		// } else {
		//   echo "Cookie '" . 'product_url' . "' is set!<br>";
		//   echo "Value is: " . $_COOKIE['product_url'];
		// }

		//echo "cookie seted";
	?>
    <p>กรุณา
			<a href="#" onClick="$('#addtocart').modal('hide'); openLogin()"> เข้าสู่ระบบ </a> หรือ
			<a href="#" onClick="$('#addtocart').modal('hide'); openRegister()">สมัครสมาชิก</a> ก่อนค่ะ
    </p>
	<?php
	}
	
	if(!isset($login_session)){
		//echo('PLEASE_LOGIN');
	}else{ 
		$product_url = $_POST['product_url'];
		$product_img = $_POST['product_img'];
		$product_name = $_POST['product_name'];
		$product_price = $_POST['product_price'];
		$product_size = $_POST["product_size"]; //1
		$product_color = $_POST["product_color"]; //2
		$product_quentity = $_POST["product_quentity"]; //3
		$shop_name = $_POST["shop_name"];
		$source = $_POST["source"];
		
		//start for loop

		
		
		//ตรวจว่าสินค้ามีหรือยัง
		$dupOrder=array();
		$dupAdd=array();
		$msgOrderRes='';
		$msgAddRes='';
		for($i=0;$i<count($product_size);++$i){
			$sql="select * from product p, shopping_cart s
					where p.product_url='$product_url'
					and p.product_size='$product_size[$i]'
					and p.product_color='$product_color[$i]'
					and p.product_id = s.product_id
					and s.customer_id = '$user_id'";
			$item_exist = mysql_query($sql, $connection);
			$item_exist_row = mysql_fetch_array($item_exist);
			$product_id = $item_exist_row['product_id'];
			
			if (mysql_num_rows($item_exist)==0) {
				//เพิ่มสินค้าใหม่
				$add_product = mysql_query("insert into product(product_url,product_img,product_name,product_color,product_size,product_price,shop_name,source)
						values('$product_url','$product_img[$i]','$product_name','$product_color[$i]','$product_size[$i]','$product_price','$shop_name','$source')");
				//เพิ่มสินค้าในต้กร้า
				$product_id = mysql_insert_id();
				$add_cart_item = mysql_query("insert into shopping_cart(product_id,customer_id,cart_quantity,cart_date)
						values('$product_id','$user_id','$product_quentity[$i]',now())");
				//echo "เพิ่มสินค้าลงในตะกร้าเรียบร้อย <br />";
				$dupAdd[$i]=1;
				$msgAddRes="เพิ่มสินค้าลงในตะกร้าเรียบร้อย <br />";
			
			}else if (mysql_num_rows($item_exist)==1) {
				//สินค้ามีอยู่แล้ว อัพเดทข้อมูลสินค้าในตะกร้า
				$update_product_id = $item_exist_row['product_id'];
				$product_in_cart = mysql_query("select cart_quantity from shopping_cart where product_id='$update_product_id'");
				$product_in_cart_row = mysql_fetch_array($product_in_cart);
				$current_product_qty = $product_in_cart_row['cart_quantity'];
				$update_product_qty = $current_product_qty + $product_quentity[$i];
			
				if (mysql_num_rows($product_in_cart) > 0) {
					$update_cart_item = mysql_query("update shopping_cart set cart_quantity = $update_product_qty, cart_date = now()
							where product_id = '$update_product_id' and customer_id = '$user_id' ");
					//echo "สินค้ามีอยู่แล้วในตะกร้า ระบบได้เพิ่มจำนวนสินค้าจากที่มีอยู่<br />";
					$dupOrder[$i]=1;
					$msgOrderRes="สินค้ามีอยู่แล้วในตะกร้า ระบบได้เพิ่มจำนวนสินค้าจากที่มีอยู่<br />";
				}else{
					$add_cart_item = mysql_query("insert into shopping_cart(product_id,customer_id,cart_quantity,cart_date)
							values('$product_id','$user_id','$product_quentity[$i]',now())");
					//echo "เพิ่มสินค้าลงในตะกร้าเรียบร้อย <br />";
					$dupAdd[$i]=1;
					$msgAddRes="เพิ่มสินค้าลงในตะกร้าเรียบร้อย <br />";
				}
			}
			
		} //end for
		
		if(count($dupOrder)>0){
			echo $msgOrderRes;
		}
		
		if(count($dupAdd)>0){
			echo $msgAddRes;
		}
		

		/* start comment
		
		end comment*/
		
		//end for loop

		//อัพเดทข้อมูลตะกร้าสินค้าที่โชว์ในหน้าเว็บ

		$cart_item = mysql_query("select count(product_id) from `shopping_cart` where customer_id = '$user_id'", $connection);
		$cart_item_row = mysql_fetch_array($cart_item);
		setcookie('CX_cart_items', $cart_item_row['count(product_id)'], time() + (86400 * 30), "/");


// 		if(array_key_exists($id, $_SESSION['CX_cart_items'])){
// 		    echo "สินค้ามีอยู่แล้วในตะกร้า ระบบจะเพิ่มจำนวนสินค้าจะที่มีอยู่<br />";
// 		    $_SESSION['CX_cart_items'][$product_url]['product_quentity'] += $product_quentity;
// 		}
// 		else{
// 			echo "เพิ่มสินค้าลงในตะกร้าเรียบร้อย <br />";
// 		    $_SESSION['CX_cart_items'][$id] = array('product_url' => $product_url, 'product_img' => $product_img,'product_name' => $product_name, 
// 		    	'product_price' => $product_price, 'product_size' => $product_size, 'product_color' => $product_color,
// 		    	'product_quentity' => $product_quentity);;
// 		}

// 		echo 	"<img style='width:100px;' class='img-thumbnail' src='".$product_img."'>".
// 				"<br />ชื่อสินค้า : ".$product_name." ".
// 				"<br />ราคา : ".$product_price." ".
// 				"<br />ขนาด : ".$product_size." ".
// 				"<br />สี : ".$product_color." ".
// 				"<br />จำนวน : ".$product_quentity." ";

// 		$selectDraft = mysql_query("select * from customer_order where customer_id='$user_id' and order_status_code='0'", $connection);
// 		if (mysql_num_rows($selectDraft) > 0) {
// 			echo "has draft";
// 		}else{
// 			echo "create new";
// 			$createDraft = mysql_query("insert into customer_order(customer_id,customer_payment_method_id,order_status_code,date_order_placed) values('$user_id','1','0',now())", $connection);
// 			$selectOrderId = mysql_insert_id();
// 			if ($createDraft) {
// 				echo "add product";
// 				$addProduct = mysql_query("insert into product(product_url,product_img,product_name,product_color,product_size,product_price) values('$product_url','$product_img','$product_name','$product_color','$product_size','$product_price')", $connection);
// 				echo "add order product";
// 				$selectProdectId = mysql_insert_id();
// 				$addOrderProduct = mysql_query("insert into customer_order_product(order_id,product_id,quentity) values('$selectOrderId','$selectProdectId','$product_quentity')", $connection) ;
// 			}
// 		}
	}
	// $sql = "select * from customer";
	// $result = mysql_query($sql);
	// while($row = mysql_fetch_array($result))
	// {
	// 	echo "ID: ".$row[0]."<br>";
	// 	echo "Name: ".$row[1]."<br>";
	// 	echo "Last: ".$row[2]."<br>";
	// 	echo "Tel: ".$row[3]."<br>";
	// 	echo "Email: ".$row[4]."<br>";
	// 	echo "==================================<br>";
	// }

?>