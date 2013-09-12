<?php

class AllianceModel {

	function get_list_of_alliances() {
		$alliance_list = array();
		$conn = db_connect();
		$query = "select * from alliance ";
		$result = $conn->query($query);
		
		for ($count = 0; $row = $result->fetch_object(); $count++) {
			$alliance_list[$count] = $row->alliance_name;
		}
		return $alliance_list;

	}

	function get_alliance_of_player($player_name) {
		$conn = db_connect();
		$query = "select * from player_alliance where player_name='$player_name'";
		$result = $conn->query($query);
		if ($result->num_rows == 0) {
			return NULL;
		}
		$row = $result->fetch_object();
		return $row->alliance;
	}

	function get_alliance_shorthand($player_name) {
		$conn = db_connect();
		$query = "select a.shorthand as shorthand from player_alliance pa, alliance a where player_name='$player_name'
			and pa.alliance = a.alliance_name";
		$result = $conn->query($query);
		if ($result->num_rows == 0) {
			return NULL;
		}
		$row = $result->fetch_object();
		return $row->shorthand;
	}

	function get_alliance_description($alliance) {
		$conn = db_connect();
		$query = "select * from description where category='alliance' and type='$alliance'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->description;
	}

	function get_alliance_leader($alliance) {
		$conn = db_connect();
		$query = "select player_name from player_alliance where alliance='$alliance' and rank='Leader'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->player_name;
	}

	function get_rank_of_player($player_name, $alliance) {
		$conn = db_connect();
		$query = "select * from player_alliance where player_name='$player_name' and alliance='$alliance' ";
		$result = $conn->query($query);
		if ($result->num_rows == 0) {
			return "nonmember";
		}
		$row = $result->fetch_object();
		return $row->rank;
	}

	function add_application($player_name, $alliance) {
		$conn = db_connect();
		$query = "insert into alliance_application values ('$player_name', '$alliance', NOW())";
		$result = $conn->query($query);
	}

	function withdraw_application($player_name) {
		$conn = db_connect();
		$query = "delete from alliance_application where player_name='$player_name'";
		$result = $conn->query($query);
	}
	
	function check_for_application($player_name) {
		$conn = db_connect();
		$query = "select * from alliance_application where player_name='$player_name'";
		$result = $conn->query($query);
		if ($result->num_rows > 0) return true;
		else return false;
	}

	function check_for_specific_application($player_name, $alliance) {
		$conn = db_connect();
		$query = "select * from alliance_application where player_name='$player_name' and alliance_name='$alliance'";
		$result = $conn->query($query);
		if ($result->num_rows > 0) return true;
		else return false;
	}

	function get_applicants($alliance) {
		$applicants = array();
		$conn = db_connect();
		$query = "select * from alliance_application where alliance_name='$alliance'";
		$result = $conn->query($query);
		
		for ($count = 0; $row = $result->fetch_object(); $count++) {
			$applicants[$count] = $row->player_name;
		}
		return $applicants;
	}

	function get_members($alliance, $rank) {
		$members = array();
		$conn = db_connect();
		$query = "select * from player_alliance where alliance='$alliance' and rank='$rank'";
		$result = $conn->query($query);
		
		for ($count = 0; $row = $result->fetch_object(); $count++) {
			$members[$count] = $row->player_name;
		}
		return $members;
	}

	function get_all_members($alliance) {
		$members = array();
		$conn = db_connect();
		$query = "select * from player_alliance where alliance='$alliance'";
		$result = $conn->query($query);
		
		for ($count = 0; $row = $result->fetch_object(); $count++) {
			$members[$count] = $row->player_name;
		}
		return $members;
	}


	function get_realtime_member_count($alliance) {
		$members = array();
		$conn = db_connect();
		$query = "select count(*) as count from player_alliance where alliance='$alliance'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->count;
	}

	function get_realtime_applicant_count($alliance) {
		$members = array();
		$conn = db_connect();
		$query = "select count(*) as count from alliance_application where alliance_name='$alliance'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->count;
	}

