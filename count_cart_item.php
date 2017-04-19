<?php
include 'connect.php';
include 'session.php';
include 'inc/php/functions_statusConvert.php';

$select_cart_item = mysql_query("select count(cart_id) from shopping_cart where customer_id = '$user_id'");
$select_cart_item_row = mysql_fetch_array($select_cart_item);
echo $select_cart_item_row['count(cart_id)'];
?>