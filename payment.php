 <?php
	include 'connect.php';
	include 'session.php';
	include 'inc/php/functions_statusConvert.php';

$error = '';
$count_order = 0;
$count_package = 0;

$redirect = $_REQUEST['redirect'];
$show_rate = $_REQUEST['show_rate'];

if(isset($_GET['order_id'])){
	$order_id[0] = $_GET['order_id'];
	$count_order = 1;
}else if(isset($_GET['package_id'])){
	$package_id[0] = $_GET['package_id'];
	$count_package = 1;
}else if (isset($_POST['payment_list'])) {
	$order_id = $_POST['order_id'];
	$package_id = $_POST['package_id'];
	$count_order = count($order_id);
	$count_package = count($package_id);
}

//show showing rate & server rate is equal
if ($show_rate!="") {
	$check_rate = mysql_query("select * from website_rate order by starting_date desc limit 1");
	$check_rate_row = mysql_fetch_array($check_rate);
	$check_rate_date = $check_rate_row['starting_date'];
	$check_rate = $check_rate_row['rate_cny'];
	if (number_format($show_rate,2)!=number_format($check_rate,2)) {
		$count_order = 0;
		$count_package = 0;
		$error_text = "มีการเปลี่ยนแปลงเรทสินค้า โปรดตรวจสอบราคาสินค้าอีกครั้งแล้วชำระเงินใหม่ค่ะ";
	}
}

