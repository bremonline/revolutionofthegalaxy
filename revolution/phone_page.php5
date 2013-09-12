<?php
	require_once('misc_fns.php5'); 
	require_once('player_data.php5'); 
	require_once('page_controller.php5'); 
	
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

  
  echo "<body style='background-color:black;'>\n";

	$pc = new PageController();
	$pc->perform_action();
	
  $player_name=$_SESSION['player_name'];
	$pd = new PlayerData();
	$pd->touch($player_name);

	echo "<br />\n";

	display_stats();
	
	display_info_bar();	
	display_warning_bar();	
	display_error_bar();	
	echo "<hr style='color:yellow;' />\n";
	display_launch_fleets();
	echo "<hr style='color:yellow;' />\n";
	display_recall_fleets();
	echo "<hr style='color:yellow;' />\n";
	display_research_chooser();
	echo "<hr style='color:yellow;' />\n";

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
		echo "<FORM method='get' action='phone_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='launch_fleet'/>\n";
		echo "     <INPUT type='hidden' name='phone' value='blackberry'/>\n";

		echo "  <SPAN class='g'>Target:</SPAN> <SELECT name='target_name'>\n";
		$player_list = $pd->get_all_player_names();
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
	}
	
	function display_recall_fleets() {
		echo "<SPAN class='w'><B> Recall </B></SPAN><BR/>\n";

		echo "<FORM method='get' action='phone_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='recall_fleet'/>\n";
		echo "     <INPUT type='hidden' name='phone' value='blackberry'/>\n";
		echo "  <SPAN class='g'>Fleet:&nbsp;&nbsp;</SPAN> <SELECT name='fleet'>\n";
		echo "   <OPTION value='fleet1'>Fleet 1</OPTION>\n";
		echo "   <OPTION value='fleet2'>Fleet 2</OPTION>\n";
		echo "   <OPTION value='fleet3'>Fleet 3</OPTION>\n";
		echo "  </SELECT><BR/>\n";
		echo "  <INPUT type='submit' name='recall' value='Recall' />";
		
		echo "</FORM>\n";
	
}


function display_research_chooser() {
		$rm = new ResearchModel();
		echo "<SPAN class='w'><B> Research </B></SPAN><BR/>\n";
		echo "<FORM method='get' action='phone_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='research'/>\n";
		echo "     <INPUT type='hidden' name='phone' value='blackberry'/>\n";

		echo "  <SPAN class='g'>Research Item:</SPAN> <SELECT name='research_item'>\n";
		$list = $rm->get_list_of_all_research();
		echo "    <OPTION> </OPTION>\n";			
		foreach ($list as $item) {
			echo "    <OPTION>$item</OPTION>\n";			
		}
		echo "  </SELECT><BR/>\n";

		echo "  <INPUT type='submit' value='Research' />";
		echo "</FORM>\n";
	}

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