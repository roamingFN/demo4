<?php
include 'connect.php';
include 'session.php';

if(isset($_GET['action'])){
	switch($_GET['action']){
		case 'activeLink':activeLink($_GET['orderId']); break;
		case 'activeLinkPk':activeLinkPk($_GET['packageid']); break;
	}
	
}

function activeLink($param=''){
	if($param!=0 || trim($param)>0){
	   /**
	    * update total_message_log-> active_link = 1 where eid =$param
	    */
		$sql='update total_message_log set active_link =0 where order_id ='.$param;
		mysql_query($sql);
		$sql_select = 'select count(eid) as sizeOf from total_message_log where active_link <> 0 and order_id = '.$param;
		$result_activeLink = mysql_query($sql_select);
		$result_activeLink_arr = array();
		while($row=mysql_fetch_assoc($result_activeLink)){
			$result_activeLink_arr[]=$row;
		}
		echo json_encode($result_activeLink_arr);
	}else{
		echo json_encode(array('sizeOf'=> -1));
	}
}

function activeLinkPk($param=''){
	if($param!=0 || trim($param)>0){
		/**
		 * update total_message_log-> active_link = 1 where eid =$param
		 */
		$sql='update total_message_log set active_link =0 where packageid ='.$param;
		mysql_query($sql);
		$sql_select = 'select count(eid) as sizeOf from total_message_log where active_link <> 0 and packageid = '.$param;
		$result_activeLink = mysql_query($sql_select);
		$result_activeLink_arr = array();
		while($row=mysql_fetch_assoc($result_activeLink)){
			$result_activeLink_arr[]=$row;
		}
		echo json_encode($result_activeLink_arr);
		
	}else{
		echo json_encode(array('sizeOf'=> -1));
	}
}

//for order only
if(isset($_POST['frmMsgSend'])){
	$customerId=$_SESSION['CX_login_id'];
	$message=$_POST['frmMsgSend'];
	$orderId=$_POST['orderId'];
	
	
	$sql='insert into total_message_log (order_id,order_product_id,customer_id,topup_id,user_id,subject,content,message_date,active_link)';
	//$sql.=' values(?,?,?,?,?,?,?,?,?)';
	$sql.='values('.$orderId.',0,'.$customerId.',0,"0","'.mysql_real_escape_string($message).'","'.mysql_real_escape_string($message).'",NOW(),0)';
	//echo $sql;
	$sqlQuery=mysql_query($sql);
	$totalMessageLog = mysql_insert_id();
	$sql='select * from total_message_log where eid='.$totalMessageLog;
	$selectMessageLog = mysql_query($sql);
	$totalMessageArr=array();
	while($row=mysql_fetch_assoc($selectMessageLog)){
		$totalMessageArr[]=$row;
	}
	
	if(!empty($totalMessageLog)){
		echo json_encode($totalMessageArr);
	}else{
		echo "n";
	}	
}

//for order only
if(isset($_POST['frmMsgSendPk'])){
	$customerId=$_SESSION['CX_login_id'];
	$message=$_POST['frmMsgSendPk'];
	$packageid=$_POST['packageId'];


	$sql='insert into total_message_log (packageid,order_product_id,customer_id,topup_id,user_id,subject,content,message_date,active_link)';
	//$sql.=' values(?,?,?,?,?,?,?,?,?)';
	$sql.='values('.$packageid.',0,'.$customerId.',0,"0","'.mysql_real_escape_string($message).'","'.mysql_real_escape_string($message).'",NOW(),0)';
	//echo $sql;
	$sqlQuery=mysql_query($sql);
	$totalMessageLog = mysql_insert_id();
	$sql='select * from total_message_log where eid='.$totalMessageLog;
	$selectMessageLog = mysql_query($sql);
	$totalMessageArr=array();
	while($row=mysql_fetch_assoc($selectMessageLog)){
		$totalMessageArr[]=$row;
	}

	if(!empty($totalMessageLog)){
		echo json_encode($totalMessageArr);
	}else{
		echo "n";
	}
}

?>