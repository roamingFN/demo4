<?php
include 'connect.php';
session_start();
//include 'session.php';

//#login part
$error=''; // Variable To Store error Message
$error_message='';
$loginfail = false;

if (isset($_GET['error_message'])) {
    $error_message=$_GET['error_message'];
}

if (isset($_POST['submit'])) {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $loginfail = true;
        $error = "Username or Password is invalid";
    }
    else
    {
        // Define $email and $password
        $email=$_POST['email'];
        $password=$_POST['password'];

        // To protect MySQL injection for Security purpose
        $email = stripslashes($email);
        $password = stripslashes($password);
        $email = mysql_real_escape_string($email);
        $password = mysql_real_escape_string($password);

    //encode password
        $password = sha1($password);

        // SQL query to fetch information of registerd users and finds user match.
        $login = mysql_query("select * from customer where passwd='$password' AND customer_email='$email'", $connection);

        if (mysql_num_rows($login) > 0) {

            //echo "login success";

            $customer_rows = mysql_fetch_array($login);
            if ($customer_rows['active'] == 1) {

                $_SESSION['CX_login_user']=$email;
                $_SESSION['CX_login_name']=$customer_rows['customer_firstname']." ".$customer_rows['customer_lastname'];
                $_SESSION['CX_login_id']=$customer_rows['customer_id'];

                if (isset($_POST['login-remember'])) {
                    $year = time() + 31536000;
                    setcookie('remember_me', $_POST['email'], $year);
                }else if (!isset($_POST['login-remember'])) {
                    if(isset($_COOKIE['remember_me'])) {
                        $past = time() - 100;
                        setcookie(remember_me, gone, $past);
                    }
                }

            }else{
                $loginfail = true;
                $error = "Please confirmation your email in <strong>". $customer_rows['customer_email'] ."</strong>";
            }
            
        } else {
            $loginfail = true;
            $error = "Username or Password is invalid";
        }
    }
}
//include 'modal.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <?php include 'page_script.php';  ?>
  </head>
  <body>
    <?php include 'nav_bar.php';  ?>
    <?php
        $query_rate = mysql_query("select * from website_rate order by starting_date desc");
    ?>
		<div class="content">
			<div class="wrapper categories"><div class="inner center">
				<h1>อัตราแลกเปลี่ยนย้อนหลัง</h1><br>
				<div class="col2">
					<table class="shipping center">
						<thead style="background:#ff5722;"><th>วันที่</th><th>Rate &#8597;</th></thead>
						<?php
						if(mysql_num_rows($query_rate) > 0){ 
							while($query_rate_row = mysql_fetch_array($query_rate)) {
								echo '<tr><td>'.date("d/m/Y", strtotime($query_rate_row['starting_date'])).
								'</td><td><b>'.$query_rate_row['rate_cny'].'</b></td></tr>';
							}
						}
						?>
					</table>
				</div>
				<div class="col4">
					<br>
					<h3>อัตราแลกเปลี่ยนปัจจุบัน</h3>
					<div class="rated">
                        <h3><i class="material-icons">trending_up</i>ปรับเรท&nbsp;</h3>
                        <?php
                            $query_rate = mysql_query("select * from website_rate order by starting_date desc limit 1");
                            $query_rate_row = mysql_fetch_array($query_rate);
                        ?>
                        <h1><strong><?php echo $query_rate_row['rate_cny']; ?></strong></h1>
                        เริ่ม <?php echo date("d/m/Y", strtotime($query_rate_row['starting_date'])); ?>
                    </div>

				</div>
				<br>
			</div></div>
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
    </body>
</html>