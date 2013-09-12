<?php
	require_once("alliance_model.php5");
	require_once("description_panel.php5");
	require_once("view_fns.php5");
	
class AllianceView {
	function display_alliance_view($subview) {
		$player_name=$_SESSION["player_name"];

		$am = new AllianceModel();
		
		$this->display_alliance_bar();

		if (strcmp($subview, "rankings") == 0) $am->display_alliance_ranking("main_page.php5?view=alliances&subview=rankings");
		else if (strcmp($subview, "details") == 0) $this->display_alliance_details_subview();
		else if (strcmp($subview, "declarations") == 0) $this->display_alliance_declarations_subview();
		else if (strcmp($subview, "create") == 0) $this->display_create_alliance();
		else if (strcmp($subview, "launches") == 0) $this->display_alliance_launches();

	}
	
	
	function display_alliance_bar() {
		$player_name=$_SESSION["player_name"];
		$am = new AllianceModel();
		$alliance = $am->get_alliance_of_player($player_name);

		echo "<TABLE class='STD'><TR>\n";
		$view_fns = new ViewFunctions();
		$view_fns->display_button('Alliance Rankings', 'A00080', 'E040B0', 'main_page.php5?view=alliances&subview=rankings');
		$view_fns->display_button('Alliance Details', '700080', 'B000B0', 'main_page.php5?view=alliances&subview=details');
		$view_fns->display_button('Alliance Declarations', '500080', '800080', 'main_page.php5?view=alliances&subview=declarations');
		$view_fns->display_button('Create New Alliance', '200080', '600080', 'main_page.php5?view=alliances&subview=create');

		if ($am->is_senior($player_name, $alliance) )
				$view_fns->display_button('Alliance Launches', '200060', '600080', 'main_page.php5?view=alliances&subview=launches');

		echo "</TR></TABLE>\n";
		
	}
	
	function display_alliance_rankings_subview() {
		$vf = new ViewFunctions();
	  $conn = db_connect();
	  
	  
		$query = "select * from alliance";
		$result = $conn->query($query);
		echo "<TABLE class='STD' >\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='4'>Alliance Rankings</TH>\n";
		echo " </TR>\n";
		if ($result->num_rows == 0) {
			echo "  <TD class='STD' colspan='5'> No Players in the game ??? </TD>\n";
		} else {
			echo " <TR>\n";
			$vf->display_button('Alliance', '008000', '40B040', 'main_page.php5?view=alliances&subview=rankings&order=name');
			$vf->display_button('Shorthand', '008000', '40B040', 'main_page.php5?view=alliances&subview=rankings&order=shorthand');
			$vf->display_button('Members', '008000', '40B040', 'main_page.php5?view=alliances&subview=rankings&order=members');
			$vf->display_button('Structures', '008000', '40B040', 'main_page.php5?view=alliances&subview=rankings&order=structures');
			$vf->display_button('Score', '008000', '40B040', 'main_page.php5?view=alliances&subview=rankings&order=score');
			$vf->display_button('Details', '008000', '40B040', 'main_page.php5?view=alliances&subview=rankings&order=score');
			echo " </TR>\n";
			for ($count=0; $row = $result->fetch_object(); $count++) {
				$total_structures = $row->unassigned + $row->extractor + $row->genetic_lab + $row->powerplant + $row->factory;
				echo " <TR>\n";
				echo "  <TD class='STD'> - {$row->alliance_name} </TD>\n";
				if (strcmp(trim($row->shorthand)) > 0) echo "  <TD class='STD'> {$row->shorthand} </TD>\n";
				else echo "  <TD class='STD'> &nbsp; </TD>\n";
				echo "  <TD class='STD'> {$row->members} </TD>\n";
				echo "  <TD class='STD'> {$row->total_structures} </TD>\n";
				echo "  <TD class='STD'> {$row->score}</TD>\n";
				$vf->display_button('Details', '404000', '808040', "main_page.php5?view=alliances&subview=details&alliance={$row->alliance_name}");
				echo " </TR>\n";
			}
		}
		echo "</TABLE>\n";	
	}

