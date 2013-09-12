<?php 
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('game_model.php5'); 
	require_once('scans_model.php5'); 
	require_once('development_model.php5'); 
	require_once('creatures_model.php5'); 
	require_once('fleet_model.php5'); 
	require_once('news_model.php5'); 
	require_once('forts_model.php5'); 
	require_once('bombs_model.php5'); 
	require_once('player_data.php5'); 

class ScansController { 
	function create_scans() {
		
		// Unfortunately this method ties the DB to the code.  Oh well.
		
 		$player_name=$_SESSION['player_name'];
		$survey_tuner = floor($_REQUEST["survey_tuner"]);
		$scan_amplifier = floor($_REQUEST["scan_amplifier"]);
		$noise_generator = floor($_REQUEST["noise_generator"]);
		$scan_sensor = floor($_REQUEST["scan_sensor"]);
		$scan_filter = floor($_REQUEST["scan_filter"]);
		$site_scan = floor($_REQUEST["site_scan"]);
		$r_and_d_scan = floor($_REQUEST["r_and_d_scan"]);
		$continent_scan = floor($_REQUEST["continent_scan"]);
		$creature_scan = floor($_REQUEST["creature_scan"]);
		$military_scan = floor($_REQUEST["military_scan"]);
		$planetary_scan = floor($_REQUEST["planetary_scan"]);
		$news_scan = floor($_REQUEST["news_scan"]);
		$full_scan = floor($_REQUEST["full_scan"]);
		$launch_monitor = floor($_REQUEST["launch_monitor"]);
		$structure_monitor = floor($_REQUEST["structure_monitor"]);

		
		// Determine cost of all scans
		$sm = new ScansModel();
		$scan_costs = $sm->get_scan_costs_and_ticks();
		
		$total_mineral_cost=0;
		$total_energy_cost=0;
		
		if ($survey_tuner > 0) $total_mineral_cost += ($survey_tuner * $scan_costs["survey_tuner"]["mineral"]);
		if ($survey_tuner > 0) $total_energy_cost += ($survey_tuner * $scan_costs["survey_tuner"]["energy"]);
		
		if ($scan_amplifier > 0) $total_mineral_cost += ($scan_amplifier * $scan_costs["scan_amplifier"]["mineral"]);
		if ($scan_amplifier > 0) $total_energy_cost += ($scan_amplifier * $scan_costs["scan_amplifier"]["energy"]);
		
		if ($noise_generator > 0) $total_mineral_cost += ($noise_generator * $scan_costs["noise_generator"]["mineral"]);
		if ($noise_generator > 0) $total_energy_cost += ($noise_generator * $scan_costs["noise_generator"]["energy"]);
		
		if ($scan_sensor > 0) $total_mineral_cost += ($scan_sensor * $scan_costs["scan_sensor"]["mineral"]);
		if ($scan_sensor > 0) $total_energy_cost += ($scan_sensor * $scan_costs["scan_sensor"]["energy"]);
		
		if ($scan_filter > 0) $total_mineral_cost += ($scan_filter * $scan_costs["scan_filter"]["mineral"]);
		if ($scan_filter > 0) $total_energy_cost += ($scan_filter * $scan_costs["scan_filter"]["energy"]);
		

		if ($site_scan > 0) $total_mineral_cost += ($site_scan * $scan_costs["site_scan"]["mineral"]);
		if ($site_scan > 0) $total_energy_cost += ($site_scan * $scan_costs["site_scan"]["energy"]);
		
		if ($r_and_d_scan > 0) $total_mineral_cost += ($r_and_d_scan * $scan_costs["r_and_d_scan"]["mineral"]);
		if ($r_and_d_scan > 0) $total_energy_cost += ($r_and_d_scan * $scan_costs["r_and_d_scan"]["energy"]);
		
		if ($continent_scan > 0) $total_mineral_cost += ($continent_scan * $scan_costs["continent_scan"]["mineral"]);
		if ($continent_scan > 0) $total_energy_cost += ($continent_scan * $scan_costs["continent_scan"]["energy"]);
		
		if ($creature_scan > 0) $total_mineral_cost += ($creature_scan * $scan_costs["creature_scan"]["mineral"]);
		if ($creature_scan > 0) $total_energy_cost += ($creature_scan * $scan_costs["creature_scan"]["energy"]);
		
		if ($military_scan > 0) $total_mineral_cost += ($military_scan * $scan_costs["military_scan"]["mineral"]);
		if ($military_scan > 0) $total_energy_cost += ($military_scan * $scan_costs["military_scan"]["energy"]);
		

		if ($planetary_scan > 0) $total_mineral_cost += ($planetary_scan * $scan_costs["planetary_scan"]["mineral"]);
		if ($planetary_scan > 0) $total_energy_cost += ($planetary_scan * $scan_costs["planetary_scan"]["energy"]);
		
		if ($news_scan > 0) $total_mineral_cost += ($news_scan * $scan_costs["news_scan"]["mineral"]);
		if ($news_scan > 0) $total_energy_cost += ($news_scan * $scan_costs["news_scan"]["energy"]);
		
		if ($full_scan > 0) $total_mineral_cost += ($full_scan * $scan_costs["full_scan"]["mineral"]);
		if ($full_scan > 0) $total_energy_cost += ($full_scan * $scan_costs["full_scan"]["energy"]);
		
		if ($launch_monitor > 0) $total_mineral_cost += ($launch_monitor * $scan_costs["launch_monitor"]["mineral"]);
		if ($launch_monitor > 0) $total_energy_cost += ($launch_monitor * $scan_costs["launch_monitor"]["energy"]);
		
		if ($structure_monitor > 0) $total_mineral_cost += ($structure_monitor * $scan_costs["structure_monitor"]["mineral"]);
		if ($structure_monitor > 0) $total_energy_cost += ($structure_monitor * $scan_costs["structure_monitor"]["energy"]);
		
		// If player knows Energy Conservation, then all items cost no energy
		$dm = new DevelopmentModel();
		if ($dm->does_player_know_development($player_name, "Energy Conservation")) $total_energy_cost = 0;

		
		// Check to make sure they really have the development
		// Advanced Scans automatically provides all developments from the scan research tree, Advanced signals gives those from the signal tree
		$dm = new DevelopmentModel();

		if ($survey_tuner > 0 && 
				(!$dm->does_player_know_development($player_name, "Surveying") && !$dm->does_player_know_development($player_name, "Advanced Scans")) ) {
			show_error("You have not completed the appropriate development: Surveying");
			return;
		}
		if ($scan_amplifier > 0 && 
				(!$dm->does_player_know_development($player_name, "Scan Amplification") && !$dm->does_player_know_development($player_name, "Advanced Scans")) ) {
			show_error("You have not completed the appropriate development: Scan Amplification");
			return;
		}
		if ($noise_generator > 0 && 
				(!$dm->does_player_know_development($player_name, "Noise Generation") && !$dm->does_player_know_development($player_name, "Advanced Signals")) ){
			show_error("You have not completed the appropriate development: Noise Generation");
			return;
		}
		if ($scan_sensor > 0 && 
				(!$dm->does_player_know_development($player_name, "Scan Sensing") && !$dm->does_player_know_development($player_name, "Advanced Signals")) ){
			show_error("You have not completed the appropriate development: Scan Sensing");
			return;
		}
		if ($scan_filter > 0 && 
				(!$dm->does_player_know_development($player_name, "Scan Filtering") && !$dm->does_player_know_development($player_name, "Advanced Signals")) ) {
			show_error("You have not completed the appropriate development: Scan Filtering");
			return;
		}
		
		
		if ($site_scan > 0 && 
				(!$dm->does_player_know_development($player_name, "Site Scan") && !$dm->does_player_know_development($player_name, "Advanced Scans")) ) {
			show_error("You have not completed the appropriate development: Site Scan");
			return;
		}
		if ($r_and_d_scan > 0 && 
				(!$dm->does_player_know_development($player_name, "R and D Scan") && !$dm->does_player_know_development($player_name, "Advanced Scans")) ) {
			show_error("You have not completed the appropriate development: R and D Scan");
			return;
		}
		if ($continent_scan > 0 && 
				(!$dm->does_player_know_development($player_name, "Continent Scan") && !$dm->does_player_know_development($player_name, "Advanced Scans")) ){
			show_error("You have not completed the appropriate development: Continent Scan");
			return;
		}
		if ($creature_scan > 0 && 
				(!$dm->does_player_know_development($player_name, "Creature Scan") && !$dm->does_player_know_development($player_name, "Advanced Scans")) ){
			show_error("You have not completed the appropriate development: Creature Scan");
			return;
		}
		if ($military_scan > 0 && 
				(!$dm->does_player_know_development($player_name, "Military Scan") && !$dm->does_player_know_development($player_name, "Advanced Scans")) ){
			show_error("You have not completed the appropriate development: Military Scan");
			return;
		}
		if ($planetary_scan > 0 && 
				(!$dm->does_player_know_development($player_name, "Planetary Scan") && !$dm->does_player_know_development($player_name, "Advanced Scans")) ){
			show_error("You have not completed the appropriate development: Planetary Scan");
			return;
		}
		if ($news_scan > 0 && 
				(!$dm->does_player_know_development($player_name, "News Scan") && !$dm->does_player_know_development($player_name, "Advanced Scans")) ){
			show_error("You have not completed the appropriate development: News Scan");
			return;
		}
		if ($full_scan > 0 && 
				(!$dm->does_player_know_development($player_name, "Full Scan") && !$dm->does_player_know_development($player_name, "Advanced Scans")) ){
			show_error("You have not completed the appropriate development: Full Scan");
			return;
		}
		if ($launch_monitor > 0 && 
				(!$dm->does_player_know_development($player_name, "Monitoring") && !$dm->does_player_know_development($player_name, "Universe Monitors")) ){
			show_error("You have not completed the appropriate development: Monitoring");
			return;
		}
		if ($structure_monitor > 0 && 
				(!$dm->does_player_know_development($player_name, "Monitoring") && !$dm->does_player_know_development($player_name, "Universe Monitors")) ){
			show_error("You have not completed the appropriate development: Monitoring");
			return;
		}

		 
		// Check to see if player can afford it
		$pd = new PlayerData();
		$pd->db_fill($player_name);
		
		if ($pd->mineral < $total_mineral_cost) {
			show_error("You do not have enough resources");
			return;
		}
		if ($pd->energy < $total_energy_cost) {
			show_error("You do not have enough resources");
			return;
		}
		
		// OK take the money away
		$pd->subtract("mineral", $total_mineral_cost);
		$pd->subtract("energy", $total_energy_cost);
		
		// Now provision the scans
		$gm = new GameModel();
  	$current_tick = $gm->get_current_tick();
		
		if ($survey_tuner > 0) $sm->provision_scans("survey_tuner", $survey_tuner, $scan_costs["survey_tuner"]["ticks"], $current_tick);
		if ($scan_amplifier > 0) $sm->provision_scans("scan_amplifier", $scan_amplifier, $scan_costs["scan_amplifier"]["ticks"], $current_tick);
		if ($noise_generator > 0) $sm->provision_scans("noise_generator", $noise_generator, $scan_costs["noise_generator"]["ticks"], $current_tick);
		if ($scan_sensor > 0) $sm->provision_scans("scan_sensor", $scan_sensor, $scan_costs["scan_sensor"]["ticks"], $current_tick);
		if ($scan_filter > 0) $sm->provision_scans("scan_filter", $scan_filter, $scan_costs["scan_filter"]["ticks"], $current_tick);

		if ($site_scan > 0) $sm->provision_scans("site_scan", $site_scan, $scan_costs["site_scan"]["ticks"], $current_tick);
		if ($r_and_d_scan > 0) $sm->provision_scans("r_and_d_scan", $r_and_d_scan, $scan_costs["r_and_d_scan"]["ticks"], $current_tick);
		if ($continent_scan > 0) $sm->provision_scans("continent_scan", $continent_scan, $scan_costs["continent_scan"]["ticks"], $current_tick);
		if ($creature_scan > 0) $sm->provision_scans("creature_scan", $creature_scan, $scan_costs["creature_scan"]["ticks"], $current_tick);
		if ($military_scan > 0) $sm->provision_scans("military_scan", $military_scan, $scan_costs["military_scan"]["ticks"], $current_tick);

		if ($planetary_scan > 0) $sm->provision_scans("planetary_scan", $planetary_scan, $scan_costs["planetary_scan"]["ticks"], $current_tick);
		if ($news_scan > 0) $sm->provision_scans("news_scan", $news_scan, $scan_costs["news_scan"]["ticks"], $current_tick);
		if ($full_scan > 0) $sm->provision_scans("full_scan", $full_scan, $scan_costs["full_scan"]["ticks"], $current_tick);
		
		if ($launch_monitor > 0) $sm->provision_scans("launch_monitor", $launch_monitor, $scan_costs["launch_monitor"]["ticks"], $current_tick);
		if ($structure_monitor > 0) $sm->provision_scans("structure_monitor", $structure_monitor, $scan_costs["structure_monitor"]["ticks"], $current_tick);
		
	}


