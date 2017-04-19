<?php

function secureText($string){
	$string = strip_tags($string);
	$string = stripcslashes($string);
	$string = mysql_real_escape_string($string);
	return $string;
}

function formatNotEmthyValue($value){
	if ($value == "") {
		return "ไม่ระบุ";
	}else{
		return $value;
	}
}

function showLocalMessage($message_text,$error_text,$info_text){
	//show page message
	if (isset($message_text)||isset($error_text)){
		$text .= "<br />";
	}
	if (isset($message_text)) {
		if ($message_text!="") {
			$text .= '<div class="alert alert-success container" role="alert"><label>'.$message_text.'</label></div>';
		}
	}
	if (isset($error_text)) {
		if ($error_text!="") {
			$text .= '<div class="alert alert-danger container" role="alert"><label>'.$error_text.'</div>';
		}
	}
	if (isset($info_text)) {
		if ($info_text!="") {
			$text .= '<div class="alert alert-danger container" role="alert"><label>'.$info_text.'</div>';
		}
	}
	return $text;
}

function showMessage(){
	//show page message
	if (isset($_GET['message'])||isset($_GET['error'])){
		echo "<br />";
	}
	if (isset($_GET['message'])) {
		if ($_GET['message']!="") {
			echo '<div class="alert alert-success container" role="alert"><label>'.$_GET['message'].'</label></div>';
		}
	}
	if (isset($_GET['error'])) {
		if ($_GET['error']!="") {
			echo '<div class="alert alert-danger container" role="alert"><label>'.$_GET['error'].'</div>';
		}
	}
}

function formatDate($date){
	if ($date == "") {
		return "-";
	}else if($date == 0){
		return "-";
	}else {
		return date("d/m/Y", strtotime($date));
	}
}

function formatPackageNo($status){
	$query_package_status = mysql_query("select * from package_status where packagestatusid = '$status'");
	if (mysql_num_rows($query_package_status)>0) {
		$row = mysql_fetch_array($query_package_status);
		return $row['packagestatusname'];
	}else{
		return $status;
	}
}

function convertPaymentRequestType($type){
	if ($type == '1') {
		return "ค่าสินค้า";
	}else if ($type == '2') {
		return "ค่าขนส่ง";
	}else{
		return $type;
	}
}

function convertPaymentRequestStatus($status){
	if ($status == '0') {
		return "ยังไม่ได้ชำระเงิน";
	}else if ($status == '1') {
		return "ชำระเงินแล้ว รอตรวจสอบ";
	}else if ($status == '2') {
		return "ชำระเงินเรียบร้อยแล้ว";
	}else if ($status == '99') {
		return "ยกเลิก";
	}else{
		return $status;
	}
}

function convertRecievedStatus($return_baht,$status,$order_status){

	if ($order_status == 0 || $order_status == 1) {
		return "ยังไม่ได้ชำระเงิน";
	} else if ( $order_status == 2 ) {
		return "ระหว่างตรวจสอบ";
	} else {
		if ($return_baht == 0) {
			return "ชำระเงินเรียบร้อยแล้ว";
		}else if($return_baht > 0){
			if ($status == 1) {
				return "อยู่ระหว่างดำเนินการ";
			}else if($status == 2){
				return "คืนเงินเรียบร้อย";
			}else{
				return $status;
			}
		}else if($return_baht < 0){
			if ($status == 1) {
				return "อยู่ระหว่างดำเนินการ";
			}else if($status == 2){
				return "จัดเก็บเรียบร้อย";
			}else{
				return $status;
			}
		}	
	}	
}
function convertOrderProductStatus($status){
	$query_order_product_status = mysql_query("select * from order_product_status_code where status_code = '$status'");
	
	if (mysql_num_rows($query_order_product_status)>0) {
		$row = mysql_fetch_array($query_order_product_status);
		return $row['description'];
	}else{
		return $status;
	}
}
function convertTransportName($transportId){

	$transport = mysql_query("select * from website_transport where transport_id = '$transportId'");
	$transport_row = mysql_fetch_array($transport);
	if (mysql_num_rows($transport) < 1) {
		$transport = mysql_query("select * from website_transport where transport_eng_name = '$transportId'");
		$transport_row = mysql_fetch_array($transport);
		if (mysql_num_rows($transport) < 1) {
			return $transportId;
		}
		return $transport_row['transport_th_name'];
	}
	return $transport_row['transport_th_name'];
}
function convertOrderStatus($status){
	$order_status_array = array();
	$query_order_status = mysql_query("select * from order_status_code");
	while ($row = mysql_fetch_array($query_order_status)) {
		$order_status_array[$row['status_id']]=$row['des'];
	}
	return $order_status_array[$status];
}
function convertTopupUsedStatus($status){
	switch ($status) {
		case '0':
			return "ยังไม่ใด้ใช้งาน";
		case '1':
			return "ใช้งานแล้ว";
		default:
			return $status;
	}
}
function convertPaymentStatus($status){
	switch ($status) {
		case '0':
			return "รอตรวจสอบยอด";
		case '1':
			return "ยอดไม่พอ";
		case '2':
			return "ดำเนินการแล้ว";
		default:
			return $status;
	}
}

function convertRequestType($status){
	switch ($status) {
		case '1':
			return "ค่าสินค้า";
		case '2':
			return "ค่าขนส่ง";
		default:
			return $status;
	}
}

function convertTopupStatus($status){
	switch ($status) {
		case '0':
			return "รอตรวจสอบ";
		case '1':
			return "ตรวจสอบแล้ว";
		case '2':
			return "ยกเลิก";
		default:
			return $status;
	}
}

function convertWithdarwStatus($status){
	switch ($status) {
		case '0':
			return "รอตรวจสอบ";
		case '1':
			return "ตรวจสอบแล้ว";
		case '2':
			return "ยกเลิก";
		default:
			return $status;
	}
}

function convertStatementZero($amout){
	if ($amout == 0) {
		return "-";
	}else{
		return $amout;
	}
}

function formatBankAccNo($acc_no){
		$acc_no = substr_replace($acc_no, '-', 3, 0);
		$acc_no = substr_replace($acc_no, '-', 5, 0);
		$acc_no = substr_replace($acc_no, '-', 11, 0);
		return $acc_no;
	}

function file_newname($path, $filename){
    if ($pos = strrpos($filename, '.')) {
           $name = substr($filename, 0, $pos);
           $ext = substr($filename, $pos);
    } else {
           $name = $filename;
    }

    $newpath = $path.'/'.$filename;
    $newname = $filename;
    $counter = 0;
    while (file_exists($newpath)) {
           $newname = $name .'_'. $counter . $ext;
           $newpath = $path.'/'.$newname;
           $counter++;
     }

    return $newpath;
}

function show_success($text){

	echo '<div class="alert alert-success container" role="alert"><label>'.$text.'</label></div>';

}

function show_error($text){

	echo '<div class="alert alert-danger container" role="alert"><label>'.$text.'</label></div>';

}

function show_info($text){

	echo '<div class="alert alert-info container" role="alert"><label>'.$text.'</label></div>';

}

function show_warning($text){

	echo '<div class="alert alert-warning container" role="alert"><label>'.$text.'</label></div>';

}

?>