	function display_alliance_details_subview() {
		$am = new AllianceModel();
 		$alliance=$_REQUEST["alliance"];
		$player_name=$_SESSION["player_name"];
		
		if (strlen($alliance) == 0) {
			$alliance = $am->get_alliance_of_player($player_name);
		}

		if (strlen($alliance) == 0) {
			return;
		}
		
		$rank = $am->get_rank_of_player($player_name, $alliance);
		if (strcmp($rank, "Leader") == 0 ) {
			$this->display_alliance_details_as_leader($alliance);
		} else if (strcmp($rank, "Senior") == 0 ) {
			$this->display_alliance_details_as_senior($alliance);
		} else if (strcmp($rank, "Member") == 0 ) {
			$this->display_alliance_details_as_member($alliance);
		} else {
			$this->display_alliance_details_as_nonmember($alliance);
		}
	}
	
	function display_alliance_details_as_leader($alliance) {
		$player_name=$_SESSION["player_name"];
		$am = new AllianceModel();
		$description=$am->get_alliance_description($alliance);
		$leader = $am->get_alliance_leader($alliance);
		$player_alliance = $am->get_alliance_of_player($player_name);
		$vf = new ViewFunctions();

		echo "<TABLE class='STD'>";
		echo "  <TR><TH class='STD' colspan='2'> $alliance </TH></TR>\n";
		echo "  <TR><TD class='STD'> &nbsp </TD>";
		$vf->display_button("Disband Alliance", '400000', 'B00000', 
				"main_page.php5?view=alliances&subview=details&action=disband_alliance&alliance=$alliance");
		echo "  </TR>\n";
		$this->display_editable_description($alliance);
		echo "  <TR><TD class='STD' style='vertical-align:top;' width='425px'>";
		$this->display_members($alliance, true, true, true);
		echo "</TD>";
		echo "<TD class='STD' style='vertical-align:top;' width='425px'>";
		$this->display_applicants($alliance, true, true, true);
		echo "</TD></TR>\n";
		echo "  <TR><TD class='STD' style='vertical-align:top;'>";
		$this->display_current_declarations($alliance, true, true, true);
		echo "</TD>";
		echo "<TD class='STD' style='vertical-align:top;'>";
		$this->display_declaration_updates($alliance, true, true, true);
		echo "</TD></TR>\n";
		echo "  </TABLE>";
		echo "  </TABLE>";
	}

	function display_alliance_details_as_senior($alliance) {
		$player_name=$_SESSION["player_name"];
		$am = new AllianceModel();
		$description=$am->get_alliance_description($alliance);
		$leader = $am->get_alliance_leader($alliance);
		$player_alliance = $am->get_alliance_of_player($player_name);
		$vf = new ViewFunctions();

		echo "<TABLE class='STD'>";
		echo "  <TR><TH class='STD' colspan='2'> $alliance </TH></TR>\n";
		echo "  <TR><TD class='STD'> &nbsp </TD>";
		$vf->display_button("Leave Alliance", '400000', 'B00000', 
				"main_page.php5?view=alliances&subview=details&action=leave_alliance&alliance=$alliance");
		echo "  </TR>\n";
		$this->display_editable_description($alliance);
		echo "  <TR><TD class='STD' style='vertical-align:top;' width='425px'>";
		$this->display_members($alliance, false, true, true);
		echo "</TD>";
		echo "<TD class='STD' style='vertical-align:top;' width='425px'>";
		$this->display_applicants($alliance, false, true, true);
		echo "</TD></TR>\n";
		echo "  <TR><TD class='STD' style='vertical-align:top;'>";
		$this->display_current_declarations($alliance, false, true, true);
		echo "</TD>";
		echo "<TD class='STD' style='vertical-align:top;'>";
		$this->display_declaration_updates($alliance, false, true, true);
		echo "</TD></TR>\n";
		echo "  </TABLE>";
	}