	function edit_description($alliance, $description) {
		$conn = db_connect();
		$query = "update alliance set description='$description' where alliance_name='$alliance'";
		$result = $conn->query($query);
	}

	function promote($player_name, $rank) {
		$conn = db_connect();
		$query = "update player_alliance set rank='$rank' where player_name='$player_name'";
		$result = $conn->query($query);
	}

	function kick_member($player_name) {
		$conn = db_connect();
		$query = "delete from player_alliance where player_name='$player_name'";
		$result = $conn->query($query);
	}
	
	function accept_applicant($player_name, $alliance_name) {
		$applicants = array();
		$conn = db_connect();
		$query = "insert into player_alliance values ('$player_name', '$alliance_name', 'Member')";
		$result = $conn->query($query);
	}
	
	function disband_alliance($alliance_name) {
		$applicants = array();
		$conn = db_connect();
		$query = "delete from alliance where alliance_name='$alliance_name'";
		echo $query;
		$result = $conn->query($query);
		$query = "delete from alliance_application where alliance_name='$alliance_name'";
		$result = $conn->query($query);
		$query = "delete from player_alliance where alliance='$alliance_name'";
		$result = $conn->query($query);
	}

	function create_new_alliance($player_name, $alliance_name, $shorthand, $description) {
		$conn = db_connect();
		$query = "select * from player_alliance where player_name='$player_name'";
		$result = $conn->query($query);
		if ($result->num_rows > 0) {
			show_error("You cannot create an alliance if you are in an alliance");
			return false;
		}
		
		$conn = db_connect();
		$query = "select * from alliance where alliance_name='$alliance_name'";
		$result = $conn->query($query);
		if ($result->num_rows > 0) {
			show_error("The alliance name: $alliance_name is already in use");
			return false;
		}
		
		$conn = db_connect();
		$query = "select * from alliance where shorthand='$shorthand'";
		$result = $conn->query($query);
		if ($result->num_rows > 0) {
			show_error("The alliance tag: $shorthand is already in use");
			return false;
		}
		
		$query = "insert into alliance values ('$alliance_name', '$shorthand', '$description', 0, 0, 0)";
		$result = $conn->query($query);
		if (!$result) return false;
		
		$query = "insert into player_alliance values ('$player_name', '$alliance_name', 'Leader')";
		$result = $conn->query($query);
		if (!$result) return false;
		
		return true;
	}
	
// becuase it is used in two places the code is put here.

	function display_alliance_ranking($from) {
 		$order=$_REQUEST['order'];

		$vf = new ViewFunctions();
	  $conn = db_connect();
	  if (strcmp($order, "name") == 0) $order_by = "ORDER BY alliance_name";
	  else if (strcmp($order, "shorthand") == 0) $order_by = "ORDER BY shorthand";
	  else if (strcmp($order, "members") == 0) $order_by = "ORDER BY members desc";
	  else if (strcmp($order, "structures") == 0) $order_by = "ORDER BY total_structures desc";
	  else if (strcmp($order, "score") == 0) $order_by = "ORDER BY score desc";
	  else $order_by = "ORDER BY score desc";
	  
	  
		$query = "select * from alliance $order_by";
		$result = $conn->query($query);
		echo "<TABLE class='STD' >\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='4'>Alliance Rankings</TH>\n";
		echo " </TR>\n";
		if ($result->num_rows == 0) {
			echo "  <TD class='STD' colspan='5'> No Players in the game ??? </TD>\n";
		} else {
			echo " <TR>\n";
			$vf->display_button('Alliance', '008000', '40B040', "{$from}&order=name");
			$vf->display_button('Shorthand', '008000', '40B040', "{$from}&order=shorthand");
			$vf->display_button('Members', '008000', '40B040', "{$from}&order=members");
			$vf->display_button('Structures', '008000', '40B040', "{$from}&order=structures");
			$vf->display_button('Score', '008000', '40B040', "{$from}&order=score");
			$vf->display_button('Details', '008000', '40B040', "{$from}&order=score");
			echo " </TR>\n";
			for ($count=0; $row = $result->fetch_object(); $count++) {
				$total_structures = $row->unassigned + $row->extractor + $row->genetic_lab + $row->powerplant + $row->factory;
				echo " <TR>\n";
				echo "  <TD class='STD'> {$row->alliance_name} </TD>\n";
				echo "  <TD class='STD'> &nbsp;{$row->shorthand}&nbsp; </TD>\n";
				echo "  <TD class='STD'> {$row->members} </TD>\n";
				echo "  <TD class='STD'> {$row->total_structures} </TD>\n";
				echo "  <TD class='STD'> {$row->score}</TD>\n";
				$vf->display_button('Details', '404000', '808040', "main_page.php5?view=alliances&subview=details&alliance={$row->alliance_name}");
				echo " </TR>\n";
			}
		}
		echo "</TABLE>\n";
		
	}

