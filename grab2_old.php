
<?php

include 'connect.php';

//Einbinden der Translate-Klasse.
require_once('Classes/translate.class.php');
 


// $url='http://blog.oscarliang.net';
// //file_get_contents() reads remote webpage content
// $lines_string=file_get_contents($url);
// //output, you can also save it locally on the server
// echo htmlspecialchars($lines_string);

// $file = "http://www.narutoroyal.com/china-express/test.html";
// $doc = new DOMDocument();
// $doc->loadHTMLFile($file);

// $xpath = new DOMXpath($doc);

// // example 1: for everything with an id
// //$elements = $xpath->query("//*[@id]");

// // example 2: for node data in a selected id
// //$elements = $xpath->query("/html/body/div[@id='yourTagIdHere']");

// // example 3: same as above with wildcard
// $elements = $xpath->query("//div");

// if (!is_null($elements)) {
//   foreach ($elements as $element) {
//     echo "<br/>[". $element->nodeName. "]";

//     $nodes = $element->childNodes;
//     foreach ($nodes as $node) {
//       echo $node->nodeValue. "\n";
//     }
//   }
// }

//ดึงข้อมูล rate
$select_rate = mysql_query("select * from website_rate order by starting_date desc limit 1");
$select_rate_row = mysql_fetch_array($select_rate);
$rate = $select_rate_row['rate_cny'];
$shipping_rate = $select_rate_row['shipping_rate_cny'];

$url = $_GET['url'];
$parseURL = '';
$hostname = '';
$bottom_host_name = '';

if ($_GET['url']) {
	$parseURL = parse_url($url);
	$hostname = $parseURL['host'];
	$host_names = explode(".", $hostname);
	$bottom_host_name = $host_names[count($host_names)-2] . "." . $host_names[count($host_names)-1];
}

