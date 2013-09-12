<?php
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('research_item.php5'); 
	require_once('description_panel.php5'); 
	require_once('research_model.php5'); 

class ResearchView {
		
		
	function display_research_view($subview) {
		if ($subview == '') $subview = 'creature';
		$view_fns = new ViewFunctions();
		echo "<TABLE class='STD'><TR>\n";
		$view_fns->display_button('Creature Science', '808000', 'B0B040', 'main_page.php5?view=research&subview=creature');
		$view_fns->display_button('Energy Physics', '800080', 'B040B0', 'main_page.php5?view=research&subview=energy');
		$view_fns->display_button('Materials Science', '404040', '808080', 'main_page.php5?view=research&subview=materials');
		echo "</TR></TABLE>\n";
		$this->display_current_research();
		if ( strcmp( $subview, "creature" ) == 0 ) $this->display_research_subview('creature');
		if ( strcmp( $subview, "energy" ) == 0 ) $this->display_research_subview('energy');
		if ( strcmp( $subview, "materials" ) == 0 ) $this->display_research_subview('materials');


		if ( strcmp( $subview, "individual" ) == 0 ) $this->display_individual_research_subview();
	}
	
	function display_current_research() {
		echo $this->make_current_research_display();
	}
	
	function make_current_research_display() {
		$player_name = $_SESSION['player_name'];
	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='research'
			and status='researching'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return;
		$row = $result->fetch_object();
		$research_item = $row->build_item;
		$ticks_remaining = $row->ticks_remaining;
		
		$query = "select ticks from research_items where name='$research_item'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		$ticks_completed = $row->ticks - $ticks_remaining;
		$percent_complete=ceil(100*$ticks_completed/$row->ticks);
		$percent_incomplete=floor(100-$percent_complete);
		
		$string = "";
		
	  
		$string .= "<TABLE class='STD' style='width:100%'><TR>\n";
		$string .= "<TD class='STD' >";
		$string .= "Researching: $research_item, ($ticks_completed/$row->ticks) completed<BR/>";
		$string .= "
		<TABLE class='BAR' width='80%' >
			<TR>
				<TD class='BAR' style='width:{$percent_complete}%;background-color:darkgrey'>&nbsp;<TD>
				<TD class='BAR' style='width:{$percent_incomplete}%;background-color:black'>&nbsp;<TD>
			</TR>
		</TABLE>\n";

		$string .= "</TD>";
		$string .= "</TR></TABLE>\n";
		
		return $string;
	}

	function display_research_subview($type) {
		$player_name = $_SESSION['player_name'];
		$research_item = new ResearchItem();
		$max_level = $research_item->get_max_level_of_research($type);


		$rm = new ResearchModel();
		$currently_researching = $rm->get_currently_researching($player_name);

	  $conn = db_connect();
		echo "<TABLE class='STD'>\n";
		for ($i=$max_level;$i>=1;$i--) {
			$query = "select * from research_items where type='$type' and level=$i";	
			$result = $conn->query($query);
			echo "<TR>\n";
			for ($count=0; $row = $result->fetch_object(); $count++) {
				$research_item->populate($row);
				$this->display_individual_research_item($research_item, $currently_researching);
			}
			echo "</TR>\n";
		}
	}
	
	function display_individual_research_item($research_item, $currently_researching) {
			$player_name = $_SESSION['player_name'];
			$rm = new ResearchModel();

				$over_color='808080';
				if ($this->is_researched($research_item->name)) $researchable_color = '006000';
				else $researchable_color = '404040';

				if ($rm->is_researchable($player_name, $research_item->name) && !$this->is_researched($research_item->name)) {
					$border_color ='FFFF00';
				}

				if ($research_item->name == $currently_researching) {
					$researchable_color = '600000';
					$border_color ='FFFF00';
				}

				echo "  <TD class='STD' colspan='$research_item->size' 
					style='background-color:$researchable_color;border-color:$border_color'
					onMouseOver=\"this.style.backgroundColor='$over_color'\" 
					onMouseOut=\"this.style.backgroundColor='$researchable_color'\"
					onClick=\"location.href='main_page.php5?view=research&subview=individual&research_item=$research_item->name'\">\n";

				echo "
				    <TABLE style='margin:0px auto;' width='100%' style='verticle-align:top;text-align:right'>\n
				      <TR><TD class='RESEARCH-CENTER' colspan='2'>$research_item->name</TD></TR>\n
				      <TR>\n
				        <TD class='RESEARCH-LEFT'>{$research_item->ticks}t</TD>\n
				        <TD class='RESEARCH-RIGHT'>{$research_item->mineral}m<br />{$research_item->organic}o</TD>\n
				      </TR>\n
				    </TABLE>\n";
				    

				echo "  </TD>\n";	
	}

// old click: onClick=\"location.href='main_page.php5?view=research&subview={$research_item->type}&action=research&research_item=$research_item->name'\">\n";
	