	function use_scan() {
 		$player_name=$_SESSION['player_name'];
		$scan_type = $_REQUEST["scan_type"];
		$number_available = $this->get_number_scans_available($player_name, $scan_type);
		show_info("Scan type: $scan_type, Available: $number_available");
		
		if ($number_available == 0) {
			show_error("Not enough scans available");
			return;
		}
		
		$this->subtract_scan($player_name, $scan_type, 1);
		
		$galaxy = $_REQUEST['galaxy'];
  	$star = $_REQUEST['star'];
  	$planet = $_REQUEST['planet'];
  	$continent = $_REQUEST['continent'];
		
		$coordinates = "{$galaxy}:{$star}:{$planet}:{$continent}";
		
		$pd = new PlayerData();
		if (strcmp($scan_type, "planetary_scan") == 0) {
			$target_name = "Planetary Scan";
		} else {
			$target_name = $pd->get_player_name_from_location($galaxy, $star, $planet, $continent);
		}
		
		// Check to see if scan was successful and was detected
		$sm = new ScansModel();
		$target_noise = $sm->get_number_of_scans_for_player($target_name, "noise_generator");
		$target_sensors = $sm->get_number_of_scans_for_player($target_name, "scan_sensor");
		$player_amps = $sm->get_number_of_scans_for_player($player_name, "scan_amplifier");
		$player_filters = $sm->get_number_of_scans_for_player($player_name, "scan_filter");
		
		// Now adjustments for Expert skills, Advance Scanning and Advanced Signals
		$dm = new DevelopmentModel();
		if ($dm->does_player_know_development($player_name, "Advanced Scans")) {
			$player_amps = $player_amps * 5;
			$player_filters = $player_filters * 5;
		}
		if ($dm->does_player_know_development($target_name, "Advanced Signals")) {
			$target_noise = $target_noise * 5;
			$target_sensors = $target_sensors * 5;
		}
		
		
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		$current_time = get_db_time();

		
		$a = (rand() % 100) + 10; // 10% chance of failed scan in all cases
		$scan_ratio = ceil ( (100 * ($player_amps - $target_noise) ) / ($player_amps+2) );
		show_info ("Target: $target_name Amps: $player_amps, Noise: $target_noise  Scan Ratio: $scan_ratio, Roll: $a");
		if ($scan_ratio > 100) $scan_ratio = 100;
		if ($a > $scan_ratio ) {
			show_error("Your Scan Failed to gather any information");	
			$scan_roll = false;
		} else {
			if (strcmp($scan_type, "r_and_d_scan") == 0) $this->use_r_and_d_scan($coordinates, $target_name, $current_tick, $current_time);
			if (strcmp($scan_type, "continent_scan") == 0) $this->use_continent_scan($coordinates, $target_name, $current_tick, $current_time);
			if (strcmp($scan_type, "creature_scan") == 0) $this->use_creature_scan($coordinates, $target_name, $current_tick, $current_time);
			if (strcmp($scan_type, "military_scan") == 0) $this->use_military_scan($coordinates, $target_name, $current_tick, $current_time);
			if (strcmp($scan_type, "news_scan") == 0) $this->use_news_scan($coordinates, $target_name, $current_tick, $current_time);
			if (strcmp($scan_type, "planetary_scan") == 0) $this->use_planetary_scan($galaxy, $star, $planet, $target_name, $current_tick, $current_time);
			if (strcmp($scan_type, "full_scan") == 0) $this->use_full_scan($coordinates, $target_name, $current_tick, $current_time);
			if (strcmp($scan_type, "launch_monitor") == 0) $this->use_launch_monitor($coordinates, $target_name, $current_tick, $current_time);
			if (strcmp($scan_type, "structure_monitor") == 0) $this->use_structure_monitor($coordinates, $target_name, $current_tick, $current_time);
			$scan_roll = true;			
		}
		
		$a = (rand() % 100);
		$detect_ratio = ceil (100 * ($player_amps - $player_filters + $target_sensors) / ($player_amps+2) );
		show_info ("Amps: $player_amps, Filters: $player_filters, Sensors: $target_sensors  Detect Ratio: $detect_ratio, Roll: $a");
		if ($a > $scan_ratio ) {
			show_error("Your Scan was detected");
			$this->make_detected_scan_news($player_name, $target_name, $scan_roll);	
		}
		if ($scan_roll) { 
			$scan_text = $_SESSION['scan_text'];
			$text = htmlspecialchars($scan_text, ENT_QUOTES);
			if ($scan_type != "launch_monitor" && $scan_type != "structure_monitor") $sm->store_scan($player_name, $target_name, $scan_type, $text);
		}
	}

