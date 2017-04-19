<?php
	include 'connect.php';
	include 'session.php';

	$cart_id = $_GET['cart_id'];
	$amount = $_GET['amount'];

	echo "update shopping_cart set cart_quantity='$amount' where cart_id='$cart_id' and customer_id = '$user_id'";

	$update = mysql_query("update shopping_cart set cart_quantity='$amount' where cart_id='$cart_id' and customer_id = '$user_id'");

	if ($update) {
		echo "success";
	}else{
		echo "fail";
	}


?>