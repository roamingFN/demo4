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
              $_SESSION['CX_login_code']=$customer_rows['customer_code'];
              $login_id = $customer_rows['customer_id'];

              //set customer session id
              $session_id = session_id();
              $update_customer_sessions = mysql_query("update customer set active_session = '$session_id' 
                where customer_id = '$login_id'");

              if (isset($_POST['login-remember'])) {
                  $year = time() + 31536000;
                  setcookie('remember_me', $_POST['email'], $year);
              }else if (!isset($_POST['login-remember'])) {
                  if(isset($_COOKIE['remember_me'])) {
                      $past = time() - 100;
                      setcookie(remember_me, gone, $past);
                  }
              }

              if (isset($_COOKIE['product_url'])) {

                //echo "found cookie";

                $product_url = $_COOKIE['product_url'];
                $product_img = $_COOKIE['product_img'];
                $product_name = $_COOKIE['product_name'];
                $product_price = $_COOKIE['product_price'];
                $product_size = $_COOKIE["product_size"];
                $product_color = $_COOKIE["product_color"];
                $product_quentity = $_COOKIE["product_quentity"];
                $shop_name = $_COOKIE["shop_name"];
                $source = $_COOKIE["source"];

                //echo " product_url:".$product_url;
                //echo " product_img:".$product_img;
                //echo " product_name:".$product_name;
                //echo " product_price:".$product_price;
                //echo " product_size:".$product_size;
                //echo " product_color:".$product_color;
                //echo " product_quentity:".$product_quentity;
                //echo " shop_name:".$shop_name;
                //echo " source:".$source;

                if (isset($_COOKIE['product_url'])) {

                  unset($_COOKIE['product_url']);
                  unset($_COOKIE['product_img']);
                  unset($_COOKIE['product_name']);
                  unset($_COOKIE['product_price']);
                  unset($_COOKIE['product_size']);
                  unset($_COOKIE['product_color']);
                  unset($_COOKIE['product_quentity']);
                  unset($_COOKIE['shop_name']);
                  unset($_COOKIE['source']);

                  setcookie('product_url', null, -1, '/');
                  setcookie('product_img', null, -1, '/');
                  setcookie('product_name', null, -1, '/');
                  setcookie('product_price', null, -1, '/');
                  setcookie('product_size', null, -1, '/');
                  setcookie('product_color', null, -1, '/');
                  setcookie('product_quentity', null, -1, '/');
                  setcookie('shop_name', null, -1, '/');
                  setcookie('source', null, -1, '/');

                }

                //ตรวจว่าสินค้ามีหรือยัง
                $item_exist = mysql_query("select * from product p, shopping_cart s 
                              where p.product_url='$product_url' 
                              and p.product_size='$product_size' 
                              and p.product_color='$product_color'
                              and p.product_id = s.product_id
                              and s.customer_id = '$login_id'", $connection);
                $item_exist_row = mysql_fetch_array($item_exist);
                $product_id = $item_exist_row['product_id'];

                if (mysql_num_rows($item_exist)==0) {
                  //เพิ่มสินค้าใหม่
                  $add_product = mysql_query("insert into product(product_url,product_img,product_name,product_color,product_size,product_price,shop_name,source)
                                values('$product_url','$product_img','$product_name','$product_color','$product_size','$product_price','$shop_name','$source')");
                  //เพิ่มสินค้าในต้กร้า
                  $product_id = mysql_insert_id();;
                  $add_cart_item = mysql_query("insert into shopping_cart(product_id,customer_id,cart_quantity,cart_date)
                                  values('$product_id','$login_id','$product_quentity',now())");
                  //echo "เพิ่มสินค้าลงในตะกร้าเรียบร้อย <br />";

                }else if (mysql_num_rows($item_exist)==1) {
                  //สินค้ามีอยู่แล้ว อัพเดทข้อมูลสินค้าในตะกร้า
                  $update_product_id = $item_exist_row['product_id'];
                  $product_in_cart = mysql_query("select cart_quantity from shopping_cart where product_id='$update_product_id'");
                  $product_in_cart_row = mysql_fetch_array($product_in_cart);
                  $current_product_qty = $product_in_cart_row['cart_quantity'];
                  $update_product_qty = $current_product_qty + $product_quentity;

                  if (mysql_num_rows($product_in_cart) > 0) {
                    $update_cart_item = mysql_query("update shopping_cart set cart_quantity = $update_product_qty, cart_date = now() 
                                    where product_id = '$update_product_id' and customer_id = '$login_id' ");
                    //echo "สินค้ามีอยู่แล้วในตะกร้า ระบบได้เพิ่มจำนวนสินค้าจากที่มีอยู่<br />";
                  }else{
                    $add_cart_item = mysql_query("insert into shopping_cart(product_id,customer_id,cart_quantity,cart_date)
                                    values('$product_id','$login_id','$product_quentity',now())");
                    //echo "เพิ่มสินค้าลงในตะกร้าเรียบร้อย <br />";
                  }
                }
                header('Location: cart.php');
                
              }else{
                //echo "cookie not set";
              }

              header('Location: order_list.php');

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
    <style type="text/css">
      footer{ width:100%;}
    </style>
  </head>
  <body>
    <?php include 'nav_bar.php';  ?>


        <div class="content">
            <ul class="slippry">
                <li><a href="#"><img src="images/slippry-01.jpg" alt="คำอธิบาย1"></a></li>
                <li><a href="#"><img src="images/slippry-02.jpg" alt="คำอธิบาย2"></a></li>
                <li><a href="#"><img src="images/slippry-03.jpg" alt="คำอธิบาย3"></a></li>
            </ul>
            <div class="wrapper"><div class="inner center">
                <div class="col3">
                    <h3>เลือกซื้อจากเว็บไซต์</h3>
                    <div id="shops">
                        <div><a target="_blank" href="https://www.1688.com"><img src="images/shops/1688.png"></a></div>
                        <div><a target="_blank" href="http://www.amazon.cn"><img src="images/shops/amazon.png"></a></div>
                        <div><a target="_blank" href="http://www.dangdang.com"><img src="images/shops/dangdang.png"></a></div>
                        <div><a target="_blank" href="http://www.jd.com"><img src="images/shops/jd.png"></a></div>
                        <div><a target="_blank" href="https://www.taobao.com"><img src="images/shops/taobao.png"></a></div>
                        <div><a target="_blank" href="https://www.tmall.com"><img src="images/shops/tmall.png"></a></div>
                    </div>
                    ท่านสามารถเลือกสินค้าจากเว็บไซต์ข้างบนนี้ แล้วนำ URL มาวางที่หน้าเว็บของเรา เพื่อสั่งซื้อสินค้าได้เลย<br>
                    <a href="#" class="focus">อ่านวิธีการสั่งซื้อสินค้า คลิ๊ก!!</a>
                </div>
                <div class="col4"><br>
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
            </div></div>
            <div class="wrapper focus"><div class="inner big">
                <div id="recommended"/>
                    <?php 
                        //query featured item
                        $featured_items = mysql_query("select * from website_featured_item where featured_item_type='featured' limit 4");
                        while ( $featured_item = mysql_fetch_array($featured_items) ) {
                            echo '
                            <div class="pick big">
                                <a href="'.$featured_item['featured_item_link'].'" class="big-img" src="'.$featured_item['featured_item_img'].'" target="_black"></a>
                                <div class="big-detail">
                                    <h4>ชื่อสินค้า</h4><p>from Taobao</p>
                                    <p>'.$featured_item['featured_item_name'].'
                                    </p>
                                    <b>'.$featured_item['featured_item_price'].' ¥</b>'; ?>
                                    <button onclick="itemURL('<?php echo $featured_item['featured_item_link']; ?>');"><i class="material-icons">add_shopping_cart</i> ซื้อ</button>
                            <?php
                            echo '
                                </div>
                            </div>
                            ';
                            
                        }
                    ?>
                </div>
                <a href="#" class="more big">ดูทั้งหมด</a>
            </div></div>
            <div class="wrapper categories"><div id="categories" class="inner">
                <h1>หมวดหมู่สินค้า</h1>
                <p>มีสินค้ามากกว่า 12 ประเภท ให้คุณได้เลือกชม</p>
                <?php
                  $query_cate = mysql_query("select * from website_featured_cate");
                  if (mysql_num_rows($query_cate)>0) {
                    while ( $row = mysql_fetch_array($query_cate) ) {
                      echo '<a href="'.$row['featured_cate_link'].'" class="cat" target="_blank"><i class="material-icons">'.$row['featured_cate_text_icon'].'</i>'.$row['featured_cate_name'].'</a>';
                    }
                  }
                ?>
            </div></div>
            <div class="wrapper"><div class="inner">
                <h3>เครื่องแต่งกายผู้หญิง</h3><a href="#" class="more">ดูทั้งหมด</a>
                <?php 
                    //query featured item
                    $featured_items = mysql_query("select * from website_featured_item where featured_item_type='women' limit 3");
                    while ( $featured_item = mysql_fetch_array($featured_items) ) {
                        echo '
                        <div class="pick">
                            <a href="'.$featured_item['featured_item_link'].'" class="pick-img" src="'.$featured_item['featured_item_img'].'"></a>
                            <h4>ชื่อสินค้า</h4><p>'.$featured_item['featured_item_name'].'</p>
                            <b>'.$featured_item['featured_item_price'].' ¥</b>'; ?>

                            <button onclick="itemURL('<?php echo $featured_item['featured_item_link']; ?>');"><i class="material-icons">add_shopping_cart</i></button>
                        <?php
                        echo '
                        </div>
                        ';
                    }
                ?>
                <br><br><br>
                <h3>เครื่องแต่งกายผู้ชาย</h3><a href="#" class="more">ดูทั้งหมด</a>
                <?php 
                    //query featured item
                    $featured_items = mysql_query("select * from website_featured_item where featured_item_type='men' limit 3");
                    while ( $featured_item = mysql_fetch_array($featured_items) ) {
                        echo '
                        <div class="pick">
                            <a href="'.$featured_item['featured_item_link'].'" class="pick-img" src="'.$featured_item['featured_item_img'].'"></a>
                            <h4>ชื่อสินค้า</h4><p>'.$featured_item['featured_item_name'].'</p>
                            <b>'.$featured_item['featured_item_price'].' ¥</b>'; ?>

                            <button onclick="itemURL('<?php echo $featured_item['featured_item_link']; ?>');"><i class="material-icons">add_shopping_cart</i></button>
                        <?php
                        echo '
                        </div>
                        ';
                    }
                ?>
            </div></div>
            <div class="wrapper focus"><div class="inner">
                <h3>วิธีการสั่งซื้อสินค้า</h3>
            </div></div>
        </div>

        <?php include 'modal.php';  ?>
        <?php include 'footer.php';  ?>

        <?php if ($error!='') { ?>
            <script type="text/javascript">
                $(document).ready(function(){ swal("Login Failed", "The username or password is incorrect, Please try again.", "error"); }) 
            </script>
        <?php } ?>

        <?php if (isset($_GET['error'])) {
                if ($_GET['error'] == 'sessions_expire') { ?>
            <script type="text/javascript">
                $(document).ready(function(){ swal("Session Expired", "You are currently logged in on another browser. Please continue to use the other window and close this one.", "info"); }) 
            </script>
        <?php } 
        } ?>

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