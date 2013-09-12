
var clockID = 0;
var offsetTime = 0;
var conversation_edit_window;

function UpdateClock() {
   if(clockID) {
      clearTimeout(clockID);
      clockID  = 0;
   }

   var tDate = new Date();
   
   tDate.setTime(tDate.getTime() + offsetTime*1000);
   timezoneoffset=(tDate.getTimezoneOffset()/60)-7;

	 clockElement = document.getElementById("clock");
	 
	 year = tDate.getFullYear();
	 month = tDate.getMonth()+1;
	 if (month < 10) month = "0" + month;
	 day = tDate.getDate();
	 if (day < 10) day = "0" + day;
	 hours = tDate.getHours()+timezoneoffset;
	 if (hours < 0) hours += 24;
	 if (hours < 10) hours = "0" + hours;
	 minutes = tDate.getMinutes();
	 if (minutes < 10) minutes = "0" + minutes;
	 seconds = tDate.getSeconds();
	 if (seconds < 10) seconds = "0" + seconds;
	 
   clockElement.innerHTML = "Current Time:<br /> "
                                   + year + "-" 
                                   + month + "-" 
                                   + day + " " 
                                   + hours + ":" 
                                   + minutes + ":" 
                                   + seconds;
   
   clockID = setTimeout("UpdateClock()", 1000);
}

function StartClock(db_time) {
   var sDate = new Date();
	 offsetTime = db_time - sDate.getTime()/1000;
   clockID = setTimeout("UpdateClock()", 500);
}

function KillClock() {
   if(clockID) {
      clearTimeout(clockID);
      clockID  = 0;
   }
}


function edit_window(name, type, category, author){
	eval('window.open("description_edit.php5?name=' + name + '&type=' + type + '&category=' + category + '&author=' + author + '","","width=600px,height=400px,resizable=1,scrollbars=1")');
}

function close_edit_window() {
// this will close the pop up window
	window.close();
// this will reload the parent window...
	if (!window.opener.closed) {
		window.opener.location.reload();
		window.opener.focus();
	}
}

function conversation_window(topic_id, parent_id, message_id, type, category) {
	eval('window.open("conversation_edit.php5' +
  	'?topic_id=' + topic_id + 
  	'&parent_id=' + parent_id + 
  	'&message_id=' + message_id + 
  	'&type=' + type + 
  	'&category=' + category + 
		'","conversation_edit_window","width=600px,height=500px,resizable=1,scrollbars=1,menubar=true")');
}

function confirm_link(message, href) {
	var conf = window.confirm('Are you sure?');
	if (conf) location.href=href;
}

function new_concept_prompter(addr) {
	var reply = prompt("What is the topic?", "");
	if(!reply){ return; }
	concept = document.getElementById("concept");
	
	concept.href=addr+'&action=new_concept&concept='+reply;
}

function new_concept_button() {
	var reply = prompt("What is the topic?", "");
	if(!reply){
		subview =	document.getElementById("concept_subview");		 
		subview.value='topics';
		action_field =	document.getElementById("concept_action");		 
		action_field.value='';
		return; 
	}

	concept = document.getElementById("concept");
	concept.value=reply;
	
}