if ($url == "") {
	echo "กรุณากรอกลิงค์สินค้าในช่องค้นหา";
}else if($bottom_host_name == "taobao.com"){


	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label class='col-sm-2 control-label' >เว็บไซด์ :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' > ".$bottom_host_name."</div>
				</div>";

	//parse_str($parseURL['query'], $query)

	//select url id
	//วิธีที่ 1
	parse_str($parseURL['query'], $query);
	//วิธีที่ 2
	preg_match("/item\/(.*).htm/", $url, $output_array);

	if (isset($query['id'])) {
		if ($hostname == "detail.taobao.com") {
			//echo "from : detail.taobao.com";
			$url = "http://detail.taobao.com/item.htm?id=".$query['id']."&detailType=".$query['detailType'];
		}else{
			$url = "http://item.taobao.com/item.htm?id=".$query['id'];
			//echo "1";
		}
	}else if (isset($query['itemid'])) {
		$url = "http://item.taobao.com/item.htm?id=".$query['itemid'];
		//echo "2";
	}else if($output_array[1] != ''){
		$url = "http://item.taobao.com/item.htm?id=".$output_array[1];
		//echo "3";
	}else{
		//echo "cannot get url";
	}

	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label  class='col-sm-2 control-label' >ลิงค์ :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' ><a href='".$url."' target='_blank'><span id='product_url'>".$url."</span></a></div>
				</div>";

	$dom = new DOMDocument();
	//$html = get_data($url);
	@$dom->loadHTMLfile($url);
	//@$dom -> load($html);

	$xpath = new DomXpath($dom);

	//Query Product Image
	$elements  = $xpath->query('//img[@id="J_ThumbView"]');
	$elements_2  = $xpath->query('//img[@id="J_ImgBooth"]');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			//echo "in for";
			if (!is_null($element->getAttribute('src'))) {
				//echo "use src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' 
										src='".$element->getAttribute('src')."'>
								</div>
							</div>";
			}else if (!is_null($element->getAttribute('data-src'))) {
				//echo "use data-src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' 
										src='".$element->getAttribute('data-src')."'>
								</div>
							</div>";
			}else {
				echo "cannot find img";
			}
		}
	}
	if (!is_null($elements_2)) {
		foreach ($elements_2 as $element) {
			//echo "in for";
			if (!is_null($element->getAttribute('src'))) {
				//echo "use src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' 
										src='".$element->getAttribute('src')."'>
								</div>
							</div>";
			}else if (!is_null($element->getAttribute('data-src'))) {
				//echo "use data-src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' 
										src='".$element->getAttribute('data-src')."'>
								</div>
							</div>";
			}else {
				echo "cannot find img";
			}
		}
	}

	//Query Product Title
	$elements  = $xpath->query('//div[@id="J_Title"]');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ชื่อสินค้า :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "h3") {
					echo "<span id='product_name'>".$node->nodeValue. "</span>
							</div>
						</div>";
				}
			}
		}
	}

	//Query Product Price
	$elements  = $xpath->query('//em[@class="tb-rmb-num"]');
	
	if (!isset($elements->nodeValue)) {
		// echo "try again";
		// echo $dom->saveHTML();
		// preg_match("/price:(.*),/", $dom->saveHTML(), $output_array);
		// echo " found[0] =".$output_array[0];
		// echo " found[1] =".$output_array[1];
		$elements  = $xpath->query('//strong[@class="tb-rmb-num"]');
	}

	if (isset($elements->nodeValue)) {
		foreach ($elements as $element) {
			$price = str_replace("¥", "", $element->nodeValue);
			$price = str_replace(" ", "", $price);
			$price = preg_replace('/\s+/', '', $price);
			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ราคา :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "¥ <span id='product_price'>".$price. "</span> หยวน ( ฿ ". number_format($price*$rate,2) ." บาท ) <br />( หากราคาสินค้าที่แสดงยังไม่ถูกลดราคา ลูกค้าไม่ต้องกังวล ราคาสินค้าจะถูกปรับลดหลังจากที่พนักงานตรวจสอบราคาอีกครั้ง )
						</div>
					</div>";
			// $nodes = $element->childNodes;
			// foreach ($nodes as $node) {
			// 	echo "<span id='product_price'>".$node->nodeValue. "</span>";
			// }
		}
	}else{

		preg_match('/price":"((?:[0-9]+,)*[0-9]+(?:\.[0-9]+)?)/', $dom->saveHTML(), $output_array);
	
		if (isset($output_array[1])) {

			$price = $output_array[1];
			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ราคา :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "¥ <span id='product_price'>".$price. "</span> หยวน ( ฿ ". number_format($price*$rate,2) ." บาท ) <br />( หากราคาสินค้าที่แสดงยังไม่ถูกลดราคา ลูกค้าไม่ต้องกังวล ราคาสินค้าจะถูกปรับลดหลังจากที่พนักงานตรวจสอบราคาอีกครั้ง )
							</div>
						</div>";
					
		}

	}


	//check size
	$checksize  = $xpath->query('//div[@id="J_SKU"]/dl[1]/dt');
	//echo "node=[".$checksize->item(0)->nodeValue."]";

	if (stristr($checksize->item(0)->nodeValue, "码")) {
		//Query Product Size
		$elements  = $xpath->query('//div[@id="J_SKU"]/dl[1]/dd/ul');

		if (!is_null($elements)) {
			foreach ($elements as $element) {
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' >ขนาด :</label>
								<div style='text-align:left;' class='col-sm-10 control-label' >";
				echo "<div id='btn-group-size' class='btn-group' data-toggle='buttons'>";
				$nodes = $element->childNodes;
				foreach ($nodes as $node) {
					if ($node->nodeName == "li") {
						$titles = $xpath->query("./a", $node);
						if ($titles->length > 0) {
							$title = $titles->item(0)->nodeValue;
							$title = preg_replace('/\s+/', '', $title);
							$title = translateSize($title);
							echo "<label class='btn btn-default' id='lbl_size' ><input name='product_size' id='product_size' value='$title' type='radio' >";
							echo $title;
							echo "</label>";
						}
						//echo "[".$node->nodeName."]".$node->nodeValue. "/";
					}
				}
				echo "</div> <label id='alert-size'></label>
								</div>
							</div>";
			}
		}

		//Query Product Color
		$elements  = $xpath->query('//div[@id="J_SKU"]/dl[2]/dd/ul');

		if (!is_null($elements)) {
			foreach ($elements as $element){
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' >สี :</label>
								<div style='text-align:left;' class='col-sm-10 control-label' >";
				echo "<div class='btn-group' data-toggle='buttons'>";
				$nodes = $element->childNodes;
				foreach ($nodes as $node) {
					if ($node->nodeName == "li") {
						$titles = $xpath->query("./a", $node);
						if ($titles->length > 0) {
							$title = $titles->item(0)->nodeValue;
							$title = preg_replace('/\s+/', '', $title);
							$title = translateColor($title);
							$pic = $titles->item(0)->getAttribute('style');
			  		    	$pic = str_replace("background:url(", "http:", $pic); 
			  		    	$pic = str_replace(") center no-repeat;", "", $pic); 
			  		    	echo "<label class='btn btn-default' ><input name='product_color' id='product_color' value='$title' type='radio' >";
			  		    	echo $title;
			  		    	echo "<img src='".$pic."'>";
			  		    	echo "</label>";
			  		    }
			  		}
			  	}
			  	echo "</div> <label id='alert-color'></label>
						  	</div>
							</div>";
			}
		}
	}else{
		//Query Product Color
		$elements  = $xpath->query('//div[@id="J_SKU"]/dl[1]/dd/ul');

		if (!is_null($elements)) {
			foreach ($elements as $element){
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' >สี :</label>
								<div style='text-align:left;' class='col-sm-10 control-label' >";
				echo "<div class='btn-group' data-toggle='buttons'>";
				$nodes = $element->childNodes;
				foreach ($nodes as $node) {
					if ($node->nodeName == "li") {
						$titles = $xpath->query("./a", $node);
						if ($titles->length > 0) {
							$title = $titles->item(0)->nodeValue;
							$title = preg_replace('/\s+/', '', $title);
							$title = translateColor($title);
							$pic = $titles->item(0)->getAttribute('style');
			  		    	$pic = str_replace("background:url(", "http:", $pic); 
			  		    	$pic = str_replace(") center no-repeat;", "", $pic); 
			  		    	echo "<label class='btn btn-default' ><input name='product_color' id='product_color' value='$title' type='radio' >";
			  		    	echo $title;
			  		    	echo "<img src='".$pic."'>";
			  		    	echo "</label>";
			  		    }
			  		}
			  	}
			  	echo "</div> <label id='alert-color'></label>
						  	</div>
							</div>";
			}
		}
	}

	//Query Shop Name
	$elements  = $xpath->query('//div[@class="tb-shop-seller"]/dl/dd/a');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo "<input type='hidden' name='shop_name' id='shop_name' value='";
			$title = $element->nodeValue;
			$title = preg_replace('/\s+/', '', $title);
			echo $title;
			echo "'>";
		}
	}

	echo "	<div style='margin-bottom:0px;' class='form-group'>
						<label  class='col-sm-2 control-label' >จำนวน :</label>
						<div style='text-align:left;' class='col-sm-10 control-label' >
			<input type='button' value='-' class='qtyminus' field='product_amount' onclick='qtyminus()' />
			<input type='text' name='product_amount' id='product_amount' style='width: 50px;' class='qty' value='1' style='width: 50px;'
			onkeypress='return isNumber(event)'>
			<input type='button' value='+' class='qtyplus' field='product_amount' onclick='qtyplus()' />
						</div>
					</div>";

	echo "<input type='hidden' name='source' id='source' value='Taobao'>";

}else if($bottom_host_name == "tmall.com"){

	################
	#### T-MALL ####
	################
	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label  class='col-sm-2 control-label' >เว็บไซด์ :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' > ".$bottom_host_name."</div>
				</div>";

	//parse_str($parseURL['query'], $query);

	//select url id
	//วิธีที่ 1
	parse_str($parseURL['query'], $query);

	if (isset($query['id'])) {
		$url = "http://world.tmall.com/item/".$query['id'].".htm?id=".$query['id'];
	}else{
		//echo "cannot get url";
	}

	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label  class='col-sm-2 control-label' >ลิงค์ :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' ><a href='".$url."' target='_blank'><span id='product_url'>".$url."</span></a></div>
				</div>";

	$dom = new DOMDocument();

	@$dom->loadHTMLfile($url);

	$xpath = new DomXpath($dom);

	//Query Product Image
	$elements  = $xpath->query('//img[@id="J_ThumbView"]');
	$elements_2  = $xpath->query('//img[@id="J_ImgBooth"]');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			//echo "in for";
			if (!is_null($element->getAttribute('src'))) {
				//echo "use src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' src='".$element->getAttribute('src')."'>
								</div>
							</div>";
			}else if (!is_null($element->getAttribute('data-src'))) {
				//echo "use data-src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' src='".$element->getAttribute('data-src')."'>
								</div>
							</div>";
			}else {
				echo "cannot find img";
			}
		}
	}
	if (!is_null($elements_2)) {
		foreach ($elements_2 as $element) {
			//echo "in for";
			if (!is_null($element->getAttribute('src'))) {
				//echo "use src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' src='".$element->getAttribute('src')."'>
								</div>
							</div>";
			}else if (!is_null($element->getAttribute('data-src'))) {
				//echo "use data-src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' src='".$element->getAttribute('data-src')."'>
								</div>
							</div>";
			}else {
				echo "cannot find img";
			}
		}
	}

	//Query Product Title
	$elements  = $xpath->query('//div[@class="tb-detail-hd"]');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ชื่อสินค้า :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "h1") {
					echo "<span id='product_name'>".$node->nodeValue. "</span>
							</div>
						</div>";
				}
			}
		}
	}

	//Query Product Price

	preg_match('/defaultItemPrice":"((?:[0-9]+,)*[0-9]+(?:\.[0-9]+)?)/', $dom->saveHTML(), $output_array);
	
	if (isset($output_array[1])) {

		$price = $output_array[1];
		echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ราคา :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >";
		echo "¥ <span id='product_price'>".$price. "</span> หยวน ( ฿ ". number_format($price*$rate,2) ." บาท ) <br />( หากราคาสินค้าที่แสดงยังไม่ถูกลดราคา ลูกค้าไม่ต้องกังวล ราคาสินค้าจะถูกปรับลดหลังจากที่พนักงานตรวจสอบราคาอีกครั้ง )
							</div>
					</div>";
				
	}

	//Query Product Size
	$elements  = $xpath->query('//div[@class="tb-sku"]/dl[2]/dd/ul');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' >ขนาด :</label>
								<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "<div id='btn-group-size' class='btn-group' data-toggle='buttons'>";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "li") {
					$titles = $xpath->query("./a", $node);
					if ($titles->length > 0) {
						$title = $titles->item(0)->nodeValue;
						$title = preg_replace('/\s+/', '', $title);
						$title = translateSize($title);
						echo "<label class='btn btn-default' id='lbl_size' ><input name='product_size' id='product_size' value='$title' type='radio' >";
						echo $title;
						echo "</label>";
					}
					//echo "[".$node->nodeName."]".$node->nodeValue. "/";
				}
			}
			echo "</div> <label id='alert-size'></label>
							</div>
						</div>";
		}
	}

	//Query Product Color
	$elements  = $xpath->query('//div[@class="tb-sku"]/dl[1]/dd/ul');

	if (!is_null($elements)) {
		foreach ($elements as $element){
			echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' >สี :</label>
								<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "<div class='btn-group' data-toggle='buttons'>";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "li") {
					$titles = $xpath->query("./a", $node);
					if ($titles->length > 0) {
						$title = $titles->item(0)->nodeValue;
						$title = preg_replace('/\s+/', '', $title);
						$title = translateColor($title);
						$pic = $titles->item(0)->getAttribute('style');
		  		    	$pic = str_replace("background:url(", "http:", $pic); 
		  		    	$pic = str_replace(") center no-repeat;", "", $pic); 
		  		    	echo "<label class='btn btn-default' ><input name='product_color' id='product_color' value='$title' type='radio' >";
		  		    	echo $title;
		  		    	echo "<img src='".$pic."'>";
		  		    	echo "</label>";
		  		    }
		  		}
		  	}
		  	echo "</div> <label id='alert-color'></label>
					  	</div>
						</div>";
		}
	}

	//Query Shop Name
	$elements  = $xpath->query('//div[@class="tb-info"]/div[1]/label');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo "<input type='hidden' name='shop_name' id='shop_name' value='";
			$title = $element->nodeValue;
			$title = str_replace('掌柜', '', $title);
			$title = str_replace(' ', '', $title);
			$title = str_replace(':', '', $title);
			//echo "title=".$title;
			$title = preg_replace('/\s+/', '', $title);
			echo $title;
			echo "'>";
		}
	}

	echo "	<div style='margin-bottom:0px;' class='form-group'>
						<label  class='col-sm-2 control-label' >จำนวน :</label>
						<div style='text-align:left;' class='col-sm-10 control-label' >
			<input type='button' value='-' class='qtyminus' field='product_amount' onclick='qtyminus()' />
			<input type='text' name='product_amount' id='product_amount' style='width: 50px;' class='qty' value='1' style='width: 50px;'
			onkeypress='return isNumber(event)'>
			<input type='button' value='+' class='qtyplus' field='product_amount' onclick='qtyplus()' />
						</div>
					</div>";

	echo "<input type='hidden' name='source' id='source' value='T-Mall'>";

}else if($bottom_host_name == "jd.com"){

####################
######## JD ########
####################

	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label  class='col-sm-2 control-label' >เว็บไซด์ :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' > ".$bottom_host_name."</div>
				</div>";

	preg_match("/jd.com\/(.*).html/", $url, $output_array);

	if($output_array[1] != ''){
		$url = "http://item.jd.com/".$output_array[1].".html";
	}else{
		//echo "cannot get url";
	}

	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label  class='col-sm-2 control-label' >ลิงค์ :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' ><a href='".$url."' target='_blank'><span id='product_url'>".$url."</span></a></div>
				</div>";

	$dom = new DOMDocument();
	//$html = get_data($url);
	@$dom->loadHTMLfile($url);
	//@$dom -> load($html);

	$xpath = new DomXpath($dom);

	//Query Product Image
	$elements  = $xpath->query('//div[@id="preview"]/div[1]/img');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			//echo "in for";
			if (!is_null($element->getAttribute('src'))) {
				//echo "use src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' src='".$element->getAttribute('src')."'>
								</div>
							</div>";
			}else {
				echo "cannot find img";
			}
		}
	}


	//Query Product Title
	$elements  = $xpath->query('//div[@id="name"]');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ชื่อสินค้า :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "h1") {
					echo "<span id='product_name'> ".$node->nodeValue. "</span>		</div>
						</div>";
				}
			}
		}
	}

	echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ราคา :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >";
	echo "¥ <span id='product_price'> ¥ 0.00 </span> <br />( หากราคาสินค้าที่แสดงยังไม่ถูกลดราคา ลูกค้าไม่ต้องกังวล ราคาสินค้าจะถูกปรับลดหลังจากที่พนักงานตรวจสอบราคาอีกครั้ง )
					</div>
				</div>";
			

	//Query Product Price
	// $elements  = $xpath->query('//em[@class="tb-rmb-num"]');
	
	// if (!isset($elements->nodeValue)) {
	// 	// echo "try again";
	// 	// echo $dom->saveHTML();
	// 	// preg_match("/price:(.*),/", $dom->saveHTML(), $output_array);
	// 	// echo " found[0] =".$output_array[0];
	// 	// echo " found[1] =".$output_array[1];
	// 	$elements  = $xpath->query('//strong[@class="tb-rmb-num"]');
	// }

	// if (!is_null($elements)) {
	// 	foreach ($elements as $element) {
	// 		$price = str_replace("¥", "", $element->nodeValue);
	// 		$price = str_replace(" ", "", $price);
	// 		$price = preg_replace('/\s+/', '', $price);
	// 		echo "<br/><strong>ราคา </strong>";
	// 		echo "¥ <span id='product_price'>".$price. "</span> หยวน ( ฿ ". number_format($price*$rate,2) ." บาท ) <br />( หากราคาสินค้าที่แสดงยังไม่ถูกลดราคา ลูกค้าไม่ต้องกังวล ราคาสินค้าจะถูกปรับลดหลังจากที่พนักงานตรวจสอบราคาอีกครั้ง )";
	// 		// $nodes = $element->childNodes;
	// 		// foreach ($nodes as $node) {
	// 		// 	echo "<span id='product_price'>".$node->nodeValue. "</span>";
	// 		// }
	// 	}
	// }

	//Query Product Size
	$elements  = $xpath->query('//div[@id="choose-version"]/div[2]');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' >ขนาด :</label>
								<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "<div id='btn-group-size' class='btn-group' data-toggle='buttons'>";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "div") {
					$titles = $xpath->query("./a", $node);
					if ($titles->length > 0) {
						$title = $titles->item(0)->nodeValue;
						$title = preg_replace('/\s+/', '', $title);
						$title = translateSize($title);
						echo "<label class='btn btn-default' id='lbl_size' ><input name='product_size' id='product_size' value='$title' type='radio' >";
						echo $title;
						echo "</label>";
					}
					//echo "[".$node->nodeName."]".$node->nodeValue. "/";
				}
			}
			echo "</div> <label id='alert-size'></label>
								</div>
							</div>";
		}
	}

	//Query Product Color
	$elements  = $xpath->query('//div[@id="choose-color"]/div[2]');

	if (!is_null($elements)) {
		foreach ($elements as $element){
			echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' >สี :</label>
								<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "<div class='btn-group' data-toggle='buttons'>";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "div") {
					$titles = $xpath->query("./a/i", $node);
					$img    = $xpath->query("./a/img", $node);
					if ($titles->length > 0) {
						$title = $titles->item(0)->nodeValue;
						$title = preg_replace('/\s+/', '', $title);
						$title = translateColor($title);
						$pic = $img->item(0)->getAttribute('src');
		  		    	echo "<label class='btn btn-default' ><input name='product_color' id='product_color' value='$title' type='radio' >";
		  		    	echo $title;
		  		    	echo "<img src='".$pic."'>";
		  		    	echo "</label>";
		  		    }
		  		}
		  	}
		  	echo "</div> <label id='alert-color'></label>
						  	</div>
							</div>";
		}
	}

	//Query Shop Name
	$elements  = $xpath->query('//span[@class="text J-shop-name"]');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo "<input type='hidden' name='shop_name' id='shop_name' value='";
			$title = $element->nodeValue;
			$title = preg_replace('/\s+/', '', $title);
			echo $title;
			echo "'>";
		}
	}

	echo "	<div style='margin-bottom:0px;' class='form-group'>
						<label  class='col-sm-2 control-label' >จำนวน :</label>
						<div style='text-align:left;' class='col-sm-10 control-label' >
			<input type='button' value='-' class='qtyminus' field='product_amount' onclick='qtyminus()' />
			<input type='text' name='product_amount' id='product_amount' style='width: 50px;' class='qty' value='1' style='width: 50px;'
			onkeypress='return isNumber(event)'>
			<input type='button' value='+' class='qtyplus' field='product_amount' onclick='qtyplus()' />
						</div>
					</div>";

	echo "<input type='hidden' name='source' id='source' value='jd.com'>";

}else if($bottom_host_name == "1688.com"){

	#################################################
	################### 1688.com ####################
	#################################################
	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label  class='col-sm-2 control-label' >เว็บไซด์ :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' > ".$bottom_host_name."</div>
				</div>";

	preg_match("/offer\/(.*).htm/", $url, $output_array);

	preg_match("/detail\/(.*).htm/", $url, $output_array_beta);

	//http://detail.1688.com/offer/521652146439.html

	if($output_array[1] != ''){
		$url = "http://detail.1688.com/offer/".$output_array[1].".html";
		//echo "3";
	}else if($output_array_beta[1] != ''){
		$url = "http://detail.1688.com/offer/".$output_array_beta[1].".html";
		//echo "3";
	}else{ 
		//echo "cannot get url";
	}

	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label  class='col-sm-2 control-label' >ลิงค์ :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' ><a href='".$url."' target='_blank'><span id='product_url'>".$url."</span></a></div>
				</div>";

	$dom = new DOMDocument();
	//$html = get_data($url);
	@$dom->loadHTMLfile($url);
	//@$dom -> load($html);
	//echo $dom->saveHTMLFile("save");


	$xpath = new DomXpath($dom);

	//Query Product Image
	$elements  = $xpath->query('//a[@trace="largepic"]/img');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			//echo "in for";
			if (!is_null($element->getAttribute('src'))) {
				//echo "use src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' src='".$element->getAttribute('src')."'>
									</div>
							</div>";
			}else if (!is_null($element->getAttribute('data-src'))) {
				//echo "use data-src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' src='".$element->getAttribute('data-src')."'>
									</div>
							</div>";
			}else {
				echo "cannot find img";
			}
		}
	}

	//Query Product Title
	$elements  = $xpath->query('//div[@id="mod-detail-title"]');

	if (!is_null($elements)) {
		foreach ($elements as $element) {

			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ชื่อสินค้า :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "h1") {
					echo "<span id='product_name'>".$node->nodeValue. "</span>
							</div>
						</div>";
				}
			}
		}
	}

	//Query Product Price
	$elements  = $xpath->query('//div[@class="price-original-sku"]/span[1]');
	
	if (!isset($elements->nodeValue)) {
		// echo "try again";
		// echo $dom->saveHTML();
		// preg_match("/price:(.*),/", $dom->saveHTML(), $output_array);
		// echo " found[0] =".$output_array[0];
		// echo " found[1] =".$output_array[1];
		$elements  = $xpath->query('//strong[@class="tb-rmb-num"]');
	}

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			$price = str_replace("¥", "", $element->nodeValue);
			$price = str_replace(" ", "", $price);
			$price = preg_replace('/\s+/', '', $price);
			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ราคา :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "¥ <span id='product_price'>".$price. "</span> หยวน ( ฿ ". number_format($price*$rate,2) ." บาท ) <br />( หากราคาสินค้าที่แสดงยังไม่ถูกลดราคา ลูกค้าไม่ต้องกังวล ราคาสินค้าจะถูกปรับลดหลังจากที่พนักงานตรวจสอบราคาอีกครั้ง )
							</div>
						</div>";
			// $nodes = $element->childNodes;
			// foreach ($nodes as $node) {
			// 	echo "<span id='product_price'>".$node->nodeValue. "</span>";
			// }
		}
	}

	//Query Product Size
	$elements  = $xpath->query('//div[@id="J_SKU"]/dl[1]/dd/ul');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' >ขนาด :</label>
								<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "<div id='btn-group-size' class='btn-group' data-toggle='buttons'>";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "li") {
					$titles = $xpath->query("./a", $node);
					if ($titles->length > 0) {
						$title = $titles->item(0)->nodeValue;
						$title = preg_replace('/\s+/', '', $title);
						$title = translateSize($title);
						echo "<label class='btn btn-default' id='lbl_size' ><input name='product_size' id='product_size' value='$title' type='radio' >";
						echo $title;
						echo "</label>";
					}
					//echo "[".$node->nodeName."]".$node->nodeValue. "/";
				}
			}
			echo "</div> <label id='alert-size'></label>
								</div>
							</div>";
		}
	}

	//Query Product Color
	$elements  = $xpath->query('//div[@id="J_SKU"]/dl[2]/dd/ul');

	if (!is_null($elements)) {
		foreach ($elements as $element){
			echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' >สี :</label>
								<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "<div class='btn-group' data-toggle='buttons'>";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "li") {
					$titles = $xpath->query("./a", $node);
					if ($titles->length > 0) {
						$title = $titles->item(0)->nodeValue;
						$title = preg_replace('/\s+/', '', $title);
						$title = translateColor($title);
						$pic = $titles->item(0)->getAttribute('style');
		  		    	$pic = str_replace("background:url(", "http:", $pic); 
		  		    	$pic = str_replace(") center no-repeat;", "", $pic); 
		  		    	echo "<label class='btn btn-default' ><input name='product_color' id='product_color' value='$title' type='radio' >";
		  		    	echo $title;
		  		    	echo "<img src='".$pic."'>";
		  		    	echo "</label>";
		  		    }
		  		}
		  	}
		  	echo "</div> <label id='alert-color'></label>
						  	</div>
							</div>";
		}
	}

	//Query Shop Name
	$elements  = $xpath->query('//div[@class="tb-shop-seller"]/dl/dd/a');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo "<input type='hidden' name='shop_name' id='shop_name' value='";
			$title = $element->nodeValue;
			$title = preg_replace('/\s+/', '', $title);
			echo $title;
			echo "'>";
		}
	}

	echo "	<div style='margin-bottom:0px;' class='form-group'>
						<label  class='col-sm-2 control-label' >จำนวน :</label>
						<div style='text-align:left;' class='col-sm-10 control-label' >
			<input type='button' value='-' class='qtyminus' field='product_amount' onclick='qtyminus()' />
			<input type='text' name='product_amount' id='product_amount' style='width: 50px;' class='qty' value='1' style='width: 50px;'
			onkeypress='return isNumber(event)'>
			<input type='button' value='+' class='qtyplus' field='product_amount' onclick='qtyplus()' />
						</div>
					</div>";

	echo "<input type='hidden' name='source' id='source' value='1688.com'>";

} else if($bottom_host_name == "amazon.cn"){

	###################################
	############# amazon.cn ###########
	###################################

	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label  class='col-sm-2 control-label' >เว็บไซด์ :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' > ".$bottom_host_name."</div>
				</div>";

	//parse_str($parseURL['query'], $query);

	//select url id
	//วิธีที่ 1
	// parse_str($parseURL['query'], $query);
	// //วิธีที่ 2
	// preg_match("/item\/(.*).htm/", $url, $output_array);

	// if (isset($query['id'])) {
	// 	if ($hostname == "detail.taobao.com") {
	// 		//echo "from : detail.taobao.com";
	// 		$url = "http://detail.taobao.com/item.htm?id=".$query['id']."&detailType=".$query['detailType'];
	// 	}else{
	// 		$url = "http://item.taobao.com/item.htm?id=".$query['id'];
	// 		//echo "1";
	// 	}
	// }else if (isset($query['itemid'])) {
	// 	$url = "http://item.taobao.com/item.htm?id=".$query['itemid'];
	// 	//echo "2";
	// }else if($output_array[1] != ''){
	// 	$url = "http://item.taobao.com/item.htm?id=".$output_array[1];
	// 	//echo "3";
	// }else{
	// 	//echo "cannot get url";
	// }

	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label  class='col-sm-2 control-label' >ลิงค์ :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' ><a href='".$url."' target='_blank'><span id='product_url'>".$url."</span></a></div>
				</div>";

	$dom = new DOMDocument();
	//$html = get_data($url);
	@$dom->loadHTMLfile($url);
	//@$dom -> load($html);

	$xpath = new DomXpath($dom);

	//Query Product Image
	$elements  = $xpath->query('//div[@id="imgTagWrapperId"]/img');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			//echo "in for";
			if (!is_null($element->getAttribute('src'))) {
				//echo "use src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' src='".$element->getAttribute('src')."'>
									</div>
							</div>";
			}else if (!is_null($element->getAttribute('data-src'))) {
				//echo "use data-src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' src='".$element->getAttribute('data-src')."'>
									</div>
							</div>";
			}else {
				echo "cannot find img";
			}
		}
	}

	//Query Product Title
	$elements  = $xpath->query('//h1[@id="title"]');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ชื่อสินค้า :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "span") {
					echo "<span id='product_name'>".$node->nodeValue. "</span>
							</div>
						</div>";
				}
			}
		}
	}

	//Query Product Price
	$elements  = $xpath->query('//span[@class="price"]');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			$price = str_replace("￥", "", $element->nodeValue);
			$price = str_replace(" ", "", $price);
			$price = preg_replace('/\s+/', '', $price);
			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ราคา :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "¥ <span id='product_price'>".$price. "</span> หยวน ( ฿ ". number_format($price*$rate,2) ." บาท ) <br />( หากราคาสินค้าที่แสดงยังไม่ถูกลดราคา ลูกค้าไม่ต้องกังวล ราคาสินค้าจะถูกปรับลดหลังจากที่พนักงานตรวจสอบราคาอีกครั้ง )
							</div>
						</div>";
			// $nodes = $element->childNodes;
			// foreach ($nodes as $node) {
			// 	echo "<span id='product_price'>".$node->nodeValue. "</span>";
			// }
		}
	}

	//Query Product Size
	$elements  = $xpath->query('//div[@id="variation_size_name"]/span[1]/span/select');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' >ขนาด :</label>
								<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "<div id='btn-group-size' class='btn-group' data-toggle='buttons'>";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "option") {
					$title = $node->nodeValue;
					$title = preg_replace('/\s+/', '', $title);
					$title = translateSize($title);
					if ($title != "Choose"){
						echo "<label class='btn btn-default' id='lbl_size' ><input name='product_size' id='product_size' value='$title' type='radio' >";
						echo $title;
						echo "</label>";
					}	
					//echo "[".$node->nodeName."]".$node->nodeValue. "/";
				}
			}
			echo "</div> <label id='alert-size'></label>
								</div>
							</div>";
		}
	}

	//Query Product Color
	$elements  = $xpath->query('//div[@id="variation_color_name"]/ul[1]');

	if (!is_null($elements)) {
		foreach ($elements as $element){
			echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' >สี :</label>
								<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "<div class='btn-group' data-toggle='buttons'>";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "li") {
					$titles = $xpath->query("./span/div/span/span/span/button/div/div/img", $node);
					if ($titles->length > 0) {
						$title = $titles->item(0)->getAttribute('alt');
						$title = preg_replace('/\s+/', '', $title);
						$title = translateColor($title);
						$pic = $titles->item(0)->getAttribute('src');
		  		    	echo "<label class='btn btn-default' ><input name='product_color' id='product_color' value='$title' type='radio' >";
		  		    	echo $title;
		  		    	echo "<img src='".$pic."'>";
		  		    	echo "</label>";
		  		    }
		  		}
		  	}
		  	echo "</div> <label id='alert-color'></label>
						  	</div>
							</div>";
		}
	}

	//Query Shop Name
	echo "<input type='hidden' name='shop_name' id='shop_name' value='amazon.cn'>";

	echo "	<div style='margin-bottom:0px;' class='form-group'>
						<label  class='col-sm-2 control-label' >จำนวน :</label>
						<div style='text-align:left;' class='col-sm-10 control-label' >
			<input type='button' value='-' class='qtyminus' field='product_amount' onclick='qtyminus()' />
			<input type='text' name='product_amount' id='product_amount' style='width: 50px;' class='qty' value='1' style='width: 50px;'
			onkeypress='return isNumber(event)'>
			<input type='button' value='+' class='qtyplus' field='product_amount' onclick='qtyplus()' />
						</div>
					</div>";

	echo "<input type='hidden' name='source' id='source' value='amazon.cn'>";

}else if($bottom_host_name == "dangdang.com"){

	###############################
	########## DANGDANG ###########
	###############################

	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label  class='col-sm-2 control-label' >เว็บไซด์ :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' > ".$bottom_host_name."</div>
				</div>";

	//parse_str($parseURL['query'], $query);

	//select url id
	//วิธีที่ 1
	// parse_str($parseURL['query'], $query);
	// //วิธีที่ 2
	// preg_match("/item\/(.*).htm/", $url, $output_array);

	// if (isset($query['id'])) {
	// 	if ($hostname == "detail.taobao.com") {
	// 		//echo "from : detail.taobao.com";
	// 		$url = "http://detail.taobao.com/item.htm?id=".$query['id']."&detailType=".$query['detailType'];
	// 	}else{
	// 		$url = "http://item.taobao.com/item.htm?id=".$query['id'];
	// 		//echo "1";
	// 	}
	// }else if (isset($query['itemid'])) {
	// 	$url = "http://item.taobao.com/item.htm?id=".$query['itemid'];
	// 	//echo "2";
	// }else if($output_array[1] != ''){
	// 	$url = "http://item.taobao.com/item.htm?id=".$output_array[1];
	// 	//echo "3";
	// }else{
	// 	//echo "cannot get url";
	// }

	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label  class='col-sm-2 control-label' >ลิงค์ :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' ><a href='".$url."' target='_blank'><span id='product_url'>".$url."</span></a></div>
				</div>";

	$dom = new DOMDocument();
	//$html = get_data($url);
	@$dom->loadHTMLfile($url);
	//@$dom -> load($html);

	$xpath = new DomXpath($dom);

	//Query Product Image
	$elements  = $xpath->query('//div[@id="detailPic"]/img');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			//echo "in for";
			if (!is_null($element->getAttribute('src'))) {
				//echo "use src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' src='".$element->getAttribute('src')."'>
									</div>
							</div>";
			}else if (!is_null($element->getAttribute('data-src'))) {
				//echo "use data-src";
				echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' ></label>
								<div style='text-align:left;' class='col-sm-10 control-label' >
									<img style='height:200px;' class='img-thumbnail' id='product_img' src='".$element->getAttribute('data-src')."'>
									</div>
							</div>";
			}else {
				echo "cannot find img";
			}
		}
	}

	//Query Product Title
	$elements  = $xpath->query('//div[@name="Title_pub"]');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ชื่อสินค้า :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "h1") {
					echo "<span id='product_name'>".$node->nodeValue. "</span>
							</div>
						</div>";
				}
			}
		}
	}


