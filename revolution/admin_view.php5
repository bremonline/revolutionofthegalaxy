<?php
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('news_view.php5'); 
	require_once('fleet_view.php5'); 
	require_once('fleet_model.php5'); 
	require_once('player_data.php5'); 
 
class AdminView {
// ---  Admin Functions
 	function display_admin_view($subview) {
 		$player_name=$_SESSION['player_name'];
 		$pd = new PlayerData();
 		
		if ($pd->is_admin($player_name) == false) {
			show_error("You tried to access a page that you were not supposed to access.  
				This infraction has been logged, please explain to an admin");
			$nm = new NewsModel();
			$nm->add_new_news('judal', 'player', 'misc', "$player_name tried to access admin pages", "eom" );
			$nm->add_new_news($player_name, 'player', 'misc', "You tried to access admin pages", "please explain to an admin" );
			return;
		}

		$vf = new ViewFunctions();
		echo "<TABLE class='STD'><TR>\n";
		$vf->display_button('Advance Ticks', '808000', 'B0B040', 'main_page.php5?view=admin&subview=advance');
		$vf->display_button('Display News', '800080', 'B040B0', 'main_page.php5?view=admin&subview=news');
		$vf->display_button('Show Timeline', '404040', '808080', 'main_page.php5?view=admin&subview=timeline');
		echo "</TR></TABLE>\n";
		 		
 		echo "<BR />\n";
 		if ($subview == 'advance') $this->display_advance_tick();
 		else if ($subview == 'news') {
 			 		$this->display_news_selection();
 					$this->show_individual_news();
 					$this->display_quick_news();
 		} else if ($subview == 'admin_show_news') {
 			 		$this->display_news_selection();
 					$this->show_individual_news();
 		}
 		else if ($subview == 'timeline') $this->display_full_timeline();
 	}

	function display_advance_tick() {
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='view' value='admin'/>\n";
		echo "     <INPUT type='hidden' name='action' value='advance_tick'/>\n";
		echo "<TABLE class='STD'><TR>\n";
 		echo " <TD class='STD' style='width:80%'>Advance Tick(s):<TD>\n";
 		echo " <TD class='STD'> <input type='text' size='10' name='num_ticks' value='1'/> </TD>\n";
 		echo " <TD class='STD'>\n";
 		echo " <TD class='STD'> <input type='submit' name='advance' value='Advance'/> </TD>\n";
 		echo "</TR></TABLE>\n";
		echo "</FORM>\n";
		
	}
	
	function display_quick_news() {
		$nv = new NewsView();
		$nv->display_quick_news();
	}

	function show_individual_news() {
	 	$id=$_REQUEST['id'];
	 	if ($id > 0) {
	 		$nv = new NewsView();
			$nv->display_individual_news($id);
			echo " <BR/>\n";
		}
	}

	function display_news_selection() {
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='view' value='admin'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='admin_show_news'/>\n";
		echo "<TABLE class='STD'>";
 		echo " <TR><TH class='STD' colspan='3'>Show News</TH></TR>\n";
 		echo " <TR><TH class='STD'>Article #</TH><TH class='STD'>Player</TH></TR>\n";
 		echo " <TR>\n";
 		echo " <TD class='STD'> <input type='text' size='10' name='id' /> </TD>\n";
 		echo " <TD class='STD'> <input type='submit' name='show_news' value='Show News'/> </TD>\n";
 		echo "</TR></TABLE>\n";
		echo "</FORM>\n";
		
	}


// ---  Super Admin Functions	
 	function display_superadmin_view($subview) {
 		echo "<BR />\n";
 		$this->display_promote_view();
 		echo "<BR />\n";
 		$this->display_reset_game_view();
 		
 	}

 	function display_promote_view() {
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='view' value='superadmin'/>\n";
		echo "     <INPUT type='hidden' name='action' value='promote_player'/>\n";
		echo "<TABLE class='STD'><TR>\n";
 		echo " <TD class='STD'>Promote Player:<TD>\n";
 		echo " <TD class='STD'> <input type='text' size='50' name='promoted_player' /> </TD>\n";
 		echo " <TD class='STD'>\n";
 		echo "  <SELECT name='admin_type' />\n";
 		echo "   <OPTION value='admin'>Admin</OPTION>\n";
 		echo "   <OPTION value='superadmin'>Superadmin</OPTION>\n";
 		echo "  </SELECT>\n";
 		echo " </TD>\n";
 		echo " <TD class='STD'> <input type='submit' name='promote' value='Promote'/> </TD>\n";
 		echo "</TR></TABLE>\n";
		echo "</FORM>\n";
 	}

	function display_reset_game_view() {
		echo "<FORM method='get' action='main_page.php5'>\n";
 		echo "     <INPUT type='hidden' name='view' value='superadmin'/>\n";
		echo "     <INPUT type='hidden' name='action' value='reset_game'/>\n";
		echo "<TABLE class='STD'>\n";
 		echo "   <TR><TD class='STD'> New Name: </TD><TD class='STD' style='text-align:left;'> <input type='text' name='new_name' size='100'/> </TD></TR>\n";
 		echo "   <TR><TD class='STD'> Base Resources: </TD><TD class='STD' style='text-align:left;'> <input type='text' name='base' size='20'/> </TD></TR>\n";
 		echo "   <TR><TD class='STD'> Starting Structures: </TD><TD class='STD' style='text-align:left;'> <input type='text' name='unassigned' size='20'/> </TD></TR>\n";
 		echo "   <TR><TD class='STD'> Ticks Per Day: </TD><TD class='STD' style='text-align:left;'> <input type='text' name='tpd' size='5'/> </TD></TR>\n";
		echo "   <TR><TH class='STD'><input type='submit' name='reset' value='Reset Game' /></TH>\n</TR>\n";
 		
 		echo "</TABLE>\n";
		echo "</FORM>\n";
		
	} 	
	
	function display_full_timeline() {
		$fm = new FleetModel();
		$fv = new FleetView();
		$launchers = $fm->get_all_players_launching();
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();


		$string = "";
		$string .= "<TABLE class='STD'>\n";
		$string .= $fv->make_timetable_header($current_tick, $current_tick-20, $current_tick+20);
		
		for ($i=0; $i < count($launchers) ;$i++) {
			$string .= $fv->make_timetable_display($launchers[$i], $current_tick-20, $current_tick+20);
			$string .= "<TR></TR>\n";
		}

		$string .= "</TABLE>\n";
		echo $string;
	}
}
 
 ?>