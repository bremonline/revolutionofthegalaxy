<?php
	require_once("db_fns.php5");
	require_once("view_fns.php5");
	require_once("creatures_model.php5");
	require_once("scans_model.php5");
	require_once("forts_model.php5");
	require_once("bombs_model.php5");
	require_once("pulses_model.php5");
	require_once("items_model.php5");
	require_once("battle_calculator.php5");
	require_once("development_model.php5");
	require_once("bombs_traps_calculator.php5");
	require_once("pulse_calculator.php5");
	require_once("milestone_model.php5");
	require_once("news_model.php5");
	require_once('email_helper.php5');
	
class GameModel {
	var $base_mineral = 754;
	var $base_organic = 752;
	var $base_energy = 755;
	
	function reset_game() {

		$conn = db_connect();
		
	  $query = "truncate table chat_last_online"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table chat_message"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table chat_player"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table click_history"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table conversation_message"; $result = $conn->query($query); if (!$result) show_error("Error: $query");

	  $query = "truncate table conversation_topic"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table last_seen"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table login_history"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table milestone"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table monitor"; $result = $conn->query($query); if (!$result) show_error("Error: $query");

	  $query = "truncate table news"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table player_build"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table player_creatures"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table player_items"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table player_orders"; $result = $conn->query($query); if (!$result) show_error("Error: $query");

	  $query = "truncate table player_scans"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table pulse_use"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table scan_results"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
	  $query = "truncate table shout"; $result = $conn->query($query); if (!$result) show_error("Error: $query");
		
		$new_name=$_REQUEST['new_name'];
		$unassigned=$_REQUEST['unassigned'];
		$base=$_REQUEST['base'];
		$tpd=$_REQUEST['tpd'];
		

	  $query = "update player set unassigned=300, extractor=0, genetic_lab=0, powerplant=0, factory=0, 
	  	mineral=1000000, organic=1000000, energy=1000000, score=0,
	  	status='inactive'"; 
	  $result = $conn->query($query); 
	  if (!$result) show_error("Error: $query");
	  
	  $query = "update game set gamename='$new_name', start_time=NOW(), current_tick=0, status='Pre-Game',
			starting_mineral=$base, starting_organic=$base, starting_energy=$base, starting_structures=$unassigned,
			number_ticks_per_day=$tpd"; 
	  $result = $conn->query($query); 
	  if (!$result) show_error("Error: $query");


		
		show_warning("Game completely reset");
		unset($_SESSION['player_name']);
		
	}
	
	function reset_game_parameters() {
		$mps=$_REQUEST['mps'];$ops=$_REQUEST['ops'];$eps=$_REQUEST['eps'];
		$bm=$_REQUEST['bm'];$bo=$_REQUEST['bo'];$be=$_REQUEST['be'];
		$sm=$_REQUEST['sm'];$so=$_REQUEST['so'];$se=$_REQUEST['se'];
		$sf=$_REQUEST['ss'];$ipf=$_REQUEST['ips'];$ntpd=$_REQUEST['ntpd'];
		
		$conn = db_connect();
	  $query = "update game set current_tick=1, start_time=NOW(), last_updated_time=NOW(),
	  	mineral_per_structure=$mps, organic_per_structure=$ops, energy_per_structure=$eps, 
	  	base_mineral=$bm, base_organic=$bo, base_energy=$be,
	  	starting_mineral=$sm, starting_organic=$so, starting_energy=$se,
	  	starting_structures=$ss, increase_per_structure=$ips, number_ticks_per_day=$ntpd
	  	";
	  $result = $conn->query($query);
		if (!$result) show_error("Could not update game: <br /> $query");
	}
	
	function advance_multiple_ticks() {
  	$num_ticks=$_REQUEST['num_ticks'];
  	if ($num_ticks > 100) $num_ticks=100;
		show_info("Advancing ticks: $num_ticks");
  	$num_ticks=$num_ticks;
		for ($i=0; $i<$num_ticks;$i++) {
		 	$this->advance_single_tick();
		}
	}


	function get_current_tick() {
		$conn = db_connect();	
	  $query = "select current_tick from game";
	  $result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->current_tick;
	}

