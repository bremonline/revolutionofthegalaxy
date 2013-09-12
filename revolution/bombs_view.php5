<?php
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('bombs_model.php5'); 
	require_once('description_panel.php5'); 
	require_once('misc_item.php5'); 

class BombsView {
	function display_bombs_view($subview) {
		if (strcmp($subview,"individual") == 0) {
			$this->display_individual_bomb_subview();
		} else {
			$this->display_current_bomb_build();
			$this->display_create_bombs();
			$this->display_activate_traps();
		}
	}

	function display_create_bombs() {
		$player_name = $_SESSION['player_name'];

		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_bombs'/>\n";
		echo "     <INPUT type='hidden' name='view' value='bombs'/>\n";
		echo "<TABLE class='STD'>\n";
		echo "<TR >";
		echo "	<TH class='STD' width='70%' style='text-align:left;'> Bombs / Traps </TH>";
		echo "	<TH class='STD' width='10%'> Cost </TH>";
		echo "	<TH class='STD' width='10%'> You Own </TH>";
		echo "	<TH class='STD' width='10%'> To Make </TH>";
		echo "</TR>\n";
		$this->display_generic_form($player_name, "Bomb", "Bomb", "bombs");
		$this->display_generic_form($player_name, "Poison Bomb", "Poison Bomb", "poison_bombs");
		$this->display_generic_form($player_name, "Trap", "Trap", "traps");
		$this->display_generic_form($player_name, "Psychological Trap", "Psychological Trap", "psych_traps");
		echo "</TABLE>\n";
		echo "  <INPUT type='submit' name='Create Items' value='Create Bombs/Traps'/>\n";
		echo "</FORM>\n";
	}

	function display_generic_form($player_name, $type, $development, $entry) {
		$dm = new DevelopmentModel();		
		$bm = new BombsModel();		
		$number = $bm->get_number_bombs($player_name, "$type");
		if ($number < 0) $number = 0;
		$details = $bm->get_bomb_details("$type");
		$mineral = $details["mineral"];
		$organic = $details["organic"];
		$ticks = $details["ticks"];
		$description = $details["description"];

		echo "<TR >";
		echo "	<TD class='STD' width='70%' style='text-align:left;'>";
		echo " <B><A style='color:white' href='main_page.php5?view=bombs&subview=individual&bomb=$type'>$type</B></A><BR />$description </TD>";
		echo "	<TD class='STD' width='10%'> {$mineral}m <br /> {$organic}o </TD>";
		echo "	<TD class='STD' width='10%'> &nbsp;$number&nbsp; </TD>";
		echo "	<TD class='STD' width='10%'>";
		if ($dm->does_player_know_development($player_name, "$development") ) $allowed = true;
		if ($dm->does_player_know_development($player_name, "Advanced Bombs") && strcmp($type, "Bomb") == 0) $allowed = true;
		if ($dm->does_player_know_development($player_name, "Advanced Bombs") && strcmp($type, "Poison Bomb") == 0) $allowed = true;
		if ($dm->does_player_know_development($player_name, "Advanced Traps") && strcmp($type, "Trap") == 0) $allowed = true;
		if ($dm->does_player_know_development($player_name, "Advanced Traps") && strcmp($type, "Psychological Trap") == 0) $allowed = true;
		
		if ($allowed) {
			echo "     <INPUT type='text' size='6' name='$entry' /> <BR />\n";
			echo "     <I>{$ticks} ticks</I>\n";
		} else {
			echo "&nbsp;";
		}
		echo "  </TD>";
		echo "</TR>\n";		
	}

	
	function display_current_bomb_build() {
		echo $this->make_current_bomb_build_display();
	}

