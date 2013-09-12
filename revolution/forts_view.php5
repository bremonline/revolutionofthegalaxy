<?php
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('forts_model.php5'); 
	require_once('development_model.php5'); 
	require_once('description_panel.php5'); 
	require_once('misc_item.php5'); 

class FortsView {
	function display_forts_view($subview) {
		$player_name = $_SESSION['player_name'];
		if (strcmp($subview,"individual") == 0) {
			$this->display_individual_fort_subview();
		} else {
			$this->display_current_fort_build();
			
			$this->display_fort_technologies();
			echo "<br />\n";
			$this->display_forts();
			echo "<br />\n";
			$this->display_create_forts();
		}
	}

	function display_fort_technologies() {
		$player_name = $_SESSION['player_name'];
		$fm = new FortsModel();		
		$dm = new DevelopmentModel();		
		$techs = $fm->get_fort_technologies();
		
		echo "<TABLE class='STD' style='width:100%;'>\n";
		echo "<TR >";
		echo "	<TH class='STD' width='40%' style='text-align:left;'> Fort Technologies</TH>";
		echo "	<TH class='STD' width='15%'> Attack/Fort </TH>";
		echo "	<TH class='STD' width='15%'> Defense/Fort </TH>";
		echo "	<TH class='STD' width='15%'> Battle Resistance </TH>";
		echo "	<TH class='STD' width='15%'> Bomb Resistance </TH>";
		echo "</TR>\n";
		foreach ($techs as $tech_name => $details) {
			$att = $details["att"];
			$def = $details["def"];
			$battle = $details["battle"];
			$bomb = $details["bomb"];
			echo "<TR >";
			
			if ($dm->does_player_know_development($player_name, $tech_name) ) {
				echo " <TD class='STD'><A style='color:white' href='main_page.php5?view=development&subview=individual&development_item=$tech_name'> $tech_name </A></TD>";
				echo " <TD class='STD'> $att </TD>";
				echo " <TD class='STD'> $def </TD>";
				echo " <TD class='STD'> {$battle}% </TD>";
				echo " <TD class='STD'> {$bomb}% </TD>";
			} else {
				echo " <TD class='STD' style='color:808080;background-color:000000';><A style='color:808080' href='main_page.php5?view=development&subview=individual&development_item=$tech_name'> $tech_name </A> </TD>";
				echo " <TD class='STD' style='color:808080;background-color:000000';'> $att </TD>";
				echo " <TD class='STD' style='color:808080;background-color:000000';'> $def </TD>";
				echo " <TD class='STD' style='color:808080;background-color:000000';'> {$battle}% </TD>";
				echo " <TD class='STD' style='color:808080;background-color:000000';'> {$bomb}% </TD>";
			}
			echo "</TR>\n";
		}
		$stats = $fm->get_fort_stats($player_name);
		$total_att = $stats["att"];
		$total_def = $stats["def"];
		$total_battle = $stats["battle"];
		$total_bomb = $stats["bomb"];
		
		echo "<TR >";
		echo " <TD class='STD'> <B>Total</B> </TD>";
		echo " <TD class='STD'> <B>$total_att</B> </TD>";
		echo " <TD class='STD'> <B>$total_def</B> </TD>";
		echo " <TD class='STD'> <B>{$total_battle}%</B> </TD>";
		echo " <TD class='STD'> <B>{$total_bomb}%</B> </TD>";
		echo "</TR>\n";
		
		
		echo "</TABLE>\n";
	}
	
