
<?php

include 'connect.php';
require_once('Classes/translate.class.php');

define('CFG_SERVICE_INSTANCEKEY','07a0be71-d84e-45ea-bc5f-9e4fa5bc53b4');
define('CFG_REQUEST_LANGUAGE', 'en');
 


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
}else if($bottom_host_name == "taobao.com" || $bottom_host_name == "tmall.com"){

	//show product host name
	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label class='col-sm-2 control-label' >เว็บไซด์ :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' > ".$bottom_host_name."</div>
				</div>";

	//query item id
	parse_str($parseURL['query'], $query);
	preg_match("/item\/(.*).htm/", $url, $output_array);

	if (isset($query['id'])) {
		if ($hostname == "detail.taobao.com") {
			$itemId = $query['id'];
		}else{
			$itemId = $query['id'];
		}
	}else if (isset($query['itemid'])) {
		$itemId = $query['itemid'];
	}else if($output_array[1] != ''){
		$itemId = $output_array[1];
	}else{
		//echo "cannot get url";
	}

	//$itemId = (isset($_REQUEST['itemId'])) ? $_REQUEST['itemId'] : 45844545906;
 
	$requestUrl = 'http://otapi.net/OtapiWebService2.asmx/GetItemFullInfoWithPromotions?instanceKey=' . CFG_SERVICE_INSTANCEKEY
							. '&language=' . CFG_REQUEST_LANGUAGE
							. '&itemId=' . $itemId
							. '&sessionId=&blockList=';
	 
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $requestUrl);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	 
	$result = curl_exec($curl);
	if ($result === FALSE) {
			echo "cURL Error: " . curl_error($curl); die();
	}
	$xmlObject = simplexml_load_string($result);
	 
	curl_close($curl);
	 
	if ((string)$xmlObject->ErrorCode !== 'Ok') {
			echo "Error: " . $xmlObject->ErrorDescription; die();
	}
	
	$itemInfo = $xmlObject->OtapiItemFullInfo;

	//show product url
	//echo "<input type='hidden' name='product_url' id='product_url' value='".$url."' >";

	echo "<div style='margin-bottom:0px; display: none;' class='form-group'>
					<label  class='col-sm-2 control-label' >ลิงค์ :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' ><a href='".$url."' target='_blank'><span id='product_url'>".$url."</span></a></div>
				</div>";

	//show product image
	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label  class='col-sm-2 control-label' ></label>
					<div style='text-align:left;' class='col-sm-10 control-label' >
						<a id='product_img_link' href='".(string)$itemInfo->MainPictureUrl."' data-lightbox='product-image' data-title='".(string)$itemInfo->Title."' title='ดูรูปใหญ่'>
							<img style='height:200px;' class='img-thumbnail' id='product_img' 
							src='".(string)$itemInfo->MainPictureUrl."'>
						</a>
						<br />";
						foreach ($itemInfo->Pictures->ItemPicture as $ItemPicture) {
							echo '<a><img onclick="showLargeImage(this.src)" src="'.(string)$ItemPicture->Url.'" class="ItemPicture img-thumbnail" style="margin:5px 5px 0px 0px;" width="50"/></a>';
						}
	echo		"</div>
				</div>";

	//show product name
	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label  class='col-sm-2 control-label' >ชื่อสินค้า :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' >
						<span id='product_name'>".(string)$itemInfo->OriginalTitle."</span>
					</div>
				</div>";

	$price = 0.00;

	if ($itemInfo->Promotions->OtapiItemPromotion->Price->ConvertedPriceList->DisplayedMoneys->Money != null) {
		foreach ($itemInfo->Promotions->OtapiItemPromotion->Price->ConvertedPriceList->DisplayedMoneys->Money as $Money)
		{
			$price = (string)$Money;
			//echo '1----->'.$price;
		}
	}

