<?php
	require_once('db_fns.php5'); 
	require_once('player_data.php5'); 
	require_once('view_fns.php5'); 
	require_once('fleet_model.php5'); 
	require_once('fleet_view.php5'); 
	require_once('forts_model.php5'); 

	require_once('research_view.php5'); 
	require_once('development_view.php5'); 
	require_once('creatures_view.php5'); 
	require_once('scans_view.php5'); 
	require_once('pulses_view.php5'); 
	require_once('forts_view.php5'); 
	require_once('bombs_view.php5'); 
	require_once('description_panel.php5'); 
	require_once('creatures_model.php5');

	require_once('chat_model.php5');
	require_once ("/home/content/b/r/e/bremonline/html/revo_smf/SSI.php");
	 

class OverviewView {
	function display_overview() {
		echo "<TABLE>\n";
		echo "<TR>";
		echo "<TD style='vertical-align:top;'>";
		$this->display_motd();
		echo "<BR />\n";
		$this->display_recent_forum_topics();
		echo "</TD>";
		echo "<TD style='vertical-align:top;'>";
		$this->display_communication_elements();
		echo "</TD>";
		echo "</TR>";

		echo "<TR>";
		echo "<TD colspan='2'>";		
		$this->display_incoming();	
		echo "</TD>";
		echo "</TR>";


		echo "<TR>";
		echo "<TD colspan='2'>";
		$fleetView = new FleetView();
		$fleetView->display_fleets_on_mission();
		echo "</TD>";
		echo "</TR>";

		echo "<TR>";
		echo "<TD style='vertical-align:top;'>";
		$this->display_current_items();
		echo "</TD>";
		echo "<TD style='vertical-align:top;'>";
		$this->display_current_builds();
		echo "</TD>";
		echo "</TR>";



		echo "</TABLE>\n";
	}
	
	function display_motd() {
		$player_name = $_SESSION['player_name'];

		$dp = new DescriptionPanel();
		$pd = new PlayerData();
		
		echo "<TABLE class='STD' style='width:420;'>";
		echo "<TH class='STD'>Message of the Day</TH>\n";
		echo "<TR>\n";
		echo "<TD class='STD' style='vertical-align:top'>";
		if ($pd->is_admin($player_name) ) {
			$dp->show_text_panel_inside("motd", "overview", "overview", "");
		} else {
			$dp->show_text_panel_uneditable_inside("motd", "overview", "overview", "");
		}
		echo "</TD>";
		echo "</TR>";
		echo "</TABLE>";
	}
	
	function display_communication_elements() {
		$this->display_last_chat_message();
		$this->display_last_alliance_chat_message();
	}
	
	function display_recent_forum_posts() {
		$news = ssi_boardNews(null, null, null, null, "array"); 
		
		
		$count = count($news);

		echo "<TABLE class='STD' style='width:420px;'>";
		echo "<TH class='STD'>General Forum Topics</TH>\n";
		echo "<TR>\n";
		echo "<TD class='STD' style='vertical-align:top;text-align:left'>";
		
		for ($i=0;$i<$count;$i++) {
			$subject = $news[$i]['subject'];
			$replies = $news[$i]['replies'];
			$href = $news[$i]['href'];
			echo "<A href='$href' target='_forum'>$subject ($replies)</A><BR />\n";
		}
		echo "</TD>";
		echo "</TR>";
		echo "</TABLE>";
	}

