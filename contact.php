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

        <div class="content">
            <div class="wrapper categories"><div class="inner center">
                <h1>ติดต่อ Order2Easy</h1>
            </div></div>
            <div class="wrapper maps">
                <div class="col3 middle">
                    <iframe height="400" style="width:100%;"
                      frameborder="0" style="border:0"
                      src="https://www.google.com/maps/embed/v1/place?key=AIzaSyDvDT_2HX-PW95Ua4vpd9886sEs2IXCaf0
                        &q=13.895246,100.450427" allowfullscreen>
                    </iframe>
                </div>
                <div class="col4 middle dark">
                    <h3>ติดต่อได้ที่</h3>
                    <p><i class="material-icons">home</i> 22/3 หมู่ที1   ตำบล ท่าอิฐ<br>อำเภอ ปากเกร็ด จังหวัด นนทบุรี  11120</p><br>
                    <p><i class="material-icons">person_pin</i> Line ID : order2easy</p><br>
                    <p><i class="material-icons">phone</i> มือถือ: 089-052-8899<br>&emsp;&nbsp;&nbsp;&nbsp;Office: 02-924-5850</p><br>
                    <p><i class="material-icons">email</i> cs@order2easy.com</p><br>
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

            $("div#dialog").dialog ({
              autoOpen : false
            });

        </script>
    </body>
</html>