	function make_current_bomb_build_display() {
		$bm = new BombsModel();
		
		$player_name = $_SESSION['player_name'];
	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='bomb'
			and status='building'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return;
		$string = "";
		
		$string .= "<TABLE class='STD' style='width:100%;'><TR>\n";
		$string .= "<TD class='STD' >";
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$number = $row->number;
			$ticks_remaining = $row->ticks_remaining;
			$total_ticks = $bm->get_total_ticks_of_bomb($row->build_item);

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
	
	function display_activate_traps() {
		$bm = new BombsModel();
		$player_name = $_SESSION['player_name'];
		$active_traps = $bm->get_number_bombs_at_location($player_name, "Trap", "active");
		$inactive_traps = $bm->get_number_bombs_at_location($player_name, "Trap", "inactive");
		$active_psych_traps = $bm->get_number_bombs_at_location($player_name, "Psychological Trap", "active");
		$inactive_psych_traps = $bm->get_number_bombs_at_location($player_name, "Psychological Trap", "inactive");
		if ($active_traps < 0) $active_traps = 0;
		if ($inactive_traps < 0) $inactive_traps = 0;
		if ($active_psych_traps < 0) $active_psych_traps = 0;
		if ($inactive_psych_traps < 0) $inactive_psych_traps = 0;
		
		
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='change_trap_status'/>\n";
		echo "     <INPUT type='hidden' name='view' value='bombs'/>\n";
		
		echo "<TABLE class='STD' >\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='5'>Traps</TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "   <TH class='STD' >&nbsp;</TH>\n";
		echo "   <TH class='STD' colspan='2'>Inactive</TH>\n";
		echo "   <TH class='STD' colspan='2'>Active</TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD' width='20%'> Traps </TD>\n";
		echo "  <TD class='STD' width='10%'> &nbsp;$inactive_traps&nbsp; </TD>\n";
		echo "  <TD class='STD' width='10%'> <INPUT type='text' name='to_activate_traps' size='8' /> </TD>\n";
		echo "  <TD class='STD' width='10%'> &nbsp;$active_traps&nbsp;</TD>\n";
		echo "  <TD class='STD' width='10%'> <INPUT type='text' name='to_deactivate_traps' size='8' /> </TD>\n";
		echo " <TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD' width='20%'> Psychological Traps </TD>\n";
		echo "  <TD class='STD' width='10%'> &nbsp;$inactive_psych_traps&nbsp; </TD>\n";
		echo "  <TD class='STD' width='10%'> <INPUT type='text' name='to_activate_psych_traps' size='8' /> </TD>\n";
		echo "  <TD class='STD' width='10%'> &nbsp;$active_psych_traps&nbsp; </TD>\n";
		echo "  <TD class='STD' width='10%'> <INPUT type='text' name='to_deactivate_psych_traps' size='8' /> </TD>\n";
		echo " <TR>\n";
		echo "</TABLE>\n";
		echo "<INPUT type='submit' value='Activate/Deactivate Traps' />\n";

		echo "</FORM>\n";
		
	}
	
	function display_individual_bomb_subview() {
		$bomb = $_REQUEST["bomb"];
		if (!$bomb) return;
		
		$dp = new DescriptionPanel();

		echo "<TABLE class='STD'>";
		echo "<TR>\n";
		echo "<TH class='STD' colspan='2'>Characteristics for $bomb </TH>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<TD class='STD' style='height:400px;width=300px;' rowspan='3'><IMG src='images/placeholder.gif' /></TD>\n";
		$this->show_order_panel($bomb);
		echo "</TR>";
		echo "<TR><TD class='STD' style='vertical-align:top'>";
		$this->show_stats_panel($bomb);
		echo "</TD></TR>";
		echo "<TR><TD class='STD' style='vertical-align:top;text-align:left'>";
		$this->show_dependent_panel($bomb);
		echo "</TD></TR>";
		echo "<TR>\n";
		$dp->show_text_panel($bomb, "color", "bomb", "text-align:center;font-style:italic;");
		$dp->show_text_panel($bomb, "basic", "bomb", "text-align:left;font-style:normal;");
		echo "</TR>";
		echo "<TR>\n";
		echo "<TH class='STD' colspan='2'>Discussion</TH>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<TD class='STD' colspan='2' height='200'>Discussion Content</TD>\n";
		echo "</TR>";
		echo "</TABLE>";
	}

	function show_order_panel($bomb) {
		$player_name = $_SESSION['player_name'];
		$dm = new DevelopmentModel();

		$mi = new MiscItem();
		$mi->db_fill($bomb);
		if ($dm->does_player_know_development($player_name, $mi->development_item) ) {
			$this->display_ordering_bar($bomb);
		} else {
			echo " <TD class='STD' style='background-color:882222'>Cannot Order this Bomb/Trap until $mi->development_item is developed.</TD>";
		}
	}

	function display_ordering_bar($bomb) {
		$player_name = $_SESSION['player_name'];
		$pm = new BombsModel();

		$mi = new MiscItem();
		$mi->db_fill($bomb);

		$pd = new PlayerData();
		$pd->db_fill($player_name);
		$max_mineral = floor($pd->mineral / $mi->mineral); 
		$max_organic = floor($pd->organic / $mi->organic); 
		$true_max = min($max_mineral, $max_energy);
		$current_bombs =  $pm->get_number_bombs($player_name, $bomb);
		if ($current_bombs < 0) $current_bombs = 0;
		
		echo "<FORM method='post' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_bombs'/>\n";
		echo "     <INPUT type='hidden' name='view' value='bomb'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='individual'/>\n";
		echo "     <INPUT type='hidden' name='bomb' value='$bomb'/>\n";
		echo " <TD class='STD'>";
		echo "   <TABLE class='STD' style='width:100%'>\n";
		echo "     <TR><TH class='STD'>You Own</TH>";
		echo "         <TH class='STD'>Max You Can Make</TH>";
		echo "         <TH class='STD'>To Make</TH>";
		echo "         <TH class='STD'>&nbsp;</TH></TR>  \n";
		echo "     <TR><TD class='STD'>$current_bombs</TD>";
		echo "     <TD class='STD'>$true_max</TD>\n";
		echo "     <TD class='STD'><INPUT type='text' size='6' name='$bomb' /></TD>\n";
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
		echo "<TR><TD class='STATS'>Mineral</TD><TD class='STATS'>$mi->mineral</TD></TR>";
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