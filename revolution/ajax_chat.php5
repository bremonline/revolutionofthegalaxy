<?php
require_once("db_fns.php5");
require_once("alliance_model.php5");
require_once("chat_model.php5");
require_once("news_model.php5");
require_once("misc_fns.php5");

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
header("Content-Type: text/xml; charset=utf-8");

$player_name = $_REQUEST["player_name"];
$request_type = $_REQUEST["request_type"];
$message = $_REQUEST["message"];
$chat_type = $_REQUEST["chat_type"];
$chat_group = $_REQUEST["chat_group"];
$chat_channel = $_REQUEST["chat_channel"];
$last = $_REQUEST["last"];

if ($request_type == 'send') {
	send_message($player_name, $chat_type, $chat_group, $chat_channel, $message);
	$xml = return_response($player_name, $chat_type, $chat_group, $chat_channel, $last);
} else if ($request_type == 'get') {
	$xml = return_response($player_name, $chat_type, $chat_group, $chat_channel, $last);
} else if ($request_type == 'check') {
	$xml = return_check_response($player_name, $last);
} else $xml = return_bad_response("UNKNOWN");

echo $xml;


///  Support Functions

function send_message($player_name, $chat_type, $chat_group, $chat_channel, $message) {
	if ($chat_type == 'personal') $chat_group = "$player_name";

	// Alliances are a special type of group.  If the chat_type is alliance set the group to the name of that player's alliance
	$am = new AllianceModel();
	if ($chat_type == 'alliance') $chat_group = $am->get_alliance_of_player($player_name);
	if ($chat_group == '') $chat_group = 'no alliance';
	
	
	$encoded_message = convertString($message);
	$conn = db_connect();
	$query = "INSERT INTO chat_message VALUES (0, '$chat_type', '$chat_group', '$chat_channel', '$player_name', NOW(), '$encoded_message')";
	$result = $conn->query($query);
}

function return_response($player_name, $chat_type, $chat_group, $chat_channel, $last) {
	set_last_online_player($player_name);
	
	$xml  = "<?xml version='1.0' ?>\n";
	$xml .= "<root>\n";

	
	$message_list = get_message_list($player_name, $chat_type, $chat_group, $chat_channel, $last);
	
	foreach ($message_list as $id => $chat_message) {
		if ($chat_message->player_name == '') $chat_message->player_name = 'unknown'; // protect against bad data
			$xml .= "
		<message id='$chat_message->id'>
			<chat_type>$chat_message->chat_type</chat_type>
			<chat_group>$chat_message->chat_group</chat_group>
			<chat_channel>$chat_message->chat_channel</chat_channel>
			<player>$chat_message->player_name</player>
			<text>$chat_message->text</text>
			<time>$chat_message->post_time</time>
		</message>
			";
	}
	
	
	$xml .= "</root>\n";
	return $xml;
}

function set_last_online_player($player_name) {
	if ( does_chat_player_exist($player_name) ) {
		update_chat_player($player_name);
	} else {
		insert_chat_player($player_name);
	}
}

function does_chat_player_exist($player_name) {
	$conn = db_connect();
	$query = "select * from chat_player where player_name='$player_name'";
	$result = $conn->query($query);	
	if ($result->num_rows > 0) return true;
	else return false;
}

function insert_chat_player($player_name) {
	$conn = db_connect();
	$query = "insert into chat_player values ('$player_name', NOW())";
	$result = $conn->query($query);	
}

function update_chat_player($player_name) {
	$conn = db_connect();
	$query = "update chat_player set last_online=NOW() where player_name='$player_name' ";
	$result = $conn->query($query);		
}

function return_bad_response($request) {
	$xml  = "<?xml version='1.0' ?>\n";
	$xml .= "<root>\n";

		$xml .= "
		<message id='0'>
			<chat_type>ERROR</chat_type>
			<chat_group>ERROR</chat_group>
			<chat_channel>ERROR</chat_channel>
			<player>admin</player>
			<text>There was a problem with your request: [$request] </text>
			<time>00:00</time>
		</message>
		";


	$xml .= "</root>\n";
	return $xml;
	
}

function get_message_list($player_name, $chat_type, $chat_group, $chat_channel, $last) {
	$message_list = array();
	// If the chat_type is individual, the get the messages a different way
	if ($chat_type == 'personal') return get_messages_for_individual($player_name, $chat_type, $player_name, $chat_channel, $last);

	// Alliances are a special type of group.  If the chat_type is alliance set the group to the name of that player's alliance
	$am = new AllianceModel();
	if ($chat_type == 'alliance') $chat_group = $am->get_alliance_of_player($player_name);
	if ($chat_group == '') $chat_group = 'no alliance';
	
	$conn = db_connect();
	$query = "SELECT id, chat_type, chat_group, chat_channel, player_name, date_format(post_time, '%d-%b %h:%i') as post_time, text 
		FROM chat_message  
		WHERE chat_type = '$chat_type' 
			AND chat_group = '$chat_group'
			AND chat_channel = '$chat_channel' 
		  AND id > $last
		  ORDER BY id DESC
		  LIMIT 0, 50";
	
	$result = $conn->query($query);
	for ($count=0; $row = $result->fetch_object(); $count++) {
		$chat_message = new ChatMessage();
		$chat_message->fill($row->id, $row->chat_type, $row->chat_group, $row->chat_channel, $row->player_name, $row->post_time, $row->text);
		$message_list[$row->id] = $chat_message;
	}
	
	return array_reverse($message_list);
} 

