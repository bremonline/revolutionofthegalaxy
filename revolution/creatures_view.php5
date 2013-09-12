<?php
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('player_data.php5'); 
	require_once('creatures_model.php5'); 
	require_once('creature_item.php5'); 
	require_once('description_panel.php5'); 


class CreaturesView {
	
	function display_creatures_view($subview) {
		$this->display_current_creature_build();
		
		if ( strcmp( $subview, "individual" ) == 0 ) $this->display_individual_creature_subview();
		else $this->display_all_creatures_subview();
	}
	
	function display_all_creatures_subview() {
		$player_name = $_SESSION['player_name'];
	  $conn = db_connect();
		$query = "select * from creature_items";
		$result = $conn->query($query);
		echo "<FORM method='post' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_creatures'/>\n";
		echo "     <INPUT type='hidden' name='view' value='creatures'/>\n";
		echo "<TABLE class='STD'>\n";
		echo "<TR ><TH class='STD' colspan='5'> Order Creatures </TH></TR>\n";
		echo "<TR >";
		echo "	<TH class='STD' width='50%' style='text-align:left;'> Creature Type </TH>";
		echo "	<TH class='STD' width='20%'> Stats </TH>";
		echo "	<TH class='STD' width='10%'> Cost </TH>";
		echo "	<TH class='STD' width='10%'> You Own </TH>";
		echo "	<TH class='STD' width='10%'> To Make </TH>";
			
		echo "</TR>\n";
		$cm = new CreaturesModel();
		
		for ($count=0; $row = $result->fetch_object(); $count++) {
			echo " <TR>";
			echo "  <TD class='STD' style='text-align:left;'>\n";
			echo "   <B><A style='color:white' href='main_page.php5?view=creatures&subview=individual&creature=$row->name'>$row->name<A></B><BR />\n";
			echo "   <I>$row->description</I>\n";
			echo "  </TD>\n";
			echo "  <TD class='STD' style='text-align:left;'>\n";
			echo "   <TABLE class='STATS'>\n";
			echo "     <TR><TD class='STATS'>att:&nbsp;&nbsp;{$row->attack}</TD>";
			echo "         <TD class='STATS'>def:&nbsp;&nbsp;{$row->defense}</TD></TR>  \n";
			echo "     <TR><TD class='STATS'>int:&nbsp;&nbsp;{$row->intelligence}</TD>";
			echo "         <TD class='STATS'>dis:&nbsp;&nbsp;{$row->discipline}</TD></TR>  \n";
			echo "     <TR><TD class='STATS'>foc:&nbsp;&nbsp;{$row->focus}</TD>";
			echo "         <TD class='STATS'>wgt:&nbsp;&nbsp;{$row->weight}</TD></TR>  \n";
			echo "   </TABLE> \n";
			echo "  </TD>\n";
			echo "  <TD class='STD'>{$row->mineral}m <br /> {$row->organic}o</TD>\n";
			echo "  <TD class='STD'>" . $cm->get_number_of_creatures($player_name, $row->name) . "</TD>\n";
			echo "  <TD class='STD'>\n";
			if ($cm->creature_available($row->name)) {
				echo "     <INPUT type='text' size='6' name='create_{$row->name}' /> <BR />\n";
				echo "     <I>$row->ticks ticks</I>\n";
			} else {
				echo "&nbsp;";
			}
			echo "  </TD>\n";
			echo " </TR>\n";
		}
		echo "</TABLE> \n";
		echo "<INPUT type='submit' value='Create Creatures' />\n";
		echo "</FORM>\n";
	}
	
	function display_current_creature_build() {
		echo $this->make_current_creature_build_display();
	}
	