	function display_forts() {
		$player_name = $_SESSION['player_name'];
		$fm = new FortsModel();		
		$number_forts = $fm->get_number_forts($player_name);
		if ($number_forts < 0) $number_forts = 0;

		$details = $fm->get_fort_details();
		$mineral = $details["mineral"];
		$organic = $details["organic"];
		$ticks = $details["ticks"];
		
		$stats = $fm->get_fort_stats($player_name);
		$att = $stats["att"];
		$def = $stats["def"];
		$battle = $stats["battle"];
		$bomb = $stats["bomb"];
		
		$total_att = $att * $number_forts;
		$total_def = $def * $number_forts;
		
		echo "<TABLE class='STD'>\n";
		echo "<TR >";
		echo "	<TH class='STD' width='15%' style='text-align:left;'>Forts</A></B> </TH>";
		echo "	<TH class='STD' width='15%'> Cost </TH>";
		echo "	<TH class='STD' width='10%'> You Own </TH>";
		echo "	<TH class='STD' width='10%'> Attack/Fort </TH>";
		echo "	<TH class='STD' width='10%'> Defense/Fort </TH>";
		echo "	<TH class='STD' width='10%'> Total Attack </TH>";
		echo "	<TH class='STD' width='10%'> Total Defense </TH>";
		echo "	<TH class='STD' width='10%'> Battle </TH>";
		echo "	<TH class='STD' width='10%'> Bomb </TH>";
		echo "</TR>\n";
		if ($number_forts == 0) {	
			echo "<TR><TD class='STD' colspan='8'> <I> No Forts Owned </I> </TD> </TR>\n";
		} else {
			echo "	<TD class='STD' width='40%' style='text-align:left;'>Forts</TD>";
			echo "	<TD class='STD' width='10%'> {$mineral}m/{$organic}o </TD>";
			echo "	<TD class='STD' width='10%'> $number_forts </TD>";
			echo "	<TD class='STD' width='10%'> $att </TD>";
			echo "	<TD class='STD' width='10%'> $def </TD>";
			echo "	<TD class='STD' width='10%'> $total_att </TD>";
			echo "	<TD class='STD' width='10%'> $total_def </TD>";
			echo "	<TD class='STD' width='10%'> {$battle}% </TD>";
			echo "	<TD class='STD' width='10%'> {$bomb}% </TD>";
			
		}
		echo "</TABLE>\n";
	}
	
	function display_create_forts() {
		$player_name = $_SESSION['player_name'];
		$fm = new FortsModel();		
		$dm = new DevelopmentModel();		
		$number_forts = $fm->get_number_forts($player_name);
		if ($number_forts < 0) $number_forts = 0;
		$details = $fm->get_fort_details();
		$mineral = $details["mineral"];
		$organic = $details["organic"];
		$ticks = $details["ticks"];
		
		
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_forts'/>\n";
		echo "     <INPUT type='hidden' name='view' value='forts'/>\n";
		echo "<TABLE class='STD'>\n";
		echo "<TR >";
		echo "	<TH class='STD' width='70%' style='text-align:left;'> Forts </TH>";
		echo "	<TH class='STD' width='10%'> Cost </TH>";
		echo "	<TH class='STD' width='10%'> You Own </TH>";
		echo "	<TH class='STD' width='10%'> To Make </TH>";
		echo "</TR>\n";
		echo "<TR >";
		echo "	<TD class='STD' width='70%' style='text-align:left;'>";
			echo "  <B><A style='color:white' href='main_page.php5?view=forts&subview=individual&fort=fort'> Forts </A></B></TD>";
		echo "	<TD class='STD' width='10%'> {$mineral}m <br /> {$organic}o </TD>";
		echo "	<TD class='STD' width='10%'> $number_forts </TD>";
		echo "	<TD class='STD' width='10%'>";
		if ($dm->does_player_know_development($player_name, "Fort") ) {
			echo "     <INPUT type='text' size='6' name='forts' /> <BR />\n";
			echo "     <I>{$ticks} ticks</I>\n";
		} else {
			echo "&nbsp;";
		}
		echo "  </TD>";
		echo "</TR>\n";
		echo "</TABLE>\n";
		echo "  <INPUT type='submit' name='Create Forts' value='Create Forts'/>\n";
		echo "</FORM>\n";
		
	}
	
	function display_current_fort_build() {
		echo $this->make_current_fort_build_display();
	}
	
