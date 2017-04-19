<?php

//Connect XAMPP
$connection = mysql_connect("localhost", "root", "");
mysql_query("SET NAMES UTF8",$connection);  
$db = mysql_select_db("china_express", $connection);

//Connect HOST
//$connection = mysql_connect("localhost", "root", "1234");
// $connection = mysql_connect("localhost", "ordereas", "6N1tjRj40l");
// mysql_query("SET NAMES UTF8",$connection);  
// $db = mysql_select_db("ordereas_db", $connection);

//Connect HOST V.2 (Secure Connect)
$db2 = new mysqli('localhost', 'root', '', 'ordereas_db');
// $db2 = new mysqli('localhost', 'ordereas', '6N1tjRj40l', 'ordereas_db');

if($db2->connect_errno > 0){
    die('Unable to connect to database [' . $db2->connect_error . ']');
}

$db2->set_charset('utf8');

?>