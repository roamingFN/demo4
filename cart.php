<?php
	include 'connect.php';
	include 'session.php';

	$query_site_url = mysql_query("select * from website_config");
	$site_url_row = mysql_fetch_array($query_site_url);
	$site_lowest_price = $site_url_row['SITE_LOWEST_PRICE'];
	$max_cart = $site_url_row['SITE_MAX_CART'];

	if(isset($_GET['action']) && $_GET['action'] != ''){

		//update product
		$action = $_GET['action'];

		if ($action == 'update' && isset($_POST['updatedata'])) {
			$cartId     = $_POST['hidCartId'];
			$productId  = $_POST['hidProductId'];
			$itemQty    = $_POST['txtQty'];
			$selected   = $_POST['selected'];
			$numItem    = count($itemQty);
			$numDeleted = 0;
			$notice     = '';
			$customer_id = $_SESSION['CX_login_id'];

			//reset product selected = 0
			$reset_selected = mysql_query("update shopping_cart set selected = 0 where customer_id = '$customer_id'", $connection);
			if(!empty($_POST['selected'])) {
			    foreach($_POST['selected'] as $check) {
			            $update_selected = mysql_query("update shopping_cart set selected = 1 where cart_id = '$check'", $connection);
			    }
			}
			
			for ($i = 0; $i < $numItem; $i++) {
				$newQty = (int)$itemQty[$i];
				// if ($newQty < 1) {
				// 	// remove this item from shopping cart
				// 	$customer_id = $_SESSION['CX_login_id'];
				// 	$delete_item = mysql_query("delete from shopping_cart 
				// 		where cart_id = '$cartId[$i]' and customer_id = '$customer_id'", $connection);

				// 	//update show cart amount
				// 	$cart_item = mysql_query("select count(product_id) from `shopping_cart` where customer_id = '$customer_id'", $connection);
				// 	$cart_item_row = mysql_fetch_array($cart_item);
				// 	setcookie('CX_cart_items', $cart_item_row['count(product_id)'], time() + (86400 * 30), "/");
				// 	header('Location: cart.php');			

				// 	$numDeleted += 1;
				// } else {
					// update product quantity
					$update_cart = mysql_query("update shopping_cart
							set cart_quantity = '$newQty'
							where cart_id = '$cartId[$i]'", $connection);
				//}
			}
			
			
		}

		//delete product
		if ($action == 'delete') {
			$delete_cartId = $_GET['cart_id'];
			$customer_id = $_SESSION['CX_login_id'];
			$delete_item = mysql_query("delete from shopping_cart 
				where cart_id = '$delete_cartId' and customer_id = '$customer_id'", $connection);

			//update show cart amount
			// $cart_item = mysql_query("select count(product_id) from `shopping_cart` where customer_id = '$customer_id'", $connection);
			// $cart_item_row = mysql_fetch_array($cart_item);
			// setcookie('CX_cart_items', $cart_item_row['count(product_id)'], time() + (86400 * 30), "/");
			// header('Location: cart.php');	
		}

		if ($action == 'update') {
			$cartId     = $_POST['hidCartId'];
			$productId  = $_POST['hidProductId'];
			$itemQty    = $_POST['txtQty'];
			$selected   = $_POST['selected'];
			$numItem    = count($itemQty);
			$numDeleted = 0;
			$notice     = '';
			$customer_id = $_SESSION['CX_login_id'];

			//reset product selected = 0
			$reset_selected = mysql_query("update shopping_cart set selected = 0 where customer_id = '$customer_id'", $connection);
			if(!empty($_POST['selected'])) {
			    foreach($_POST['selected'] as $check) {
			            $update_selected = mysql_query("update shopping_cart set selected = 1 where cart_id = '$check'", $connection);
			    }
			}
			
			for ($i = 0; $i < $numItem; $i++) {
				$newQty = (int)$itemQty[$i];
				// if ($newQty < 1) {
				// 	// remove this item from shopping cart
				// 	$customer_id = $_SESSION['CX_login_id'];
				// 	$delete_item = mysql_query("delete from shopping_cart 
				// 		where cart_id = '$cartId[$i]' and customer_id = '$customer_id'", $connection);

				// 	//update show cart amount
				// 	$cart_item = mysql_query("select count(product_id) from `shopping_cart` where customer_id = '$customer_id'", $connection);
				// 	$cart_item_row = mysql_fetch_array($cart_item);
				// 	setcookie('CX_cart_items', $cart_item_row['count(product_id)'], time() + (86400 * 30), "/");
				// 	header('Location: cart.php');			

				// 	$numDeleted += 1;
				// } else {
					// update product quantity
					$update_cart = mysql_query("update shopping_cart
							set cart_quantity = '$newQty'
							where cart_id = '$cartId[$i]'", $connection);
				//}
			}
			header( "location: createorder.php" );
			
		}
	}else{

		//set all item selected
	  $update_cart = mysql_query("update shopping_cart set selected = 1 where customer_id = '$user_id'");
	}

	if ($_GET['error_no'] == 1) {
		$error_text = "คุณมีรายการสินค้าที่ต้องการสั่งซื้อมากกว่าที่กำหนดไว้ค่ะ (1 บิลสามารถสั่งสินค้าได้สูงสุด ".$_GET['max_cart']." รายการค่ะ)";
	}

?>
<!DOCTYPE html>
<html>
  <head>
    <?php include 'page_script.php';  ?>
    <style type="text/css">
    	.btn-circle {
		  width: 24px;
		  height: 24px;
		  text-align: center;
		  padding: 6px 0;
		  font-size: 12px;
		  line-height: 1.128571429;
		  border-radius: 12px;
		}
		.cart_search{
				width:400px;
			}
		@media screen and (max-width: 1024px) {
			.cart_search{
				width:240px;
			}
		}
    </style>
  </head>
  <body>
    <?php include 'nav_bar.php';  ?>
	<div class="content manage">
		<?php include 'left_menu.php' ?>
		<div class="menu-content col3">
			<br><table class="content-light center">
				<tr>
				<td class="selected"><i class="material-icons">check_circle</i><br>เลือกสินค้า</td><td>&#10095;</td>
				<td><i class="material-icons">check_circle</i><br>สั่งซื้อสินค้า</td><td>&#10095;</td>
				<td><i class="material-icons">check_circle</i><br>สินค้ารอตรวจสอบ</td><td>&#10095;</td>
				<td><i class="material-icons">check_circle</i><br>สินค้ารอชำระเงิน</td><td>&#10095;</td>
				<td><i class="material-icons">check_circle</i><br>ส่งมอบสินค้า</td>
				</tr>
			</table>
		<div class="content-line"></div>
		<table style="width:100%;">
		<tr>
		<td style="height: 70px;">
			<h1>ตะกร้าสินค้า</h1>
		</td>
		<td style="text-align:right;">
		<form method="post" action="cart.php">
			<label>ค้นหาสินค้า : </label>
			<input class="input cart_search" type="text" name="product_name" placeholder="ค้นหารายการสินค้าในตะกร้า">
			<button style="border-width:0px;" type="submit" value="Submit"><i class="material-icons">search</i></button>
			<a href="cart.php" class="button" style="margin-right:0px;" >แสดงทั้งหมด</a>
		</form>
		</td>
		</tr>
		</table>
		
			
<?php

	//ดึงข้อมูล rate & shipping rate
	$select_rate = mysql_query("select * from website_rate order by starting_date desc limit 1");
	$select_rate_row = mysql_fetch_array($select_rate);
	$rate = $select_rate_row['rate_cny'];
	$shipping_rate = $select_rate_row['shipping_rate_cny'];

	echo "<input type='hidden' id='current_rate' value='".number_format($rate,4)."'>";

	$query_status = "";
	if (isset($_POST['product_name'])) {
		$query_status .= " and p.product_name like '%".$_POST['product_name']."%' ";
	}

	//start table
	$select_shop_group = mysql_query("select p.shop_name, p.source
										from product p, shopping_cart s
										where customer_id = '$user_id' 
										and s.product_id=p.product_id ".$query_status." 
										group by p.shop_name", $connection);

	if (mysql_num_rows($select_shop_group) > 0) {
		echo "<form action='cart.php?action=update&cart_id=' method='post' name='formCart' id='formCart'>";
		echo "<table class='content-grid' id='cartTable'>";
	        // our table heading
	        echo "<thead>
	        	  <tr class='bg-primary'>";
	        	echo "<th colspan='3'>สินค้า 
 						<input type='checkbox' onClick='toggle(this)' name='checkall' checked /> เลือกทั้งหมด<br/>
	        	      </th>";
	            echo "<th >ราคา (หยวน)</th>";
	            echo "<th style='text-align:left; padding-left:1em;'>จำนวน</th>";
	            echo "<th style='text-align:right; padding-right:2em;'>ราคารวม (หยวน)</th>";
	            echo "<th class='text-center'>ตัวเลือก</th>";
	        echo "</tr>
	              </thead>
				  <tbody id='myTable'>";

	    $sum_price = 0;
	    $sum_item = 0;
	    $row_count = 0;
		while ($shop_row = mysql_fetch_array($select_shop_group)) {
			$shop_name = $shop_row['shop_name'];

			$select_first_item = mysql_query("select * from shopping_cart s, product p where customer_id = '$user_id' 
			and s.product_id=p.product_id and shop_name = '$shop_name' ".$query_status." ", $connection);
			$select_first_item_row =  mysql_fetch_array($select_first_item);
			
			$item_header = "	<tr><td colspan='7' class='bg-info'>
						<strong> ชื่อร้าน: ".$shop_name.", </strong>
						<strong> เว็บไซต์: ".$shop_row['source']." </strong> 
						<a href='fav_shop.php?action=add&shop_name=".$shop_name."&shop_url=".$select_first_item_row['product_url']."' target='_blank' title='เพิ่มร้านค้าที่ชื่นชอบ'>
							<button type='button' class='btn btn-danger btn-circle' ><i class='glyphicon glyphicon-heart'></i></button>
						</a>
					</td></tr>";
			echo $item_header;

			//count header row
			$row_count += 1;

			//if row header is last cell of page -> show again in next page !!
			if ($row_count%15 == 0) {
				echo $item_header;
			}

			$select_item = mysql_query("select * from shopping_cart s, product p where customer_id = '$user_id' 
			and s.product_id=p.product_id and shop_name = '$shop_name' ".$query_status." ", $connection);

			//count item row
			$row_count += mysql_num_rows($select_item);

			if(mysql_num_rows($select_item) > 0){ 
			    
			      while($row = mysql_fetch_array($select_item)) {
			 			if($row['selected']=='1'){ $sum_price += $row['product_price']*$row['cart_quantity']; }
			 			$sum_item++;
			            //creating new table row per record
			            echo "<tr>";
			            	echo "<td class='text-center'><input type='checkbox' id='selected' name='selected[]' value='".$row['cart_id']."'"; if($row['selected']=='1'){ echo "checked";} echo "></td>";
			                echo "<td>";
			                	echo "<a href='".$row['product_img']."' data-lightbox='preview_img'><img style='min-width:50px;height:50px;' class='img-thumbnail' src='".$row['product_img']."''></a>";
			                echo "</td>";
			                echo "<td style='min-width:300px;'>";
			                	echo "<a href='".$row['product_url']."' target='_blank'>".$row['product_name']."</a><br />";
			                	echo "ขนาด:".$row['product_size']."<br />";
			                	echo "สี:".$row['product_color']."";
			                echo "</td>";
			                echo "<td style='text-align:right; padding-right:1em;'>";
			                	echo "<span name='y_price[]'>".number_format($row['product_price'],2)."</span> ¥";
			                echo "</td>";
			                echo "<td class='text-left' style='padding-left:1em;'>";
			                	echo "	<input style='background-color:silver;' name='txtQty[]' id='txtQty[]' type='text' size='3' value='".$row['cart_quantity']."' data-cart-id='".$row['cart_id']."' onkeypress='return isNumber(event)'>
			                			<input name='hidCartId[]' type='hidden' value='".$row['cart_id']."'>
			                			<input name='hidProductId[]' type='hidden' value='".$row['product_id']."'>";
			                echo "</td>";
			                echo "<td style='text-align:right; padding-right:2em;'>";
			                	echo "<span name='item_price[]' data-price='".$row['product_price']*$row['cart_quantity']."' >".number_format($row['product_price']*$row['cart_quantity'],2)."</span> ¥";
			                echo "</td>";
			                echo "<td>";
			                    echo "<a onClick='deleteItem(".$row['cart_id'].");' class='delete' title='ลบสินค้า'>";
			                        echo "✖";
			                    echo "</a>";
			                echo "</td>";
			            echo "</tr>";
			        }
			            
			}
		}
		echo "</tbody>
				<tr>
							<th colspan='3'><p>ทั้งหมด : $sum_item | <input type='checkbox' name='checkall' onClick='toggle(this)' checked /> เลือกทั้งหมด</p> | <a style='color:white;' onclick='removeChecked()'>ลบ</a></th>
        			<th colspan='2' style='text-align:right;'>ราคาทั้งหมด</th>
        			<th style='text-align:right;  padding-right:2em;'><span id='total_price'>". number_format($sum_price,2)."</span></th>
        			<th>หยวน</th>
        	  	</tr>";
		echo "
			  </table><br />
			  จำนวนที่เลือกไว้ : <span id='select_amount'></span>
			  <div class='col-md-12 text-center'>
      				<ul class='pagination pagination-lg pager' id='myPager'></ul>
     			</div>";
		echo "<hr><hr><hr>";
        //echo "<input class='btn btn-info' type='submit' value='Update' name='updatedata'>";
        //echo "(กด update ทุกครั้งเมื่อเปลี่ยนแปลงข้อมูล)<br /><br /> ";
	    echo "<center><button type='button' name='submitdata' onclick='checkpricezero();' >
	    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<i class='material-icons'>shopping_cart</i><h3>สั่งซื้อ</h3>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</button></center>";

	  $select_max_cart = mysql_query("select SITE_MAX_CART from website_config");
		$select_max_cart_row = mysql_fetch_array($select_max_cart);
		$max_cart = $select_max_cart_row['SITE_MAX_CART'];
	    echo "</form><br /><hr />
	    	<div class='col-md-9 text-left'>
	    		<p><i class='material-icons'>info</i><span style='color:red;'> 1 บิล สามารถเพิ่มรายการสินค้าได้สูงสุด ".$max_cart." รายการเท่านั้น</span></p>
	    	</div>
	    	<div class='col-md-3 text-right'>
				<a href='#' onclick='gotoTop()'>กลับไปด้านบน</a>
			</div>
	    	";

	}else
	{
		echo "<div class='col-md-12'><div class='alert alert-info' role='alert'><p>ยังไม่มีรายการสินค้าในตะกร้า</p></div></div>";
		$emthycart = 1;
	}  

?>
	</div>
	</div>

	<div class="modal" id="mdl_agreement" role="dialog">
		<div class="modal-dialog" style="overflow-y: initial !important">
			<div class="modal-content">
				<div class="modal-header" style="padding:9px 15px;
			    border-bottom:1px solid #eee;
			    background-color: #ff5431;
			    -webkit-border-top-left-radius: 5px;
			    -webkit-border-top-right-radius: 5px;
			    -moz-border-radius-topleft: 5px;
			    -moz-border-radius-topright: 5px;
			     border-top-left-radius: 5px;
			     border-top-right-radius: 5px;">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" style="color:white">ข้อตกลงและเงื่อนไขการสั่งซื้อ</h4>
				</div>
				<div class="modal-body" style="height:300px; overflow-y: auto;">
					1.ORDER2EASY ทำหน้าที่เป็นผู้ประสานงานการสั่งซื้อสินค้าให้กับลูกค้าเท่านั้น ดังนั้นเราจะไม่สามารถรับผิดชอบความเสียหายของสินค้าในกรณีที่เป็นความเสียหายอันเกิดจากร้านค้าจีนหรือผู้จัดจำหน่ายสินค้าได้ อย่างไรก็ตามเรายินดีช่วยประสานงานกับร้านค้าเพื่อทวงสิทธิ์และผลประโยชน์ของท่านให้อย่างเต็มความสามารถ<br /><br />
					2. เราไม่มีนโยบายสนับสนุนสินค้าละเมิดลิขสิทธิ์ หากท่านสั่งซื้อสินค้าที่เข้าข่ายละเมิดลิขสิทธิ์ เราจะไม่มีการรับผิดชอบใดๆ เนื่องจากท่านเป็นผู้ตัดสินใจเลือกซื้อสินค้าด้วยตนเอง และเราเป็นเพียงผู้ช่วยในการสั่งซื้อเท่านั้น จึงไม่สามารถตรวจสอบสินค้านั้นๆได้<br /><br />
					3. หลังจากที่ลูกค้าเปิดคำสั่งซื้อ ทางบริษัทฯจะทำการสรุปยอดสั่งซื้อและส่งกลับไปยังท่าน โดยราคาที่เสนอกลับไปนั้นมีผลเพียง 7 วัน หากท่านไม่ชำระเงินภายในระยะเวลาที่กำหนด คำสั่งซื้อจะถูกยกเลิกโดยอัตโนมัติ<br /><br />
					4. หากกรณีที่สินค้าเกิดความเสียหายอันเกิดจากการขนส่งของบริษัทฯ เช่น สินค้าเสียหายหรือสูญหายขณะขนส่ง กรณีเช่นนี้บริษัทยินดีรับผิดชอบไม่เกิน2,000บาท ต่อหนึ่งออร์เดอร์ แต่หากเกิดจากความผิดพลาดของทางร้านค้าจีนหรือผู้จัดจำหน่ายสินค้า เราจะทำหน้าที่เป็นผู้ประสานงานให้ แต่ไม่สามารถรับผิดชอบความเสียหาย รวมถึงค่าใช้จ่ายต่างๆที่เกิดขึ้นได้<br /><br />
					5. หากสินค้าที่ท่านได้รับมีความผิดปกติ จะต้องแจ้งรายละเอียดกับเราภายใน 7 วันนับจากวันที่ได้รับสินค้า พร้อมส่งหลักฐานภาพถ่ายความเสียหายนั้น เพื่อที่เราจะได้ประสานงานกับทางร้านค้าต่อไป หากเลยเวลาที่กำหนด ทางเราจะไม่ขอรับผิดชอบทุกกรณี<br /><br />
					5.1 การตรวจสอบสินค้า(QC) จะตรวจเพียงลักษณะภายนอกและจำนวนสินค้าเท่านั้น<br /><br />
					5.2 สินค้าประเภทอิเล็กทรอนิกส์และเครื่องใช้ไฟฟ้า เราจะตรวจสอบเฉพาะสภาพภายนอกเท่านั้น เนื่องจากเป็นสินค้าที่ต้องอาศัยความชำนาญทางด้านเทคนิค เราจึงไม่สามารถตรวจสอบคุณภาพและการใช้งานของสินค้าได้<br /><br />
					5.3 สินค้าประเภทเครื่องแก้ว เราจะไม่รับเคลมสินค้าที่เสียหายทุกกรณี หากท่านต้องการสั่งซื้อสินค้าประเภทนี้ ควรตีลังไม้ เพื่อลดความเสี่ยงที่จะเกิดความเสียหายต่อสินค้านั้นๆ<br /><br />
					5.4 กล่องบรรจุสินค้าที่ลูกค้าได้รับอาจมีสภาพไม่สมบูรณ์ไปบ้าง เนื่องจากเป็นการขนส่งระยะทางไกล แต่หากสินค้าที่บรรจุเกิดความเสียหายจากการขนส่งจนไม่สามารถใช้งานได้ ทางเรายินดีรับผิดชอบในความเสียหายของสินค้าที่เกิดขึ้น<br /><br />
					6. ระยะเวลาการขนส่งสินค้าเป็นไปตามมาตรฐานการขนส่งของบริษัทฯ อย่างไรก็ตามระยะเวลาการขนส่งจะขึ้นอยู่กับร้านค้าจีนและขนส่งเอกชนต่างๆด้วย ว่าใช้ระยะเวลา ในการผลิตและจัดส่งเท่าใด แต่เราจะควบคุมเวลาการจัดส่งสินค้าให้ถึงมือท่านโดยเร็วที่สุด<br /><br />
					7. รูปภาพสินค้าในเว็บไซต์ร้านค้าที่เห็นนั้น อาจแตกต่างจากสินค้าจริงที่ท่านได้รับไปบ้าง เนื่องจากร้านค้าจีนส่วนใหญ่จะมีการตกแต่งภาพสินค้าให้สวยงาม ทำให้ในบางกรณีสินค้าที่ได้รับจริงอาจแตกต่างหรือผิดเพี้ยนไปบ้าง<br /><br />
					8.หากท่านสั่งซื้อสินค้ากับทางบริษัทฯ จะถือว่าท่านยินยอมและยอมรับในข้อตกลงต่างๆ ข้างต้นในทุกกรณี<br /><br />
				</div>
				<div class="modal-footer">
					<div class="col-md-6 text-left">
						<div class="checkbox">
					    <label>
					      <input type="checkbox" name="rad_agree" id="rad_agree"> <b>ยอมรับเงื่อนไขการสั่งซื้อ</b>
					    </label>
					  </div>
					</div>
					<div class="col-md-6 text-right">
						<button type="button" class="btn" style="background-color:#ff5431;color:#fff;" id="addtocart_button" onclick="checkagree();">ตกลง
						</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
					</div>
				</div>
			</div>
		</div>
	</div>

<?php if($emthycart != 1){ ?>
<script type="text/javascript">
	function gotoTop(){
		$('html, body').animate({ scrollTop: 0 }, 'slow');
	}
	
	function checkpricezero(){

		var price = document.getElementById('total_price').innerHTML;
		var current_rate = document.getElementById('current_rate').value;
		// if (price != "0.00") {
			
		// }else{
		// 	alert("กรุณาเลือกรายการสินค้าอย่างน้อย 1 รายการ");
		// 	return false;
		// }
		// return false;

		var count_box = 0;
    checkboxes = document.getElementsByName('selected[]');
    var qty = document.getElementsByName('txtQty[]');
		for(var i=0, n=checkboxes.length;i<n;i++) {
		    if(checkboxes[i].checked){
		    	count_box++;
		    	if (qty[i].value == "" || qty[i].value == 0) {
		    		sweetAlert("เกิดข้อผิดพลาด", "จำนวนสินค้าที่สั่งต้องไม่เป็นค่าว่างหรือ 0 ค่ะ", "error");
		    		return false;
		    	}
		    }
		}

		if (count_box == 0) {
			sweetAlert("เกิดข้อผิดพลาด", "กรุณาสั่งซื้อสินค้าอย่างน้อย 1 รายการค่ะ", "error");
			return false;
		}else if (count_box > <?php echo $max_cart; ?>) {
			sweetAlert("คุณมีรายการสินค้าที่ต้องการสั่งซื้อมากกว่าที่กำหนดไว้ค่ะ", "(1 บิลสามารถสั่งสินค้าได้สูงสุด <?php echo $max_cart; ?> รายการค่ะ)", "error");
			return false;
		}

		if (price == 0 ){
			sweetAlert("เกิดข้อผิดพลาด", "ท่านยังไม่มีรายการที่สั่งซื้อในขณะนี้", "error");
			return false;

		}else if (price*current_rate < <?php echo $site_lowest_price; ?> ){
			var text = "ท่านจำเป็นต้องมียอดการสั่งซื้อมากกว่า <?php echo $site_lowest_price; ?> บาทค่ะ, ยอดการสั่งซื้อปัจจุบันของคุณคือ "+((price*current_rate).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,'))+" บาทค่ะ ";
			sweetAlert("เกิดข้อผิดพลาด", text, "error");
			return false;
		}else{
			$('#mdl_agreement').modal('show');
			return false;
		}

	}

	function checkagree(){
		var radio_by_self = document.getElementById("rad_agree");
		if (radio_by_self.checked) {
			$('form#formCart').submit();
		}else{
			alert("กรุณากดยอมรับเงื่อนไขการสั่งซื้อก่อนค่ะ!");
		}
	}

	function toggle(source) {
		checkboxes = document.getElementsByName('selected[]');
		for(var i=0, n=checkboxes.length;i<n;i++) {
		    checkboxes[i].checked = source.checked;
		}

		checkall = document.getElementsByName('checkall');
		for(var i=0, n=checkall.length;i<n;i++) {
		    checkall[i].checked = source.checked;
		}

		var count_box = 0;
		var sum_price = 0;
	       	checkboxes = document.getElementsByName('selected[]');
	       	//item_price = document.getElementsByName('item_price[]');
	       	var array = [];
			$('span[name="item_price[]"]').each(function () {
			    array.push(this.getAttribute("data-price"));
			});
			//alert(array);

			for(var i=0, n=checkboxes.length;i<n;i++) {
			    if(checkboxes[i].checked){
			    	count_box++;
			    	//var x = item_price[i].getAttribute("data-price");
			    	//alert(item_price[0].innerHTML);
			    	sum_price += parseFloat(array[i]);
			    }
			}
		document.getElementById('select_amount').innerHTML = count_box;
		document.getElementById('total_price').innerHTML = sum_price.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');

		// var count_box = 0;
	 //       	checkboxes = document.getElementsByName('selected[]');
		// 	for(var i=0, n=checkboxes.length;i<n;i++) {
		// 	    if(checkboxes[i].checked){
		// 	    	count_box++;
		// 	    }
		// 	}
		// document.getElementById('select_amount').innerHTML = count_box;
	}

	function removeChecked() {
		checkboxes = document.getElementsByName('selected[]');
		for(var i=0, n=checkboxes.length;i<n;i++) {
		    checkboxes[i].checked = false;
		}

		checkall = document.getElementsByName('checkall');
		for(var i=0, n=checkall.length;i<n;i++) {
		    checkall[i].checked = false;
		}

		var count_box = 0;
		var sum_price = 0;
	       	checkboxes = document.getElementsByName('selected[]');
	       	//item_price = document.getElementsByName('item_price[]');
	       	var array = [];
			$('span[name="item_price[]"]').each(function () {
			    array.push(this.getAttribute("data-price"));
			});
			//alert(array);

			for(var i=0, n=checkboxes.length;i<n;i++) {
			    if(checkboxes[i].checked){
			    	count_box++;
			    	//var x = item_price[i].getAttribute("data-price");
			    	//alert(item_price[0].innerHTML);
			    	sum_price += parseFloat(array[i]);
			    }
			}
		document.getElementById('select_amount').innerHTML = count_box;
		document.getElementById('total_price').innerHTML = sum_price.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');

		// var count_box = 0;
	 //       	checkboxes = document.getElementsByName('selected[]');
		// 	for(var i=0, n=checkboxes.length;i<n;i++) {
		// 	    if(checkboxes[i].checked){
		// 	    	count_box++;
		// 	    }
		// 	}
		// document.getElementById('select_amount').innerHTML = count_box;
	}

	function isNumber(evt) {
		//Enable arrow for firefox.
		if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
		    if (evt.keyCode == 8 || evt.keyCode == 46 || evt.keyCode == 37 || evt.keyCode == 39) {
			    return true;
			}
		}

	    evt = (evt) ? evt : window.event;
	    var charCode = (evt.which) ? evt.which : evt.keyCode;

	    if (charCode > 31 && (charCode < 48 || charCode > 57 )) {
        return false;
	    }
	    return true;
	}

	$(document).ready(function(){

	  	$('#myTable').pageMe({pagerSelector:'#myPager',showPrevNext:true,hidePageNumbers:false,perPage:15});
	    
	});

	$(document).ready(function() {
			var count_box = 0;
			var sum_price = 0;
	       	checkboxes = document.getElementsByName('selected[]');
	       	var array = [];
			$('span[name="item_price[]"]').each(function () {
			    array.push(this.getAttribute("data-price"));
			});
			for(var i=0, n=checkboxes.length;i<n;i++) {
			    if(checkboxes[i].checked){
			    	count_box++;
			    	sum_price += parseFloat(array[i]);
			    }
			}
			document.getElementById('select_amount').innerHTML = count_box;
			
			document.getElementById('total_price').innerHTML = sum_price.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');

	});

	$(document).ready(function() {
	  $('input:checkbox').change(function() {
			var count_box = 0;
			var sum_price = 0;
	       	checkboxes = document.getElementsByName('selected[]');

	       	var array = [];
			$('span[name="item_price[]"]').each(function () {
			    array.push(this.getAttribute("data-price"));
			});
	       	//var item_price = document.getElementsByName('item_price[]');
	       	//if (item_price == null) { alert("null")};
			for(var i=0, n=checkboxes.length;i<n;i++) {
			    if(checkboxes[i].checked){
			    	count_box++;
			    	sum_price += parseFloat(array[i]);
			    }
			}
			document.getElementById('select_amount').innerHTML = count_box;
			document.getElementById('total_price').innerHTML = sum_price.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');

			
			// for(var i=0, n=item_price.length;i<n;i++) {
			//     sum_price += Number(item_price[i].innerHTML);
			// }
			// sum_price = 100;
			//document.getElementById('total_price').innerHTML = sum_price;
	  });
	});

	$(document).ready(function() {

		$('[name="txtQty[]"]').keyup(function(event) {

			var clicked = $(this);

	    qty = document.getElementsByName('txtQty[]');
	    y_price = document.getElementsByName('y_price[]');
	    item_price = document.getElementsByName('item_price[]');
	    for(var i=0, n=qty.length;i<n;i++) {
			    var price = parseFloat(y_price[i].innerHTML)*parseInt(qty[i].value);

			    if (!price) {
			    	item_price[i].innerHTML = "0.00";
			    	item_price[i].setAttribute("data-price", "0");
			    	// qty[i].value = "0";
			    }else{
			    	item_price[i].setAttribute("data-price", price);
			    	item_price[i].innerHTML = price.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');
			    }

			    if(clicked.attr("data-cart-id") == qty[i].getAttribute("data-cart-id")){
			    	updateCartAmount(clicked.attr("data-cart-id"),parseInt(qty[i].value));
			    }
			    
			}

	    //recalculate price
		  var count_box = 0;
			var sum_price = 0;
     	checkboxes = document.getElementsByName('selected[]');
     	var array = [];
			$('span[name="item_price[]"]').each(function () {
			    array.push(this.getAttribute("data-price"));
			});
			for(var i=0, n=checkboxes.length;i<n;i++) {
			    if(checkboxes[i].checked){
			    	count_box++;
			    	sum_price += parseFloat(array[i]);
			    }
			}
			document.getElementById('select_amount').innerHTML = count_box;
			document.getElementById('total_price').innerHTML = sum_price.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');

		});
	});

	function deleteItem(itemID){

		var ask = confirm('คุณต้องการลบสินค้าชิ้นนี้หรือไม่');
	    if (ask){
	        window.location.href = 'cart.php?action=delete&cart_id='+itemID;
	    }

	}

	function updateCartAmount(cart_id,amount){

		var req;
		if (window.XMLHttpRequest) {
			req = new XMLHttpRequest();
		}
		else if (window.ActiveXObject) {
			req = new ActiveXObject("Microsoft.XMLHTTP"); 
		}
		else{
			alert("Browser error");
			return false;
		}
		req.onreadystatechange = function()
		{
			if (req.readyState == 4) {
				// alert(req.responseText);
				// var resultarea = document.getElementById('show_search_result');
				// resultarea.innerHTML = req.responseText;
			}
			else
			{
				// var resultarea = document.getElementById('show_search_result');
				// resultarea.innerHTML = "<center><img src=progress_bar.gif><br /><br /><small>การรวบรวมข้อมูลอาจใช้เวลาสักระยะ หากข้อมูลสินค้ามีจำนวนมาก</small></center>";
			}
		}

		req.open("GET", "update_cart_amount.php?cart_id="+cart_id+"&amount="+amount, true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
		req.send(null); 

	}

</script>
<?php } ?>

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