// 	foreach ($itemInfo->Price->ConvertedPriceList->DisplayedMoneys->Money as $Money){
// 		if ($price == 0.00) {
// 			$price = (string)$Money;
// 			echo '2----->'.$price;
// 		}
// 	}

	foreach ($itemInfo->Price->OriginalPrice as $Money){
		if ($price == 0.00) {
			$price = (string)$Money;
			//echo '2----->'.$price;
		}
	}

	//show product price
	echo "<div style='margin-bottom:0px;' class='form-group'>
					<label  class='col-sm-2 control-label' >ราคา :</label>
					<div style='text-align:left;' class='col-sm-10 control-label' >
						<span id='product_price'>".$price. "</span> หยวน ( ฿ ". number_format($price*$rate,2) ." บาท ) <br />( หากราคาสินค้าที่แสดงยังไม่ถูกลดราคา ลูกค้าไม่ต้องกังวล ราคาสินค้าจะถูกปรับลดหลังจากที่พนักงานตรวจสอบราคาอีกครั้ง )
					</div>
				</div>";

	// prepare item attributes
	$itemAttributes = array();
	if (isset($itemInfo->Attributes->ItemAttribute)) {
			foreach ($itemInfo->Attributes->ItemAttribute as $ItemAttribute) {
					$itemAttributes[(string)$ItemAttribute['Pid']]['PropertyName'] = (string)$ItemAttribute->PropertyName;
					$itemAttributes[(string)$ItemAttribute['Pid']]['IsConfigurator'] = ((string)$ItemAttribute->IsConfigurator === 'true');
					$itemAttributes[(string)$ItemAttribute['Pid']]['Values'][(string)$ItemAttribute['Vid']]['Value'] = (string)$ItemAttribute->Value;
					$itemAttributes[(string)$ItemAttribute['Pid']]['Values'][(string)$ItemAttribute['Vid']]['OriginalValue'] = (string)$ItemAttribute->OriginalValue;
					$itemAttributes[(string)$ItemAttribute['Pid']]['Values'][(string)$ItemAttribute['Vid']]['MiniImageUrl'] = (string)$ItemAttribute->MiniImageUrl;
					$itemAttributes[(string)$ItemAttribute['Pid']]['Values'][(string)$ItemAttribute['Vid']]['ImageUrl'] = (string)$ItemAttribute->ImageUrl;
			}
	}

	$iCZ=0;

	//show product color
	$flagNoColor=array(); //flag for check no size and no color choose be insert to list.(20170318)
	//show product size
	$flagNoSize=array(); //flag for check no size and no color choose be insert to list.(20170318)
	foreach ($itemAttributes as $attribute){

		if ($attribute['PropertyName'] == "primary color" || 
				$attribute['PropertyName'] == "Color Classification" ||
				$attribute['PropertyName'] == "Colour" ) {
			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >สี :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >
								<div class='btn-group' data-toggle='buttons'>";
			$ii=0;
			foreach ($attribute['Values'] as $value) {
				echo "<label class='btn btn-default' ><input name='product_color' id='product_color' value='".$value['OriginalValue']."'  type='radio' >";
				if ($value['MiniImageUrl'] != "" && $value['MiniImageUrl'] != null && isset($value['MiniImageUrl']) ) {					
					echo "<img title='".$value['OriginalValue']."' src='".$value['MiniImageUrl']."' data-largeimage='".$value['ImageUrl']."' >";
					
				}else{
					
					/*todo : modify if not found image show image original
					 * date modify : 2017-01-15
					 * 
					 * */
					//check image color <= image thum.
					if(count($attribute['Values'])<=count($itemInfo->Pictures->ItemPicture)){						
						echo '<img onclick="showLargeImage(this.src)" src="'.(string)$itemInfo->Pictures->ItemPicture[$ii]->Url.'" class="ItemPicture img-thumbnail" style="margin:5px 5px 0px 0px;" width="50"/>';
					}else{						
						echo '<img onclick="showLargeImage(this.src)" src="'.(string)$itemInfo->Pictures->ItemPicture[0]->Url.'" class="ItemPicture img-thumbnail" style="margin:5px 5px 0px 0px;" width="50"/>';
					}					//
					// end modify : 2017-01-15
					
				}
				//echo $value['Value'];
				echo "</label>";
				$ii++; //udpate image color index (modiyfy 2017-01-15
				$flagNoColor[$iCZ]='2';
			}
			echo "		</div> 
								<label id='alert-color'></label>
							</div>
						</div>";
			
		}else{
			$flagNoColor[$iCZ]='1';
			
		}
			
		if ($attribute['PropertyName'] == "size" ||
			$attribute['PropertyName'] == "Reference Height") {
			
			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ขนาด :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >
								<div id='btn-group-size' class='btn-group' data-toggle='buttons'>";
			foreach ($attribute['Values'] as $value) {
				echo "<label class='btn btn-default' id='lbl_size' ><input name='product_size' id='product_size' value='".$value['Value']."' type='radio' >";
				echo $value['Value'];
				echo "</label>";
			}
			echo "		</div>
								<label id='alert-size'></label>
							</div>
						</div>";
			
			$flagNoSize[$iCZ]='2';
			
		}else{
			$flagNoSize[$iCZ]='1';			
		}
				
		
		if($attribute['PropertyName']=='specification'){
			echo "<div style='margin-bottom:0px;' class='form-group'>
							<label  class='col-sm-2 control-label' >ขนาด :</label>
							<div style='text-align:left;' class='col-sm-10 control-label' >
								<div id='btn-group-size' class='btn-group' data-toggle='buttons'>";
			foreach ($attribute['Values'] as $value) {
				echo "<label class='btn btn-default' id='lbl_size' ><input name='product_size' id='product_size' value='".$value['Value']."' type='radio' >";
				echo $value['Value'];
				echo "</label>";
			}
			echo "		</div>
								<label id='alert-size'></label>
							</div>
						</div>";
		}
		
		$iCZ++;
	}

	//echo "<input value='noCS' name='flagCS' value='0'/>";
	if((count($flagNoColor)>0)&& (count($flagNoSize)>0)){
		if(!(in_array("2",$flagNoColor) && in_array("2",$flagNoSize))){
			echo "<input type='hidden' value='haveCS' name='flagCS' id='flagCS' />";
		}
	}else{
		echo "<input type='hidden' value='noCS' name='flagCS' id='flagCS' />";
	}
	

	//show product shop name
	echo "<input type='hidden' name='shop_name' id='shop_name' value='".(string)$itemInfo->VendorName."' >";

	//show product amount
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

	### ไม่ใช้แล้วครับผม, ใช้รวมกับ API ใน if แรก ###

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

