<?php
	require_once('db_fns.php5'); 

class ChatModel {
	function get_chat_channels($type, $group) {
		$channels = array();
	  $conn = db_connect();
		$query = "select * from chat where chat_type='$type' and chat_group='$group'";
		$result = $conn->query($query);
		for ($count=0;$row = $result->fetch_object();$count++) {
			$channels[$count] = $row->chat_channel;
		}
		return $channels;
	}

	function create_chat($type, $group, $channel) {
	  $conn = db_connect();
		$query = "insert into chat values ('$type', '$group', '$channel', NOW() )";
		$result = $conn->query($query);
		
	}
	
	function get_max_message_number() {
	  $conn = db_connect();
		$query = "select max(id) as max from chat_message";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->max;
	}

	
	function set_last_seen($player_name, $type, $group, $channel) {
		$max = $this->get_max_message_number();
		
		//If there is a row, update it
		if ($this->get_last_seen_by_channel($player_name, $type, $group, $channel)) {
			$this->update_last_seen_by_channel($player_name, $type, $group, $channel, $max);
		} else {
			$this->insert_last_seen_by_channel($player_name, $type, $group, $channel, $max);
		}
	}

	function get_last_seen_by_channel($player_name, $type, $group, $channel) {
	  $conn = db_connect();
		$query = "SELECT last_read_id FROM last_seen 
			WHERE player_name = '$player_name'
			  AND communication_type = 'chat'
			  AND message_category = '$type'
			  AND message_group = '$group'
			  AND message_channel = '$channel'";
		$result = $conn->query($query);
		if ($result->num_rows == 0) return 0;
		$row = $result->fetch_object();
//		echo "<BR />Last Seen: $query [$row->last_read_id]";
		return $row->last_read_id;
	}
	
	function update_last_seen_by_channel($player_name, $type, $group, $channel, $max) {
	  $conn = db_connect();
		$query = "UPDATE last_seen SET last_read_id=$max 
			WHERE player_name = '$player_name'
			  AND communication_type = 'chat'
			  AND message_category = '$type'
			  AND message_group = '$group'
			  AND message_channel = '$channel'";		
		$result = $conn->query($query);
	}
	
	function update_last_seen_all_channels($player_name) {
		$max = $this->get_max_message_number();
		
	  $conn = db_connect();
		$query = "UPDATE last_seen SET last_read_id=$max 
			WHERE player_name = '$player_name'
			  AND communication_type = 'chat'";		
		$result = $conn->query($query);
	}
	
	function insert_last_seen_by_channel($player_name, $type, $group, $channel, $max) {
	  $conn = db_connect();
		$query = "insert into last_seen values ('$player_name', 'chat', '$type', '$group', '$channel', $max )";		
		$result = $conn->query($query);		
	}
	
	function count_unread_messages_by_channel($player_name, $type, $group, $channel) {
		$last_read_id = $this->get_last_seen_by_channel($player_name, $type, $group, $channel);

	  $conn = db_connect();
		$query = "SELECT count(id) as count FROM chat_message 
			WHERE chat_type = '$type'
			  AND chat_group = '$group'
			  AND chat_channel = '$channel'
			  AND id > $last_read_id
		 	  AND post_time > NOW() - 1000000"; // 1 00 00 00 is 1 day
		 	 
		$result = $conn->query($query);
		if ($result->num_rows == 0) return 0;
		$row = $result->fetch_object();
//		echo "\n<BR />Count Unread $type, $group, $channel :  $query [$row->count]";
		return $row->count;
	}

	function count_recent_messages_by_channel($player_name, $type, $group, $channel) {
		$last_read_id = $this->get_last_seen_by_channel($player_name, $type, $group, $channel);
		$last_read_id = 0;

	  $conn = db_connect();
		$query = "SELECT count(id) as count FROM chat_message 
			WHERE chat_type = '$type'
			  AND chat_group = '$group'
			  AND chat_channel = '$channel'
			  AND id > $last_read_id
		 	  AND post_time > NOW() - 0040000"; // 1 00 00 00 is 1 day
		 	 
		$result = $conn->query($query);
		if ($result->num_rows == 0) return 0;
		$row = $result->fetch_object();
//		echo "\n<BR />Count Unread $type, $group, $channel :  $query [$row->count]";
		return $row->count;
	}

