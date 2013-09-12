<?php
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('scans_model.php5'); 
	require_once('development_model.php5'); 
	require_once('player_data.php5'); 
	require_once('scan_item.php5'); 
	require_once('description_panel.php5'); 
	require_once('game_model.php5'); 

class ScansView {
	function display_scans_view($subview) {
		$this->display_current_scan_build();
		  
		echo "<TABLE class='STD'><TR>\n";
		$view_fns = new ViewFunctions();
		$view_fns->display_button('Create Scans', '808000', 'B0B040', 'main_page.php5?view=scans&subview=create');
		$view_fns->display_button('Site Scans', '008080', '40B0B0', 'main_page.php5?view=scans&subview=site');
		$view_fns->display_button('Remote Scans', '800080', 'B040B0', 'main_page.php5?view=scans&subview=remote');
		$view_fns->display_button('Monitors', '202060', '6060A0', 'main_page.php5?view=scans&subview=monitor');
		$view_fns->display_button('Scan History', '406020', '80A060', 'main_page.php5?view=scans&subview=history');
//		$view_fns->display_button('Individual Scan Details', '404040', '808080', 'main_page.php5?view=scans&subview=individual');
		echo "</TR></TABLE>\n";
		
		if (strcmp($subview, "create") == 0) $this->display_create_scan_subview();
		else if (strcmp($subview, "site") == 0) $this->display_site_scan_subview();
		else if (strcmp($subview, "remote") == 0) $this->display_remote_scan_subview();
		else if (strcmp($subview, "monitor") == 0) $this->display_monitor_scan_subview();
		else if (strcmp($subview, "history") == 0) $this->display_scan_history_chooser();
		else if (strcmp($subview, "individual") == 0) $this->display_individual_scan_subview();
		
		$this->display_current_scan_text();
	}
	
