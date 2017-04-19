
function isPhoneNumber(evt) {
	//Enable arrow for firefox.
	if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1) {
			if (evt.keyCode == 8 || evt.keyCode == 46 || evt.keyCode == 37 || evt.keyCode == 39) {
				return true;
		}
	}

		evt = (evt) ? evt : window.event;
		var charCode = (evt.which) ? evt.which : evt.keyCode;

		//Enable dot.
		if (charCode == 45) { return true; };

		if (charCode > 31 && (charCode < 48 || charCode > 57 )) {
				return false;
		}
		return true;
}

function checkLogin() {

	var flag=0;

		var email = document.getElementById('login-email');
		var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

		if (!filter.test(email.value)) {
			document.getElementById('help-login-email').innerText = "กรุณากรอกอีเมล์ให้ถูกต้อง";
			email.focus;
			flag = 1;
	}

	if (document.getElementById('login-password').value == "") {
		document.getElementById('help-login-password').innerText = "กรุณากรอกรหัสผ่าน";
		flag = 1;
	}else{
		document.getElementById('help-login-password').innerText = "";
	}

	if (flag == 1) {return false;};
}

function validateRegisterForm(){

	var flag=0;

	var email = document.getElementById('register-email');
		var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;

		if (!filter.test(email.value)) {
			document.getElementById('help-register-email').innerText = "กรุณากรอกอีเมล์ให้ถูกต้อง";
			flag = 1;
	}else{

		if (document.getElementById('help-register-email').innerText == "อีเมล์นี้ถูกใช้งานไปแล้ว!") {
			flag = 1;
		}else{   
			document.getElementById('help-register-email').innerText = "";
		}
	}

	if (document.getElementById('register-password').value == "") {
		document.getElementById('help-register-password').innerText = "กรุณากรอกรหัสผ่าน";
		flag = 1;
	}else{
		document.getElementById('help-register-password').innerText = "";
	}

	if (document.getElementById('register-firstname').value == "") {
		document.getElementById('help-register-firstname').innerText = "กรุณากรอกชื่อ";
		flag = 1;
	}else{
		document.getElementById('help-register-firstname').innerText = "";
	}

	if (document.getElementById('register-lastname').value == "") {
		document.getElementById('help-register-lastname').innerText = "กรุณากรอกนามสกุล";
		flag = 1;
	}else{
		document.getElementById('help-register-lastname').innerText = "";
	}

	if (document.getElementById('register-phone').value == "") {
		document.getElementById('help-register-phone').innerText = "กรุณากรอกเบอร์โทรศัพท์";
		flag = 1;
	}else{
		document.getElementById('help-register-phone').innerText = "";
	}



	if (flag == 1) {return false;};
}

$(document).ready(function(){
	$("#register-email").blur(function()
				{
						$.ajax({
								type: "POST",
								data: {
										email: $('#register-email').val(),
								},
								url: "emailexists.php",
								success: function(data)
								{
										if(data === 'USER_EXISTS')
										{
												$('#help-register-email')
														.css('color', 'red')
														.html("อีเมล์นี้ถูกใช้งานไปแล้ว!");
										}
										else if(data === 'USER_AVAILABLE')
										{
											if ($('#register-email').val() == "") {
												$('#help-register-email')
															.html("กรุณากรอกอีเมล์");
											}else{ 
													$('#help-register-email')
															.css('color', 'green')
															.html("ท่านสามารถใช้งานอีเมล์นี้ได้");
												}
										}
								}
						})              
				}
		)

});



function searchURL(){
	
	$('#search_mdl').modal('show');
	var req;
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	}
	else if (window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP"); 
	}
	else{
		alert("Browser error");
		return false;
	}
	req.onreadystatechange = function()
	{
		if (req.readyState == 4) {
			var resultarea = document.getElementById('show_search_result');
			resultarea.innerHTML = req.responseText;
			
			$('#tblOrderList tbody').html("");
			if($('#tblOrderList tbody tr').length <= 0){			
				//$('#addtocart_button1').attr('disabled','disabled');
				$('#alert-size-orderlist,#alert-color-orderlist').text('');
			}
		}
		else
		{
			var resultarea = document.getElementById('show_search_result');
			resultarea.innerHTML = "<center><img class='img-responsive' src=progress_bar3.gif><br /><br /><small>การรวบรวมข้อมูลอาจใช้เวลาสักระยะ หากข้อมูลสินค้ามีจำนวนมาก</small></center>";
		}
	}
	var searchValue = document.getElementById('searchText').value;

	req.open("GET", "grab2.php?url="+encodeURIComponent(searchValue), true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
	req.send(null); 


	// Timeout to abort in 10 seconds
	// var xmlHttpTimeout=setTimeout(ajaxTimeout,30000);
	// function ajaxTimeout(){
//  			req.abort();
//  			alert("Request timed out");
	// }

}

