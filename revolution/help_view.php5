<?php
	require_once('help_model.php5'); 
	require_once('description_panel.php5'); 
	

class HelpView {
	function display_help_view($subview) {
		echo "<TABLE class='STD'><TR>\n";
		$view_fns = new ViewFunctions();
		$view_fns->display_button('Main Help', '208020', '60B060', 'main_page.php5?view=help&subview=main');
		$view_fns->display_button('Topics', '804020', 'A06040', 'main_page.php5?view=help&subview=topics');
		$view_fns->display_button('Frequently Asked Questions', '604080', '6060A0', 'main_page.php5?view=help&subview=faq');
		$view_fns->display_button('New Players Discussion', '404040', '606060', 'main_page.php5?view=help&subview=new_players');
		echo "</TR></TABLE>\n";

		if (strcmp($subview, "main") == 0) $this->display_main_help_subview();
		else if (strcmp($subview, "topics") == 0) $this->display_topic_help_subview();
		else if (strcmp($subview, "faq") == 0) $this->display_faq_help_subview();
		else if (strcmp($subview, "new_players") == 0) $this->display_new_players_help_chooser();
		else if (strcmp($subview, "individual") == 0) $this->display_individual_help_subview();
	}
	
		function display_main_help_subview() {
			$dp = new DescriptionPanel();
			echo "<TABLE class='STD'>\n";
			$dp->show_text_panel("MAIN", "main", "help", "");
			echo "</TABLE>";
		}
		
		function display_topic_help_subview() {
			echo "<TABLE class='STD'>\n";	
			echo "<TR>\n";	
			echo "<TD class='STD' colspan='4'>";
			echo "Pick a topic from those below:";
			echo "</TD>\n";
			echo "</TR>\n";	
			echo "<TR>\n";
			echo "<TD class='STD' style='text-align:left;vertical-align:top;'>";
			$this->display_concept_column();
			echo "</TD>\n";
			echo "<TD class='STD' style='text-align:left;vertical-align:top;'>";
			$this->display_research_column();
			echo "</TD>\n";
			echo "<TD class='STD' style='text-align:left;vertical-align:top;'>";
			$this->display_development_column();
			echo "</TD>\n";
			echo "<TD class='STD' style='text-align:left;vertical-align:top;'>";
			$this->display_items_column();
			echo "</TD>\n";
			echo "</TR>\n";	
		}
		
		function display_faq_help_subview() {
			echo "<TABLE class='STD'><TR><TD class='STD' style='text-align:left'>\n";
			$this->display_faq_list_subcolumn();
			$this->display_faq_new_question_button();
			echo "</TD></TR></TABLE>";
		}
		
		function display_faq_new_question_button() {
			$player_name = $_SESSION['player_name'];
			$hm = new HelpModel();
			$ord = $hm->get_max_ordinality('faq', 'question');
			$neword = $ord + 1.0;
			$faq_id = "faq{$neword}";

			echo "<FORM method='get' action='main_page.php5'>";
			echo "     <INPUT type='hidden' name='view' value='help''/>\n";
//			echo "     <INPUT type='hidden' id='question_action' name='action' value='new_question'/>\n";
			echo "     <INPUT type='hidden' id='question_subview' name='subview' value='faq'/>\n";
			echo "<BR /><INPUT type='submit' value='Create New Question' id='question_button' onClick='edit_window(\"$faq_id\",\"question\", \"faq\", \"$player_name\");'/>";
			echo "</FORM>";
		}
		
		function display_faq_list_subcolumn() {
			echo "<TABLE class='STD' style='width:100%'>\n";
			echo "<TR><TH class='STD'>Frequently Asked Questions:</TH></TR>\n";
			
			$dp = new DescriptionPanel();
			$hm = new HelpModel();
			$faq_names = $hm->get_faq_information();			
			for ($count=0; $count < count($faq_names); $count++) {
				$question = $faq_names[$count]["question"];
				$question_id = $faq_names[$count]["id"];
				echo "<TR><TD class='STD' style='text-align:left' ><I>$question</I></TD></TR>\n";
				$dp->show_text_panel($question_id, "answer", "faq", "");
				echo "<TR><TH class='STD' style='text-align:left' ></TH></TR>\n";
			}
			echo "</TABLE>\n";
			
		}

		
		function display_new_players_help_chooser() {
			echo "<SPAN style='color:grey;'> New Players Discussion coming soon! </SPAN>";		
		}