	function display_scan_history_chooser() {
		$player_name = $_SESSION['player_name'];
	  $conn = db_connect();
		$query = "select * from scan_results where player_name='$player_name' order by time desc limit 0, 20";
		$result = $conn->query($query);
		
		
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='scan_history'/>\n";
		echo "     <INPUT type='hidden' name='view' value='scans'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='active'/>\n";
		echo "<TABLE class='STD'><TR>\n";	
		echo "<TD class='STD'>";
		echo "<SELECT name='scan_id'> \n";
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$scan_name = $this->get_scan_name_from_type($row->scan_type);
			echo " <OPTION value='$row->id'> $scan_name: $row->target_name - Tick #{$row->tick} </OPTION>";
		}
		echo "</SELECT>";
		echo "</TD>\n";
		echo "<TD class='STD'>";
		echo "<INPUT type='submit' value='View Scan' />";
		echo "</TD>\n";
		echo "</TR></TABLE>\n";
		echo "</FORM>\n";
	}
	
	function display_current_scan_text() {
		$scan_text = $_SESSION['scan_text'];

		echo $scan_text;
		
		$_SESSION['scan_text']="";
	}
	
	function display_create_scan_subview() {
		$player_name = $_SESSION['player_name'];
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_scans'/>\n";
		echo "     <INPUT type='hidden' name='view' value='scans'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='create'/>\n";
		echo "<TABLE class='STD'>\n";
		echo "<TR >";
		echo "	<TH class='STD' width='70%' style='text-align:left;'> Scan Type </TH>";
		echo "	<TH class='STD' width='10%'> Cost </TH>";
		echo "	<TH class='STD' width='10%'> You Own </TH>";
		echo "	<TH class='STD' width='10%'> To Make </TH>";
		echo "</TR>\n";

	  $conn = db_connect();
	  
	  // first we have to create an array of the current scans owned by a player
		$query = "select * from player_scans where player_name='$player_name' ";
		$result = $conn->query($query);
		$player_scans = array();
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$player_scans["$row->scan_type"] = $row->number;
		}

		// Then we need to get other details from the scan_items table	  
		$query = "select * from scan_items where type='equipment'";
		$result = $conn->query($query);

		$dm = new DevelopmentModel();
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$current_number = $player_scans["$row->subtype"];
			if (strlen($current_number) == 0) $current_number=0;
			
			$active = $dm->does_player_know_development($player_name, $row->dependent_development);
			// Special rule for Advanced Scans, Surveys and Scan Amps can be made
			// Special rule for Advanced Signals, Noise Generators, Scans Sensors and Scan Filters can be made
			if ($dm->does_player_know_development($player_name, "Advanced Scans") && 
					(strcmp($row->name, "Survey Tuners") == 0 || strcmp($row->name, "Scan Amplifiers") == 0 ) ) $active = true;
			if ($dm->does_player_know_development($player_name, "Advanced Signals") && 
					(strcmp($row->name, "Noise Generators") == 0 || strcmp($row->name, "Scan Sensors") == 0  || strcmp($row->name, "Scan Filters") == 0 )) $active = true;
			$this->display_scan_purchase_info($row->name, $row->mineral, $row->energy, $current_number, $row->subtype, $row->ticks, $row->description, $active);
		}

		echo "<TR></TR>\n";

		$query = "select * from scan_items where type='active'";
		$result = $conn->query($query);
		
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$current_number = $player_scans["$row->subtype"];
			if (strlen($current_number) == 0) $current_number=0;
			$active = $dm->does_player_know_development($player_name, $row->dependent_development);
			// Special rule from Advanced Scans, all active scans types
			if ($dm->does_player_know_development($player_name, "Advanced Scans")) $active = true;
			$this->display_scan_purchase_info($row->name, $row->mineral, $row->energy, $current_number, $row->subtype, $row->ticks, $row->description, $active);
		}

		echo "<TR></TR>\n";

		$query = "select * from scan_items where type='monitor'";
		$result = $conn->query($query);

		$dm = new DevelopmentModel();
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$current_number = $player_scans["$row->subtype"];
			if (strlen($current_number) == 0) $current_number=0;
			$active = $dm->does_player_know_development($player_name, $row->dependent_development);
			$this->display_scan_purchase_info($row->name, $row->mineral, $row->energy, $current_number, $row->subtype, $row->ticks, $row->description, $active);
		}


		echo "</TABLE> \n";
		echo "<INPUT type='submit' value='Create Scans' />\n";
		echo "</FORM>\n";

	}
	
	function set_scan_menu_item($scan_type) {
		
	}
	
	function display_scan_purchase_info($scan_type, $mineral, $energy, $current_number, $field_name, $ticks, $description, $active) {
		echo " <TR>";
		echo "  <TD class='STD' width='70%' style='text-align:left;'>";
		echo "    <B><A style='color:white' href='main_page.php5?view=scans&subview=individual&scan=$scan_type'>$scan_type</B></A><BR />";
		echo "    <I>$description</I>";
		echo "  </TD>\n";
		echo "  <TD class='STD' width='10%' >{$mineral}m <br /> {$energy}e</TD>\n";
		echo "  <TD class='STD' width='10%' >$current_number</TD>\n";
		echo "  <TD class='STD' width='10%' >\n";
		if ($active) {
			echo "     <INPUT type='text' size='6' name='{$field_name}' /> <BR />\n";
			echo "     <I>{$ticks} ticks</I>\n";
		} else {
			echo "&nbsp;";
		}
		echo "  </TD>\n";
		echo " </TR>\n";
	}
	
	function display_current_scan_build() {
		echo $this->make_current_scan_build_display();
	}
	
	function make_current_scan_build_display() {
		$sm = new ScansModel();
		
		$player_name = $_SESSION['player_name'];
	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='scan'
			and status='building'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return;
		
		$string = "";
		$string .= "<TABLE class='STD' style='width:100%'><TR>\n";
		$string .= "<TD class='STD' >";
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$scan_type = $row->build_item;
			$number = $row->number;
			$ticks_remaining = $row->ticks_remaining;
			$total_ticks = $sm->get_total_ticks_of_scan($scan_type);

			$ticks_completed = $total_ticks - $ticks_remaining;
			$percent_complete=100*$ticks_completed/$total_ticks;
			$percent_incomplete=100-$percent_complete;

			$string .= "Building: $scan_type ($number), ($ticks_completed/$total_ticks) completed<BR/>";
			$string .= "
					<TABLE class='BAR' width='80%' >
						<TR>
							<TD class='BAR' style='width:{$percent_complete}%;background-color:darkgrey'>&nbsp;<TD>
							<TD class='BAR' style='width:{$percent_incomplete}%;background-color:black'>&nbsp;<TD>
						</TR>
					</TABLE>\n";

		}
		$string .= "<BR />";
		$string .= "</TD>";
		$string .= "</TR></TABLE>\n";
		
		return $string;
	}

	function display_site_scan_subview() {
 		$player_name=$_SESSION['player_name'];

		$view_fns = new ViewFunctions();
		$pd = new PlayerData();
		$pd->db_fill($player_name);
		$total = $pd->unassigned + $pd->extractor + $pd->genetic_lab + $pd->powerplant + $pd->factory;
		
		$sm = new ScansModel();
		$site_scans = $sm->get_number_of_scans_for_player($player_name, "site_scan");
		if($site_scans <= 0) $site_scans = 0; 
		$tuners = $sm->get_number_of_scans_for_player($player_name, "survey_tuner");
		if ($tuners <= 0) $tuners = 0;
		$ratio = ceil (100*$tuners/$total);

		$dm = new DevelopmentModel();
		if ($dm->does_player_know_development($player_name, "Advanced Scans")) {
			$tuners = $tuners . "(x3 for Adv. Scans)";
			$ratio = ceil (100*$tuners*3/$total);
		}
		
		echo "<TABLE>";
		echo "<TR><TD>";
		echo "<TABLE class='STD' style='width:421px;'>\n";
		echo "<TR><TD class='STD'>Current Unassigned</TD><TD class='STD'>$pd->unassigned</TD></TR>\n";
		echo "<TR><TD class='STD'>Current Extractor(s)</TD><TD class='STD'>$pd->extractor</TD></TR>\n";
		echo "<TR><TD class='STD'>Current Genetic Lab(s)</TD><TD class='STD'>$pd->genetic_lab</TD></TR>\n";
		echo "<TR><TD class='STD'>Current Powerplant(s)</TD><TD class='STD'>$pd->powerplant</TD></TR>\n";
		echo "<TR><TD class='STD'>Current Factory(ies)</TD><TD class='STD'>$pd->factory</TD></TR>\n";
		echo "<TR><TD class='STD'>Current Total Structures</TD><TD class='STD'><B>$total</B></TD></TR>\n";
		echo "<TR><TD class='STD'>Number of Survey Tuners</TD><TD class='STD'>$tuners</TD></TR>\n";
		echo "<TR><TD class='STD'>Tuner/Stucture Ratio</TD><TD class='STD'>{$ratio}%</TD></TR>\n";
		echo "</TABLE>\n";
		echo "</TD><TD>";
		
		echo "<TABLE class='STD' style='width:421px;'>\n";
		echo "<TR><TD class='STD'>Number of Scans Available</TD><TD class='STD'>$site_scans</TD></TR>\n";
		echo "<TR><TD class='STD' colspan='2'>&nbsp</TD></TR>\n";
		echo "<TR><TD class='STD'>Use a single scan</TD>";
		$view_fns->display_button('Scan 1', '800080', 'B04080', 'main_page.php5?view=scans&subview=site&action=site_scan&number=1');
		echo "<TR><TD class='STD'>Use 10 scans at once</TD>";
		$view_fns->display_button('Scan 10', '800080', 'B04080', 'main_page.php5?view=scans&subview=site&action=site_scan&number=10');
		echo "<TR><TD class='STD'>Use 100 scans at once</TD>";
		$view_fns->display_button('Scan 100', '800080', 'B04080', 'main_page.php5?view=scans&subview=site&action=site_scan&number=100');
		echo "<TR><TD class='STD'>Use 1000 scans at once</TD>";
		$view_fns->display_button('Scan 1000', '800080', 'B04080', 'main_page.php5?view=scans&subview=site&action=site_scan&number=1000');
		echo "<TR><TD class='STD'>Use 10000 scans at once</TD>";
		$view_fns->display_button('Scan 10000', '800080', 'B04080', 'main_page.php5?view=scans&subview=site&action=site_scan&number=10000');
		echo "<TR><TD class='STD'>Use ALL scans at once</TD>";
		$view_fns->display_button('Scan All', '800080', 'B04080', 'main_page.php5?view=scans&subview=site&action=site_scan&number=all');
		echo "</TABLE>\n";
		
		echo "</TD><TR>";
		echo "</TABLE>\n";
	}
	
	function display_remote_scan_subview() {
 		$player_name=$_SESSION['player_name'];
		$sm = new ScansModel();
		$r_and_d_scans = $sm->get_number_of_scans_for_player($player_name, "r_and_d_scan");
		if ($r_and_d_scans < 0) $r_and_d_scans = 0;
		$continent_scans = $sm->get_number_of_scans_for_player($player_name, "continent_scan");
		if ($continent_scans < 0) $continent_scans = 0;
		$creature_scans = $sm->get_number_of_scans_for_player($player_name, "creature_scan");
		if ($creature_scans < 0) $creature_scans = 0;
		$military_scans = $sm->get_number_of_scans_for_player($player_name, "military_scan");
		if ($military_scans < 0) $military_scans = 0;
		$news_scans = $sm->get_number_of_scans_for_player($player_name, "news_scan");
		if ($news_scans < 0) $news_scans = 0;
		$planetary_scans = $sm->get_number_of_scans_for_player($player_name, "planetary_scan");
		if ($planetary_scans < 0) $planetary_scans = 0;
		$full_scans = $sm->get_number_of_scans_for_player($player_name, "full_scan");
		if ($full_scans < 0) $full_scans = 0;
	
		echo "<TABLE class='STD'>\n";
		echo "<TR>\n";
		echo "  <TH class='STD'>Scan Type</TH>\n";
		echo "  <TH class='STD'># Available</TH>\n";
		echo "  <TH class='STD'>Galaxy</TH>\n";
		echo "  <TH class='STD'>Star</TH>\n";
		echo "  <TH class='STD'>Planet</TH>\n";
		echo "  <TH class='STD'>Continent</TH>\n";
		echo "  <TH class='STD'>Scan</TH>\n";
		echo "</TR>\n";
		echo "<TR>\n"; 
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='use_scan'/>\n";
		echo "     <INPUT type='hidden' name='scan_type' value='r_and_d_scan'/>\n";
		echo "     <INPUT type='hidden' name='view' value='scans'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='active'/>\n";
		echo "  <TD class='STD'>Research and Development Scan</TD>\n";
		echo "  <TD class='STD'>$r_and_d_scans</TD>\n";
		if ($r_and_d_scans > 0) {
			$this->show_galaxy_select();
			$this->show_star_select();
			$this->show_planet_select();
			$this->show_continent_select();
			echo "  <TD class='STD'><INPUT type='submit' name='Scan' value='Scan' /></TD>\n";
		} else {
			echo "  <TD class='STD' colspan='5'><I>No scans available to use</I></TD>\n";
		}
		echo "</FORM>\n";
		echo "</TR>\n";

		echo "<TR>\n"; 
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='use_scan'/>\n";
		echo "     <INPUT type='hidden' name='scan_type' value='continent_scan'/>\n";
		echo "     <INPUT type='hidden' name='view' value='scans'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='active'/>\n";
		echo "  <TD class='STD'>Continent Scan</TD>\n";
		echo "  <TD class='STD'>$continent_scans</TD>\n";
		if ($continent_scans > 0) {
			$this->show_galaxy_select();
			$this->show_star_select();
			$this->show_planet_select();
			$this->show_continent_select();
			echo "  <TD class='STD'><INPUT type='submit' name='Scan' value='Scan'  /></TD>\n";
		} else {
			echo "  <TD class='STD' colspan='5'><I>No scans available to use</I></TD>\n";			
		}
		echo "</FORM>\n";
		echo "</TR>\n";
		
		echo "<TR>\n"; 
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='use_scan'/>\n";
		echo "     <INPUT type='hidden' name='scan_type' value='creature_scan'/>\n";
		echo "     <INPUT type='hidden' name='view' value='scans'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='active'/>\n";
		echo "  <TD class='STD'>Creature Scan</TD>\n";
		echo "  <TD class='STD'>$creature_scans</TD>\n";
		if ($creature_scans > 0) {
			$this->show_galaxy_select();
			$this->show_star_select();
			$this->show_planet_select();
			$this->show_continent_select();
			echo "  <TD class='STD'><INPUT type='submit' name='Scan' value='Scan'  /></TD>\n";
		} else {
			echo "  <TD class='STD' colspan='5'><I>No scans available to use</I></TD>\n";			
		}
		echo "</FORM>\n";
		echo "</TR>\n";
		
		echo "<TR>\n"; 
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='use_scan'/>\n";
		echo "     <INPUT type='hidden' name='scan_type' value='military_scan'/>\n";
		echo "     <INPUT type='hidden' name='view' value='scans'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='active'/>\n";
		echo "  <TD class='STD'>Military Scan</TD>\n";
		echo "  <TD class='STD'>$military_scans</TD>\n";
		if ($military_scans > 0) {
			$this->show_galaxy_select();
			$this->show_star_select();
			$this->show_planet_select();
			$this->show_continent_select();
			echo "  <TD class='STD'><INPUT type='submit' name='Scan' value='Scan'  /></TD>\n";
		} else {
			echo "  <TD class='STD' colspan='5'><I>No scans available to use</I></TD>\n";			
		}
		echo "</FORM>\n";
		echo "</TR>\n";
		
		echo "<TR>\n"; 
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='use_scan'/>\n";
		echo "     <INPUT type='hidden' name='scan_type' value='news_scan'/>\n";
		echo "     <INPUT type='hidden' name='view' value='scans'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='active'/>\n";
		echo "  <TD class='STD'>News Scan</TD>\n";
		echo "  <TD class='STD'>$news_scans</TD>\n";
		if ($news_scans > 0) {
			$this->show_galaxy_select();
			$this->show_star_select();
			$this->show_planet_select();
			$this->show_continent_select();
			echo "  <TD class='STD'><INPUT type='submit' name='Scan' value='Scan'  /></TD>\n";
		} else {
			echo "  <TD class='STD' colspan='5'><I>No scans available to use</I></TD>\n";			
		}
		echo "</FORM>\n";
		echo "</TR>\n";

		echo "<TR>\n"; 
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='use_scan'/>\n";
		echo "     <INPUT type='hidden' name='scan_type' value='planetary_scan'/>\n";
		echo "     <INPUT type='hidden' name='view' value='scans'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='active'/>\n";
		echo "  <TD class='STD'>Planetary Scan</TD>\n";
		echo "  <TD class='STD'>$planetary_scans</TD>\n";
		if ($planetary_scans > 0) {
			$this->show_galaxy_select();
			$this->show_star_select();
			$this->show_planet_select();
			echo "  <TD class='STD'>&nbsp;</TD>\n";
			echo "  <TD class='STD'><INPUT type='submit' name='Scan' value='Scan'  /></TD>\n";
		} else {
			echo "  <TD class='STD' colspan='5'><I>No scans available to use</I></TD>\n";			
		}
		echo "</FORM>\n";
		echo "</TR>\n";


		echo "<TR>\n"; 
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='use_scan'/>\n";
		echo "     <INPUT type='hidden' name='scan_type' value='full_scan'/>\n";
		echo "     <INPUT type='hidden' name='view' value='scans'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='active'/>\n";
		echo "  <TD class='STD'>Full Scan</TD>\n";
		echo "  <TD class='STD'>$full_scans</TD>\n";
		if ($full_scans > 0) {
			$this->show_galaxy_select();
			$this->show_star_select();
			$this->show_planet_select();
			$this->show_continent_select();
			echo "  <TD class='STD'><INPUT type='submit' name='Scan' value='Scan'  /></TD>\n";
		} else {
			echo "  <TD class='STD' colspan='5'><I>No scans available to use</I></TD>\n";			
		}		
		echo "</FORM>\n";
		echo "</TR>\n";
		
		echo "</TABLE>\n";	
	
		
		
	}
	
	function display_monitor_scan_subview() {
 		$player_name=$_SESSION['player_name'];
		$sm = new ScansModel();
		$launch_monitors = $sm->get_number_of_scans_for_player($player_name, "launch_monitor");
		if ($launch_monitors < 0) $launch_monitors = 0;
		$structure_monitors = $sm->get_number_of_scans_for_player($player_name, "structure_monitor");
		if ($structure_monitors < 0) $structure_monitors = 0;
	
		echo "<TABLE class='STD'>\n";
		echo "<TR>\n";
		echo "  <TH class='STD'>Scan Type</TH>\n";
		echo "  <TH class='STD'># Available</TH>\n";
		echo "  <TH class='STD'>Galaxy</TH>\n";
		echo "  <TH class='STD'>Star</TH>\n";
		echo "  <TH class='STD'>Planet</TH>\n";
		echo "  <TH class='STD'>Continent</TH>\n";
		echo "  <TH class='STD'>Scan</TH>\n";
		echo "</TR>\n";
		echo "<TR>\n"; 
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='use_scan'/>\n";
		echo "     <INPUT type='hidden' name='scan_type' value='launch_monitor'/>\n";
		echo "     <INPUT type='hidden' name='view' value='scans'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='active'/>\n";
		echo "  <TD class='STD'>Launch Monitor</TD>\n";
		echo "  <TD class='STD'>$launch_monitors</TD>\n";
		if ($launch_monitors > 0) {
			$this->show_galaxy_select();
			$this->show_star_select();
			$this->show_planet_select();
			$this->show_continent_select();
			echo "  <TD class='STD'><INPUT type='submit' name='Scan' value='Start Monitoring' /></TD>\n";
		} else {
			echo "  <TD class='STD' colspan='5'><I>No scans available to use</I></TD>\n";
		}
		echo "</FORM>\n";
		echo "</TR>\n";

		echo "<TR>\n"; 
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='use_scan'/>\n";
		echo "     <INPUT type='hidden' name='scan_type' value='structure_monitor'/>\n";
		echo "     <INPUT type='hidden' name='view' value='scans'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='active'/>\n";
		echo "  <TD class='STD'>Structure Monitor</TD>\n";
		echo "  <TD class='STD'>$structure_monitors</TD>\n";
		if ($structure_monitors > 0) {
			$this->show_galaxy_select();
			$this->show_star_select();
			$this->show_planet_select();
			$this->show_continent_select();
			echo "  <TD class='STD'><INPUT type='submit' name='Scan' value='Start Monitoring' /></TD>\n";
		} else {
			echo "  <TD class='STD' colspan='5'><I>No scans available to use</I></TD>\n";
		}
		echo "</FORM>\n";
		echo "</TR>\n";

		echo "<BR />";
		$this->show_active_monitors_panel();
	}
		
	function show_galaxy_select() {
		$galaxy = $_REQUEST["galaxy"];
		
		echo "  <TD class='STD'>\n";
		echo 	"   <SELECT name='galaxy'>\n ";
		for ($i=1; $i <= 3; $i++) {
			if ($i == $galaxy)  echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
			else echo 	"   <OPTION value='$i'>$i</OPTION>\n ";
		}
		echo "    </SELECT>\n";
		echo "</TD>\n";
	}

	function show_star_select() {
		$star = $_REQUEST["star"];

		echo "  <TD class='STD'>\n";
		echo 	"   <SELECT name='star'>\n ";
		for ($i=1; $i <= 29; $i++) {
			if ($i == $star)  echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
			echo 	"   <OPTION value='$i'>$i</OPTION>\n ";
		}
		echo "    </SELECT>\n";
		echo "</TD>\n";
	}

	function show_planet_select() {
		$planet = $_REQUEST["planet"];

		echo "  <TD class='STD'>\n";
		echo 	"   <SELECT name='planet'>\n ";
		for ($i=1; $i <= 9; $i++) {
			if ($i == $planet)  echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
			echo 	"   <OPTION value='$i'>$i</OPTION>\n ";
		}
		echo "    </SELECT>\n";
		echo "</TD>\n";
	}

	function show_continent_select() {
		$continent = $_REQUEST["continent"];

		echo "  <TD class='STD'>\n";
		echo 	"   <SELECT name='continent'>\n ";
		for ($i=1; $i <= 9; $i++) {
			if ($i == $continent)  echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
			echo 	"   <OPTION value='$i'>$i</OPTION>\n ";
		}
		echo "    </SELECT>\n";
		echo "</TD>\n";
	}
	
	function get_scan_name_from_type($scan_type) {
		if (strcmp($scan_type, "r_and_d_scan") == 0) return "Research and Development Scan";
		if (strcmp($scan_type, "continent_scan") == 0) return "Continent Scan";
		if (strcmp($scan_type, "creature_scan") == 0) return "Creature Scan";
		if (strcmp($scan_type, "military_scan") == 0) return "Military Scan";
		if (strcmp($scan_type, "planetary_scan") == 0) return "Planetary Scan";
		if (strcmp($scan_type, "news_scan") == 0) return "News Scan";
		if (strcmp($scan_type, "full_scan") == 0) return "Full Scan";
	}

	function display_individual_scan_subview() {
		$scan = $_REQUEST["scan"];
		if (!$scan) return;
		
		$dp = new DescriptionPanel();

		echo "<TABLE class='STD'>";
		echo "<TR>\n";
		echo "<TH class='STD' colspan='2'>Scan Characteristics for $scan </TH>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<TD class='STD' style='height:400px;width=300px;' rowspan='3'><IMG src='images/placeholder.gif' /></TD>\n";
		$this->show_order_panel($scan);
		echo "</TR>";
		echo "<TR><TD class='STD' style='vertical-align:top'>";
		$this->show_stats_panel($scan);
		echo "</TD></TR>";
		echo "<TR><TD class='STD' style='vertical-align:top;text-align:left'>";
		$this->show_dependent_panel($scan);
		echo "</TD></TR>";
		echo "<TR>\n";
		$dp->show_text_panel($scan, "color", "scan", "text-align:center;font-style:italic;");
		$dp->show_text_panel($scan, "basic", "scan", "text-align:left;font-style:normal;");
		echo "</TR>";
		echo "<TR>\n";
		echo "<TH class='STD' colspan='2'>Discussion</TH>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<TD class='STD' colspan='2' height='200'>Discussion Content</TD>\n";
		echo "</TR>";
		echo "</TABLE>";
	}

	function show_order_panel($scan) {
		$player_name = $_SESSION['player_name'];
		$dm = new DevelopmentModel();

		$si = new ScanItem();
		$si->db_fill($scan);
		if ($dm->does_player_know_development($player_name, $si->dependent_development) ) {
			$this->display_ordering_bar($scan);
		} else {
			echo " <TD class='STD' style='background-color:882222'>Cannot Order this Scan until $si->dependent_development is developed.</TD>";
		}
	}

	function display_ordering_bar($scan) {
		$player_name = $_SESSION['player_name'];
		$sm = new ScansModel();

		$si = new ScanItem();
		$si->db_fill($scan);

		$pd = new PlayerData();
		$pd->db_fill($player_name);
		$max_mineral = floor($pd->mineral / $si->mineral); 
		$max_energy = floor($pd->energy / $si->energy); 
		$true_max = min($max_mineral, $max_energy);
		$current_scans =  $sm->get_number_of_scans_for_player($player_name, $scan);
		if ($current_scans < 0) $current_scans = 0;
		
		echo "<FORM method='post' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_scans'/>\n";
		echo "     <INPUT type='hidden' name='view' value='scans'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='individual'/>\n";
		echo "     <INPUT type='hidden' name='scan' value='$scan'/>\n";
		echo " <TD class='STD'>";
		echo "   <TABLE class='STD' style='width:100%'>\n";
		echo "     <TR><TH class='STD'>You Own</TH>";
		echo "         <TH class='STD'>Max You Can Make</TH>";
		echo "         <TH class='STD'>To Make</TH>";
		echo "         <TH class='STD'>&nbsp;</TH></TR>  \n";
		echo "     <TR><TD class='STD'>$current_scans</TD>";
		echo "     <TD class='STD'>$true_max</TD>\n";
		echo "     <TD class='STD'><INPUT type='text' size='6' name='$scan' /></TD>\n";
		echo "         <TD class='STD'><INPUT type='submit' value='Create' /></TD></TR>  \n";
		echo "   </TABLE> \n";
		echo " </TD>";
		echo "</FORM>\n";
		
	}
		
	function show_stats_panel($scan) {
		$player_name = $_SESSION['player_name'];
		$si = new ScanItem();
		$si->db_fill($scan);
		
		echo "<TABLE class='STATS' style='width:530px;'>";
		echo "<TR><TD class='STATS'>Mineral</TD><TD class='STATS'>$si->mineral</TD></TR>";
		echo "<TR><TD class='STATS'>Energy</TD><TD class='STATS'>$si->energy</TD></TR>";
		echo "<TR><TD class='STATS'>Ticks</TD><TD class='STATS'>$si->ticks</TD></TR>";
		echo "</TABLE>";
	}

	function show_dependent_panel($scan) {
		$si = new ScanItem();
		$si->db_fill($scan);

		echo "Required Development:<BR />\n";
		echo "<UL>\n";
		echo "<LI><A style='color:white' href='main_page.php5?view=development&subview=individual&development_item=$si->dependent_development'>$si->dependent_development</A></LI>\n";
		echo "</UL>\n";	
	}

	function show_active_monitors_panel() {
		$player_name = $_SESSION['player_name'];
		$gm = new GameModel();
		$sm = new ScansModel();
		$current_tick = $gm->get_current_tick();
		
		$structure_monitors = $sm->get_all_active_monitors_by_type($player_name, "structure", $current_tick);
		$launch_monitors = $sm->get_all_active_monitors_by_type($player_name, "launch", $current_tick);
		
		echo "<TABLE><TR>";
		echo "<TD style='vertical-align:top;'>  <TABLE class='STD' style='width:420px'><TR><TH class='STD'>Target Player</TH><TH class='STD'>From Tick</TH><TH class='STD'>Until Tick</TH>\n";
		echo "<CAPTION class='STD'>Structure Monitors</CAPTION>";
		for ($i=0;$i<count($structure_monitors);$i++) {
			$target_name = $structure_monitors[$i]["target_name"];
			$start_tick = $structure_monitors[$i]["start_tick"];
			$until_tick = $structure_monitors[$i]["until_tick"];
			echo "    <TR><TD class='STD'>$target_name</TD><TD class='STD'>$start_tick</TD><TD class='STD'>$until_tick</TD>\n";
		}
		echo "  </TABLE></TD>";

		echo "<TD style='vertical-align:top;'>  <TABLE class='STD' style='width:420px'><TR><TH class='STD'>Target Player</TH><TH class='STD'>From Tick</TH><TH class='STD'>Until Tick</TH>\n";
		echo "<CAPTION class='STD'>Launch Monitors</CAPTION>";
		for ($i=0;$i<count($launch_monitors);$i++) {
			$target_name = $launch_monitors[$i]["target_name"];
			$start_tick = $launch_monitors[$i]["start_tick"];
			$until_tick = $launch_monitors[$i]["until_tick"];
			echo "    <TR><TD class='STD'>$target_name</TD><TD class='STD'>$start_tick</TD><TD class='STD'>$until_tick</TD>\n";
		}
		echo "  </TABLE></TD>";


		echo "</TR></TABLE>";
	}
}
?>