<style type="text/css">
	.header-dialog {
		width: 300px;

    /*แก้ไขเรื่อง scroll กับความสูงกล่อง*/
		/*max-height: 320px;
    	overflow-y: scroll;*/
    	
	}
	.header-dialog *{
		position:relative;
	}
	.forgetpass{
		float:left;
	}
	.notify{
		background-color:#f00;
	}
	
</style>

<script type="text/javascript">


	function activeLink(id,op){
		//alert('');
		// unread is zero active link
		//read is 1 active link
		if(op=='o'){
			if(id !=0 || id.lenght >0){
				$.get("./message-do.php",{orderId:id,action:"activeLink"},function(data){

         var objData=JSON.parse(data);
         if(objData[0].sizeOf==0){              
              window.location.replace('order_show_detail_confirmed.php?order_id='+id+'&msg=view');
            return true;
         }else{
            return false;
         }

				});
			}
		}else{
			if(id !=0 || id.lenght >0){
				$.get("./message-do.php",{packageid:id,action:"activeLinkPk"},function(data){
					var objData=JSON.parse(data);
         if(objData[0].sizeOf==0){              
              window.location.replace('package_detail.php?packageid='+id+'&msg=view');
            return true;
         }else{
            return false;
         }
				});
			}
		}

	}
</script>
<div class="header">
  <div class="header-menu">
    <div>
      <a id="logo" href="index"></a>
      <input id="searchText" type="search" placeholder="ค้นหา หรือ คัดลอกลิงค์สินค้าวางที่นี่..." style="vertical-align:middle;height:30px;line-height:150%;color:#000000;" onkeypress="return runScript(event)">
      <button id="search-btn" onclick="searchURL();"><i class="material-icons">search</i></button>
      <button id="clear-btn" class="menu-focus" onclick="clearURL();"><i class="material-icons">close</i></button>
      <button id="more-btn"><i class="material-icons">more_vert</i></button>
    </div>
    <div id="header-group">
      <a class="clickable" href="cart"><i class="material-icons">shopping_cart</i>
        <span class="badge" id="item_badge">
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
      <a class="clickable" href="order_list">รายการสั่งซื้อ
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
      
      <!-- Insert notification by Hack -->
      <a class="clickable" href="javascript:;" onClick="openNofication();">
      	<i class="material-icons">question_answer</i>
      	<span class="badge notify" id="question_answer">
          <?php
          if (isset($_SESSION['CX_login_user'])) {
          	$sizeOfMessage=0;
            $user_id = $_SESSION['CX_login_id'];
            /*$sql='select count(eid) from total_message_log
				where eid in (
				select max(eid) 
				from total_message_log
				where customer_id = '.$user_id.' 
				and topup_id=0 
				and active_link=1
				and order_id!=0
				order by eid desc )';
				*/
            //select count with order id not zero 
            $sql='SELECT count(eid)
			     FROM total_message_log
			     WHERE customer_id = '.$user_id.' 
			       AND topup_id=0
			       AND active_link=1
			       AND order_id!=0
				   AND packageid=0';
            //echo $sql;
            $select_order_item = mysql_query($sql);
            $select_order_item_row = mysql_fetch_array($select_order_item);
            
           //echo $select_order_item_row['count(eid)'];  // order id number of message header 
            $sizeOfMessage=$select_order_item_row['count(eid)'];
            
            
           //Get count of package id.
           //Active link is one that is unread message.
            /*$sqlpackageId='select count(eid) from total_message_log
				where eid in (
				select max(eid)
				from total_message_log
				where customer_id = '.$user_id.'
				and topup_id=0
				and active_link=1
      			and packageid!=0
				order by eid desc )';
				*/
            $sqlpackageId='SELECT count(eid) as CPK FROM total_message_log WHERE customer_id = '.$user_id.' AND topup_id=0 AND active_link=1 AND order_id=0 AND packageid!=0';
           // echo $sqlpackageId;
            $select_order_item = mysql_query($sqlpackageId);
            $select_order_item_row = mysql_fetch_array($select_order_item);
            //echo $select_order_item_row['count(eid)'].'p';  //number of message header (packageid)
            
            $sizeOfMessage=($sizeOfMessage+$select_order_item_row['CPK']);
            echo $sizeOfMessage;
           
           
          }else{
            echo "0";
          }
          ?>
        </span>
      </a>
      <!-- End notification by Hack -->
      
      <a href="cart" class="cart"><i class="material-icons">shopping_cart</i></a>
      <a href="order_list" class="list"><i class="material-icons">view_list</i></a>
      <?php
    if (isset($_SESSION['CX_login_user']) && !empty($_SESSION['CX_login_user'])) {
    	$totalMessageArr=array();
    	$user_id = $_SESSION['CX_login_id'];
    	/*$sql='select * from total_message_log TML
left join customer_order CO on CO.order_id=TML.order_id
left join user U on U.userid=TML.user_id
where TML.customer_id='.$user_id.'
and TML.topup_id=0 order by eid desc limit 1';*/
    	//sql 2-16
    	$sql='select T1.* from (
				select TML.*,CO.order_number,P.packageno from 
				total_message_log TML 
				left join customer_order CO on CO.order_id=TML.order_id 
				left join user U on U.userid=TML.user_id 
				left join package P ON P .packageid=TML.packageid
				where TML.customer_id='.$user_id.' 
				and TML.topup_id=0 
				order by eid desc ) T1
				where T1.active_link=1
				order by T1.eid desc;';  	
    	
    	$select_order_item = mysql_query($sql);
    	while($row=mysql_fetch_assoc($select_order_item)){
    		$totalMessageArr[]=$row; // หา orderid and packageid แสดงรายละเอียดข้อความ
    	}
    	
      echo '
      <div class="header-right">
        <table style="line-height:0px;">
        <tr>
          <td style="line-height:0px; padding:0;font-size: 13px;">
            <a style="line-height:20px; padding-top:15px;" class="user" onclick="openSetting();">ชื่อ '.$_SESSION['CX_login_name'].'
              <br><span style="line-height:20px;">รหัสสมาชิก '.$_SESSION['CX_login_code'].'</span>
            </a>
          </td>
          <td><i class="material-icons" >account_circle</i></td>
        </tr>
      </table>
      </div>
    </div> <!-- close header group -->
  </div> <!-- close header menu -->