	function make_detected_scan_news($player_name, $target_name, $scan_roll) {
		$nm = new NewsModel();
		
		$subject = 'Scan Detected';
		if ($scan_roll) $text = "$player_name successfully scanned you.";
		else $text = "$player_name tried to scan you but he failed.";
		
		$nm->add_new_news($target_name, 'player', 'scans', $subject, $text);
		
	}
	
	function use_r_and_d_scan($coordinates, $target_name, $current_tick, $current_time) {		
		show_info("Research and Development Scan");
		
		$scan_text = $this->r_and_d_scan_text($coordinates, $target_name, $current_tick, $current_time);
		$_SESSION['scan_text'] = $scan_text;
	}
	
	function r_and_d_scan_text($coordinates, $target_name, $current_tick, $current_time) {
		$sm = new ScansModel();
		$research_items=$sm->get_all_research($target_name);
		$development_items=$sm->get_all_developments($target_name);
		$current_research_items=$sm->get_current_research($target_name);
		$current_development_items=$sm->get_current_developments($target_name);
		
		$scan_text = "
		<TABLE class='STD'>
		<TR><TH class='STD' colspan='2'>Reasearch and Development Scan - $coordinates - $target_name</TH></TR>
		<TR><TH class='STD'>Tick #{$current_tick}</TH><TH class='STD'>$current_time</TH></TR>
		<TR>
		  <TD class='STD' style='text-align:left; vertical-align:top;'><B>Completed Research Items</B><br />$research_items</TD>
		  <TD class='STD' style='text-align:left; vertical-align:top;'><B>Completed Development Items</B><br />$development_items</TD>
		</TR>
		<TR>
		  <TD class='STD' style='text-align:left; vertical-align:top;'><B>Currently Researching</B><br />$current_research_items</TD>
		  <TD class='STD' style='text-align:left; vertical-align:top;'><B>Currently Developing</B><br />$current_development_items</TD>
		</TR>
		</TABLE>
		";
		
		 return $scan_text;
	}