	function display_individual_research_subview() {
		$research_item = $_REQUEST["research_item"];
		$dp = new DescriptionPanel();
		
		echo "<TABLE class='STD'>";
		echo "<TR>\n";
		echo "<TH class='STD' colspan='2'>Research for $research_item </TH>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<TD class='STD' style='height:400px;width=300px;' rowspan='3'><IMG src='images/placeholder.gif' /></TD>\n";
		$this->show_order_panel($research_item);
		echo "</TR>";
		echo "<TR><TD class='STD' style='vertical-align:top'>";
		$this->show_stats_panel($research_item);
		echo "</TD></TR>";
		echo "<TR><TD class='STD' style='vertical-align:top;text-align:left'>";
		$this->show_dependent_panel($research_item);
		echo "</TD></TR>";
		echo "<TR>\n";
		$dp->show_text_panel($research_item, "color", "research", "");
		$dp->show_text_panel($research_item, "basic", "research", "");
		echo "</TR>";
		echo "<TR>\n";
		echo "<TH class='STD' colspan='2'>Discussion</TH>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<TD class='STD' colspan='2' height='200'>Discussion Content</TD>\n";
		echo "</TR>";
		echo "</TABLE>";
	}

	function show_order_panel($research_item) {
		$player_name = $_SESSION['player_name'];
		$vf = new ViewFunctions();
		$rm = new ResearchModel();
		
		if ($this->is_researched($research_item) ) {
			echo "<TD class='STD' style='background-color:228822'>Already Research</TD>";
		} else if ( $rm->is_researchable($player_name, $research_item) ) {
			$vf->display_button("Start Research", "40A000", "60C000", 
				"main_page.php5?view=research&action=research&subview=individual&research_item=$research_item");
		} else if (strcmp($rm->get_currently_researching($player_name), $research_item) == 0 ) {
			echo "<TD class='STD' style='background-color:888822'>Currently Researching</TD>";
		} else {
			echo "<TD class='STD' style='background-color:882222'>Unable to research at this time</TD>";
		}
	}
	
	function show_stats_panel($research_item) {
		$ri = new ResearchItem();
		$ri->db_fill($research_item);
		
		echo "<TABLE class='STATS' style='width:530px;'>";
		echo "<TR><TD class='STATS'>Mineral</TD><TD class='STATS'>$ri->mineral</TD></TR>";
		echo "<TR><TD class='STATS'>Organic</TD><TD class='STATS'>$ri->organic</TD></TR>";
		echo "<TR><TD class='STATS'>Ticks</TD><TD class='STATS'>$ri->ticks</TD></TR>";
		echo "</TABLE>";
		
	}

	function show_dependent_panel($research_item) {
		$ri = new ResearchItem();
		$dependent_developments = $ri->get_dependent_developments($research_item);
		echo "Dependent Developments:<BR />\n";
		echo "<UL>\n";
		if ( count($dependent_developments) == 0) {
				echo "<LI><I>None</I></LI>\n";		
		} else {
			for ($i=0; $i < count($dependent_developments); $i++) {
				$development = $dependent_developments[$i];
				echo "<LI><A style='color:white' href='main_page.php5?view=development&subview=individual&development_item=$development'>$development</A></LI>\n";
			}
		}
		echo "</UL>\n";	

		$ancestors = $ri->get_ancestors($research_item);
		if ( count ($ancestors) > 0) {
			echo "This Research is dependent on:<BR />\n";
			echo "<UL>\n";
			for ($i=0; $i < count($ancestors); $i++) {
				$ancestor = $ancestors[$i];
				echo "<LI><A style='color:white' href='main_page.php5?view=research&subview=individual&research_item=$ancestor'>$ancestor</A></LI>\n";
			}
		}
		echo "</UL>\n";	
		
		$dependents = $ri->get_dependents($research_item);
		if ( count ($dependents) > 0) {
			echo "The following research items are dependent on this research:<BR />\n";
			echo "<UL>\n";
			for ($i=0; $i < count($dependents); $i++) {
				$dependent = $dependents[$i];
				echo "<LI><A style='color:white' href='main_page.php5?view=research&subview=individual&research_item=$dependent'>$dependent</A></LI>\n";
			}
		}
		echo "</UL>\n";	

	}

	function is_researched($research_name) {
		$player_name = $_SESSION['player_name'];

	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='research'
			and build_item='$research_name' and status='completed'";	
		$result = $conn->query($query);
		if ($result->num_rows>0) return true;
		else return false;	
	}

	function is_researchable($research_name) {
		$player_name = $_SESSION['player_name'];
		
		// If anything is currently being researched, then nothing is researchable until it is done.
	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='research'
			and status='researching'";
		$result = $conn->query($query);
		if ($result->num_rows>0) return false;		

		$query = "select * from research_items where name='$research_name'";	
		$result = $conn->query($query);
		$row = $result->fetch_object();
		$pre1=$row->pre1;
		$pre2=$row->pre2;
		$pre3=$row->pre3;
		
		$researchable = true;
		if ($pre1 != '') {
			$query = "select * from player_build where player_name='$player_name' 
				and build_type='research'
				and build_item='$pre1' and status='completed'";	
			$result = $conn->query($query);
			if ($result->num_rows==0) $researchable = false;  	
		}
		if ($pre2 != '') {
			$query = "select * from player_build where player_name='$player_name' 
				and build_type='research'
				and build_item='$pre2'";	
			$result = $conn->query($query);
			if ($result->num_rows==0) $researchable = false;  	
		}
		if ($pre3 != '') {
			$query = "select * from player_build where player_name='$player_name' 
				and build_type='research'
				and build_item='$pre3'";	
			$result = $conn->query($query);
			if ($result->num_rows==0) $researchable = false;  	
		}
		return $researchable;	
	}
	
	
}

?>