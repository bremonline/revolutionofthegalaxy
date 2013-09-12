
var TIMEOUT_PERIOD = 9000;
var WARNING_TIME=300;

var panes = new Array();
var mainActiveNav;

var _player_name;
var _chat_type;
var _pane_name;
var _time_until_inactive;
var _timerId = 0;
var _mTimer = 0;
var _cTimer = 0;
var _last = 0;
var _chat_active = true;
var _choice_up = '';

var playerReq = getXmlHttpRequestObject();
var sendMessageReq = getXmlHttpRequestObject();
var getMessagesReq = getXmlHttpRequestObject();
var resetReq = getXmlHttpRequestObject();


function setupPanes() {
	
  panes = new Array();  // List of all the panes available, only one will be displayed, but all will have chat contant
  var maxHeight = 0; var maxWidth = 0;
  var chat_panel = document.getElementById('chat_panel');
  var paneList = chat_panel.childNodes;

  for (var i=0; i < paneList.length; i++ ) {
    var pane = paneList[i];
    if (pane.nodeType != 1) continue;

    panes[pane.id] = pane;
    pane.style.display = "none";
  }

  chat_panel.style.height = "500px";
  chat_panel.style.width  = "750px";
  

  return true;
}
////// Ajax Functions Below

function startChat(player_name) {
	_player_name = player_name
	reset_online_time(player_name, false);
	
	checkPlayers();
	_rcTimer =  setTimeout('reset_all_choices();', 1000);
}

function getXmlHttpRequestObject() {
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else if(window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		document.getElementById('chat_status').innerHTML = 'Status: invalid XmlHttpRequest Object.  Use a standard browser.';
	}
}

function checkPlayers() {
		if (playerReq.readyState == 4 || playerReq.readyState == 0) {
			playerReq.open("GET", 'chat_server.php5?action=players&player_name=' + _player_name, true);
			playerReq.onreadystatechange = handleReceivePlayers; 
			playerReq.send(null);
	}
}

function setup_people_panel(online_type) {
	var chat_people_panel = document.getElementById('chat_people');

	var xmldoc = playerReq.responseXML;
	var current_time_node = xmldoc.getElementsByTagName("time");
	var current_time = current_time_node[0].firstChild.nodeValue;

	chat_people_panel.innerHTML = "Players&nbsp;Online<hr/>";
	var player_nodes = xmldoc.getElementsByTagName(online_type); 		
	var n_players = player_nodes.length
	for (i = 0; i < n_players; i++) {
		var player_name_node = player_nodes[i].getElementsByTagName("player_name");
		var name = player_name_node[0].firstChild.nodeValue;
		var last_online_node = player_nodes[i].getElementsByTagName("last_online");
		var last_online = last_online_node[0].firstChild.nodeValue;
		var timeDiff = current_time - last_online;
	
	
		newdiv = document.createElement('div');
		newdiv.setAttribute('class','player_node');
		newdiv.setAttribute('id','player_' + name);
		newdiv.innerHTML += name;
	
		set_player_color(newdiv, timeDiff);
		chat_people_panel.appendChild(newdiv);	
	}
	
}

function handleReceivePlayers() {
	if (playerReq && playerReq.readyState == 4) {

		var status_panel = document.getElementById('chat_status');
		var chat_people_panel = document.getElementById('chat_people');
		var xmldoc = playerReq.responseXML;

		if (xmldoc) {
			var current_time_node = xmldoc.getElementsByTagName("time");
			var current_time = current_time_node[0].firstChild.nodeValue;
	
			var last_updated_node = xmldoc.getElementsByTagName("last_updated");
			if (last_updated_node[0].firstChild)
				var last_updated = last_updated_node[0].firstChild.nodeValue;
	
			var inactive_time = current_time - last_updated;
			
			if (inactive_time < (TIMEOUT_PERIOD - WARNING_TIME) ) set_chat_active();
			else if (inactive_time < TIMEOUT_PERIOD) set_chat_warning(TIMEOUT_PERIOD - inactive_time);
			else set_chat_inactive();

			if (_chat_type == 'General') setup_people_panel('player');
			else if (_chat_type == 'Alliance') setup_people_panel('alliance');
			else if (_chat_type == 'Senior') setup_people_panel('senior');
			else if (_chat_type == 'Group') setup_people_panel('player');
			else if (_chat_type == 'Personal') setup_people_panel('player');
			else setup_people_panel('player');

		}

		// This is here to ensure that the checkPlayers function continues through an error
		if (_cTimer != 0) clearTimeout(_cTimer);
		if (inactive_time < TIMEOUT_PERIOD) setTimeout('checkPlayers();', 10000); //Refresh check every 10 seconds			
	}
}

