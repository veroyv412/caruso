String.prototype.replaceAll = function(strSearch,strReplace){
	var tmp=this
	if(typeof strSearch == "undefined"){return this;}

	while(tmp.search(strSearch) != -1){
			tmp=tmp.replace(strSearch,strReplace);
	}
	return tmp;
}

NT = {
	debug:null,
	debugWindowId:'Debug',
	init:function(){},

	initDebug:function(){
		if(this.debug){this.stopDebug();}
		this.debug=document.createElement('div');
		this.debug.setAttribute('id',this.debugWindowId);
		document.body.insertBefore(this.debug,document.body.firstChild);
	},
	setDebug:function(bug){
		if(!this.debug){this.initDebug();}
		this.debug.innerHTML+=bug+'<br>';
	},
	stopDebug:function(){
		if(this.debug){
			this.debug.parentNode.removeChild(this.debug);
			this.debug=null;
		}
	},

	addLoadEvent:function(func){
		var oldonload = window.onload;
		if(typeof window.onload != 'function'){
			window.onload = func;
		}else{
			window.onload = function(){
				oldonload();
				func();
			}
		}
	},

	addEvent:function(element,evType,fn,useCapture){
		if(element.addEventListener){
			element.addEventListener(evType,fn,useCapture);
		}else if(element.attachEvent){
			element.attachEvent('on' + evType,fn);
		}else{
			element['on'+evType] = fn;
		}
	},
	getTarget:function(e){
		var target = window.event ? window.event.srcElement :
		e ? e.target : null;
		if (!target){return false;}
		return target;
	},
	stopDefault:function(e){
		if(window.event && window.event.returnValue){
			window.event.cancelBubble = true;
		}
		if (e && e.preventDefault){
			e.preventDefault();
		}
	},
	stopBubble:function(e){
		if(window.event && window.event.cancelBubble){
			window.event.cancelBubble = true;
		}
		if (e && e.stopPropagation){
			e.stopPropagation();
		}
	},
	cancelClick:function(e){
		if (window.event && window.event.cancelBubble && window.event.returnValue){
			window.event.cancelBubble = true;
			window.event.returnValue = false;
			return;
		}
		if (e && e.stopPropagation && e.preventDefault){
			e.stopPropagation();
			e.preventDefault();
		}
	},





	isIE:function(){
		return (document.all ? true : false);
	},
	isOPERA:function(){
		return (navigator.userAgent.indexOf('Opera') != -1 ? true : false);
	},
	isDOM:function(){
		return (document.getElementById && document.createTextNode ? true : false);
	},
	isEmpty:function(str){
		return ( (str == "") || (str == null) ) ? true : false;
	},
	inRange:function(iValue,lo,hi){
		try{
			var num = parseInt(iValue,10);
			if( (isNaN(num)) || (num < lo) || (num > hi) ){
				return false;
			}
			return true;
		}catch(e){
			return false;
		}
	},

	/* Cookies */

	//
	// "Internal" function to return the decoded value of a cookie
	getCookieVal:function(offset) {
		var endstr = document.cookie.indexOf (";", offset);
		if (endstr == -1) {
			endstr = document.cookie.length;
		}
		return unescape(document.cookie.substring(offset, endstr));
	},

	//
	// Function to correct for 2.x Mac date bug. Call this function to
	// fix a date object prior to passing it to SetCookie.
	fixCookieDate:function(date) {
		var base = new Date(0);
		var skew = base.getTime(); // dawn of (Unix) time - should be 0
		if (skew > 0) { // Except on the Mac - ahead of its time
			date.setTime (date.getTime() - skew);
		}
	},

	// Function to return the value of the cookie specified by "name".
	//   name - String object containing the cookie name.
	//   returns - String object containing the cookie value, or null if
	//     the cookie does not exist.
	getCookie:function(name) {
		var arg = name + "=";
		var alen = arg.length;
		var clen = document.cookie.length;
		var i = 0;
		while (i < clen) {
			var j = i + alen;
			if (document.cookie.substring(i, j) == arg) {
				return getCookieVal (j);
			}
			i = document.cookie.indexOf(" ", i) + 1;
			if (i == 0) {
				break;
			}
		}
		return null;
	},

	setCookie:function(name,value,expires,path,domain,secure) {
   		document.cookie = name + "=" + escape (value) +
      	((expires) ? "; expires=" + expires.toGMTString() : "") +
      	((path) ? "; path=" + path : "") +
      	((domain) ? "; domain=" + domain : "") +
      	((secure) ? "; secure" : "");
	},

	deleteCookie:function(name,path,domain){
		if (GetCookie(name)) {
			document.cookie = name + "=" +
				((path) ? "; path=" + path : "") +
				((domain) ? "; domain=" + domain : "") +
				"; expires=Thu, 01-Jan-70 00:00:01 GMT";
		}
	},



	// Allow only use numbers into an input
	// use into onkeydown and onkeyup event
	// <input type="text" onkeydown="return NT.onlyNumbers(event);"  onkeyup="return NT.onlyNumbers(event);" name="test" />
	onlyNumbers:function(e){
		if(window.event){
			var key = window.event.keyCode;
		}else if(e){
			var key = e.keyCode;
		}

		if( ( (key<48) || (key>57)) && (key != 8) && (key != 46) && (key != 127) && (key != 9) && (key != 110) && ( (key<37) || (key>40)) && ( (key<96) || (key>105)) ){
			return false;
		}
	},

	isValidDate:function(sDate,sFormat){
		var tmp 	= sDate.toString();

		var vDate 	= null;
		var iDay  	= 0;
		var iMonth 	= 0;
		var iYear 	= 0;
		var dt 		= new Date();


		var vMaxDays = new Array(31,31,28,31,30,31,30,31,31,30,31,30,31);
		var vMaxDaysBi = new Array(31,31,29,31,30,31,30,31,31,30,31,30,31);

		tmp	= tmp.replaceAll("/","-");

		if ( (typeof sFormat == "undefined") || (sFormat == null)){
			sFormat = "ddmmyyyy";
		}

		switch(sFormat){
			case "ddmmyyyy":
				vDate = tmp.split("-");
				iDay = 0;
				iMonth = 1;
				iYear = 2;
				break;
			case "yyyymmdd":
				vDate = tmp.split("-");
				iDay = 2;
				iMonth = 1;
				iYear = 0;
				break;
			case "yyyyddmm":
				vDate = tmp.split("-");
				iDay = 1;
				iMonth = 2;
				iYear = 0;
				break;
			case "mmddyyyy":
				vDate = tmp.split("-");
				iDay = 1;
				iMonth = 0;
				iYear = 2;
				break;
		}
		try{
			for(i=0;i<vDate.length;i++){
				vDate[i] = parseInt(vDate[i],10);
				if(isNaN(vDate[i])){
					return false;
				}
			}
			var bYear	= this.inRange(vDate[iYear],1900,2100);
			var bMonth 	= this.inRange(vDate[iMonth]);
			if((vDate[iYear] % 4) == 0){
				var bDay	= this.inRange(vDate[iDay],1,vMaxDaysBi[vDate[iMonth]]);
			}else{
				var bDay	= this.inRange(vDate[iDay],1,vMaxDays[vDate[iMonth]]);
			}


		}catch(e){
			return false
		}
		return bMonth && bDay && bYear;
	},

	lastSibling:function(node){
		var tempObj=node.parentNode.lastChild;
		while(tempObj.nodeType!=1 && tempObj.previousSibling!=null){
			tempObj=tempObj.previousSibling;
		}
		return (tempObj.nodeType==1)?tempObj:false;
	},
	firstSibling:function(node){
		var tempObj=node.parentNode.firstChild;
		while(tempObj.nodeType!=1 && tempObj.nextSibling!=null){
			tempObj=tempObj.nextSibling;
		}
		return (tempObj.nodeType==1)?tempObj:false;
	},
	getText:function(node){
		if(!node.hasChildNodes()){return false;}
		var reg=/^\s+$/;
		var tempObj=node.firstChild;
		while(tempObj.nodeType!=3 && tempObj.nextSibling!=null ||    reg.test(tempObj.nodeValue)){
			tempObj=tempObj.nextSibling;
		}
		return tempObj.nodeType==3?tempObj.nodeValue:false;
	},

	closestSibling:function(node,direction){
		var tempObj;
		if(direction==-1 && node.previousSibling!=null){
			tempObj=node.previousSibling;
			while(tempObj.nodeType!=1 && tempObj.previousSibling!=null){
				tempObj=tempObj.previousSibling;
			}
		}else if(direction==1 && node.nextSibling!=null){
			tempObj=node.nextSibling;
			while(tempObj.nodeType!=1 && tempObj.nextSibling!=null){
				tempObj=tempObj.nextSibling;
			}
		}
		return ((typeof tempObj != 'undefined') && (tempObj.nodeType==1))?tempObj:false;
	}

}