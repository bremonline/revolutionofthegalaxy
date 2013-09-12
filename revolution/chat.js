
	var sendMessageReq = getXmlHttpRequestObject();
	var receiveMessageReq = getXmlHttpRequestObject();
	var sendCheckReq = getXmlHttpRequestObject();
	var receiveCheckReq = getXmlHttpRequestObject();
	var lastMessage = 0;
	var mTimer;
	var cTimer;
	var player = '';
	var chat_type = 'main';
	var chat_group = 'main';
	var chat_channel = 'main';
	
	
function getXmlHttpRequestObject() {
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else if(window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		document.getElementById('status').innerHTML = 'Status: invalid XmlHttpRequest Object.  Use a standard browser.';
	}
}
	

function startChat(p_name, c_type, c_channel) {
	player = p_name;
	chat_type = c_type;
	chat_channel = c_channel;
	getChatText();
	checkChat();
}

function checkChat() {
		if ( document.getElementById('chat_panel') ) updateNotice = 'true';
		else updateNotice = 'false';

		if (receiveCheckReq.readyState == 4 || receiveCheckReq.readyState == 0) {
			receiveCheckReq.open("GET", 'ajax_chat.php5?' + 
				'&player_name=' + player + 
				'&request_type=' + 'check' + 
				'&update=' + updateNotice + 
				'&last=' + lastMessage, 
				true);		
			receiveCheckReq.onreadystatechange = handleReceiveCheckChat; 
			receiveCheckReq.send(null);
	}
}

function handleReceiveCheckChat() {
	if (receiveCheckReq.readyState == 4) {
		var xmldoc = receiveCheckReq.responseXML;
		var main_nodes = xmldoc.getElementsByTagName("status_main"); 
		var alliance_nodes = xmldoc.getElementsByTagName("status_alliance"); 
		var personal_nodes = xmldoc.getElementsByTagName("status_personal"); 
		var news_all_nodes = xmldoc.getElementsByTagName("status_news_all"); 
		var news_launch_nodes = xmldoc.getElementsByTagName("status_news_launch"); 
		var news_battle_nodes = xmldoc.getElementsByTagName("status_news_battle"); 
		var news_items_nodes = xmldoc.getElementsByTagName("status_news_items"); 
		var news_scans_nodes = xmldoc.getElementsByTagName("status_news_scans"); 
		var news_alliance_nodes = xmldoc.getElementsByTagName("status_news_alliance"); 
		var news_misc_nodes = xmldoc.getElementsByTagName("status_news_misc"); 
		var shout_sender = xmldoc.getElementsByTagName("status_shout_sender"); 
		var shout_text = xmldoc.getElementsByTagName("status_shout_text"); 

		var main_text = main_nodes[0].firstChild.nodeValue;
		var alliance_text = alliance_nodes[0].firstChild.nodeValue;
		var personal_text = personal_nodes[0].firstChild.nodeValue;
		
		document.getElementById('chat_main').firstChild.nodeValue = 'Main Chat (' + main_text + ')';
		document.getElementById('chat_alliance').firstChild.nodeValue = 'Alliance Chat (' + alliance_text + ')';
		document.getElementById('chat_personal').firstChild.nodeValue = 'Personal Chat (' + personal_text + ')';

		document.getElementById('player_news_all').firstChild.nodeValue = 'All [' + news_all_nodes[0].firstChild.nodeValue + ']';
		document.getElementById('player_news_launch').firstChild.nodeValue = 'Launch [' + news_launch_nodes[0].firstChild.nodeValue + ']';
		document.getElementById('player_news_battle').firstChild.nodeValue = 'Battle [' + news_battle_nodes[0].firstChild.nodeValue + ']';
		document.getElementById('player_news_items').firstChild.nodeValue = 'Items [' + news_items_nodes[0].firstChild.nodeValue + ']';
		document.getElementById('player_news_scans').firstChild.nodeValue = 'Scans [' + news_scans_nodes[0].firstChild.nodeValue + ']';
		document.getElementById('player_news_alliance').firstChild.nodeValue = 'Alliance [' + news_alliance_nodes[0].firstChild.nodeValue + ']';
		document.getElementById('player_news_misc').firstChild.nodeValue = 'Misc [' + news_misc_nodes[0].firstChild.nodeValue + ']';

//		if (main_text > 0) document.getElementById('chat_main').style.backgroundColor = '#D04040';
//		else document.getElementById('chat_main').style.backgroundColor = '#404040';
//		if (alliance_text > 0) document.getElementById('chat_alliance').style.backgroundColor = '#D04040';
//		else document.getElementById('chat_alliance').style.backgroundColor = '#402040';
//		if (personal_text > 0) document.getElementById('chat_personal').style.backgroundColor = '#D04040';
//		else document.getElementById('chat_personal').style.backgroundColor = '#406040';
			
		if (news_all_nodes[0].firstChild.nodeValue > 0) document.getElementById('player_news_all').style.backgroundColor = '#D04040';
		else document.getElementById('player_news_all').style.backgroundColor = '#A0A040';
		if (news_launch_nodes[0].firstChild.nodeValue > 0) document.getElementById('player_news_launch').style.backgroundColor = '#D04040';
		else document.getElementById('player_news_launch').style.backgroundColor = '#90A040';
		if (news_battle_nodes[0].firstChild.nodeValue > 0) document.getElementById('player_news_battle').style.backgroundColor = '#D04040';
		else document.getElementById('player_news_battle').style.backgroundColor = '#80A040';
		if (news_items_nodes[0].firstChild.nodeValue > 0) document.getElementById('player_news_items').style.backgroundColor = '#D04040';
		else document.getElementById('player_news_items').style.backgroundColor = '#809040';
		if (news_scans_nodes[0].firstChild.nodeValue > 0) document.getElementById('player_news_scans').style.backgroundColor = '#D04040';
		else document.getElementById('player_news_scans').style.backgroundColor = '#808040';
		if (news_alliance_nodes[0].firstChild.nodeValue > 0) document.getElementById('player_news_alliance').style.backgroundColor = '#D04040';
		else document.getElementById('player_news_alliance').style.backgroundColor = '#708040';
		if (news_misc_nodes[0].firstChild.nodeValue > 0) document.getElementById('player_news_misc').style.backgroundColor = '#D04040';
		else document.getElementById('player_news_misc').style.backgroundColor = '#707040';

		if (shout_sender[0].firstChild && shout_sender[0].firstChild.nodeValue != '') {
			document.getElementById('shout_text').firstChild.innerHTML = "&nbsp;" + shout_text[0].firstChild.nodeValue + "&nbsp;";
			document.getElementById('shout_panel').style.display='block';
			document.getElementById('shout_sender').firstChild.innerHTML = "&nbsp;" + shout_sender[0].firstChild.nodeValue + "&nbsp;";
//			document.getElementById('shout_text').firstChild.innerHTML = "&nbsp;" + shout_text[0].firstChild.nodeValue + "&nbsp;";
			document.getElementById('shout_text').style.width='529px;';
		}

		if ( document.getElementById('chat_panel') )
			cTimer = setTimeout('checkChat();', 20000); //Refresh chat numbers in 60 seconds
		else
			cTimer = setTimeout('checkChat();', 20000); //Refresh chat numbers in 60 seconds
			
	}
}

