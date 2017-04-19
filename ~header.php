<!DOCTYPE html>
<html lang="en">
<head>
  <title>China Express : Index</title>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet" href="bootstrap/css/bootstrap.css">

  <link rel="stylesheet" type="text/css" href="css/style.css">

  <link href="slippry/dist/slippry.css" rel="stylesheet"/>
  <script src="slippry/dist/slippry.min.js"></script>

  <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>

  <script src="slick/slick.min.js"></script>
  <link href="slick/slick.css" rel="stylesheet"/>
  <link href="slick/slick-theme.css" rel="stylesheet"/>

  <script charset="utf-8" src="js/page.js"></script>

  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <script charset="utf-8" src="bootstrap/js/bootstrap.min.js"></script>

  <meta name="viewport" content="width=device-width">

  <script src="js/core.js"></script>

  <!-- Date Picker -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>


  <!--<link rel="stylesheet" href="http://cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css"></style>-->
  <!--<script type="text/javascript" src="http://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>-->
  <!--<script src="js/jquery-1.11.3.min.js"></script>-->
</head>
<body>
    <div class="header">
      <a href="index.php"><img id="logo" src="images/logo.png"/></a>
      <div class="header-menu">
        <div>
          <i class="material-icons">search</i><input id="searchText" style="width:650px" placeholder="ค้นหา หรือ คัดลอกลิ้งสินค้าวางที่นี่..." onkeypress="return runScript(event)">
        </div>
        <a class="clickable"><i class="material-icons">shopping_cart</i>
          <span class="badge">
            <?php 
                if (isset($_SESSION['CX_login_user'])) {
                  $user_id = $_SESSION['CX_login_id'];
                  $select_cart_item = mysql_query("select count(cart_id) from shopping_cart where customer_id = '$user_id'");
                  $select_cart_item_row = mysql_fetch_array($select_cart_item);
                  echo $select_cart_item_row['count(cart_id)']; 
                }else {
                  echo "0";
                }

            ?>
          </span>
        </a>
        <a class="clickable">รายการสั่งซื้อ
          <span class="badge">
            <?php 
                if (isset($_SESSION['CX_login_user'])) {
                  $user_id = $_SESSION['CX_login_id'];
                  $select_order_item = mysql_query("select count(order_id) from customer_order where customer_id = '$user_id'");
                  $select_order_item_row = mysql_fetch_array($select_order_item);
                  echo $select_order_item_row['count(order_id)']; 
                }else{
                  echo "0";
                }
            ?>
          </span>
        </a>
        <?php
          if (isset($_SESSION['CX_login_user']) && !empty($_SESSION['CX_login_user'])) {
            echo '
              <div class="header-right">
                <a class="clickable focus" onclick="openProfile();">'.$_SESSION['CX_login_name'].'</a>
              </div>
            </div>
          </div>
          <div id="profile" class="header-dialog-wrap">
            <div class="header-dialog">
                <a href="profile.php"><p>ข้อมูลส่วนตัว</p></a><br />
                <a href="change_password.php"><p>เปลี่ยนรหัสผ่าน</p></a><br />
                <a href="fav_shop.php"><p>ร้านค้าที่ชื่นชอบmmm</p></a><br />
                <hr />
                <a href="logout.php"><p>ออกจากระบบ</p></a><br />
            </div>
          </div>
            ';
          }else{
            echo '
              <div class="header-right">
                <a class="clickable focus" onclick="openRegister();">สมัครสมาชิก</a>
                <a class="clickable focus" onclick="openLogin();">เข้าสู่ระบบ</a>
              </div>
            </div>
          </div>
          <div id="login" class="header-dialog-wrap">
            <div class="header-dialog">
              <form>
                <h3 class="focus">เข้าสู่ระบบ Order<p class="orange">2</p>Easy</h3>
                <input placeholder="อีเมล"/>
                <input placeholder="รหัสผ่าน"/>
                <button>เข้าใช้งาน</button>
              </form>
            </div>
          </div>
          <div id="register" class="header-dialog-wrap">
            <div class="header-dialog">
              <form>
                <h3 class="focus">สมัครสมาชิก Order<p class="orange">2</p>Easy</h3>
                <input placeholder="อีเมล"/>
                <input placeholder="รหัสผ่าน"/>
                <input placeholder="ชื่อ"/>
                <input placeholder="นามสกุล"/>
                <input placeholder="หมายเลขโทรศัพท์"/>
                <button>สมัครเดี๋ยวนี้</button>
              </form>
            </div>
          </div>
            ';
          }
        ?>
         