</div>
<div id="setting" class="header-dialog-wrap">
  <div class="header-dialog">
    <a href="profile">ข้อมูลส่วนตัว</a>
    <a href="change_password">เปลี่ยนรหัสผ่าน</a>
    <a href="fav_shop">ร้านค้าที่ชื่นชอบ</a>
    <div class="header-line"></div>
    <a href="logout"><i class="material-icons">exit_to_app</i> ออกจากระบบ</a>
  </div>
</div>';
     
     if(count($totalMessageArr)>0){
     	/**
     	 * find number message unread where active_link=0 and order_id=477
     	 * $sql='select count(eid) from total_message_log where active_link=0 and order_id=? and customer_id=118';
     	 * 
     	 */
     	
     	//echo count($totalMessageArr).'sd';
     	
     	
     	//echo $selectCountUnRead['count(eid)'];
	echo '<div id="notify" class="header-dialog-wrap"><div class="header-dialog"><ul>';

		foreach($totalMessageArr as $val){?> 
				    <li style="padding: 0px;">
				      <div style="padding: 0px;">
				      <?php if(is_null($val['order_number'])){?>
				      		<a style="padding: 0px;" href="javascript:;" onClick="activeLink('<?php echo $val['packageid'];?>','pk');">
				      <?php }else{?>
				      		<a style="padding: 0px;" href="javascript:;" onClick="activeLink('<?php echo $val['order_id'];?>','o');">
				      <?php }?>
				        
				          <div style="padding: 5px 0px; border-bottom: 1px solid rgb(221, 221, 221);">
				            <div style="padding: 0px;">
				              <div style="padding: 0px; font-size: 15px;display: inline;">
				               <span style="display: inline; font-weight: bold; padding: 0px;"></span> 
				               <?php //echo $val['order_number'];
				               if(is_null($val['order_number'])){
				               		echo $val['packageno'];
				               }else{
				               	echo $val['order_number'];
				               }
				               
				               $sqlTotalUnread='select count(eid)  from total_message_log where active_link=0 and order_id='.$val['order_id'].' and customer_id='.$user_id;
				               //echo $sqlTotalUnread;
				               $sqlTotalUnreadQ = mysql_query($sqlTotalUnread);
				               $selectCountUnRead = mysql_fetch_array($sqlTotalUnreadQ);
				               	 if($selectCountUnRead['count(eid)']!=0){
				               	 	//echo ' ('.$selectCountUnRead['count(eid)'].')';
				               	 }
				               
				               ?>
				               </div>
				              <div style="padding-top: 0px; padding-right: 0px; padding-bottom: 0px; color: rgb(153, 153, 153); display: inline; font-size: 11px; position: absolute; right: 5px; top: 2px;"><?php echo messageTime($val['message_date']);?></div>
				            	<div style="padding: 0px; color: rgb(144, 147, 156); font-size: 13px;">
				               <?php 
				               $strContent=strip_tags($val['subject']);
				               $strContentTmp='';
				               if(strlen($strContent)>0 && !is_null($strContent)){
				               		if(strlen($strContent)>320){
				               			
				               			$strContentTmp=iconv_substr($strContent, 0,120,'UTF-8').'...';
				               		}else{
				               			$strContentTmp=$strContent;
				               		}
				               }
				               //$cutstr=substr(strip_tags($val['subject']),0,25);
				               //echo strip_tags($val['subject']);
				               echo $strContentTmp;
				               /*ตัดคำให้สั้น*/?>
				               </div>
				            </div>
				          </div>
				        </a>
				      </div>
				    </li>
		<?php }
		echo '</ul></div></div>';
	}
	
    }else{
      echo '
      <div class="header-right">
        <a onclick="openRegister();">สมัครสมาชิก</a>
        <a onclick="openLogin();">เข้าสู่ระบบ</a>
      </div>
    </div>
  </div>
</div>
<div id="login" class="header-dialog-wrap">
  <div class="header-dialog">
    <form action="index" method="post" onsubmit="return checkLogin()">
      <h3 class="focus">เข้าสู่ระบบ Order<p class="orange">2</p>Easy</h3>

      <label class="control-label" id="help-login-email" style="color:red;"></label>
      <input placeholder="อีเมล" name="email" id="login-email" style="color:black" />

      <label class="control-label" id="help-login-password" style="color:red;"></label>
      <input placeholder="รหัสผ่าน" name="password" id="login-password" type="password" style="color:black" />
      <div id="line" style="width:100%;height:5px;"></div>
      <button name="submit" type="submit">เข้าใช้งาน</button>
      <a href="#" class="forgetpass" onclick="forgetpass()">ลืมรหัสผ่าน</a> 
    </form>
  </div>
</div>
<div id="register" class="header-dialog-wrap">
  <div class="header-dialog">
    <form id="signupform" action="register" method="post" onsubmit="return validateRegisterForm()">
      <h3 class="focus">สมัครสมาชิก Order<p class="orange">2</p>Easy</h3>

      <label class="control-label" id="help-register-email" style="color:red;"></label>
      <input name="email" id="register-email"  placeholder="อีเมล" style="color:black"/>

      <label class="control-label" id="help-register-password" style="color:red;"></label>
      <input name="password" id="register-password"  placeholder="รหัสผ่าน" type="password" style="color:black"/>

      <label class="control-label" id="help-register-firstname" style="color:red;"></label>
      <input name="firstname" id="register-firstname" placeholder="ชื่อ" style="color:black"/>

      <label class="control-label" id="help-register-lastname" style="color:red;"></label>
      <input name="lastname" id="register-lastname" placeholder="นามสกุล" style="color:black"/>

      <label class="control-label" id="help-register-phone" style="color:red;"></label>
      <input name="phone" onkeypress="return isPhoneNumber(event)" id="register-phone" placeholder="หมายเลขโทรศัพท์" style="color:black"/>
      <div id="line" style="width:100%;height:5px;"></div>
      <button type="submit" name="signup">สมัครเดี๋ยวนี้</button>
      <a href="#" class="forgetpass" onclick="forgetpass()">ลืมรหัสผ่าน</a> 
    </form>
  </div>
</div>
';
}

