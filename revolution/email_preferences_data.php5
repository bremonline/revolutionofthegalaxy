<?php
	require_once('db_fns.php5'); 

class EmailPreferencesData {
	var $player_name;
	var $email_on_launch;
	var $email_on_recall;
	var $email_on_battle;
	var $email_on_bombs;
	var $email_on_scans;
	var $email_on_pulses;
	var $email_on_completed_research;
	var $email_on_completed_development;
	var $email_on_completed_creatures;
	var $email_on_completed_scans;
	var $email_on_completed_bombs;
	var $email_on_completed_pulses;
	var $email_on_traps;
	var $email_on_launch_monitor;
	var $email_on_any_milestone;
	var $email_on_any_victory;
	var $email_on_any_shield;
	var $email_on_any_alliance;
	
	
	function update() {
		if ($this->db_entry_exists()) $this->db_update();
		else $this->db_insert();
	}
	
	function populate($player_name) {
		$this->player_name = $player_name;
		if ($this->db_entry_exists()) $this->db_fill();
		else {
	  	$this->player_name = $player_name; 
			$this->email_on_launch = false;
			$this->email_on_recall = false;
			$this->email_on_battle = false;
			$this->email_on_bombs = false;
			$this->email_on_scans = false;
			$this->email_on_pulses = false;
			$this->email_on_completed_research = false;
			$this->email_on_completed_development = false;
			$this->email_on_completed_creatures = false;
			$this->email_on_completed_scans = false;
			$this->email_on_completed_bombs = false;
			$this->email_on_completed_pulses = false;
			$this->email_on_traps = false;
			$this->email_on_launch_monitor = false;
			$this->email_on_any_milestone = false;
			$this->email_on_any_victory = false;
			$this->email_on_any_shield = false;
			$this->email_on_any_alliance = false;
		}
	}
	
	function get_email_preference($player_name, $type) {
		return $this->db_check_preference($player_name, $type);	
	}
	// ---  Database Functions ---

	function db_insert() {
	  $conn = db_connect();
	  $query = "insert into email_preferences values (
	  	'$this->player_name', 
			$this->email_on_launch,
			$this->email_on_recall,
			$this->email_on_battle,
			$this->email_on_bombs,
			$this->email_on_scans,
			$this->email_on_pulses,
			$this->email_on_completed_research,
			$this->email_on_completed_development,
			$this->email_on_completed_creatures,
			$this->email_on_completed_scans,
			$this->email_on_completed_bombs,
			$this->email_on_completed_pulses,
			$this->email_on_traps,
			$this->email_on_launch_monitor,
			$this->email_on_any_milestone,
			$this->email_on_any_victory,
			$this->email_on_any_shield,
			$this->email_on_any_alliance
			)";
 		$result = $conn->query($query);
	}
	
	function db_entry_exists() {
	  $conn = db_connect();
	  $query = "select * from email_preferences where player_name = '$this->player_name' ";
 		$result = $conn->query($query);
		if ($result->num_rows == 1) return true;
		else return false;
		
	}
	
	function db_update() {
	  $conn = db_connect();
	  $query = "update email_preferences set
				email_on_launch = $this->email_on_launch,
				email_on_recall = $this->email_on_recall,
				email_on_battle = $this->email_on_battle,
				email_on_bombs = $this->email_on_bombs,
				email_on_scans = $this->email_on_scans,
				email_on_pulses = $this->email_on_pulses,
				email_on_completed_research = $this->email_on_completed_research,
				email_on_completed_development = $this->email_on_completed_development,
				email_on_completed_creatures = $this->email_on_completed_creatures,
				email_on_completed_scans = $this->email_on_completed_scans,
				email_on_completed_bombs = $this->email_on_completed_bombs,
				email_on_completed_pulses = $this->email_on_completed_pulses,
				email_on_traps = $this->email_on_traps,
				email_on_launch_monitor = $this->email_on_launch_monitor,
				email_on_any_milestone = $this->email_on_any_milestone,
				email_on_any_victory = $this->email_on_any_victory,
				email_on_any_shield = $this->email_on_any_shield,
				email_on_any_alliance = $this->email_on_any_alliance
			where player_name = '$this->player_name' ";
 		$result = $conn->query($query);
	}

	function db_fill() {
	  $conn = db_connect();
	  $query = "select * from email_preferences where player_name = '$this->player_name' ";
 		$result = $conn->query($query);
		$row = $result->fetch_object();
		$this->player_name = $player_name;
		$this->email_on_launch = $row->email_on_launch;
		$this->email_on_recall = $row->email_on_recall;
		$this->email_on_battle = $row->email_on_battle;
		$this->email_on_bombs = $row->email_on_bombs;
		$this->email_on_scans = $row->email_on_scans;
		$this->email_on_pulses = $row->email_on_pulses;
		$this->email_on_completed_research = $row->email_on_completed_research;
		$this->email_on_completed_development = $row->email_on_completed_development;
		$this->email_on_completed_creatures = $row->email_on_completed_creatures;
		$this->email_on_completed_scans = $row->email_on_completed_scans;
		$this->email_on_completed_bombs = $row->email_on_completed_bombs;
		$this->email_on_completed_pulses = $row->email_on_completed_pulses;
		$this->email_on_traps = $row->email_on_traps;
		$this->email_on_launch_monitor = $row->email_on_launch_monitor;
		$this->email_on_any_milestone = $row->email_on_any_milestone;
		$this->email_on_any_victory = $row->email_on_any_victory;
		$this->email_on_any_shield = $row->email_on_any_shield;
		$this->email_on_any_alliance = $row->email_on_any_alliance;
	}
	
	function db_check_preference($player_name, $type) {
	  $conn = db_connect();
	  $query = "select $type as retval from email_preferences where player_name = '$player_name' ";
 		$result = $conn->query($query);
 		if ($result->num_rows == 0) return 0; // No entry for this player, always return false
		$row = $result->fetch_object();
		return $row->retval;
	}

	// -- Debugging --
	function to_string() {
		return "$this->player_name<BR />"
			. " email_on_launch = $this->email_on_launch<br />\n"
			. " email_on_recall = $this->email_on_recall<br />\n"
			. " email_on_battle = $this->email_on_battle<br />\n"
			. " email_on_bombs = $this->email_on_bombs<br />\n"
			. " email_on_scans = $this->email_on_scans<br />\n"
			. " email_on_pulses = $this->email_on_pulses<br />\n"
			. " email_on_completed_research = $this->email_on_completed_research<br />\n"
			. " email_on_completed_development = $this->email_on_completed_development<br />\n"
			. " email_on_completed_creatures = $this->email_on_completed_creatures<br />\n"
			. " email_on_completed_scans = $this->email_on_completed_scans<br />\n"
			. " email_on_completed_bombs = $this->email_on_completed_bombs<br />\n"
			. " email_on_completed_pulses = $this->email_on_completed_pulses<br />\n"
			. " email_on_traps = $this->email_on_traps<br />\n"
			. " email_on_launch_monitor = $this->email_on_launch_monitor<br />\n"
			. " email_on_any_milestone = $this->email_on_any_milestone<br />\n"
			. " email_on_any_victory = $this->email_on_any_victory<br />\n"
			. " email_on_any_shield = $this->email_on_any_shield<br />\n"
			. " email_on_any_alliance = $this->email_on_any_alliance";
	}
}
?>