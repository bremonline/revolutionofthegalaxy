<?php
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('description_panel.php5'); 
	require_once('development_item.php5'); 

class DevelopmentView {

	function display_development_view($subview) {
		$this->display_current_development();

		if ( strcmp( $subview, "individual" ) == 0 ) $this->display_individual_development_subview();
		else $this->display_development_subview();
		
	}
	
	function display_development_subview() {

		echo "<TABLE class='STD'>\n";
		echo " <TR>\n";
		$this->display_technologies('Master Creature Technologies', 'creature', 'master', 1);
		$this->display_technologies('Master Energy Technologies', 'energy', 'master', 1);
		$this->display_technologies('Master Materials Technologies', 'materials', 'master', 1);
		echo " </TR>";
		echo " <TR>\n";
		$this->display_technologies('Expert Creature Technologies', 'creature', 'expert', 2);
		$this->display_technologies('Expert Energy Technologies', 'energy', 'expert', 2);
		$this->display_technologies('Expert Materials Technologies', 'materials', 'expert', 2);
		echo " </TR>";
		echo " <TR>\n";
		$this->display_technologies('Basic Creature Technologies', 'creature', 'basic', 8);
		$this->display_technologies('Basic Energy Technologies', 'energy', 'basic', 8);
		$this->display_technologies('Basic Materials Technologies', 'materials', 'basic', 8);
		echo " </TR>";
		echo "</TABLE>\n";
	}

