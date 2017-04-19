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
				<h1>วิธีการชำระเงิน & ค่าขนส่ง</h1>
			</div></div>
			<div class="wrapper"><div class="inner">
				<h3><i class="material-icons">attach_money</i>วิธีการชำระเงินจะแบ่งเป็นสองรอบ</h3>
				<h4>รอบที่ 1 (เป็นการชำระค่าสินค้า คิดตามจริงไม่มีค่าบริการ)</h4>
				<b>• ค่าสินค้า</b><br>
				&emsp;ราคาสินค้า(หยวน) x อัตราแลกเปลี่ยน**<br>
				<b>• ค่าขนส่งภายในประเทศจีน</b><br>
				&emsp;เก็บค่าขนส่งตามจริงที่ร้านค้าจีนเรียกเก็บ
				&emsp;<h6>** ยกเว้น บางร้านค้าที่คิดค่าขนส่งแบบเหมาจ่าย วิธีการคิดจะแตกต่างจากที่กล่าวมาข้างต้น **</h6><br>
				<div class="line"></div>
				<h4>รอบที่ 2 (เป็นการชำระค่าขนส่ง จีน-ไทย และค่าขนส่งภายในไทย)</h4>
				<b>• ค่าขนส่ง จีน-ไทย (สินค้าจะถูกส่งทางรถเท่านั้น)</b>
			</div></div>
			<div class="wrapper focus"><div class="inner">
				<h3><i class="material-icons">local_shipping</i> อัตราค่าขนส่งทางรถ</h3>
				<div class="col2">
					<table class="shipping" cellspacing="0">
						<thead style="background:#f44336;"><th>Classic Kg.</th><th>สินค้าทั่วไป</th><th>สินค้าพิเศษ</th></thead>
						<tbody><td>0.00-04.99</td><td>90</td><td>180</td></tbody>
						<tr><td>2.00-19.99</td><td>75</td><td>170</td></tr>
						<tr><td>20.00-49.99</td><td>65</td><td>160</td></tr>
						<tr><td>50.00-100.00</td><td>55</td><td>150</td></tr>
					</table>
					<table class="shipping" cellspacing="0">
						<thead style="background:#ff9800;"><th>Gold Kg.</th><th>สินค้าทั่วไป</th><th>สินค้าพิเศษ</th></thead>
						<tbody><td>0.00-04.99</td><td>80</td><td>170</td></tbody>
						<tr><td>2.00-19.99</td><td>70</td><td>160</td></tr>
						<tr><td>20.00-49.99</td><td>60</td><td>150</td></tr>
						<tr><td>50.00-100.00</td><td>55</td><td>140</td></tr>
					</table>
					<table class="shipping" cellspacing="0">
						<thead style="background:#3f51b5;"><th>Platinum Kg.</th><th>สินค้าทั่วไป</th><th>สินค้าพิเศษ</th></thead>
						<tbody><td>0.00-04.99</td><td>70</td><td>160</td></tbody>
						<tr><td>2.00-19.99</td><td>65</td><td>150</td></tr>
						<tr><td>20.00-49.99</td><td>60</td><td>140</td></tr>
						<tr><td>50.00-100.00</td><td>55</td><td>130</td></tr>
					</table>
					<table class="shipping" cellspacing="0">
						<thead style="background:#009688;"><th>Diamond Kg.</th><th>สินค้าทั่วไป</th><th>สินค้าพิเศษ</th></thead>
						<tbody><td>0.00-04.99</td><td>55</td><td>130</td></tbody>
						<tr><td>2.00-19.99</td><td>55</td><td>130</td></tr>
						<tr><td>20.00-49.99</td><td>55</td><td>130</td></tr>
						<tr><td>50.00-100.00</td><td>55</td><td>130</td></tr>
					</table>
				</div>
				<div class="col2">
					<table class="shipping">
						<thead style="background:#f44336;"><th>Classic Q.</th><th>สินค้าทั่วไป</th><th>สินค้าพิเศษ</th></thead>
						<tbody><td>0.00-0.20</td><td>9500</td><td>19000</td></tbody>
						<tr><td>0.20-0.99</td><td>9000</td><td>18000</td></tr>
						<tr><td>1.00-4.99</td><td>8500</td><td>17000</td></tr>
						<tr><td>5.00+</td><td>8000</td><td>16000</td></tr>
					</table>
					<table class="shipping">
						<thead style="background:#ff9800;"><th>Gold Q.</th><th>สินค้าทั่วไป</th><th>สินค้าพิเศษ</th></thead>
						<tbody><td>0.00-0.20</td><td>9000</td><td>18000</td></tbody>
						<tr><td>0.20-0.99</td><td>8500</td><td>17000</td></tr>
						<tr><td>1.00-4.99</td><td>8500</td><td>16000</td></tr>
						<tr><td>5.00+</td><td>8000</td><td>15000</td></tr>
					</table>
					<table class="shipping">
						<thead style="background:#3f51b5;"><th>Platinum Q.</th><th>สินค้าทั่วไป</th><th>สินค้าพิเศษ</th></thead>
						<tbody><td>0.00-0.20</td><td>8500</td><td>17000</td></tbody>
						<tr><td>0.20-0.99</td><td>8500</td><td>16000</td></tr>
						<tr><td>1.00-4.99</td><td>8000</td><td>15000</td></tr>
						<tr><td>5.00+</td><td>8000</td><td>15000</td></tr>
					</table>
					<table class="shipping">
						<thead style="background:#009688;"><th>Diamond Q.</th><th>สินค้าทั่วไป</th><th>สินค้าพิเศษ</th></thead>
						<tbody><td>0.00-0.20</td><td>8000</td><td>15000</td></tbody>
						<tr><td>0.20-0.99</td><td>8000</td><td>15000</td></tr>
						<tr><td>1.00-4.99</td><td>8000</td><td>15000</td></tr>
						<tr><td>5.00+</td><td>8000</td><td>15000</td></tr>
					</table>
				</div>
			</div></div>
			<div class="wrapper categories"><div class="inner center">
				<h1>การปรับเปลี่ยนระดับลูกค้า</h1><br>
				<table class="customer center">
					<thead><th>ระดับใหม่</th><th>วิธีเพิ่มระดับสมาชิก</th></thead>
					<tbody><td style="color:#f44336;"><i class="material-icons" style="color:#f44336;">check_circle</i> Classic</td><td style="color:#f44336;">เริ่มสมัครสมาชิก</td></tbody>
					<tr><td style="color:#ff9800;"><i class="material-icons" style="color:#ff9800;">star</i> Gold</td><td style="color:#ff9800;">มียอดสะสมครบ 50,000 บาท</td></tr>
					<tr><td style="color:#3f51b5;"><i class="material-icons" style="color:#3f51b5;">stars</i> Platinum</td><td style="color:#3f51b5;">มียอดสะสมครบ 500,000 บาท</td></tr>
					<tr><td style="color:#009688;"><i class="material-icons" style="color:#009688;">security</i> Diamond</td><td style="color:#009688;">มียอดสะสมครบ 2,000,000 บาท</td></tr>
				</table>
			</div></div>
			<div class="wrapper"><div class="inner">
				<h3><i class="material-icons">file_download</i> เกณฑ์การคิดค่าบริการตามน้ำหนักหรือปริมาตรสินค้า</h3>
				<table class="customer criteria">
					<tr><th>สินค้าที่คิดตามน้ำหนัก</th><td>เสื้อผ้าแฟชั่น กระเป๋า - รองเท้าที่มีน้ำหนักมาก มือถือ - คอมพิวเตอร์และอุปกรณ์ต่อพ่วง เครื่องใช้ไฟฟ้าต่างๆ และสินค้าที่มีน้ำหนักมากอื่นๆ</td></tr>
					<tr><th>สินค้าที่คิดตามปริมาตร</th><td>เฟอร์นิเจอร์ ยานยนต์และอุปกรณ์ ตุ๊กตาและของเล่นต่างๆ กระเป๋า - รองเท้าที่มีน้ำหนักเบา ของใช้ - ของแต่งบ้านขนาดใหญ่/น้ำหนักเบา</td></tr>
				</table>
				<h6>** มาตรฐานการคิดสินค้าเป็นปริมาตรคือ สินค้าที่มีน้ำหนักน้อยกว่า 150 กิโลกรัมต่อคิว **</h6><br>
				<div class="line"></div>
				<h3>วิธีการคำนวณตามปริมาตร(คิว)ทางรถหรือเรือ</h3>
				<div class="col2">
					<br>
					<table class="formula focus">
						<tr><th>ความกว้าง x ยาว x สูง (หน่วยเป็น cm)</th></tr>
						<tr><td>1,000,000</td></tr>
					</table>
				</div>
				<div class="col2">
					<div class="center">
						<p class="underline">ตัวอย่าง</p> การคิดค่าขนส่งแบบเป็นคิว ให้สินค้า 1 ลัง ขนาดลัง 30 x 40 x 50 cm<br><br>
						<table class="formula">
							<tr><th>30 x 40 x 50</th></tr>
							<tr><td>1,000,000</td></tr>
						</table>
						<p>= 0.06 คิว</p>
					</div>
				</div>
				<div class="center"><img src="images/box.png"></div><br>
				<h6><i class="material-icons">error</i> <b>ข้อควรระวัง : </b>
				สำหรับสินค้าที่แตกหักง่ายหรือต้องการการดูแลเป็นพิเศษ ลูกค้าต้องแจ้งให้ต่อลังไม้เพื่อป้องกันการเสียหายของสินค้า และอาจจะมีค่าใช้จ่ายเพิ่มเติม
				ยกตัวอย่าง เช่น แก้วเครื่องกระเบื้อง หุ่นยนต์กันดั้ม ฟิกเกอร์ เป็นต้น</h6>
			</div></div>
			<div class="wrapper focus"><div class="inner">
				<h3><i class="material-icons">flag</i>การขนส่งภายในประเทศไทย</h3>
				<div class="col2">
					การขนส่งไปยังลูกค้ามี 2 แบบ คือ<br><br>
					&emsp;<b>• มารับเองที่บริษัท</b> (ไม่มีค่าใช้จ่าย)<br>
					&emsp;<b>• จัดส่งผ่านบริษัทขนส่งภายในประเทศ</b> การจัดส่งสินค้าผ่านบริษัทขนส่ง คิดค่าบริการตามจริง + ค่าฝากส่งสินค้า (20 - 50 บาท ตามขนาดสินค้า)
					โดยลูกค้าสามารถเลือกบริษัทขนส่งได้ เช่น ไปรษณีย์ไทย นิ่มซี่เส็ง KERRY EXPRESS<br>
				</div>
				<div class="col2">
					<div class="remark">
						<h6><b>หมายเหตุในการจัดส่งสินค้า</b><br>
						&emsp;• Order2Easy จะเริ่มนับระยะเวลาการจัดส่งสินค้า เมื่อได้รับหลักฐานยืนยันการชำระเงินจากลูกค้า<br>
						&emsp;• ระยะเวลาในการจัดส่งเป็นระยะเวลาโดยประมาณ อาจมีการคลาดเคลื่อนได้ โดยเฉพาะอย่างยิ่งในช่วงเวลาเทศกาล</h6>
					</div>
				</div>
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