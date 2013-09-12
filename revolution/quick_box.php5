<?php
	require_once('scans_model.php5'); 
	require_once('pulses_model.php5'); 
	require_once('fleet_model.php5'); 

function display_quick_action_box($target_name, $g, $s, $p, $c, $smf_id) {
	$player_name = $_SESSION["player_name"];
	$sm = new ScansModel();
	$pm = new PulsesModel();
	$fm = new FleetModel();
	
	$tn = str_replace( ' ', '_', $target_name );
	
//	echo "<DIV id='box_$tn' style='position:absolute;width:177px;height:150px;visibility:hidden;' >";
	echo "<DIV id='box_$tn' style='position:absolute;display:none' >";
	echo "<TABLE class='STD' style='width:200px;'>";
	echo "<TR><TD class='STD'><B>$target_name</B></TD></TR>";

	ql('PM', 'Personal Message', 
		"/revo_smf/index.php?action=pm;sa=send;f=inbox;u={$smf_id}", 'pm');

	if ($sm->get_number_of_scans_for_player($player_name, "r_and_d_scan") > 0) ql('scan', 'R and D Scan', 
		"main_page.php5?view=scans&subview=active&action=use_scan&scan_type=r_and_d_scan&galaxy=$g&star=$s&planet=$p&continent=$c");
	if ($sm->get_number_of_scans_for_player($player_name, "continent_scan") > 0) ql('scan', 'Continent Scan', 
		"main_page.php5?view=scans&subview=active&action=use_scan&scan_type=continent_scan&galaxy=$g&star=$s&planet=$p&continent=$c");
	if ($sm->get_number_of_scans_for_player($player_name, "creature_scan") > 0) ql('scan', 'Creature Scan', 
		"main_page.php5?view=scans&subview=active&action=use_scan&scan_type=creature_scan&galaxy=$g&star=$s&planet=$p&continent=$c");
	if ($sm->get_number_of_scans_for_player($player_name, "military_scan") > 0) ql('scan', 'Military Scan', 
		"main_page.php5?view=scans&subview=active&action=use_scan&scan_type=military_scan&galaxy=$g&star=$s&planet=$p&continent=$c");
	if ($sm->get_number_of_scans_for_player($player_name, "news_scan") > 0) ql('scan', 'News Scan', 
		"main_page.php5?view=scans&subview=active&action=use_scan&scan_type=news_scan&galaxy=$g&star=$s&planet=$p&continent=$c");
	if ($sm->get_number_of_scans_for_player($player_name, "full_scan") > 0) ql('scan', 'Full Scan', 
		"main_page.php5?view=scans&subview=active&action=use_scan&scan_type=full_scan&galaxy=$g&star=$s&planet=$p&continent=$c");
	if ($sm->get_number_of_scans_for_player($player_name, "launch_monitor") > 0) ql('scan', 'Launch Monitor', 
		"main_page.php5?view=rankings&action=use_scan&scan_type=launch_monitor&galaxy=$g&star=$s&planet=$p&continent=$c");
	if ($sm->get_number_of_scans_for_player($player_name, "structure_monitor") > 0) ql('scan', 'Structure Monitor', 
		"main_page.php5?view=rankings&action=use_scan&scan_type=structure_monitor&galaxy=$g&star=$s&planet=$p&continent=$c");

	if ($fm->determine_fleet_launch_cost($player_name, "fleet1") > 0 && ! $fm->is_active_fleet_orders($player_name, "fleet1") ) ql('fleet', 'Attack(3) Fleet 1', 
		"main_page.php5?view=fleets&action=launch_fleet&fleet=1&mission=attack3&galaxy=$g&star=$s&planet=$p&continent=$c");
	if ($fm->determine_fleet_launch_cost($player_name, "fleet2") > 0 && ! $fm->is_active_fleet_orders($player_name, "fleet2") ) ql('fleet', 'Attack(3) Fleet 2', 
		"main_page.php5?view=fleets&action=launch_fleet&fleet=2&mission=attack3&galaxy=$g&star=$s&planet=$p&continent=$c");
	if ($fm->determine_fleet_launch_cost($player_name, "fleet3") > 0 && ! $fm->is_active_fleet_orders($player_name, "fleet3") ) ql('fleet', 'Attack(3) Fleet 3', 
		"main_page.php5?view=fleets&action=launch_fleet&fleet=3&mission=attack3&galaxy=$g&star=$s&planet=$p&continent=$c");
	if ($fm->determine_fleet_launch_cost($player_name, "fleet1") > 0 && ! $fm->is_active_fleet_orders($player_name, "fleet1") ) ql('fleet', 'Defend(6) Fleet 1', 
		"main_page.php5?view=fleets&action=launch_fleet&fleet=1&mission=defense6&galaxy=$g&star=$s&planet=$p&continent=$c");
	if ($fm->determine_fleet_launch_cost($player_name, "fleet2") > 0 && ! $fm->is_active_fleet_orders($player_name, "fleet2") ) ql('fleet', 'Defend(6) Fleet 2', 
		"main_page.php5?view=fleets&action=launch_fleet&fleet=2&mission=defense6&galaxy=$g&star=$s&planet=$p&continent=$c");
	if ($fm->determine_fleet_launch_cost($player_name, "fleet3") > 0 && ! $fm->is_active_fleet_orders($player_name, "fleet3") ) ql('fleet', 'Defend(6) Fleet 3', 
		"main_page.php5?view=fleets&action=launch_fleet&fleet=3&mission=defense6&galaxy=$g&star=$s&planet=$p&continent=$c");

	if ($pm->get_number_pulses($player_name, "Electromagnetic Blast") > 0) ql('blast', 'Electromagnetic Blast', 
		"main_page.php5?view=pulses&action=fire_pulse&pulse_type=Electromagnetic+Blast&galaxy=$g&star=$s&planet=$p&continent=$c");
	if ($pm->get_number_pulses($player_name, "Microwave Blast") > 0) ql('blast', 'Microwave Blast', 
		"main_page.php5?view=pulses&action=fire_pulse&pulse_type=Microwave+Blast&galaxy=$g&star=$s&planet=$p&continent=$c");


	echo "<TR><TD class='STD close' style='background-color:#888' >close</TD></TR>";
	echo "</TABLE>";		
	echo "</DIV>";
}

function ql($class, $name, $href, $target = null) {
	if ($target == null) echo "<TR><TD class='STD $class' style='background-color:#888' onClick=\"location.href='$href'\">$name</TD></TR>";
	else echo "<TR><TD class='STD $class' style='background-color:#888' onClick=\"location.href='$href'\" >$name</TD></TR>";
	
}


?>