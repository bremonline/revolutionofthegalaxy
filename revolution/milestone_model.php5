<?php

class MilestoneModel {
	function insert_new_milestone($player_name, $tick, $type, $milestone_name, $amount) {
		$conn = db_connect();	
		$query = "INSERT INTO milestone values ('$player_name', $tick, '$type', '$milestone_name', $amount)" ;
	  $result = $conn->query($query);
	}
	
	function check_milestone($player_name, $type, $milestone_name, $amount) {
		$conn = db_connect();	
		$query = "SELECT tick FROM milestone 
			WHERE player_name='$player_name' AND type='$type' AND milestone_name='$milestone_name' AND amount=$amount" ;
	  $result = $conn->query($query);
		if ($result->num_rows > 0 ) return true;
		else return false;	
	}
}
?>