//http://product.dangdang.com/1303389930.html
	//Query Product Price
	$elements  = $xpath->query('//b[@class="d_price"]');
	$elements2  = $xpath->query('//span[@id="salePriceTag"]');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			$price = str_replace("¥", "", $element->nodeValue);
			$price = str_replace(" ", "", $price);
			$price = preg_replace('/\s+/', '', $price);
			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ราคา :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "¥ <span id='product_price'>".$price. "</span> หยวน ( ฿ ". number_format($price*$rate,2) ." บาท ) <br />( หากราคาสินค้าที่แสดงยังไม่ถูกลดราคา ลูกค้าไม่ต้องกังวล ราคาสินค้าจะถูกปรับลดหลังจากที่พนักงานตรวจสอบราคาอีกครั้ง )
						</div>
					</div>";
			// $nodes = $element->childNodes;
			// foreach ($nodes as $node) {
			// 	echo "<span id='product_price'>".$node->nodeValue. "</span>";
			// }
		}
	}
	if (!is_null($elements2)) {
		foreach ($elements2 as $element) {
			$price = str_replace("¥", "", $element->nodeValue);
			$price = str_replace(" ", "", $price);
			$price = preg_replace('/\s+/', '', $price);
			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ราคา :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "¥ <span id='product_price'>".$price. "</span> หยวน ( ฿ ". number_format($price*$rate,2) ." บาท ) <br />( หากราคาสินค้าที่แสดงยังไม่ถูกลดราคา ลูกค้าไม่ต้องกังวล ราคาสินค้าจะถูกปรับลดหลังจากที่พนักงานตรวจสอบราคาอีกครั้ง )
							</div>
						</div>";
			// $nodes = $element->childNodes;
			// foreach ($nodes as $node) {
			// 	echo "<span id='product_price'>".$node->nodeValue. "</span>";
			// }
		}
	}

	//Query Product Size
	$elements  = $xpath->query('//ul[@class="size"]');

	if (!is_null($elements)) {
		foreach ($elements as $element) {
			echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' >ขนาด :</label>
								<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "<div id='btn-group-size' class='btn-group' data-toggle='buttons'>";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "li") {
					$titles = $xpath->query("./a", $node);
					if ($titles->length > 0) {
						$title = $titles->item(0)->nodeValue;
						$title = preg_replace('/\s+/', '', $title);
						$title = translateSize($title);
						if ($title != '尺码对照表') {
							echo "<label class='btn btn-default' id='lbl_size' ><input name='product_size' id='product_size' value='$title' type='radio' >";
							echo $title;
							echo "</label>";
						}
					}
					//echo "[".$node->nodeName."]".$node->nodeValue. "/";
				}
			}
			echo "</div> <label id='alert-size'></label>
								</div>
							</div>";
		}
	}

	//Query Product Color
	$elements  = $xpath->query('//ul[@class="color"]');

	if (!is_null($elements)) {
		foreach ($elements as $element){
			echo "<div style='margin-bottom:0px;' class='form-group'>
								<label  class='col-sm-2 control-label' >สี :</label>
								<div style='text-align:left;' class='col-sm-10 control-label' >";
			echo "<div class='btn-group' data-toggle='buttons'>";
			$nodes = $element->childNodes;
			foreach ($nodes as $node) {
				if ($node->nodeName == "li") {
					$titles = $xpath->query("./a", $node);
					$img = $xpath->query("./a/img", $node);
					if ($titles->length > 0 || $span->length > 0) {
						$title = $titles->item(0)->getAttribute('title');
						$title = preg_replace('/\s+/', '', $title);
						$title = translateColor($title);
						$pic = $img->item(0)->getAttribute('src');
		  		    	$pic = str_replace("background:url(", "http:", $pic); 
		  		    	$pic = str_replace(") center no-repeat;", "", $pic); 
		  		    	echo "<label class='btn btn-default' ><input name='product_color' id='product_color' value='$title' type='radio' >";
		  		    	echo $title;
		  		    	echo "<img src='".$pic."' style='height:28px;'>";
		  		    	echo "</label>";
		  		    }
		  		}
		  	}
		  	echo "</div> <label id='alert-color'></label>
						  	</div>
							</div>";
		}
	}

	//Query Shop Name
	echo "<input type='hidden' name='shop_name' id='shop_name' value='dangdang'>";

	echo "	<div style='margin-bottom:0px;' class='form-group'>
						<label  class='col-sm-2 control-label' >จำนวน :</label>
						<div style='text-align:left;' class='col-sm-10 control-label' >
			<input type='button' value='-' class='qtyminus' field='product_amount' onclick='qtyminus()' />
			<input type='text' name='product_amount' id='product_amount' style='width: 50px;' class='qty' value='1' style='width: 50px;'
			onkeypress='return isNumber(event)'>
			<input type='button' value='+' class='qtyplus' field='product_amount' onclick='qtyplus()' />
						</div>
					</div>";

	echo "<input type='hidden' name='source' id='source' value='danddang'>";

}else{
	echo "เว็บไซด์นี้ยังไม่รองรับการค้นหาด้วย URL หรือ URL ผิดพลาด<br />
			ตัวอย่าง URL ที่ถูกต้อง เช่น <br />
			- http://item.taobao.com/item.htm?id=45445759732<br />
			- https://world.tmall.com/item/530164045182.htm?spm=a21bp.7806943.topsale_XX.1.dWLlq5<br />
			<br />
			หากต้องการสั่งสินค้าที่ไม่สามารถดึงรายละเอียดได้ โปรดกรอกข้อมูลสินค้าด้วยตัวเอง <a onclick='manualAdd();'>โดยคลิกที่นี่</a>";
}