	function make_current_creature_build_display() {
		$cm = new CreaturesModel();
		
		$player_name = $_SESSION['player_name'];
	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='creature'
			and status='building'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return;
		$string = "";
		
		$string .= "<TABLE class='STD' style='width:100%;' ><TR>\n";
		$string .= "<TD class='STD' >";
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$creature = $row->build_item;
			$number = $row->number;
			$ticks_remaining = $row->ticks_remaining;
			$total_ticks = $cm->get_total_ticks_of_creature($creature);

			$ticks_completed = $total_ticks - $ticks_remaining;
			$percent_complete=100*$ticks_completed/$total_ticks;
			$percent_incomplete=100-$percent_complete;

			$string .= "Building: $creature ($number), ($ticks_completed/$total_ticks) completed<BR/>";
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

	function display_individual_creature_subview() {
		$creature = $_REQUEST["creature"];
		$dp = new DescriptionPanel();

		echo "<TABLE class='STD'>";
		echo "<TR>\n";
		echo "<TH class='STD' colspan='2'>Creature Statistics for $creature </TH>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<TD class='STD' style='height:400px;width=300px;' rowspan='3'><IMG src='images/placeholder.gif' /></TD>\n";
		$this->show_order_panel($creature);
		echo "</TR>";
		echo "<TR><TD class='STD' style='vertical-align:top'>";
		$this->show_stats_panel($creature);
		echo "</TD></TR>";
		echo "<TR><TD class='STD' style='vertical-align:top;text-align:left'>";
		$this->show_dependent_panel($creature);
		echo "</TD></TR>";
		echo "<TR>\n";
		$dp->show_text_panel($creature, "color", "creature", "text-align:center;font-style:italic;");
		$dp->show_text_panel($creature, "basic", "creature", "text-align:left;font-style:normal;");
		echo "</TR>";
		echo "<TR>\n";
		echo "<TH class='STD' colspan='2'>Discussion</TH>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<TD class='STD' colspan='2' height='200'>Discussion Content</TD>\n";
		echo "</TR>";
		echo "</TABLE>";
	}

	function show_order_panel($creature) {
		$player_name = $_SESSION['player_name'];
		$dm = new DevelopmentModel();

		$ci = new CreatureItem();
		$ci->db_fill($creature);
		if ($dm->does_player_know_development($player_name, $ci->development_item) ) {
			$this->display_ordering_bar($creature);
		} else {
			echo " <TD class='STD' style='background-color:882222'>Cannot Order this Creature until $ci->development_item is developed.</TD>";
		}
	}

	function display_ordering_bar($creature) {
		$player_name = $_SESSION['player_name'];
		$cm = new CreaturesModel();

		$ci = new CreatureItem();
		$ci->db_fill($creature);

		$pd = new PlayerData();
		$pd->db_fill($player_name);
		$max_mineral = floor($pd->mineral / $ci->mineral); 
		$max_organic = floor($pd->organic / $ci->organic); 
		$true_max = min($max_mineral, $max_organic);
			
		echo "<FORM method='post' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_creatures'/>\n";
		echo "     <INPUT type='hidden' name='view' value='creatures'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='$creature'/>\n";
		echo " <TD class='STD'>";
		echo "   <TABLE class='STD' style='width:100%'>\n";
		echo "     <TR><TH class='STD'>You Own</TH>";
		echo "         <TH class='STD'>Max You Can Make</TH>";
		echo "         <TH class='STD'>To Make</TH>";
		echo "         <TH class='STD'>&nbsp;</TH></TR>  \n";
		echo "     <TR><TD class='STD'>" . $cm->get_number_of_creatures($player_name, $creature) . "</TD>";
		echo "     <TD class='STD'>$true_max</TD>\n";
		echo "     <TD class='STD'><INPUT type='text' size='6' name='create_{$creature}' /></TD>\n";
		echo "         <TD class='STD'><INPUT type='submit' value='Create' /></TD></TR>  \n";
		echo "   </TABLE> \n";
		echo " </TD>";
		echo "</FORM>\n";
		
	}
		
	function show_stats_panel($creature) {
		$player_name = $_SESSION['player_name'];
		$ci = new CreatureItem();
		$ci->db_fill($creature);
		
		echo "<TABLE class='STATS' style='width:530px;'>";
		echo "<TR><TD class='STATS'>Mineral</TD><TD class='STATS'>$ci->mineral</TD></TR>";
		echo "<TR><TD class='STATS'>Organic</TD><TD class='STATS'>$ci->organic</TD></TR>";
		echo "<TR><TD class='STATS'>Ticks</TD><TD class='STATS'>$ci->ticks</TD></TR>";
		echo "<TR><TD class='STATS'>Attack</TD><TD class='STATS'>$ci->att</TD></TR>";
		echo "<TR><TD class='STATS'>Defense</TD><TD class='STATS'>$ci->def</TD></TR>";
		echo "<TR><TD class='STATS'>Focus</TD><TD class='STATS'>$ci->foc</TD></TR>";
		echo "<TR><TD class='STATS'>Intellience</TD><TD class='STATS'>$ci->int</TD></TR>";
		echo "<TR><TD class='STATS'>Discipline</TD><TD class='STATS'>$ci->dis</TD></TR>";
		echo "<TR><TD class='STATS'>Weight</TD><TD class='STATS'>$ci->weight</TD></TR>";
		echo "<TR><TD class='STATS'>Class</TD><TD class='STATS'>$ci->class</TD></TR>";
		echo "<TR><TD class='STATS'>Type</TD><TD class='STATS'>$ci->type</TD></TR>";
		echo "<TR><TD class='STATS'>Level</TD><TD class='STATS'>$ci->level</TD></TR>";
		echo "</TABLE>";
	}

	function show_dependent_panel($creature) {
		$ci = new CreatureItem();
		$ci->db_fill($creature);

		echo "Required Development:<BR />\n";
		echo "<UL>\n";
		echo "<LI><A style='color:white' href='main_page.php5?view=development&subview=individual&development_item=$ci->development_item'>$ci->development_item</A></LI>\n";
		echo "</UL>\n";	
	}
	
	
}
?>