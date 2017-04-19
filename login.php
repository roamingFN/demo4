<?php
include 'connect.php';
include 'inc/php/functions.php';

session_start ();
if (isset ( $_SESSION ['CX_login_user'] )) {
	// Storing Session
	
	// echo "<pre>";
	// print_r($_SESSION);
	// echo "</pre>";
	
	$email_check = $_SESSION ['CX_login_user'];
	// SQL Query To Fetch Complete Information Of User
	$session_id = session_id ();
	// echo $session_id;
	$ses_sql = mysql_query ( "select customer_email, customer_id from customer 
			where customer_email='$email_check' and active_session = '$session_id'", $connection );
	$row = mysql_fetch_assoc ( $ses_sql );
	$login_session = $row ['customer_email'];
	$user_id = $row ['customer_id'];
	
	if (! isset ( $login_session )) {
		session_unset ();
		
		header ( 'Location: index.php?error=sessions_expire' ); // Redirecting To Home Page
	} else {
		$protocol = stripos ( $_SERVER ['SERVER_PROTOCOL'], 'https' ) === true ? 'https://' : 'http://';
		$url =   $_GET ['returnUrl'];
		//header ( 'Location: ' . $url );
		echo "<script>window.location.href = '".$url."';</script>";
		exit ();
		// http://www.order2easy.com/demo2/order_show_detail_confirmed.php?order_id=477
	}
} else {
	// header('Location: login.php'); // Redirecting To Home Page
	viewLogin ();
}
$error='';
if (isset ( $_POST ['submit'] )) {
	if (empty ( $_POST ['email'] ) || empty ( $_POST ['password'] )) {
		$loginfail = true;
		$error = "Username or Password is invalid";
	} else {
		// Define $email and $password
		$email = $_POST ['email'];
		$password = $_POST ['password'];
		
		// To protect MySQL injection for Security purpose
		$email = stripslashes ( $email );
		$password = stripslashes ( $password );
		$email = mysql_real_escape_string ( $email );
		$password = mysql_real_escape_string ( $password );
		
		// encode password
		$password = sha1 ( $password );
		
		// SQL query to fetch information of registerd users and finds user match.
		$login = mysql_query ( "select * from customer where passwd='$password' AND customer_email='$email'", $connection );
		
		if (mysql_num_rows ( $login ) > 0) {
			
			// echo "login success";
			
			$customer_rows = mysql_fetch_array ( $login );
			if ($customer_rows ['active'] == 1) {
				
				$_SESSION ['CX_login_user'] = $email;
				$_SESSION ['CX_login_name'] = $customer_rows ['customer_firstname'] . " " . $customer_rows ['customer_lastname'];
				$_SESSION ['CX_login_id'] = $customer_rows ['customer_id'];
				$_SESSION ['CX_login_code'] = $customer_rows ['customer_code'];
				$login_id = $customer_rows ['customer_id'];
				
				// set customer session id
				$session_id = session_id ();
				$update_customer_sessions = mysql_query ( "update customer set active_session = '$session_id'
							where customer_id = '$login_id'" );
				
				if (isset ( $_POST ['login-remember'] )) {
					$year = time () + 31536000;
					setcookie ( 'remember_me', $_POST ['email'], $year );
				} else if (! isset ( $_POST ['login-remember'] )) {
					if (isset ( $_COOKIE ['remember_me'] )) {
						$past = time () - 100;
						setcookie ( remember_me, gone, $past );
					}
				}
				
				if (isset ( $_COOKIE ['product_url'] )) {
					
					// echo "found cookie";
					
					$product_url = $_COOKIE ['product_url'];
					$product_img = $_COOKIE ['product_img'];
					$product_name = $_COOKIE ['product_name'];
					$product_price = $_COOKIE ['product_price'];
					$product_size = $_COOKIE ["product_size"];
					$product_color = $_COOKIE ["product_color"];
					$product_quentity = $_COOKIE ["product_quentity"];
					$shop_name = $_COOKIE ["shop_name"];
					$source = $_COOKIE ["source"];
					
					// echo " product_url:".$product_url;
					// echo " product_img:".$product_img;
					// echo " product_name:".$product_name;
					// echo " product_price:".$product_price;
					// echo " product_size:".$product_size;
					// echo " product_color:".$product_color;
					// echo " product_quentity:".$product_quentity;
					// echo " shop_name:".$shop_name;
					// echo " source:".$source;
					
					if (isset ( $_COOKIE ['product_url'] )) {
						
						unset ( $_COOKIE ['product_url'] );
						unset ( $_COOKIE ['product_img'] );
						unset ( $_COOKIE ['product_name'] );
						unset ( $_COOKIE ['product_price'] );
						unset ( $_COOKIE ['product_size'] );
						unset ( $_COOKIE ['product_color'] );
						unset ( $_COOKIE ['product_quentity'] );
						unset ( $_COOKIE ['shop_name'] );
						unset ( $_COOKIE ['source'] );
						
						setcookie ( 'product_url', null, - 1, '/' );
						setcookie ( 'product_img', null, - 1, '/' );
						setcookie ( 'product_name', null, - 1, '/' );
						setcookie ( 'product_price', null, - 1, '/' );
						setcookie ( 'product_size', null, - 1, '/' );
						setcookie ( 'product_color', null, - 1, '/' );
						setcookie ( 'product_quentity', null, - 1, '/' );
						setcookie ( 'shop_name', null, - 1, '/' );
						setcookie ( 'source', null, - 1, '/' );
					}
					
					// ตรวจว่าสินค้ามีหรือยัง
					$item_exist = mysql_query ( "select * from product p, shopping_cart s
								where p.product_url='$product_url'
								and p.product_size='$product_size'
								and p.product_color='$product_color'
								and p.product_id = s.product_id
								and s.customer_id = '$login_id'", $connection );
					$item_exist_row = mysql_fetch_array ( $item_exist );
					$product_id = $item_exist_row ['product_id'];
					
					if (mysql_num_rows ( $item_exist ) == 0) {
						// เพิ่มสินค้าใหม่
						$add_product = mysql_query ( "insert into product(product_url,product_img,product_name,product_color,product_size,product_price,shop_name,source)
									values('$product_url','$product_img','$product_name','$product_color','$product_size','$product_price','$shop_name','$source')" );
						// เพิ่มสินค้าในต้กร้า
						$product_id = mysql_insert_id ();
						;
						$add_cart_item = mysql_query ( "insert into shopping_cart(product_id,customer_id,cart_quantity,cart_date)
									values('$product_id','$login_id','$product_quentity',now())" );
						// echo "เพิ่มสินค้าลงในตะกร้าเรียบร้อย <br />";
					} else if (mysql_num_rows ( $item_exist ) == 1) {
						// สินค้ามีอยู่แล้ว อัพเดทข้อมูลสินค้าในตะกร้า
						$update_product_id = $item_exist_row ['product_id'];
						$product_in_cart = mysql_query ( "select cart_quantity from shopping_cart where product_id='$update_product_id'" );
						$product_in_cart_row = mysql_fetch_array ( $product_in_cart );
						$current_product_qty = $product_in_cart_row ['cart_quantity'];
						$update_product_qty = $current_product_qty + $product_quentity;
						
						if (mysql_num_rows ( $product_in_cart ) > 0) {
							$update_cart_item = mysql_query ( "update shopping_cart set cart_quantity = $update_product_qty, cart_date = now()
										where product_id = '$update_product_id' and customer_id = '$login_id' " );
							// echo "สินค้ามีอยู่แล้วในตะกร้า ระบบได้เพิ่มจำนวนสินค้าจากที่มีอยู่<br />";
						} else {
							$add_cart_item = mysql_query ( "insert into shopping_cart(product_id,customer_id,cart_quantity,cart_date)
									values('$product_id','$login_id','$product_quentity',now())" );
							// echo "เพิ่มสินค้าลงในตะกร้าเรียบร้อย <br />";
						}
					}
					header ( 'Location: cart.php' );
				} else {
					// echo "cookie not set";
				}
				
				
				header ( 'Location: order_list.php' );
			} else {
				$loginfail = true;
				$error = "Please confirmation your email in <strong>" . $customer_rows ['customer_email'] . "</strong>";
			}
		} else {
			$loginfail = true;
			$error = "Username or Password is invalid";
		}
	}
	
	
		if(!$loginfail){
			//localhost/demo3/login.php?returnUrl=localhost/demo3/order_show_detail_confirmed.php?order_id=477
			$protocol = stripos ( $_SERVER ['SERVER_PROTOCOL'], 'https' ) === true ? 'https://' : 'http://';
			$url = $_POST['returnURL'];
			//echo $url;
			echo "<script>window.location.href = '".$url."';</script>";
		}
		
	
	
}
?>
<?php if ($error!='') { ?>
            <script type="text/javascript">
                $(document).ready(function(){ swal("Login Failed", "The username or password is incorrect, Please try again.", "error"); }) 
            </script>
           <?php  $protocol = stripos ( $_SERVER ['SERVER_PROTOCOL'], 'https' ) === true ? 'https://' : 'http://';
			$url = '?returnUrl='.$_POST['returnURL'];// $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].
			//echo $_SERVER['SERVER_NAME'].$_SERVER ['REQUEST_URI'];
			//PHP_SELF
// 			echo "<pre>";
// 			print_r($_SERVER);
// 			echo "</pre>";
			echo $url;
			echo "<script>window.location.href = '".$url."';</script>";
			?>
        <?php } ?>

        <?php if (isset($_GET['error'])) {
                if ($_GET['error'] == 'sessions_expire') { ?>
            <script type="text/javascript">
                $(document).ready(function(){ swal("Session Expired", "You are currently logged in on another browser. Please continue to use the other window and close this one.", "info"); }) 
            </script>
        <?php } 
} ?>

<?php function viewLogin(){?>

<!DOCTYPE html>
<html>
<head>
    <?php include 'page_script.php';  ?>
    <style type="text/css">
.header-dialog {
	
	margin: 0 auto;
}

.header-right a:nth-child(2){
	display:none;
}

.lightbox {
	display: none;
}
</style>
<script type="text/javascript">
//     	$( window ).load(function(){
// 			$('#login').remove();
// 		});

		$( window ).load(function(){
			$('#login').remove();

			//return checkLogin()
			$("form").submit(function(event){
				if(checkLoginEmail()){
					//alert('true');
					//$(this).attr('action',)
					//$(this).submit();
					var returnURL=getUrlParameter('returnUrl');
					console.log(returnURL);
					$('#returnURL').val(returnURL);
					$(this).submit();
					//window.location.href ='order_show_detail_confirmed.php?order_id=477';
				}else{
					//alert('false');
					return false;
				}
			});
		});

		function getUrlParameter(sParam) {
		    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
		        sURLVariables = sPageURL.split('returnUrl='),
		        sParameterName,
		        i;
	        
// 		    for (i = 0; i < sURLVariables.length; i++) {
// 		        sParameterName = sURLVariables[i].split('&page=login');
// 		    }

		    
// 		    console.log(sParameterName);
// 		    return sParameterName[0];
//alert(sURLVariables[1]);
			return sURLVariables[1];
		};

		function checkLoginEmail() {
			

			var flag=0;

				var email = document.getElementById('login-email');
				var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

				if (!filter.test(email.value)) {
					document.getElementById('help-login-email').innerText = "กรุณากรอกอีเมล์ให้ถูกต้อง";
					email.focus;
					flag = 1;
				}

			if (document.getElementById('login-password').value == "") {
				document.getElementById('help-login-password').innerText = "กรุณากรอกรหัสผ่าน";
				flag = 1;
			}else{
				document.getElementById('help-login-password').innerText = "";
			}
			

			if (flag == 1) {return false;}return true;
		}
    </script>
</head>
<body>
    <?php include 'nav_bar.php';  ?>

<div class="container" style="padding-top: 50px; padding-bottom: 50px;">
		<div class="header-dialog" style="position: relative;">
			<form action="login" method="post"  >
				<h3 class="focus">
					เข้าสู่ระบบ Order
					<p class="orange">2</p>
					Easy
				</h3>

				<label class="control-label" id="help-login-email"
					style="color: red;"></label> <input placeholder="อีเมล"
					name="email" id="login-email" style="color: black" /> <label
					class="control-label" id="help-login-password" style="color: red;"></label>
				<input placeholder="รหัสผ่าน" name="password" id="login-password"
					type="password" style="color: black" />
				<div id="line" style="width: 100%; height: 5px;"></div>
				<button name="submit" type="submit">เข้าใช้งาน</button>
				<a href="#" class="forgetpass" onclick="forgetpass()" style="float:left;">ลืมรหัสผ่าน</a>
				<input type="hidden" name="returnURL" value="" id="returnURL" />
				<div class="clear"></div>
			</form>
		</div>
	</div>

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

<?php }//end function view login?>