function clearURL(){
	document.getElementById('searchText').value = "";
}

function itemURL(url){
	$('#search_mdl').modal('show');
	var req;
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	}
	else if (window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP"); 
	}
	else{
		alert("Browser error");
		return false;
	}
	req.onreadystatechange = function()
	{
		if (req.readyState == 4) {
			var resultarea = document.getElementById('show_search_result');
			resultarea.innerHTML = req.responseText;
		}
		else
		{
			var resultarea = document.getElementById('show_search_result');
			resultarea.innerHTML = "<center><img class='img-responsive' src=progress_bar3.gif><br /><br /><small>การรวบรวมข้อมูลอาจใช้เวลาสักระยะ หากข้อมูลสินค้ามีจำนวนมาก</small></center>";

		}
	}

	//alert(url);
	var searchValue =  decodeURIComponent(url);
	//alert(searchValue);

	req.open("GET", "grab2.php?url="+encodeURIComponent(url), true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
	req.send(null); 
	// Timeout to abort in 10 seconds
	// var xmlHttpTimeout=setTimeout(ajaxTimeout,30000);
	// function ajaxTimeout(){
//  			req.abort();
//  			alert("Request timed out");
	// }

}

function manualAdd(){
	$('#search_mdl').modal('hide');
	$('#manualadd').modal('show');
	var resultarea = document.getElementById('show_manualadd');
	//resultarea.innerHTML = "req.responseText";
}


function addtocart(){

	if($('#tblOrderList tbody tr').length <= 0){			
		//$('#addtocart_button1').attr('disabled','disabled');
		document.getElementById('alert-add-orderlist').innerHTML = 'กรุณากดปุ่ม เพิ่มในรายการ';
		document.getElementById('alert-add-orderlist').style.color = 'red';
		return false;
	}
	
	var product_url = document.getElementById('product_url').innerHTML;
	var product_img = document.getElementById('product_img').src;
	var product_name = document.getElementById('product_name').innerHTML;
	var product_price = document.getElementById('product_price').innerHTML;
	
	/**
	 * todo: convert to array list
	 */
//	var product_size = $('input[id=product_size]:checked').val();
//	var product_color = $('input[id=product_color]:checked').val();
//	var product_quentity = document.getElementById('product_amount').value;
	
//	var product_size = $('input[name=product_size_list]').val();
//	var product_color = $('input[name=product_color_list]').val();
//	var product_quentity=$('input[name=product_amount_list]').val();
	//var product_quentity = document.getElementById('product_amount_list').value;
	
	var product_size_list_arr= $('.product_size_list').map(function(){
        return $(this).val()
    }).get();
	
	var product_color_list_arr =$('.product_color_list').map(function(){
        return $(this).val()
    }).get();
	
	var product_amount_list_arr= $('.product_amount_list').map(function(){
        return $(this).val()
    }).get();
	
	var product_image_list_arr=$('.product_image_list').map(function(){

		return $(this).val()
	
	}).get();
	
	var product_size=product_size_list_arr;
	var product_color=product_color_list_arr;
	var product_quentity=product_amount_list_arr;
	var product_img=product_image_list_arr;
	
	console.log(product_size);
	console.log(product_color);
	console.log(product_quentity);
	console.log(product_img);
	
	/**
	 * end todo convert to array list
	 */
	
	var shop_name = document.getElementById('shop_name').value;
	var source = document.getElementById('source').value;

//	if (document.getElementById('product_size')!= null) {
//		if (typeof product_size == 'undefined') { 
//		document.getElementById('alert-size').innerHTML = 'Please select size';
//		document.getElementById('alert-size').style.color = 'red';
//		return false; 
//		}
//	}else{
//		product_size = 'No Size';
//	}
//	
//	if (document.getElementById('product_color')!= null) {
//		if (typeof product_color == 'undefined') { 
//			document.getElementById('alert-color').innerHTML = 'Please select color';
//			document.getElementById('alert-color').style.color = 'red';
//			return false; 
//		}
//	}else{
//		product_color = 'No Color';
//	}
	
	$.post("addtocart.php",{product_url:product_url,product_img:product_img,product_name:product_name,
		product_price:product_price,product_size:product_size,product_color:product_color,product_quentity:product_quentity,
		shop_name:shop_name,source:source},function(res){
			if (res) {
			var resultarea = document.getElementById('show_addtocart_result');
			resultarea.innerHTML = res;
			$('#search_mdl').modal('hide');
			$('#addtocart').modal('show');
		}
		else
		{
			var resultarea = document.getElementById('show_addtocart_result');
			resultarea.innerHTML = "<img src='progress_bar.gif'>";
			//document.getElementById('addtocart_button1').disabled = true;
		}
		});

//	var req;
//	if (window.XMLHttpRequest) {
//		req = new XMLHttpRequest();
//	}
//	else if (window.ActiveXObject) {
//		req = new ActiveXObject("Microsoft.XMLHTTP"); 
//	}
//	else{
//		alert("Browser error");
//		return false;
//	}
//	req.onreadystatechange = function()
//	{
//		if (req.readyState == 4) {
//			var resultarea = document.getElementById('show_addtocart_result');
//			resultarea.innerHTML = req.responseText;
//			$('#search_mdl').modal('hide');
//			$('#addtocart').modal('show');
//		}
//		else
//		{
//			var resultarea = document.getElementById('show_addtocart_result');
//			resultarea.innerHTML = "<img src='progress_bar.gif'>";
//			document.getElementById('addtocart_button').disabled = true;
//		}
//	}
//
//	req.open("POST","addtocart.php",true);
//	req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
//	req.send("product_url="+product_url+"&product_img="+product_img+"&product_name="+product_name+
//		"&product_price="+product_price+"&product_size="+product_size+"&product_color="+product_color+
//		"&product_quentity="+product_quentity+"&shop_name="+shop_name+"&source="+source);
}

