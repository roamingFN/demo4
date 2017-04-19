<?php
	include 'connect.php';
	include 'session.php';

	$query_site_url = mysql_query("select * from website_config");
	$site_url_row = mysql_fetch_array($query_site_url);
	$site_lowest_price = $site_url_row['SITE_LOWEST_PRICE'];

	//check shopping cart < max cart
	$max_cart = $site_url_row['SITE_MAX_CART'];
	$select_selected_item = mysql_query("select * from shopping_cart s, product p where customer_id = '$user_id' 
				and s.product_id=p.product_id and selected = '1'", $connection);
	if (mysql_num_rows($select_selected_item) > $max_cart) {
		$location = "Location: cart.php?error_no=1&max_cart=".$max_cart;
		header($location);
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
			<br><table class="content-light center">
				<tr>
				<td class="selected"><i class="material-icons">check_circle</i><br>เลือกสินค้า</td><td>&#10095;</td>
				<td class="selected"><i class="material-icons">check_circle</i><br>สั่งซื้อสินค้า</td><td>&#10095;</td>
				<td><i class="material-icons">check_circle</i><br>สินค้ารอตรวจสอบ</td><td>&#10095;</td>
				<td><i class="material-icons">check_circle</i><br>สินค้ารอชำระเงิน</td><td>&#10095;</td>
				<td><i class="material-icons">check_circle</i><br>ส่งมอบสินค้า</td>
				</tr>
			</table>
		<h2>รายการสั่งซื้อสินค้า</h2>
<?php
	//ดึงข้อมูล rate & shipping rate
	$select_rate = mysql_query("select * from website_rate order by starting_date desc limit 1");
	$select_rate_row = mysql_fetch_array($select_rate);
	$rate_date = $select_rate_row['starting_date'];
	$rate = $select_rate_row['rate_cny'];
	$shipping_rate = $select_rate_row['shipping_rate_cny'];

	//start table
	$select_shop_group = mysql_query("select p.shop_name, p.source
										from product p, shopping_cart s
										where customer_id = '$user_id' 
										and s.product_id = p.product_id
										and s.selected = '1' 
										group by p.shop_name", $connection);

	if (mysql_num_rows($select_shop_group) > 0) {
		echo "<form action='order.php?action=submit_order' method='post' name='formCart' id='formCart'>";
		echo "<table class='content-grid'>";
					// our table heading
					echo "<tr class='bg-primary'>";
							echo "<th class='text-center'>ลำดับ</th>";
							echo "<th colspan='2'>สินค้า</th>";
							echo "<th style='text-align:right; padding-right:2em;'>ราคา (หยวน)</th>";
							echo "<th style='text-align:left; padding-left:1em;'>จำนวน</th>";
							echo "<th style='text-align:right; padding-right:2em;'>ราคาทั้งหมด (หยวน)</th>";
					echo "</tr>";

			$sum_price = 0;
			$sum_product = 0;
			$sum_product_amount = 0;
			$product_row = 1;

			if (mysql_num_rows($select_shop_group)>0) {
			while ($shop_row = mysql_fetch_array($select_shop_group)) {
				$shop_name = $shop_row['shop_name'];
				
				echo "	<tr><td colspan='6' class='bg-info'>
							<strong> ชื่อร้าน: ".$shop_name.", </strong>
							<strong> เว็บไซต์: ".$shop_row['source']." </strong>
						</td></tr>";

				$select_item = mysql_query("select * from shopping_cart s, product p where customer_id = '$user_id' 
				and s.product_id=p.product_id and shop_name = '$shop_name' and selected = '1'", $connection);

				if(mysql_num_rows($select_item) > 0){ 
					$sum_product += mysql_num_rows($select_item);

							while($row = mysql_fetch_array($select_item)) {
						$sum_price += $row['product_price']*$row['cart_quantity'];
						$sum_product_amount += $row['cart_quantity'];
									//creating new table row per record
									echo "<tr>";
										echo "<td class='text-center'>".$product_row."</td>";
											echo "<td>";
												echo "<img style='width:50px;' class='img-thumbnail' src='".$row['product_img']."''>";
											echo "</td>";
											echo "<td>";
												echo "<a href='".$row['product_url']."' target='_blank'>".$row['product_name']."</a><br />";
												echo "ขนาด:".$row['product_size']."<br />";
												echo "สี:".$row['product_color']."";
											echo "</td>";
											echo "<td style='text-align:right; padding-right:2em;'>";
												echo "<span name='y_price[]'>".number_format($row['product_price'],2)."</span> ¥";
											echo "</td>";
											echo "<td style='text-align:left; padding-left:1em;'>";
												echo "	<input style='background-color:silver;' name='txtQty[]' id='txtQty[]' type='text' size='3' value='".$row['cart_quantity']."' data-cart-id='".$row['cart_id']."' onkeypress='return isNumber(event)'>
														<input name='hidCartId[]' type='hidden' value='".$row['cart_id']."'>
														<input name='hidProductId[]' type='hidden' value='".$row['product_id']."'>";
											echo "</td>";
											echo "<td class='text-right' style='padding-right:2em;'>";
												echo "<span name='item_price[]' data-price='".$row['product_price']*$row['cart_quantity']."' >".number_format($row['product_price']*$row['cart_quantity'],2)."</span> ¥";
											echo "</td>";
									echo "</tr>";
									$product_row++;
							}
				}
			}
		}
							$query_customer = mysql_query("select * from customer where customer_id = '$user_id'");
							$query_customer_row = mysql_fetch_array($query_customer);
							$customer_current_amount = $query_customer_row['current_amount'];

							echo "
									<tr>
										<td colspan='4' class='bg-info text-right' style='padding-right:2em;'><b>ยอดรวม</b><b> &nbsp&nbsp&nbsp".$sum_product." &nbsp&nbsp&nbspรายการ</b></td>
										<td class='bg-info text-center'><b id='total_qty'>".$sum_product_amount."</b></td>
										<td class='bg-info'></td>
						</tr>
									<tr>
						</tr>
									<tr>
									<td colspan='5' class='text-right'><b>ราคาสินค้ารวม (หยวน)</b></td>
									<td class='text-right' style='padding-right:2em;'><b id='total_price'>". number_format($sum_price, 2)."</b></td>
									</tr>	
									<tr>
									<td colspan='5' class='text-right'><b>Rate @ ".date("Y-m-d G:i")." (บาท)</b></td>
									<td class='text-right' style='padding-right:2em;'><b id='rate_cny'> ". number_format($rate, 2)."</b></td>
									</tr>	
									<tr>
									<td colspan='5' class='text-right'><b>ราคาสินค้ารวม (บาท)</b></td>
									<td class='text-right' style='padding-right:2em;'><b id='total_price_bath'>". number_format($sum_price*$rate,2)."</b></td>
									</tr>
									<tr>
									<td colspan='5' class='text-right' style='color:#3949ab;'><b>ยอดเงินคงเหลือ (บาท)</b></td>
									<td class='text-right' style='padding-right:2em;color:#3949ab;'><b>". number_format($customer_current_amount,2)."</b></td>
									</tr>";
	 
			echo 	"</table>";
			echo 	"<input type='hidden' name='order_amount' id='order_amount' value='".$sum_product_amount."'>";
			echo 	"<h3>ข้อมูลการขนส่ง</h3>
						<div class='row col-md-12'>
							<div class='graybox'>
								<ul>
									<li>ขั้นตอนการชำระเงิน ลูกค้าจะต้องทำการชำระเงิน 2รอบ คือจ่ายค่าสินค้าและค่าขนส่งในจีน 									
 ส่วนค่าขนส่งจีน-ไทย และค่าขนส่งในไทย จะแจ้งเมื่อสินค้าเข้าโกดัง</li>
									<li> เพิ่มความรวดเร็วของสินค้า บริษัทส่งสินค้าจากจีนมาไทยด้วยวิธีรถด่วน เท่านั้น</li>
								</ul>
							</div>
							<h3>ขนส่งในไทยโดยใช้วิธี</h3>
							<div class='graybox'>
								<div class='form-group'>
									<div class='radio'>
									<label><input type='radio' name='opt_send' value='by_self' id='radio_by_self' >มารับเอง</label>
							</div>
						</div>
						<div class='form-group'>
							<div class='radio'>
									<label><input type='radio' name='opt_send' value='by_company' id='radio_by_company' checked>
									เลือกบริษัทขนส่งในไทย</label>
							</div>

							<div id='opt_send_div' style='margin-left:50px;'>";

							$select_transport = mysql_query("select * from website_transport");
							$checked = "checked";
							while ($row = mysql_fetch_array($select_transport)) {
								echo "
								<div class='radio'>
										<label><input type='radio' name='opt_company' value='".$row['transport_id']."' ".$checked." >".$row['transport_th_name']." ".$row['transport_th_desc']."</label>
								</div>
								";
								$checked = "";
							}

							echo "
								<div class='radio'>
									<label><input type='radio' name='opt_company' value='other' >อื่นๆ (ในกรณีที่ชิ้นใหญ่ หรือน้ำหนักเกิน 20 Kg/ชิ้น)</label>
									<input class='form-control' type='text' name='opt_company_other' id='opt_company_other' placeholder='โปรดระบุ' >
								</div>
							</div>
						</div>
					</div>
					<h3 id='address_head'>เลือกที่อยู่ส่งของ</h3>
					<div class='graybox' id='address_section'>
						<div class='form-group'>
							
							";
							$flag = 0;
							$exist_address = mysql_query("select * from customer_address where customer_id = '$user_id'");
							if (mysql_num_rows($exist_address) > 0) {
								echo "
								<div class='radio'>
									<label><input type='radio' name='opt_address' value='exist_address' checked>ที่อยู่ที่บันทึกไว้</label>
								</div>";
								
								while ($row = mysql_fetch_array($exist_address)) {
								echo "
								<div name='exist_address_div'  style='margin-left:50px;' class='well well-sm'>
								<input type='radio' name='exist_address_id' value='".$row['address_id']."' ";
								if($flag == 0){$flag = 1; echo "checked";}
								echo "
								> &nbsp&nbsp<strong>".$row['address_name']."</strong>
								<div class='box'>";
								echo $row['line_1']."<br />".$row['city'].", ".$row['country']."<br />".$row['zipcode']."<br />Tel. ".$row['phone']; 
							
								echo " 
									</div>
								</div>
								";
								}
								$newAddressChecked = "";
							}else{
								$newAddressChecked = "checked";
							}

						$query_site_url = mysql_query("select * from website_config");
						$site_url_row = mysql_fetch_array($query_site_url);
						$site_url = $site_url_row['SITE_URL'];

						echo "<input type='hidden' id='address_count' value='".mysql_num_rows($exist_address) ."'
						</div>
						<div class='form-group'>
							<div class='radio'>
									<label><input type='radio' name='opt_address' value='new_address' id='create_new_address' ".$newAddressChecked.">ที่อยู่ใหม่</label>
							</div>
						</div>
						<div class='row' id='new_address_div' style='margin-left:50px;'>
							<div class='form-horizontal'>
								<div class='form-group'>
									<div class='col-md-3'>
										<label>ชื่อผู้รับ </label>
									</div>
									<div class='col-md-8'>
										<input type='text' class='form-control' name='address_name' id='address_name'>
									</div>
								</div>";

						include_once 'inc/php/DB.php';

						$database = new DB();
						$result =  $database->query("SELECT * FROM tbl_province")->findAll();
						 
						// ตรวจสอบ
						if(!empty($result)){
								// พบข้อมูล
							echo '<div class="form-group">
											<div class="col-md-3">
												<label>จังหวัด </label>
											</div>
											<div class="col-md-8">
												<select id="province" name="province" class="form-control" >
													<option value=""> --- เลือกจังหวัด --- </option>';
							foreach ($result as $province) {
								echo 		 '<option value="' . $province->PROVINCE_ID . '">' . $province->PROVINCE_NAME . '</option>';
							}
									echo '</select>
											</div>
										</div>';
						}
						 
						// อำเภอ
						echo '<div class="form-group">
										<div class="col-md-3">
											<label>อำเภอ </label>
										</div>';
						echo '	<div class="col-md-8">
											<select id="amphoe" name="amphoe" class="form-control">';
						echo '			<option value=""> --- กรุณาเลือกจังหวัด (ก่อน) --- </option>';
						echo '		</select>
										</div>
									</div>';
						 
						 
						// ตำบล
						echo '<div class="form-group">
										<div class="col-md-3">
											<label>ตำบล </label>
										</div>';
						echo '	<div class="col-md-8">
											<select id="district" name="district" class="form-control">>';
						echo '			<option value=""> --- กรุณาเลือกอำเภอ (ก่อน) --- </option>';
						echo '		</select>
										</div>
									</div>';
															 
						echo "<div class='form-group'>
									<div class='col-md-3'>
										<label>รายละเอียดที่อยู่</label>
									</div>
									<div class='col-md-8'>
										<input type='text' class='form-control' name='address_line1' id='address_line1'
											placeholder='รายละเอียดเพิ่มเติม (บ้านเลขที่,ซอย)'>
									</div>
								</div>
								<div class='form-group'>
									<div class='col-md-3'>
										<label>ประเทศ </label>
									</div>
									<div class='col-md-8'>
										<input type='text' class='form-control' name='address_country' id='address_country' 
										 value='ไทย' disabled >
									</div>
								</div>
								<div class='form-group'>
									<div class='col-md-3'>
										<label>รหัสไปรษณีย์ </label>
									</div>
									<div class='col-md-8'>
										<input type='text' class='form-control' name='address_zipcode' id='address_zipcode'>
									</div>
								</div>
								<div class='form-group'>
									<div class='col-md-3'>
										<label>โทรศัพท์ </label>
									</div>
									<div class='col-md-8'>
										<input type='text' class='form-control' name='address_tel' id='address_tel'>
									</div>
								</div>
							</div>
						</div>
					</div>
					<h3>หมายเหตุ</h3>
					<div class='form-group'>
						<textarea class='form-control' rows='3' cols='100' name='other_order_detail'></textarea>
					</div>
				</div>
				<h3 id='showmap'>แผนที่สำหรับมารับสินค้า</h3>
				<div class='content' id='showmap_section'>
					<div class='wrapper maps'>
							<div class='col3 middle'>
									<iframe height='400' style='width:100%;'
										frameborder='0' style='border:0'
										src='https://www.google.com/maps/embed/v1/place?key=AIzaSyDvDT_2HX-PW95Ua4vpd9886sEs2IXCaf0
											&q=13.895246,100.450427' allowfullscreen>
									</iframe>
							</div>
							<div class='col4 middle dark'>
									<h3>ติดต่อได้ที่</h3>
									<p><i class='material-icons'>home</i> 22/3 หมู่ที1   ตำบล ท่าอิฐ<br>อำเภอ ปากเกร็ด จังหวัด นนทบุรี  11120</p><br>
									<p><i class='material-icons'>person_pin</i> Line ID : order2easy</p><br>
									<p><i class='material-icons'>phone</i> มือถือ: 089-052-8899<br>&emsp;&nbsp;&nbsp;&nbsp;Office: 02-924-5850</p><br>
									<p><i class='material-icons'>email</i> cs@order2easy.com</p><br>
							</div>
					</div>
				</div>
			";
			echo "<div class='row col-md-12' style='padding:30px;'>";
			echo "<i class='material-icons'>info</i><p style='color:red;'>โปรดตรวจสอบความถูกต้องของรายการสั่งซื้อทุกครั้ง ก่อนกดยืนยันการสั่งซื้อ</p><br /><br />";
			echo "</div>";
			echo "<table style='width:100%;'><tr><td style='height:60px;width:21%;' class='text-left'><a href='cart.php'>&#10094; แก้ไขรายการสั่งซื้อ</a></td>";
			echo "<td><center><h1><input style='font-size:28px;' type='submit' class='button' value='ยืนยันการสั่งซื้อ' onclick='return checkaddress()'></h1></center></td>";
			echo "<td style='height:60px;width:21%;' class='text-right'><a href='#' onclick='gotoTop()'>กลับไปด้านบน</a></td></tr></table></form>";
			
	}else{
		echo "error : ยังไม่มีรายการสินค้าในตะกร้า";
	}
?>
	</div>
</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#showmap').hide();
			$('#showmap_section').hide();

		$("input[name=opt_send]:radio").change(function () {
			radio = document.getElementById('radio_by_self');
			if (radio.checked){
				$('#opt_send_div').hide();
				$('#address_head').hide();
				$('#address_section').hide();
				$('#showmap').show();
				$('#showmap_section').show();
			}else{
				$('#opt_send_div').show();
				$('#address_head').show();
				$('#address_section').show();
				$('#showmap').hide();
				$('#showmap_section').hide();
			}
		})

		<?php if($newAddressChecked=="") echo "$('#new_address_div').hide();"; ?>
		$( "#opt_company_other" ).hide();

		$("input[name=opt_address]:radio").change(function () {
		radio = document.getElementById('create_new_address');
		if (radio.checked){
						$('#new_address_div').show();
						$("div[name='exist_address_div']").hide();
				}else{
					$('#new_address_div').hide();
					$("div[name='exist_address_div']").show();
				}
		})

		$('input[type=radio][name=opt_company]').change(function() {
				if (this.value == 'other') {
						$( "#opt_company_other" ).show();        
				}else {
						$( "#opt_company_other" ).hide();
				}
		});
	});

	function gotoTop(){
		$('html, body').animate({ scrollTop: 0 }, 'slow');
	}

	function checkaddress(){

		//check price < 2000 thb
		if (isChangeAmout == true && order_price_thb < <?php echo $site_lowest_price; ?> ) {
			sweetAlert("เกิดข้อผิดพลาด", "คุณต้องมียอดการสั่งซื้ออย่างน้อย <?php echo $site_lowest_price; ?> บาทค่ะ", "error");
			return false;
		}

		// check option company
		if ($('input[name=opt_company]:checked', '#formCart').val() == 'other') {
			if ($('input[name=opt_company_other]').val()=='') {
				$('input[name=opt_company_other]').focus();
				sweetAlert("เกิดข้อผิดพลาด", "กรุณากรอกบริษัทขนส่งในไทยด้วยค่ะ", "error");
				return false;
			}
		}

		//check emthy item
		var qty = document.getElementsByName('txtQty[]');
		for(var i=0, n=qty.length;i<n;i++) {
			if (qty[i].value == "" || qty[i].value == 0) {
				sweetAlert("เกิดข้อผิดพลาด", "จำนวนสินค้าที่สั่งต้องไม่เป็นค่าว่างหรือ 0 ค่ะ", "error");
				return false;
			}
		}

		var radio_by_self = document.getElementById("radio_by_self");
		if (radio_by_self.checked) { return true; alert("by self"); }

		var address_count = document.getElementById("address_count").value;
		var address_name = document.getElementById("address_name").value;
		var address_line1 = document.getElementById("address_line1").value;
		var address_city = document.getElementById("province").value;
		var address_amphoe = document.getElementById("amphoe").value;
		var address_district = document.getElementById("district").value;
		var address_country = document.getElementById("address_country").value;
		var address_zipcode = document.getElementById("address_zipcode").value;
		var address_tel = document.getElementById("address_tel").value;
		var create_new = document.getElementById("create_new_address");

		if (Number(address_count) == 0) {
			if (create_new.checked) {
				if (address_name!="" && address_line1!="" && address_city!="" &&  address_country!="" && address_zipcode!="" && address_tel!="" && address_amphoe!="" && address_district!="") {
					return true;
				}else{
					alert("กรุณากรอกข้อมูลที่อยู่ให้ครบทุกช่อง");
					return false;
				}
			}else{
				alert("กรุณาสร้างข้อมูลที่อยู่ใหม่");
				return false;
			}
		}else{
			if (create_new.checked) {
				if (address_name!="" && address_line1!="" && address_city!="" &&  address_country!="" && address_zipcode!="" && address_tel!="" && address_amphoe!="" && address_district!="") {
					return true;
				}else{
					alert("กรุณากรอกข้อมูลที่อยู่ให้ครบทุกช่อง");
					return false;
				}
			}else{
				return true;
			}
		}
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

	var isChangeAmout = false;
	var order_price_thb = 0;

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
			var sum_price = 0;
			var array = [];
			$('span[name="item_price[]"]').each(function () {
					array.push(this.getAttribute("data-price"));
			});
			for(var i=0;i<array.length;i++) {
				sum_price += parseFloat(array[i]);
			}
			document.getElementById('total_price').innerHTML = sum_price.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');

			//recalculate amount
			var sum_amount = 0;
			var array = [];
			$('[name="txtQty[]"]').each(function () {
					array.push(this.value);
			});
			for(var i=0;i<array.length;i++) {
				sum_amount += parseInt(array[i]);
			}
			document.getElementById('total_qty').innerHTML = sum_amount;
			document.getElementById('order_amount').value = sum_amount;

			//recalculate price thb
			var rate = parseFloat(document.getElementById('rate_cny').innerHTML);
			var sum_price_thb = sum_price*rate;
			isChangeAmout = true;
			order_price_thb = sum_price_thb;
			document.getElementById('total_price_bath').innerHTML = sum_price_thb.toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1,');

		});
	});

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

	jQuery(function($) {
		jQuery('body').on('change','#province',function(){
				jQuery.ajax({
						'type':'POST',
						'url':'inc/php/amphoe.php',
						'cache':false,
						'data':{province:jQuery(this).val()},
						'success':function(html){
								jQuery("#amphoe").html(html);
						}
				});
				return false;
		});
	jQuery('body').on('change','#amphoe',function(){
				jQuery.ajax({
						'type':'POST',
						'url':'inc/php/district.php',
						'cache':false,
						'data':{amphoe:jQuery(this).val()},
						'success':function(html){
								jQuery("#district").html(html);
						}
				});
				return false;
		});
	});
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