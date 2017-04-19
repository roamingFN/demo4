<?php
	
	session_start();
	if (isset($_SESSION['CX_login_user'])) {
		// Storing Session
		$email_check = $_SESSION['CX_login_user'];
		// SQL Query To Fetch Complete Information Of User
        $session_id = session_id();
		$ses_sql = mysql_query("select customer_email, customer_id from customer 
			where customer_email='$email_check' and active_session = '$session_id'", $connection);
		$row = mysql_fetch_assoc($ses_sql);
		$login_session =$row['customer_email'];
		$user_id =$row['customer_id'];
		
		if(!isset($login_session)){
			session_unset();
			header('Location: index.php?error=sessions_expire'); // Redirecting To Home Page
		}
	}else{
		header('Location: index'); // Redirecting To Home Page
	}


?>