function set_chat_active() {
	var status_panel = document.getElementById('chat_status');
	status_panel.style.background = '#80F080';
	status_panel.innerHTML = 'Active';

	var active_area_panel = document.getElementById('active_area');
	active_area_panel.style.display="block";
}

function set_chat_warning(inactive_time) {
	_time_until_inactive = inactive_time;
	
	var status_panel = document.getElementById('chat_status');
	status_panel.style.background = '#F0F080';

  if (_timerId == 0) update_timeout_timer();
}


function set_chat_inactive() {
	var status_panel = document.getElementById('chat_status');
	status_panel.style.background = '#F08080';
	status_panel.innerHTML = 'Inactive (click here to re-activate)';

	var active_area_panel = document.getElementById('active_area');
	active_area_panel.style.display="none";
	_chat_active = false;
}

function set_player_color(divNode, timeDiff) { 
	if (timeDiff < 60) divNode.style.background = '#80FF80';
	else if (timeDiff < 120) divNode.style.background = '#80F080';
	else if (timeDiff < 180) divNode.style.background = '#80E880';
	else if (timeDiff < 240) divNode.style.background = '#80E080';
	else if (timeDiff < 300) divNode.style.background = '#80D880';
	else if (timeDiff < 600) divNode.style.background = '#80D080';
	else if (timeDiff < 900) divNode.style.background = '#80C880';
	else if (timeDiff < 1200) divNode.style.background = '#80C080';
	else if (timeDiff < 1800) divNode.style.background = '#80B880';
	else if (timeDiff < 2400) divNode.style.background = '#80B080';
	else if (timeDiff < 3000) divNode.style.background = '#80A880';
	else if (timeDiff < 4500) divNode.style.background = '#80A080';
	else if (timeDiff < 6000) divNode.style.background = '#809880';
	else if (timeDiff < 7500) divNode.style.background = '#809080';
	else if (timeDiff < 9000) divNode.style.background = '#808880';
	else divNode.style.background = '#808080';
	
}


function reset_online_time(player_name, update_checks) {
	_timerId = 0;
	_time_until_inactive=TIMEOUT_PERIOD;
	var status_panel = document.getElementById('chat_status');
	status_panel.innerHTML = "Connecting to Server...";
	set_chat_active();	
	
	if (resetReq.readyState == 4 || resetReq.readyState == 0) {
		resetReq.open("GET", 'chat_server.php5?action=reset&player_name=' + player_name, true);
		resetReq.onreadystatechange = handleReset; 
		resetReq.send(null);
	}

	_chat_active = true;
	getChatText();
}

function handleReset() {
	if (resetReq.readyState == 4) {
		var status_panel = document.getElementById('chat_status');
		status_panel.style.background = '#80F080';
		status_panel.innerHTML = "Active";
		var xmldoc = resetReq.responseXML;
	}

}

////////////////////////////////
// Moo Effects
var generalSlide;

function show_choices(choice_type) {

  var selectors_node = document.getElementById("choice_selectors");
  var selectorList = selectors_node.childNodes;
  for (var i=0; i < selectorList.length; i++ ) {
    var currentSelector = selectorList[i];
    if (currentSelector.nodeType != 1) continue;
		if (choice_type == currentSelector.id && choice_type != _choice_up) {
			currentSelector.style.display = "block";
			currentSelector.style.zIndex = 1;
		} else {
			currentSelector.style.display = "none";
			currentSelector.style.zIndex = -1;
		}
	}
	if (_choice_up != choice_type) _choice_up = choice_type;
	else _choice_up = 0; // Because we brought it down
}

