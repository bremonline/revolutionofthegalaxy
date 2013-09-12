<?php
	require_once('misc_fns.php5'); 
	require_once('help_view.php5'); 
	require_once('research_view.php5'); 
	require_once('development_view.php5'); 
	require_once('creatures_view.php5'); 
	require_once('scans_view.php5'); 
	require_once('pulses_view.php5'); 
	require_once('bombs_view.php5'); 
	require_once('forts_view.php5'); 

	do_html_header("Revolution Help");
	
	$research_item=$_REQUEST['research_item'];
	$development_item=$_REQUEST['development_item'];
	$creature=$_REQUEST['creature'];
	$scan=$_REQUEST['scan'];
	$pulse=$_REQUEST['pulse'];
	$bomb=$_REQUEST['bomb'];
	$fort=$_REQUEST['fort'];
	
	$rv = new ResearchView();
	$dv = new DevelopmentView();
	$cv = new CreaturesView();
	$sv = new ScansView();
	$pv = new PulsesView();
	$bv = new BombsView();
	$fv = new FortsView();
	
	if ($research_item) $rv->display_individual_research_subview($research_item);
	else if ($development_item) $dv->display_individual_development_subview($development_item);
	else if ($creature) $cv->display_individual_creature_subview($creature);
	else if ($scan) $sv->display_individual_scan_subview($scan);
	else if ($pulse) $pv->display_individual_pulse_subview($pulse);
	else if ($bomb) $bv->display_individual_bomb_subview($bomb);
	else if ($fort) $fv->display_individual_fort_subview($fort);
	else display_topic_help_no_session();
	
	do_html_footer();
	
	
function display_topic_help_no_session() {
	echo "<TABLE class='STD'>\n";	
	echo "<TR>\n";	
	echo "<TD class='STD' colspan='4'>";
	echo "Pick a topic from those below:";
	echo "</TD>\n";
	echo "</TR>\n";	
	echo "<TR>\n";
	echo "<TD class='STD' style='text-align:left;vertical-align:top;'>";
	display_concept_column();
	echo "</TD>\n";
	echo "<TD class='STD' style='text-align:left;vertical-align:top;'>";
	display_research_column();
	echo "</TD>\n";
	echo "<TD class='STD' style='text-align:left;vertical-align:top;'>";
	display_development_column();
	echo "</TD>\n";
	echo "<TD class='STD' style='text-align:left;vertical-align:top;'>";
	display_items_column();
	echo "</TD>\n";
	echo "</TR>\n";	
}

function display_concept_column() {
	echo "CONCEPTS:<br /><br />";
	
	$hm = new HelpModel();
	$concept_names = $hm->get_basic_concepts();			
	for ($count=0; $count < count($concept_names); $count++) {
		$name = $concept_names[$count]["name"];
		if (strcmp($concept_names[$count]["text"], "No Description Available") == 0) $pic='redball.gif';
		else $pic='greenball.gif';
		echo "<A style='color:white;vertical-align:center' href='help.php5?concept=$name'><img border='0' src='images/$pic' />$name</A><br />";
	}	
}

function display_research_column() {
	echo "RESEARCH:<br /><br />";
	
	$hm = new HelpModel();
	$research_names = $hm->get_all_names_from_table("research_items");
	$basic_description_exists = $hm->get_description_matrix("research", "basic");			
	$color_description_exists = $hm->get_description_matrix("research", "color");			
	for ($count=0; $count < count($research_names); $count++) {
		$color = 0;
		$name = $research_names[$count]["name"];
		if ($basic_description_exists["$name"])  $color++;
		if ($color_description_exists["$name"]) $color++;
		if ($color == 0) $pic='redball.gif';
		else if ($color == 1) $pic='yellowball.gif';
		else if ($color == 2) $pic='greenball.gif';
		
		echo "<A style='color:white;vertical-align:center' href='help.php5?research_item=$name'><img border='0' src='images/$pic' />$name</A><br />";
	}
	
}

function display_development_column() {
	echo "DEVELOPMENT:<br /><br />";
	
	$hm = new HelpModel();
	$development_names = $hm->get_all_names_from_table("development_items");
	$basic_description_exists = $hm->get_description_matrix("development", "basic");			
	$color_description_exists = $hm->get_description_matrix("development", "color");			
	for ($count=0; $count < count($development_names); $count++) {
		$color = 0;
		$name = $development_names[$count]["name"];
		if ($basic_description_exists["$name"])  $color++;
		if ($color_description_exists["$name"]) $color++;
		if ($color == 0) $pic='redball.gif';
		else if ($color == 1) $pic='yellowball.gif';
		else if ($color == 2) $pic='greenball.gif';
		
		echo "<A style='color:white;vertical-align:center' href='help.php5?development_item=$name'><img border='0' src='images/$pic' />$name</A><br />";
	}
	
}

