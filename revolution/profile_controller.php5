<?php 
	require_once('player_data.php5'); 
	require_once('email_preferences_data.php5'); 

class ProfileController {
	function modify_email() {
 		$player_name=$_SESSION['player_name'];
		$new_email = $_REQUEST["new_email"];
		
		$pd = new PlayerData();	
		$pd->update_email ($player_name, $new_email);
		show_info("Your email has been updated");
	}
	
	function update_email_preferences() {
 		$player_name=$_SESSION['player_name'];
		$epd = new EmailPreferencesData();
		$epd->player_name = $player_name;

		if ($_REQUEST['email_on_launch'] == 'on') $epd->email_on_launch = 'true'; else $epd->email_on_launch = 'false';
		if ($_REQUEST['email_on_recall'] == 'on') $epd->email_on_recall= 'true'; else $epd->email_on_recall = 'false';
		if ($_REQUEST['email_on_battle'] == 'on') $epd->email_on_battle = 'true'; else $epd->email_on_battle = 'false';
		if ($_REQUEST['email_on_bombs'] == 'on') $epd->email_on_bombs = 'true'; else $epd->email_on_bombs = 'false';
		if ($_REQUEST['email_on_scans'] == 'on') $epd->email_on_scans = 'true'; else $epd->email_on_scans = 'false';
		if ($_REQUEST['email_on_pulses'] == 'on') $epd->email_on_pulses = 'true'; else $epd->email_on_pulses = 'false';
		if ($_REQUEST['email_on_completed_research'] == 'on') $epd->email_on_completed_research = 'true'; else $epd->email_on_completed_research = 'false';
		if ($_REQUEST['email_on_completed_development'] == 'on') $epd->email_on_completed_development = 'true'; else $epd->email_on_completed_development = 'false';
		if ($_REQUEST['email_on_completed_creatures'] == 'on') $epd->email_on_completed_creatures = 'true'; else $epd->email_on_completed_creatures = 'false';
		if ($_REQUEST['email_on_completed_scans'] == 'on') $epd->email_on_completed_scans = 'true'; else $epd->email_on_completed_scans = 'false';
		if ($_REQUEST['email_on_completed_bombs'] == 'on') $epd->email_on_completed_bombs = 'true'; else $epd->email_on_completed_bombs = 'false';
		if ($_REQUEST['email_on_completed_pulses'] == 'on') $epd->email_on_completed_pulses = 'true'; else $epd->email_on_completed_pulses = 'false';
		if ($_REQUEST['email_on_traps'] == 'on') $epd->email_on_traps = 'true'; else $epd->email_on_traps = 'false';
		if ($_REQUEST['email_on_launch_monitor'] == 'on') $epd->email_on_launch_monitor = 'true'; else $epd->email_on_launch_monitor = 'false';
		if ($_REQUEST['email_on_any_milestone'] == 'on') $epd->email_on_any_milestone = 'true'; else $epd->email_on_any_milestone = 'false';
		if ($_REQUEST['email_on_any_victory'] == 'on') $epd->email_on_any_victory = 'true'; else $epd->email_on_any_victory = 'false';
		if ($_REQUEST['email_on_any_shield'] == 'on') $epd->email_on_any_shield = 'true'; else $epd->email_on_any_shield = 'false';
		if ($_REQUEST['email_on_any_alliance'] == 'on') $epd->email_on_any_alliance = 'true'; else $epd->email_on_any_alliance = 'false';
		
		$epd->update();
		show_info("Your preferences have been updated");
	}
}
?>