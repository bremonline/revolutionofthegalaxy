<?php
  require_once("chat_model.php5");
  require_once("chat_message.php5");
  require_once("../misc_fns.php5");
  require_once("../db_fns.php5");
  require_once("../alliance_model.php5");

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");

$action = $_REQUEST["action"];

if ($action == 'reset') $xml = create_reset_response();
else if ($action == 'send') $xml = create_send_response();
else if ($action == 'get') $xml = create_get_response();
else if ($action == 'players') $xml = create_players_response();
else $xml = create_error_response();

echo $xml;

//////////////////////////

function create_players_response() {
	$player_name = $_REQUEST["player_name"];
	
	$cm = new ChatModel();
	$recent_players = $cm->get_recent_players();
	$last_updated = $cm->get_last_updated_for_player($player_name);
	$recent_alliance_players = $cm->get_recent_alliance_players($player_name);
	$recent_senior_players = $cm->get_recent_senior_players($player_name);
	
	$xml  = "<?xml version='1.0' ?>\n";
	$xml .= "<root>\n";
	$xml .= "<time>"  . get_db_unix_time() . "</time>\n";
	$xml .= "<last_updated>$last_updated</last_updated>\n";
	
	if ($recent_players) {
	foreach ($recent_players as $player_name => $last_online) {
			$xml .= "
				<player>
					<player_name>$player_name</player_name>
					<last_online>$last_online</last_online>
				</player>\n";
		}
	}
	
	if ($recent_alliance_players) {
		foreach ($recent_alliance_players as $player_name => $last_online) {
			$xml .= "
				<alliance>
					<player_name>$player_name</player_name>
					<last_online>$last_online</last_online>
				</alliance>\n";
		}
	}
	
	if ($recent_senior_players) {
		foreach ($recent_senior_players as $player_name => $last_online) {
			$xml .= "
				<senior>
					<player_name>$player_name</player_name>
					<last_online>$last_online</last_online>
				</senior>\n";
		}
	}
	$xml .= "</root>\n";
	return $xml;
}

function create_reset_response() {
	$player_name = $_REQUEST["player_name"];

  $cm = new ChatModel();
	$msg = $cm->reset_chat_player_online($player_name);
	$xml  = "<?xml version='1.0' ?>\n";
	$xml .= "<root>\n";
	
	$xml .= "</root>\n";
	
	return $xml;
}

function create_send_response() {
	$player_name = $_REQUEST["player_name"];
	$type = $_REQUEST["type"];
	$pane = $_REQUEST["pane"];
	$message = $_REQUEST["message"];

	$xml  = "<?xml version='1.0' ?>\n";
	$xml .= "<root>\n";

	$status = send_message($player_name, $type, $pane, $message);
	
	if ($status) $xml .= "<response>Message Received</response>\n";
	else $xml .= "<response>Message Failed</response>\n";
	
	$xml .= "</root>\n";
	
	return $xml;
}

function send_message($player_name, $chat_type, $chat_channel, $message) {

	if ($chat_type == 'General') {
		$chat_group = 'General';
	} else if ($chat_type == 'Alliance') {
		$am = new AllianceModel();
		$chat_group = $am->get_alliance_of_player($player_name);
	} else if ($chat_type == 'Senior') {
		$am = new AllianceModel();
		$chat_group = $am->get_alliance_of_player($player_name);	
	} else if ($chat_type == 'Personal') {
		$chat_group = $chat_channel;
	}
	
	$encoded_message = convertString($message);
	$conn = db_connect();
	$query = "INSERT INTO chat_message VALUES (0, '$chat_type', '$chat_group', '$chat_channel', '$player_name', NOW(), '$encoded_message')";
	$result = $conn->query($query);
	if ($result) return true;
	else return false;
}

function my_sort($message1, $message2)
{
  if ($message1->id == $message2->id) return 0;
  return ($message1->id < $message2->id) ? -1 : 1;
}


function create_get_response() {
	$player_name = $_REQUEST["player_name"];
	$last = $_REQUEST["last"];
	$general_message_list = get_general_message_list($player_name, $last);
	$alliance_message_list = get_alliance_message_list($player_name, $last);
	$senior_message_list = get_senior_message_list($player_name, $last);
	$personal_message_list = get_personal_message_list($player_name, $last);
	
	$full_message_list = $general_message_list + $alliance_message_list + $senior_message_list + $personal_message_list;
	
	$xml  = "<?xml version='1.0' ?>\n";
	$xml .= "<root>\n";

	uasort($full_message_list, "my_sort");
	
	foreach ($full_message_list as $id => $chat_message) {
		$xml .= "
		<message id='$id'>
			<player>$chat_message->player_name</player>
			<type>$chat_message->chat_type</type>
			<category>$chat_message->chat_channel</category>
			<text>$chat_message->text</text>
			<time>$chat_message->post_time</time>
		</message>
		";
	}

	$xml .= "</root>\n";
	
	return $xml;
}


