<?php
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('pulses_model.php5'); 
	require_once('description_panel.php5'); 
	require_once('misc_item.php5'); 

class PulsesView {
	function display_pulses_view($subview) {
		if (strcmp($subview,"individual") == 0) {
			$this->display_individual_pulse_subview();
		} else {
			$this->display_current_pulse_build();
			$this->display_current_active_pulse();
			$this->display_create_pulses();
			$this->display_use_pulses();
		}
	}

	function display_create_pulses() {
		$player_name = $_SESSION['player_name'];

		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_pulses'/>\n";
		echo "     <INPUT type='hidden' name='view' value='pulses'/>\n";
		echo "<TABLE class='STD'>\n";
		echo "<TR >";
		echo "	<TH class='STD' width='70%' style='text-align:left;'> Pulses / Shields / Blasts / Jammers </TH>";
		echo "	<TH class='STD' width='10%'> Cost </TH>";
		echo "	<TH class='STD' width='10%'> You Own </TH>";
		echo "	<TH class='STD' width='10%'> To Make </TH>";
		echo "</TR>\n";
		$this->display_generic_form($player_name, "Modulator", "Modulator", "modulators");
		$this->display_generic_form($player_name, "Reflector", "Reflector", "reflectors");
		echo "<TR></TR>";

		$this->display_generic_form($player_name, "Electromagnetic Pulse", "Electromagnetic Pulse", "electromagnetic_pulses");
		$this->display_generic_form($player_name, "Microwave Pulse", "Microwave Pulse", "microwave_pulses");
		$this->display_generic_form($player_name, "Electromagnetic Shield", "Electromagnetic Shield", "electromagnetic_shields");
		$this->display_generic_form($player_name, "Microwave Shield", "Microwave Shield", "microwave_shields");
		$this->display_generic_form($player_name, "Electromagnetic Blast", "Electromagnetic Blast", "electromagnetic_blasts");
		$this->display_generic_form($player_name, "Microwave Blast", "Microwave Blast", "microwave_blasts");
		echo "<TR></TR>";
		$this->display_generic_form($player_name, "Command Jammer", "Command Jammer", "command_jammers");
		echo "</TABLE>\n";
		echo "  <INPUT type='submit' name='Create Items' value='Create Items' />\n";
		echo "</FORM>\n";
	}
	
