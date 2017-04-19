<?php
include 'connect.php';
session_start();
//include 'session.php';

//#login part
$error=''; // Variable To Store error Message
$error_message='';
$loginfail = false;

if (isset($_GET['error_message'])) {
    $error_message=$_GET['error_message'];
}

if (isset($_POST['submit'])) {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $loginfail = true;
        $error = "Username or Password is invalid";
    }
    else
    {
        // Define $email and $password
        $email=$_POST['email'];
        $password=$_POST['password'];

        // To protect MySQL injection for Security purpose
        $email = stripslashes($email);
        $password = stripslashes($password);
        $email = mysql_real_escape_string($email);
        $password = mysql_real_escape_string($password);

    //encode password
        $password = sha1($password);

        // SQL query to fetch information of registerd users and finds user match.
        $login = mysql_query("select * from customer where passwd='$password' AND customer_email='$email'", $connection);

        if (mysql_num_rows($login) > 0) {

            //echo "login success";

            $customer_rows = mysql_fetch_array($login);
            if ($customer_rows['active'] == 1) {

                $_SESSION['CX_login_user']=$email;
                $_SESSION['CX_login_name']=$customer_rows['customer_firstname']." ".$customer_rows['customer_lastname'];
                $_SESSION['CX_login_id']=$customer_rows['customer_id'];

                if (isset($_POST['login-remember'])) {
                    $year = time() + 31536000;
                    setcookie('remember_me', $_POST['email'], $year);
                }else if (!isset($_POST['login-remember'])) {
                    if(isset($_COOKIE['remember_me'])) {
                        $past = time() - 100;
                        setcookie(remember_me, gone, $past);
                    }
                }

            }else{
                $loginfail = true;
                $error = "Please confirmation your email in <strong>". $customer_rows['customer_email'] ."</strong>";
            }
            
        } else {
            $loginfail = true;
            $error = "Username or Password is invalid";
        }
    }
}
//include 'modal.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include 'page_script.php';  ?>
    <style type="text/css">
    	.circle{
    		-webkit-box-sizing: content-box;
			  -moz-box-sizing: content-box;
			  box-sizing: content-box;
    	}
    </style>
  </head>
  <body>
    <?php include 'nav_bar.php';  ?>

		<div class="content">
			<div class="wrapper categories"><div class="inner center">
				<h1>Order2Easy คืออะไร?</h1>
			</div></div>
			<div class="wrapper"><div class="inner">
				<br>
				<div class="col2 middle">
					<div class="center"><img src="images/large.png"/></div>
				</div>
				<div class="col2 middle">
					Order2Easy คือผู้ให้บริการรับสั่งสินค้าจากตลาดออนไลน์ จากประเทศจีนทั้งปลีกและส่ง โดยไม่ผ่านคนกลางแบบ 
					Door to Door ด้วยราคามิตรภาพ และบริการแบบมืออาชีพ
				</div>
			</div></div>
			<div class="line"></div>
			<div class="wrapper partner" style="background-image:url(images/web.png),url(images/seamless.jpg);">				
			</div>
			<div class="wrapper partner" style="background-image:url(images/web.png),url(images/seamless.jpg);">
				<div class="center">
					<img src="images/shops/taobao.png"/>
				</div>
			</div>
			<div class="wrapper partner" style="background-image:url(images/web.png),url(images/seamless.jpg);">
				<div class="center">
					<img src="images/shops/tmall.png"/>
				</div>
			</div>
			<div class="wrapper partner" style="background-image:url(images/web.png),url(images/seamless.jpg);">
				<div class="center">
					<img src="images/shops/jd.png"/>
				</div>
			</div>
			<div class="wrapper partner" style="background-image:url(images/web.png),url(images/seamless.jpg);">
				<div class="center">
					<img src="images/shops/dangdang.png"/>
				</div>
			</div>
			<div class="wrapper partner" style="background-image:url(images/web.png),url(images/seamless.jpg);">
				<div class="center">
					<img src="images/shops/amazon.png"/>
				</div>
			</div>
			<div class="wrapper partner" style="background-image:url(images/web.png),url(images/seamless.jpg);">
				<div class="center">
					<img src="images/shops/1688.png"/>
				</div>
			</div>
			<div class="wrapper partner big" style="background-image:url(images/web.png),url(images/seamless.jpg);">
				<p>ผ่านเว็บไซต์<br>Order2Easy.com</p>
				<div class="center">
					<img src="images/large_circle.png"/>
				</div>
				<br><br>
			</div>
			<div class="wrapper partner big" style="background-image:url(images/web.png),url(images/seamless.jpg);">
				<div class="center">
					<img src="images/avatars/8.png"/>
					<img src="images/avatars/2.png"/>
					<img src="images/avatars/6.png"/>
					<br><p>&emsp;ผู้ค้าปลีก&emsp;&emsp;&nbsp;&nbsp;ผู้ค้าส่ง&emsp;&emsp;เจ้าของกิจการ</p>
				</div>
			</div>
			<div class="wrapper partner big" style="background-image:url(images/web.png),url(images/seamless.jpg);">
				<div class="center">
					<i class="material-icons" style="color:#f44336;">clear</i><br>
					<img src="images/avatars/1.png"/><br>
					<p style="color:#fff;background:#f44336;">ไม่ผ่าน พ่อค้าคนกลาง</p><br>
					<i class="material-icons" style="color:#f44336;">clear</i>
				</div>
			</div>
			<div class="wrapper partner big" style="background-image:url(images/web.png),url(images/seamless.jpg);">
				<br><br>
				<div class="center">
					<img src="images/logo_circle.png"/>
				</div>
			</div>
			<div class="wrapper partner big" style="background-image:url(images/web.png),url(images/seamless.jpg);">
				<div class="inner center">
					<div class="circle"><br><h4>ต้นทุนถูกกว่า</h4>เพราะไม่ต้องผ่านคนกลาง</div>&emsp;&emsp;
					<div class="circle"><br><h4>สินค้าทันสมัยกว่า</h4>เลือกสินค้าได้เอง โดยตรงจากจีน</div>&emsp;&emsp;
					<div class="circle"><br><h4>สะดวกสบายกว่า</h4>สั่งซื้อออนไลน์ ง่ายๆสบายๆ</div>
				</div>
			</div>
			<div class="wrapper partner" style="background-image:url(images/web.png),url(images/seamless.jpg);">				
			</div>
			<div class="wrapper"><div class="inner center">
				<h1>บริการของ Order2Easy</h1>
					<div class="circle middle"><h1><i class="material-icons">shopping_cart</i></h1>สั่งซื้อสินค้าออนไลน์ จากจีน</div>&emsp;
					<div class="circle middle"><h1><i class="material-icons">business</i></h1>สั่งซื้อจากร้านค้า & โรงงานจีน</div>&emsp;
					<div class="circle middle"><h1><i class="material-icons">local_shipping</i></h1>ขนส่งสินค้าจีน - ไทย</div>&emsp;
					<div class="circle middle"><h1><i class="material-icons">transform</i></h1>โอนเงินหยวน เรทถูกที่สุด</div>&emsp;
			</div></div>
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

            $("div#dialog").dialog ({
              autoOpen : false
            });

        </script>
    </body>
</html>