	function check_tick_for_advancement() {
		// Hardcode 5 minute ticks for now...
		$conn = db_connect();
		$query = "select DATE_FORMAT(last_updated_time, \"%Y%m%d%H%i%s\") as lu, DATE_FORMAT(now(), \"%Y%m%d%H%i%s\") as nw from game";
		$result = $conn->query($query);
		if (!$result) alert("Could not get tick information");
		$row = $result->fetch_object();
		
		// Find an even interval for time sets
		$tick_start = floor($row->lu/500) * 500;
		echo "<TABLE>\n";
		echo "<TR><TD>Last Updated is:</TD><TD>$row->lu</TD></TR>\n";
		echo "<TR><TD>Tick Start is:</TD><TD>$tick_start</TD></TR>\n";
		echo "<TR><TD>Now is:</TD><TD>$row->nw</TD></TR>\n";
		echo "<TR><TD>Last Updated is:</TD><TD>$row->lu</TD></TR>\n";
		$check_time = $row->nw - $tick_start;
		echo "<TR><TD>Check Time:</TD><TD>$check_time</TD></TR>\n";
		echo "<TABLE>\n";
		return $check_time; // 500 is 5 minutes, 10000 is 1 hour
	}

	function advance_single_tick() {
		show_info("Advancing to tick: " . ($this->get_current_tick()+1) );
		show_info("Starting Tick Advance: " . get_db_time() );
				
		$this->set_game_status("Advancing Tick");
		$this->increment_tick();
		show_info("Tick Incremented: " . get_db_time() );
		$this->advance_build();
		show_info("Build Advanced: " . get_db_time() );
		$this->complete_research();
		show_info("Research Completed: " . get_db_time() );
		$this->complete_development();
		show_info("Development Completed: " . get_db_time() );
		$this->complete_creatures();
		show_info("Creatures Completed: " . get_db_time() );
		$this->complete_scans();
		show_info("Scans Completed: " . get_db_time() );
		$this->complete_forts();
		show_info("Forts Completed: " . get_db_time() );
		$this->complete_bombs();
		show_info("Bombs Completed: " . get_db_time() );
		$this->complete_pulses();
		show_info("Pulses Completed: " . get_db_time() );
		$this->return_fleets();
		show_info("Fleets Returned: " . get_db_time() );
		$this->continuous_survey();
		show_info("Continuous Survey: " . get_db_time() );

		$this->add_resources();
		show_info("Resources added: " . get_db_time() );
		$btc = new BombTrapsCalculator();
		$btc->bombs_traps_calculator($this->get_current_tick());
		show_info("Bombs and Traps Calculated: " . get_db_time() );
		$pc = new PulseCalculator();
		$pc->cross_shield($this->get_current_tick());
		show_info("Crossing Shield: " . get_db_time() );
		$bc = new BattleCalculator();
		$bc->battle_calculator($this->get_current_tick());
		show_info("Battles Calculated: " . get_db_time() );

		$this->compute_all_scores();
		show_info("Scores Computed: " . get_db_time() );
		$this->compute_alliance_scores();
		show_info("Alliance Scores Computed: " . get_db_time() );
		$this->compute_victory_conditions();
		show_info("Victory Conditions Computed: " . get_db_time() );



		$this->set_game_status("Active");
		show_info("Finishing Tick Advance: " . get_db_time() );

	}	