function get_general_message_list($player_name, $last) {
	$message_list = array();

	$conn = db_connect();
	$query = "SELECT id, chat_type, chat_channel, player_name, date_format(post_time, '%d-%b %H:%i') as post_time, text 
		FROM chat_message  
		WHERE chat_type = 'General' 
		  AND id > $last
		  ORDER BY id DESC
		  LIMIT 0, 50"; 
	
	$result = $conn->query($query);
	for ($count=0; $row = $result->fetch_object(); $count++) {
		$chat_message = new ChatMessage();
		$chat_message->fill($row->id, $row->chat_type, $row->chat_channel, $row->player_name, $row->post_time, $row->text);
		$message_list[$row->id] = $chat_message;
	}
	
	return $message_list;
} 

function get_alliance_message_list($player_name, $last) {
	$am = new AllianceModel();
	$alliance = $am->get_alliance_of_player($player_name);
	$message_list = array();

	$conn = db_connect();
	$query = "SELECT id, chat_type, chat_channel, player_name, date_format(post_time, '%d-%b %H:%i') as post_time, text 
		FROM chat_message  
		WHERE chat_type = 'Alliance' 
			AND chat_group = '$alliance'
		  AND id > $last
		  ORDER BY id DESC
		  LIMIT 0, 50"; 
	
	$result = $conn->query($query);
	for ($count=0; $row = $result->fetch_object(); $count++) {
		$chat_message = new ChatMessage();
		$chat_message->fill($row->id, $row->chat_type, $row->chat_channel, $row->player_name, $row->post_time, $row->text);
		$message_list[$row->id] = $chat_message;
	}
	
	return $message_list;
} 

function get_senior_message_list($player_name, $last) {
	$message_list = array();
	$am = new AllianceModel();
	$alliance = $am->get_alliance_of_player($player_name);
	if ($am->is_senior($player_name, $alliance) == false) return $message_list; // empty array if the member is not a senior

	$conn = db_connect();
	$query = "SELECT id, chat_type, chat_channel, player_name, date_format(post_time, '%d-%b %H:%i') as post_time, text 
		FROM chat_message  
		WHERE chat_type = 'Senior' 
			AND chat_group = '$alliance'
		  AND id > $last
		  ORDER BY id DESC
		  LIMIT 0, 50"; 
	
	$result = $conn->query($query);
	for ($count=0; $row = $result->fetch_object(); $count++) {
		$chat_message = new ChatMessage();
		$chat_message->fill($row->id, $row->chat_type, $row->chat_channel, $row->player_name, $row->post_time, $row->text);
		$message_list[$row->id] = $chat_message;
	}
	
	return $message_list;
} 

function get_personal_message_list($player_name, $last) {
	$message_list = array();

	$conn = db_connect();
	$query = "SELECT id, chat_type, chat_channel, player_name, date_format(post_time, '%d-%b %H:%i') as post_time, text 
		FROM chat_message  
		WHERE chat_type = 'Personal' 
			AND chat_group = '$player_name'
		  AND id > $last
		  ORDER BY id DESC
		  LIMIT 0, 50"; 
//	echo "Q1: $query";
	
	$result = $conn->query($query);
	for ($count=0; $row = $result->fetch_object(); $count++) {
		$chat_message = new ChatMessage();
		$chat_message->fill($row->id, $row->chat_type, $row->chat_channel, $row->player_name, $row->post_time, $row->text);
		$message_list[$row->id] = $chat_message;
//		echo "R1: $row->id, $row->chat_type, $row->chat_channel, $row->player_name, $row->post_time, $row->text";
	}
	
	$conn = db_connect();
	$query = "SELECT id, chat_type, chat_channel, player_name, date_format(post_time, '%d-%b %H:%i') as post_time, text 
		FROM chat_message  
		WHERE chat_type = 'Personal' 
			AND player_name = '$player_name'
		  AND id > $last
		  ORDER BY id DESC
		  LIMIT 0, 200"; 
//	echo "Q2: $query";

	$result = $conn->query($query);
	for ($count=0; $row = $result->fetch_object(); $count++) {
		$chat_message = new ChatMessage();
		$chat_message->fill($row->id, $row->chat_type, $row->chat_channel, $row->player_name, $row->post_time, $row->text);
		$message_list[$row->id] = $chat_message;
//		echo "R2: $row->id, $row->chat_type, $row->chat_channel, $row->player_name, $row->post_time, $row->text \n";
	}

	return $message_list;
}

function create_error_response() {
	$player_name = $_REQUEST["player_name"];

  $cm = new ChatModel();
	$msg = $cm->reset_chat_player_online($player_name);
	$xml  = "<?xml version='1.0' ?>\n";
	$xml .= "<root>\n";
	$xml .= "<error>There was an error</error>\n";
	$xml .= "</root>\n";
	
	return $xml;
}