	function display_alliance_details_as_member($alliance) {
		$player_name=$_SESSION["player_name"];
		$am = new AllianceModel();
		$description=$am->get_alliance_description($alliance);
		$leader = $am->get_alliance_leader($alliance);
		$player_alliance = $am->get_alliance_of_player($player_name);
		$vf = new ViewFunctions();

		echo "<TABLE class='STD'>";
		echo "  <TR><TH class='STD' colspan='2'> $alliance </TH></TR>\n";
		echo "  <TR><TD class='STD'> &nbsp </TD>";
		$vf->display_button("Leave Alliance", '400000', 'B00000', 
				"main_page.php5?view=alliances&subview=details&action=leave_alliance&alliance=$alliance");
		echo "  </TR>\n";
		$this->display_uneditable_description($alliance);
		echo "  </TR>\n";
		echo "  <TR><TD class='STD' style='vertical-align:top;' width='425px'>";
		$this->display_members($alliance, false, false, true);
		echo "</TD>";
		echo "<TD class='STD' style='vertical-align:top;' width='425px'>";
		$this->display_applicants($alliance, false, false, true);
		echo "</TD></TR>\n";
		echo "  <TR><TD class='STD' style='vertical-align:top;'>";
		$this->display_current_declarations($alliance, false, false, true);
		echo "</TD>";
		echo "<TD class='STD' style='vertical-align:top;'>";
		$this->display_declaration_updates($alliance, false, false, true);
		echo "</TD></TR>\n";
		echo "  </TABLE>";
	}

	function display_alliance_details_as_nonmember($alliance) {
		$player_name=$_SESSION["player_name"];
		$am = new AllianceModel();
		$description=$am->get_alliance_description($alliance);
		$leader = $am->get_alliance_leader($alliance);
		$player_alliance = $am->get_alliance_of_player($player_name);
		$vf = new ViewFunctions();

		echo "<TABLE class='STD'>";
		echo "  <TR><TH class='STD' colspan='2'> $alliance </TH></TR>\n";
		echo "  <TR><TD class='STD'> &nbsp </TD>";

		if ($am->check_for_specific_application($player_name, $alliance)) {
			$vf->display_button("Withdraw application to $alliance", '000080', '4040B0', "main_page.php5?view=alliances&subview=details&action=withdraw_application&alliance=$alliance");
		} else if (!$am->check_for_application($player_name) && $player_alliance == NULL ) {
			$vf->display_button("Apply to $alliance", '000080', '4040B0', "main_page.php5?view=alliances&subview=details&action=apply_to_alliance&alliance=$alliance");
		} else {
			echo "  <TD class='STD'> Not Applicable </TD>";
		}
		$this->display_uneditable_description($alliance);
		echo "  </TR>\n";
		echo "  <TR><TD class='STD' style='vertical-align:top;' width='425px'>";
		$this->display_members($alliance, false, false, false);
		echo "</TD>";
		echo "<TD class='STD' style='vertical-align:top;' width='425px'>";
		$this->display_applicants($alliance, false, false, false);
		echo "</TD></TR>\n";
		echo "  <TR><TD class='STD' style='vertical-align:top;'>";
		$this->display_current_declarations($alliance, false, false, false);
		echo "</TD>";
		echo "<TD class='STD' style='vertical-align:top;'>";
		$this->display_declaration_updates($alliance, false, false, false);
		echo "</TD></TR>\n";
		echo "  </TABLE>";
	}