	function use_continent_scan($coordinates, $target_name, $current_tick, $current_time) {		
		show_info("Continent Scan");
		
		$scan_text = $this->continent_scan_text($coordinates, $target_name, $current_tick, $current_time);
		$_SESSION['scan_text'] = $scan_text;
	}

	function continent_scan_text($coordinates, $target_name, $current_tick, $current_time) {
		
		$td = new PlayerData();
		$td->db_fill($target_name);
		
		$dm= new DevelopmentModel();
		$basic_tech_count = $dm->get_technology_count($target_name, "basic");
		$expert_tech_count = $dm->get_technology_count($target_name, "expert");
		$master_tech_count = $dm->get_technology_count($target_name, "master");
		
		$cm= new CreaturesModel();
		$number_creatures = $cm->get_number_of_creatures_for_player($target_name);
		
		$fortModel = new FortsModel();
		$number_forts = $fortModel->get_number_forts($target_name);
		if ($number_forts <= 0) $number_forts = "&nbsp;";

		$bm = new BombsModel();
		$number_bombs = $bm->get_number_bombs($target_name, "Bomb");
		if ($number_bombs <= 0) $number_bombs = "&nbsp;";
		$number_poison_bombs = $bm->get_number_bombs($target_name, "Poison Bomb");
		if ($number_poison_bombs <= 0) $number_poison_bombs = "&nbsp;";
		
		$number_traps = $bm->get_number_bombs($target_name, "Trap");
		if ($number_traps <= 0) $number_traps = "&nbsp;";
		$number_psych_traps = $bm->get_number_bombs($target_name, "Psychological Trap");
		if ($number_psych_traps <= 0) $number_psych_traps = "&nbsp;";
				
		$scan_text = "
		<TABLE class='STD'>
			<TR><TH class='STD' colspan='5'>Continent Scan - $coordinates - $target_name</TH></TR>
			<TR><TH class='STD' colspan='2'>Tick #{$current_tick}</TH><TH class='STD' colspan='2'>$current_time</TH></TR>
			<TR>
				<TD class='STD' style='text-align:left; vertical-align:top;'>Score</TD>
				<TD class='STD' style='text-align:left; vertical-align:top;'>$td->score</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>Unassigned Structures</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>$td->unassigned</TD>
			</TR>
			<TR>
				<TD class='STD' style='text-align:left; vertical-align:top;'>&nbsp;</TD>
				<TD class='STD' style='text-align:left; vertical-align:top;'>&nbsp;</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>Extractors</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>$td->extractor</TD>
			</TR>
			<TR>
				<TD class='STD' style='text-align:left; vertical-align:top;'>Mineral</TD>
				<TD class='STD' style='text-align:left; vertical-align:top;'>$td->mineral</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>Genetic Labs</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>$td->genetic_lab</TD>
			</TR>
			<TR>
				<TD class='STD' style='text-align:left; vertical-align:top;'>Organic</TD>
				<TD class='STD' style='text-align:left; vertical-align:top;'>$td->organic</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>Powerplants</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>$td->powerplant</TD>
			</TR>
			<TR>
				<TD class='STD' style='text-align:left; vertical-align:top;'>Energy</TD>
				<TD class='STD' style='text-align:left; vertical-align:top;'>$td->energy</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>Factories</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>$td->factory</TD>
			</TR>
			<TR>
				<TD class='STD' style='text-align:left; vertical-align:top;background-color:000000;border-color:000000;' colspan='5'></TD>
			</TR>
			<TR>
				<TD class='STD' style='text-align:left; vertical-align:top;'>Forts</TD>
				<TD class='STD' style='text-align:left; vertical-align:top;'>$number_forts</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>Creatures</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>$number_creatures</TD>
			</TR>
			<TR>
				<TD class='STD' style='text-align:left; vertical-align:top;'>Traps</TD>
				<TD class='STD' style='text-align:left; vertical-align:top;'>$number_traps</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>&nbsp;</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>&nbsp;</TD>
			</TR>
			<TR>
				<TD class='STD' style='text-align:left; vertical-align:top;'>Psychological Traps</TD>
				<TD class='STD' style='text-align:left; vertical-align:top;'>$number_psych_traps</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>Basic Technologies</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>$basic_tech_count</TD>
			</TR>
			<TR>
				<TD class='STD' style='text-align:left; vertical-align:top;'>Bombs</TD>
				<TD class='STD' style='text-align:left; vertical-align:top;'>$number_bombs</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>Expert Technologies</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>$expert_tech_count</TD>
			</TR>
			<TR>
				<TD class='STD' style='text-align:left; vertical-align:top;'>Poision Bombs</TD>
				<TD class='STD' style='text-align:left; vertical-align:top;'>$number_poison_bombs</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>Masteries</TD>
			  <TD class='STD' style='text-align:left; vertical-align:top;'>$master_tech_count</TD>
			</TR>
		</TABLE>
		
		
		";
		
		return $scan_text;
	}

