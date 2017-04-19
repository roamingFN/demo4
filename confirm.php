<?php

include_once 'connect.php';
include_once 'inc/php/functions.php';

?>

<?php

//setup some variables
$action = array();
$action['result'] = null;

//check if the $_GET variables are present
	
//quick/simple validation
if(empty($_GET['email']) || empty($_GET['key'])){
	$action['result'] = 'error';
	$action['text'] = 'We are missing variables. Please double check your email.';			
}
		
if($action['result'] != 'error'){

	//cleanup the variables
	$email = mysql_real_escape_string($_GET['email']);
	$key = mysql_real_escape_string($_GET['key']);
	
	//check if the key is in the database
	$check_key = mysql_query("select * from `customer_confirm` where `email` = '$email' AND `confirm_key` = '$key' order by id desc LIMIT 1") or die(mysql_error());
	
	if(mysql_num_rows($check_key) != 0){
				
		//get the confirm info
		$confirm_info = mysql_fetch_assoc($check_key);
		
		//confirm the email and update the users database
		$update_users = mysql_query("update `customer` SET `active` = 1 where `customer_id` = '$confirm_info[user_id]' LIMIT 1") or die(mysql_error());
		//delete the confirm row
		$delete = mysql_query("delete from `customer_confirm` where `id` = '$confirm_info[customer_id]' LIMIT 1") or die(mysql_error());
		
		if($update_users){
						
			$action['result'] = 'success';
			$action['text'] = 'User has been confirmed. Thank-You!';
		
		}else{

			$action['result'] = 'error';
			$action['text'] = 'The user could not be updated Reason: '.mysql_error();;
		
		}
	
	}else{
	
		$action['result'] = 'error';
		$action['text'] = 'The key and email is not in our database.';
	
	}

}

?>

<html>
  <head>
    <?php include 'page_script.php';  ?>
  </head>
  <body>
    <?php include 'nav_bar.php';  ?>
	<div class="container" style="padding-top:50px;">

	<div class="jumbotron">
	<p><?php echo show_errors($action); ?></p>
	<p><a href="#" onclick="openLogin()">Click here to Login</a></p>
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

