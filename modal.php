

	<!-- Modal Search -->
	<div class="modal" id="search_mdl" role="dialog">
		<div class="modal-dialog" style="width:800px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">ค้นหาสินค้า</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" id="frmAddtoCardList" role="form" action="" method="post" > 
					
						<div class="row">
						  <div class="col-md-6"><div id="show_search_result" name="show_search_result"></div></div>
						  <div class="col-md-6 ">
							  <div id="search_list" name="search_list" class="search_list">
							  <h3>รายการ</h3>
							  <table class="table table-bordered" id="tblOrderList">
							  <thead>
							    <tr>
							      <th>#</th>
							      <th>สี</th>
							      <th>ขนาด</th>
							      <th>จำนวน</th>
							      <th>ตัวเลือก</th>
							    </tr>
							  </thead>
							  <tbody></tbody>
							</table>
							<div class="err-orderlist">
								<label id="alert-size-orderlist"></label>
								<label id="alert-color-orderlist"></label>
							</div>
							<button type="button" class="btn btn-success" id="addOrderToList">เพิ่มในรายการ</button>
							</div>						  
						  </div>
						</div>	
					</form> 
				</div>
				<div class="modal-footer">
					<label id="alert-add-orderlist"></label>
					<button type="submit" class="btn btn-primary" id="addtocart_button1"  onclick="addtocart();"><span class="glyphicon glyphicon-shopping-cart"></span> หยิบใส่ตะกร้า</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Send Email -->
	<div class="modal" id="send_email" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">ส่งข้อความผ่านเว็บถึงเรา</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal"  role="form" action="" method="post" > 
						<div class="row" id="form_send_email" name="show_manualadd">
							<div class="col-md-3 control-label"><label>ชื่อ : </label></div>
							<div class="col-md-8">
								<input id="sendmail_name" type="text" class="form-control"><br>
							</div>
							<div class="col-md-3 control-label"><label>ที่อยู่ : </label></div>
							<div class="col-md-8">
								<input id="sendmail_address" type="text" class="form-control"><br>
							</div>
							<div class="col-md-3 control-label"><label>เบอร์โทรศัพท์ : </label></div>
							<div class="col-md-8">
								<input id="sendmail_phone" type="text" class="form-control"><br>
							</div>
							<div class="col-md-3 control-label"><label>อีเมล์ : </label></div>
							<div class="col-md-8">
								<input id="sendmail_email" type="text" class="form-control"><br>
							</div>
							<div class="col-md-3 control-label"><label>ข้อความ : </label></div>
							<div class="col-md-8">
								<input id="sendmail_message" type="text" class="form-control"><br>
							</div>
						</div>
					</form> 
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" >ส่งข้อความ</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Manual Add -->
	<div class="modal" id="manualadd" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">สั่งซื้อสินค้า</h4>
				</div>
				<div class="modal-body">
					<form class="form-horizontal" id="frmAddtoCard" role="form" action="" method="post" > 
						<div class="row" id="show_manualadd" name="show_manualadd">
									<div class="col-md-3 control-label"><label>ชื่อเว็บไซด์ : </label></div>
									<div class="col-md-8"><input id="msource" type="text" class="form-control" placeholder="เช่น Taobao, T-mall, JD, Amazon.cn"><br></div>

									<div class="col-md-3 control-label"><label>ลิงค์เว็บไซด์ : </label></div>
									<div class="col-md-8"><input id="mproduct_url" type="text" class="form-control" placeholder="เช่น http://item.taobao.com/item.htm?id=520517112316"><br></div>
									<div class="col-md-1"><span style="font-size:large;color:red;">*</span></div>

									<div class="col-md-3 control-label"><label>ชื่อร้านค้า: </label></div>
									<div class="col-md-8"><input id="mshop_name" type="text" class="form-control" placeholder="ชื่อร้านค้า"><br></div>

									<div class="col-md-3 control-label"><label>ชื่อสินค้า : </label></div>
									<div class="col-md-8"><input id="mproduct_name" type="text" class="form-control" placeholder="ชื่อสินค้า"><br></div>
									<div class="col-md-1"><span style="font-size:large;color:red;">*</span></div>

									<div class="col-md-3 control-label"><label>รูปสินค้า : </label></div>
									<div class="col-md-8"><input id="mproduct_img" type="text" class="form-control" placeholder="รูปสินค้า"><br></div>
									
									<div class="col-md-3 control-label"><label>ราคา (¥) : </label></div>
									<div class="col-md-8"><input id="mproduct_price" type="text" class="form-control" placeholder="ราคาสินค้า"><br></div>
									<div class="col-md-1"><span style="font-size:large;color:red;">*</span></div>

									<div class="col-md-3 control-label"><label>ขนาด : </label></div>
									<div class="col-md-8"><input id="mproduct_size" type="text" class="form-control" placeholder="ขนาดสินค้า"><br></div>
									
									<div class="col-md-3 control-label"><label>สี : </label></div>
									<div class="col-md-8"><input id="mproduct_color" type="text" class="form-control" placeholder="กรอกสีสินค้า"><br></div>
									
									<div class="col-md-3 control-label"><label>จำนวน : </label></div>
									<div class="col-md-8"><input id="mproduct_quantity" type="text" class="form-control" placeholder="จำนวนสินค้าที่ต้องการสั่ง"><br></div>
									<div class="col-md-1"><span style="font-size:large;color:red;">*</span></div>

						</div>
					</form> 
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary" id="addtocart_button" onclick="manual_addtocart();"><span class="glyphicon glyphicon-shopping-cart"></span> เพิ่มในตะกร้า</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">ยกเลิก</button>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Modal Add to Cart -->
	<div class="modal" id="addtocart" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">เพิ่มสินค้า</h4>
				</div>
				<div class="modal-body">
					<form id="loginform" class="form-horizontal" role="form" action="order.php" method="post" onsubmit="return addtocart()"> 
						<span id="show_addtocart_result" name="show_addtocart_result"></span>
					</form> 
				</div>
				<div class="modal-footer">
					<a href="cart.php"><button type="submit" class="btn btn-success" <?php if(!isset($_SESSION['CX_login_user'])){ echo "disabled";} ?>>ชำระเงิน</button></a>
					<button type="button" class="btn btn-default" onclick="gotoCartPage()">ซื้อสินค้าต่อ</button>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		function isNumber(evt) {
			//Enable arrow for firefox.
			if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
					if (evt.keyCode == 8 || evt.keyCode == 46 || evt.keyCode == 37 || evt.keyCode == 39) {
						return true;
				}
			}

				evt = (evt) ? evt : window.event;
				var charCode = (evt.which) ? evt.which : evt.keyCode;

				//Enable dot.
				//if (charCode == 46) { return true; };

				if (charCode > 31 && (charCode < 48 || charCode > 57 )) {
						return false;
				}
				return true;
		}

		function gotoCartPage(){
			document.location.href = 'cart.php';
		}

		function reloadCart(){
			//document.getElementsByTagName("BODY")[0].style.paddingRight = "0px";
			var href = window.location.href;
			var page = href.substr(href.lastIndexOf('/') + 1);
			if (page == 'cart' || page == 'cart.php') {
				location.reload();
				return;
			}

			$('#addtocart').modal('hide');
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
					var item_badge = document.getElementById('item_badge');
					item_badge.innerHTML = req.responseText;
				}
			}
			req.open("GET", "count_cart_item.php", true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
			req.send(null); 
		}

		function showLargeImage(imageURL){
			//alert(imageURL);
			document.getElementById("product_img").src= imageURL;
			document.getElementById("product_img_link").href= imageURL;
		}
	</script>

        <link href="css/lightbox.css" rel="stylesheet">
				<script src="js/lightbox.js"></script>