	function advance_tick_automated() {
		echo "Advancing to tick: " . ($this->get_current_tick()+1)  . "\n";
		echo "Starting Tick Advance: " . get_db_time() . "\n";
				
		$this->set_game_status("Advancing Tick");
		$this->increment_tick();
		echo "Tick Incremented: " . get_db_time() . "\n" ;
		$this->advance_build();
		echo "Build Advanced: " . get_db_time() . "\n";
		$this->complete_research();
		echo "Research Completed: " . get_db_time() . "\n";
		$this->complete_development();
		echo "Development Completed: " . get_db_time() . "\n";
		$this->complete_creatures();
		echo "Creatures Completed: " . get_db_time() . "\n";
		$this->complete_scans();
		echo "Scans Completed: " . get_db_time() . "\n";
		$this->complete_forts();
		echo "Forts Completed: " . get_db_time() . "\n";
		$this->complete_bombs();
		echo "Bombs Completed: " . get_db_time() . "\n";
		$this->complete_pulses();
		echo "Pulses Completed: " . get_db_time() . "\n";
		$this->return_fleets();
		echo "Fleets Returned: " . get_db_time() . "\n";
		$this->continuous_survey();
		echo "Continuous Survey: " . get_db_time() . "\n";

		$this->add_resources();
		echo "Resources added: " . get_db_time() . "\n";
		$btc = new BombTrapsCalculator();
		$btc->bombs_traps_calculator($this->get_current_tick());
		echo "Bombs and Traps Calculated: " . get_db_time() . "\n" ;
		$pc = new PulseCalculator();
		$pc->cross_shield($this->get_current_tick());
		echo "Crossing Shield: " . get_db_time() . "\n" ;
		$bc = new BattleCalculator();
		$bc->battle_calculator($this->get_current_tick());
		echo "Battles Calculated: " . get_db_time() . "\n";

		$this->compute_all_scores();
		echo "Scores Computed: " . get_db_time() . "\n";
		$this->compute_alliance_scores();
		echo "Alliance Scores Computed: " . get_db_time() . "\n" ;
		$this->compute_victory_conditions();
		echo "Victory Conditions Computed: " . get_db_time() . "\n" ;



		$this->set_game_status("Active");
		echo "Finishing Tick Advance: " . get_db_time() . "\n";

	}	



	function increment_tick() {
		$conn = db_connect();	
	  $query = "update game set current_tick=current_tick+1, last_updated_time=NOW()";
	  $result = $conn->query($query);
	}	
	
	function advance_build() {
		$conn = db_connect();	
	  $query = "update player_build set ticks_remaining=ticks_remaining - 1 where ticks_remaining > 0";
	  $result = $conn->query($query);
		
	}
	
	function complete_research() {
		$conn = db_connect();	
		
		$email = new EmailHelper();
		$nm = new NewsModel();
		$ct = $this->get_current_tick();
		
	  $query = "select * from player_build where
	  			build_type='research' and status='researching' and ticks_remaining=0";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
				$subject = "You have completed the research of $row->build_item";
				$text = "You have completed the research of $row->build_item on tick $ct";
				$nm->add_new_news($row->player_name, "player", "misc", $subject, $text);
				$email->send_complete_email($row->player_name, $row->build_item, 'email_on_completed_research', 0, $ct);		
		}		
		
