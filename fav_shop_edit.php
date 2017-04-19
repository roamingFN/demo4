<?php
	include 'connect.php';
	include 'session.php';
	include 'inc/php/functions_statusConvert.php';
	$error = '';
	if (!empty($_POST['update'])) {

		$fav_id 			= $_POST['fav_id'];
		$shop_name 			= $_POST['shop_name'];
		$shop_url 			= $_POST['shop_url'];
		$note 				= '';
		if (isset($_POST['note'])) {
			$note = $_POST['note'];
		}

		if(empty($shop_name)){ $error .= '<li>คุณยังไม่ได้กรอกชื่อร้านค้า</li>';}
		if(empty($shop_url)){ $error .= '<li>คุณยังไม่ได้กรอกลิงค์ร้านค้า</li>';}

		$shop_url 			= str_replace("http://", "", $shop_url);

    $shop_name      = strip_tags($shop_name);
    $shop_url       = strip_tags($shop_url);
    $note           = strip_tags($note);

		$shop_name 			= stripcslashes($shop_name);
		$shop_url 			= stripcslashes($shop_url);
		$note 				  = stripcslashes($note);

		$shop_name 			= mysql_real_escape_string($shop_name);
		$shop_url 			= mysql_real_escape_string($shop_url);
		$note   				= mysql_real_escape_string($note);

		if ($error == '') {

			$add_fav = mysql_query("update customer_fav set fav_name='$shop_name',fav_url='$shop_url',note='$note' where customer_id ='$user_id' and fav_id = '$fav_id'");

			if($add_fav) {
				show_success("อัพเดทข้อมูลร้านค้าเรียบร้อย");
			}else{
				show_error("fail".mysql_error());
			}
		}else{
			show_error($error);
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
			<h1>ร้านค้าที่ชื่นชอบ</h1>
			<h3>แก้ไขข้อมูลร้านค้า</h3>
			<?php 

				$fav_id = $_GET['fav_id'];
				if (isset($_POST['fav_id'])) {
					$fav_id = $_POST['fav_id'];
				}
				
				//echo $fav_id;
				$select_fav = mysql_query("select * from customer_fav where customer_id = '$user_id' and fav_id = '$fav_id'");
				$row = mysql_fetch_array($select_fav);

				if (isset($fav_id)) {
					echo'
						<form method="post" action="fav_shop_edit.php">
						<table class="content-light">
							<tr><th>ชื่อร้านค้า</th><td><input name="shop_name"  placeholder="ชื่อของร้านค้า" value="'.$row['fav_name'].'"/></td></tr>
							<tr><th>ลิ้งค์ร้านค้า</th><td><input name="shop_url" placeholder="URL ของร้าน" value="'.$row['fav_url'].'"/></td></tr>
							<tr><th>หมายเหตุ</th><td><input name="note" placeholder="หมายเหตุ" value="'.$row['note'].'"/></td></tr>
							<input type="hidden" value="'.$fav_id.'" name="fav_id">
							<tr><td></td><td><button type="submit" value="submit" name="update">แก้ไขข้อมูล</button>&emsp;
							<a href="fav_shop.php">&#10094; กลับไปยังรายการ</a></td></tr>
						</table>
						</form>';
				}else{
					if (empty($_POST['update'])){
						echo '<div class="alert alert-danger row-md-9" role="alert">เกิดข้อผิดพลาด<li>ไม่สามารถดึงข้อมูลร้านค้าได้</li></div>';
						echo '<a href="fav_shop.php" class="btn btn-default"><-- กลับไปหน้ารายการร้านค้าที่ชื่นชอบ</a>';
					}
				}
				?>
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