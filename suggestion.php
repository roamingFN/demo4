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
				<h1>การแนะนำร้านค้า</h1>
			</div></div>
			<div class="wrapper"><div class="inner">
				<a href="http://taobao.com" target="_blank">Taobao.com</a> เป็นศูนย์รวมร้านค้าจากทั่วประเทศจีนมาไว้ในที่เดียวซึ่งทำให้ผู้ซื้อหาซื้อสินค้าได้ง่ายดายแต่ข้อควรระวังประการหนึ่งก็คือ 
				การเลือกร้านค้าเพื่อซื้อสินค้า การเลือกร้านค้าที่มีคุณภาพนั้นมีประโยชน์ในหลายๆด้านด้วยกันคือ<br><br>
				&emsp;1. คุณภาพสินค้าไว้ใจได้<br>
				&emsp;2. ราคาสินค้าเหมาะสม<br>
				&emsp;3. บริการหลังการขายที่ดี<br>
				&emsp;4. หากเกิดปัญหา ร้านค้าจะช่วยแก้ปัญหาและมีความรับผิดชอบ (การคืนเงินจะมีเฉพาะบาง ร้านเท่านั้น) ทิปและเทคนิคในการเลือกร้านค้า ที่ง่ายและรวดเร็วคือดู 
				มงกุฏจะบอกถึง คุณภาพ ที่ขายไปแปลว่าร้านนี้อยู่มานาน ซื้อได้ไม่โกง แต่แถบเปอร์เซ็นกับคอมเม้นจะสะท้อนความ เห็นจริงๆจากลูกค้า โดยดูจาก 
				ตรงส่วนบนหน้าเว็บจะมีรูปสัญลักษณ์บอกถึงระบบร้านค้า<br><br>
				<img src="images/suggestion/1.gif"/><br>
				<h3>เรียงลำดับจากคะแนนน้อยสุด – มากสุด</h3>
				<table>
				<tr><td><b>• หัวใจ</b></td><td><img src="images/suggestion/heart.gif"/></td></tr>
				<tr><td><b>• เพชร</b></td><td><img src="images/suggestion/diamond.gif"/></td></tr>
				<tr><td><b>• มงกุฎสีน้ำเงิน</b></td><td><img src="images/suggestion/blue.gif"/></td></tr>
				<tr><td><b>• มงกุฎสีทอง</b></td><td><img src="images/suggestion/gold.gif"/></td></tr>
				</table><br>
				<p class="underline">ยิ่งจำนวนมงกุฎ หรือ เพชร มากเท่าไหร่ ยิ่งดี</p> แต่ก็ใช้ว่า หัวใจ หรือ เพรช จะสั่งไม่ได้ สั่งได้เหมือนกันแต่เลือกร้านให้ดูดีๆ<br><br>
				<img src="images/suggestion/2.gif"/><br><br>
				<h3>การดูการ value คะแนนจากลูกค้าในร้านค้า</h3>
				&emsp;1. แสดงถึงความเหมือนของสินค้าในร้านกับสินค้าจริงที่ลูกค้าได้รับ แต่ไม่ได้วัดคุณภาพของสินค้า (คะแนนเต็ม 5 )<br>
				&emsp;2. แสดงถึงทัศนคติของลูกค้า ที่มีต่อการบริการของร้านค้านั้นๆจ้า (คะแนนเต็ม 5 )<br>
				&emsp;3. แสดงถึงความรวดเร็วในการส่งสินค้า (คะแนนเต็ม 5 )<br>
				&emsp;4. จำนวนสินค้าทั้งหมดที่มีอยู่ในร้าน<br>
				&emsp;5. แสดงวันที่เปิดร้านค้าค่ะ ทำให้เราทราบว่าร้านนี้เปิดให้บริการมานานแค่ไหนแล้ว<br><br>
				<img src="images/suggestion/3.gif"/><br><br>
				<h3>วิธีการดูสินค้า Taobao.com</h3>
				<img src="images/suggestion/4.gif"/><br><br>
				&emsp;1. แสดงราคาหยวน 23 หยวน<br>
				&emsp;2. แสดงค่าขนส่งในจีน<br>
				&emsp;3. แสดงให้เลือก ชาย,หญิง หรือขนาด Size<br>
				&emsp;4. แสดงสีหรือแบบเสื้อผ้าถ้ามีลายอื่นจะมีให้เลือก<br>
				&emsp;5. แสดงสินค้าที่เหลืออยู่ในร้านที่สามารถสั่งซื้อได้<br>
				<h3>วิธีการดูคอมเมนจากลูกค้า</h3>
				<img src="images/suggestion/5.gif"/><br><br>
				&emsp;1. แสดงรายละเอียดสินค้า ขนาด เนื้อผ้า ฯลฯ<br>
				&emsp;2. แสดงคอมเมนจากลูกค้าที่สั่งซื้อไปมาแล้ว ในตัวอย่าง มีทั้งหมด 142 คอมเมน<br>
				&emsp;3. แสดงจำนวนและชื่อของสินค้าชนิดนั้นๆที่ถูกขายออกไปกี่ชิ้นแล้ว ในตัวอย่าง กระเป๋าในนี้ได้ถูกขายไป 21 ชิ้นแล้ว<br><br>
				วิธีการดูคอมเมนจากลูกค้าทางร้านแนะนำให้ทางลูกค้าใช่งาน web brownser ของ Goolge Chrome 
				เพราะมันจะสามารถแปลภาษาจีนเป็นภาษาไทยได้ทั้งหน้า ดังตัวอย่าง<br><br>
				<h3>แถบ Comment</h3>
				<img src="images/suggestion/6.png"/><br><br>
				<p class="underline">รายละเอียดปุ่มให้เลือกดูคอมเมนของลูกค้าที่สั่งสินค้าไปแล้วว่าเหมือนแบบไหมเนื้อผ้าเป็นยังไง 
				โดยสามารถเลือกปุ่มคือ ดี, แย่และรูปภาพ</p><br><br>
				<h3>แถบ จำนวนสั่งซื้อ</h3>
				<img src="images/suggestion/7.jpg"/><br><br>
				แสดงจำนวนของสินค้าที่ถูกขายออกไปและชื่อของผู้ซื้อรวมไปถึงระดับของผู้ซื้อ<br><br>
				&emsp;• ช่องที่ 1 คือ แสดงชื่อและตำแหน่งของผู้ซื้อ ทั้งนี้ขึ้นอยู่กับว่าตอนจ่ายเงินลูกค้าจะยินยอมให้เปิดเผยหรือไหม<br>
				&emsp;• ช่องที่ 2 คือ ราคาสินค้า<br>
				&emsp;• ช่องที่ 3 คือ จำนวนที่สั่งซื้อ<br>
				&emsp;• ช่องที่ 4 คือ วันเวลาของการสั่งซื้อสินค้า<br>
				&emsp;• ช่องที่ 5 คือ สีหรือแบบลายของสินค้านั้นๆ<br>
			</div></div>
			<div class="wrapper focus"><div class="inner">
				<div class="remark"><h6><b>*ข้อควรระวัง</b> ในลำดับที่ 3 และ 4 โชว์ให้เห็นว่ามีสินค้า แต่ลูกค้าต้องคลิ๊กดูว่า แบบนี้เหลือชายหรือหญิงหรือเหลือเพียงอย่างใดอย่างหนึ่ง   
				ให้สังเกตุทำตามขั้นตอนดังต่อไปนี้ ในลำดับที่ 3 เป็นขนาดชายและหญิง ส่วนลำดับที่ 4 คือ ลายของเสื้อผ้า ให้ลองคลิ๊กที่ สีหรือแบบเสื้อนั้น คือ 
				ลำดับที่ 4 และให้สั่งเกตุว่าในช่องของลำดับที่ 3 เปลี่ยนไปไหม ถ้าเปลี่ยนแสดงว่า จะเหลือไม่ชายก็หญิง (ถ้ามีสินค้าในลำดับที่ 3 
				จะสามารถคลิ๊กเลือกไปมาระหว่างชายกับหญิงได้ ถ้าไม่มีสินค้าระบบจะเปลี่ยนช่องนั้นเป็นกรอบปะๆและคลิ๊กไม่ได้) 
				ซึ่งมีลูกค้าหลายท่านพลาดในจุดนี้และต้องยืนเรื่องคืนเงินเพราะมีสินค้าในภายหลัง</h6></div>
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

        </script>
    </body>
</html>