<?php
	include 'connect.php';
	include 'session.php';
	include 'header.php';
	include 'modal.php';
?>

<div class="container"> 

    <div>
        <img src="img/logo.png" class="img-responsive center-block">
    </div>
    <nav class="navbar navbar-default">
        <ul class="nav navbar-nav">
            <li><a class="text-center" href="index.php"><span class="glyphicon glyphicon-home" style="font-size:2em;"></span><br>หน้าแรก</a></li>
            <li><a class="text-center" href="#"><span class="glyphicon glyphicon-bullhorn" style="font-size:2em;"></span><br>เกี่ยวกับเรา</a></li>
            <li><a class="text-center" href="#"><span class="glyphicon glyphicon-list-alt" style="font-size:2em;"></span><br>วิธีใช้งาน</a></li>
            <li><a class="text-center" href="shopping.php"><span class="glyphicon glyphicon-shopping-cart" style="font-size:2em;"></span><br>สั่งสินค้า</a></li>
            <li><a class="text-center" href="#"><span class="glyphicon glyphicon-plane" style="font-size:2em;"></span><br>ค่าขนส่ง</a></li>
            <li><a class="text-center" href="#"><span class="glyphicon glyphicon-exclamation-sign" style="font-size:2em;"></span><br>เงื่อนไขการใช้งาน</a></li>
            <li><a class="text-center" href="#"><span class="glyphicon glyphicon-question-sign" style="font-size:2em;"></span><br>ถามตอบ</a></li>
            <li><a class="text-center" href="#"><span class="glyphicon glyphicon-heart-empty" style="font-size:2em;"></span><br>แนะนำร้านค้า</a></li>
            <li><a class="text-center" href="#"><span class="glyphicon glyphicon-phone-alt" style="font-size:2em;"></span><br>ติดต่อเรา</a></li>
        </ul>
    </nav>

    <div class="row">
        <div class="col-md-12">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search for..." id="searchtext" name="searchtext" >
                <span class="input-group-btn">
                    <button class="btn btn-default" type="button" id="search_button" name="search_button" onclick="searchURL();">
                    Go!</button>
                </span>
            </div>
            <a class="btn btn-default" onclick="manualAdd();">สั่งซื้อสินค้าแบบเก่า</a>
        </div>
        <div style="padding-top:20px;"> &nbsp</div>

        <div class="col-md-3" style="height:300px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">เสื้อผ้าผู้หญิง/ผู้ชาย</h3>
              </div>
              <div class="panel-body">
                <ul>
                    <li><a href="http://list.taobao.com/itemlist/nvzhuang2011a.htm?spm=0.0.0.0.InyG1j&cat=16" target="_blank">เสื้อผู้หญิง</a></li>
                    <li><a href="http://list.taobao.com/itemlist/nvzhuang2011a.htm?spm=0.0.0.0.InyG1j&cat=16" target="_blank">กางเกงผู้หญิง</a></li>
                    <li><a href="http://list.taobao.com/itemlist/nvzhuang2011a.htm?spm=0.0.0.0.InyG1j&cat=16" target="_blank">กระโปรง</a></li>
                    <li><a href="http://list.taobao.com/itemlist/nvzhuang2011a.htm?spm=0.0.0.0.InyG1j&cat=16" target="_blank">เสื้อผ้าแบบอื่น</a></li>
                    <li><a href="http://list.taobao.com/itemlist/nanzhuang2011a.htm?spm=0.0.0.0.InyG1j&cat=30" target="_blank">เสื้อผู้ชายหน้าร้อน</a></li>
                    <li><a href="http://list.taobao.com/itemlist/nanzhuang2011a.htm?spm=0.0.0.0.InyG1j&cat=30" target="_blank">กางเกงผู้ชาย</a></li>
                    <li><a href="http://list.taobao.com/itemlist/nanzhuang2011a.htm?spm=0.0.0.0.InyG1j&cat=30" target="_blank">เสื้อ</a></li>
                    <li><a href="http://list.taobao.com/itemlist/nanzhuang2011a.htm?spm=0.0.0.0.InyG1j&cat=30" target="_blank">สไตล์เสื้อผ้าผู้ชาย</a></li>
                    <li><a href="http://list.taobao.com/itemlist/nanzhuang2011a.htm?spm=0.0.0.0.InyG1j&cat=30" target="_blank">เสื้อผ้าแบบอื่น</a></li>
                </ul>
              </div>
            </div>
        </div>
        <div class="col-md-3" style="height:300px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">รองเท้ากระเป๋า</h3>
              </div>
              <div class="panel-body">
                <ul>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/nvxieshichang2011a.htm?spm=0.0.0.0.InyG1j&cat=50006843">รองเท้าผู้ชายฤดูใบไม้ผลิใบไม้ร่วง</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/nvxieshichang2011a.htm?spm=0.0.0.0.InyG1j&cat=50006843">รองเท้าผู้หญิงฤดูร้อน</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/nanxie2011a.htm?spm=0.0.0.0.InyG1j&cat=50016853">รองเท้าผู้ชายฤดูใบไม้ผลิใบไม้ร่วง</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/nanxie2011a.htm?spm=0.0.0.0.InyG1j&cat=50016853">รองเท้าผู้ชายฤดูร้อน</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/nvbao2011a.htm?spm=0.0.0.0.InyG1j&cat=50006842">กระเป๋าผู้หญิงคุณภาพดี</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/nanbao2011a.htm?spm=0.0.0.0.InyG1j&cat=50072686">กระเป๋าผู้ชายคุณภาพดี</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/nvbao2011a.htm?spm=0.0.0.0.InyG1j&cat=50072688">กระเป๋าเดินทาง</a></li>
                </ul>
              </div>
            </div>
        </div>
        <div class="col-md-3" style="height:300px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">ชุดชั้นใน</h3>
              </div>
              <div class="panel-body">
                <ul>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/neiyi2011a.htm?spm=0.0.0.0.InyG1j&cat=1625">Lingerie Category</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/neiyi2011a.htm?spm=0.0.0.0.InyG1j&cat=50016870">ยี่ห้อชุดชั้นใน</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/peishi2011a.htm?spm=0.0.0.0.InyG1j&cat=50010404">อะไหล่เสื้อผ้า</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/peishi2011a.htm?spm=0.0.0.0.InyG1j">ค้นหายอดนิยม</a></li>
                </ul>
              </div>
            </div>
        </div>
        <div class="col-md-3" style="height:300px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">กีฬา</h3>
              </div>
              <div class="panel-body">
                <ul>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/sport2011a.htm?spm=0.0.0.0.InyG1j&cat=50010388">รองเท้ากีฬา</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/sport2011a.htm?spm=0.0.0.0.InyG1j&cat=50016756">ชุดกีฬา</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/sport2011a.htm?spm=0.0.0.0.InyG1j&cat=50484015">กระเป๋ากีฬา</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/sport2011a.htm?spm=0.0.0.0.InyG1j&cat=2203">อุปกรณ์ปิกนิค</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/sport2011a.htm?spm=0.0.0.0.InyG1j&cat=50010728">กีฬา/โยคะ/ฟิตเนส/อุปกรณ์แฟนบอล</a></li>
                </ul>
              </div>
            </div>
        </div>
        <div class="col-md-3" style="height:300px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">จิวเวอลี่ นาฬิกา</h3>
              </div>
              <div class="panel-body">
                <ul>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/sp.htm?spm=0.0.0.0.InyG1j&cat=50015926">เครื่องประดับเพชร</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/sp.htm?spm=0.0.0.0.InyG1j&cat=50005700">นาฬิกาแบรน์เนม</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/sp.htm?spm=0.0.0.0.InyG1j&cat=1705">เครื่องประดับยอดนิยม</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/sp.htm?spm=0.0.0.0.InyG1j&cat=28">เครื่องประดับอื่นๆ</a></li>
                </ul>
              </div>
            </div>
        </div>
        <div class="col-md-3" style="height:300px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">ไอที</h3>
              </div>
              <div class="panel-body">
                <ul>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/shuma.htm?spm=0.0.0.0.InyG1j&cat=1512">มือถือ</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/shuma.htm?spm=0.0.0.0.InyG1j&cat=14">กล้อง/DV</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/shuma.htm?spm=0.0.0.0.InyG1j&cat=1101">โน๊ตบุ๊ค</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/shuma.htm?spm=0.0.0.0.InyG1j&cat=50047310">คอมพิวเตอร์โน๊ตบุ๊ค</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/shuma.htm?spm=0.0.0.0.InyG1j&cat=11">เกี่ยวกับคอมพิวเตอร์</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/shuma.htm?spm=0.0.0.0.InyG1j&cat=50007218">อุปกรณ์ในสำนักงาน</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/shuma.htm?spm=0.0.0.0.InyG1j&cat=50041307">internet storage</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/shuma.htm?spm=0.0.0.0.InyG1j&cat=50008090">อุปกรณ์เสริมเครื่องใช้ดิจิตอล</a></li>
                </ul>
              </div>
            </div>
        </div>
        <div class="col-md-3" style="height:300px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">เครื่องใช้ไฟฟ้าบ้านสำนักงาน</h3>
              </div>
              <div class="panel-body">
                <ul>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/jiadiano.htm?spm=0.0.0.0.InyG1j&cat=50035182">เครื่องใช้ไฟฟ้าภายในบ้านขนาดใหญ่</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/jiadiano.htm?spm=0.0.0.0.InyG1j&cat=50018930">เครื่องใช้ไฟฟ้าในครัว</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/jiadiano.htm?spm=0.0.0.0.InyG1j&cat=50018957">เครื่องใช้ไฟฟ้าในชีวิตประจำวัน</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/jiadiano.htm?spm=0.0.0.0.InyG1j&cat=50018908">อุปกรณ์เครื่องเสียง</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/jiadiano.htm?spm=0.0.0.0.InyG1j&cat=50049318">นวด</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/jiadiano.htm?spm=0.0.0.0.InyG1j&cat=50051952">อะไหล่เครื่องใช้ไฟฟ้า</a></li>
                </ul>
              </div>
            </div>
        </div>
        <div class="col-md-3" style="height:300px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">บำรุงผิว เครื่องสำอางค์</h3>
              </div>
              <div class="panel-body">
                <ul>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/beauty.htm?spm=0.0.0.0.InyG1j&cat=1801">ผลิตภัณท์บำรุงผิว</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/beauty.htm?spm=0.0.0.0.InyG1j&cat=50010788">เครื่องสำอาง/น้ำหอม</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/beauty.htm?spm=0.0.0.0.InyG1j&cat=50071436">ผลิตภัณท์บำรุงผม</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/beauty.htm?spm=0.0.0.0.InyG1j&cat=1801">แบรนด์ยอดนิยม</a></li>
                </ul>
              </div>
            </div>
        </div>
        <div class="col-md-3" style="height:300px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">เสื้อเด็ก</h3>
              </div>
              <div class="panel-body">
                <ul>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/baby.htm?spm=0.0.0.0.InyG1j&cat=50008165">หนังสือเด็ก</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/baby.htm?spm=0.0.0.0.InyG1j&cat=50067081">เครื่องใช้สตรีมีครรภ์</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/baby.htm?spm=0.0.0.0.InyG1j&cat=50097448">เด็กทารก</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/baby.htm?spm=0.0.0.0.InyG1j&cat=35">ผลิตภัณท์อาหารเด็ก</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/baby.htm?spm=0.0.0.0.InyG1j&cat=50006004">ของใช้เด็ก</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/baby.htm?spm=0.0.0.0.InyG1j&cat=50005998">ของเล่นเด็ก</a></li>
                </ul>
              </div>
            </div>
        </div>
        <div class="col-md-3" style="height:300px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">อุปกรณ์ภายในบ้าน</h3>
              </div>
              <div class="panel-body">
                <ul>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/jiaju.htm?spm=0.0.0.0.InyG1j&cat=27">วัสดุหลักตกแต่งบ้าน</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/jiaju.htm?spm=0.0.0.0.InyG1j&cat=50008164">เฟอร์นิเจอร์</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/jiaju.htm?spm=0.0.0.0.InyG1j&cat=50065206">ของตกแต่งบ้าน</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/jiaju.htm?spm=0.0.0.0.InyG1j&cat=50065205">เครื่องนอน</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/jiaju.htm?spm=0.0.0.0.InyG1j&cat=50065355">เครื่องใช้ไฟฟ้า</a></li>
                </ul>
              </div>
            </div>
        </div>
        <div class="col-md-3" style="height:300px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">อาหารขึ้นชื่อ</h3>
              </div>
              <div class="panel-body">
                <ul>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/food2011.htm?spm=0.0.0.0.InyG1j&cat=50002766">อาหารว่าง</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/food2011.htm?spm=0.0.0.0.InyG1j&cat=50008825">ผลิตภัณท์เพื่อสุขภาพ</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/food2011.htm?spm=0.0.0.0.InyG1j&cat=50035978">ธัญพืช,น้ำมัน,ข้าว,บะหมี่</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/food2011.htm?spm=0.0.0.0.InyG1j&cat=50103359">ชา/เครื่องดื่ม</a></li>
                </ul>
              </div>
            </div>
        </div>
        <div class="col-md-3" style="height:300px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">ของใช้ประจำวัน</h3>
              </div>
              <div class="panel-body">
                <ul>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/baihuoshichang.htm?spm=0.0.0.0.InyG1j&cat=50051688">เครื่องใช้จัดเก็บสิ่งของ</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/baihuoshichang.htm?spm=0.0.0.0.InyG1j&cat=21">เครื่องใช้ในบ้าน</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/baihuoshichang.htm?spm=0.0.0.0.InyG1j&cat=50035867">อุปกรณ์ทานอาหาร</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/baihuoshichang.htm?spm=0.0.0.0.InyG1j&cat=50035458">ผลิตภัณท์อนามัย</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/baihuoshichang.htm?spm=0.0.0.0.InyG1j&cat=50035966">sex toy</a></li>
                </ul>
              </div>
            </div>
        </div>
        <div class="col-md-3" style="height:300px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">รถ</h3>
              </div>
              <div class="panel-body">
                <ul>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/car.htm?spm=0.0.0.0.InyG1j&cat=26">อุปกรณ์รถยนต์</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/car.htm?spm=0.0.0.0.InyG1j&cat=50316001">มอเตอร์ไซด์</a></li>
                </ul>
              </div>
            </div>
        </div>
        <div class="col-md-3" style="height:300px;">
            <div class="panel panel-default">
              <div class="panel-heading">
                <h3 class="panel-title">บันเทิง</h3>
              </div>
              <div class="panel-body">
                <ul>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/wenyu.htm?spm=0.0.0.0.InyG1j&cat=23">งานอดิเรก สะสม</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/wenyu.htm?spm=0.0.0.0.InyG1j&cat=33">หนังสือนิตยสาร</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/wenyu.htm?spm=0.0.0.0.InyG1j&cat=34">โสตทัศน์/วีดีโอ</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/wenyu.htm?spm=0.0.0.0.InyG1j&cat=50039094">เครื่องดนตรี</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/enjoy.htm?spm=0.0.0.0.InyG1j&cat=50007216">ดอกไม้สด ตกแต่งสวน</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/enjoy.htm?spm=0.0.0.0.InyG1j&cat=29">อุปกรณ์สัตว์เลี้ยง</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/wenyu.htm?spm=0.0.0.0.InyG1j&cat=50096795">สั่งทำส่วนบุคคล</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/wenyu.htm?spm=0.0.0.0.InyG1j&cat=50032886">บริการอินเตอร์เนต</a></li>
                    <li><a target="_blank" href="http://list.taobao.com/itemlist/market/bendishenghuo.htm?spm=0.0.0.0.InyG1j&cat=50768003">บันเทิง</a></li>
                </ul>
              </div>
            </div>
        </div>


    </div>

</div>

<?php 
	include 'footer.php'; 
?>