	function display_recent_forum_topics() {
		$posts = ssi_recentTopics(8, null, "array"); 

		echo "<TABLE class='STD' style='width:420px;'>";
		echo "<TH class='STD'>General Forum Topics</TH>\n";
		echo "<TR>\n";
		echo "<TD class='STD' style='vertical-align:top;text-align:left'>";
		foreach ($posts as $post) {
			$board_name = $post['board']['name'];
			$board_href = $post['board']['href'];
			$topic = $post['topic'];
			$short_subject = $post['short_subject'];
			$poster = $post['poster']['name'];
			$time = $post['time'];
			$new = $post['new'];
			$topic_href = $post['href'];
			if (! $post['new']) $is_new_image = "<A href='$topic_href' target=_forum' ><img src='images/new.gif' alt='new' border='0'></A>";
			else $is_new_image = "";
			echo "<span ><A href='$board_href' target=_forum' style='text-decoration:none;color:grey;''>[$board_name]</A> </span><B><A href='$topic_href' target=_forum' style='text-decoration:none;color:white;'>$short_subject</A></B> $is_new_image</BR />\n";
		}
		echo "</TD>";
		echo "</TR>";
		echo "</TABLE>";
	}
	
	function display_last_chat_message() {
		$cm = new ChatModel();
		$msg = $cm->get_last_general_message();
		echo "<TABLE class='STD' style='width:420px;'>";
		echo "<TH class='STD'>Last Chat Message</TH>\n";
		echo "<TR>\n";
		echo "<TD class='STD' style='vertical-align:top;text-align:left'>";
		echo $msg;
		echo "<BR /><BR /><A href='chat/chat_page.php5' target='revo_chat' />Open Chat in a new tab </A><BR />";
		echo "</TD>";
		echo "</TR>";
		echo "</TABLE>";
		
	}

	function display_last_alliance_chat_message() {
		$cm = new ChatModel();
		$am = new AllianceModel();
		
		$player_name = $_SESSION["player_name"];
		
		$alliance = $am->get_alliance_of_player($player_name);
		$msg = $cm->get_last_alliance_message($alliance);
		echo "<TABLE class='STD' style='width:420px;'>";
		echo "<TH class='STD'>Last Alliance Message ($alliance) </TH>\n";
		echo "<TR>\n";
		echo "<TD class='STD' style='vertical-align:top;text-align:left'>";
		echo $msg;
		echo "</TD>";
		echo "</TR>";
		echo "</TABLE>";
		
	}

	function display_current_items() {
		echo $this->get_current_items_display();
	}

	function get_current_items_display() {
		$string = "";
		
		$string .= "<TABLE class='STD' style='width:420;'>\n";
		$string .= " <TR><TH class='STD'>Current Creatures, Scans, and Items</TH></TR>\n";
		$string .= " <TR><TD class='STD' style='text-align:left;background-color:003000'>\n";
		$string .= $this->display_current_creatures();
		$string .= $this->display_current_infrastructure();
		$string .= $this->display_current_scans();
		$string .= $this->display_current_pulses();
		$string .= $this->display_current_bombs();
		$string .= $this->display_current_forts();
		$string .= "  </TD>\n";
		$string .= " </TR>\n";
		$string .= "</TABLE>\n";		
		return $string;
	}

	function display_current_creatures() {
		$player_name = $_SESSION['player_name'];
		$cm = new CreaturesModel();
		$creature_list = $cm->get_creature_list_for_player($player_name);
		$t = $cm->get_all_creature_totals_for_player($player_name);
		$att = $t["att"]; $def = $t["def"]; $foc = $t["foc"]; $int = $t["int"]; $dis = $t["dis"];

		$string = "";
		
		$string .= "Creatures: {$att}A/ {$def}D/ {$foc}F/ {$int}i/ {$dis}d ";
		$string .=  "<UL> ";
		if ($creature_list) {
			foreach ($creature_list as $creature => $number) {
				$string .=  "<LI>$creature: $number</LI> ";
			}
		} else {
				$string .=  "<LI>No Creatures Available</LI> ";
		}
		$string .=  "</UL> ";
		
		return $string;
		
	}

