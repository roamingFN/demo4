<?php 
require_once( 'inc/php/functions_statusConvert.php');
echo showLocalMessage($message_text,$error_text,$info_text);
$url = $_SERVER['REQUEST_URI'];
$tokens = explode('/', $url);
$path = $tokens[sizeof($tokens)-1];
?>
<div id="left_menu" class="menu-tab col4">
	<h3>
		<i class="material-icons">supervisor_account</i> ประเภทลูกค้า<br />
		<?php 
			$select_class = mysql_query("select class_name from customer c, customer_class cc 
				where c.class_id = cc.class_id and c.customer_id = $user_id");
			$select_class_row = mysql_fetch_array($select_class);
			$class_name = $select_class_row["class_name"];
		?>
		<img src="img/rank/<?php echo strtolower($class_name); ?>.png">
	</h3>

	<div class="menu-line"></div>
	<h3><i class="material-icons">assignment_ind</i> ข้อมูลลูกค้า</h3>
	<a href="profile" 			<?php if($path == "profile" || $path == "profile.php"){echo 'class="selected"';} ?> >ข้อมูลทั่วไป</a>
	<a href="change_password"	<?php if($path == "change_password" || $path == "change_password.php"){echo 'class="selected"';} ?> >รหัสผ่าน</a>
	<a href="fav_shop"			<?php if($path == "fav_shop" || $path == "fav_shop.php"){echo 'class="selected"';} ?> >ร้านค้าที่ชื่นชอบ</a>

	<div class="menu-line"></div>
	<h3><i class="material-icons">shopping_cart</i> สั่งซื้อ</h3>
	<a href="cart"				<?php if($path == "cart" || $path == "cart.php"){echo 'class="selected"';} ?> >ตะกร้า</a>
	<a href="order_list"		<?php if($path == "order_list" || $path == "order_list.php"){echo 'class="selected"';} ?> >รายการสั่งซื้อ</a>
	<a href="package"		<?php if($path == "package" || $path == "package.php"){echo 'class="selected"';} ?> >กล่อง</a>
	<!--<a href="payment_edit"		<?php //if($path == "payment_edit"){echo 'class="selected"';} ?> >ตรวจสอบสถานะการชำระเงิน</a>-->

	<div class="menu-line"></div>
	<h3><i class="material-icons">assessment</i> บัญชี</h3>
	<a href="topup"				<?php if($path == "topup" || $path == "topup.php"){echo 'class="selected"';} ?> >เติมเงิน (แจ้งโอนเงิน)</a>
	<a href="topup_history"		<?php if($path == "topup_history" || $path == "topup_history.php"){echo 'class="selected"';} ?> >ประวัติการเติมเงิน</a>
	<a href="payment_list"			<?php if($path == "payment_list" || $path == "payment_list.php"){echo 'class="selected"';} ?> >ชำระเงิน</a>
	<a href="statement"			<?php if($path == "statement" || $path == "statement.php"){echo 'class="selected"';} ?> >รายการบัญชีลูกค้า (statement)</a>
	<a href="withdraw"			<?php if($path == "withdraw" || $path == "withdraw.php"){echo 'class="selected"';} ?> >แจ้งถอนเงิน</a>
	<a href="withdraw_history"	<?php if($path == "withdraw_history" || $path == "withdraw_history.php"){echo 'class="selected"';} ?> >ประวัติการแจ้งถอนเงิน</a>

	<div class="menu-line"></div>
	<h3><i class="material-icons">settings</i> ตั้งค่าการใช้งาน</h3>
	<a href="#">แก้ไขการรับข้อมูล<br>(ผ่านทาง email)</a>

	<div class="menu-line"></div>
	<img src="img/kerry.png" style="border-radius:5px;"><br/>
	<form class="form-horizontal" action="http://th.kerryexpress.com/th/track/" id="frmTrack" method="get" target="_blank">
  	<input class="form-control" type="text" class="txt_input" id="track" name="track" placeholder="ป้อนรหัสติดตามสินค้า" maxlength="20" autocomplete="off" style="border-radius:0px 0px 0px 0px;">
  	<input class="form-control input-sm btn btn-success" type="submit" value="ตรวจสอบ" style="border-radius:0px 0px 0px 0px; margin-top:-1px;">
	</form>
	<div class="menu-line"></div>
	<img src="img/thai.png" style="border-radius:5px;"><br/>
	<form class="form-horizontal" action="http://www.thailandpost.com/index.php" method="get" target="_blank">
  	<input type="hidden" name="page" value="tracking">
  	<input class="form-control" type="text" name="barcode" placeholder="ป้อนรหัสติดตามสินค้า" maxlength="20" autocomplete="off" style="border-radius:0px 0px 0px 0px;">
  	<input class="form-control input-sm btn btn-success" type="submit" value="ตรวจสอบ" style="border-radius:0px 0px 0px 0px; margin-top:-1px;">
	</form>
	<div class="menu-line"></div>
</div>