	  $query = "update player_build set status='completed' where
	  			build_type='research' and status='researching' and ticks_remaining=0";
	  $result = $conn->query($query);
	}

	function complete_development() {
		$conn = db_connect();	
		
		$email = new EmailHelper();
		$nm = new NewsModel();
		$ct = $this->get_current_tick();
		
	  $query = "select * from player_build where
	  			build_type='development' and status='developing' and ticks_remaining=0";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
				$subject = "You have completed the development of $row->build_item";
				$text = "You have completed the development of $row->build_item on tick $ct";
				$nm->add_new_news($row->player_name, "player", "misc", $subject, $text);
				$email->send_complete_email($row->player_name, $row->build_item, 'email_on_completed_development', 0, $ct);		
		}		
		
		
	  $query = "update player_build set status='completed' where
	  			build_type='development' and status='developing' and ticks_remaining=0";
	  $result = $conn->query($query);
	}
	
	function complete_creatures() {
		$email = new EmailHelper();
		$ct = $this->get_current_tick();

		$conn = db_connect();	
		$query = "select * from player_build where
	  			build_type='creature' and status='building' and ticks_remaining=0 ";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$cm = new CreaturesModel();
			$cm->add_creatures_to_player($row->player_name, $row->build_item, $row->number, 'home');
			$email->send_complete_email($row->player_name, $row->build_item, 'email_on_completed_creatures', $row->number, $ct);		
			}	  

	  $query = "update player_build set status='completed' where
	  			build_type='creature' and status='building' and ticks_remaining=0";
	  $result = $conn->query($query);
	}

	function complete_scans() {
		$email = new EmailHelper();
		$ct = $this->get_current_tick();

		$conn = db_connect();	
		$query = "select * from player_build where
	  			build_type='scan' and status='building' and ticks_remaining=0 ";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$sm = new ScansModel();
			$sm->add_scans_to_player($row->player_name, $row->build_item, $row->number);
			$email->send_complete_email($row->player_name, $row->build_item, 'email_on_completed_scans', $row->number, $ct);		
		}	  

	  $query = "update player_build set status='completed' where
	  			build_type='scan' and status='building' and ticks_remaining=0";
	  $result = $conn->query($query);
	}

	function complete_forts() {
		$email = new EmailHelper();
		$ct = $this->get_current_tick();

		$conn = db_connect();	
		$query = "select * from player_build where
	  			build_type='fort' and status='building' and ticks_remaining=0 ";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$fm = new FortsModel();
			$fm->add_forts_to_player($row->player_name, $row->number);
			$email->send_complete_email($row->player_name, $row->build_item, 'email_on_completed_bombs', $row->number, $ct);		
		}	  

	  $query = "update player_build set status='completed' where
	  			build_type='fort' and status='building' and ticks_remaining=0";
	  $result = $conn->query($query);
	}

	function complete_bombs() {
		$email = new EmailHelper();
		$ct = $this->get_current_tick();

		$conn = db_connect();	
		$query = "select * from player_build where
	  			build_type='bomb' and status='building' and ticks_remaining=0 ";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$fm = new BombsModel();
			$fm->add_bombs_to_player($row->player_name, $row->build_item, $row->number);
			$email->send_complete_email($row->player_name, $row->build_item, 'email_on_completed_bombs', $row->number, $ct);		
		}	  

	  $query = "update player_build set status='completed' where
	  			build_type='bomb' and status='building' and ticks_remaining=0";
	  $result = $conn->query($query);
	}
	
	function complete_pulses() {
		$email = new EmailHelper();
		$ct = $this->get_current_tick();

		$conn = db_connect();	
		$query = "select * from player_build where
	  			build_type='pulse' and status='building' and ticks_remaining=0 ";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$fm = new PulsesModel();
			$fm->add_pulses_to_player($row->player_name, $row->build_item, $row->number);
			$email->send_complete_email($row->player_name, $row->build_item, 'email_on_completed_pulses', $row->number, $ct);		
		}	  

	  $query = "update player_build set status='completed' where
	  			build_type='pulse' and status='building' and ticks_remaining=0";
	  $result = $conn->query($query);
	}

	function add_resources () {
		$base_mineral = $this->get_game_parameter("base_mineral");
		$base_organic = $this->get_game_parameter("base_organic");
		$base_energy = $this->get_game_parameter("base_energy");
		
		$standard_mineral_per_structure = $this->get_game_parameter("mineral_per_structure");
		$standard_organic_per_structure = $this->get_game_parameter("organic_per_structure");
		$standard_energy_per_structure = $this->get_game_parameter("energy_per_structure");

		$number_victors_mineral = $this->count_number_people_with_victory_condition("Mineral Victory Condition");
		$number_victors_organic = $this->count_number_people_with_victory_condition("Organic Victory Condition");
		$number_victors_energy = $this->count_number_people_with_victory_condition("Energy Victory Condition");

		$mineral_per_structure = $this->calculate_resources_per_tick($standard_mineral_per_structure, $number_victors_mineral);
		$organic_per_structure = $this->calculate_resources_per_tick($standard_organic_per_structure, $number_victors_organic);
		$energy_per_structure = $this->calculate_resources_per_tick($standard_energy_per_structure, $number_victors_energy);

		$total_number_of_victors = $number_victors_mineral + $number_victors_organic + $number_victors_energy;

		// Under most circumstances there will be no victory conditions, so do not slow down the query
		if ($total_number_of_victors == 0) {
			$conn = db_connect();	
			$query = "select * from player where status='active'";
		  $result = $conn->query($query);
			for ($count=0; $row = $result->fetch_object(); $count++) {
				$this->update_all_resources($row->name, $row->extractor, $row->genetic_lab, $row->powerplant, 
					$base_mineral, $base_organic, $base_energy,
					$mineral_per_structure, $organic_per_structure, $energy_per_structure);
			}
		} else {
			// There is at least one person with the victory condition
			$conn = db_connect();	
			$query = "select * from player where status='active'";
		  $result = $conn->query($query);
			for ($count=0; $row = $result->fetch_object(); $count++) {
				// Most of the time we will use the reduced per_structure, unless this player has the victory, then use the full.
				$real_mineral_per_structure = $mineral_per_structure;
				$real_organic_per_structure = $organic_per_structure;
				$real_energy_per_structure = $energy_per_structure;
				
				// Override when they do have it
				if ($this->does_player_have_victory_condition($row->name, "Mineral Victory Condition", $row->extractor) ) $real_mineral_per_structure = $standard_mineral_per_structure;
				if ($this->does_player_have_victory_condition($row->name, "Organic Victory Condition", $row->genetic_lab) ) $real_organic_per_structure = $standard_organic_per_structure;
				if ($this->does_player_have_victory_condition($row->name, "Energy Victory Condition", $row->powerplant) ) $real_energy_per_structure = $standard_energy_per_structure;
				
				$this->update_all_resources($row->name, $row->extractor, $row->genetic_lab, $row->powerplant, 
					$base_mineral, $base_organic, $base_energy,
					$real_mineral_per_structure, $real_organic_per_structure, $real_energy_per_structure);
			}
		}
	}
	
	function update_all_resources($player_name, $number_extractors, $number_genetic_lab, $number_powerplant, 
				$base_mineral, $base_organic, $base_energy,
				$mineral_per_structure, $organic_per_structure, $energy_per_structure) {

		$new_mineral = ($number_extractors * $mineral_per_structure) + $base_mineral;
		$new_organic = ($number_genetic_lab * $organic_per_structure) + $base_organic;
		$new_energy = ($number_powerplant * $energy_per_structure) + $base_energy;
		
		$conn = db_connect();	
		$query = "update player set mineral=mineral + $new_mineral, organic=organic + $new_organic, energy=energy+$new_energy
			where name='$player_name'";
	  $result = $conn->query($query);
	}
	
	function return_fleets() {
		$pd = new PlayerData();
		$current_tick = $this->get_current_tick();
		$conn = db_connect();	
		$query = "select * from player_orders where return_tick=$current_tick";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$pd->add_structures_to_player($row->player_name, $row->unassigned, $row->extractors, $row->genetic_labs, $row->powerplants, $row->factories);
		}		
	}
	
	function compute_all_scores() {

		$pd = new PlayerData();
		$conn = db_connect();	
		// Added the active part for vacation mode...
//		$query = "select name from player where status='active'";
		$query = "select name from player where status != 'inactive'";
	  $result = $conn->query($query);

		$cm = new CreaturesModel();
		$sm = new ScansModel();
		$im = new ItemsModel();
		$creature_values = $cm->get_creature_values();
		$scan_values = $sm->get_scan_values();
		$item_values = $im->get_items_values();
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$pd->compute_score_for_player($row->name, $creature_values, $scan_values, $item_values );
		}
		
	}
	

	function compute_alliance_scores() {

		$pd = new PlayerData();
		$conn = db_connect();	
		$query = "select * from alliance";
	  $result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$alliance_score = $this->calculate_score_of_alliance($row->alliance_name);
			$membership = $this->calculate_membership_of_alliance($row->alliance_name);
			$structures = $this->calculate_structures_of_alliance($row->alliance_name);
			$this->set_alliance_stats($row->alliance_name, $alliance_score, $membership, $structures);
		}
	}
	
	function calculate_score_of_alliance($alliance_name) {
		$conn = db_connect();	
		$query = "select sum(p.score) as score from player_alliance pa, player p where pa.player_name=p.name and pa.alliance='$alliance_name'";
	  $result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->score;
	}

	function calculate_membership_of_alliance($alliance_name) {
		$conn = db_connect();	
		$query = "select count(*) as count from player_alliance pa, player p where pa.player_name=p.name and pa.alliance='$alliance_name'";
	  $result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->count;
	}

	function calculate_structures_of_alliance($alliance_name) {
		$conn = db_connect();	
		$query = "select sum(unassigned) + sum(extractor) + sum(genetic_lab) + sum(powerplant) + sum(factory) as total 
				from player_alliance pa, player p where pa.player_name=p.name and pa.alliance='$alliance_name'";
	  $result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->total;
	}

	function set_alliance_stats($alliance, $alliance_score, $membership, $structures) {
		$conn = db_connect();	
		$query = "update alliance set score=$alliance_score, members=$membership, total_structures=$structures where alliance_name='$alliance'";
	  $result = $conn->query($query);
		return;
	}
	
	function set_game_status($status) {
		$conn = db_connect();	
	  $query = "update game set status='$status'";
	  $result = $conn->query($query);
	}	

	function get_game_parameter($parameter) {
		$conn = db_connect();	
	  $query = "select $parameter as output from game";
	  $result = $conn->query($query);
	  if (!$result) {
	  	show_error("Failed Query: $query");
	  	return 0;
	  } else {
	  	$row = $result->fetch_object();
			return $row->output;
		}
	}
	
	function continuous_survey() {
		$pd = new PlayerData();
		srand(time());

		$conn = db_connect();	
		$query = "select player_name from player_build where build_item = 'Continuous Surveying' and status='completed'" ;
	  $result = $conn->query($query);
		for ($count = 0; $row = $result->fetch_object(); $count++) {
			$a = mt_rand() % 5;
			show_warning("roll: $a");
			if ($a == 0) $pd->add_structures_to_player($row->player_name, 1, 0, 0, 0, 0);
			if ($a == 1) $pd->add_structures_to_player($row->player_name, 0, 1, 0, 0, 0);
			if ($a == 2) $pd->add_structures_to_player($row->player_name, 0, 0, 1, 0, 0);
			if ($a == 3) $pd->add_structures_to_player($row->player_name, 0, 0, 0, 1, 0);
			if ($a == 4) $pd->add_structures_to_player($row->player_name, 0, 0, 0, 0, 1);
		}
	}
	
	function compute_victory_conditions() {
		$this->compute_single_victory_condition("Mineral Victory Condition", "extractor", 1000);
		$this->compute_single_victory_condition("Organic Victory Condition", "genetic_lab", 500);
		$this->compute_single_victory_condition("Energy Victory Condition", "powerplant", 250);
		
		// Now to notify the world when someone gets close
		$this->check_milestones();
	}
	
	function compute_single_victory_condition($victory_type, $structure_type, $amount) {
		$conn = db_connect();	
		$query = "SELECT p.name as name, p.score as score FROM player_build pb, player p 
			WHERE pb.player_name = p.name
				AND pb.build_item = '$victory_type' 
				AND pb.status='completed'
				AND p.{$structure_type} > $amount
			" ;
	  $result = $conn->query($query);
		for ($count = 0; $row = $result->fetch_object(); $count++) {
//			$nm = new NewsModel();
//			$nm->add_new_news('judal', 'player', 'misc', "$row->name has achieved $victory_type", "Score BEFORE doubling is: $row->score" );

			$this->double_score($row->name);
		}
	}
	
	function double_score($player_name) {
		$conn = db_connect();	
		$query = "UPDATE player set score = score * 2 where name='$player_name'" ;
	  $result = $conn->query($query);
	}
	
	function check_milestones() {
		$this->check_resource_milestone("extractor", 100);
		$this->check_resource_milestone("genetic_lab", 100);
		$this->check_resource_milestone("powerplant", 100);

		$this->check_resource_milestone("extractor", 200);
		$this->check_resource_milestone("genetic_lab", 200);
		$this->check_resource_milestone("powerplant", 200);

		$this->check_resource_milestone("extractor", 500);
		$this->check_resource_milestone("genetic_lab", 500);
		$this->check_resource_milestone("powerplant", 500);

		$this->check_resource_milestone("extractor", 1000);
		$this->check_resource_milestone("genetic_lab", 1000);
		$this->check_resource_milestone("powerplant", 1000);

		$this->check_resource_milestone("extractor", 2000);
		$this->check_resource_milestone("genetic_lab", 2000);
		$this->check_resource_milestone("powerplant", 2000);

		$this->check_resource_milestone("extractor", 5000);
		$this->check_resource_milestone("genetic_lab", 5000);
		$this->check_resource_milestone("powerplant", 5000);

		$this->check_development_milestone("Mineral Victory Condition", "started");
		$this->check_development_milestone("Organic Victory Condition", "started");
		$this->check_development_milestone("Energy Victory Condition", "started");

		$this->check_development_milestone("Mineral Victory Condition", "completed");
		$this->check_development_milestone("Organic Victory Condition", "completed");
		$this->check_development_milestone("Energy Victory Condition", "completed");
	}
	
	function check_resource_milestone($milestone_name, $amount) {
		$nm = new NewsModel();
		$mm = new MilestoneModel();
		$current_tick = $this->get_current_tick();

		
		// First find out who qualifies
		$conn = db_connect();	
		$query = "SELECT name from player where $milestone_name >= $amount" ;
	  $result = $conn->query($query);
		for ($count = 0; $row = $result->fetch_object(); $count++) {
			$has_milestone = $mm->check_milestone($row->name, 'resource', $milestone_name, $amount);
			if ($has_milestone == false) {
				$subject = "$row->name has achieved an important resource milestone.";
				$text = "$row->name has just passed $amount of {$milestone_name}s";
				$nm->add_new_news($row->name, "universe", "misc", $subject, $text);
				$mm->insert_new_milestone($row->name, $current_tick, 'resource', $milestone_name, $amount);
			} 
		}
	}	
	
	function check_development_milestone($development_name, $status) {
		$nm = new NewsModel();
		$mm = new MilestoneModel();
		$current_tick = $this->get_current_tick();

		// First find out who qualifies
		$conn = db_connect();	
		if ($status == "completed") $query = "SELECT * from player_build where build_type='development' and build_item='$development_name' and status='completed'";
		if ($status == "started") $query = "SELECT * from player_build where build_type='development' and build_item='$development_name' and status='developing'";
	  $result = $conn->query($query);
		for ($count = 0; $row = $result->fetch_object(); $count++) {
			$has_milestone = $mm->check_milestone($row->player_name, "development $status", $development_name, 1);
			if ($has_milestone == false) {
				$subject = "$row->player_name has achieved an important development milestone.";
				$text = "$row->player_name has $status $development_name";
				$nm->add_new_news($row->player_name, "universe", "misc", $subject, $text);
				$mm->insert_new_milestone($row->player_name, $current_tick, "development $status", $development_name, 1);
			} 
		}
	}

	function calculate_resources_per_tick($starting_resources_per_tick, $number_victors) {
		$rpt = $starting_resources_per_tick - (50 * $number_victors);
		if ($rpt < 0) $rpt = 0;
		return $rpt;
	}

	function count_number_people_with_victory_condition($victory_condition) {
		$conn = db_connect();	
		// Have to make three seperate queries because the structure counts change now
		if ($victory_condition == 'Mineral Victory Condition') {
			$query = "SELECT count(p.name) as count FROM player_build pb, player p 
				WHERE pb.player_name = p.name
				AND pb.build_item = '$victory_condition' 
				AND pb.status='completed'
				AND p.extractor > 5000
				AND p.status = 'active'
			";
		} else if ($victory_condition == 'Organic Victory Condition') {
			$query = "SELECT count(p.name) as count FROM player_build pb, player p 
				WHERE pb.player_name = p.name
				AND pb.build_item = '$victory_condition' 
				AND pb.status='completed'
				AND p.genetic_lab > 2500
				AND p.status = 'active'
			";
		} else if ($victory_condition == 'Energy Victory Condition') {
			$query = "SELECT count(p.name) as count FROM player_build pb, player p 
				WHERE pb.player_name = p.name
				AND pb.build_item = '$victory_condition' 
				AND pb.status='completed'
				AND p.powerplant > 1250
				AND p.status = 'active'
			";
		} else return 0; // Do not know what type of victory condition, this should NEVER happen

	  $result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->count;
	}
	
	function does_player_have_victory_condition($player_name, $victory_condition, $key_structure_count) {
		$dm = new DevelopmentModel();
		if ($dm->does_player_know_development($player_name, $victory_condition) == false) return false;
		if ($key_structure_count <= 5000) return false;
		
		return true;
	}
	
	function archive_click() {
  	$player_name = $_SESSION["player_name"];
		$click_info = $_SERVER["REQUEST_URI"];
		$ip = $_SERVER['REMOTE_ADDR'];
		
		$conn = db_connect();	
		$query = "INSERT INTO click_history VALUES ('$player_name', NOW(), '$ip', '$click_info')";
	  $result = $conn->query($query);
//		show_info("$query");
	}
}
?>