function messageTime($param){
	date_default_timezone_set("Asia/Bangkok");
	$currentTime=time();
	
	if(!empty($param)){
		$remainTime=$currentTime-strtotime($param);
		if($remainTime<3600){
			return intval(date('i',$remainTime)).' นาทีที่แล้ว';
		}else if($remainTime>3600 && $remainTime <=7200){
			return 'ประมาณ 1 ชั่วโมงที่แล้ว';
		}else if($remainTime>7200 && $remainTime<=86400){
			return floor($remainTime/3600).' ชั่วโมงที่แล้ว';
		}else if($remainTime>86400 && $remainTime<=172800){
			//echo $remainTime;
			
			return 'เมื่อวานนี้ เวลา '.date('H:i:s',strtotime($param)).' น.';
		}else{
			
			return DateThai($param);
		}
	}
}

function DateThai($strDate)
{
	date_default_timezone_set("Asia/Bangkok");
	$strYear = date("Y",strtotime($strDate))+543;
	$strMonth= date("n",strtotime($strDate));
	$strDay= date("j",strtotime($strDate));
	$strHour= date("H",strtotime($strDate));
	$strMinute= date("i",strtotime($strDate));
	$strSeconds= date("s",strtotime($strDate));
	$strMonthCut = Array("","มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฎาคม","สิงหาคม","กันยายน","ตุลาคม","พฤษจิกายน","ธันวาคม");
	$strMonthThai=$strMonthCut[$strMonth];
	return "$strDay $strMonthThai $strYear เวลา  $strHour:$strMinute น.";
}
?>