function display_items_column() {
	display_creature_subcolumn();
	echo "<BR />";
	display_scan_subcolumn();
	echo "<BR />";
	display_pulse_subcolumn();
	echo "<BR />";
	display_bomb_subcolumn();
	echo "<BR />";
	display_fort_subcolumn();

}

function display_creature_subcolumn() {
	echo "CREATURES:<br /><br />";

	$hm = new HelpModel();
	$creature_names = $hm->get_all_names_from_table("creature_items");
	$basic_description_exists = $hm->get_description_matrix("creature", "basic");			
	$color_description_exists = $hm->get_description_matrix("creature", "color");			
	for ($count=0; $count < count($creature_names); $count++) {
		$color = 0;
		$name = $creature_names[$count]["name"];
		if ($basic_description_exists["$name"])  $color++;
		if ($color_description_exists["$name"]) $color++;
		if ($color == 0) $pic='redball.gif';
		else if ($color == 1) $pic='yellowball.gif';
		else if ($color == 2) $pic='greenball.gif';
		
		echo "<A style='color:white;vertical-align:center' href='help.php5?creature=$name'><img border='0' src='images/$pic' />$name</A> <br />";
	}
}


function display_scan_subcolumn() {
	echo "SCANS:<br /><br />";

	$hm = new HelpModel();
	$scan_names = $hm->get_all_names_from_table("scan_items");
	$basic_description_exists = $hm->get_description_matrix("scan", "basic");			
	$color_description_exists = $hm->get_description_matrix("scan", "color");			
	for ($count=0; $count < count($scan_names); $count++) {
		$color = 0;
		$name = $scan_names[$count]["name"];
		if ($basic_description_exists["$name"])  $color++;
		if ($color_description_exists["$name"]) $color++;
		if ($color == 0) $pic='redball.gif';
		else if ($color == 1) $pic='yellowball.gif';
		else if ($color == 2) $pic='greenball.gif';
		
		echo "<A style='color:white;vertical-align:center' href='help.php5?scan=$name'><img border='0' src='images/$pic' />$name</A> <br />";
	}
}

function display_pulse_subcolumn() {
	echo "PULSES/SHIELDS/<br />BLASTS/JAMMERS:<br /><br />";

	$hm = new HelpModel();
	$pulse_names = $hm->get_all_names_from_misc_items_table("pulse");
	$basic_description_exists = $hm->get_description_matrix("pulse", "basic");			
	$color_description_exists = $hm->get_description_matrix("pulse", "color");			
	for ($count=0; $count < count($pulse_names); $count++) {
		$color = 0;
		$name = $pulse_names[$count]["name"];
		if ($basic_description_exists["$name"])  $color++;
		if ($color_description_exists["$name"]) $color++;
		if ($color == 0) $pic='redball.gif';
		else if ($color == 1) $pic='yellowball.gif';
		else if ($color == 2) $pic='greenball.gif';
		
		echo "<A style='color:white;vertical-align:center' href='help.php5?pulse=$name'><img border='0' src='images/$pic' />$name</A><br />";
	}
}

function display_bomb_subcolumn() {
	echo "BOMBS/TRAPS:<br /><br />";

	$hm = new HelpModel();
	$bomb_names = $hm->get_all_names_from_misc_items_table("bomb");
	$basic_description_exists = $hm->get_description_matrix("bomb", "basic");			
	$color_description_exists = $hm->get_description_matrix("bomb", "color");			
	for ($count=0; $count < count($bomb_names); $count++) {
		$color = 0;
		$name = $bomb_names[$count]["name"];
		if ($basic_description_exists["$name"])  $color++;
		if ($color_description_exists["$name"]) $color++;
		if ($color == 0) $pic='redball.gif';
		else if ($color == 1) $pic='yellowball.gif';
		else if ($color == 2) $pic='greenball.gif';
		
		echo "<A style='color:white;vertical-align:center' href='help.php5?bomb=$name'><img border='0' src='images/$pic' />$name</A><br />";
	}
}

function display_fort_subcolumn() {
	echo "FORTS:<br /><br />";

	$hm = new HelpModel();
	$fort_names = $hm->get_all_names_from_misc_items_table("fort");
	$basic_description_exists = $hm->get_description_matrix("fort", "basic");			
	$color_description_exists = $hm->get_description_matrix("fort", "color");			
	for ($count=0; $count < count($fort_names); $count++) {
		$color = 0;
		$name = $fort_names[$count]["name"];
		if ($basic_description_exists["$name"])  $color++;
		if ($color_description_exists["$name"]) $color++;
		if ($color == 0) $pic='redball.gif';
		else if ($color == 1) $pic='yellowball.gif';
		else if ($color == 2) $pic='greenball.gif';
		
		echo "<A style='color:white;vertical-align:center' href='help.php5?fort=$name'><img border='0' src='images/$pic' />$name</A><br />";
	}
}

?>