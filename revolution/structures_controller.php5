<?php
	require_once('player_data.php5'); 
	
class StructuresController {

	function allocate_new_structure() {
		$number = $_REQUEST['number'];
		for ($i=0; $i<$number;$i++) {
			$this->allocate_single_structure();
		}
	}
	
	function allocate_single_structure() {
		$player_name = $_SESSION['player_name'];
		$structure_type = $_REQUEST['structure_type'];
		$pd = new PlayerData();				
		$pd->db_fill($player_name);

		$cost = 150 * (1 + $pd->extractor + $pd->genetic_lab + $pd->powerplant + $pd->factory);
		
		if ($cost > $pd->mineral) {
	 		$_SESSION['error_info'] = "Not enough resources to allocate the structure" ;
			return;
		}
		if ($pd->unassigned < 1) {
	 		$_SESSION['error_info'] = "No more structures to allocate" ;
			return;
		}
		
		$pd->subtract('mineral', $cost);
		$pd->subtract('unassigned', 1);
		
		$this->allocate_structure($structure_type);
		
	}
	
	function allocate_structure($structure_type) {
		if ($structure_type != 'extractor' && $structure_type != 'genetic_lab' && $structure_type != 'powerplant' && $structure_type != 'factory') {
			show_error("Invalid structure type: $structure_type");
			return;
		}
  	$player_name=$_SESSION['player_name'];
	  $conn = db_connect();
		$query = "select $structure_type as amount from player where name='$player_name'";
		$result = $conn->query($query);
		$row = $result->fetch_object();		
		$old_amount = $row->amount;
		$new_amount = $old_amount + 1;
		
		$update_query = "update player set $structure_type=$new_amount where name='$player_name'";
		$result = $conn->query($update_query);
		
		
	}
}
?>