	function display_members($alliance, $is_leader, $is_senior, $is_member) {
		$am = new AllianceModel();
		$seniors = $am->get_members($alliance, "Senior");
		$members = $am->get_members($alliance, "Member");
		$leader = $am->get_alliance_leader($alliance);
		$vf = new ViewFunctions();
	
		echo "<TABLE class='STD' style='width:100%;'>";
		echo "  <TR><TH class='STD' colspan='3'> Leader </TH></TR>\n";
		echo "<TD class='STD'> $leader </TD>";
		echo "</TR>\n";
		echo "  </TABLE>";
		
		echo "<TABLE class='STD' style='width:100%;'>";
		echo "  <TR><TH class='STD' colspan='3'> Seniors </TH></TR>\n";
		foreach($seniors as $senior) {
			echo "  <TR>";
			echo "<TD class='STD'> $senior </TD>";
			if ($is_leader) $vf->display_button("Give Leadership", '000080', '4040B0', 
				"main_page.php5?view=alliances&subview=details&action=promote_to_leader&alliance=$alliance&member=$senior");
			if ($is_senior) $vf->display_button("Demote", '808000', 'B0B040', 
				"main_page.php5?view=alliances&subview=details&action=demote_to_member&alliance=$alliance&member=$senior");
			if ($is_senior) $vf->display_button("Kick", '800000', 'B04040', 
				"main_page.php5?view=alliances&subview=details&action=kick_member&alliance=$alliance&member=$senior");
			echo "</TR>\n";
		}
		echo "  </TABLE>";

		echo "<TABLE class='STD' style='width:100%;'>";
		echo "  <TR><TH class='STD' colspan='3'> Members </TH></TR>\n";
		foreach($members as $member) {
			echo "  <TR>";
			echo "<TD class='STD'> $member </TD>";
			if ($is_senior) $vf->display_button("Promote", '000080', '4040B0', 
				"main_page.php5?view=alliances&subview=details&action=promote_to_senior&alliance=$alliance&member=$member");
			if ($is_senior) $vf->display_button("Kick", '800000', 'B04040', 
				"main_page.php5?view=alliances&subview=details&action=kick_member&alliance=$alliance&member=$member");
			echo "</TR>\n";
		}
		echo "  </TABLE>";
	}
	
	function display_applicants($alliance, $is_leader, $is_senior, $is_member) {
		$am = new AllianceModel();
		$applicants = $am->get_applicants($alliance);
		$vf = new ViewFunctions();
		
		echo "<TABLE class='STD' style='width:100%;'>";
		echo "  <TR><TH class='STD'> Applicants </TH></TR>\n";
		foreach($applicants as $applicant) {
			echo "  <TR>";
			echo "<TD class='STD'> $applicant </TD>";
			if ($is_senior) $vf->display_button("Accept", '000080', '4040B0', 
				"main_page.php5?view=alliances&subview=details&action=accept_applicant&alliance=$alliance&applicant=$applicant");
			if ($is_senior) $vf->display_button("Reject", '800000', 'B04040', 
				"main_page.php5?view=alliances&subview=details&action=reject_applicant&alliance=$alliance&applicant=$applicant");
			echo "</TR>\n";
			
		}
		echo "  </TABLE>";
	}

	function display_editable_description($alliance) {
		$dp = new DescriptionPanel();
		echo "<TR><TD class='STD' style='vertical-align:top' colspan='2'>";
		$dp->show_text_panel_inside($alliance, "description", "alliance", "");
		echo "</TD></TR>\n";
		
/*		
		echo "<TR><TD class='STD' colspan='2'>";
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='edit_alliance_description'/>\n";
		echo "     <INPUT type='hidden' name='view' value='alliances'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='details'/>\n";
		echo "<TEXTAREA name='description' rows='5' cols='100'>$description</TEXTAREA>";
		echo "<BR /> <INPUT type='submit' value='Edit Description'>\n";
		echo "</TD></TR>\n";
		echo "</FORM>\n";
*/
	}
	
	function display_uneditable_description($alliance) {
		$dp = new DescriptionPanel();
		echo "<TR><TD class='STD' style='vertical-align:top' colspan='2'>";
		$dp->show_text_panel_uneditable_inside($alliance, "description", "alliance", "");
		echo "</TD></TR>\n";
	}
		