function manual_addtocart(){

	var product_url = document.getElementById('mproduct_url').value;
	var product_img = document.getElementById('mproduct_img').value;
	var product_name = document.getElementById('mproduct_name').value;
	var product_price = document.getElementById('mproduct_price').value;
	var product_size = document.getElementById('mproduct_size').value;
	var product_color = document.getElementById('mproduct_color').value;
	var product_quentity = document.getElementById('mproduct_quantity').value;
	var shop_name = document.getElementById('mshop_name').value;
	var source = document.getElementById('msource').value;

	if (product_url== '') {
		alert('กรุณาใส่ ลิงค์เว็บไซด์');
		return false;
	}
	if (product_name== '') {
		alert('กรุณาใส่ ชื่อสินค้า');
		return false;
	}
	if (product_img == '') {product_img = 'img/x6.png';};
	if (shop_name == '') {shop_name = 'ไม่ระบุ';};
	if (product_size == '') {product_size = 'No Size';};
	if (product_color == '') {product_color = 'No Color';};
	if (product_price== '') {
		alert('กรุณาใส่ ราคา');
		return false;
	}
	if (product_quentity== '') {
		alert('กรุณาใส่ จำนวน');
		return false;
	}

	$('#manualadd').modal('hide');
	$('#addtocart').modal('show');
	var req;
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	}
	else if (window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP"); 
	}
	else{
		alert("Browser error");
		return false;
	}
	req.onreadystatechange = function()
	{
		if (req.readyState == 4) {

			var resultarea = document.getElementById('show_addtocart_result');
			resultarea.innerHTML = req.responseText;
		}
		else
		{
			var resultarea = document.getElementById('show_addtocart_result');
			resultarea.innerHTML = "<img src=progress_bar.gif>";

		}
	}

	req.open("POST","addtocart.php",true);
	req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	req.send("product_url="+product_url+"&product_img="+product_img+"&product_name="+product_name+
		"&product_price="+product_price+"&product_size="+product_size+"&product_color="+product_color+
		"&product_quentity="+product_quentity+"&shop_name="+shop_name+"&source="+source);
}

function qtyplus(){
	var amout = document.getElementById('product_amount').value;
	var currentVal = parseInt(amout);
	if (!isNaN(currentVal)) {
		document.getElementById('product_amount').value = currentVal+1;
	}else {
					// Otherwise put a 0 there
					document.getElementById('product_amount').value = 0;
			}
	
}