	function display_generic_form($player_name, $type, $development, $entry) {
		$dm = new DevelopmentModel();		
		$pm = new PulsesModel();		
		$number = $pm->get_number_pulses($player_name, "$type");
		if ($number < 0) $number = 0;
		$details = $pm->get_pulse_details("$type");
		$mineral = $details["mineral"];
		$organic = $details["organic"];
		$energy = $details["energy"];
		$ticks = $details["ticks"];
		$description = $details["description"];

		echo "<TR >";
		echo "	<TD class='STD' width='70%' style='text-align:left;'> ";
		echo "<B><A style='color:white' href='main_page.php5?view=pulses&subview=individual&pulse=$type'>$type</A></B><BR />$description </TD>";
		echo "	<TD class='STD' width='10%'> {$mineral}m <br /> {$organic}o <br /> {$energy}e </TD>";
		echo "	<TD class='STD' width='10%'> $number </TD>";
		echo "	<TD class='STD' width='10%'>";
		$allowed = $dm->does_player_know_development($player_name, "$development");
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Modulator") == 0) $allowed = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Reflector") == 0) $allowed = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Electromagnetic Pulse") == 0) $allowed = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Electromagnetic Shield") == 0) $allowed = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Electromagnetic Blast") == 0) $allowed = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Microwave Pulse") == 0) $allowed = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Microwave Shield") == 0) $allowed = true;
		if ($dm->does_player_know_development($player_name, "Advanced Pulses") && strcmp($type, "Microwave Blast") == 0) $allowed = true;
		if ($allowed) {
			echo "     <INPUT type='text' size='6' name='$entry' /> <BR />\n";
			echo "     <I>{$ticks} ticks</I>\n";
		} else {
			echo "&nbsp;";
		}
		echo "  </TD>";
		echo "</TR>\n";		
	}
	
	function display_current_pulse_build() {
		echo $this->make_current_pulse_build_display();
	}
	

	function make_current_pulse_build_display() {
		$pm = new PulsesModel();
		
		$player_name = $_SESSION['player_name'];
	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='pulse'
			and status='building'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return;
		
		$string = "";
		
		$string .= "<TABLE class='STD' style='width:100%;'><TR>\n";
		$string .= "<TD class='STD' >";
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$number = $row->number;
			$ticks_remaining = $row->ticks_remaining;
			$total_ticks = $pm->get_total_ticks_of_pulse($row->build_item);

			$ticks_completed = $total_ticks - $ticks_remaining;
			$percent_complete=100*$ticks_completed/$total_ticks;
			$percent_incomplete=100-$percent_complete;

			$string .= "Building: $row->build_item ($number), ($ticks_completed/$total_ticks) completed<BR/>";
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

	function display_current_active_pulse() {
		$player_name = $_SESSION['player_name'];
		$pm = new PulsesModel();
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		echo "<TABLE class='STD'>";
		echo "<TR><TH class='STD'>Active Pulse</TH><TH class='STD'>Tick</TH></TR>";
		
		$is_active_pulse = $pm->has_pulse_been_fired($player_name, $current_tick);
		
		if ($is_active_pulse) {
			list($pulse_type, $end_tick) = $pm->active_pulse($player_name, $current_tick);
			echo "<TR><TD class='STD'> $pulse_type </TD><TD class='STD'> $end_tick </TD></TR>";
		} else {
			echo "<TR><TD class='STD' colspan='2'> No active pulses, blasts, or shields </TD></TR>";
		}
		echo "	</TABLE>\n";
		echo "	<BR />";
		
	}

	function display_use_pulses() {
		$enabled = true;
		echo "<TABLE class='STD'>\n";
		echo "<TR >";
		echo "	<TH class='STD' width='50%' style='text-align:left;'> Pulses / Shields / Blasts / Jammers </TH>";
		echo "	<TH class='STD' width='10%'> You Own </TH>";
		echo "	<TH class='STD' width='30%'> Target </TH>";
		echo "	<TH class='STD' width='10%'> Fire </TH>";
		echo "</TR>\n";
		$this->display_pulse_command("Electromagnetic Pulse", false, $enabled);
		$this->display_pulse_command("Microwave Pulse", false, $enabled);
		$this->display_pulse_command("Electromagnetic Shield", false, $enabled);
		$this->display_pulse_command("Microwave Shield", false, $enabled);
		$this->display_pulse_command("Electromagnetic Blast", true, $enabled);
		$this->display_pulse_command("Microwave Blast", true, $enabled);
		$this->display_pulse_command("Command Jammer", false, $enabled);
		echo "</TABLE>\n";
		
	}

	function display_pulse_command($pulse_type, $location_needed, $enabled) {
		$player_name = $_SESSION['player_name'];
		$pm = new PulsesModel();
		$number = $pm->get_number_pulses($player_name, $pulse_type);
		if ($number <= 0) {
			$enabled = false;
			$number = "&nbsp;";
		}
		echo "<TR >";

		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='fire_pulse'/>\n";
		echo "     <INPUT type='hidden' name='view' value='pulses'/>\n";
		echo "     <INPUT type='hidden' name='pulse_type' value='$pulse_type'/>\n";
		
		echo "	<TD class='STD' width='50%' style='text-align:left;'>$pulse_type</TD>";
		echo "	<TD class='STD' width='10%'> $number </TD>";
		echo "	<TD class='STD' width='30%'> ";
		if ($location_needed) {
			$this->show_galaxy_select();
			$this->show_star_select();
			$this->show_planet_select();
			$this->show_continent_select();
		} else {
			echo "&nbsp;";		
		}
		echo " </TD>";
		echo "	<TD class='STD' width='10%'>";
		if ($enabled)	echo "    <INPUT type='submit' name='Fire' value='Fire' />\n";
		else echo "&nbsp;";
		echo "  </TD>";
		echo "</FORM>\n";
		echo "</TR>\n";
		
	}
	
	function show_galaxy_select() {
  	$galaxy = $_REQUEST["galaxy"];
  	
		echo 	"   <SELECT name='galaxy'>\n ";
		for ($i=1; $i <= 3; $i++) {
			if ($i == $galaxy)  echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
			else if ($i == $pd->galaxy && $galaxy == '')  echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
			else echo 	"   <OPTION value='$i'>$i</OPTION>\n ";
		}
		echo "    </SELECT>\n";		
	}
	function show_star_select() {
  	$star = $_REQUEST["star"];

		echo 	"   <SELECT name='star'>\n ";
		for ($i=1; $i <= 29; $i++) {
			if ($i == $star)  echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
			else if ($i == $pd->star) echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
			else echo 	"   <OPTION value='$i'>$i</OPTION>\n ";
		}
		echo "    </SELECT>\n";		
	}
	function show_planet_select() {
  	$planet = $_REQUEST["planet"];

		echo 	"   <SELECT name='planet'>\n ";
		for ($i=1; $i <= 9; $i++) {
			if ($i == $planet)  echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
			else if ($i == $pd->planet) echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
			else echo 	"   <OPTION value='$i'>$i</OPTION>\n ";
		}
		echo "    </SELECT>\n";		
	}
	function show_continent_select() {
  	$continent = $_REQUEST["continent"];

		echo 	"   <SELECT name='continent'>\n ";
		for ($i=1; $i <= 9; $i++) {
			if ($i == $continent)  echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
			else if ($i == $pd->continent) echo 	"   <OPTION value='$i' selected='true'>$i</OPTION>\n ";
			else echo 	"   <OPTION value='$i'>$i</OPTION>\n ";
		}
		echo "    </SELECT>\n";		
	}
	

	function display_individual_pulse_subview() {
		$pulse = $_REQUEST["pulse"];
		if (!$pulse) return;
		
		$dp = new DescriptionPanel();

		echo "<TABLE class='STD'>";
		echo "<TR>\n";
		echo "<TH class='STD' colspan='2'>Characteristics for $pulse </TH>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<TD class='STD' style='height:400px;width=300px;' rowspan='3'><IMG src='images/placeholder.gif' /></TD>\n";
		$this->show_order_panel($pulse);
		echo "</TR>";
		echo "<TR><TD class='STD' style='vertical-align:top'>";
		$this->show_stats_panel($pulse);
		echo "</TD></TR>";
		echo "<TR><TD class='STD' style='vertical-align:top;text-align:left'>";
		$this->show_dependent_panel($pulse);
		echo "</TD></TR>";
		echo "<TR>\n";
		$dp->show_text_panel($pulse, "color", "pulse", "text-align:center;font-style:italic;");
		$dp->show_text_panel($pulse, "basic", "pulse", "text-align:left;font-style:normal;");
		echo "</TR>";
		echo "<TR>\n";
		echo "<TH class='STD' colspan='2'>Discussion</TH>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<TD class='STD' colspan='2' height='200'>Discussion Content</TD>\n";
		echo "</TR>";
		echo "</TABLE>";
	}

	function show_order_panel($pulse) {
		$player_name = $_SESSION['player_name'];
		$dm = new DevelopmentModel();

		$mi = new MiscItem();
		$mi->db_fill($pulse);
		if ($dm->does_player_know_development($player_name, $mi->development_item) ) {
			$this->display_ordering_bar($pulse);
		} else {
			echo " <TD class='STD' style='background-color:882222'>Cannot Order this Pulse until $mi->development_item is developed.</TD>";
		}
	}

	function display_ordering_bar($pulse) {
		$player_name = $_SESSION['player_name'];
		$pm = new PulsesModel();

		$mi = new MiscItem();
		$mi->db_fill($pulse);

		$pd = new PlayerData();
		$pd->db_fill($player_name);
		$max_organic = floor($pd->organic / $mi->organic); 
		$max_energy = floor($pd->energy / $mi->energy); 
		$true_max = min($max_organic, $max_energy);
		$current_pulses =  $pm->get_number_pulses($player_name, $pulse);
		if ($current_pulses < 0) $current_pulses = 0;
		
		echo "<FORM method='post' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_pulses'/>\n";
		echo "     <INPUT type='hidden' name='view' value='pulse'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='individual'/>\n";
		echo "     <INPUT type='hidden' name='pulse' value='$pulse'/>\n";
		echo " <TD class='STD'>";
		echo "   <TABLE class='STD' style='width:100%'>\n";
		echo "     <TR><TH class='STD'>You Own</TH>";
		echo "         <TH class='STD'>Max You Can Make</TH>";
		echo "         <TH class='STD'>To Make</TH>";
		echo "         <TH class='STD'>&nbsp;</TH></TR>  \n";
		echo "     <TR><TD class='STD'>$current_pulses</TD>";
		echo "     <TD class='STD'>$true_max</TD>\n";
		echo "     <TD class='STD'><INPUT type='text' size='6' name='$pulse' /></TD>\n";
		echo "         <TD class='STD'><INPUT type='submit' value='Create' /></TD></TR>  \n";
		echo "   </TABLE> \n";
		echo " </TD>";
		echo "</FORM>\n";
		
	}
		
	function show_stats_panel($pulse) {
		$player_name = $_SESSION['player_name'];
		$mi = new MiscItem();
		$mi->db_fill($pulse);
		
		echo "<TABLE class='STATS' style='width:530px;'>";
		echo "<TR><TD class='STATS'>Organic</TD><TD class='STATS'>$mi->organic</TD></TR>";
		echo "<TR><TD class='STATS'>Energy</TD><TD class='STATS'>$mi->energy</TD></TR>";
		echo "<TR><TD class='STATS'>Ticks</TD><TD class='STATS'>$mi->ticks</TD></TR>";
		echo "</TABLE>";
	}

	function show_dependent_panel($pulse) {
		$mi = new MiscItem();
		$mi->db_fill($pulse);

		echo "Required Development:<BR />\n";
		echo "<UL>\n";
		echo "<LI><A style='color:white' href='main_page.php5?view=development&subview=individual&development_item=$mi->development_item'>$mi->development_item</A></LI>\n";
		echo "</UL>\n";	
	}
	
}
?>