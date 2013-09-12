<?php
	require_once('view_fns.php5'); 
	require_once('game_view.php5'); 
	require_once('overview_view.php5'); 
	require_once('news_view.php5'); 
	require_once('admin_view.php5'); 
	require_once('research_view.php5'); 
	require_once('development_view.php5'); 
	require_once('creatures_view.php5'); 
	require_once('universe_view.php5'); 
	require_once('structures_view.php5'); 
	require_once('fleet_view.php5'); 
	require_once('ranking_view.php5'); 
	require_once('move_view.php5'); 
	require_once('conversations_view.php5'); 
	require_once('scans_view.php5'); 
	require_once('alliance_view.php5'); 
	require_once('forts_view.php5'); 
	require_once('bombs_view.php5'); 
	require_once('pulses_view.php5'); 
	require_once('help_view.php5'); 
	require_once('chat_view.php5'); 
	require_once('vacation_view.php5'); 
	require_once('profile_view.php5'); 
	require_once('victory_view.php5'); 

// Evo chat at  http://www.thejackofclubs.net/evo/javairc/ 
// http://www.pjirc.com/main.php

Class PageView {
	
	function display_logo() {
		echo "<IMG src='revo-logo.gif' />\n";
	}
	
	function display_game_bar() {
		$gv = new GameView();
		$gv->display_game_bar();
	}
	
	function display_command_bar($admin) {
		$view_fns = new ViewFunctions();
		echo "<TABLE class='STD' style='width:150px;'>\n";

		if (strcmp ( $admin, "admin" ) == 0) {
			$view_fns->display_command_button('Admin', '800080', 'B040B0', 'main_page.php5?view=admin');
		} else if (strcmp ( $admin, "superadmin" ) == 0) {
			$view_fns->display_command_button('Admin', '800080', 'B040B0', 'main_page.php5?view=admin');
			$view_fns->display_command_button('Super-Admin', '800080', 'B040B0', 'main_page.php5?view=superadmin');
		}
		
		$view_fns->display_command_button('Overview', '000080', '4040B0', 'main_page.php5?view=overview');
		$view_fns->display_command_button('News', '000080', '4040B0', 'main_page.php5?view=news');
		$view_fns->display_command_button('Universe', '000080', '4040B0', 'main_page.php5?view=universe');
		$view_fns->display_command_button('Ranking', '000080', '4040B0', 'main_page.php5?view=rankings');
		
		$view_fns->display_command_button('Research', 'A04000', 'D08040', 'main_page.php5?view=research');
		$view_fns->display_command_button('Development', 'A04000', 'D08040', 'main_page.php5?view=development');
		$view_fns->display_command_button('Structures', 'A04000', 'D08040', 'main_page.php5?view=structures');
		
		$view_fns->display_command_button('Move', '800000', 'B04040', 'main_page.php5?view=move');
		$view_fns->display_command_button('Fleets', '800000', 'B04040', 'main_page.php5?view=fleets');
		$view_fns->display_command_button('Creatures', '800000', 'B04040', 'main_page.php5?view=creatures');
		$view_fns->display_command_button('Scans', '800000', 'B04040', 'main_page.php5?view=scans');
		$view_fns->display_command_button('Pulses/Shields<br />Blasts/Jammers', '800000', 'B04040', 'main_page.php5?view=pulses');
		$view_fns->display_command_button('Forts', '800000', 'B04040', 'main_page.php5?view=forts');
		$view_fns->display_command_button('Bombs/Traps', '800000', 'B04040', 'main_page.php5?view=bombs');

		$new_message_count = $this->get_number_new_messages();
		if ($new_message_count == 0) $message_label = "Messages ($new_message_count)";
		else $message_label = "<B> Messages ($new_message_count)</B>";
		$view_fns->display_command_button($message_label, '606000', 'A0A000', 'main_page.php5?view=messages');
		$view_fns->display_command_button('<B> NEW Forums </B>', '606000', 'A0A000', 'main_page.php5?view=forums');
		$view_fns->display_command_button('Wiki', '606000', 'A0A000', 'main_page.php5?view=wiki');
		$view_fns->display_command_button('Chat', '606000', 'A0A000', 'main_page.php5?view=chat');
		$view_fns->display_command_button('Alliances', '606000', 'A0A000', 'main_page.php5?view=alliances');
	
		$view_fns->display_command_button('Profile', '008000', '40A040', 'main_page.php5?view=profile');
		$view_fns->display_command_button('Vacation', '008000', '40A040', 'main_page.php5?view=vacation');

		$view_fns->display_command_button('Victory Conditions', '800000', 'A04040', 'main_page.php5?view=victory');
		$view_fns->display_command_button('HELP', '800000', 'A04040', 'main_page.php5?view=help');
		$view_fns->display_command_button('Logout', '800000', 'A04040', 'logout.php5');
	
		echo "</TABLE>\n";
	}
	
	function display_top_bar($player) {
		$player_name = $_SESSION['player_name'];
		$total_structures = $player->unassigned + $player->extractor + $player->genetic_lab + $player->powerplant + $player->factory;
		$total_allocated = $player->extractor + $player->genetic_lab + $player->powerplant + $player->factory;
		$ratio = ceil (1000* $total_allocated / $total_structures)/10;
		
		echo "<TABLE class='TOPBAR' ><TR><TD class='TOPBAR' >\n";
		echo "<TABLE class='TOPBAR'>\n";
		echo "  <TR>";
		echo "   <TD class='TOPBAR' style='text-align:left;' id='player_name'> (Revo+ v0.0.0) $player_name of $player->location </TD>\n";
		echo "   <TD class='TOPBAR' style='text-align:right;color:#888;'>";
		echo " Score: <SPAN style='color:#FFF;' id='score'>" . number_format($player->score) . "</SPAN>";
		echo "   </TD>\n";
		echo "  </TR>\n";
		echo "  <TR>";
		echo "   <TD class='TOPBAR' colspan='2' style='color:#888;'> ";
		echo "      Min.: <SPAN style='color:#FFF;' id='mineral'>" . number_format($player->mineral) . "</SPAN> \n";
		echo "      Org.: <SPAN style='color:#FFF;' id='organic'>" . number_format($player->organic) . "</SPAN> \n";
		echo "      Eng: <SPAN style='color:#FFF;' id='energy'>" . number_format($player->energy) . "</SPAN> \n";
		echo "   </TD>\n";
		echo "  </TR>\n";
		echo "  <TR>";
		echo "   <TD class='TOPBAR' style='color:#888;'> ";
		echo "  		Total: <SPAN style='color:#FFF;' id='total_structures'>"  . number_format($total_structures) . "</SPAN> \n";
		echo "  		Ratio: <SPAN style='color:#FFF;' id='ratio'>{$ratio}%</SPAN> \n";
		echo "  		UA: <SPAN style='color:#FFF;' id='unassigned'>"   . number_format($player->unassigned) . "</SPAN> \n";
		echo "  		Extr: <SPAN style='color:#FFF;' id='extractor'>"   . number_format($player->extractor) . "</SPAN> \n";
		echo "  		Labs: <SPAN style='color:#FFF;' id='genetic_lab'>"   . number_format($player->genetic_lab) . "</SPAN> \n";
		echo "  		Power: <SPAN style='color:#FFF;' id='powerplant'>"   . number_format($player->powerplant) . "</SPAN> \n";
		echo "  		Fac: <SPAN style='color:#FFF;' id='factory'>"   . number_format($player->factory) . "</SPAN> \n";
		echo "     <BR />    ";
		echo "  </TD>";
		echo "   <TD class='TOPBAR' colspan='2' style='text-align:right;color:#888;'> ";
		echo " Min. Tgt Sc.: <SPAN style='color:#8F8;' id='min_score'>" . number_format($player->score / 2) . "</SPAN>";
		echo " | Max Att Sc.: <SPAN style='color:#F88;' id='max_score'>" . number_format($player->score * 2) . "</SPAN>";
		echo "   </TD>\n";
		echo " </TR>\n";
		echo "</TABLE>\n";
		echo "<TD></TR></TABLE>\n";
	}
	
	function display_main_view($view, $subview) {
		if ( strcmp ($action, "research") == 0) {
			$research = new ResearchView();
			$research->start_research($research_item);
		}
		
		if ( strcmp( $view, "overview" ) == 0 ) {
			$ov = new OverviewView();
			$ov->display_overview();
		} else if ( strcmp( $view, "news" ) == 0 ) {
			$nv = new NewsView();
			$nv->display_news_view($subview);
		} else if ( strcmp( $view, "superadmin" ) == 0 ) {
			$av = new AdminView();
			$av->display_superadmin_view($subview);
		} else if ( strcmp( $view, "admin" ) == 0 ) {
			$av = new AdminView();
			$av->display_admin_view($subview);
		} else if ( strcmp( $view, "research" ) == 0 ) {
			$rv = new ResearchView();
			$rv->display_research_view($subview);
		} else if ( strcmp( $view, "development" ) == 0 ) {
			$dv = new DevelopmentView();
			$dv->display_development_view($subview);
		} else if ( strcmp( $view, "creatures" ) == 0 ) {
			$cv = new CreaturesView();
			$cv->display_creatures_view($subview);
		} else if ( strcmp( $view, "universe" ) == 0 ) {
			$uv = new UniverseView();
			$uv->display_universe_view();
		} else if ( strcmp( $view, "structures" ) == 0 ) {
			$sv = new StructuresView();
			$sv->display_structures();
		} else if ( strcmp( $view, "fleets" ) == 0 ) {
			$fv = new FleetView();
			$fv->display_fleet();
		} else if ( strcmp( $view, "rankings" ) == 0 ) {
			$rv = new RankingView();
			$rv->display_ranking($subview);
		} else if ( strcmp( $view, "conversations" ) == 0 ) {
			$cv = new ConversationsView();
			$cv->display_conversations($subview);
		} else if ( strcmp( $view, "move" ) == 0 ) {
			$mv = new MoveView();
			$mv->display_move_form();
		} else if ( strcmp( $view, "forums" ) == 0 ) {
			$this->display_forum_in_frame();
		} else if ( strcmp( $view, "messages" ) == 0 ) {
			$this->display_messages_in_frame();
		} else if ( strcmp( $view, "wiki" ) == 0 ) {
			$this->display_wiki_in_frame();
		} else if ( strcmp( $view, "scans" ) == 0 ) {
			$sv = new ScansView();
			$sv->display_scans_view($subview);
		} else if ( strcmp( $view, "alliances" ) == 0 ) {
			$av = new AllianceView();
			$av->display_alliance_view($subview);
		} else if ( strcmp( $view, "forts" ) == 0 ) {
			$fv = new FortsView();
			$fv->display_forts_view($subview);
		} else if ( strcmp( $view, "bombs" ) == 0 ) {
			$bv = new BombsView();
			$bv->display_bombs_view($subview);
		} else if ( strcmp( $view, "pulses" ) == 0 ) {
			$pv = new PulsesView();
			$pv->display_pulses_view($subview);
		} else if ( strcmp( $view, "help" ) == 0 ) {
			$hv = new HelpView();
			$hv->display_help_view($subview);
		} else if ( strcmp( $view, "chat" ) == 0 ) {
			$cv = new ChatView();
			$cv->display_chat_view($subview);
		} else if ( strcmp( $view, "vacation" ) == 0 ) {
			$vv = new VacationView();
			$vv->display_vacation_view($subview);
		} else if ( strcmp( $view, "profile" ) == 0 ) {
			$pv = new ProfileView();
			$pv->display_profile_view($subview);
		} else if ( strcmp( $view, "victory" ) == 0 ) {
			$vv = new VictoryView();
			$vv->display_victory_view($subview);
		} 
		else echo "Coming Soon: $view";
	}


	// ----------------------------------------------------------------
	// Status Functions
	
	function display_info_bar() {
  	$status_info=$_SESSION['status_info'];
		if (strlen($status_info) == 0) return; // No status to display
		echo "<TABLE class='INFO'>\n";
		echo "  <TR><TD class='INFO'>";
		echo "<IMG src='images/greenball.gif' />&nbsp;";
		echo " $status_info </TD></TR>\n";
		echo "</TABLE>\n";
		$_SESSION['status_info'] = '';
	}
	
	function display_warning_bar() {
  	$warning_info=$_SESSION['warning_info'];
		if (strlen($warning_info) == 0) return; // No status to display
		echo "<TABLE class='WARNING'>\n";
		echo "  <TR><TD class='WARNING'>";
		echo "<IMG src='images/yellowball.gif' />&nbsp;";
		echo " $warning_info </TD></TR>\n";
		echo "</TABLE>\n";
		$_SESSION['warning_info'] = '';
	}
	
	function display_error_bar() {
  	$error_info=$_SESSION['error_info'];
		if (strlen($error_info) == 0) return; // No error to display
		echo "<TABLE class='ERROR'>\n";
		echo "  <TR><TD class='ERROR'>";
		echo "<IMG src='images/redball.gif' />&nbsp;";
		echo " $error_info </TD></TR>\n";
		echo "</TABLE>\n";
		$_SESSION['error_info'] = '';
	}
		
	function display_chat_bar() {
//		$vf = new ViewFunctions();
//		echo "<TABLE class='STD' >\n";
//		echo "  <TR>";
//		echo "    <TH class='STD' style='width:113px;'> Chat: </TH>";
//		$vf->display_id_button("Main Chat [0]", "404040", "707070", "chat_main", "239px", "main_page.php5?view=chat&subview=main");
//		$vf->display_id_button("Alliance Chat [0]", "402040", "705070", "chat_alliance", "239px", "main_page.php5?view=chat&subview=alliance");
//		$vf->display_id_button("Personal Chat [0]", "406040", "709070", "chat_personal", "239px", "main_page.php5?view=chat&subview=personal");
//		echo "  </TR>\n";
//		echo "</TABLE>\n";
	}

	// ----------------------------------------------------------------
	// Misc View Functions
	

	function display_comms_bar() {
//		require('../revo_smf/SSI.php');
//		global $context; 

		echo "<TABLE class='STD'>\n";
		echo "  <TR><TD class='STD' style='text-align:left'>";
		echo " You have: " . $this->get_number_new_messages() . " Unread Messages" . 
			", <a href='../revo_smf/index.php?action=pm' target='messages' style='color:yellow;'> Click here to go to the message center </A> <br />\n";


//		echo " You have: " . $context['user']['unread_messages'] . " Unread Messages" . 
//			", <a href='../revo_smf/index.php?action=pm' target='messages' style='color:yellow;'> Click here to go to the message center </A> <br />\n";

		echo "  </TD></TR>\n";
		echo "</TABLE>\n";
	}
	
	function get_number_new_messages() {
		require_once('../revo_smf/SSI.php');
		global $context; 

		return $context['user']['unread_messages'];
	}

	function display_forum_in_frame() {
		echo "<SPAN style='color:grey;'> Note: Forum logins are currently a seperate manual process.  Please login using your existing User Account Information</SPAN><BR/>\n";
		echo "<SPAN style='color:grey;'>Note: Alliance Subforums are manually processed at the moment.  Please PM Judal for updates here.</SPAN><br/>\n";
		echo "<IFRAME width='845px' height='800px;' src='http://revolutionofthegalaxy.com/revo_smf/'>\n";
	}

	function display_messages_in_frame() {
		echo "<a href='../revo_smf/index.php?action=pm' target='messages' style='color:grey;'>Click Here to open the Message Center in a new tab</A><BR />\n";
		echo "<IFRAME width='845px' height='800px;' src='http://revolutionofthegalaxy.com/revo_smf/index.php?action=pm'>\n";
	}
	
	function display_wiki_in_frame() {
		echo "<IFRAME width='845px' height='100%' src='http://edgeoftheempire.com/wiki/pmwiki.php?n=Revolution.Revolution'>\n";
	}
}
?>