	function use_creature_scan($coordinates, $target_name, $current_tick, $current_time) {		
		show_info("Creature Scan");
		
		$scan_text = $this->creature_scan_text($coordinates, $target_name, $current_tick, $current_time);
		$_SESSION['scan_text'] = $scan_text;
	}


	function creature_scan_text($coordinates, $target_name, $current_tick, $current_time) {
		show_info("Creature Scan");

		$cm = new CreaturesModel();

		$creature_totals = $cm->get_all_creature_totals_for_player($target_name);
		$att = $creature_totals["att"];
		$def = $creature_totals["def"];
		$foc = $creature_totals["foc"];
		$int = $creature_totals["int"];
		$dis = $creature_totals["dis"];

		$imp_number = $cm->get_number_of_creatures($target_name, "Imp" );
		$wyrm_number = $cm->get_number_of_creatures($target_name, "Wyrm" );
		$wyvern_number = $cm->get_number_of_creatures($target_name, "Wyvern" );
		$dragon_number = $cm->get_number_of_creatures($target_name, "Dragon" );

		$sprite_number = $cm->get_number_of_creatures($target_name, "Sprite" );
		$dryad_number = $cm->get_number_of_creatures($target_name, "Dryad" );
		$centaur_number = $cm->get_number_of_creatures($target_name, "Centaur" );
		$unicorn_number = $cm->get_number_of_creatures($target_name, "Unicorn" );
		
		$ogre_number = $cm->get_number_of_creatures($target_name, "Ogre" );
		$troll_number = $cm->get_number_of_creatures($target_name, "Troll" );
		$giant_number = $cm->get_number_of_creatures($target_name, "Giant" );
		$demon_number = $cm->get_number_of_creatures($target_name, "Demon" );
		
		$cheetah_number = $cm->get_number_of_creatures($target_name, "Cheetah" );
		$panther_number = $cm->get_number_of_creatures($target_name, "Panther" );
		$tiger_number = $cm->get_number_of_creatures($target_name, "Tiger" );
		$lion_number = $cm->get_number_of_creatures($target_name, "Lion" );
		
		$cyborg_number = $cm->get_number_of_creatures($target_name, "Cyborg" );
		$spider_number = $cm->get_number_of_creatures($target_name, "Spider" );
		$mantis_number = $cm->get_number_of_creatures($target_name, "Mantis" );
		$megadon_number = $cm->get_number_of_creatures($target_name, "Megadon" );
		
		$humvee_number = $cm->get_number_of_creatures($target_name, "Humvee" );
		$tank_number = $cm->get_number_of_creatures($target_name, "Tank" );
		$crusher_number = $cm->get_number_of_creatures($target_name, "Crusher" );
		$doomcrusher_number = $cm->get_number_of_creatures($target_name, "Doomcrusher" );
		
		$scan_text = "<TABLE class='STD'>
			<TR><TH class='STD' colspan='6'>Creature Scan - $coordinates - $target_name</TH></TR>
			<TR><TH class='STD' >Tick #{$current_tick}</TH><TH class='STD' colspan='3'>&nbsp;</TH><TH class='STD' colspan='2'>$current_time</TH></TR>
			<TR>
				<TD class='STD' colspan='6'>
					Att: $att Def: $def Foc: $foc Int: $int Dis: $dis
				</TD>
			</TR>
			<TR>
				<TD class='STD' width='25%'>Imp</TD><TD class='STD' width='8%'>$imp_number</TD>
				<TD class='STD' width='25%'>Ogre</TD><TD class='STD' width='8%'>$ogre_number</TD>
				<TD class='STD' width='25%'>Cyborg</TD><TD class='STD' width='8%'>$cyborg_number</TD>
			</TR>
			<TR>
				<TD class='STD'>Wyrm</TD><TD class='STD'>$wyrm_number</TD>
				<TD class='STD'>Troll</TD><TD class='STD'>$troll_number</TD>
				<TD class='STD'>Spider</TD><TD class='STD'>$spider_number</TD>
			</TR>
			<TR>
				<TD class='STD'>Wyvern</TD><TD class='STD'>$wyvern_number</TD>
				<TD class='STD'>Giant</TD><TD class='STD'>$giant_number</TD>
				<TD class='STD'>Mantis</TD><TD class='STD'>$mantis_number</TD>
			</TR>
			<TR>
				<TD class='STD'>Dragon</TD><TD class='STD'>$dragon_number</TD>
				<TD class='STD'>Demon</TD><TD class='STD'>$demon_number</TD>
				<TD class='STD'>Megadon</TD><TD class='STD'>$megadon_number</TD>
			</TR>

			<TR>
				<TD class='STD'>Sprite</TD><TD class='STD'>$sprite_number</TD>
				<TD class='STD'>Cheetah</TD><TD class='STD'>$cheetah_number</TD>
				<TD class='STD'>Humvee</TD><TD class='STD'>$humvee_number</TD>
			</TR>
			<TR>
				<TD class='STD'>Dryad</TD><TD class='STD'>$dryad_number</TD>
				<TD class='STD'>Panther</TD><TD class='STD'>$panther_number</TD>
				<TD class='STD'>Tank</TD><TD class='STD'>$tank_number</TD>
			</TR>
			<TR>
				<TD class='STD'>Centaur</TD><TD class='STD'>$centaur_number</TD>
				<TD class='STD'>Tiger</TD><TD class='STD'>$tiger_number</TD>
				<TD class='STD'>Crusher</TD><TD class='STD'>$crusher_number</TD>
			</TR>
			<TR>
				<TD class='STD'>Unicorn</TD><TD class='STD'>$unicorn_number</TD>
				<TD class='STD'>Lion</TD><TD class='STD'>$lion_number</TD>
				<TD class='STD'>Doomcrusher</TD><TD class='STD'>$doomcrusher_number</TD>
			</TR>
			
			</TABLE>
			";
		
		return $scan_text;
		
	}

