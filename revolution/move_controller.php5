<?php
	require_once('db_fns.php5'); 
	require_once('fleet_model.php5'); 

class MoveController {
	function move_player() {
		$ticks_out = 24;
		$fm = new FleetModel();
		
  	$player_name=$_SESSION['player_name'];
  	$galaxy=$_REQUEST['galaxy'];
  	$star=$_REQUEST['star'];
  	$planet=$_REQUEST['planet'];
  	$continent=$_REQUEST['continent'];
  	$invite_key=$_REQUEST['invite_key'];
  	
  	if (!$this->is_move_valid($galaxy, $star, $planet, $continent)) {
 			// Errors reported in sub-function 
 			return;
  	} 
  	
  	if ($fm->is_active_fleet_orders($player_name, "fleet1")) {
 			show_error("Cannot move when any fleet is on a mission");
 			return;
  	}
  	if ($fm->is_active_fleet_orders($player_name, "fleet2")) {
 			show_error("Cannot move when any fleet is on a mission");
 			return;
  	}
  	if ($fm->is_active_fleet_orders($player_name, "fleet3")) {
 			show_error("Cannot move when any fleet is on a mission");
 			return;
  	}
  	
  	
 	  $conn = db_connect();
		$query = "update player 
			set galaxy=$galaxy, star=$star, planet=$planet, continent=$continent
			where name='$player_name'"; 
		$result = $conn->query($query);
	
		// Now set all fleets to out of action for 72 ticks....
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		
		
		$launch_tick = $current_tick;
		$arrival_tick = $launch_tick + $ticks_out;
		$depart_tick = $arrival_tick ;
		$return_tick = $depart_tick;	
		
		$query = "insert into player_orders values ('$player_name', '$player_name', 'move', 24,
			'fleet1', NOW(), $launch_tick, $arrival_tick, $depart_tick, $return_tick, 0, 0, 0, 0, 0)";
		$result = $conn->query($query);

		$query = "insert into player_orders values ('$player_name', '$player_name', 'move', 24,
			'fleet2', NOW(), $launch_tick, $arrival_tick, $depart_tick, $return_tick, 0, 0, 0, 0, 0)";
		$result = $conn->query($query);

		$query = "insert into player_orders values ('$player_name', '$player_name', 'move', 24,
			'fleet3', NOW(), $launch_tick, $arrival_tick, $depart_tick, $return_tick, 0, 0, 0, 0, 0)";
		$result = $conn->query($query);
		
		show_info("You have moved to $galaxy:$star:$planet:$continent.  All you fleets will be out moving your homebase for the next 24 ticks");
	}
	
	function is_move_valid($galaxy, $star, $planet, $continent) {
		if ($galaxy != 1) {
			show_error("Currently Galaxy 1 is the only possible destination for moves");
			return false;
		}
		if ($this->is_taken($galaxy, $star, $planet, $continent)) {
			show_error("The destination is already taken");
			return false;
		}
		if ($continent != 1) {
			if (!$this->is_invite_key_correct()) {
				show_error("Wrong invite key");
				return false;
			}
		}
		return true;
	}
	
	function is_invite_key_correct() {
  	$galaxy=$_REQUEST['galaxy'];
  	$star=$_REQUEST['star'];
  	$planet=$_REQUEST['planet'];
  	$continent=$_REQUEST['continent'];
  	$invite_key=$_REQUEST['invite_key'];
  	
 	  $conn = db_connect();
		$query = "select * from invite_key
			where galaxy=$galaxy 
			and star=$star 
			and planet=$planet
			and invite_key='$invite_key'";
		$result = $conn->query($query);
		if ($result->num_rows > 0) return true;
		else return false;
				
	}
	
	function is_taken($galaxy, $star, $planet, $continent) {
	  $conn = db_connect();
		$query = "select * from player 
			where galaxy=$galaxy 
			and   star=$star
			and   planet=$planet
			and   continent=$continent"; 
		$result = $conn->query($query);
		if ($result->num_rows > 0) return true;
		else return false;
	}
	
	function set_invite_key() {
  	$player_name=$_SESSION['player_name'];
  	$galaxy=$_REQUEST['galaxy'];
  	$star=$_REQUEST['star'];
  	$planet=$_REQUEST['planet'];
  	$invite_key=$_REQUEST['invite_key'];

	  $conn = db_connect();
		$query = "delete from invite_key
			where galaxy=$galaxy 
			and   star=$star
			and   planet=$planet";
		$result = $conn->query($query);

		$query = "insert into invite_key values 
				('$player_name', $galaxy, $star, $planet, '$invite_key')";
		$result = $conn->query($query);
			 
				
	}
}

?>