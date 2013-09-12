<?php
	require_once('db_fns.php5'); 
	require_once('player_data.php5'); 
	require_once('view_fns.php5'); 
	require_once('game_model.php5');
	require_once('fleet_model.php5');
	require_once('bombs_model.php5');
	require_once("creatures_model.php5");

class FleetView {
	var $fleet_weights;
	
	function display_fleet() {
  	$player_name=$_SESSION['player_name'];

		$this->display_fleet_assignments();
		$this->display_bomb_loading();
		$this->display_targeting();
		$this->display_fleets_on_mission();
	}
	
	function display_fleet_assignments() {
  	$player_name=$_SESSION['player_name'];

		echo "<FORM method='post' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='move_fleets'/>\n";
		echo "     <INPUT type='hidden' name='view' value='fleets'/>\n";

		echo "<TABLE class='STD' >\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='9'>Fleets</TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='9'>&nbsp; </TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "   <TH class='STD' >&nbsp;</TH>\n";
		echo "   <TH class='STD' colspan='2'>Home</TH>\n";
		echo "   <TH class='STD' colspan='2'>Fleet #1</TH>\n";
		echo "   <TH class='STD' colspan='2'>Fleet #2</TH>\n";
		echo "   <TH class='STD' colspan='2'>Fleet #3</TH>\n";
		echo " </TR>\n";

		$creatures = array();

	  $conn = db_connect();
		$query = "select * from player_creatures where player_name='$player_name'";
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$critter=$row->creature;
			$fleet_loc=$row->fleet_location;
			$creatures["$critter"]["$fleet_loc"] = $row->number;
		}
		$count = 0;
		$attack = array( array('home', 0), array('fleet1', 0), array('fleet2', 0), array('fleet3', 0) );
		$focus = array( array('home', 0), array('fleet1', 0), array('fleet2', 0), array('fleet3', 0) );
		$defense = array( array('home', 0), array('fleet1', 0), array('fleet2', 0), array('fleet3', 0) );
		$weight = array( array('home', 0), array('fleet1', 0), array('fleet2', 0), array('fleet3', 0) );
		$launch_cost = array( array('home', 0), array('fleet1', 0), array('fleet2', 0), array('fleet3', 0) );
		
		foreach ($creatures as $key=>$arr) {
			$home=$arr['home']; if (strlen($home)==0) $home = '&nbsp;';
			$f1=$arr['fleet1']; if (strlen($f1)==0) $f1 = '&nbsp;';
			$f2=$arr['fleet2']; if (strlen($f2)==0) $f2 = '&nbsp;';
			$f3=$arr['fleet3']; if (strlen($f3)==0) $f3 = '&nbsp;';
			
			echo " <TR>\n";
			echo "  <TD class='STD' width='20%'> $key </TD>\n";
			echo "  <TD class='STD' width='10%'> $home </TD>\n";
			echo "  <TD class='STD' width='10%'> <INPUT type='text' name='row{$count}_home' size='8' /> </TD>\n";
			echo "  <TD class='STD' width='10%'> $f1</TD>\n";
			echo "  <TD class='STD' width='10%'> <INPUT type='text' name='row{$count}_fleet1' size='8' /> </TD>\n";
			echo "  <TD class='STD' width='10%'> $f2</TD>\n";
			echo "  <TD class='STD' width='10%'> <INPUT type='text' name='row{$count}_fleet2' size='8' /> </TD>\n";
			echo "  <TD class='STD' width='10%'> $f3</TD>\n";
			echo "  <TD class='STD' width='10%'> <INPUT type='text' name='row{$count}_fleet3' size='8' /> </TD>\n";
			echo "     <INPUT type='hidden' name='row{$count}_creature' value='$key'/>\n";
			echo " </TR>\n";
			$count++;
			$cm = new CreaturesModel();
			$attack_value = $cm->get_attack_value($key);
			$attack['home'] += ($home * $attack_value);
			$attack['fleet1'] += ($f1 * $attack_value);
			$attack['fleet2'] += ($f2 * $attack_value);
			$attack['fleet3'] += ($f3 * $attack_value);

			$focus_value = $cm->get_focus_value($key);
			$focus['home'] += ($home * $focus_value);
			$focus['fleet1'] += ($f1 * $focus_value);
			$focus['fleet2'] += ($f2 * $focus_value);
			$focus['fleet3'] += ($f3 * $focus_value);

			$defense_value = $cm->get_defense_value($key);
			$defense['home'] += ($home * $defense_value);
			$defense['fleet1'] += ($f1 * $defense_value);
			$defense['fleet2'] += ($f2 * $defense_value);
			$defense['fleet3'] += ($f3 * $defense_value);

			$weight_value = $cm->get_weight_value($key);
			if ($weight_value > $weight['home'] && $home > 0) $weight['home'] = $weight_value;
			if ($weight_value > $weight['fleet1'] && $f1 > 0) $weight['fleet1'] = $weight_value;
			if ($weight_value > $weight['fleet2'] && $f2 > 0) $weight['fleet2'] = $weight_value;
			if ($weight_value > $weight['fleet3'] && $f3 > 0) $weight['fleet3'] = $weight_value;

			$launch_cost_value = $cm->get_launch_cost($key);
			$launch_cost['home'] += ($home * $launch_cost_value);
			$launch_cost['fleet1'] += ($f1 * $launch_cost_value);
			$launch_cost['fleet2'] += ($f2 * $launch_cost_value);
			$launch_cost['fleet3'] += ($f3 * $launch_cost_value);

		}
		echo "     <INPUT type='hidden' name='total_rows' value='$count'/>\n";
		
