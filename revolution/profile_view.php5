<?php
	require_once('player_data.php5'); 
	require_once('email_preferences_data.php5'); 
	require_once('description_panel.php5'); 
	require_once('view_fns.php5'); 
	
class ProfileView {

	function display_profile_view() {
		$player_name = $_SESSION['player_name'];
		$profile_name = $_REQUEST['profile_name'];
		if ($profile_name == '') $profile_name = $player_name;
		
		if ($profile_name == $player_name) $this->display_profile_as_player();
		else $this->display_profile_as_non_player();
				
	}
	
	function display_profile_as_player() {
		$player_name = $_SESSION['player_name'];
		$this->display_email_preferences();
		echo "<BR/>\n";
		$dp = new DescriptionPanel();
		echo "<TABLE class='STD'>";
		echo "<TH class='STD' colspan='2'>Your Personal Description</TH>\n";
		echo "<TR>\n";
		echo "<TD class='STD' style='vertical-align:top'>";
		$dp->show_text_panel_inside($player_name, "profile", "profile", "");
		echo "</TD>";
		echo "</TR>";
		echo "</TABLE>";
	}
	
	function display_email_preferences() {
		$player_name = $_SESSION['player_name'];
		$pd = new PlayerData();
		$pd->db_fill($player_name);
		$epd = new EmailPreferencesData();
		$epd->populate($player_name);
		echo "<TABLE class='STD'>";
		echo "<TR><TH class='STD' colspan='3'>Email Preferences</TH></TR>\n";
		echo "<TR><TD class='STD' colspan='3'>To set email from forum pages, please goto the profile of the forum</TD></TR>\n";
		echo "<TR>\n";
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='view' value='profile'/>\n";
		echo "     <INPUT type='hidden' name='action' value='modify_email'/>\n";
		echo "<TD class='STD'>$pd->email</TD>";
		echo "<TD class='STD'><INPUT type='text' name='new_email' size='80'/></TD>";
		echo "<TD class='STD'><INPUT type='submit' value='Modify' /></TD>";
		echo "</FORM>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='view' value='profile'/>\n";
		echo "     <INPUT type='hidden' name='action' value='update_email_preferences'/>\n";
		echo "<TD class='STD' style='vertical-align:top;'>Email Preferences</TD>";
		echo "<TD class='STD' style='text-align:left;'>\n";
		echo "  <INPUT type='checkbox' name='email_on_launch' " . $this->show_checked("$epd->email_on_launch") . " /> I would like to get emails when I launch or am the target of a launch (active) <br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_recall' " . $this->show_checked("$epd->email_on_recall") . " /> I would like to get emails when I recall or a fleet targeting me is recalled (active) <br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_battle' " . $this->show_checked("$epd->email_on_battle") . " /> I would like to get emails when I am involved in a battle (active)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_bombs' " . $this->show_checked("$epd->email_on_bombs") . " /> I would like to get emails when I drop bombs or poison bombs (disabled)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_scans' " . $this->show_checked("$epd->email_on_scans") . " /> I would like to get emails when I detect a scan (disabled)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_pulses' " . $this->show_checked("$epd->email_on_pulses") . " /> I would like to get emails when I am the target of a pulse, blast, shield, or jammer (disabled)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_completed_research' " . $this->show_checked("$epd->email_on_completed_research") . " /> I would like to get emails when I complete research (active)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_completed_development' " . $this->show_checked("$epd->email_on_completed_development") . " /> I would like to get emails when I complete development (active)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_completed_creatures' " . $this->show_checked("$epd->email_on_completed_creatures") . " /> I would like to get emails when I complete creatures (active)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_completed_scans' " . $this->show_checked("$epd->email_on_completed_scans") . " /> I would like to get emails when I complete scans (active)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_completed_bombs' " . $this->show_checked("$epd->email_on_completed_bombs") . " /> I would like to get emails when I complete forts or bombs (active)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_completed_pulses' " . $this->show_checked("$epd->email_on_completed_pulses") . " /> I would like to get emails when I complete pulses, blasts, sheilds, or jammers (active)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_traps' " . $this->show_checked("$epd->email_on_traps") . " /> I would like to get emails when my traps or psycholgical traps are triggered (disabled)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_launch_monitor' " . $this->show_checked("$epd->email_on_launch_monitor") . " /> I would like to get emails when my launch monitor detects a launch (disabled)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_any_milestone' " . $this->show_checked("$epd->email_on_any_milestone") . " /> I would like to get emails when anyone reaches a new milestone (disabled)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_any_victory' " . $this->show_checked("$epd->email_on_any_victory") . " /> I would like to get emails when anyone completes a victory condition (disabled)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_any_shield' " . $this->show_checked("$epd->email_on_any_shield") . " /> I would like to get emails when anyone activates a shield or jammer (disabled)<br/>\n";
		echo "  <INPUT type='checkbox' name='email_on_any_alliance' " . $this->show_checked("$epd->email_on_any_alliance") . " /> I would like to get emails when any alliance makes a new declaration (disabled)<br/>\n";
		echo "</TD>";
		echo "<TD class='STD' style='vertical-align:bottom;'><INPUT type='submit' value='Update Preferences' /></TD>";
		echo "</FORM>\n";
		echo "</TR>";
		echo "</TABLE>";
			
	}
	
	function show_checked($name) {
		if ($name == 1) return "checked='$name' ";
		else return "";
	}
	
	function display_profile_as_non_player() {
		$dp = new DescriptionPanel();
		$pd = new PlayerData();
		$vf = new ViewFunctions();
		
		$profile_name = $_REQUEST['profile_name'];
		
		list ($galaxy, $star, $planet, $continent) = $pd->get_location_from_player_name($profile_name); 
		echo "<TABLE class='STD'>";
		echo "<TH class='STD' colspan='2'>Personal Description for $profile_name</TH>\n";
		echo "<TR>\n";
		echo "<TD class='STD' style='vertical-align:top'>";
		$dp->show_text_panel_uneditable_inside($profile_name, "profile", "profile", "");
		echo "</TD>";
		echo "</TR>";
		echo "</TABLE>";

		echo "<BR />\n";
		echo "<TABLE class='STD'>";
		echo "<TH class='STD' colspan='2'>Possible Actions</TH>\n";
		echo "<TR>\n";
		$vf->display_button("Attack $profile_name <I>(Goto the Fleets page with location autochecked)</I>", "804020", "A06040", 
				"main_page.php5?view=fleets&galaxy=$galaxy&star=$star&planet=$planet&continent=$continent");
		echo "</TR>";
		echo "<TR>\n";
		$vf->display_button("Scan $profile_name <I>(Goto the Scans page with location autochecked)</I>", "804020", "A06040", 
				"main_page.php5?view=scans&subview=remote&galaxy=$galaxy&star=$star&planet=$planet&continent=$continent");
		echo "</TR>";
		echo "<TR>\n";
		$vf->display_button("Monitor $profile_name <I>(Goto the Monitor page with location autochecked)</I>", "804020", "A06040", 
				"main_page.php5?view=scans&subview=monitor&galaxy=$galaxy&star=$star&planet=$planet&continent=$continent");
		echo "</TR>";
		echo "<TR>\n";
		$vf->display_button("Use a Blast against $profile_name <I>(Goto the Pulses/Blasts page with location autochecked)</I>", "804020", "A06040", 
				"main_page.php5?view=pulses&galaxy=$galaxy&star=$star&planet=$planet&continent=$continent");
		echo "</TR>";

		echo "<TR>\n";
		echo "</TR>";
		echo "</TABLE>";
	}

}

?>