function select_nav(nav_name, pane_name) {
	var nav = document.getElementById(nav_name + '_nav');
	if (nav) {
		nav.innerHTML= nav_name + ' (' + nav_name + '_' + pane_name + ')';
		nav.style.background="#FEB";
		nav.style.color="#696";
	}
	reset_choice(nav_name + "_" + pane_name + "_choice");
}

function deselect_nav(nav_name) {
	var nav = document.getElementById(nav_name + '_nav');
	if (nav) {
		nav.innerHTML= nav_name;
		nav.style.background="#696";
		nav.style.color="#FEB";
	}
}

function select_pane(pane_name) {
	_pane_name = pane_name;
	var paneId = _chat_type + '_' + pane_name + "_pane";
//	alert(pane_name);
  var container = document.getElementById("chat_panel");
  var paneList = container.childNodes;
  for (var i=0; i < paneList.length; i++ ) {
    var currentPane = paneList[i];
    if (currentPane.nodeType != 1) continue;
		if (paneId == currentPane.id) currentPane.style.display = "block";
		else currentPane.style.display = "none";
	}	

	var choices = document.getElementById(_chat_type + '_choices');
	choices.style.display = 'none';
	choices.style.zIndex = -1;
	_choice_up = 0; // reset the topnav button when you bring down the choices
	
	var chat_people_panel = document.getElementById('chat_people');
	chat_people_panel.innerHTML = "Updating&nbsp;...<hr/>";
	
}

function select_general_pane(pane_name) {
	_chat_type = 'General';

	select_nav('General', pane_name);
	deselect_nav('Alliance');
	deselect_nav('Senior');
	deselect_nav('Group');
	deselect_nav('Personal');
	
	select_pane(pane_name);
}

function select_alliance_pane(pane_name) {
	_chat_type = 'Alliance';

	select_nav('Alliance', pane_name);
	deselect_nav('General');
	deselect_nav('Senior');
	deselect_nav('Group');
	deselect_nav('Personal');

	select_pane(pane_name);
}

function select_senior_pane(pane_name) {
	_chat_type = 'Senior';

	select_nav('Senior', pane_name);
	deselect_nav('General');
	deselect_nav('Alliance');
	deselect_nav('Group');
	deselect_nav('Personal');

	select_pane(pane_name);
}

function select_group_pane(pane_name) {
	_chat_type = 'Group';

	select_nav('Group', pane_name);
	deselect_nav('General');
	deselect_nav('Alliance');
	deselect_nav('Senior');
	deselect_nav('Personal');

	select_pane(pane_name);
}

function select_personal_pane(pane_name) {
	_chat_type = 'Personal';

	select_nav('Personal', pane_name);
	deselect_nav('General');
	deselect_nav('Alliance');
	deselect_nav('Senior');
	deselect_nav('Group');

	select_pane(pane_name);
}


function update_timeout_timer() {
	if (_time_until_inactive == TIMEOUT_PERIOD) return; // Just reset the time so stop the warning timer
	_time_until_inactive -= 1;
	var status_panel = document.getElementById('chat_status');
	status_panel.innerHTML = 'Getting ready to go inactive in ' + _time_until_inactive  + ' seconds';	
	

  if (_time_until_inactive > 1) _timerId = setTimeout("update_timeout_timer()", 1000);
  else status_panel.innerHTML = 'Disconnecting from the server ...';	

}


////  Chat functions

function sendChatText() {
	var message_field = 'text_message';
	if(document.getElementById(message_field).value == '') {
		alert("You have not entered a message");
		return false;
	}
	var param = escape(document.getElementById(message_field).value).replace(/\+/g,'%2B');
	sendText(param);

	document.getElementById(message_field).value = '';
	return false;
}