function translateColor($color) {
    switch ($color) {
    	case '咖啡色':
    		return 'brown';
    		break;
    	case '橙色':
    		return 'orange';
    		break;
    	case '灰色':
    		return 'gray';
    		break;
    	case '白色':
    		return 'white';
    		break;
    	case '粉红色':
    		return 'pink';
    		break;
    	case '紫色':
    		return 'purple';
    		break;
    	case '红色':
    		return 'red';
    		break;
    	case '绿色':
    		return 'green';
    		break;
    	case '蓝色':
    		return 'blue';
    		break;
    	case '黄色':
    		return 'yellow';
    		break;
    	case '黑色':
    		return 'black';
    		break;
    	default:
    		break;
    }


// $BingTranslator = new BingTranslator('CNEX2015', '/jPKNWhwhR2rpcAil+Q5CioFsDxm6/RQgmUxabNinBE=');
 

// $translation = $BingTranslator->getTranslation('zh', 'en', $color);
 

// return $translation;

return $color;

}

function translateSize($size) {
    switch ($size) {
    	case '均码':
    		return 'Free Size';
    		break;
    	default:
    		break;
    }

// $BingTranslator = new BingTranslator('CNEX2015', '/jPKNWhwhR2rpcAil+Q5CioFsDxm6/RQgmUxabNinBE=');
 

// $translation = $BingTranslator->getTranslation('zh', 'en', $size);
 
 
// return $translation;

return $size;

}

function get_data($url) {
	$ch = curl_init();
	$timeout = 10;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

?>