	function display_current_declarations($alliance, $is_leader, $is_senior, $is_member) {
		$vf = new ViewFunctions();
		$am = new AllianceModel();
		echo "<TABLE class='STD' style='width:100%;'>";
		echo "  <TR><TH class='STD'> Declaring Alliance </TH><TH class='STD'> Target Alliance </TH>
			<TH class='STD'> Type </TH><TH class='STD'>Until</TH><TH class='STD'>Time</TH></TR>\n";
		$declaration_list = $am->get_declarations_by_alliance($alliance);
		echo "<TR>";
		foreach($declaration_list as $target_alliance => $delaration_details) {
			$type = $delaration_details['type'];
			
			if (strcmp($type,"War") == 0) $color = '600000';
			else if (strcmp($type,"NAP") == 0) $color = '808000';
			else $color = '008000';
			
			$until_tick = $delaration_details['until_tick'];
			$description = $delaration_details['description'];
			$time = $delaration_details['time'];
			if ($until_tick < 1) $until_tick = '&nbsp;';
			echo "<TR>";
			echo "<TD class='STD' style='background-color:$color'>$alliance</TD>\n";
			echo "<TD class='STD' style='background-color:$color'>$target_alliance</TD>\n";
			echo "<TD class='STD' style='background-color:$color'>$type</TD>\n";
			echo "<TD class='STD' style='background-color:$color'>$until_tick</TD>\n";
			echo "<TD class='STD' style='background-color:$color'>$time</TD>\n";
			if ($is_senior) {
				$vf->display_button("Remove Declaration", "$color", '808080', "main_page.php5?view=alliances&subview=details&action=remove_declaration&target_alliance=$target_alliance");
			}
			echo "</TR>";
			echo "<TR>";
			if ($is_senior) {
				echo "<TD class='STD' colspan='6' style='background-color:$color'>$description</TD>\n";
			} else {
				echo "<TD class='STD' colspan='5' style='background-color:$color'>$description</TD>\n";
			}
			echo "</TR>";
			echo "<TR></TR>";
		}
		
		echo "</TABLE>";
		
	}
	
	function display_declaration_updates($alliance, $is_leader, $is_senior, $is_member) {
		if (!$is_senior) {
			echo "&nbsp;";
			return;  // Show nothing if not senior access
		}
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_declaration'/>\n";
		echo "     <INPUT type='hidden' name='view' value='alliances'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='details'/>\n";
		echo "<TABLE class='STD' style='width:100%;'>";
		echo "  <TR><TH class='STD'> New Declarations </TH></TR>\n";
		echo "  <TR><TD class='STD'> Alliance Name <TD><TD class='STD' style='text-align:left'>$alliance</TD></TR>\n";
		echo "  <TR><TD class='STD'> Target Alliance <TD>";
		echo "     <TD class='STD' style='text-align:left'>";
		$this->display_alliance_select();
		echo "</TD></TR>\n";
		echo "  <TR><TD class='STD'> Declaration Type <TD><TD class='STD' style='text-align:left'>";
		echo "<SELECT name='type'>";
			echo "<OPTION name='war'>War</OPTION>";
			echo "<OPTION name='ally'>Ally</OPTION>";
			echo "<OPTION name='NAP'>NAP</OPTION>";
		echo "</SELECT>";
		echo "</TD></TR>\n";
		echo "  <TR><TD class='STD'> Until Tick # <TD><TD class='STD' style='text-align:left'><INPUT type='text' name='ticks' size='8' /></TD></TR>\n";

		echo "  <TR><TD class='STD'> Message <TD><TD class='STD' style='text-align:left'><TEXTAREA name='description' rows='5' cols='40'></TEXTAREA></TD></TR>\n";
		echo "  </TABLE>";
		echo "<INPUT type='submit' value='Create New Declaration'/> ";
		echo "</FORM>\n";

	}
	
	function display_alliance_select() {
		$am = new AllianceModel();
		$alliance_list = $am->get_list_of_alliances();
		echo "<SELECT name='target_alliance'>";
		foreach ($alliance_list as $alliance_name) {
			echo "<OPTION name='$alliance_name'>$alliance_name</OPTION>";
		}
		echo "</SELECT>";
	}
	