function get_messages_for_individual($player_name, $chat_type, $from, $to, $last) {
	$message_list = array();

	// First get messages from the player to the other player
	$conn = db_connect();
	$query = "SELECT id, chat_type, chat_group, chat_channel, player_name, date_format(post_time, '%d-%b %h:%i') as post_time, text 
		FROM chat_message  
		WHERE chat_type = '$chat_type' 
			AND chat_group = '$from' 
			AND chat_channel = '$to' 
		  AND id > $last
		  AND post_time > NOW() - 1000000
		  ORDER BY id"; // 1 00 00 00 is 1 day
	
	$result = $conn->query($query);
	for ($count=0; $row = $result->fetch_object(); $count++) {
		$chat_message = new ChatMessage();
		$chat_message->fill($row->id, $row->chat_type, $row->chat_group, $row->chat_channel, $row->player_name, $row->post_time, $row->text);
		$message_list[$row->id] = $chat_message;
	}
	// Then get messages TO this player
	$conn = db_connect();
	$query = "SELECT id, chat_type, chat_group, chat_channel, player_name, date_format(post_time, '%d-%b %h:%i') as post_time, text 
		FROM chat_message  
		WHERE chat_type = '$chat_type' 
			AND chat_group = '$to' 
			AND chat_channel = '$from' 
		  AND id > $last
		  AND post_time > NOW() - 1000000
		  ORDER BY id"; // 1 00 00 00 is 1 day

	
	$result = $conn->query($query);
	for ($count=0; $row = $result->fetch_object(); $count++) {
		$chat_message = new ChatMessage();
		$chat_message->fill($row->id, $row->chat_type, $row->chat_group, $row->chat_channel, $row->player_name, $row->post_time, $row->text);
		$message_list[$row->id] = $chat_message;
	}

	return $message_list;	
}

function return_check_response($player_name, $last) {
	$update = $_REQUEST["update"];

	$xml  = "<?xml version='1.0' ?>\n";
	$xml .= "<root>\n";

	$cm = new ChatModel();
	$nm = new NewsModel();
	$am = new AllianceModel();
	$alliance_name = $am->get_alliance_of_player($player_name);

	$number_main = get_number_main_messages($player_name);
	$number_alliance = get_number_alliance_messages($player_name, $alliance_name);
	$number_personal = get_number_personal_messages($player_name);
	$number_news_all = $nm->get_unread_player_news_by_type($player_name, "all");
	$number_news_launch = $nm->get_unread_player_news_by_type($player_name, "launch");
	$number_news_battle = $nm->get_unread_player_news_by_type($player_name, "battle");
	$number_news_items = $nm->get_unread_player_news_by_type($player_name, "items");
	$number_news_scans = $nm->get_unread_player_news_by_type($player_name, "scans");
	$number_news_alliance = $nm->get_unread_player_news_by_type($player_name, "alliance");
	$number_news_misc = $nm->get_unread_player_news_by_type($player_name, "misc");
	$shout_details = $cm->get_most_recent_shout_for_player($player_name);
	if ($shout_details) {
		list($shout_sender, $shout_text) = $shout_details;
	}

	if ($update == 'false') {
		$xml .= "
			<status id='0'>
				<status_main>$number_main</status_main>
				<status_alliance>$number_alliance</status_alliance>
				<status_personal>$number_personal</status_personal>
				<status_news_all>$number_news_all</status_news_all>
				<status_news_launch>$number_news_launch</status_news_launch>
				<status_news_battle>$number_news_battle</status_news_battle>
				<status_news_items>$number_news_items</status_news_items>
				<status_news_scans>$number_news_scans</status_news_scans>
				<status_news_alliance>$number_news_alliance</status_news_alliance>
				<status_news_misc>$number_news_misc</status_news_misc>
				<status_shout_sender>$shout_sender</status_shout_sender>
				<status_shout_text>$shout_text</status_shout_text>
			</status>";
	}

	$xml .= "</root>\n";
	return $xml;
}

function get_number_main_messages($player_name) {
	$cm = new ChatModel();
	$main_message_count = $cm->count_recent_messages_by_group($player_name, "main", "main");
	return $main_message_count;
}

function get_number_alliance_messages($player_name, $alliance_name) {
	$cm = new ChatModel();
	$alliance_message_count = $cm->count_recent_messages_by_group($player_name, "alliance", $alliance_name);
	return $alliance_message_count;
}


function get_number_personal_messages($player_name) {
	$cm = new ChatModel();
	$personal_message_count = $cm->count_recent_messages_by_group($player_name, "personal", $player_name);
	return $personal_message_count ;
}

class ChatMessage {
	var $id;
	var $chat_type;
	var $chat_group;
	var $chat_channel;
	var $player_name;
	var $post_time;
	var $text;
	
	function fill($id, $chat_type, $chat_group, $chat_channel, $player_name, $post_time, $text) {
		$this->id = $id;
		$this->chat_type = $chat_type;
		$this->chat_group = $chat_group;
		$this->chat_channel = $chat_channel;
		$this->player_name = $player_name;
		$this->post_time = $post_time;
		$this->text = $text;
	}
	
	
}
?>