	function display_current_infrastructure() {
		$player_name = $_SESSION['player_name'];
		$sm = new ScansModel();
		$pm = new PulsesModel();
		$string = "";


		$string .= "Infrastructure: ";
		$string .= "<UL> ";
			if ($sm->get_number_of_scans_for_player($player_name, "survey_tuner") > 0 ) 
				$string .= "<LI>Survey Tuners: " . $sm->get_number_of_scans_for_player($player_name, "survey_tuner") . "</LI>";
			if ($sm->get_number_of_scans_for_player($player_name, "scan_amplifier") > 0 ) 
				$string .= "<LI>Scan Amplifiers: " . $sm->get_number_of_scans_for_player($player_name, "scan_amplifier") . "</LI>";
			if ($sm->get_number_of_scans_for_player($player_name, "noise_generator") > 0 ) 
				$string .= "<LI>Noise Generators: " . $sm->get_number_of_scans_for_player($player_name, "noise_generator") . "</LI>";
			if ($sm->get_number_of_scans_for_player($player_name, "scan_sensor") > 0 ) 
				$string .= "<LI>Scan Sensors: " . $sm->get_number_of_scans_for_player($player_name, "scan_sensor") . "</LI>";
			if ($sm->get_number_of_scans_for_player($player_name, "scan_filter") > 0 ) 
				$string .= "<LI>Scan Filters: " . $sm->get_number_of_scans_for_player($player_name, "scan_filter") . "</LI>";
			if ($pm->get_number_pulses($player_name, "Modulator") > 0 ) 
				$string .= "<LI>Pulse Modulators: " . $pm->get_number_pulses($player_name, "Modulator") . "</LI>";
			if ($pm->get_number_pulses($player_name, "Reflector") > 0 ) 
				$string .= "<LI>Pulse Reflectors: " . $pm->get_number_pulses($player_name, "Reflector") . "</LI>";
		$string .= "</UL> ";

		$string .= "</UL> ";	
		return $string;
	}
	
	function display_current_scans() {
		$player_name = $_SESSION['player_name'];
		$sm = new ScansModel();
		$pm = new PulsesModel();
		$string = "";

		$string .= "Scans: ";
		$string .= "<UL> ";
			if ($sm->get_number_of_scans_for_player($player_name, "site_scan") > 0 ) 
				$string .= "<LI>Site Scans: " . $sm->get_number_of_scans_for_player($player_name, "site_scan") . "</LI>";
			if ($sm->get_number_of_scans_for_player($player_name, "r_and_d_scan") > 0 ) 
				$string .= "<LI>R & D Scans: " . $sm->get_number_of_scans_for_player($player_name, "r_and_d_scan") . "</LI>";
			if ($sm->get_number_of_scans_for_player($player_name, "continent_scan") > 0 ) 
				$string .= "<LI>Continent Scans: " . $sm->get_number_of_scans_for_player($player_name, "continent_scan") . "</LI>";
			if ($sm->get_number_of_scans_for_player($player_name, "creature_scan") > 0 ) 
				$string .= "<LI>Creature Scans: " . $sm->get_number_of_scans_for_player($player_name, "creature_scan") . "</LI>";
			if ($sm->get_number_of_scans_for_player($player_name, "military_scan") > 0 ) 
				$string .= "<LI>Military Scans: " . $sm->get_number_of_scans_for_player($player_name, "military_scan") . "</LI>";
			if ($sm->get_number_of_scans_for_player($player_name, "planetary_scan") > 0 ) 
				$string .= "<LI>Planetary Scans: " . $sm->get_number_of_scans_for_player($player_name, "planetary_scan") . "</LI>";
			if ($sm->get_number_of_scans_for_player($player_name, "news_scan") > 0 ) 
				$string .= "<LI>News Scans: " . $sm->get_number_of_scans_for_player($player_name, "news_scan") . "</LI>";
			if ($sm->get_number_of_scans_for_player($player_name, "full_scan") > 0 ) 
				$string .= "<LI>Full Scans: " . $sm->get_number_of_scans_for_player($player_name, "full_scan") . "</LI>";
			if ($sm->get_number_of_scans_for_player($player_name, "launch_monitor") > 0 ) 
				$string .= "<LI>Launch Monitors: " . $sm->get_number_of_scans_for_player($player_name, "launch_monitor") . "</LI>";
			if ($sm->get_number_of_scans_for_player($player_name, "structure_monitor") > 0 ) 
				$string .= "<LI>Structure Monitors: " . $sm->get_number_of_scans_for_player($player_name, "structure_monitor") . "</LI>";
		$string .= "</UL> ";

		$string .= "</UL> ";	
		return $string;
	}