	function display_technologies($section_title, $type, $proficiency, $num_choices) {
		$player_name = $_SESSION['player_name'];
		$dm = new DevelopmentModel();
		$ready
		 = true;
		$current_development = $dm->get_currently_developing($player_name);
		if ($current_development == false) $ready = false;
		$num_developments = $this->number_developments_at_profiency($player_name, $type, $proficiency);
		if ($num_developments >= $num_choices) $ready = false;
	  $conn = db_connect();
		$query = "select * from development_items where type='$type' and proficiency='$proficiency'";
		$result = $conn->query($query);
		echo "  <TD class='STD' style='vertical-align:top;'> <B>$section_title ($num_choices)</B><BR /><BR />\n";
		echo " <TABLE class='LIST'>\n";
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$this->display_individual_technology_bar($row->name, $row->ticks, $current_development, $ready);
		}
		echo " </TABLE>\n";
		echo "</TD>\n";
	} 
	
	function display_individual_technology_bar($development_item, $ticks, $current_development, $ready) {
		
		$over_color='404040';
		if (strcmp($current_development, $development_item) == 0) {
			$developable_color = '600000';
			$text_color = 'FFFFFF';
			$ready_for_new_development = false;
		} else if ($this->is_developed($development_item)) {
			$over_color='208020';
			$developable_color = '006000';
			$text_color = 'FFFFFF';
		} else { 
			$developable_color = '202020';
			$text_color = '808080';
		}
		if ($this->is_developable($development_item) && !$this->is_developed($development_item) ) {
			$border_color ='FFFF00';
			$text_color = 'FFFFFF';
		} else {
			if ($this->is_developable($development_item)) $text_color = 'FFFFFF';
			$border_color ='F0F0F0';
		}
		echo "  <TD class='STD'  
			style='background-color:$developable_color;border-color:$border_color; color:$text_color;'
			onMouseOver=\"this.style.backgroundColor='$over_color'\" 
			onMouseOut=\"this.style.backgroundColor='$developable_color'\"
			onClick=\"location.href='main_page.php5?view=development&subview=individual&development_item=$development_item'\">\n";
		
		echo "$development_item ($ticks)";
		echo "</TD></TR>\n";
	}
	
	function display_individual_development_subview() {
		$development_item = $_REQUEST["development_item"];
		$dp = new DescriptionPanel();
		
		echo "<TABLE class='STD'>";
		echo "<TR>\n";
		echo "<TH class='STD' colspan='2'>Development for $development_item </TH>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<TD class='STD' style='height:400px;width=300px;' rowspan='3'><IMG src='images/placeholder.gif' /></TD>\n";
		$this->show_order_panel($development_item);
		echo "</TR>";
		echo "<TR><TD class='STD' style='vertical-align:top'>";
		$this->show_stats_panel($development_item);
		echo "</TD></TR>";
		echo "<TR><TD class='STD' style='vertical-align:top;text-align:left'>";
		$this->show_dependent_panel($development_item);
		echo "</TD></TR>";
		echo "<TR>\n";
		$dp->show_text_panel($development_item, "color", "development", "text-align:center;font-style:italic;");
		$dp->show_text_panel($development_item, "basic", "development", "text-align:left;font-style:normal;");
		echo "</TR>";
		echo "<TR>\n";
		echo "<TH class='STD' colspan='2'>Discussion</TH>\n";
		echo "</TR>";
		echo "<TR>\n";
		echo "<TD class='STD' colspan='2' height='200'>Discussion Content</TD>\n";
		echo "</TR>";
		echo "</TABLE>";
	}

	function show_order_panel($development_item) {
		$player_name = $_SESSION['player_name'];
		$dm = new DevelopmentModel();
		$vf = new ViewFunctions();
		
		if ($this->is_developed($development_item) ) {
			$vf->display_confirmable_button("Remove Development", "228822", "AA4444", 
				"main_page.php5?view=development&subview=individual&action=remove_development&development_item=$development_item");
		} else if ($this->is_developable($development_item) == false ) {
			echo "<TD class='STD' style='background-color:882222'>Unable to develop this technology until the proper knowledge is researched.</TD>";
		} else if (strcmp($dm->get_currently_developing($player_name), $development_item) == 0 ) {
			$vf->display_confirmable_button("Cancel Development", "882222", "AA4444", 
				"main_page.php5?view=development&subview=individual&action=cancel_development&development_item=$development_item");
		} else if ($dm->get_currently_developing($player_name) != false) {
			echo "<TD class='STD' style='background-color:882222'>Currently Developing a different technology</TD>";			
		} else if ($this->is_developable($development_item) ) {
			$vf->display_button("Start Development", "888800", "CCCC00", 
				"main_page.php5?view=development&subview=individual&action=develop&development_item=$development_item");
		} else {
			echo "<TD class='STD' style='background-color:882222'>Unable to develop this at this time</TD>";
		}
	}
	
	function show_stats_panel($development_item) {
		$player_name = $_SESSION['player_name'];

		$di = new DevelopmentItem();
		$di->db_fill($development_item);
		
		$dm = new DevelopmentModel();
		if (strcmp($di->proficiency,"basic") == 0) $allowed_developments = 6;
		else if (strcmp($di->proficiency,"expert") == 0) $allowed_developments = 2;
		else $allowed_developments = 1;
		$current_developments = $dm->get_current_developments($player_name, $di->type, $di->proficiency);
		
		echo "<TABLE class='STATS' style='width:530px;'>";
		echo "<TR><TD class='STATS'>Proficiency</TD><TD class='STATS'>$di->proficiency</TD></TR>";
		echo "<TR><TD class='STATS'>Type</TD><TD class='STATS'>$di->type</TD></TR>";
		echo "<TR><TD class='STATS'>Number of Developments for $di->type:$di->proficiency</TD><TD class='STATS'>$current_developments/$allowed_developments</TD></TR>";
		echo "<TR><TD class='STATS'>Ticks</TD><TD class='STATS'>$di->ticks</TD></TR>";
		echo "</TABLE>";
	}

	function show_dependent_panel($development_item) {
		$di = new DevelopmentItem();
		$di->db_fill($development_item);
		echo "Required Research:<BR />\n";
		echo "<UL>\n";
		echo "<LI><A style='color:white' href='main_page.php5?view=research&subview=individual&research_item=$di->dependent_research'>$di->dependent_research</A></LI>\n";
		echo "</UL>\n";	
		
		$cm = new CreaturesModel();
		$creature_name = $cm->get_creature_for_development($development_item);
		if ($creature_name) {
			echo "Creatures made available by this technology:<BR />\n";
			echo "<UL>\n";
			echo "<LI><A style='color:white' href='main_page.php5?view=creatures&subview=individual&creature=$creature_name'>$creature_name<A></LI>\n";
			echo "</UL>\n";				
		}

		$sm = new ScansModel();
		$scan_list = $sm->get_scans_for_development($development_item);
		if (count($scan_list) > 0) {
			echo "Scans made available by this technology:<BR />\n";
			echo "<UL>\n";
			for ($i=0; $i<count($scan_list); $i++) { 
				echo "<LI><A style='color:white' href='main_page.php5?view=scans&subview=individual&scan=$scan_list[$i]'>$scan_list[$i]</A></LI>\n";
			}
			echo "</UL>\n";				
		}
		
		$im = new ItemsModel();
		$item_list = $im->get_items_for_development($development_item);
		if (count($item_list) > 0) {
			echo "Items made available by this technology:<BR />\n";
			echo "<UL>\n";
			for ($i=0; $i<count($item_list); $i++) { 
				echo "<LI>$item_list[$i]</LI>\n";
			}
			echo "</UL>\n";				
		}
		
	}
		
	function number_developments_at_profiency($player_name, $type, $proficiency) {
	  $conn = db_connect();
		$query = "select count(*) as count from development_items di, player_build pb where 
					  di.name = pb.build_item
				and pb.build_type='development'
				and pb.player_name = '$player_name' 
				and di.type='$type'
				and proficiency='$proficiency'
				and pb.status='completed'";
				
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->count;
		
	}

	function is_developed($development_item) {
		$player_name = $_SESSION['player_name'];

	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='development'
			and build_item='$development_item' and status='completed'";	
		$result = $conn->query($query);
		if ($result->num_rows>0) return true;
		else return false;	
	}
	
	function is_developable($development_item) {
		$player_name = $_SESSION['player_name'];

	  $conn = db_connect();
		$query = "select * from development_items di, player_build pb where 
    		di.dependent_research = pb.build_item
    		and pb.build_type='research'
    		and pb.status = 'completed'
				and pb.player_name='$player_name'
				and di.name = '$development_item'";	
		$result = $conn->query($query);
		if ($result->num_rows>0) return true;
		else return false;	
	} 
	
	function get_current_development() {
		$player_name = $_SESSION['player_name'];
	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='development'
			and status='developing'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return false;
		$row = $result->fetch_object();
		return $row->build_item;		
	}
	
	function display_current_development() {
		echo $this->make_current_development_display();
	}
	
	function make_current_development_display() {
		$player_name = $_SESSION['player_name'];
	  $conn = db_connect();
		$query = "select * from player_build where player_name='$player_name' 
			and build_type='development'
			and status='developing'";
		$result = $conn->query($query);
		if ($result->num_rows==0) return;
		$row = $result->fetch_object();
		$development_item = $row->build_item;
		$ticks_remaining = $row->ticks_remaining;
		
		$query = "select ticks from development_items where name='$development_item'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		$ticks_completed = $row->ticks - $ticks_remaining;
		$percent_complete=100*$ticks_completed/$row->ticks;
		$percent_incomplete=100-$percent_complete;
		
		$string = "";
	  
		$string .= "<TABLE class='STD' style='width:100%;'><TR>\n";
		$string .= "<TD class='STD' >";
		$string .= "Developing: $development_item, ($ticks_completed/$row->ticks) completed<BR/>";
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

}

?>