	function count_recent_messages_by_group($player_name, $type, $group) {
	  $conn = db_connect();
		$query = "SELECT chat_channel from chat WHERE chat_type='$type' AND chat_group='$group'"; 
		$result = $conn->query($query);
		// Main is a special channel
		$count_messages = $this->count_recent_messages_by_channel($player_name, $type, $group, 'main');
		
		for ($count=0;$row = $result->fetch_object();$count++) {
			$count_messages += $this->count_recent_messages_by_channel($player_name, $type, $group, $row->chat_channel);
		}
		return $count_messages;
	}
	
	function count_unread_messages_by_group($player_name, $type, $group) {
		$count_messages = 0;
	  $conn = db_connect();
		$query = "SELECT chat_channel from chat WHERE chat_type='$type' AND chat_group='$group'"; 
		$result = $conn->query($query);
		for ($count=0;$row = $result->fetch_object();$count++) {
			$count_messages += $this->count_unread_messages_by_channel($player_name, $type, $group, $row->chat_channel);
		}
		return $count_messages;
	}

	function count_unread_messages_by_type($player_name, $type) {
		$count_messages = 0;
	  $conn = db_connect();
		$query = "SELECT chat_group, chat_channel from chat WHERE chat_type='$type'"; 
		$result = $conn->query($query);
		for ($count=0;$row = $result->fetch_object();$count++) {
			$count_of_channel = $this->count_unread_messages_by_channel($player_name, $type, $row->chat_group, $row->chat_channel);
			$count_messages += $count_of_channel;
		}
		// also every type a main that is not listed as a seperate channel
		$count_of_channel = $this->count_unread_messages_by_channel($player_name, $type, "main", "main");
		$count_messages += $count_of_channel;

		return $count_messages;
	}
	
	
	function get_last_seen_by_player($player_name) {
		$categories = array();
		
		// First get the last seen
	  $conn = db_connect();
		$query = "SELECT cm.type, count(cm.id) as count
				FROM chat_message cm, last_seen ls
				WHERE cm.chat_type = '$chat_type'
				AND ls.player_name = '$player_name'
				AND ls.communication_type = 'chat'
				AND cm.id > ls.{$chat_type} 
				GROUP BY chat_channel ";
		$result = $conn->query($query);
		
	}
	
	function get_all_messages_as_text($type, $group, $channel) {
	  $conn = db_connect();
		$query = "SELECT date_format(post_time, '%d-%b %h:%i') as post_time, player_name, text from chat_message where chat_type='$type' and chat_group='$group' and chat_channel='$channel'; ";
		$result = $conn->query($query);
		$text = "";
		for ($count = 0; $row = $result->fetch_object(); $count++) {
			$text .= "$row->post_time $row->player_name: $row->text\n";
		}
		return $text;
	}
	
	function add_new_shout($author, $recipient, $type, $tick, $raw_message) {
		$message = htmlentities($raw_message);
	  $conn = db_connect();
		$query = "insert into shout values (0, '$author', '$recipient', '$type', $tick, NOW(), '$message', 'active' )";
		$result = $conn->query($query);
	}
	
	function get_most_recent_shout_for_player($player_name) {
	  $conn = db_connect();
		$query = "SELECT * from shout where ID=(SELECT MAX(ID) FROM shout WHERE recipient in ('$player_name', 'broadcast') AND status='active')";
		$result = $conn->query($query);
		if ($result->num_rows == 0) return false;
		else {
			$row = $result->fetch_object();
			$ret = array("$row->author", "$row->message_text");
			return $ret;
		}
	}

	function clear_shout($player_name) {
	  $conn = db_connect();
		$query = "update shout set status='inactive' where recipient='$player_name'";
		$result = $conn->query($query);
	}

	function get_last_general_message() {
	  $conn = db_connect();
		$query = "SELECT * from chat_message where id=
			(SELECT max(id) FROM `chat_message` where chat_type='General' and chat_group='General' and chat_channel='Revolution')";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return "$row->post_time $row->player_name:<br /><B>$row->text</B>\n";
	}

	function get_last_alliance_message($alliance) {
	  $conn = db_connect();
		$query = "SELECT * from chat_message where id=
			(SELECT max(id) FROM `chat_message` where chat_type='Alliance' and chat_group='$alliance' and chat_channel='General Talk')";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return "$row->post_time $row->player_name:<br /><B>$row->text</B>\n";
	}

}
?>