	function display_create_alliance() {
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_alliance'/>\n";
		echo "     <INPUT type='hidden' name='view' value='alliances'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='details'/>\n";
		echo "<TABLE class='STD'><CAPTION class='STD'>You are not currently part of any alliance</CAPTION>\n";
		echo "  <TR><TD class='STD'> Alliance Name <TD><TD class='STD' style='text-align:left'><INPUT type='text' name='alliance_name' size='50' /></TD></TR>\n";
		echo "  <TR><TD class='STD'> TAG <TD><TD class='STD' style='text-align:left'><INPUT type='text' name='shorthand' size='8' /></TD></TR>\n";
		  
		echo "  </TABLE>";
		echo "<INPUT type='submit' value='Create New Alliance'/> ";
		echo "</FORM>\n";
		
	}
	
	function display_alliance_declarations_subview() {
		echo "<BR />\n";
		$this->display_alliance_declaration_by_type("War");
		echo "<BR />\n";
		$this->display_alliance_declaration_by_type("NAP");
		echo "<BR />\n";
		$this->display_alliance_declaration_by_type("Ally");
	}
	
	function display_alliance_declaration_by_type($type) {
		$vf = new ViewFunctions();
		if (strcmp($type,"War") == 0) { $color = '600000'; $highlight_color = '800000'; }
		else if (strcmp($type,"NAP") == 0) { $color = '808000'; $highlight_color = 'A0A000'; }
		else { $color = '008000'; $highlight_color = '00A000'; }
		
		
		echo "<TABLE class='STD'><CAPTION class='STD' style='background-color:$color' >$type Declarations</CAPTION>\n";
		echo "  
			<TR>
				<TH class='STD'> Alliance Making Declaration </TH>
				<TH class='STD'> Target Alliance </TH>
				<TH class='STD'> Until Tick </TH>
				<TH class='STD'> Time Declared</TH>
			</TR>\n";
		$am = new AllianceModel();
		$decl_list = $am->get_all_declarations_by_type($type);
		
		$count_decl_list = count($decl_list);
		for ($i=0;$i < $count_decl_list; $i++) {
		$alliance = $decl_list[$i]["alliance"];
		$target_alliance = $decl_list[$i]["target_alliance"];
		$type = $decl_list[$i]["type"];
		$until_tick = $decl_list[$i]["until_tick"];
		if ($until_tick == 0) $until_tick = "&nbsp;";
		$time = $decl_list[$i]["time"];
		$description = $decl_list[$i]["description"];
		echo "<TR>";
		$vf->display_button($alliance, $color, $highlight_color, "main_page.php5?view=alliances&subview=details&alliance={$alliance}");
		$vf->display_button($target_alliance, $color, $highlight_color, "main_page.php5?view=alliances&subview=details&alliance={$target_alliance}");
		echo "
				<TD class='STD' style='background-color:$color'> $until_tick </TD>
				<TD class='STD' style='background-color:$color'> $time </TD>
			</TR>
			<TR>
				<TD class='STD' style='background-color:$color' colspan='4'> $description </TD>
			</TR><TR></TR><TR></TR>\n";
		}
		echo "</TABLE>\n";

	}

	function display_alliance_launches() {
		$player_name=$_SESSION["player_name"];

		$fm = new FleetModel();
		$fv = new FleetView();
		$am = new AllianceModel();
		
		$alliance = $am->get_alliance_of_player($player_name);
		if (!$am->is_senior($player_name, $alliance) ) {
			echo "Only Seniors can view the Alliance Launches";
			return;
		}
		
		$members = $am->get_all_members($alliance);
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();


		$string = "";
		$string .= "<TABLE class='STD'>\n";
		$string .= $fv->make_timetable_header($current_tick, $current_tick-20, $current_tick+20);
		
		for ($i=0; $i < count($members) ;$i++) {
			$string .= $fv->make_timetable_display($members[$i], $current_tick-20, $current_tick+20);
			$string .= "<TR></TR>\n";
		}

		$string .= "</TABLE>\n";
		echo $string;


	}

	
}


?>