<?php
  require_once("../db_fns.php5");

class ChatModel {
	function reset_chat_player_online($player_name) {
		if ($player_name == '') return;
	  $conn = db_connect();
		$query = "select * from chat_last_online where player_name='$player_name'";
		$result = $conn->query($query);
		if ($result->num_rows > 0) $this->update_chat_player_last_online($player_name);
		else $this->insert_chat_player_last_online($player_name);
		
		return "<query>$query</query>";
	}

	function update_chat_player_last_online($player_name) {
	  $conn = db_connect();
		$query = "update chat_last_online set last_online=NOW() where player_name='$player_name'";
		$result = $conn->query($query);
	}

	function insert_chat_player_last_online($player_name) {
	  $conn = db_connect();
		$query = "insert into chat_last_online values ('$player_name', NOW() )";
		$result = $conn->query($query);
	}
	
	function get_recent_players() {
		$recent_players = array();
	  $conn = db_connect();
		$query = "select player_name, UNIX_TIMESTAMP(last_online) as last_online from chat_last_online where last_online > NOW() -  0001500";
		$result = $conn->query($query);
	 	for ($count = 0; $row = $result->fetch_object(); $count++) {
	 		$recent_players[$row->player_name] = $row->last_online;
	 	}
	 	return $recent_players;
	}

	function get_recent_alliance_players($player_name) {
		$recent_players = array();
		
		// First get the alliance of the player
		$conn = db_connect();
		$query = "select alliance from player_alliance where player_name='$player_name'";
		$result = $conn->query($query);
		if ($result->num_rows == 0) return false;
		$row = $result->fetch_object();
		$alliance = $row->alliance;
		
	  // Then get the other players in that alliance
	  $conn = db_connect();
		$query = "select clo.player_name, UNIX_TIMESTAMP(clo.last_online) as last_online 
			from chat_last_online clo, player_alliance pa 
			where pa.alliance='$alliance'
			  and clo.player_name = pa.player_name
			";
		$result = $conn->query($query);
	 	for ($count = 0; $row = $result->fetch_object(); $count++) {
	 		$recent_players[$row->player_name] = $row->last_online;
	 	}
	 	return $recent_players;
	}

	function get_recent_senior_players($player_name) {
		$recent_players = array();
		
		// First get the alliance of the player
		$conn = db_connect();
		$query = "select alliance from player_alliance where player_name='$player_name' and rank in ('Leader', 'Senior')";
		$result = $conn->query($query);
		if ($result->num_rows == 0) return false;
		$row = $result->fetch_object();
		$alliance = $row->alliance;
		
	  // Then get the other players in that alliance
	  $conn = db_connect();
		$query = "select clo.player_name, UNIX_TIMESTAMP(clo.last_online) as last_online 
			from chat_last_online clo, player_alliance pa 
			where pa.alliance='$alliance'
			  and pa.rank in ('Leader', 'Senior')
			  and clo.player_name = pa.player_name
			";
		$result = $conn->query($query);
	 	for ($count = 0; $row = $result->fetch_object(); $count++) {
	 		$recent_players[$row->player_name] = $row->last_online;
	 	}
	 	return $recent_players;
	}
	
	function get_last_updated_for_player($player_name) {
	  $conn = db_connect();
		$query = "select player_name, UNIX_TIMESTAMP(last_online) as last_online from chat_last_online where player_name='$player_name'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->last_online;
	}
	
	function get_general_channels($alliance) {
		// For now hardcode them
		$channels = array("Revolution", "Chat", "Bugs/Suggestions", "Other Games", "Off Topic");
		return $channels;
	}


	function get_alliance_channels($alliance) {
		// For now hardcode them
		$channels = array("General Talk", "Attack Plans", "Defenses");
		return $channels;
	}

	function get_senior_channels($alliance) {
		// For now hardcode them
		$channels = array("Diplomacy", "Membership", "Discussion");
		return $channels;
	}

	function get_personal_channels($player_name, $number_returned) {
		$contacted_players = array();
	  // Then get the other players in that alliance
	  $conn = db_connect();
		$query = "select distinct(chat_channel) from chat_message where chat_type='Personal' and player_name = '$player_name' ";
		$result = $conn->query($query);
	 	for ($count = 0; $row = $result->fetch_object(); $count++) {
	 		$contacted_players[$count] = $row->chat_channel;
	 	}
	 	
		$query = "select distinct(player_name) from chat_message where chat_type='Personal' and chat_channel = '$player_name' ";
		$result = $conn->query($query);
	 	for ($count = 0; $row = $result->fetch_object(); $count++) {
	 		array_push($contacted_players, $row->player_name);
	 	}



		return array_unique($contacted_players);
	}

	function get_group_channels($alliance, $number_returned) {
		// For now hardcode them
		$channels = array("Dwarves", "Elves", "Giants", "Kobolds", "Goblins", "Orcs", "Halflings", "Hobbits", "Humans", "Treants", "Trolls", 
		"Dwarves2", "Elves2", "Giants2", "Kobolds2", "Goblins2", "Orcs2", "Halflings2", "Hobbits2", "Humans2", "Treants2", "Trolls2",
		"Dwarves3", "Elves3", "Giants3", "Kobolds3", "Goblins3", "Orcs3", "Halflings3", "Hobbits3", "Humans3", "Treants3", "Trolls3");
		
		if ($number_returned == 0) return $channels;
		else if (count($channels) < $number_returned) return $channels;
		else {
			$return = array();
			for ($i=0;$i < $number_returned; $i++) {
				$return[$i] = $channels[$i]; 
			}
			return $return;
		}
	}
	
	
}

?>