	function use_military_scan($coordinates, $target_name, $current_tick, $current_time) {		
		show_info("Military Scan");
		
		$scan_text = $this->military_scan_text($coordinates, $target_name, $current_tick, $current_time);
		$_SESSION['scan_text'] = $scan_text;
	}
	
	function military_scan_text($coordinates, $target_name, $current_tick, $current_time) {
		$fm = new FleetModel();

		$fleet1_orders = $fm->get_fleet_orders($target_name, "fleet1");
		$fleet2_orders = $fm->get_fleet_orders($target_name, "fleet2");
		$fleet3_orders = $fm->get_fleet_orders($target_name, "fleet3");
		if ($fleet1_orders) {
			$fleet1_target = $fleet1_orders["target"];
			$fleet1_launch = $fleet1_orders["launch"];
			$fleet1_arrive = $fleet1_orders["arrive"];
			$fleet1_depart = $fleet1_orders["depart"];
			$fleet1_return = $fleet1_orders["return"];
		} else {
			$fleet1_target = "<I>Home</I>";
			$fleet1_launch = "&nbsp;";
			$fleet1_arrive = "&nbsp;";
			$fleet1_depart = "&nbsp;";
			$fleet1_return = "&nbsp;";
		}
		if ($fleet2_orders) {
			$fleet2_target = $fleet2_orders["target"];
			$fleet2_launch = $fleet2_orders["launch"];
			$fleet2_arrive = $fleet2_orders["arrive"];
			$fleet2_depart = $fleet2_orders["depart"];
			$fleet2_return = $fleet2_orders["return"];
		} else {
			$fleet2_target = "<I>Home</I>";
			$fleet2_launch = "&nbsp;";
			$fleet2_arrive = "&nbsp;";
			$fleet2_depart = "&nbsp;";
			$fleet2_return = "&nbsp;";
		}
		if ($fleet3_orders) {
			$fleet3_target = $fleet3_orders["target"];
			$fleet3_launch = $fleet3_orders["launch"];
			$fleet3_arrive = $fleet3_orders["arrive"];
			$fleet3_depart = $fleet3_orders["depart"];
			$fleet3_return = $fleet3_orders["return"];
		} else {
			$fleet3_target = "<I>Home</I>";
			$fleet3_launch = "&nbsp;";
			$fleet3_arrive = "&nbsp;";
			$fleet3_depart = "&nbsp;";
			$fleet3_return = "&nbsp;";
		}
		

		$scan_text = "<TABLE class='STD'>
			<TR><TH class='STD' colspan='6'>Military Scan - $coordinates - $target_name</TH></TR>
			<TR><TH class='STD' >Tick #{$current_tick}</TH><TH class='STD' colspan='2'>&nbsp;</TH><TH class='STD' colspan='2'>$current_time</TH></TR>
			<TR>
				<TH class='STD' width='25%'>&nbsp</TH>
				<TH class='STD' width='25%'>Fleet 1</TH>
				<TH class='STD' width='25%'>Fleet 2</TH>
				<TH class='STD' width='25%'>Fleet 3</TH>
			</TR>
			<TR>
				<TH class='STD' width='25%'>Target</TH>
				<TD class='STD' width='25%'>$fleet1_target</TD>
				<TD class='STD' width='25%'>$fleet2_target</TD>
				<TD class='STD' width='25%'>$fleet3_target</TD>
			</TR>
			<TR>
				<TH class='STD' width='25%'>Launch</TH>
				<TD class='STD' width='25%'>$fleet1_launch</TD>
				<TD class='STD' width='25%'>$fleet2_launch</TD>
				<TD class='STD' width='25%'>$fleet3_launch</TD>
			</TR>
			<TR>
				<TH class='STD' width='25%'>Arrive</TH>
				<TD class='STD' width='25%'>$fleet1_arrive</TD>
				<TD class='STD' width='25%'>$fleet2_arrive</TD>
				<TD class='STD' width='25%'>$fleet3_arrive</TD>
			</TR>
			<TR>
				<TH class='STD' width='25%'>Depart</TH>
				<TD class='STD' width='25%'>$fleet1_depart</TD>
				<TD class='STD' width='25%'>$fleet2_depart</TD>
				<TD class='STD' width='25%'>$fleet3_depart</TD>
			</TR>
			<TR>
				<TH class='STD' width='25%'>Return</TH>
				<TD class='STD' width='25%'>$fleet1_return</TD>
				<TD class='STD' width='25%'>$fleet2_return</TD>
				<TD class='STD' width='25%'>$fleet3_return</TD>
			</TR>
					
			</TABLE>
			";
		return $scan_text;
	}

