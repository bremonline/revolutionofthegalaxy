<?php
	require_once('chat_model.php5'); 
	require_once('game_model.php5'); 
	
class ChatController {
	function create_new_chat() {
		$player_name = $_SESSION["player_name"];
		
		$type = $_REQUEST["type"];
		$group = $_REQUEST["group"];
		$channel = $_REQUEST["channel"];

		$channel = strtr($channel, "'", " "); // removes ' 
		
		$cm = new ChatModel();
		$cm->create_chat($type, $group, $channel);
		
		// When personal chat, make a channel for the recipient too.
		if ($type == "personal") $cm->create_chat($type, $channel, $group);
	}
	
	function create_new_personal_shout() {
		$player_name = $_SESSION["player_name"];
		$gm = new GameModel();
		$ct = $gm->get_current_tick();
		$recipient = $_REQUEST["recipient"];
		$shout_text = $_REQUEST["shout_text"];
		
		$cm = new ChatModel();
		$cm->add_new_shout($player_name, $recipient, "personal", $ct, $shout_text);
		show_info("You just shouted: $shout_text to $recipient");
	}
	
	function clear_shout() {
		$player_name = $_SESSION["player_name"];
		$cm = new ChatModel();
		$cm->clear_shout($player_name);
		show_info("You just cleared all your shouts");
		
	}
}

?>