function getChatText() {
	var chat_panel = document.getElementById('chat_panel');
	if (chat_panel) {
		if (receiveMessageReq.readyState == 4 || receiveMessageReq.readyState == 0) {
			receiveMessageReq.open("GET", 'ajax_chat.php5?' + 
				'request_type=' + 'get' + 
				'&chat_type=' + chat_type + 
				'&chat_group=' + chat_group + 
				'&chat_channel=' + chat_channel + 
				'&player_name=' + player + 
				'&last=' + lastMessage, 
				true);		
			receiveMessageReq.onreadystatechange = handleReceiveChat; 
			receiveMessageReq.send(null);
		}
	}
}

function sendChatText() {
	if(document.getElementById('message').value == '') {
		alert("You have not entered a message");
		return;
	}
	if (sendMessageReq.readyState == 4 || sendMessageReq.readyState == 0) {
		sendMessageReq.open("POST", 'ajax_chat.php5?' + 
			'request_type=' + 'send' + 
			'&chat_type=' + chat_type + 
			'&chat_group=' + chat_group + 
			'&chat_channel=' + chat_channel + 
			'&player_name=' + player + 
			'&last=' + lastMessage, 
			true);
		sendMessageReq.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		sendMessageReq.onreadystatechange = handleSendChat; 
		var param = 'message=' + escape(document.getElementById('message').value).replace(/\+/g,'%2B');
		sendMessageReq.send(param);
		document.getElementById('message').value = '';
	}							
}

function handleSendChat() {
	clearInterval(mTimer);
	getChatText();
}

function handleReceiveChat() {
	if (receiveMessageReq.readyState == 4) {
		var chat_panel = document.getElementById('chat_panel');
		var xmldoc = receiveMessageReq.responseXML;
		var message_nodes = xmldoc.getElementsByTagName("message"); 
		var n_messages = message_nodes.length
		for (i = 0; i < n_messages; i++) {
			var chat_type_node = message_nodes[i].getElementsByTagName("chat_type");
			var chat_group_node = message_nodes[i].getElementsByTagName("chat_group");
			var chat_channel_node = message_nodes[i].getElementsByTagName("chat_channel");
			var player_node = message_nodes[i].getElementsByTagName("player");
			var text_node = message_nodes[i].getElementsByTagName("text");
			var time_node = message_nodes[i].getElementsByTagName("time");
			if (message_nodes && chat_panel) {
				chat_panel.innerHTML += time_node[0].firstChild.nodeValue + '&nbsp;';
				chat_panel.innerHTML += player_node[0].firstChild.nodeValue + ':&nbsp;';
				chat_panel.innerHTML += text_node[0].firstChild.nodeValue + '<br />';
				chat_panel.scrollTop = chat_panel.scrollHeight;
				lastMessage = (message_nodes[i].getAttribute('id'));
				document.getElementById("status").firstChild.nodeValue = lastMessage;
			}
		}
		mTimer = setTimeout('getChatText();', 20000); //Refresh chat in 2 seconds
	}
}

function blockSubmit() {
	sendChatText();
	return false;
}

function setChatName(chat_name) {
	document.getElementById("dbg").innerHTML += "Setting chat name to: [" + chat_name + "] ";
	chat = chat_name;
}