	function display_current_pulses() {
		$player_name = $_SESSION['player_name'];
		$sm = new ScansModel();
		$pm = new PulsesModel();
		$string = "";

		$string .= "Pulses/Blasts/Shields/Jammers: ";
		$string .= "<UL> ";
			if ($pm->get_number_pulses($player_name, "Electromagnetic Pulse") > 0 ) 
				$string .= "<LI>Electromagnetic Pulses: " . $pm->get_number_pulses($player_name, "Electromagnetic Pulse") . "</LI>";
			if ($pm->get_number_pulses($player_name, "Microwave Pulse") > 0 ) 
				$string .= "<LI>Micrwowave Pulses: " . $pm->get_number_pulses($player_name, "Microwave Pulse") . "</LI>";
			if ($pm->get_number_pulses($player_name, "Electromagnetic Shield") > 0 ) 
				$string .= "<LI>Electromagnetic Shields: " . $pm->get_number_pulses($player_name, "Electromagnetic Shield") . "</LI>";
			if ($pm->get_number_pulses($player_name, "Microwave Shield") > 0 ) 
				$string .= "<LI>Microwave Shields: " . $pm->get_number_pulses($player_name, "Microwave Shield") . "</LI>";
			if ($pm->get_number_pulses($player_name, "Electromagnetic Blast") > 0 ) 
				$string .= "<LI>Electromagnetic Blasts: " . $pm->get_number_pulses($player_name, "Electromagnetic Blast") . "</LI>";
			if ($pm->get_number_pulses($player_name, "Microwave Blast") > 0 ) 
				$string .= "<LI>Microwave Blasts: " . $pm->get_number_pulses($player_name, "Microwave Blast") . "</LI>";
			if ($pm->get_number_pulses($player_name, "Command Jammer") > 0 ) 
				$string .= "<LI>Command Jammers: " . $pm->get_number_pulses($player_name, "Command Jammer") . "</LI>";
		$string .= "</UL> ";
		return $string;
	}

	function display_current_bombs() {
		$player_name = $_SESSION['player_name'];
		$bm = new BombsModel();
		$string = "";

		$string .= "Bombs/Traps: ";
		$string .= "<UL> ";
			if ($bm->get_number_bombs($player_name, "Bomb") > 0 ) 
				$string .= "<LI>Bombs: " . $bm->get_number_bombs($player_name, "Bomb") . "</LI>";
			if ($bm->get_number_bombs($player_name, "Poison Bomb") > 0 ) 
				$string .= "<LI>Poison Bombs: " . $bm->get_number_bombs($player_name, "Poison Bomb") . "</LI>";
			if ($bm->get_number_bombs($player_name, "Trap") > 0 ) 
				$string .= "<LI>Traps: " . $bm->get_number_bombs($player_name, "Trap") . "</LI>";
			if ($bm->get_number_bombs($player_name, "Psychological Trap") > 0 ) 
				$string .= "<LI>Psychological Traps: " . $bm->get_number_bombs($player_name, "Psychological Trap") . "</LI>";
		$string .= "</UL> ";
		return $string;
	}

	function display_current_forts() {
		$player_name = $_SESSION['player_name'];
		$fm = new FortsModel();
		$string = "";

		$stats = $fm->get_fort_stats($player_name);
		$att = $stats["att"];
		$def = $stats["def"];
		$survive = $stats["survive"];

		$string .= "Forts: {$att}a/{$def}d Survive: {$survive}% ";
		$string .= "<UL> ";
			if ($fm->get_number_forts($player_name) > 0 ) 
				$string .= "<LI>Forts: " . $fm->get_number_forts($player_name) . "</LI>";
		$string .= "</UL> ";
		return $string;
	}
		
	
	function display_current_builds() {
		echo $this->make_current_builds_display();
	}
	
