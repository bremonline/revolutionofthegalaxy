<?php

class ChatMessage {
	var $id;
	var $chat_type;
	var $chat_channel;
	var $player_name;
	var $post_time;
	var $text;
	
	function fill($id, $chat_type, $chat_channel, $player_name, $post_time, $text) {
		$this->id = $id;
		$this->chat_type = $chat_type;
		$this->chat_channel = $chat_channel;
		$this->player_name = $player_name;
		$this->post_time = $post_time;
		$this->text = $text;
	}
}
?>