	function create_new_declaration($alliance, $target_alliance, $type, $until_tick, $description) {
		if ($until_tick < 1) $until_tick = 0;
		// Check to see that there is no current declaration involving the two alliances.
	  $conn = db_connect();
		$query = "select * from alliance_declarations where alliance = '$alliance' and target_alliance = '$target_alliance' and active=1";
		$result = $conn->query($query);
		if ($result->num_rows > 0) {
			show_error("You must remove declarations concerning these two alliances before you make another");
			return;
		}		
		$query = "insert into alliance_declarations values ('$alliance', '$target_alliance', '$type', $until_tick, NOW(), '$description', 1) ";
		$result = $conn->query($query);
	}

	function remove_declaration($alliance, $target_alliance) {
	  $conn = db_connect();
		$query = "update alliance_declarations set active=0 where alliance='$alliance' and target_alliance='$target_alliance'";
		$result = $conn->query($query);
	}
	
	function get_declarations_by_alliance($alliance) {
	  $conn = db_connect();
		$declaration_list = array();
		$query = "select * from alliance_declarations where alliance='$alliance' and active=1";
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$declaration_list["$row->target_alliance"]["type"] = $row->type;
			$declaration_list["$row->target_alliance"]["until_tick"] = $row->until_tick;
			$declaration_list["$row->target_alliance"]["time"] = $row->time;
			$declaration_list["$row->target_alliance"]["description"] = $row->text;
		}
		
		return $declaration_list;
	}
	
	function get_all_declarations_by_type($type) {
	  $conn = db_connect();
		$declaration_list = array();
		$query = "select * from alliance_declarations where type='$type' and active=1 order by time";
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$declaration_list[$count]["alliance"] = $row->alliance;
			$declaration_list[$count]["target_alliance"] = $row->target_alliance;
			$declaration_list[$count]["type"] = $row->type;
			$declaration_list[$count]["until_tick"] = $row->until_tick;
			$declaration_list[$count]["time"] = $row->time;
			$declaration_list[$count]["description"] = $row->text;
		}
		
		return $declaration_list;
	}
	
	function is_leader($player_name, $alliance) {
	  $conn = db_connect();
		$query = "select * from player_alliance where player_name='$player_name' and alliance='$alliance'";
		$result = $conn->query($query);
		$row = $result->fetch_object(); 
		if ($row->rank == 'Leader') return true;
		else return false;
	}

	function is_senior($player_name, $alliance) {
	  $conn = db_connect();
		$query = "select * from player_alliance where player_name='$player_name' and alliance='$alliance'";
		$result = $conn->query($query);
		$row = $result->fetch_object(); 
		if ($row->rank == 'Leader') return true;
		else if ($row->rank == 'Senior') return true;
		else return false;
	}

	function is_member($player_name, $alliance) {
	  $conn = db_connect();
		$query = "select * from player_alliance where player_name='$player_name' and alliance='$alliance'";
		$result = $conn->query($query);
		$row = $result->fetch_object(); 
		if ($row->rank == 'Leader') return true;
		else if ($row->rank == 'Senior') return true;
		else if ($row->rank == 'Member') return true;
		else return false;
	}

}
?>