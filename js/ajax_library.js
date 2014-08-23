/**
 * Ajax.js
 *
 * Collection of Scripts to allow in page communication from browser to (struts) server
 * ie can reload part instead of full page
 *
 * How to use
 * ==========
 * 1) Call retrieveURL from the relevant event on the HTML page (e.g. onclick)
 * 2) Pass the url to contact (e.g. Struts Action) and the name of the HTML form to post
 * 3) When the server responds ...
 *		 - the script loops through the response , looking for <span id="name">newContent</span>
 * 		 - each <span> tag in the *existing* document will be replaced with newContent
 *
 * NOTE: <span id="name"> is case sensitive. Name *must* follow the first quote mark and end in a quote
 *		 Everything after the first '>' mark until </span> is considered content.
 *		 Empty Sections should be in the format <span id="name"></span>
 */

function doAlert(cad){
	alert(cad);	
}

//global variables
  var req;
  var which;
  var divUpdateDiv;
  var divArray;
  
  var ajaxLoading = "Cargando...";
  
 /**
   * Esta funci�n se utiliza para ejecutar funcionalidad despu�s de procesar el POST
   * Forma de utilizarla en la JSP invocante: postProcessFunction = new Function("informacionPregunta();");
   */
  function postProcessFunction() {}
   
  
  /**
   * Get the contents of the URL via an Ajax call
   * url - to get content from (e.g. /struts-ajax/sampleajax.do?ask=COMMAND_NAME_1) 
   * nodeToOverWrite - when callback is made
   * divName - The page area (div) where the changes will be update
   *
   */
  function retrieveURL(url,allDivsName) {
  	
  	divArray = allDivsName.split(',');
    doPOSTRequest(url,getFormAsString(document.forms[0].name));
    
/*
	//get the (form based) params to push up as part of the get request
    
    if (document.forms[0].name){
    	url=url+getFormAsString(document.forms[0].name);
    }
    //Do the Ajax call

    if (window.XMLHttpRequest) { // Non-IE browsers
      req = new XMLHttpRequest();
      req.onreadystatechange = processStateChange;
      try {
      	req.open("POST", url, true); //was get
      } catch (e) {
        alert("Problem Communicating with Server\n"+e);
      }
      req.send(null);
    } else if (window.ActiveXObject) { // IE
      
      req = new ActiveXObject("Microsoft.XMLHTTP");
      if (req) {
      	showMessageLoading();
      	req.onreadystatechange = processStateChange;
        req.open("POST", url, true);
        req.send();
      }
    }
*/
  }
  
  /**
  * Do a POST URL request
  * @param url - to get content from
  * @param parameters
  */
	function doPOSTRequest(url, parameters) {
      req = false;
      if (window.XMLHttpRequest) { // Non-IE browsers
         req = new XMLHttpRequest();
         if (req.overrideMimeType) {
            req.overrideMimeType('text/html');
         }
      } else if (window.ActiveXObject) { // IE
         req = new ActiveXObject("Microsoft.XMLHTTP");
      }
      if (req) {
		 showMessageLoading();
	     req.onreadystatechange = processStateChange;
         req.open('POST', url, true);
         req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
         req.setRequestHeader("Content-length", parameters.length);
         req.setRequestHeader("Connection", "close");
         req.send(parameters);
      } else {
         alert("Problem Communicating with Server");
         return false;
      } 
      
   }

/*
   * Set as the callback method for when XmlHttpRequest State Changes 
   * used by retrieveUrl
  */
  function processStateChange() {
  
  	  if (req.readyState == 4) { // Complete
	      if (req.status == 200) { // OK response
	       
	        ///alert("Ajax response:"+req.responseText);
	        
	        //Split the text response into Span elements
	        //spanElements = splitTextIntoSpan(req.responseText);
	        
	        //Use these span elements to update the page
	        //replaceExistingWithNewHtml(spanElements);
			
			replaceHtml(req.responseText);
	        
	      	postProcessFunction();
	        
	      } else {
	        alert("Problem with server response:\n " + req.statusText);
	      }
          hideMessageLoading();
      }

  }
 
  /**
  * Replace html elements in the existing document with new elements
  * @param responseText to analize
  */
  function replaceHtml(responseText) {
		var newDiv = document.createElement("div");
		newDiv.innerHTML = extractContent(responseText);
		
		for (j=0;j<divArray.length;j++) {
			divName = divArray[j];
			divContent = getInnerHTML(newDiv,divName);
			if (document.getElementById(divName)) {
				if (divContent) {
					document.getElementById(divName).innerHTML = divContent;
				}
			} else {
				//alert("Element:"+divName+" not found in existing document");
			}
			
		}
  }
 
 
 /**
  * gets the contents of the response html text
  * @param responseText to analize
  * @return string
  */
 function extractContent(responseText) {
	var responseTextUpperCase=responseText.toUpperCase();
	var content='';
	var startPos=responseTextUpperCase.indexOf("<DIV");
	var endPos=responseTextUpperCase.lastIndexOf('/DIV>')-1;	
	if(startPos>-1 && endPos>-1){
		content=responseText.substring(startPos,endPos);
	}
	return content;
}

 /**
  * gets the inner HTML code of the DIV element
  * @param elem to analize
  * @return string
  */
