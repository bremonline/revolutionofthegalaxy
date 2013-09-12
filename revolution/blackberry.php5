<?php
	require_once('misc_fns.php5'); 
	require_once('player_data.php5'); 
	require_once('page_controller.php5'); 
	require_once('game_model.php5'); 
	require_once('fleet_model.php5'); 
	
	session_start();
	check_valid_user();
  $action=$_REQUEST['action'];
	if (strcmp($action, 'logout') == 0) {
		echo "You are now logged out.  To login again please go to the <A href='login.php5>Login Page</A><BR />\n";
		exit();
	}
	

	echo "<html>\n";
	
	echo "
<style type='text/css'>

#score{
color:blue;
text-align:right;
}

.w{
color:white;
}

.g{
color:grey;
}

.r{
color:red;
}

.lr{
color:#F88;
}

.lg{
color:#8F8;
}

.info{
color:lightgreen;
background-color:#131;
border-color:lightgreen;
border-width:1px;
border-style:solid;
}

.warning{
color:#DD8;
background-color:#331;
border-color:#DD8;
border-width:1px;
border-style:solid;
}

.error{
color:#D88;
background-color:#311;
border-color:#D88;
border-width:1px;
border-style:solid;
}
</style>";

  
  echo "<body style='background-color:#335;'>\n";

	$pc = new PageController();
	$pc->perform_action();
	
  $player_name=$_SESSION['player_name'];
	$pd = new PlayerData();
	$pd->touch($player_name);

	$gm = new GameModel();
	$gm->archive_click();

	echo "<br />\n";

	display_stats();
	
	display_info_bar();	
	display_warning_bar();	
	display_error_bar();	
	echo "<hr style='color:yellow;' />\n";
	display_incoming();
	display_launch_fleets();
	display_recall_fleets();
	display_research_chooser();
	display_development_chooser();
	display_structures();
	display_forum_links();

	echo "</body>\n";
	echo "</html>\n";

function display_stats() {
  $player_name = $_SESSION["player_name"];
	$pd = new PlayerData();
	$pd->touch($player_name);
	$pd->db_fill($player_name);
	echo "<TABLE><TR><TD><IMG src='revo-logo.gif' /></TD><TD>\n";

	echo "<SPAN class='w'><B>$player_name</B></SPAN>&nbsp;&nbsp;&nbsp;<SPAN id='score' align='right'><B>" . number_format($pd->score) . "</SPAN><BR />\n";;
	echo "<SPAN class='w'><B>" . number_format($pd->extractor) . "</B></SPAN><SPAN class='g'>e</SPAN>&nbsp;&nbsp;&nbsp;<SPAN class='w'>" . number_format($pd->mineral) . "</SPAN><SPAN class='g'>m</SPAN><BR />\n";
	echo "<SPAN class='w'><B>" . number_format($pd->genetic_lab) . "</B></SPAN><SPAN class='g'>g</SPAN>&nbsp;&nbsp;&nbsp;<SPAN class='w'>" . number_format($pd->organic) . "</SPAN><SPAN class='g'>o</SPAN><BR />\n";
	echo "<SPAN class='w'><B>" . number_format($pd->powerplant) . "</B></SPAN><SPAN class='g'>p</SPAN>&nbsp;&nbsp;&nbsp;<SPAN class='w'>" . number_format($pd->energy) . "</SPAN><SPAN class='g'>e</SPAN><BR />\n";
	echo "<SPAN class='w'><B>" . number_format($pd->factory) . "</B></SPAN><SPAN class='g'>f</SPAN>";

	echo "</TABLE>\n";	
}
	
