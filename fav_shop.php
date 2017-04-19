<?php
	include 'connect.php';
	include 'session.php';
	include 'inc/php/functions_statusConvert.php';

	if (!empty($_POST['add_favorite'])) {

		$shop_name 			= $_POST['shop_name'];
		$shop_url 			= $_POST['shop_url'];
		$note 				= '';
		if (isset($_POST['note'])) {
			$note = $_POST['note'];
		}

		if(empty($shop_name)){ $error .= '<li>คุณยังไม่ได้กรอกชื่อร้านค้า</li>';}
		if(empty($shop_url)){ $error .= '<li>คุณยังไม่ได้กรอกลิงค์ร้านค้า</li>';}

		$shop_url 			= str_replace("http://", "", $shop_url);

		$shop_name 			= strip_tags($shop_name);
		$shop_url 			= strip_tags($shop_url);
		$note 				  = strip_tags($note);

		$shop_name 			= stripcslashes($shop_name);
		$shop_url 			= stripcslashes($shop_url);
		$note 				  = stripcslashes($note);

		$shop_name 			= mysql_real_escape_string($shop_name);
		$shop_url 			= mysql_real_escape_string($shop_url);
		$note 				  = mysql_real_escape_string($note);

		if ($error == '') {

			$add_fav = mysql_query("insert into customer_fav(customer_id,fav_name,fav_url,note) 
				values('$user_id','$shop_name','$shop_url','$note')");

			if($add_fav) {
				$info_text = 'เพิ่มร้านค้าลงในรายการโปรดเรียบร้อย';
			}else{
				//echo "fail".mysql_error();
			}
		}else{
			$error_text = '<label>เกิดข้อผิดพลาด</label>'.$error;
		}
	}

	if (!empty($_GET['delete'])) {
		$fav_id = $_GET['delete'];

		$delete_fav = mysql_query("delete from customer_fav where customer_id = '$user_id' and fav_id = '$fav_id'");

		if ($delete_fav) {
			$message_text = "ลบร้านค้าเรียบร้อย";
		}else{
			//show_error("Error".mysql_error());
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
			<h3>เพิ่มร้านค้าที่ชื่นชอบ</h3>
			<form method="post" action="fav_shop.php">
			<table class="content-light">
				<tr><th>ชื่อร้านค้า</th><td><input name="shop_name" type="text" placeholder="ชื่อของร้านค้า" value="<?php if($add_shop_name!='')echo $add_shop_name; ?>" /></td></tr>
				<tr><th>ลิ้งค์ร้านค้า</th><td><input name="shop_url" type="text" placeholder="URL ของร้าน" value="<?php if($add_shop_url!='')echo $add_shop_url; ?>" /></td></tr>
				<tr><th>หมายเหตุ</th><td><input name="note" type="text" placeholder="หมายเหตุ"/></td></tr>
				<tr><td></td><td><button type="submit" name="add_favorite" value="Submit">เพิ่มร้านค้า</button></td></tr>
			</table>
			</form>
			<hr>
			<h3>รายการร้านค้าที่ชื่นชอบ</h3>
			<table class="content-grid">
				<tr>
					<th>ลำดับ</th>
					<th>ชื่อร้านค้า</th>
					<th>ลิงค์ร้านค้า</th>
					<th>หมายเหตุ</th>
					<th>การดำเนินการ</th>
				</tr>
				<?php 

				$select_fav = mysql_query("select * from customer_fav where customer_id = '$user_id'");
				$count_fav = 1;
				while($row = mysql_fetch_array($select_fav)){
					echo "
					<tr>
					<td>".$count_fav."</td>
					<td>".htmlentities($row['fav_name'])."</td>
					<td><a target='_blank' href='http://".htmlentities($row['fav_url'])."'>".htmlentities($row['fav_url'])."</a></td>
					<td>".htmlentities($row['note'])." &nbsp</td>
					<td><a class='button' name='delete' href='fav_shop_edit.php?fav_id=".$row['fav_id']."' >แก้ไข</a> <a class='delete' href='fav_shop.php?delete=".$row['fav_id']."' onclick='return confirm_remove()'>✖</a></td>
					</tr>";
					$count_fav++;
				}
				
				?>
			</table>
		</div>
	</div>

<script type="text/javascript">
	function confirm_remove(){
		var r = confirm("คุณต้องการลบร้านค้าออกจากรายการที่ชืนชอบใช่หรือไม่");
		return r;
	}
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