		$this->display_fleet_selectors();
		$this->display_fleet_characteristics($attack, $focus, $defense, $weight, $launch_cost);
		$this->display_launch_times($weight);

		echo "</TABLE>\n";
		
		echo "<INPUT type='submit' value='Move Creatures' />\n";
		echo "</FORM>\n";
		
		$this->fleet_weights = $weight;
	}
	
	function display_fleet_selectors() {
  	$player_name=$_SESSION['player_name'];
		$fm = new FleetModel();
		
		echo " <TR>\n";
		echo "  <TD class='STD' width='20%'> &nbsp; </TD>\n";
		echo "  <TD class='STD' width='10%'> &nbsp; </TD>\n";
		echo "  <TD class='STD' width='10%'>\n";
		echo "    <SELECT name='from_home'>\n";
		echo "      <OPTION value='nowhere' selected='true'> &nbsp;</OPTION>\n";
		echo "      <OPTION value='to_home' disabled='true'> Home </OPTION>\n";
		if (!$fm->is_active_fleet_orders($player_name, "fleet1")) echo "      <OPTION value='to_1'> Fleet #1</OPTION>\n";
		else echo "      <OPTION value='to_1' disabled='true'> Fleet #1</OPTION>\n";
		if (!$fm->is_active_fleet_orders($player_name, "fleet2")) echo "      <OPTION value='to_2'> Fleet #2</OPTION>\n";
		else echo "      <OPTION value='to_2' disabled='true'> Fleet #2</OPTION>\n";
		if (!$fm->is_active_fleet_orders($player_name, "fleet3")) echo "      <OPTION value='to_3'> Fleet #3</OPTION>\n";
		else echo "      <OPTION value='to_3' disabled='true'> Fleet #3</OPTION>\n";
		echo "    </SELECT>\n";
		echo "   </TD>\n";
		echo "  <TD class='STD' width='10%'> &nbsp; </TD>";
		echo "  <TD class='STD' width='10%'>\n";
		echo "    <SELECT name='from_1'>\n";
		echo "      <OPTION value='nowhere' selected='true'> &nbsp;</OPTION>\n";
		echo "      <OPTION value='to_home'> Home </OPTION>\n";
		echo "    </SELECT>\n";
		echo "   </TD>\n";
		echo "  <TD class='STD' width='10%'> &nbsp; </TD>";
		echo "  <TD class='STD' width='10%'>\n";
		echo "    <SELECT name='from_2'>\n";
		echo "      <OPTION value='nowhere' selected='true'> &nbsp;</OPTION>\n";
		echo "      <OPTION value='to_home'> Home</OPTION>\n";
		echo "    </SELECT>\n";
		echo "   </TD>\n";
		echo "  <TD class='STD' width='10%'> &nbsp; </TD>";
		echo "  <TD class='STD' width='10%'>\n";
		echo "    <SELECT name='from_3'>\n";
		echo "      <OPTION value='nowhere' selected='true'> &nbsp;</OPTION>\n";
		echo "      <OPTION value='to_home'> Home</OPTION>\n";
		echo "    </SELECT>\n";
		echo "   </TD>\n";
		echo " </TR>\n";
	}
	
	function display_fleet_characteristics($attack, $focus, $defense, $weight, $launch_cost) {
		echo " <TR>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD'> <B>ATTACK</B> </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $attack['home']  . " &nbsp;  </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $attack['fleet1'] . " &nbsp; </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $attack['fleet2'] . " &nbsp; </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $attack['fleet3'] . " &nbsp; </TD>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD'> <B>FOCUS</B> </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $focus['home']  . " &nbsp; </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $focus['fleet1'] . " &nbsp; </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $focus['fleet2'] . " &nbsp; </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $focus['fleet3'] . " &nbsp; </TD>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD'> <B>DEFENSE</B> </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $defense['home'] . " &nbsp; </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $defense['fleet1'] . " &nbsp; </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $defense['fleet2'] . " &nbsp; </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $defense['fleet3'] . " &nbsp; </TD>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD'> <B>WEIGHT</B> </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $weight['home'] . " &nbsp; </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $weight['fleet1'] . " &nbsp; </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $weight['fleet2'] . " &nbsp; </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $weight['fleet3'] . " &nbsp; </TD>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD'> <B>LAUNCH COST</B> </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $launch_cost['home'] . " &nbsp; </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $launch_cost['fleet1'] . " &nbsp; </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $launch_cost['fleet2'] . " &nbsp; </TD>\n";
		echo "  <TD class='STD' colspan='2'> &nbsp; " . $launch_cost['fleet3'] . " &nbsp; </TD>\n";
		echo " </TR>\n";
	}
	
	function display_launch_times($weight) {		
  	$player_name=$_SESSION['player_name'];
		require_once('fleet_model.php5');
		$fm = new FleetModel();		

		echo " <TR></TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD'> <B>Galaxy Travel</B> </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'galaxy', $weight['home']) . " </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'galaxy', $weight['fleet1']) . " </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'galaxy', $weight['fleet2']) . " </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'galaxy', $weight['fleet3']) . " </TD>\n";
		echo " </TR>\n";
		
		echo " <TR>\n";
		echo "  <TD class='STD'> <B>Star Travel</B> </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'star', $weight['home']) . " </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'star', $weight['fleet1']) . " </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'star', $weight['fleet2']) . " </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'star', $weight['fleet3']) . " </TD>\n";
		echo " </TR>\n";
		
		echo " <TR>\n";
		echo "  <TD class='STD'> <B>Planet Travel</B> </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'planet', $weight['home']) . " </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'planet', $weight['fleet1']) . " </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'planet', $weight['fleet2']) . " </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'planet', $weight['fleet3']) . " </TD>\n";
		echo " </TR>\n";
		
		echo " <TR>\n";
		echo "  <TD class='STD'> <B>Continent Travel</B> </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'continent', $weight['home']) . " </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'continent', $weight['fleet1']) . " </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'continent', $weight['fleet2']) . " </TD>\n";
		echo "  <TD class='STD' colspan='2'> " . $fm->get_fleet_speed($player_name, 'continent', $weight['fleet3']) . " </TD>\n";
		echo " </TR>\n";
		
	}
	
	function display_bomb_loading() {
  	$player_name=$_SESSION['player_name'];
		$bm = new BombsModel();
		$bomb_location = $bm->get_bomb_locations($player_name, 'Bomb');
		$bomb_at_home = $bomb_location["home"]; if (strlen($bomb_at_home) == 0) $bomb_at_home = '&nbsp';
		$bomb_in_fleet1 = $bomb_location["fleet1"]; if (strlen($bomb_in_fleet1) == 0) $bomb_in_fleet1 = '&nbsp';
		$bomb_in_fleet2 = $bomb_location["fleet2"]; if (strlen($bomb_in_fleet2) == 0) $bomb_in_fleet2 = '&nbsp';
		$bomb_in_fleet3 = $bomb_location["fleet3"]; if (strlen($bomb_in_fleet3) == 0) $bomb_in_fleet3 = '&nbsp';

		$poison_bomb_location = $bm->get_bomb_locations($player_name, 'Poison Bomb');
		$poison_bomb_at_home = $poison_bomb_location["home"]; if (strlen($poison_bomb_at_home) == 0) $poison_bomb_at_home = '&nbsp';
		$poison_bomb_in_fleet1 = $poison_bomb_location["fleet1"]; if (strlen($poison_bomb_in_fleet1) == 0) $poison_bomb_in_fleet1 = '&nbsp';
		$poison_bomb_in_fleet2 = $poison_bomb_location["fleet2"]; if (strlen($poison_bomb_in_fleet2) == 0) $poison_bomb_in_fleet2 = '&nbsp';
		$poison_bomb_in_fleet3 = $poison_bomb_location["fleet3"]; if (strlen($poison_bomb_in_fleet3) == 0) $poison_bomb_in_fleet3 = '&nbsp';


		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='move_bombs'/>\n";
		echo "     <INPUT type='hidden' name='view' value='fleets'/>\n";
		
		echo "<TABLE class='STD' >\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='9'>Bombs</TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "   <TH class='STD' >&nbsp;</TH>\n";
		echo "   <TH class='STD' colspan='2'>Home</TH>\n";
		echo "   <TH class='STD' colspan='2'>Fleet #1</TH>\n";
		echo "   <TH class='STD' colspan='2'>Fleet #2</TH>\n";
		echo "   <TH class='STD' colspan='2'>Fleet #3</TH>\n";
		echo " </TR>\n";
			echo " <TR>\n";
			echo "  <TD class='STD' width='20%'> Bomb </TD>\n";
			echo "  <TD class='STD' width='10%'> $bomb_at_home </TD>\n";
			echo "  <TD class='STD' width='10%'> <INPUT type='text' name='bombs_from_home' size='8' /> </TD>\n";
			echo "  <TD class='STD' width='10%'> $bomb_in_fleet1</TD>\n";
			echo "  <TD class='STD' width='10%'> <INPUT type='text' name='bombs_from_fleet1' size='8' /> </TD>\n";
			echo "  <TD class='STD' width='10%'> $bomb_in_fleet2</TD>\n";
			echo "  <TD class='STD' width='10%'> <INPUT type='text' name='bombs_from_fleet2' size='8' /> </TD>\n";
			echo "  <TD class='STD' width='10%'> $bomb_in_fleet3</TD>\n";
			echo "  <TD class='STD' width='10%'> <INPUT type='text' name='bombs_from_fleet3' size='8' /> </TD>\n";
			echo " </TR>\n";
		echo " <TR>\n";
		echo " </TR>\n";
			echo " <TR>\n";
			echo "  <TD class='STD' width='20%'> Poison Bomb </TD>\n";
			echo "  <TD class='STD' width='10%'> $poison_bomb_at_home </TD>\n";
			echo "  <TD class='STD' width='10%'> <INPUT type='text' name='poison_bombs_from_home' size='8' /> </TD>\n";
			echo "  <TD class='STD' width='10%'> $poison_bomb_in_fleet1</TD>\n";
			echo "  <TD class='STD' width='10%'> <INPUT type='text' name='poison_bombs_from_fleet1' size='8' /> </TD>\n";
			echo "  <TD class='STD' width='10%'> $poison_bomb_in_fleet2</TD>\n";
			echo "  <TD class='STD' width='10%'> <INPUT type='text' name='poison_bombs_from_fleet2' size='8' /> </TD>\n";
			echo "  <TD class='STD' width='10%'> $poison_bomb_in_fleet3</TD>\n";
			echo "  <TD class='STD' width='10%'> <INPUT type='text' name='poison_bombs_from_fleet3' size='8' /> </TD>\n";
			echo " </TR>\n";
		echo " <TR>\n";




		echo " </TR>\n";
		$this->display_fleet_selectors();

		echo "</TABLE>\n";
		echo "<INPUT type='submit' value='Move Bombs' />\n";
		echo "</FORM>\n";
		echo "<BR />";
	}
	
	function display_targeting() {
  	$player_name=$_SESSION['player_name'];
		require_once('fleet_model.php5');
		$fm = new FleetModel();		
		
		echo "<TABLE class='STD' >\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='8'>Targeting</TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "   <TD class='STD' > Fleet # </TD>\n";
		echo "   <TD class='STD' > Galaxy </TD>\n";
		echo "   <TD class='STD' > Star </TD>\n";
		echo "   <TD class='STD' > Planet </TD>\n";
		echo "   <TD class='STD' > Continent </TD>\n";
		echo "   <TD class='STD' > Mission </TD>\n";
		echo "   <TD class='STD' > Ticks Left </TD>\n";
		echo "   <TD class='STD' > Launch </TD>\n";
		echo " </TR>\n";
		
		if ($fm->is_active_fleet_orders($player_name, 'fleet1')) {
			$this->display_fleet_out('fleet1');
		} else {
			$this->display_fleet_launch(1, $this->fleet_weights['fleet1']);
		}

		if ($fm->is_active_fleet_orders($player_name, 'fleet2')) {
			$this->display_fleet_out('fleet2');
		} else {
			$this->display_fleet_launch(2, $this->fleet_weights['fleet2']);
		}
		
		if ($fm->is_active_fleet_orders($player_name, 'fleet3')) {
			$this->display_fleet_out('fleet3');
		} else {
			$this->display_fleet_launch(3, $this->fleet_weights['fleet3']);
		}
		
		echo "</TABLE>\n";
		echo "<BR />";
	}
	
	function display_fleet_launch($fleet, $weight) {
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='launch_fleet'/>\n";
		echo "     <INPUT type='hidden' name='view' value='fleets'/>\n";
		echo "     <INPUT type='hidden' name='fleet' value='$fleet'/>\n";
		echo "     <INPUT type='hidden' name='weight' value='$weight'/>\n";

		echo " <TR>\n";
		echo "   <TD class='STD' > Fleet $fleet </TD>\n";
		
		$this->show_galaxy_select();
		$this->show_star_select();
		$this->show_planet_select();
		$this->show_continent_select();
		$this->show_mission_select();
		echo "   <TD class='STD' > <I> on ground </I> </TD>\n";
		echo "   <TD class='STD' > <INPUT type='submit' value='Launch' /> </TD>\n";
		echo " </TR>\n";
		echo "</FORM>\n";
		
	}

	function display_fleet_out($fleet) {
		echo "   <TD class='STD' colspan='8'> $fleet is currently out </TD>\n";		
		echo " </TR>\n";
	}
	
	function show_galaxy_select() {
  	$player_name=$_SESSION['player_name'];
  	$galaxy = $_REQUEST["galaxy"];
  	
		$pd = new PlayerData();
		$pd->db_fill($player_name);
		require_once('development_model.php5');
		$dm = new DevelopmentModel();
		echo 	"  <TD class='STD' > ";
		if($dm->does_player_know_development($player_name, 'Intergalactic Vehicles')) {
			echo 	"   <SELECT name='galaxy'>\n ";
			for ($i=1; $i <= 3; $i++) {
				if ($i == $galaxy)  echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
				else if ($i == $pd->galaxy && $galaxy == '')  echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
				else echo 	"   <OPTION value='$i'>$i</OPTION>\n ";
			}
			echo "    </SELECT>\n";
		} else {
			require_once("player_data.php5");
			
			echo "$pd->galaxy";
			echo "     <INPUT type='hidden' name='galaxy' value='$pd->galaxy'/>\n";
		}
		echo  "  </TD>\n";
	}
	function show_star_select() {
  	$player_name=$_SESSION['player_name'];
  	$star = $_REQUEST["star"];
  	
		$pd = new PlayerData();
		$pd->db_fill($player_name);
		require_once('development_model.php5');
		$dm = new DevelopmentModel();
		echo 	"  <TD class='STD' > ";
		if($dm->does_player_know_development($player_name, 'Interstellar Vehicles') || 
					$dm->does_player_know_development($player_name, 'Intergalactic Vehicles')) {
			echo 	"   <SELECT name='star'>\n ";
			for ($i=1; $i <= 29; $i++) {
				if ($i == $star) echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
				else if ($i == $pd->star && $star == '') echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
				else echo 	"   <OPTION value='$i'>$i</OPTION>\n ";
			}
			echo "    </SELECT>\n";
		} else {
			require_once("player_data.php5");
			echo "$pd->star";
			echo "     <INPUT type='hidden' name='star' value='$pd->star'/>\n";
		}
		echo  "  </TD>\n";
	}

	function show_planet_select() {
  	$player_name=$_SESSION['player_name'];
   	$planet = $_REQUEST["planet"];
 	
		$pd = new PlayerData();
		$pd->db_fill($player_name);
		require_once('development_model.php5');
		$dm = new DevelopmentModel();
		echo 	"  <TD class='STD' > ";
		if($dm->does_player_know_development($player_name, 'Interplanetary Vehicles') || 
					$dm->does_player_know_development($player_name, 'Interstellar Vehicles') || 
					$dm->does_player_know_development($player_name, 'Intergalactic Vehicles') ) {
			echo 	"   <SELECT name='planet'>\n ";
			for ($i=1; $i <= 9; $i++) {
				if ($i == $planet) echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
				else if ($i == $pd->planet && $planet == '') echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
				else echo 	"   <OPTION value='$i'>$i</OPTION>\n ";
			}
			echo "    </SELECT>\n";
		} else {
			require_once("player_data.php5");
			echo "$pd->planet";
			echo "     <INPUT type='hidden' name='planet' value='$pd->planet'/>\n";
		}
		echo  "  </TD>\n";
	}
	
	function show_continent_select() {
  	$player_name=$_SESSION['player_name'];
    $continent = $_REQUEST["continent"];
 	
		$pd = new PlayerData();
		$pd->db_fill($player_name);
		require_once('development_model.php5');
		$dm = new DevelopmentModel();
		echo 	"  <TD class='STD' > ";
		if($dm->does_player_know_development($player_name, 'Intercontinental Vehicles') ||
					$dm->does_player_know_development($player_name, 'Interplanetary Vehicles') || 
					$dm->does_player_know_development($player_name, 'Interstellar Vehicles') || 
					$dm->does_player_know_development($player_name, 'Intergalactic Vehicles') ) {
			echo 	"   <SELECT name='continent'>\n ";
			for ($i=1; $i <= 9; $i++) {
				if ($i == $continent) echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
				else if ($i == $pd->continent && $continent == '') echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
				else echo 	"   <OPTION value='$i'>$i</OPTION>\n ";
			}
			echo "    </SELECT>\n";
		} else {
			require_once("player_data.php5");
			echo "$pd->continent";
			echo "     <INPUT type='hidden' name='continent' value='$pd->continent'/>\n";
		}
		echo  "  </TD>\n";
	}
	function show_mission_select() {
		echo 	"  <TD class='STD' > ";
		echo 	"   <SELECT name='mission'>\n ";
			echo 	"   <OPTION value='nothing'> </OPTION>\n ";
			echo 	"   <OPTION value='attack1'>1 tick attack</OPTION>\n ";
			echo 	"   <OPTION value='attack2'>2 tick attack</OPTION>\n ";
			echo 	"   <OPTION value='attack3'>3 tick attack</OPTION>\n ";
			echo 	"   <OPTION value='defense1'>1 tick defense</OPTION>\n ";
			echo 	"   <OPTION value='defense2'>2 tick defense</OPTION>\n ";
			echo 	"   <OPTION value='defense3'>3 tick defense</OPTION>\n ";
			echo 	"   <OPTION value='defense4'>4 tick defense</OPTION>\n ";
			echo 	"   <OPTION value='defense5'>5 tick defense</OPTION>\n ";
			echo 	"   <OPTION value='defense6'>6 tick defense</OPTION>\n ";
		echo "    </SELECT>\n";
		echo  "  </TD>\n";
	}

	function display_fleets_on_mission() {
		echo $this->make_fleets_on_mission_display(true);
	}
	
	function make_fleets_on_mission_display($display_recall_button) {
		$player_name=$_SESSION['player_name'];
		$fm = new FleetModel();		

		if (! $fm->is_any_active_fleet_orders($player_name)) return ""; // If no fleet is on orders then return empty
		
		require_once('game_model.php5');
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		
		$string = "";
		
		$string .= "<TABLE class='STD' >\n";
		$string .= " <TR>\n";
		$string .= "   <TH class='STD' colspan='8'>Missions </TH>\n";
		$string .= " </TR>\n";

/*
		$string .= " <TR>\n";
		$string .= "   <TH class='STD' style='background-color:202080'> Player </TH>\n";
		$string .= "   <TH class='STD' style='background-color:202080'> Fleet # </TH>\n";
		$string .= "   <TD class='STD' style='background-color:202080'> Target Location</TD>\n";
		$string .= "   <TD class='STD' style='background-color:202080'> Target Name </TD>\n";
		$string .= "   <TD class='STD' style='background-color:202080'> Mission Type </TD>\n";
		$string .= "   <TD class='STD' style='background-color:202080'> Launch Tick </TD>\n";
		$string .= "   <TD class='STD' style='background-color:202080'> Arrival Tick </TD>\n";
		$string .= "   <TD class='STD' style='background-color:202080'> Depart Tick </TD>\n";
		$string .= "   <TD class='STD' style='background-color:202080'> Return Tick </TD>\n";
		if ($display_recall_button) $string .= "   <TD class='STD' style='background-color:202080'> Recall </TD>\n";
		$string .= " </TR>\n";
*/

		$string .= $this->display_order_by_fleet($player_name, 'fleet1', $display_recall_button);
		$string .= $this->display_order_by_fleet($player_name, 'fleet2', $display_recall_button);
		$string .= $this->display_order_by_fleet($player_name, 'fleet3', $display_recall_button);
		
		$string .= "</TABLE>\n";

		$string .= $this->make_individual_timetable_display();

		
		return $string;
	}
	
	
	
	function display_order_by_fleet($player_name, $fleet, $display_recall_button) {
		$vf = new ViewFunctions();
		$fm = new FleetModel();
		$am = new AllianceModel();
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		
		if ($fleet == 'fleet1') $bcolor = "#822";
		if ($fleet == 'fleet2') $bcolor = "#282";
		if ($fleet == 'fleet3') $bcolor = "#228";
		
	  $conn = db_connect();
		$query = "select * from player_orders
			where player_name='$player_name' 
			  and fleet='$fleet'
			  and return_tick>$current_tick";
		$result = $conn->query($query);
		
		$string = "";
		$string .= " <TR>\n";
		if ($fleet == 'fleet1') $string .= "  <TD class='STD' rowspan='6'>$player_name</TD>\n";
		$string .= "  <TD class='STD' rowspan='2' style='background-color:$bcolor' >$fleet</TD>\n";
		
		if ($result->num_rows == 0) {
			$string .= "   <TD class='STD' style='background-color:$bcolor' colspan='9' rowspan='2'> $fleet is currently not on a mission </TD>\n";
			$string .= " </TR>\n";
			$string .= " <TR>\n";
			$string .= " </TR>\n";
		} else {
			$row = $result->fetch_object();

			$pd = new PlayerData();
			$pd->db_fill($row->target_name);
			$target_alliance = $am->get_alliance_shorthand($row->target_name);

			$string .= "   <TD class='STD' style='background-color:$bcolor' > ";
			$string .= "{$pd->galaxy}:{$pd->star}:{$pd->planet}:{$pd->continent} [$target_alliance] $row->target_name";
			$string .= "   </TD>\n";			
			$string .= "   <TD class='STD' style='background-color:$bcolor' > $row->mission_type ($row->mission_ticks) </TD>\n";
			
			if ($row->arrival_tick > $current_tick) {
				$status_description = "Arriving in " . ($row->arrival_tick - $current_tick) . " tick(s)";
			} else if ($row->depart_tick > $current_tick) {
				$status_description = "Fighting for " . ($row->depart_tick - $current_tick) . " tick(s)";				
			} else if ($row->return_tick > $current_tick) {
				$status_description = "Returning in " . ($row->return_tick - $current_tick) . " tick(s)";				
			}
			$string .= "   <TD class='STD' style='background-color:$bcolor' > $status_description </TD>\n";
			$string .= "   <TD class='STD' style='background-color:$bcolor' > Timing: $row->launch_tick / $row->arrival_tick / $row->depart_tick / $row->return_tick </TD>\n";
			
			if ($display_recall_button) {
				if ($fm->is_move_order($player_name, $fleet) ) {
					$string .= "   <TD class='STD' rowspan='2'> No Recall </TD>\n";
				} else {
					$string .= $vf->make_rowspan_button('RECALL', '808000', 'B0B040', 2, "main_page.php5?view=fleets&action=recall_fleet&fleet=$fleet");
				}
			}
			$string .= " </TR>\n";
			$string .= " <TR>\n";
			$fd = $fm->get_fleet_details($player_name, $fleet, $current_tick);
			$string .= " <TD class='STD' style='background-color:$bcolor' colspan='2' >" . $fd->get_fleet_info() . "</TD>";
			if ($fd->get_total_structures() > 0) {
				$string .= " <TD class='STD' style='background-color:$bcolor' colspan='2' >" . $fd->get_fleet_structures() . "</TD>";
			} else {
				$string .= " <TD class='STD' style='background-color:$bcolor' colspan='2' > No Structures </TD>";				
			}
			$string .= " </TR>\n";
			
		}
		return $string;
	}
	
	function make_individual_timetable_display() {
		$player_name=$_SESSION['player_name'];
		$fm = new FleetModel();

		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();

		// Get fleet timings
		$fleet1_orders = $fm->get_fleet_orders($player_name, "fleet1");
		$fleet2_orders = $fm->get_fleet_orders($player_name, "fleet2");
		$fleet3_orders = $fm->get_fleet_orders($player_name, "fleet3");
		
		// Get first and last ticks of the orders
		if ($fleet1_orders["launch"] != '') $min_tick = $fleet1_orders["launch"];
		if ($fleet2_orders["launch"] != '' && $fleet2_orders["launch"] < $min_tick) $min_tick = $fleet2_orders["launch"];
		if ($fleet3_orders["launch"] != '' && $fleet3_orders["launch"] < $min_tick) $min_tick = $fleet3_orders["launch"];
		if ($min_tick < $current_tick - 20) $min_tick = $current_tick-20;
		
		$max_tick = $fleet1_orders["return"];
		if ($fleet2_orders["return"] > $max_tick) $max_tick = $fleet2_orders["return"];
		if ($fleet3_orders["return"] > $max_tick) $max_tick = $fleet3_orders["return"];
		
		$string = "";
		$string .= "<TABLE class='TIMELINE'>\n";
		$string .= $this->make_timetable_header($current_tick, $min_tick, $max_tick);
		$string .= $this->make_timetable_display($player_name, $min_tick, $max_tick);
		
		$string .= "</TABLE>\n";
		return $string;
	}
	
	function make_timetable_display($player_name, $min_tick, $max_tick) {
		$vf = new ViewFunctions();
		$fm = new FleetModel();

		// Get fleet timings
		$fleet1_orders = $fm->get_fleet_orders($player_name, "fleet1");
		$fleet2_orders = $fm->get_fleet_orders($player_name, "fleet2");
		$fleet3_orders = $fm->get_fleet_orders($player_name, "fleet3");
		
		$string .= $this->make_fleet_timeline_display($player_name, "fleet1", $fleet1_orders, $min_tick, $max_tick); 
		$string .= $this->make_fleet_timeline_display($player_name, "fleet2", $fleet2_orders, $min_tick, $max_tick);
		$string .= $this->make_fleet_timeline_display($player_name, "fleet3", $fleet3_orders, $min_tick, $max_tick);

		return $string;
	}
	
	function make_timetable_header($current_tick, $min_tick, $max_tick) {
		if ($max_tick - $min_tick == 0) return;
		$cw = 600 / ($max_tick - $min_tick);		

		$string .= "<TR>\n";
		$string .= " <TH class='TIMELINE'>Player</TH>\n";
		$string .= " <TH class='TIMELINE'>Fleet</TH>\n";
		$string .= " <TH class='TIMELINE'>Target</TH>\n";

		for ($i=$min_tick; $i<=$max_tick; $i++) {
			if ($i == $current_tick) {
				$string .= " <TH class='TIMELINE' style='color:#FFF;'>" . $i % 100 . "</TH>\n";
			} else {
				$string .= " <TH class='TIMELINE'>" . $i % 100 . "</TH>\n";
			}
		}

		$string .= "</TR>\n";
		$string .= "<TR>\n";
		for ($i=$min_tick; $i<=$max_tick+3; $i++) {
			$string .= " <TH class='TIMELINE' style='width:{$cw}px;color:#FFF;'></TH>\n";	
		}
		$string .= "</TR>\n";
		
		return $string;		
	}
	
	function make_fleet_timeline_display($player_name, $fleet_name, $fleet_orders, $min_tick, $max_tick) {
		if ($fleet_orders["mission"] == "move") return $this->make_move_timeline($player_name, $fleet_name, $fleet_orders, $min_tick, $max_tick);
		else return $this->make_mission_timeline($player_name, $fleet_name, $fleet_orders, $min_tick, $max_tick);
	}
	
	function make_mission_timeline($player_name, $fleet_name, $fleet_orders, $min_tick, $max_tick) {
		if ($fleet_orders["fleet_name"] == 'fleet1') $color='#F88';
		if ($fleet_orders["fleet_name"] == 'fleet2') $color='#8F8';
		if ($fleet_orders["fleet_name"] == 'fleet3') $color='#88F';
		
		if (!$fleet_orders) {
			return $this->make_empty_mission_timeline($player_name, $fleet_name, $min_tick, $max_tick);
		}		
		$out_travel_time = $fleet_orders["arrive"] - $fleet_orders["launch"] - 1;
		$back_travel_time = $fleet_orders["return"] - $fleet_orders["depart"];
		$fight_time = $fleet_orders["depart"] -  $fleet_orders["arrive"];
		
		if ($fleet_orders["mission"] == "attack") {	
			if ($fight_time == 1) $mission_word = "A";
			if ($fight_time == 2) $mission_word = "Att";
			if ($fight_time > 2) $mission_word = "Attack";
			$bcolor="#822";
		}
		if ($fleet_orders["mission"] == "defense") {	
			if ($fight_time == 1) $mission_word = "D";
			if ($fight_time == 2) $mission_word = "Def";
			if ($fight_time > 2) $mission_word = "Defense";
				$bcolor="#282";
		}
		if ($fleet_orders["return"] - $fleet_orders["depart"] > 2) $back_travel_word = 'Travel';
		else $back_travel_word = 'T';
		
// Add something to show size of fleet
		$cm = new CreaturesModel();
		$t = $cm->get_creature_totals_for_player_by_fleet($player_name, $fleet_name);
		// fm function at bottom of this file
		$att = fm($t["att"]); $def = fm($t["def"]); $foc = fm($t["foc"]); $int = fm($t["int"]); $dis = fm($t["dis"]);


		
		$string .= "<TR>\n";
		if ($fleet_orders["fleet_name"] == 'fleet1') $string .= "<TD class='TIMELINE' rowspan='3'>" . substr($player_name, 0 , 10) . "</TD>";
		$string .= "<TD class='TIMELINE' style='color:$color;'>" . $fleet_orders["fleet_name"] . "<BR /><SPAN style='font-size:10px;white-space:nowrap;'>{$att}/{$def}/{$foc}/{$int}/{$dis}</SPAN></TD>";
		$string .= "<TD class='TIMELINE'>" . substr($fleet_orders["target"], 0 , 10) . "</TD>";
		
		// Special case when launch is less than start, this should normally not happen
		if ($fleet_orders["launch"]  < $min_tick) {
			$out_remaining_travel = $fleet_orders["arrive"] - $min_tick;
			if ($out_remaining_travel > 0) $string .= "<TD class='TIMELINE'  style='color:$color;' colspan='$out_remaining_travel'>T</TD>";
			else {
				// The whole travel time is before, lets check the fight
				$fight_remaining_travel = $fleet_orders["depart"] - $min_tick;
				if ($fight_remaining_travel > 0) $string .= "<TD class='TIMELINE'  style='color:$color;background-color:$bcolor;' colspan='$fight_remaining_travel'>$mission_word</TD>";
				else {
					$back_remaining_travel = $fleet_orders["return"] - $min_tick;
					if ($back_remaining_travel > 0) $string .= "<TD class='TIMELINE'  style='color:$color;' colspan='$back_remaining_travel'>T</TD>";
				}
			}
		}
		
		for ($i=$min_tick; $i<=$max_tick; $i++) {
			if ($i < $fleet_orders["launch"])$string .= "<TH class='TIMELINE'>&nbsp;</TH>";
			else if ($i == $fleet_orders["launch"]) $string .= "<TD class='TIMELINE'  style='color:$color;'>L</TD>";
			else if ($i == $fleet_orders["launch"] + 1) $string .= "<TD class='TIMELINE'  style='color:$color;' colspan='$out_travel_time'>Travel</TD>";
			else if ($i == $fleet_orders["arrive"] && $fight_time > 0) $string .= "<TD class='TIMELINE' style='color:$color;background-color:$bcolor;' colspan='$fight_time'>$mission_word</TD>";
			else if ($i == $fleet_orders["depart"]) $string .= "<TD class='TIMELINE' style='color:$color;' colspan='$back_travel_time'>$back_travel_word</TD>";
			else if ($i == $fleet_orders["return"]) $string .= "<TD class='TIMELINE' style='color:$color;'>H</TD>";
		}
		$string .= "</TR>\n";
		
		return $string;
	}
	
	function make_move_timeline($player_name, $fleet_name, $fleet_orders, $min_tick, $max_tick) {
		if ($fleet_orders["fleet_name"] == 'fleet1') $color='#F88';
		if ($fleet_orders["fleet_name"] == 'fleet2') $color='#8F8';
		if ($fleet_orders["fleet_name"] == 'fleet3') $color='#88F';

		$string .= "<TR>\n";
		if ($fleet_orders["fleet_name"] == 'fleet1') $string .= "<TD class='TIMELINE' rowspan='3'>$player_name</TD>";
		$string .= "<TD class='TIMELINE' style='color:$color'>" . $fleet_orders["fleet_name"] . "</TD>";
		$string .= "<TD class='TIMELINE' style='color:$color'>&nbsp;</TD>";

		if ($min_tick > $fleet_orders["launch"]) $out_travel_time = $fleet_orders["return"] - $min_tick;
		else $out_travel_time = $fleet_orders["return"] - $fleet_orders["launch"] - 1;
		
		if ($out_travel_time > 4) $move_word = 'Moving';
		else $move_word = 'M';
		
		for ($i=$min_tick; $i<=$max_tick; $i++) {
			if ($i < $fleet_orders["launch"])$string .= "<TH class='TIMELINE'>&nbsp;</TH>";
			else if ($i == $fleet_orders["launch"]) $string .= "<TD class='TIMELINE' style='color:$color'>L</TD>";
			else if ($i == $fleet_orders["launch"] + 1) $string .= "<TD class='TIMELINE' colspan='$out_travel_time' style='color:$color'>$move_word</TD>";
			else if ($i == $fleet_orders["return"]) $string .= "<TD class='TIMELINE' style='color:$color'>H</TD>";
		}
	
		$string .= "</TR>\n";
		
		return $string;
		
	}
	
	function make_empty_mission_timeline($player_name, $fleet_name, $min_tick, $max_tick) {
		if ($fleet_name == 'fleet1') $color='#F88';
		if ($fleet_name == 'fleet2') $color='#8F8';
		if ($fleet_name == 'fleet3') $color='#88F';
		
		$duration = ($max_tick - $min_tick) + 1;
		$string = "";
		$string .= "<TR>\n";
		if ($fleet_name == 'fleet1') $string .= "<TD class='TIMELINE' rowspan='3'>$player_name</TD>";
		$string .= "<TD class='TIMELINE' style='color:$color;'>" . $fleet_name . "</TD>";
		$string .= "<TD class='TIMELINE' style='color:$color;'>&nbsp;</TD>";
		$string .= "<TH class='TIMELINE' style='color:$color;background-color:#000' colspan='$duration'>&nbsp</TH>";
		
		$string .= "</TR>\n";
		return $string;
	}
}

function fm($val) {
	if ($val > 1000000000) return intval($val / 1000000000) . 'B';
	if ($val > 1000000) return intval($val / 1000000) . 'M';
	if ($val > 1000) return intval($val / 1000) . 'k';
	return $val;
}
?>