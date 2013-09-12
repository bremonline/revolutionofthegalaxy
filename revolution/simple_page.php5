<?php
	require_once('misc_fns.php5'); 
	require_once('player_data.php5'); 
	require_once('page_controller.php5'); 
	require_once('page_view.php5'); 
	require_once('news_view.php5'); 
	require_once('conversations_view.php5'); 
	require_once('game_model.php5'); 
	
	
//	echo "Site down for maintenence, it is currently 10:19am.  Site should be up by 11am EST";
//	return;
	
	session_start();
	check_valid_user();
  $action=$_REQUEST['action'];
	if (strcmp($action, 'logout') == 0) {
		echo "You are now logged out.  To login again please go to the <A href='login.php5>Login Page</A><BR />\n";
		exit();
	}
	
	$gm = new GameModel();
	$gm->archive_click();
	
	do_html_header('Main Page');
	
  $db_unix_time = get_db_unix_time();
  $player_name = $_SESSION["player_name"];
  $subview = $_REQUEST["subview"];
  $type = $subview;
  $channel = $_REQUEST["channel"];
  if ($channel == '') $channel='main';
  if ($type == '') $type='main';

	$fm = new FleetModel();
	$incoming_list = $fm->get_incoming($player_name);
	
	if (count($incoming_list) > 0) $alert="style='background-color:darkred'";
	else $alert="background='background.gif'";
  
  echo "<body link='#F06000' vlink='#F09000' onload='StartClock($db_unix_time);' onunload='KillClock()' $alert >\n";
	
  $player_name=$_SESSION['player_name'];
  $view=$_REQUEST['view'];
  $subview=$_REQUEST['subview'];

	$pd = new PlayerData();
	$pd->touch($player_name);

	$_SESSION['player_data'] = $player;
	
//	echo "<TABLE><TR><TD style='vertical-align:top;'>\n";
	$pageview = new PageView();
	$nv = new NewsView();
	$convoView = new ConversationsView();
	$pagecontroller = new PageController();
	$pagecontroller->perform_action();

	$pd->db_fill($player_name);
//	$pageview->display_logo();
//	$pageview->display_game_bar($pd->admin);
//	$pageview->display_command_bar($pd->admin);
//	echo "</TD><TD style='vertical-align:top;'>\n";
//	$pageview->display_top_bar($pd);
	
	$chatView = new ChatView();
//	$chatView->display_most_recent_shout($view, $subview);
	
//	$pageview->display_chat_bar();
//	$nv->display_news_bar();
//	$convoView->display_conversation_top_bar();
	
//	$pageview->display_error_bar();
//	$pageview->display_warning_bar();
//	$pageview->display_info_bar();
	
//	$pageview->display_comms_bar();
//	echo "<br />";
	
	$pageview->display_main_view($view, $subview);
//	echo "</TD></TR>";
//	echo "</TABLE>\n";
	
	do_html_footer();

?>