	function make_current_builds_display() {
		$rv = new ResearchView();
		$dv = new DevelopmentView();
		$cv = new CreaturesView();
		$sv = new ScansView();
		$pv = new PulsesView();
		$fv = new FortsView();
		$bv = new BombsView();

		$string = "";
		
		$string .= "<TABLE class='STD' style='width:420;'>\n";
		$string .= " <TR><TH class='STD'>Current Research, Development, and Builds</TH></TR>\n";
		$string .= " <TR><TD class='STD' style='text-align:left;background-color:003000'>\n";
		$string .= $rv->make_current_research_display();
		$string .= $dv->make_current_development_display();
		$string .= $cv->make_current_creature_build_display();
		$string .= $sv->make_current_scan_build_display();
		$string .= $fv->make_current_fort_build_display();
		$string .= $pv->make_current_pulse_build_display();
		$string .= $bv->make_current_bomb_build_display();
		$string .= "  </TD>\n";
		$string .= " </TR>\n";
		$string .= "</TABLE>\n";		
		
		return $string;
	}
	
	function display_incoming() {
		$player_name = $_SESSION['player_name'];
		$vf = new ViewFunctions();
		$pd = new PlayerData();
		
		$fm = new FleetModel();
		$incoming_list = $fm->get_incoming($player_name);
		
		if (count($incoming_list) > 0) {
			echo "<TABLE class='STD'>\n";
			echo "<TR >";
			echo "	<TH class='STD'> Scan Player </TH>";
			echo "	<TH class='STD'> Launch Tick </TH>";
			echo "	<TH class='STD'> Arrival Tick </TH>";
			echo "	<TH class='STD'> Depart Tick </TH>";
			echo "	<TH class='STD'> Return Tick </TH>";
			echo "	<TH class='STD'> Mission </TH>";
			echo "	<TH class='STD'> Fleet </TH>";
			echo "</TR>\n";
			echo "<TR >";
			for ($a=0; $a < count($incoming_list); $a++) {
				$launcher_name = $incoming_list[$a]["launcher_name"];
				$launch_tick = $incoming_list[$a]["launch_tick"];
				$arrival_tick = $incoming_list[$a]["arrival_tick"];
				$depart_tick = $incoming_list[$a]["depart_tick"];
				$return_tick = $incoming_list[$a]["return_tick"];
				$mission_type = $incoming_list[$a]["mission_type"];
				$fleet = $incoming_list[$a]["fleet"];
				if (strcmp($mission_type, "attack") == 0) { $color = "802020"; $hcolor = "A04040"; }
				else { $color = "208020"; $hcolor = "40A040"; }

				$pd->db_fill($launcher_name);
				
//				$vf->display_button("$launcher_name", "$color", "$hcolor", "main_page.php5?view=profile&profile_name=$launcher_name");
				echo "	<TD class='player STD' style='background-color:$color;' player='$launcher_name'> $launcher_name ";
				display_quick_action_box($launcher_name, $pd->galaxy, $pd->star, $pd->planet, $pd->continent, $pd->smf_id);
				echo "</TD>";

				echo "	<TD class='STD' style='background-color:$color'> $launch_tick </TD>";
				echo "	<TD class='STD' style='background-color:$color'> $arrival_tick </TD>";
				echo "	<TD class='STD' style='background-color:$color'> $depart_tick </TD>";
				echo "	<TD class='STD' style='background-color:$color'> $return_tick </TD>";
				echo "	<TD class='STD' style='background-color:$color'> $mission_type </TD>";
				echo "	<TD class='STD' style='background-color:$color'> $fleet </TD>";
				echo "</TR>\n";
			}
			echo "</TABLE\n";

		} 
	}
}
?>