function qtyminus(){
	var amout = document.getElementById('product_amount').value;
	var currentVal = parseInt(amout);
	if (!isNaN(currentVal) && currentVal > 0) {
		document.getElementById('product_amount').value = currentVal-1;
	}else {
					// Otherwise put a 0 there
					document.getElementById('product_amount').value = 0;
			}
}

function forgetpass(){
	swal({   title: "ลืมรหัสผ่าน",   
		text: "โปรดกรอกอีเมล์ที่ท่านใช้งานค่ะ",   
		type: "input",   
		showCancelButton: true,   
		cancelButtonText: "ยกเลิก",
		confirmButtonText: "ตกลง",
		closeOnConfirm: false,   
		animation: true,   
		inputPlaceholder: "อีเมล์" 
	}, 
	function(inputValue){   
		if (inputValue === false) return false;      
		if (inputValue === "") {     
			swal.showInputError("คุณจำเป็นต้องกรอกอีเมล์ค่ะ!");     
			return false   
		}

		//request php
		var req;
		if (window.XMLHttpRequest) {
			req = new XMLHttpRequest();
		}
		else if (window.ActiveXObject) {
			req = new ActiveXObject("Microsoft.XMLHTTP"); 
		}
		else{
			alert("Browser error");
			return false;
		}
		req.onreadystatechange = function()
		{
			if (req.readyState == 4) {
				//alert('deleted');
				if (req.responseText == "SUCCESS") {
					swal("ลืมรหัสผ่าน", "โปรดตรวจสอบอีเมล์ " + inputValue + " เพื่อทำการตั้งรหัสผ่านใหม่ค่ะ", "success"); 
				}else{
					sweetAlert("ลืมรหัสผ่าน", req.responseText, "error");
				}
			}
			else
			{
				swal.disableButtons();
				//alert('wait');
			}
		}
		req.open("GET", "resetpassword.php?request_reset_password=1&email="+inputValue, true);	// ส่งค่าไปประมวลผลที่ไฟล์ sql.php
		req.send(null); 

		//swal("Nice!", "You wrote: " + inputValue, "success"); 
	});
}

$.fn.pageMe = function(opts){
		var $this = this,
				defaults = {
						perPage: 7,
						showPrevNext: false,
						hidePageNumbers: false
				},
				settings = $.extend(defaults, opts);
		
		var listElement = $this;
		var perPage = settings.perPage; 
		var children = listElement.children();
		var pager = $('.pager');
		
		if (typeof settings.childSelector!="undefined") {
				children = listElement.find(settings.childSelector);
		}
		
		if (typeof settings.pagerSelector!="undefined") {
				pager = $(settings.pagerSelector);
		}
		
		var numItems = children.size();
		var numPages = Math.ceil(numItems/perPage);

		pager.data("curr",0);
		
		if (settings.showPrevNext){
				$('<li><a href="#" class="prev_link">«</a></li>').appendTo(pager);
		}
		
		var curr = 0;
		while(numPages > curr && (settings.hidePageNumbers==false)){
				$('<li><a href="#" class="page_link">'+(curr+1)+'</a></li>').appendTo(pager);
				curr++;
		}
		
		if (settings.showPrevNext){
				$('<li><a href="#" class="next_link">»</a></li>').appendTo(pager);
		}
		
		pager.find('.page_link:first').addClass('active');
		pager.find('.prev_link').hide();
		if (numPages<=1) {
				pager.find('.next_link').hide();
		}
		pager.children().eq(1).addClass("active");
		
		children.hide();
		children.slice(0, perPage).show();
		
		pager.find('li .page_link').click(function(){
				var clickedPage = $(this).html().valueOf()-1;
				goTo(clickedPage,perPage);
				return false;
		});
		pager.find('li .prev_link').click(function(){
				previous();
				return false;
		});
		pager.find('li .next_link').click(function(){
				next();
				return false;
		});
		
		function previous(){
				var goToPage = parseInt(pager.data("curr")) - 1;
				goTo(goToPage);
		}
		 
		function next(){
				goToPage = parseInt(pager.data("curr")) + 1;
				goTo(goToPage);
		}
		
		function goTo(page){
				var startAt = page * perPage,
						endOn = startAt + perPage;
				
				children.css('display','none').slice(startAt, endOn).show();
				
				if (page>=1) {
						pager.find('.prev_link').show();
				}
				else {
						pager.find('.prev_link').hide();
				}
				
				if (page<(numPages-1)) {
						pager.find('.next_link').show();
				}
				else {
						pager.find('.next_link').hide();
				}
				
				pager.data("curr",page);
				pager.children().removeClass("active");
				pager.children().eq(page+1).addClass("active");
				
				gotoTop();
		}
};

