$(function(){
	$('.slippry').slippry({auto:true,pager:false});
	$('#shops').slick({
		arrows:false,
		autoplay:true,
		variableWidth:true,
		infinite:true,
		slidesToShow:5,
		slidesToScroll:2
	});
	if(window.innerWidth>480){
		$('#recommended').slick({
			arrows:false,
			autoplay:true,
			infinite:true,
			slidesToShow:2,
			slidesToScroll:1
		});
	}else{
		$('#recommended').slick({
			arrows:false,
			autoplay:true,
			infinite:true,
			slidesToShow:1,
			slidesToScroll:1
		});
	}
	$('#notify').on('click',function(){
		this.style.visibility = 'hidden';
	});
	var picks = document.getElementsByClassName('pick-img');
	for(var i=0;i<picks.length;i++){
		picks[i].style.backgroundImage = 'url('+picks[i].getAttribute('src')+')';
		picks[i].style.backgroundColor = '#fff';
		picks[i].style.backgroundRepeat = 'no-repeat';
		picks[i].style.backgroundSize = 'cover';
		picks[i].style.backgroundPosition = 'center';
	}
	var bigs = document.getElementsByClassName('big-img');
	for(var i=0;i<bigs.length;i++){
		bigs[i].style.backgroundImage = 'url('+bigs[i].getAttribute('src')+')';
		bigs[i].style.backgroundColor = '#fff';
		bigs[i].style.backgroundRepeat = 'no-repeat';
		bigs[i].style.backgroundSize = 'cover';
		bigs[i].style.backgroundPosition = 'center';
	}
	
	DialogBack = function(e){
		var div = document.createElement('div');
		div.className = 'header-dialog-back';
		div.onclick = function(){
			e.style.visibility = 'hidden';
		}
		return div;
	}
	login = document.getElementById('login');
	if(login) login.insertBefore(new DialogBack(login),login.firstChild);
	register = document.getElementById('register');
	if(register) register.insertBefore(new DialogBack(register),register.firstChild);
	setting = document.getElementById('setting');
	if(setting) setting.insertBefore(new DialogBack(setting),setting.firstChild);
	
	//reponsive menu
//	document.getElementById('more-btn').onclick = function(){
//		if(document.getElementById('header-group').style.display=='none'){
//			document.getElementById('header-group').style.display = 'block';
//		}else{
//			document.getElementById('header-group').style.display = 'none';
//			if(login) login.style.visibility = 'hidden';
//			if(register) register.style.visibility = 'hidden';
//			if(setting) setting.style.visibility = 'hidden';
//		}
//	}
//	resize();
});

function openLogin(){
	document.getElementById('login').style.visibility = 'visible';
	document.getElementById('register').style.visibility = 'hidden';
}

function openRegister(){
	document.getElementById('login').style.visibility = 'hidden';
	document.getElementById('register').style.visibility = 'visible';
}

function openSetting(){
	if(document.getElementById('notify') !=null){
		document.getElementById('notify').style.visibility = 'hidden';
	}
	document.getElementById('setting').style.visibility = 'visible';
}

function openNofication(){
	document.getElementById('setting').style.visibility = 'hidden';
	document.getElementById('notify').style.visibility = 'visible';
}

function resize(){
//	document.getElementById('header-group').style.display = 'inline-block';
//	//alert(window.innerWidth-760+'px');
	if(window.innerWidth>1024){
//		//alert(window.innerWidth-760+'px');
		document.getElementById('header-group').style.display = 'inline-block';
		document.getElementById('searchText').style.width = window.innerWidth-760+'px';
	}else{
//		//alert("window.innerWidth = "+window.innerWidth);
//		document.getElementById('searchText').style.width = window.innerWidth-(250)+'px';
//		//alert("searchText Width = "+ document.getElementById('searchText').style.width);
//		document.getElementById('header-group').style.display = 'none';
		document.getElementById('header-group').style.display = 'inline-block';
//		if(login) login.style.visibility = 'hidden';
//		if(register) register.style.visibility = 'hidden';
//		//if(setting) setting.style.visibility = 'hidden';
	}
}
////alert(window.innerWidth);
//if(window.innerWidth>1024){
//	//alert(window.innerWidth-760+'px');
//	document.getElementById('header-group').style.display = 'inline-block';
//	document.getElementById('searchText').style.width = window.innerWidth-760+'px';
//}else{
//	
//	document.getElementById('header-group').style.display = 'inline-block';
//	document.getElementById('searchText').style.width = window.innerWidth-1200+'px';
//}
//	window.onresize = resize;