	function use_news_scan($coordinates, $target_name, $current_tick, $current_time) {		
		show_info("News Scan");
		
		$scan_text = $this->news_scan_text($coordinates, $target_name, $current_tick, $current_time);
		$_SESSION['scan_text'] = $scan_text;
	}

	function news_scan_text($coordinates, $target_name, $current_tick, $current_time) {
		$pd= new PlayerData();
//		if ($pd->is_admin($target_name)) {
//			$scan_text = "<TABLE class='STD'><TR><TH class='STD'>Cannot News Scan an Admin</TH></TR></TABLE>";
//		} else {
			$nm = new NewsModel();
			$news_text = $nm->get_player_news($target_name);
			
			
			$scan_text = "<TABLE class='STD'>
				<TR><TH class='STD' colspan='6'>News Scan - $coordinates - $target_name</TH></TR>
				<TR>
				    <TH class='STD' >Tick #{$current_tick}</TH><TH class='STD' colspan='2'>&nbsp;</TH>
				    <TH class='STD' colspan='2'>$current_time</TH>
				</TR>
				</TABLE>
				$news_text";
//		}	
			
		return $scan_text;
	}

	function use_planetary_scan($galaxy, $star, $planet, $target_name, $current_tick, $current_time) {		
		show_info("Planetary Scan");
		
		$scan_text = $this->planetary_scan_text($galaxy, $star, $planet, $target_name, $current_tick, $current_time);
		$_SESSION['scan_text'] = $scan_text;
	}

	
	function planetary_scan_text($galaxy, $star, $planet, $target_name, $current_tick, $current_time) {
 	  srand(time());
		$player_name=$_SESSION['player_name'];

		$scan_text = "
		<TABLE class='STD'>
			<TR><TH class='STD' colspan='19'>Planetary Scan - {$galaxy}:{$star}:{$planet}</TH></TR>
			<TR><TH class='STD' colspan='9'>Tick #{$current_tick}</TH><TH class='STD' colspan='10'>$current_time</TH></TR>
			<TR>
				<TD class='STD'>Location</TD>
				<TD class='STD'>Player</TD>
				<TD class='STD'>Score</TD>
				<TD class='STD'>Online</TD>
				<TD class='STD'>Un</TD>
				<TD class='STD'>Ext</TD>
				<TD class='STD'>Gen</TD>
				<TD class='STD'>Pow</TD>
				<TD class='STD'>Fac</TD>
				<TD class='STD'>Min</TD>
				<TD class='STD'>Org</TD>
				<TD class='STD'>Eng</TD>
				<TD class='STD'>Forts</TD>
				<TD class='STD'>Bombs</TD>
				<TD class='STD'>Poison Bombs</TD>
				<TD class='STD'>Traps</TD>
				<TD class='STD'>Psych Traps</TD>
				<TD class='STD'>Creatures</TD>
				<TD class='STD'>Basic</TD>
				<TD class='STD'>Expert</TD>
				<TD class='STD'>Master</TD>
			</TR>
		";
		
		$pd= new PlayerData();
		$dm= new DevelopmentModel();
		$cm= new CreaturesModel();
		
		for ($i=1; $i < 9; $i++) {
			$target_name = $pd->get_player_name_from_location($galaxy, $star, $planet, $i);
			if ($target_name) {
				$target_player = $pd->db_fill($target_name);
						// Check to see if scan was successful and was detected
				$sm = new ScansModel();
				$target_noise = $sm->get_number_of_scans_for_player($target_name, "noise_generator");
				$target_sensors = $sm->get_number_of_scans_for_player($target_name, "scan_sensor");
				$player_amps = $sm->get_number_of_scans_for_player($player_name, "scan_amplifier");
				$player_filters = $sm->get_number_of_scans_for_player($player_name, "scan_filter");

				// Now adjustments for Expert skills, Advance Scanning and Advanced Signals
				$dm = new DevelopmentModel();
				if ($dm->does_player_know_development($player_name, "Advanced Scans")) {
					$player_amps = $player_amps * 5;
					$player_filters = $player_filters * 5;
				}
				if ($dm->does_player_know_development($target_name, "Advanced Signals")) {
					$target_noise = $target_noise * 5;
					$target_sensors = $target_sensors * 5;
				}



				$a = (rand() % 100);
				$scan_ratio = ceil ( (100 * ($player_amps - $target_noise) ) / ($player_amps+2) );
				if ($scan_ratio > 100) $scan_ratio = 100;
				if ($a > $scan_ratio ) {
					$scan_text = $scan_text . "
						<TR>
							<TD class='STD'>{$pd->galaxy}:{$pd->star}:{$pd->planet}:{$pd->continent}</TD>
							<TD class='STD'>$pd->name</TD>
							<TD class='STD'>$pd->score</TD>
							<TD class='STD' colspan='15'><I>Target Continent Scan Failed</I></TD>
						</TR>";
				} else {
					
					$basic_tech_count = $dm->get_technology_count($target_name, "basic");
					$expert_tech_count = $dm->get_technology_count($target_name, "expert");
					$master_tech_count = $dm->get_technology_count($target_name, "master");
					
					$number_creatures = $cm->get_number_of_creatures_for_player($target_name);

					$fortModel = new FortsModel();
					$number_forts = $fortModel->get_number_forts($target_name);

					$bm = new BombsModel();
					$number_bombs = $bm->get_number_bombs($target_name, "Bomb");
					if ($number_bombs <= 0) $number_bombs = "&nbsp;";
					$number_poison_bombs = $bm->get_number_bombs($target_name, "Poison Bomb");
					if ($number_poison_bombs <= 0) $number_poison_bombs = "&nbsp;";
					
					$number_traps = $bm->get_number_bombs($target_name, "Trap");
					if ($number_traps <= 0) $number_traps = "&nbsp;";
					$number_psych_traps = $bm->get_number_bombs($target_name, "Psychological Trap");
					if ($number_psych_traps <= 0) $number_psych_traps = "&nbsp;";
					
					
					
					$scan_text = $scan_text . "
						<TR>
							<TD class='STD'>{$pd->galaxy}:{$pd->star}:{$pd->planet}:{$pd->continent}</TD>
							<TD class='STD'>$pd->name</TD>
							<TD class='STD'>$pd->score</TD>
							<TD class='STD'>$pd->last_online</TD>
							<TD class='STD'>$pd->unassigned</TD>
							<TD class='STD'>$pd->extractor</TD>
							<TD class='STD'>$pd->genetic_lab</TD>
							<TD class='STD'>$pd->powerplant</TD>
							<TD class='STD'>$pd->factory</TD>
							<TD class='STD'>$pd->mineral</TD>
							<TD class='STD'>$pd->organic</TD>
							<TD class='STD'>$pd->energy</TD>
							<TD class='STD'>$number_forts</TD>
							<TD class='STD'>$number_bombs</TD>
							<TD class='STD'>$number_poison_bombs</TD>
							<TD class='STD'>$number_traps</TD>
							<TD class='STD'>$number_psych_traps</TD>
							<TD class='STD'>$number_creatures</TD>
							<TD class='STD'>$basic_tech_count</TD>
							<TD class='STD'>$expert_tech_count</TD>
							<TD class='STD'>$master_tech_count</TD>
						</TR>
					";
				}
			}
		}	  

		
		$scan_text = $scan_text . "
			</TABLE>
		";

		return $scan_text;
	}
	