function sendText(param) {
	if (sendMessageReq.readyState == 4 || sendMessageReq.readyState == 0) {

		sendMessageReq.open("GET", 'chat_server.php5?action=send' + 
			'&player_name=' + _player_name + 
			'&type=' + _chat_type + 
			'&pane=' + _pane_name +
			'&message=' + param, 
			true);
		sendMessageReq.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		sendMessageReq.onreadystatechange = handleSendChat; 
		sendMessageReq.send(null);

		reset_online_time(_player_name, false);
		
	}							

	return false;
}

function handleSendChat() {
	// Who cares the response, just go and get the message (and any others)
}

function getChatText() {
	if (_chat_active == false) return; // If the chat is inactive, then do not make a request
		
	if (getMessagesReq.readyState == 4 || getMessagesReq.readyState == 0) {
		getMessagesReq.open("GET", 'chat_server.php5?action=get&player_name=' + _player_name + '&last=' + _last, true);
		getMessagesReq.onreadystatechange = handleGetMessages; 
		getMessagesReq.send(null);
	}
}

function handleGetMessages() {
	if (getMessagesReq.readyState == 4) {

		var xmldoc = getMessagesReq.responseXML;
		if (!xmldoc) return;
		var message_nodes = xmldoc.getElementsByTagName("message"); 
		var n_messages = message_nodes.length
		for (i = 0; i < n_messages; i++) {
			var player_node = message_nodes[i].getElementsByTagName("player");
			var type_node = message_nodes[i].getElementsByTagName("type");
			var category_node = message_nodes[i].getElementsByTagName("category");
			var text_node = message_nodes[i].getElementsByTagName("text");
			var time_node = message_nodes[i].getElementsByTagName("time");

			// Find the appropriate chat_area to put the message
			var type = type_node[0].firstChild.nodeValue;
			var category = category_node[0].firstChild.nodeValue;
			
			if (type=='Personal' && category == _player_name) category = player_node[0].firstChild.nodeValue;
			
			var messageId = (message_nodes[i].getAttribute('id'));
			var panel_name = type + '_' + category + '_chat';
			var chat_panel = document.getElementById(panel_name);
			if (chat_panel) {
				chat_panel.innerHTML += time_node[0].firstChild.nodeValue + '&nbsp;';
				chat_panel.innerHTML += player_node[0].firstChild.nodeValue + ':&nbsp;';
				if (text_node[0].firstChild) chat_panel.innerHTML += '<B>' + text_node[0].firstChild.nodeValue + '</B><br />';
				else chat_panel.innerHTML += '<B><I>  Error in Message #' + messageId + '</I></B><br />';
				chat_panel.scrollTop = chat_panel.scrollHeight;
			}
			_last = messageId;
			
			// OK now alert the player if it is a new message
			if (category != _pane_name) {
				highlight_choice(type + '_nav');
				highlight_choice(type + '_' + category + '_choice');
			}
			
			
		}
		
		if (_mTimer != 0) clearTimeout(_cTimer);
		_mTimer = setTimeout('getChatText();', 1000); //Refresh chat in 1 second
	}
}

function highlight_choice(choice) {
	var choice_node = document.getElementById(choice);
	if (choice_node) {
		choice_node.style.background = '#A44';
		choice_node.style.color = '#FEE';
	}
}

function reset_choice(choice) {
	var choice_node = document.getElementById(choice);
	if (choice_node) {
		choice_node.style.background = '#FEB';
		choice_node.style.color = '#000';
	}
}

function reset_all_choices() {
	var choicesList = getElementsByClass('choice');
	for ( i=0;i<choicesList.length;i++ ) {
		var choice_node = choicesList[i];
		choice_node.style.background = '#FEB';
		choice_node.style.color = '#000';
	}
	
	reset_choice('General_nav');
	reset_choice('Alliance_nav');
	reset_choice('Senior_nav');
	reset_choice('Personal_nav');
}


/// Support functions

function getElementsByClass(searchClass,node,tag) {
	var classElements = new Array();
	if ( node == null )
		node = document;
	if ( tag == null )
		tag = '*';
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp("(^|\\\\s)"+searchClass+"(\\\\s|$)");
//	var pattern = new RegExp("(^¦\s)"+searchClass+"(\s¦$)");
	var i;
	var j;
	for (i = 0, j = 0; i < elsLen; i++) {
		if ( pattern.test(els[i].className) ) {
			classElements[j] = els[i];
			j++;
		}
	}
	return classElements;
}