		function display_concept_column() {
			echo "CONCEPTS";
			
			$this->display_add_concept_subcolumn();
			echo "<br />";
			
			$this->display_concept_list_subcolumn();
		}
		
		function display_add_concept_subcolumn() {
			echo "<FORM method='get' action='main_page.php5'>";
			echo "     <INPUT type='hidden' name='view' value='help''/>\n";
			echo "     <INPUT type='hidden' id='concept_action' name='action' value='new_concept'/>\n";
			echo "     <INPUT type='hidden' id='concept_subview' name='subview' value='individual'/>\n";
			echo "     <INPUT type='hidden' id='concept' name='concept' value='none'/>\n";
			echo "<BR /><INPUT type='submit' value='Create New Topic' id='concept_button' onClick='new_concept_button();'/>";
			echo "</FORM>";

// Old Style button, didn't look good	
//			echo "<TABLE width='100%'><TR><TD class='STD' style='background-color:202080;'
//				onMouseOver=\"this.style.backgroundColor='6060B0'\" onMouseOut=\"this.style.backgroundColor='202080'\">";
//			echo "<A style='color:white;' href='main_page.php5' id='concept' onClick='new_concept_prompter(\"main_page.php5?view=help&subview=individual\");'> Insert New Concept </A><BR />";
//			echo "</TD></TR></TABLE>";
			
		}
		
		function display_concept_list_subcolumn() {
			echo "CONCEPTS:<br /><br />";
			
			$hm = new HelpModel();
			$concept_names = $hm->get_basic_concepts();			
			for ($count=0; $count < count($concept_names); $count++) {
				$name = $concept_names[$count]["name"];
				if (strcmp($concept_names[$count]["text"], "No Description Available") == 0) $pic='redball.gif';
				else $pic='greenball.gif';
				echo "<A style='color:white;vertical-align:center' href='main_page.php5?view=help&subview=individual&concept=$name'><img border='0' src='images/$pic' />$name</A><br />";
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
				
				echo "<A style='color:white;vertical-align:center' href='main_page.php5?view=research&subview=individual&research_item=$name'><img border='0' src='images/$pic' />$name</A><br />";
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
				
				echo "<A style='color:white;vertical-align:center' href='main_page.php5?view=development&subview=individual&development_item=$name'><img border='0' src='images/$pic' />$name</A><br />";
			}
			
		}
		
		function display_items_column() {
			$this->display_creature_subcolumn();
			echo "<BR />";
			$this->display_scan_subcolumn();
			echo "<BR />";
			$this->display_pulse_subcolumn();
			echo "<BR />";
			$this->display_bomb_subcolumn();
			echo "<BR />";
			$this->display_fort_subcolumn();
		
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
				
				echo "<A style='color:white;vertical-align:center' href='main_page.php5?view=creatures&subview=individual&creature=$name'><img border='0' src='images/$pic' />$name</A> <br />";
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
				
				echo "<A style='color:white;vertical-align:center' href='main_page.php5?view=scans&subview=individual&scan=$name'><img border='0' src='images/$pic' />$name</A> <br />";
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
				
				echo "<A style='color:white;vertical-align:center' href='main_page.php5?view=pulses&subview=individual&pulse=$name'><img border='0' src='images/$pic' />$name</A><br />";
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
				
				echo "<A style='color:white;vertical-align:center' href='main_page.php5?view=bombs&subview=individual&bomb=$name'><img border='0' src='images/$pic' />$name</A><br />";
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
				
				echo "<A style='color:white;vertical-align:center' href='main_page.php5?view=forts&subview=individual&fort=$name'><img border='0' src='images/$pic' />$name</A><br />";
			}
		}
	
	function display_individual_help_subview() {
		$concept = $_REQUEST["concept"];
		if (!$concept) return;
		
		$dp = new DescriptionPanel();

		echo "<TABLE class='STD'>\n";
		echo "<TR>\n";
		$dp->show_text_panel($concept, "basic", "concept", "text-align:center;font-style:italic;");
		echo "</TR>";
		echo "</TABLE>";
	}
}
?>