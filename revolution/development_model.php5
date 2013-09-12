<?php 
	require_once('db_fns.php5'); 

class DevelopmentModel {
	
	function add_new_development($player_name, $development_item) {
	  $conn = db_connect();
		$gm = new GameModel();
		$tick_started = $gm->get_current_tick();
		$total_ticks = $this->get_development_time($development_item);
		
		$query = "insert into player_build values ('$player_name', 'development', '$development_item', 1, $tick_started, $total_ticks, 'developing')"; 
		
		$result = $conn->query($query);
	}
	
	function cancel_development($player_name, $development_item) {
	  $conn = db_connect();
		$query = "delete from player_build 
			where player_name='$player_name' 
			  and build_type='development' 
			  and build_item='$development_item'
			  and status='developing'";
		 
		$result = $conn->query($query);
	}

	function remove_development($player_name, $development_item) {
	  $conn = db_connect();
		$query = "delete from player_build 
			where player_name='$player_name' 
			  and build_type='development' 
			  and build_item='$development_item'
			  and status='completed'";
		 
		$result = $conn->query($query);
	}
	
	function get_development_time($development_item) {
	  $conn = db_connect();
		$query = "select ticks from development_items where name='$development_item'"; 
		$result = $conn->query($query);
		$row = $result->fetch_object();	
		return $row->ticks;
	}

	function get_development_proficiency($development_item) {
	  $conn = db_connect();
		$query = "select proficiency from development_items where name='$development_item'"; 
		$result = $conn->query($query);
		$row = $result->fetch_object();	
		return $row->proficiency;	
	}

	function get_development_type($development_item) {
	  $conn = db_connect();
		$query = "select type from development_items where name='$development_item'"; 
		$result = $conn->query($query);
		$row = $result->fetch_object();	
		return $row->type;	
	}
	
	function get_current_developments($player_name, $type, $proficiency) {
	  $conn = db_connect();
		$query = "select count(*) as count from player_build pb, development_items di 
			where pb.build_item = di.name
			and pb.build_type='development' 
			and di.proficiency = '$proficiency'
			and di.type = '$type' 
			and pb.status = 'completed'
			and player_name='$player_name'";
		$result = $conn->query($query);
		$row = $result->fetch_object();	
		return $row->count;	
		
	}

	
	function does_player_know_development($player_name, $development_name) {
	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name'
			and build_type='development' 
			and build_item='$development_name'
			and status='completed'";
		$result = $conn->query($query);
		if ($result->num_rows> 0) return true;
		else return false;
		
	}
	
	function get_technology_count($player_name, $tech_level) {
	  $conn = db_connect();
		$query = "select count(*) as count from player_build pb, development_items di 
			where pb.player_name='$player_name'
			  and di.name = pb.build_item
			  and di.proficiency = '$tech_level'
			  and pb.build_type='development' 
			  and pb.status='completed'";
		$result = $conn->query($query);
		$row = $result->fetch_object();	
		
		return $row->count;

	}

	function get_currently_developing($player_name) {
	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='development'
			and status='developing'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return false;
		$row = $result->fetch_object();
		return $row->build_item;		
	}

	function get_current_development_details($player_name) {
	  $conn = db_connect();
		$query = "select di.name, pb.ticks_remaining, di.ticks from player_build pb, development_items di where player_name='$player_name' and di.name=pb.build_item
			and pb.build_type='development'
			and pb.status='developing'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return false;
		$row = $result->fetch_object();
		$retval = array('name' => $row->name, 'total_ticks'=> $row->ticks, 'ticks_remaining'=> $row->ticks_remaining);
		return $retval;		
	}

	function get_all_players_with_development($development_name) {
		$player_list = array();
		$conn = db_connect();	
		$query = "SELECT player_name FROM player_build  
			WHERE build_item = '$development_name' 
				AND status='completed'
			" ;
	  $result = $conn->query($query);
		for ($count = 0; $row = $result->fetch_object(); $count++) {
			$player_list[$count] = $row->player_name;
		}
		return $player_list;
	}
	
	function get_list_of_all_developments() {
		$conn = db_connect();	 
		$query = "select name, ticks from development_items order by name";
		$result = $conn->query($query);
			  
		$ist = array();
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$list[$count]['item'] = $row->name;
			$list[$count]['ticks'] = $row->ticks;
		}	  
		return $list;
	}

	function is_developable($development_item) {
		$player_name = $_SESSION['player_name'];

	  $conn = db_connect();
		$query = "select * from development_items di, player_build pb where 
    		di.dependent_research = pb.build_item
    		and pb.build_type='research'
    		and pb.status = 'completed'
				and pb.player_name='$player_name'
				and di.name = '$development_item'";	
		$result = $conn->query($query);
		if ($result->num_rows>0) return true;
		else return false;	
	} 

}
?>