function getInnerHTML(elem,elemId) {
	var items = elem.getElementsByTagName("div");	
	for(i=0;i<items.length;i++)
	{
		if (items[i].id==elemId) return (items[i].innerHTML);
	}
	return null;
}
 
 /**
  * gets the contents of the form as a URL encoded String
  * suitable for appending to a url
  * @param formName to encode
  * @return string with encoded form values , beings with &
  */ 
 function getFormAsString(formName){
 	
 	//Setup the return String
 	returnString ="";
 	
  	//Get the form values
 	formElements=document.forms[formName].elements;
 	
 	//loop through the array , building up the url
 	//in the form /strutsaction.do&name=value
 	
 	for ( var i=formElements.length-1; i>=0; --i ){
 		//we escape (encode) each value
 		var field = formElements[i];
 		var fieldName = field.name;
 		var fieldValue = '';
		if (field.type == 'hidden' ||
            field.type == 'text' ||
            field.type == 'textarea' ||
            //field.type == 'file' ||
            field.type == 'checkbox' ||
            field.type == 'select-one' ||
            field.type == 'password') {
            if (field.type == "select-one") {
                var si = field.selectedIndex;
                if (si >= 0) {
                    fieldValue = field.options[si].value;                    
                }
            } else if (field.type == 'checkbox') {
                if (field.checked) {
                    fieldValue = field.value;
                }
            } else {
                fieldValue = field.value;                
            }
            returnString+="&"+escape(fieldName)+"="+escape(fieldValue);
        } else if (field.type == "select-multiple"){
			var numOptions = field.options.length;
			var empty = true;
            for(var j=numOptions-1;j>=0;j--) {           	
				if(field.options[j].selected) {
					empty = false;
					fieldValue = field.options[j].value;              		
	                returnString+="&"+escape(fieldName)+"="+escape(fieldValue);
               }
            }
            if (empty) {
	        	fieldValue = -1;
	            returnString+="&"+escape(fieldName)+"="+escape(fieldValue);
            }
		} else if ((field.type == 'radio')&& (field.checked)) {
			fieldValue = field.value;
            returnString+="&"+escape(fieldName)+"="+escape(fieldValue);			
		} else if (field.type == 'checkbox') {
			isChecked=-1;
          	for (loop=0;loop < field.length;loop++) {
            	if (field[loop].checked) {
                	fieldValue = field[loop].value;
	                returnString+="&"+escape(fieldName)+"="+escape(fieldValue);                	
                  	break; // only one needs to be checked
              	}
          	}
		}
 	} 	
 	//return the values
 	return returnString; 
 }
 
 /**
 * Splits the text into <span> elements
 * @param the text to be parsed
 * @return array of <span> elements - this array can contain nulls
 */
/*
 function splitTextIntoSpan(textToSplit){
 
  	//Split the document
 	returnElements=textToSplit.split("</div>")
 	
 	//Process each of the elements 	
 	for ( var i=returnElements.length-1; i>=0; --i ){
 		
 		//Remove everything before the 1st span
 		spanPos = returnElements[i].indexOf("<div");		
 		
 		//if we find a match , take out everything before the span
 		if(spanPos>0){
 			subString=returnElements[i].substring(spanPos);
 			returnElements[i]=subString;
 		
 		} 
 	}
 	
 	return returnElements;
 }
*/
 
 /*
  * Replace html elements in the existing (ie viewable document)
  * with new elements (from the ajax requested document)
  * WHERE they have the same name AND are <span> elements
  * @param newTextElements (output of splitTextIntoSpan)
  *					in the format <span id=name>texttoupdate
  */
/*
 function replaceExistingWithNewHtml(newTextElements){
 
 	//loop through newTextElements
 	for ( var i=newTextElements.length-1; i>=0; --i ){
  
 		//check that this begins with <span
 		if(newTextElements[i].indexOf("<div")>-1){
 			
 			//get the name - between the 1st and 2nd quote mark
 			startNamePos=newTextElements[i].indexOf('"')+1;
 			endNamePos=newTextElements[i].indexOf('"',startNamePos);
 			name=newTextElements[i].substring(startNamePos,endNamePos);
 			
 			//get the content - everything after the first > mark
 			startContentPos=newTextElements[i].indexOf('>')+1;
 			content=newTextElements[i].substring(startContentPos);
 			//Now update the existing Document with this element
 			if (name==divUpdateDiv1 || name==divUpdateDiv2) {
 			    //check that this element exists in the document
 				if(document.getElementById(name)){
	 				//alert("Replacing Element:"+name);
	 				document.getElementById(name).innerHTML = content;
	 			} else {	 				 			
	 				//alert("Element:"+name+"not found in existing document");	 			
	 			}
	 		}
 		}
 	}
 }
*/
 
 /**
*  Default sample loading message show function. Overrride it if you like.
*/
function showMessageLoading() {

    var div = document.getElementById("AA_" + this.id + "_loading_div");
   
    if (div == null) {
    	div = document.createElement("DIV");
    	document.body.appendChild(div);
        div.id = "AA_" + this.id + "_loading_div";

        //div.innerHTML = "&nbsp;"+ajaxLoading;
        div.innerHTML = "&nbsp;";
        div.style.position = "absolute";
        div.style.border = "1 solid black";
        div.style.color = "white";
        //div.style.backgroundColor = "#63213A";
        div.style.backgroundImage = "url('images/ajax-loader2.gif')";
        div.style.backgroundRepeat = "no-repeat";
        div.style.width = "100px";
        div.style.heigth = "100px";
        div.style.fontFamily = "Arial, Helvetica, sans-serif";
        div.style.fontWeight = "bold";
        div.style.fontSize = "11px";
        
    }
    div.style.top = document.body.scrollTop + "px";
    div.style.left = (document.body.offsetWidth - 100 - (document.all?20:0)) + "px";

    div.style.display = "";
}

/**
*  Default sample loading message hide function. Overrride it if you like.
*/
function hideMessageLoading() {
    var div = document.getElementById("AA_" + this.id + "_loading_div");
    if (div != null){
    	div.style.display = "none";
	}
}