	function use_full_scan($coordinates, $target_name, $current_tick, $current_time) {
		show_info("Full Scan");
		
		$scan_text = $this->continent_scan_text($coordinates, $target_name, $current_tick, $current_time) .
				$this->creature_scan_text($coordinates, $target_name, $current_tick, $current_time) .
				$this->military_scan_text($coordinates, $target_name, $current_tick, $current_time) .
				$this->r_and_d_scan_text($coordinates, $target_name, $current_tick, $current_time) .
				$this->news_scan_text($coordinates, $target_name, $current_tick, $current_time);
				
		$_SESSION['scan_text'] = $scan_text;
	}

	function use_launch_monitor($coordinates, $target_name, $current_tick, $current_time) {
		$player_name=$_SESSION['player_name'];
		show_info("Launch Monitor successfully deployed");
		$sm = new ScansModel();
		$sm->add_monitor($player_name, $target_name, "launch", $current_tick, $current_tick + 50);  // reduced to 50 ticks, was too long before
		
				
	}

	function use_structure_monitor($coordinates, $target_name, $current_tick, $current_time) {
		$player_name=$_SESSION['player_name'];
		show_info("Structure Monitor successfully deployed");

		$sm = new ScansModel();
		$sm->add_monitor($player_name, $target_name, "structure", $current_tick, $current_tick + 250);
	}

	
	function use_site_scan() {
 	  srand(time());
		$player_name=$_SESSION['player_name'];
		$number = $_REQUEST["number"];
		$number_available = $this->get_number_scans_available($player_name, 'site_scan');
		if (strcmp($number, "all") == 0) $number = $number_available;
		if ($number > $number_available) {
			show_error("Not enough scans available");
			return;
		}
	  $conn = db_connect();
		// First subtract the scans
		$query = "update player_scans set number=number-$number where player_name='$player_name' and scan_type='site_scan'";
		$result = $conn->query($query);
		
		
		$count = 0;
		for ($i=0; $i<$number;$i++) {
			$status = $this->check_single_site_scan($player_name, $count);
			if ($status) $count++;
		}
		
		// Then add successes to the unassigned
		$query = "update player set unassigned = unassigned + $count where name='$player_name'";
		$result = $conn->query($query);
		
		show_info("Site Scans: $count of $number were successful");
	}
	
	function check_single_site_scan($player_name, $number_successes_so_far) {
		$sm = new ScansModel();
		
		$pd = new PlayerData();
		$pd->db_fill($player_name);
		$total = $pd->unassigned + $pd->extractor + $pd->genetic_lab + $pd->powerplant + $pd->factory + $number_successes_so_far;

		$site_scans = $sm->get_number_of_scans_for_player($player_name, "site_scan");
		$tuners = $sm->get_number_of_scans_for_player($player_name, "survey_tuner");

		$dm = new DevelopmentModel();
		if ($dm->does_player_know_development($player_name, "Advanced Scans")) {
			$tuners = $tuners * 3;
		}

		$ratio = $tuners/$total;
		$chance = $ratio * $ratio * 100;
			

		// Check to see if it was successful
		$a = (rand() % 100);
		
		if ($a > $chance ) return false;  //Scan failed
		else return true;
	}
	
	function get_number_scans_available($player_name, $scan_type) {
	  $conn = db_connect();
		$query = "select * from player_scans where player_name='$player_name' and scan_type='$scan_type'";
		$result = $conn->query($query);
		$row = $result->fetch_object();

		return $row->number;
	}
	
	function subtract_scan($player_name, $scan_type, $number) {
	  $conn = db_connect();
		$query = "update player_scans set number= number-$number where player_name='$player_name' and scan_type='$scan_type'";
		$result = $conn->query($query);
		
	}
	
	function show_scan_history() {
		$scan_id = $_REQUEST['scan_id'];
		
	  $conn = db_connect();
		$query = "select * from scan_results where id='$scan_id'";
		$result = $conn->query($query);
		$row = $result->fetch_object();

		$scan_text = htmlspecialchars_decode($row->text, ENT_QUOTES);
		
		$_SESSION['scan_text'] = $scan_text;
	}
	
}
?>