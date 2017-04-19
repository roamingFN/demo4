<?php
	include '../connect.php';
	include '../session.php';
	include '../inc/php/functions_statusConvert.php';

	$array_address_id = json_decode($_GET['array_address_id']);
	$shipping_conflit = $_GET['shipping_conflit'];

	$array_shipping_option = json_decode($_GET['array_shipping_option']);
	$array_shipping_option = array_unique($array_shipping_option);
	rsort($array_shipping_option);

	if ($shipping_conflit) {
		echo "<h3>กรุณาเลือกวิธีการขนส่งสินค้า</h3>";
		for ($i=0; $i < count($array_shipping_option); $i++) { 
			echo '<div class="checkbox">
							<label>
								<input type="radio" onclick="toggleAddress(this)" name="select_shipping_option" value="'.$array_shipping_option[$i].'"> '.convertTransportName($array_shipping_option[$i]).'
							</label>
						</div>';
		}
		
	}

	$str_address_id = "";
	for ($i=0; $i < count($array_address_id); $i++) { 
		if ($i != 0) {
			$str_address_id .= " or ";
		}
		$str_address_id .= " order_address_id = '".$array_address_id[$i]."' ";
	}

	$str_query = "select order_address_id from customer_order where customer_id = '$user_id' and ".$str_address_id." group by order_address_id order by order_address_id";
	//echo $str_query;

	$select_address = mysql_query($str_query);

	if (mysql_num_rows($select_address) > 0) {
		echo "<hr /><div id='select_address_content'><h3>กรุณาเลือกที่อยู่จัดส่ง</h3>";
		while ($row = mysql_fetch_array($select_address)) {
			if ($row['order_address_id'] != 0){
				echo '<div class="checkbox">
								<label>
									<input type="radio" name="select_address_id" value="'.$row['order_address_id'].'"> ';

				if ($row['order_address_id'] == 0) {
					echo "มารับด้วยตนเอง";
				}else{
					$select_address_detail =  mysql_query("select * from customer_address where address_id = '".$row['order_address_id']."'");
					if (mysql_num_rows($select_address_detail)>0) {
						$address_detail = mysql_fetch_array($select_address_detail);
						$order_address_text = $address_detail['address_name']." ".$address_detail['line_1']." ".$address_detail['city'].", ".$address_detail['country']." ".$address_detail['zipcode']." Tel. ".$address_detail['phone']; 
						echo $order_address_text;
					}
				}
				echo  ' </label>
							</div>';
			}
		}
		echo "</div>";
	}

?>