function display_launch_fleets() {
		$pd = new PlayerData();
		echo "<SPAN class='w'><B> Launch </B></SPAN><BR/>\n";
		echo "<FORM method='get' action='blackberry.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='launch_fleet'/>\n";
		echo "     <INPUT type='hidden' name='phone' value='blackberry'/>\n";

		echo "  <SPAN class='g'>Target:</SPAN> <SELECT name='target_name'>\n";
		$player_list = $pd->get_all_active_player_names();
		echo "    <OPTION> </OPTION>\n";			
		foreach ($player_list as $target) {
			echo "    <OPTION>$target</OPTION>\n";			
		}
		echo "  </SELECT><BR/>\n";

		echo "  <SPAN class='g'>Mission:</SPAN> <SELECT name='mission'>\n";
		echo "   <OPTION value='attack1'>Attack 1</OPTION>\n";
		echo "   <OPTION value='attack2'>Attack 2</OPTION>\n";
		echo "   <OPTION value='attack3'>Attack 3</OPTION>\n";

		echo "   <OPTION value='defense1'>Defend 1</OPTION>\n";
		echo "   <OPTION value='defense2'>Defend 2</OPTION>\n";
		echo "   <OPTION value='defense3'>Defend 3</OPTION>\n";
		echo "   <OPTION value='defense4'>Defend 4</OPTION>\n";
		echo "   <OPTION value='defense5'>Defend 5</OPTION>\n";
		echo "   <OPTION value='defense6'>Defend 6</OPTION>\n";
		echo "  </SELECT><BR/>\n";

		echo "  <SPAN class='g'>Fleet:&nbsp;&nbsp;</SPAN> <SELECT name='fleet'>\n";
		echo "   <OPTION value='1'>Fleet 1</OPTION>\n";
		echo "   <OPTION value='2'>Fleet 2</OPTION>\n";
		echo "   <OPTION value='3'>Fleet 3</OPTION>\n";
		echo "  </SELECT><BR/>\n";

		echo "  <INPUT type='submit' name='launch' value='Launch' />";
		echo "</FORM>\n";
		echo "<hr style='color:yellow;' />\n";
	}
	
	function display_recall_fleets() {
		echo "<SPAN class='w'><B> Recall </B></SPAN><BR/>\n";

		echo "<FORM method='get' action='blackberry.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='recall_fleet'/>\n";
		echo "     <INPUT type='hidden' name='phone' value='blackberry'/>\n";
		echo "  <INPUT type='submit' name='recall' value='Recall' />";
		echo "  <SELECT name='fleet'>\n";
		echo "   <OPTION value='fleet1'>Fleet 1</OPTION>\n";
		echo "   <OPTION value='fleet2'>Fleet 2</OPTION>\n";
		echo "   <OPTION value='fleet3'>Fleet 3</OPTION>\n";
		echo "  </SELECT><BR/>\n";
		
		echo "</FORM>\n";
		echo "<hr style='color:yellow;' />\n";
	
}


function display_research_chooser() {
  	$player_name=$_SESSION['player_name'];
		$rm = new ResearchModel();
		echo "<SPAN class='w'><B> Research </B></SPAN><BR/>\n";

		$cr = $rm->get_current_research_details($player_name);
		if ($cr != false) {
			echo "<SPAN class='g'>" . $cr['name'] . " (" . ($cr['total_ticks'] - $cr['ticks_remaining']) . "/" . $cr['total_ticks'] . ")</SPAN>";
		} else { 
			echo "<FORM method='get' action='blackberry.php5'>\n";
			echo "     <INPUT type='hidden' name='action' value='research'/>\n";
			echo "     <INPUT type='hidden' name='phone' value='blackberry'/>\n";
			echo "  <SELECT name='research_item'>\n";
			$list = $rm->get_list_of_all_research();
			echo "    <OPTION> </OPTION>\n";			
			foreach ($list as $research) {
				$item = $research['item'];
				$ticks = $research['ticks'];
				if ($rm->is_researchable($player_name, $item) && !$rm->is_researched($player_name, $item)) echo "    <OPTION value='$item'>$item ($ticks)</OPTION>\n";			
			}
			echo "  </SELECT><BR/>\n";
	
			echo "  <INPUT type='submit' value='Research' />";
			echo "</FORM>\n";
		}
		echo "<hr style='color:yellow;' />\n";
	}

function display_development_chooser() {
 		$player_name=$_SESSION['player_name'];
		$dm = new DevelopmentModel();
		echo "<SPAN class='w'><B> Development </B></SPAN><BR/>\n";
		
		$cd = $dm->get_current_development_details($player_name);
		if ($cd != false) {
			echo "<SPAN class='g'>" . $cd['name'] . " (" . ($cd['total_ticks'] - $cd['ticks_remaining']) . "/" . $cd['total_ticks'] . ")</SPAN>";
		} else { 
			echo "<FORM method='get' action='blackberry.php5'>\n";
			echo "     <INPUT type='hidden' name='action' value='develop'/>\n";
			echo "     <INPUT type='hidden' name='phone' value='blackberry'/>\n";
	
			echo "  <SELECT name='development_item'>\n";
			$list = $dm->get_list_of_all_developments();
			echo "    <OPTION> </OPTION>\n";			
			foreach ($list as $development) {
				$item = $development['item'];
				$ticks = $development['ticks'];
				if ($dm->is_developable($item) && !$dm->does_player_know_development($player_name, $item) ) echo "    <OPTION value='$item'>$item ($ticks)</OPTION>\n";			
			}
			echo "  </SELECT><BR/>\n";
	
			echo "  <INPUT type='submit' value='Develop' />";
			echo "</FORM>\n";
		}
		echo "<hr style='color:yellow;' />\n";
	}