function show_goto_button(prefix) {
	var button_node = document.getElementById(prefix + '_button');
	var select_node = document.getElementById(prefix + '_select');
	var selection_name = select_node.options[select_node.selectedIndex].value;
	if (select_node.selectedIndex == 0) {
		button_node.style.zIndex = -1;
		button_node.style.display = 'none';
	} else {
		button_node.style.zIndex = 0;
		button_node.style.display = 'inline';	
	}
}

function show_create_button() {
	var button_node = document.getElementById('group_create_button');
	var text_node = document.getElementById('group_create_text').value;
	button_node.style.zIndex = 0;
	button_node.style.display = 'inline';	
	
}

function select_group_by_dropdown() {
	var select_node = document.getElementById('group_goto_select');
	var selection_name = select_node.options[select_node.selectedIndex].value;
	select_group_pane('Group_' + selection_name);
}

function select_personal_by_dropdown() {
	var select_node = document.getElementById('personal_goto_select');
	var selection_name = select_node.options[select_node.selectedIndex].value;
	
	// Does pane exist?
	var player_pane = document.getElementById('Personal_' + selection_name + '_pane');
	if (player_pane) select_personal_pane('Personal_' + selection_name);
	else create_new_personal_pane(selection_name);
}

// Oh boy, user clicked on a new player that doesn;t have a pane, we need to create it, then attach it and select it

function create_new_personal_pane(player_name) {

	var chat_panel_node = document.getElementById('chat_panel');
	var player_pane = document.createElement('TABLE');
	player_pane.setAttribute('class','panes');
	player_pane.setAttribute('id','Personal_' + player_name + '_pane');
	player_pane.innerHTML += "<TR><TD style='color:white;text-align:center;'><B>Personal (" + player_name + ")</B></TD></TR>\n";
	player_pane.innerHTML += "<TR><TD class='chat_area'>" +
	  "<div id='Personal_" + player_name + "_chat' colspan='4' style='height:500px;width:670px;overflow:auto;text-align:left;'></div>" +
	  "</TD></TR>\n";	
	chat_panel_node.appendChild(player_pane);	

	var personal_choice_menu = document.getElementById('Personal_choices');
	var player_choice = document.createElement('DIV');
	player_choice.setAttribute('class','choice');
	player_choice.setAttribute('id','Personal_' + player_name + '_choice');
	player_choice.setAttribute('onClick','select_personal_pane(\"' + player_name + '");');
	player_choice.innerHTML += player_name;

	var first_choice = personal_choice_menu.firstChild;

	personal_choice_menu.insertBefore(player_choice, first_choice);	

	select_personal_pane(player_name);
}

function sendTopBar() {
	var answer=true;
	if (_chat_type=='General') answer = confirm("Are you really sure you want to send this information in an open channel?");
	if (answer) {
		var param = escape(document.getElementById('top_bar_data').value).replace(/\+/g,'%2B');
		sendText(param);
	}
	return false;
}

function sendCurrentItemsBox() {
	var answer=true;
	if (_chat_type=='General') answer = confirm("Are you really sure you want to send this information in an open channel?");
	if (answer) {
		var param = escape(document.getElementById('current_items_box_data').value).replace(/\+/g,'%2B');
		sendText(param);
	}
	return false;
}

function sendFleetBox() {
	var answer=true;
	if (_chat_type=='General') answer = confirm("Are you really sure you want to send this information in an open channel?");
	if (answer) {
		var param = escape(document.getElementById('fleet_box_data').value).replace(/\+/g,'%2B');
		sendText(param);
	}
	return false;
}

function sendBuildBox() {
	var answer=true;
	if (_chat_type=='General') answer = confirm("Are you really sure you want to send this information in an open channel?");
	if (answer) {
		var param = escape(document.getElementById('build_box_data').value).replace(/\+/g,'%2B');
		sendText(param);
	}
	return false;
}