	function make_current_fort_build_display() {
		$fm = new FortsModel();
		
		$player_name = $_SESSION['player_name'];
	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='fort'
			and status='building'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return;
		$string = "";
		$string .= "<TABLE class='STD' style='width:100%;'><TR>\n";
		$string .= "<TD class='STD' >";
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$fort_type = $row->build_item;
			$number = $row->number;
			$ticks_remaining = $row->ticks_remaining;
			$total_ticks = $fm->get_total_ticks_of_fort();

			$ticks_completed = $total_ticks - $ticks_remaining;
			$percent_complete=100*$ticks_completed/$total_ticks;
			$percent_incomplete=100-$percent_complete;

			$string .= "Building: Fort ($number), ($ticks_completed/$total_ticks) completed<BR/>";
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
	
	function display_individual_fort_subview() {
		$fort = $_REQUEST["fort"];
		if (!$fort) return;
		
		$dp = new DescriptionPanel();

		echo "<TABLE class='STD'>";
		echo "<TR>\n";
		echo "<TH class='STD' colspan='2'>Characteristics for $pulse </TH>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<TD class='STD' style='height:400px;width=300px;' rowspan='4'><IMG src='images/placeholder.gif' /></TD>\n";
		$this->show_order_panel($fort);
		echo "</TR>";
		echo "<TR><TD class='STD' style='vertical-align:top'>";
		$this->show_stats_panel($fort);
		echo "</TD></TR>";
		echo "<TR><TD class='STD' style='vertical-align:top;text-align:left'>";
		$this->show_dependent_panel($fort);
		echo "</TD></TR>";
		echo "<TR><TD class='STD' style='vertical-align:top;text-align:left'>";
		$this->display_fort_technologies();
		echo "</TD></TR>";
		echo "<TR>\n";
		$dp->show_text_panel($fort, "color", "fort", "text-align:center;font-style:italic;");
		$dp->show_text_panel($fort, "basic", "fort", "text-align:left;font-style:normal;");
		echo "</TR>";
		echo "<TR>\n";
		echo "<TH class='STD' colspan='2'>Discussion</TH>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<TD class='STD' colspan='2' height='200'>Discussion Content</TD>\n";
		echo "</TR>";
		echo "</TABLE>";
	}

	function show_order_panel($fort) {
		$player_name = $_SESSION['player_name'];
		$dm = new DevelopmentModel();

		$mi = new MiscItem();
		$mi->db_fill($fort);
		if ($dm->does_player_know_development($player_name, $mi->development_item) ) {
			$this->display_ordering_bar($fort);
		} else {
			echo " <TD class='STD' style='background-color:882222'>Cannot Order Forts until $mi->development_item is developed.</TD>";
		}
	}

	function display_ordering_bar($fort) {
		$player_name = $_SESSION['player_name'];
		$fm = new FortsModel();

		$mi = new MiscItem();
		$mi->db_fill($fort);

		$pd = new PlayerData();
		$pd->db_fill($player_name);
		$max_mineral = floor($pd->mineral / $mi->mineral); 
		$max_organic = floor($pd->organic / $mi->organic); 
		$true_max = min($max_mineral, $max_organic);
		$current_forts =  $fm->get_number_forts($player_name, $fort);
		if ($current_forts < 0) $current_forts = 0;
		
		echo "<FORM method='post' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_forts'/>\n";
		echo "     <INPUT type='hidden' name='view' value='fort'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='individual'/>\n";
		echo "     <INPUT type='hidden' name='fort' value='$fort'/>\n";
		echo " <TD class='STD'>";
		echo "   <TABLE class='STD' style='width:100%'>\n";
		echo "     <TR><TH class='STD'>You Own</TH>";
		echo "         <TH class='STD'>Max You Can Make</TH>";
		echo "         <TH class='STD'>To Make</TH>";
		echo "         <TH class='STD'>&nbsp;</TH></TR>  \n";
		echo "     <TR><TD class='STD'>$current_forts</TD>";
		echo "     <TD class='STD'>$true_max</TD>\n";
		echo "     <TD class='STD'><INPUT type='text' size='6' name='$fort' /></TD>\n";
		echo "         <TD class='STD'><INPUT type='submit' value='Create' /></TD></TR>  \n";
		echo "   </TABLE> \n";
		echo " </TD>";
		echo "</FORM>\n";
	}
		
	function show_stats_panel($fort) {
		$player_name = $_SESSION['player_name'];
		$mi = new MiscItem();
		$mi->db_fill($fort);
		
		echo "<TABLE class='STATS' style='width:530px;'>";
		echo "<TR><TD class='STATS'>Mineral</TD><TD class='STATS'>$mi->mineral</TD></TR>";
		echo "<TR><TD class='STATS'>Energy</TD><TD class='STATS'>$mi->energy</TD></TR>";
		echo "<TR><TD class='STATS'>Ticks</TD><TD class='STATS'>$mi->ticks</TD></TR>";
		echo "</TABLE>";
	}

	function show_dependent_panel($fort) {
		$mi = new MiscItem();
		$mi->db_fill($fort);

		echo "Required Development:<BR />\n";
		echo "<UL>\n";
		echo "<LI><A style='color:white' href='main_page.php5?view=development&subview=individual&development_item=$mi->development_item'>$mi->development_item</A></LI>\n";
		echo "</UL>\n";	
	}
	

}