function display_structures() {
		$dm = new DevelopmentModel();
		echo "<SPAN class='w'><B> Structures </B></SPAN><BR/>\n";

		echo "<FORM method='get' action='blackberry.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='allocate'/>\n";
		echo "     <INPUT type='hidden' name='phone' value='blackberry'/>\n";
		echo "     <INPUT type='hidden' name='structure_type' value='extractor'/>\n";
		echo "<SPAN class='g'>Extractors:&nbsp;&nbsp;&nbsp;</SPAN><INPUT name='number' size='4' />";
		echo "  <INPUT type='submit' value='Allocate' />";
		echo "</FORM><BR />\n";

		echo "<FORM method='get' action='blackberry.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='allocate'/>\n";
		echo "     <INPUT type='hidden' name='phone' value='blackberry'/>\n";
		echo "     <INPUT type='hidden' name='structure_type' value='genetic_lab'/>\n";
		echo "<SPAN class='g'>Genetic Labs:&nbsp;</SPAN><INPUT name='number' size='4' />";
		echo "  <INPUT type='submit' value='Allocate' />";
		echo "</FORM><BR />\n";

		echo "<FORM method='get' action='blackberry.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='allocate'/>\n";
		echo "     <INPUT type='hidden' name='phone' value='blackberry'/>\n";
		echo "     <INPUT type='hidden' name='structure_type' value='powerplant'/>\n";
		echo "<SPAN class='g'>Powerpants:&nbsp;&nbsp;</SPAN><INPUT name='number' size='4' />";
		echo "  <INPUT type='submit' value='Allocate' />";
		echo "</FORM><BR />\n";

		echo "<FORM method='get' action='blackberry.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='allocate'/>\n";
		echo "     <INPUT type='hidden' name='phone' value='blackberry'/>\n";
		echo "     <INPUT type='hidden' name='structure_type' value='factory'/>\n";
		echo "<SPAN class='g'>Factories:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</SPAN><INPUT name='number' size='4' />";
		echo "  <INPUT type='submit' value='Allocate' />";
		echo "</FORM><BR />\n";
		echo "<hr style='color:yellow;' />\n";
	}

function display_forum_links() {
	echo "<A href='http://revolutionofthegalaxy.com/revolution/blackberry_chat.php5'>Quick Chat</A><BR />";
	echo "<A href='http://revolutionofthegalaxy.com/revo_smf/index.php?action=pm'>Personal Messages</A><BR />";
	echo "<A href='http://revolutionofthegalaxy.com/revo_smf/index.php'>Main Forum</A><BR />";
	echo "<hr style='color:yellow;' />\n";
}


function display_incoming() {
  $player_name = $_SESSION["player_name"];
	$fm = new FleetModel();
	$incoming = $fm->get_incoming($player_name);
	$gm = new GameModel();
	$ct = $gm->get_current_tick();
	
	
	if (count($incoming) > 0) echo "<SPAN class='r'><B> Incoming </B></SPAN><BR/>\n";
	foreach ($incoming as $fleet) {
		$launcher = $fleet["launcher_name"];
		$arrival = $fleet["arrival_tick"] - $ct;
		$mission = $fleet["mission_type"];
		$fleet_number = $fleet["fleet"];
		
		if ($arrival > 0) {
			if ($mission == 'attack') echo "<SPAN class='lr'>$launcher $fleet_number is attacking in $arrival ticks </SPAN> <br />";
			else echo "<SPAN class='lg'>$launcher $fleet_number $is defending in $arrival ticks </SPAN> <br />";
		}
	}
	if (count($incoming) > 0) echo "<hr style='color:yellow;' />\n";

}

// ------  Helper functions  ------

	function display_info_bar() {
  	$status_info=$_SESSION['status_info'];
		if (strlen($status_info) == 0) return; // No status to display
		echo "<DIV class='info'>&nbsp;$status_info</DIV>\n";;
		$_SESSION['status_info'] = '';
	}
	
	function display_warning_bar() {
  	$warning_info=$_SESSION['warning_info'];
		if (strlen($warning_info) == 0) return; // No status to display
		echo "<DIV class='warning'>&nbsp;$warning_info</DIV>\n";;
		$_SESSION['warning_info'] = '';
	}
	
	function display_error_bar() {
  	$error_info=$_SESSION['error_info'];
		if (strlen($error_info) == 0) return; // No error to display
		echo "<DIV class='error'>&nbsp;$error_info</DIV>\n";;
		$_SESSION['error_info'] = '';
	}
8
?>