$(document).ready(function() {
	
//	$("#addtocart_button1").on('click',function(e){
//		
//	});

	$("#show_search_result").on("change","#product_color",function(){
			//alert(this.value);
			if ($( this ).parent().find("img").attr("src") != null) {
				var tag = $( this ).parent().find("img").attr("src");
				//alert(tag);
				tag = tag.replace("30x30", "300x300");
				tag = tag.replace("40x40", "300x300");
				if (tag.match(/amazon.*/)) {
					tag = tag.substr(0, tag.lastIndexOf('.')) || tag;
					tag = tag.substr(0, tag.lastIndexOf('.')) || tag;
					tag = tag+".jpg";
				}
				//alert(tag);
				if (tag!="") {
					$("#product_img").attr("src",tag);
				};
				
			};

	});
	
	$('#addOrderToList').on('click',function(){
		
		$('#alert-size-orderlist,#alert-color-orderlist').text('');
		
		var product_size = $('input[id=product_size]:checked').val();
		var product_color = $('input[id=product_color]:checked').val();
		var product_amount=$('input[id=product_amount]').val();
		var imgOrderList=$('input[id=product_color]:checked').parent().find('img').attr('src');
		
		var imgOrderListLarge=$('input[id=product_color]:checked').parent().find('img').attr('src');
		//var imgOrderListLarge=$('input[id=product_color]:checked').parent().find('img').attr('data-largeimage');
		
		
		var flagCZ=$('#flagCS').val();		
		
		var flagForSizeAndColor=false;
		var flagForSizeAndColorCZ=false;
		//Check size of order.
		
		if(typeof flagCZ!='undefined'){
			
			var imgOrderListLargeCZ=$('#product_img').attr('src');
			imgOrderListLarge=imgOrderListLargeCZ;
			//alert("flagCZ"+flagCZ);
			if(flagCZ=='haveCS'){
				flagForSizeAndColorCZ=false;
				flagForSizeAndColor=true;
			}else{
				flagForSizeAndColorCZ=true;
				flagForSizeAndColor=false;
			}
			
		}else{
			
			if(typeof product_size == 'undefined' && typeof product_color=='undefined'){		
				document.getElementById('alert-color-orderlist').innerHTML = 'กรุณาเลือกสีและไซส์';
				document.getElementById('alert-color-orderlist').style.color = 'red';
				return false;
			}
			if (typeof product_color == 'undefined') { 
				document.getElementById('alert-color-orderlist').innerHTML = 'กรุณาเลือกสี';
				document.getElementById('alert-color-orderlist').style.color = 'red';
				flagForSizeAndColor=false;
			}else{
				flagForSizeAndColor=true;
				document.getElementById('alert-color-orderlist').innerHTML = '';
			}
			
			var tag = "";
			if(typeof imgOrderListLarge != undefined){
				tag=imgOrderListLarge;

				tag = tag.replace("30x30", "300x300");
				tag = tag.replace("40x40", "300x300");
				if (tag.match(/amazon.*/)) {
					tag = tag.substr(0, tag.lastIndexOf('.')) || tag;
					tag = tag.substr(0, tag.lastIndexOf('.')) || tag;
					tag = tag+".jpg";
				}			
				
				if(tag!=''){
					imgOrderListLarge=tag;
				}			
				
			}
			//console.log(tag);
					
			
	
			if (document.getElementById('product_size')!= null) {
				if (typeof product_size == 'undefined') { 
					document.getElementById('alert-size-orderlist').innerHTML = 'กรุณาเลือก ไซส์';
					document.getElementById('alert-size-orderlist').style.color = 'red';
					flagForSizeAndColor=false;
					return false; 
				}else{
					flagForSizeAndColor=true;
					document.getElementById('alert-size-orderlist').innerHTML = '';
				}
			}else{		
				flagForSizeAndColor=true;
				product_size = 'No Size';
			}
			
			if (document.getElementById('product_color')!= null) {
				if (typeof product_color == 'undefined') { 
					document.getElementById('alert-color-orderlist').innerHTML = 'กรุณาเลือกสี';
					document.getElementById('alert-color-orderlist').style.color = 'red';
					flagForSizeAndColor=false;
				}else{
					
					flagForSizeAndColor=true;
					document.getElementById('alert-color-orderlist').innerHTML = '';
				}
			}else{			
				flagForSizeAndColor=true;
				product_color = 'No Color';
			}			
		}


		
		
			//alert(flagForSizeAndColor);
		//have color
		if(flagForSizeAndColor){			
			var i=$('#tblOrderList tbody tr').length;
			var htmlTrOrderList='<tr>';
				htmlTrOrderList+='<td>'+(i+1)+'</td>';
				htmlTrOrderList+='<td><input type="hidden" class="product_color_list" name="product_color_list[]" id="product_color_list[]" value="'+((typeof product_color !='undefined')?product_color:'-')+'"/>';
				htmlTrOrderList+='<input type="hidden" class="product_image_list" name="product_image_list[]" id="product_image_list[]" value="'+((typeof imgOrderListLarge !='undefined')?imgOrderListLarge:'-')+'"/>';
				htmlTrOrderList+='<div><img style="width:30px;" title="" src="'+((typeof imgOrderList != 'undefined')?imgOrderList:'-')+'" data-largeimage="'+((typeof imgOrderList != 'undefined')?imgOrderList:'-')+'"></div>';
				htmlTrOrderList+='<div>'+((typeof product_color !='undefined')?product_color:'-')+'</div>';
				htmlTrOrderList+='</td>';
				htmlTrOrderList+='<td><input type="hidden" class="product_size_list" name="product_size_list[]" id="product_size_list[]" value="'+product_size+'" />'+product_size+'</td>';
				htmlTrOrderList+='<td><input type="hidden" class="product_amount_list" name="product_amount_list[]" id="product_amount_list[]" value="'+((typeof product_amount != 'undefinded')?product_amount:'0')+'" />'+((typeof product_amount != 'undefinded')?product_amount:'-')+'</td>';
				htmlTrOrderList+='<td><a onclick="deleteItemOrderList(this);" class="delete" title="ลบสินค้า">✖</a></td>';
				htmlTrOrderList+='</tr>';
			$('#tblOrderList > tbody').append(htmlTrOrderList);
			i++;				
			if(i > 0){					
				//$('#addtocart_button1').removeAttr('disabled');
			}
		}else if(flagForSizeAndColorCZ){
			//alert(product_color);
			var i=$('#tblOrderList tbody tr').length;
			var htmlTrOrderList='<tr>';
				htmlTrOrderList+='<td>'+(i+1)+'</td>';
				htmlTrOrderList+='<td><input type="hidden" class="product_color_list" name="product_color_list[]" id="product_color_list[]" value="-"/>';
				htmlTrOrderList+='<input type="hidden" class="product_image_list" name="product_image_list[]" id="product_image_list[]" value="'+((typeof imgOrderListLarge !='undefined')?imgOrderListLarge:'-')+'"/>';				
				htmlTrOrderList+='<div>No Color</div>';
				htmlTrOrderList+='</td>';
				htmlTrOrderList+='<td><input type="hidden" class="product_size_list" name="product_size_list[]" id="product_size_list[]" value="-" />No Size</td>';
				htmlTrOrderList+='<td><input type="hidden" class="product_amount_list" name="product_amount_list[]" id="product_amount_list[]" value="'+((typeof product_amount != 'undefinded')?product_amount:'0')+'" />'+((typeof product_amount != 'undefinded')?product_amount:'-')+'</td>';
				htmlTrOrderList+='<td><a onclick="deleteItemOrderList(this);" class="delete" title="ลบสินค้า">✖</a></td>';
				htmlTrOrderList+='</tr>';
			$('#tblOrderList > tbody').append(htmlTrOrderList);
			i++;				
			if(i > 0){					
				//$('#addtocart_button1').removeAttr('disabled');
			}
		}
	

	});
});

function deleteItemOrderList(o){	
	var indexOfTr=$(o).closest('tr').index();
	var obj=$('#tblOrderList tbody tr').eq(indexOfTr);
	obj.hide(300,function(){
		$(this).remove();
		var i=$('#tblOrderList tbody tr').length;
		for(var ii=0;ii<i;++ii){
			$('#tblOrderList tbody tr:eq('+ii+') td:first').text((ii+1));
		}
		
		if($('#tblOrderList tbody tr').length <= 0){			
			//$('#addtocart_button1').attr('disabled','disabled');
		}
	});
}

function gotoTop(){
	$('html, body').animate({ scrollTop: 0 }, 'slow');
}