if($count_order > 0 || $count_package > 0){

	//#######################
	//#### Order Payment ####
	//#######################
	for ($i=0; $i < $count_order ; $i++) { 

		if (!empty($order_id[$i])) {
		
			$order_payment_id = $order_id[$i];

			if ($error == '') {

				/* ตัดเงิน */
				$sel_order = mysql_query("select * from customer_order where order_id = '$order_payment_id'
					and customer_id = '$user_id'");
				$sel_order_row = mysql_fetch_array($sel_order);
				$order_number = $sel_order_row['order_number'];
				$order_date = $sel_order_row['date_order_created'];
				$amout=0;

				if ($sel_order_row['order_status_code'] == 1) {
					$amout = $sel_order_row['order_price'];
					$temp_amount = $amout;

					//เชิคว่าเงินพอจ่ายหรือไม่
					$total_topup_amout = 0;

					$sel_topup = mysql_query("select * from customer_request_topup where 
						customer_id = '$user_id' and usable_amout > 0 and (topup_status = 0 or topup_status = 1)");
					while ($row = mysql_fetch_array($sel_topup)) {
						$total_topup_amout += $row['usable_amout'];
					}
					// echo "totol topup = ".$total_topup_amout;

					if ($amout > $total_topup_amout) {
						echo '<div class="alert alert-danger container" role="alert">
										<label>เกิดข้อผิดพลาด</label>
										ยอดเงินที่มีอยู่ไม่พอจ่าย กรุณาเติมเงินค่ะ
									</div>';
						//$error_text .= "- ไม่สามารถชำระเงินออร์เดอร์เลขที่ ".$order_number." ได้ เนื่องจากยอดเงินไม่พอ <br>";
						$error_text .= "ไม่สามารถชำระเงินออร์เดอร์เลขที่ ".$order_number." ได้ เนื่องจากยอดเงินไม่พอ ";
						$error_id = "1";
					}else{
						//ยอดเงินที่มีอยู่พอจ่ายแน่นอน
						$sel_topup = mysql_query("select * from customer_request_topup where 
						customer_id = '$user_id' and usable_amout > 0 and (topup_status = 0 or topup_status = 1) 
						order by topup_status desc");
						$sel_topup_row = mysql_fetch_array($sel_topup);
						$isUseWaitingTopup = 0;

						while ($amout > 0) {
							if (($sel_topup_row['usable_amout'] - $amout) >= 0) {
								//ยอดเงินจาก topup ปัจจุบันจ่ายได้
								echo "ยอดเงินจาก topup ปัจจุบันจ่ายได้";

								// echo "sel_topup_row['usable_amout'] = ".$sel_topup_row['usable_amout'];
								$balance = $sel_topup_row['usable_amout'] - $amout;

								//เชค topup ว่าอยู่ในสถานะ waiting หรือไม่
								if ($sel_topup_row['topup_status'] == 0) { $isUseWaitingTopup = 1; } 

								//บันทึก balance ที่เหลือในลงใน topup->usable_amout
								$topup_id = $sel_topup_row['topup_id'];
								echo "update customer_request_topup set usable_amout = '$balance' where topup_id = '$topup_id'
									and customer_id = '$user_id'";
								$update_topup = mysql_query("update customer_request_topup set usable_amout = '$balance', used = 1 
									where topup_id = '$topup_id' and customer_id = '$user_id'");


								//บันทึกลงใน payment detail
								// echo "insert into 
								// 	payment_detail(topup_id,order_id,customer_id,payment_detail_date,before_paying_amount,after_paying_amount,current_amount) 
								// 	values('$topup_id','$order_payment_id','$user_id',NOW(),'".$sel_topup_row['usable_amout']."','$balance','$balance') ";
								$insert_payment_detail = mysql_query("insert into 
									payment_detail(topup_id,order_id,customer_id,payment_detail_date,before_paying_amount,after_paying_amount,current_amount) 
									values('$topup_id','$order_payment_id','$user_id',NOW(),'".$sel_topup_row['usable_amout']."','$balance','$balance') ");

								//เปลี่ยนสนานะ order
								echo "isUseWaitingTopup = ".$isUseWaitingTopup;
								$order_status_code = ($isUseWaitingTopup==1)?2:3;
								echo "update customer_order set order_status_code = '$order_status_code', 
									date_order_last_update = NOW(), date_order_paid = NOW(), order_payment_flag='1' 
									where order_id = '$order_payment_id' and customer_id = '$user_id'";
								$update_order = mysql_query("update customer_order set order_status_code = '$order_status_code', date_order_last_update = NOW(), date_order_paid = NOW(), order_payment_flag='1' 
									where order_id = '$order_payment_id' and customer_id = '$user_id'");

								//update statement
								// echo "insert into 
								// 	customer_statement(customer_id,order_id,statement_name,statement_date,credit) 
								// 	values('$user_id','$order_payment_id','ชำระค่าสินค้า',NOW(),'$amout')";
								// ---------- insert statement(31/08/2016) ------------
								if ($isUseWaitingTopup) {
									$insert_statement = mysql_query("insert into 
									customer_statement(customer_id,order_id,statement_name,statement_date,credit) 
									values('$user_id','$order_payment_id','ชำระค่าสินค้า เลขที่สั่งซื้อ $order_number (รอตรวจสอบยอดเงินที่โอนเข้า)','$order_date','$temp_amount')");	
								}else{
									$insert_statement = mysql_query("insert into 
									customer_statement(customer_id,order_id,statement_name,statement_date,credit) 
									values('$user_id','$order_payment_id','ชำระค่าสินค้า เลขที่สั่งซื้อ $order_number','$order_date','$temp_amount')");	
								}

								//select last payment number --(2)
								$select_payment_number = mysql_query("SELECT payment_number 
																											FROM payment 
																											WHERE YEAR(payment_date) = '".date('Y')."' 
																											ORDER BY payment_id DESC");

								//บันทึกลงใน payment -- (1)
								// echo "insert into 
								// 	payment(payment_date,customer_id,order_id,payment_amount,payment_type) 
								// 	values(NOW(),'$user_id','$order_payment_id','".$sel_order_row['order_price']."','1') ";
								$insert_payment_detail = mysql_query("insert into 
									payment(payment_date,customer_id,order_id,payment_amount,payment_type) 
									values(NOW(),'$user_id','$order_payment_id','".$sel_order_row['order_price']."','1') ");
								$payment_id = mysql_insert_id();

								//update withdraw number -- (2)
								// echo "num row = ".mysql_num_rows($select_payment_number);
								if (mysql_num_rows($select_payment_number) > 0) {
									//เอา order_number เก่ามา +1
									$select_payment_number_row = mysql_fetch_array($select_payment_number);
									$old_payment_number = $select_payment_number_row[0];
									// echo "old_payment_number=".$old_payment_number;
									$number = (int)substr($old_payment_number, 3);
									$payment_number = "R".date("y").str_pad($number+1 ,7, "0", STR_PAD_LEFT);
									// echo "new_payment_number=".$payment_number;
									$update_number = mysql_query("update payment set payment_number='$payment_number' where payment_id = '$payment_id'");
								}else{
									//สร้าง payment_number ใหม่
									$payment_number = "R".date("y").str_pad(1 ,7, "0", STR_PAD_LEFT);
									// echo "create_new=".$payment_number;
									$update_number = mysql_query("update payment set payment_number='$payment_number' where payment_id = '$payment_id'");
								}

								//update customer current_amount
								$select_cus_amount = mysql_query("select current_amount from customer where customer_id = '$user_id'");
								$select_cus_amount_row = mysql_fetch_array($select_cus_amount);
								$cus_amount = $select_cus_amount_row['current_amount'];
								$cus_amount -= $temp_amount;
								$update_cus_amount = mysql_query("update customer set current_amount = '$cus_amount' where customer_id = '$user_id'");

								//update customer_request_payment payment_request_status
								$payment_request_status = ($isUseWaitingTopup==1)?1:2;
								echo "update customer_request_payment set 
									payment_request_status = '$payment_request_status' 
									where customer_id = '$user_id' and order_id = '$order_payment_id' and payment_request_type = '1'";
								$update_payment_request_status = mysql_query("update customer_request_payment set 
									payment_request_status = '$payment_request_status', date_payment_paid = NOW() 
									where customer_id = '$user_id' and order_id = '$order_payment_id' and payment_request_type = '1'");

								//update customer_order_product current_status = 3 , current_updatetime = today()
								//ช่องตรง คืนเงิน เปลี่ยนเป็น ชำระเงินเรียบร้อยแล้ว พี่ไม่รู้ field ไหน
								$current_status = ($isUseWaitingTopup==1)?2:3;
								echo "update customer_order_product set 
									current_status = '$current_status' 
									where order_id = '$order_payment_id'";
								$update_customer_order_product = mysql_query("update customer_order_product set 
									current_status = '$current_status' 
									where order_id = '$order_payment_id'");


								if ($update_topup && $update_order) {
									echo '<div class="alert alert-success container" role="alert"><label>ชำระเงินเรียบร้อย</label></div>';
									$message_text .= "- ชำระเงินออร์เดอร์เลขที่ ".$order_number." เรียบร้อย <br>";
									$isPaymentSuccess = true;
								}else{
									echo '<div class="alert alert-danger container" role="alert"><label>เกิดข้อผิดพลาด</label>'. mysql_error().'</div>';
									$error_text .= "เกิดข้อผิดพลาด ".mysql_error();
								}

								$amout = 0;

							}else{

								//ยอดเงินจาก topup ปัจจุบันจ่ายได้ไม่พอ
								echo "ยอดเงินจาก topup ปัจจุบันจ่ายได้ไม่พอ";
								$amout = $amout - $sel_topup_row['usable_amout'];

								//เชค topup ว่าอยู่ในสถานะ waiting หรือไม่
								if ($sel_topup_row['topup_status'] == 0) { $isUseWaitingTopup = 1; } 
								echo "isUseWaitingTopup = ".$isUseWaitingTopup;

								//บันทึก 0 ในลงใน topup->usable_amout
								$topup_id = $sel_topup_row['topup_id'];
								// echo "update customer_request_topup set usable_amout = 0 where topup_id = '$topup_id' 
								// 	and customer_id = '$user_id'";
								$update_topup = mysql_query("update customer_request_topup set usable_amout = 0, used = 1 where 
									topup_id = '$topup_id' and customer_id = '$user_id'");

								//update statement
								$credit = $sel_topup_row['usable_amout'];
								// echo "insert into 
								// 	customer_statement(customer_id,order_id,statement_name,statement_date,credit) 
								// 	values('$user_id','$order_payment_id','ชำระค่าสินค้า',NOW(),'$credit')";
								// ---------- insert statement(31/08/2016) ------------
								// if ($isUseWaitingTopup) {
								// 	$insert_statement = mysql_query("insert into 
								// 	customer_statement(customer_id,order_id,statement_name,statement_date,credit) 
								// 	values('$user_id','$order_payment_id','ชำระค่าสินค้า เลขที่สั่งซื้อ $order_number (รอตรวจสอบยอดเงินที่โอนเข้า)','$order_date','$credit')");	
								// }else{
								// 	$insert_statement = mysql_query("insert into 
								// 	customer_statement(customer_id,order_id,statement_name,statement_date,credit) 
								// 	values('$user_id','$order_payment_id','ชำระค่าสินค้า เลขที่สั่งซื้อ $order_number','$order_date','$credit')");	
								// }

								//บันทึกลงใน payment detail
								// echo "insert into 
								// 	payment_detail(topup_id,order_id,customer_id,payment_detail_date,before_paying_amount,after_paying_amount,current_amount) 
								// 	values('$topup_id','$order_payment_id','$user_id',NOW(),'".$sel_topup_row['usable_amout']."','0','0') ";
								$insert_payment_detail = mysql_query("insert into 
									payment_detail(topup_id,order_id,customer_id,payment_detail_date,before_paying_amount,after_paying_amount,current_amount) 
									values('$topup_id','$order_payment_id','$user_id',NOW(),'".$sel_topup_row['usable_amout']."','0','0') ");

								$sel_topup_row = mysql_fetch_array($sel_topup);
							}
						}
					}
				}else{

					$error_text .= "ออร์เดอร์เลขที่ ".$order_number." ได้ถูกกดชำระเงินแล้ว";
				}
			}
		}
	}

	//#####################
	//## package payment ##
	//##################### 
	for ($i=0; $i < $count_package ; $i++) { 

		if (!empty($package_id[$i])) { 
		
			$package_payment_id = $package_id[$i];

			if ($error == '') {

				/* ตัดเงิน */
				$sel_order = mysql_query("select * from package where packageid = '$package_payment_id'
					and customerid = '$user_id'");
				$sel_payment_row = mysql_fetch_array($sel_order);
				$packageno = $sel_payment_row['packageno'];

				$amout=0;

				if ($sel_payment_row['statusid'] == 3) { //สถานะรอชำระค่าขนส่ง
					$amout = $sel_payment_row['total'];
					$temp_amount = $amout;

					//เชิคว่าเงินพอจ่ายหรือไม่
					$total_topup_amout = 0;

					$sel_topup = mysql_query("select * from customer_request_topup where 
						customer_id = '$user_id' and usable_amout > 0 and (topup_status = 0 or topup_status = 1)");
					while ($row = mysql_fetch_array($sel_topup)) {
						$total_topup_amout += $row['usable_amout'];
					}
					// echo "totol topup = ".$total_topup_amout;

					if ($amout > $total_topup_amout) {
						echo '<div class="alert alert-danger container" role="alert">
										<label>เกิดข้อผิดพลาด</label>
										ยอดเงินที่มีอยู่ไม่พอจ่าย กรุณาเติมเงินค่ะ
									</div>';
						$error_text .= "ไม่สามารถชำระค่ากล่องเลขที่ ".$packageno." ได้ เนื่องจากยอดเงินไม่พอ ";
						$error_id = "1";
					}else{
						//ยอดเงินที่มีอยู่พอจ่ายแน่นอน
						$sel_topup = mysql_query("select * from customer_request_topup where 
						customer_id = '$user_id' and usable_amout > 0 and (topup_status = 0 or topup_status = 1) 
						order by topup_status desc");
						$sel_topup_row = mysql_fetch_array($sel_topup);
						$isUseWaitingTopup = 0;

						while ($amout > 0) {
							if (($sel_topup_row['usable_amout'] - $amout) >= 0) {
								//ยอดเงินจาก topup ปัจจุบันจ่ายได้
								echo "ยอดเงินจาก topup ปัจจุบันจ่ายได้";

								// echo "sel_topup_row['usable_amout'] = ".$sel_topup_row['usable_amout'];
								$balance = $sel_topup_row['usable_amout'] - $amout;

								//เชค topup ว่าอยู่ในสถานะ waiting หรือไม่
								if ($sel_topup_row['topup_status'] == 0) { $isUseWaitingTopup = 1; } 

								//บันทึก balance ที่เหลือในลงใน topup->usable_amout
								$topup_id = $sel_topup_row['topup_id'];
								echo "update customer_request_topup set usable_amout = '$balance' where topup_id = '$topup_id'
									and customer_id = '$user_id'";
								$update_topup = mysql_query("update customer_request_topup set usable_amout = '$balance', used = 1 
									where topup_id = '$topup_id' and customer_id = '$user_id'");

								//update statement
								// echo "insert into 
								// 	customer_statement(customer_id,order_id,statement_name,statement_date,credit) 
								// 	values('$user_id','$package_payment_id','ชำระค่าสินค้า',NOW(),'$amout')";
								// ---------- ไม่ต้อง insert statement ------------
								// $insert_statement = mysql_query("insert into 
								// 	customer_statement(customer_id,order_id,statement_name,statement_date,credit) 
								// 	values('$user_id','$package_payment_id','ชำระค่าสินค้า',NOW(),'$amout')");

								//บันทึกลงใน payment detail
								// echo "insert into 
								// 	payment_detail(topup_id,order_id,customer_id,payment_detail_date,before_paying_amount,after_paying_amount,current_amount) 
								// 	values('$topup_id','$package_payment_id','$user_id',NOW(),'".$sel_topup_row['usable_amout']."','$balance','$balance') ";
								$insert_payment_detail = mysql_query("insert into 
									payment_detail(topup_id,package_id,customer_id,payment_detail_date,before_paying_amount,after_paying_amount,current_amount) 
									values('$topup_id','$package_payment_id','$user_id',NOW(),'".$sel_topup_row['usable_amout']."','$balance','$balance') ");

								//เปลี่ยนสนานะ package
								echo "isUseWaitingTopup = ".$isUseWaitingTopup;
								$payment_statusid = ($isUseWaitingTopup==1)?4:5;
								
								$update_package = mysql_query("update package 
									set statusid = '$payment_statusid', editdate = NOW(), paydate = NOW() 
									where packageid = '$package_payment_id' and customerid = '$user_id'");

								//select last payment number --(2)
								$select_payment_number = mysql_query("SELECT payment_number 
																											FROM payment 
																											WHERE YEAR(payment_date) = '".date('Y')."' 
																											ORDER BY payment_id DESC");

								//บันทึกลงใน payment -- (1)
								// echo "insert into 
								// 	payment(payment_date,customer_id,order_id,payment_amount,payment_type) 
								// 	values(NOW(),'$user_id','$package_payment_id','".$sel_payment_row['order_price']."','1') ";
								$insert_payment_detail = mysql_query("insert into 
									payment(payment_date,customer_id,package_id,payment_amount,payment_type) 
									values(NOW(),'$user_id','$package_payment_id','".$sel_payment_row['total']."','1') ");
								$payment_id = mysql_insert_id();

								//update withdraw number -- (2)
								// echo "num row = ".mysql_num_rows($select_payment_number);
								if (mysql_num_rows($select_payment_number) > 0) {
									//เอา order_number เก่ามา +1
									$select_payment_number_row = mysql_fetch_array($select_payment_number);
									$old_payment_number = $select_payment_number_row[0];
									// echo "old_payment_number=".$old_payment_number;
									$number = (int)substr($old_payment_number, 3);
									$payment_number = "R".date("y").str_pad($number+1 ,7, "0", STR_PAD_LEFT);
									// echo "new_payment_number=".$payment_number;
									$update_number = mysql_query("update payment set payment_number='$payment_number' where payment_id = '$payment_id'");
								}else{
									//สร้าง payment_number ใหม่
									$payment_number = "R".date("y").str_pad(1 ,7, "0", STR_PAD_LEFT);
									// echo "create_new=".$payment_number;
									$update_number = mysql_query("update payment set payment_number='$payment_number' where payment_id = '$payment_id'");
								}

								//update customer current_amount
								$select_cus_amount = mysql_query("select current_amount from customer where customer_id = '$user_id'");
								$select_cus_amount_row = mysql_fetch_array($select_cus_amount);
								$cus_amount = $select_cus_amount_row['current_amount'];
								$cus_amount -= $temp_amount;
								$update_cus_amount = mysql_query("update customer set current_amount = '$cus_amount' where customer_id = '$user_id'");

								//update customer_request_payment payment_request_status
								//$payment_request_status = ($isUseWaitingTopup==1)?1:2;
								//echo "update customer_request_payment set 
								//	payment_request_status = '$payment_request_status' 
								//	where customer_id = '$user_id' and order_id = '$package_payment_id' and payment_request_type = '1'";
								//$update_payment_request_status = mysql_query("update customer_request_payment set 
								//	payment_request_status = '$payment_request_status', date_payment_paid = NOW() 
								//	where customer_id = '$user_id' and order_id = '$package_payment_id' and payment_request_type = '1'");


								if ($update_topup && $update_package) {
									echo '<div class="alert alert-success container" role="alert"><label>ชำระเงินเรียบร้อย</label></div>';
									$message_text .= "- ชำระค่ากล่องเลขที่ ".$packageno." เรียบร้อย <br>";
								}else{
									echo '<div class="alert alert-danger container" role="alert"><label>เกิดข้อผิดพลาด</label>'. mysql_error().'</div>';
									$error_text .= "- เกิดข้อผิดพลาด ".mysql_error()."<br>";
								}

								$amout = 0;

							}else{

								//ยอดเงินจาก topup ปัจจุบันจ่ายได้ไม่พอ
								echo "ยอดเงินจาก topup ปัจจุบันจ่ายได้ไม่พอ";
								$amout = $amout - $sel_topup_row['usable_amout'];

								//เชค topup ว่าอยู่ในสถานะ waiting หรือไม่
								if ($sel_topup_row['topup_status'] == 0) { $isUseWaitingTopup = 1; } 
								echo "isUseWaitingTopup = ".$isUseWaitingTopup;

								//บันทึก 0 ในลงใน topup->usable_amout
								$topup_id = $sel_topup_row['topup_id'];
								// echo "update customer_request_topup set usable_amout = 0 where topup_id = '$topup_id' 
								// 	and customer_id = '$user_id'";
								$update_topup = mysql_query("update customer_request_topup set usable_amout = 0, used = 1 where 
									topup_id = '$topup_id' and customer_id = '$user_id'");

								//update statement
								$credit = $sel_topup_row['usable_amout'];
								// echo "insert into 
								// 	customer_statement(customer_id,order_id,statement_name,statement_date,credit) 
								// 	values('$user_id','$package_payment_id','ชำระค่าสินค้า',NOW(),'$credit')";
								// ----------- ไม่ต้อง update statement แล้ว -------------
								// $insert_statement = mysql_query("insert into 
								// 	customer_statement(customer_id,order_id,statement_name,statement_date,credit) 
								// 	values('$user_id','$package_payment_id','ชำระค่าสินค้า',NOW(),'$credit')");

								//บันทึกลงใน payment detail
								// echo "insert into 
								// 	payment_detail(topup_id,order_id,customer_id,payment_detail_date,before_paying_amount,after_paying_amount,current_amount) 
								// 	values('$topup_id','$package_payment_id','$user_id',NOW(),'".$sel_topup_row['usable_amout']."','0','0') ";
								$insert_payment_detail = mysql_query("insert into 
									payment_detail(topup_id,package_id,customer_id,payment_detail_date,before_paying_amount,after_paying_amount,current_amount) 
									values('$topup_id','$package_payment_id','$user_id',NOW(),'".$sel_topup_row['usable_amout']."','0','0') ");

								$sel_topup_row = mysql_fetch_array($sel_topup);
							}
						}
					}
				}else{

					$error_text .= "กล่องเลขที่ ".$packageno." ไม่อยู่ในสถานะรอชำระเงินหรือได้ถูกกดชำระเงินไปแล้ว";
				}
			}
		}
	}
}

if ($redirect==1) {
	if ($isPaymentSuccess) {
		header( "location: order_list.php?&message=".$message_text);
	}else{
		header( "location: order_show_detail_confirmed.php?order_id=".$_GET['order_id']."&message=".$message_text."&error=".$error_text."&error_id=".$error_id );
	}
}
if ($redirect==2) {
	header( "location: payment_list.php?message=".$message_text."&error=".$error_text );
	//echo "$message_text // $error_text";
}
if ($redirect==3) {
	header( "location: package_detail.php?packageid=".$_GET['package_id']."&message=".$message_text."&error=".$error_text."&error_id=".$error_id );
	//echo "$message_text // $error_text";
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

	<div class="row" style="margin:20px 0px 20px 0px;">
		<div class="col-md-6">
			<div class="from-group" id="payment">
				<h1>ชำระเงิน</h1>
			</div>
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