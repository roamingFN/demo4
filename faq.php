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
  </head>
  <body>
    <?php include 'nav_bar.php';  ?>

		<div class="content">
			<div class="wrapper categories"><div class="inner center">
				<h1>คำถามที่พบบ่อย</h1>
			</div></div>
			<div class="wrapper"><div class="inner column">
				<table class="pin" cellspacing="0">
					<tr><th><i class="material-icons">announcement</i> ทำไมราคาที่แสงในเว็ปจีน กับ Order2Easy ถึงไม่เท่ากัน?</th></tr>
					<tr><td>
						&emsp;&emsp;เวลาลูกค้าสั่งของ โดยใช้ลิงค์ของเถาเป่าจะเห็นราคาเต็ม เนื่องจากระบบ Order2Easy 
						จะแสดงราคาสินค้าที่สูงสุดก่อน แต่พอลูกค้ายืนยันออเดอร์ เจ้าหน้าที่CSตรวจสอบแล้ว ราคาจะเปลี่ยนอัตโนมัติทันที
					</td></tr>
				</table>
				<table class="pin" cellspacing="0">
					<tr><th><i class="material-icons">announcement</i> ระยะเวลาในการขนส่ง ?</th></tr>
					<tr><td>
						&emsp;&emsp;ระยะเวลาที่ร้านส่งของมายังโกดังที่จีนขึ้นอยู่กับความใกล้ไกลของร้านค้าใช้เวลาประมาณ1-3วัน 
						และเมื่อสินค้ามาถึงโกดังที่จีนแล้วจะใช้เวลาขอส่งประมาณ 4-5วัน ทาง Order2Easy 
						จะรอลูกค้าคอนเฟิร์มจ่ายค่าขนส่ง แล้วสามารถมารับสินค้าได้เลย ถ้าส่งทางไปรษณีย์ใช้เวลาอีก1-3วัน
					</td></tr>
				</table>
				<table class="pin" cellspacing="0">
					<tr><th><i class="material-icons">announcement</i> สินค้าอะไรบ้างที่อาจจะเสียหายได้จากการขนส่ง ?</th></tr>
					<tr><td>
						&emsp;&emsp;สินค้าพวกที่ทำจากแก้ว กระจก เฟอร์นิเจอร์ โคมไฟ สินค้าที่มีรูปทรง กระเป๋าบางแบบ หมวกบางแบบ สินค้าพวกนี้ต้องการกล่องหรือลังที่มีความแข็งแรง 
						เพราะระหว่างการขนส่งสินค้าจะมีการขนย้ายหลายครั้ง บางครั้งอาจมีการวางสินค้าซ้อนกัน ซึ่งอาจทำให้สินค้าเสียหายได้ทางลูกค้าต้องแจ้งทางร้านค้า
						หรือโรงงานให้แพ็คสินค้ามาให้เหมาะสม
					</td></tr>
				</table>
				<table class="pin" cellspacing="0">
					<tr><th><i class="material-icons">announcement</i> ทำไมสั่งสินค้าพร้อมกัน แต่ส่งของมาถึงไทยไม่พร้อมกัน ?</th></tr>
					<tr><td>
						&emsp;&emsp;ทาง Order2Easy จะสั่งสินค้าให้ลูกค้าตามออเดอร์ที่ได้รับมา แต่บางครั้งทางร้านค้าอาจมีของไม่ครบ 
						ร้านค้าอาจจะแยกส่งสินค้ามาทำให้ลูกค้าได้รับสินค้าไม่พร้อมกัน
					</td></tr>
				</table>
				<table class="pin" cellspacing="0">
					<tr><th><i class="material-icons">announcement</i> สั่งสินค้าแล้วสินค้าหมดจะมีการคืนเงินไหม ?</th></tr>
					<tr><td>
						&emsp;&emsp;เงินที่เหลือจากการที่สินค้ามีไม่ครบจะถูกคืนในระบบ ลูกค้าสามารถนำเงินที่มีในระบบสั่งชำระค่าสินค้า 
						หรือค่าขนส่งได้ แต่ถ้าลูกค้ามีความต้องการให้โอนเงินคืนไปยังบัญชีลูกค้า 
					</td></tr>
				</table>
				<table class="pin" cellspacing="0">
					<tr><th><i class="material-icons">announcement</i> ระยะเวลาการคืนเงิน กรณีสินค้าไม่ครบ สินค้าขาด เคลมสินค้า และถอนเงินสดใช้ระยะเวลาเท่าไหร่ ?</th></tr>
					<tr><td>
						&emsp;&emsp;ทางเราจะทำการคืนเงินให้ลูกค้าภายใน 5-7 วัน
					</td></tr>
				</table>
				<table class="pin" cellspacing="0">
					<tr><th><i class="material-icons">announcement</i> ถ้าสั่งสินค้าแล้วสินค้าหมด สินค้าไม่ครบ จะทำอย่างไร ?</th></tr>
					<tr><td>
						&emsp;&emsp;หลังจากที่ลูกค้าทำการสั่งสินค้าแล้ว ทาง Order2Easy จะ...
					</td></tr>
				</table>
				<table class="pin" cellspacing="0">
					<tr><th><i class="material-icons">announcement</i> ถ้าสินค้าเสียหายจากการขนส่งจะมีการดูแลเรื่องนี้อย่างไร ?</th></tr>
					<tr><td>
						&emsp;&emsp;ถ้าเกินจากขนส่ง ทาง Order2Easy จะทำการรับผิดชอบมูลค่า 3เท่าของค่าขนส่ง แต่ไม่เกิน 2,000บ แต่ถ้าเกิดจากผิดพลาดของร้านค้า 
						เช่นสีไม่ตรง ไซด์ไม่ตรง ผิดแบบ จำนวนไม่ครบ ลูกค้าต้องแจ้งภายใน 3วันหลังจากได้รับสินค้า ทาง Order2Easy จะทำการประสานงานกับร้านค้าให้ 
						แต่ทาง Order2Easy จะไม่รับผิดชอบ
					</td></tr>
				</table>
				<table class="pin" cellspacing="0">
					<tr><th><i class="material-icons">announcement</i> สั่งซื้อสินค้าเรียบร้อยแล้ว จะรู้ได้อย่างไรว่าสินค้าถึงวันไหน ?</th></tr>
					<tr><td>
						&emsp;&emsp;สามารถเช็คสถานะสินค้าได้จาก เมนูเช็คสถานะสินค้า โดยสามารถค้นห้าได้จาก เลขที่ออเดอร์ เลข Tracking จีน เลข Tracking ไทย
					</td></tr>
				</table>
				<table class="pin" cellspacing="0">
					<tr><th><i class="material-icons">announcement</i> จะรู้ได้อย่างไรว่าค่าขนส่ง จะถูกคิดเป็นคิว หรือ Kg. ?</th></tr>
					<tr><td>
						&emsp;&emsp;ขึ้นอยู่ประเถทสินค้าที่ส่งเช่นถ้าเป็นสินค้าน้ำหนักเบา และใช้พื้นที่จะคิดเป็นคิว เช่น รองเท้า ถุงเท้า หมวก ผ้าพันคอ เสื้อหนาว 
						โต๊ะ เก้าอี้ โคมไฟ สินค้าอีเล็คโทรนิค ของเล่นเด็ก เสื้อผ้าเด็ก ถ้าเป็นสินค้าน้ำหนักมาก ใช้พื้นที่น้อยจะคิดเป็นกิโล เช่น เสื้อผ้า เข็มขัด 
						กระเป๋าที่ไม่มีทรง อะไหร่รถ ร่ม 
					</td></tr>
				</table>
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