<?php
	require_once('chat_model.php5'); 
	require_once('chat_extensions.php5'); 
	require_once('../alliance_model.php5'); 
	require_once('../misc_fns.php5'); 
	require_once('../player_data.php5'); 
	require_once('../game_model.php5'); 

session_name("DEVREV");
session_start();

check_valid_user();

$am = new AllianceModel();
$cm = new ChatModel();

$player_name = $_SESSION['player_name'];
if ($player_name == '') show_error_page();

$alliance = $am->get_alliance_of_player($player_name);
$is_senior = $am->is_senior($player_name, $alliance);



echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
echo "<head>\n";
echo "<title>Revolution Chat Page</title>\n";
echo "  <link rel='icon' href='./favicon-revo-chat.ico' type='image/x-icon'>\n";
echo "<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />\n";
//echo "  <script src='../scripts/prototype-1.6.0.js' type='text/javascript' />\n";
//echo "  <script src='../scripts/mootools-1.11.js' type='text/javascript' />\n";
echo "  <script src='chat.js' type='text/javascript' > </script>\n";
echo "  <link rel='Stylesheet' href='chat.css' title='Style' type='text/css'/>\n";
echo "  <link rel='Stylesheet' href='../revolution.css' title='Style' type='text/css'/>\n";
echo "</head>\n";
echo "<body style='background-color:#696;' onload='setupPanes();select_general_pane(\"Revolution\");startChat(\"$player_name\");'>\n";
echo "  <script src='../scripts/wz_tooltip.js' type='text/javascript' > </script>\n";
	
	
build_status_bar();

echo "<div id='active_area'>\n";

echo "<TABLE>\n";
echo "  <TR id='navs'>\n";
echo "    <TD class='nav' onClick='show_choices(\"General_choices\");' id='General_nav'>General</TD>\n";
echo "    <TD class='nav' onClick='show_choices(\"Alliance_choices\");' id='Alliance_nav'>Alliance</TD>\n";
if ($is_senior) echo "    <TD class='nav' onClick='show_choices(\"Senior_choices\");' id='Senior_nav'>Alliance Senior</TD>\n";
//echo "    <TD class='nav' onClick='show_choices(\"Group_choices\");' id='Group_nav'>Group</TD>\n";
echo "    <TD class='nav' onClick='show_choices(\"Personal_choices\");' id='Personal_nav'>Personal</TD>\n";

echo "  </TR>\n";
echo "</TABLE>\n";

echo "<div id='choice_selectors'>\n";
build_general_choices();
build_alliance_choices();
build_senior_choices();
//build_group_choices();
build_personal_choices();
echo "</div>";

echo "<TABLE><TR>\n";

build_people_pane();

echo "  <TD class='panes' id='chat_panel'> \n";

$general_channels = $cm->get_general_channels($alliance);
for ($i=0; $i<count($general_channels); $i++) {
	build_chat_pane("General", $general_channels[$i]);
}

$alliance_channels = $cm->get_alliance_channels($alliance);
for ($i=0; $i<count($alliance_channels); $i++) {
	build_chat_pane("Alliance", $alliance_channels[$i]);
}

if ($is_senior) {
	$senior_channels = $cm->get_senior_channels($alliance);
	for ($i=0; $i<count($alliance_channels); $i++) {
		build_chat_pane("Senior", $senior_channels[$i]);
	}
}

/*
$group_channels = $cm->get_group_channels($alliance, 0);
for ($i=0; $i<count($group_channels); $i++) {
	build_chat_pane("Group", $group_channels[$i]);
}
build_additional_groups_pane();
*/

$personal_channels = $cm->get_personal_channels($player_name, 0);
for ($i=0; $i<count($personal_channels); $i++) {
	build_chat_pane("Personal", $personal_channels[$i]);
}

build_new_personal_contact_pane();

	echo "  <TR><TD class='panes'>&nbsp;</TD><TD class='panes'>\n";
	echo "     <FORM method='GET' action='chat_page.php5?view=chat' onsubmit='sendChatText(); return false;'>";
	echo "     <INPUT TYPE='text' id='text_message' style='width:600' /><INPUT TYPE='submit' id='send_button' value='Send' />\n";
	echo "     </FORM>";
build_send_buttons();
	echo "   </TD></TR>";



echo "  </TD>\n"; // chat_panel
echo "</TR>\n";


echo "</TABLE>\n";

echo "</div>\n"; // active_area

build_spans();

echo "</body>\n";
echo "</html>\n";

/////////////////////////////////////////
// Support Functions


function build_status_bar() {
	$player_name = $_SESSION['player_name'];
	
	echo "<TABLE><TR>\n";
	echo "<TD class='status_bar' id='chat_status' onClick='reset_online_time(\"$player_name\", true)' > $Active</TD>";
	echo "</TR>";
	echo "</TABLE>\n";
}


function show_error_page() {
	echo "Your session has expired, please close this window, reload the page and try again";
	exit;
}

function build_people_pane() {
	echo "  <TD class='panes' id='chat_people' style='width:150px;text-align:center;' >&nbsp;</TD>\n"; // chat_people
}

function build_chat_pane($type, $pane_name) {
	$player_name = $_SESSION['player_name'];
	
	echo " <TABLE id='{$type}_{$pane_name}_pane' class='panes'>\n";
	echo "  <TR><TD style='color:white;text-align:center;'><B>$type ($pane_name)</B></TD></TR>\n";
	echo "  <TR><TD class='chat_area'><div id='{$type}_{$pane_name}_chat' colspan='4' style='height:500px;width:670px;overflow:auto;text-align:left;'></div></TD></TR>\n";


	echo "  </TABLE>\n";	
}

function build_new_personal_contact_pane() {
	$cm = new ChatModel();
	$pd = new PlayerData();

	echo " <TABLE id='Personal_NewContact_pane' class='panes'>\n";
	echo "  <TR>\n";
	echo "   <TD style='color:white;text-align:center;'><B>Contact Player:</B></TD>\n";

	echo "   <TD style='color:white;text-align:center;'>\n";
	echo "    <SELECT id='personal_goto_select' onChange='show_goto_button(\"personal_goto\")'>";
	$player_list = $pd->get_all_player_names();
	$count_players = count($player_list);
	echo "     <OPTION name=''></OPTION>\n";
	for ($i=0; $i<$count_players; $i++) {
		if ($player_list[$i] != $player_name) 
			echo "     <OPTION name='$player_list[$i]'>$player_list[$i]</OPTION>\n";
	}
	echo"    </SELECT></TD>\n";
	echo "   </TD>\n";

	echo "   <TD id='personal_goto_button' style='color:white;text-align:left;z-index:-1;display:none'>";
	echo "    <INPUT type='button' value='Contact Person' onClick='select_personal_by_dropdown();'/>";
	echo "   </TD>";
	echo "  </TR>\n";
	echo "  </TABLE>\n";	
	
	}

function build_additional_groups_pane() {
	$cm = new ChatModel();
	$group_channels = $cm->get_group_channels($player_name, 0);


	echo " <DIV id='Group_Additional_pane'>\n";
	echo " <TABLE>\n";

	// Goto
	echo "  <TR>\n";
	echo "   <TD style='color:white;text-align:left;'><B>Goto Groups (any that you are a member):</B></TD>";
	echo "   <TD style='color:white;text-align:left;'>\n";
	echo "<SELECT id='group_goto_select' onChange='show_goto_button(\"group_goto\")' >\n";
	echo "<option name='none'>&nbsp;</option>";
	for ($i = 0; $i < count($group_channels); $i++) {
		 echo "<option name='$group_channels[$i]'>$group_channels[$i]</option>";
	}
	echo "</SELECT>\n";
	echo "</TD>\n";
	echo "   <TD id='group_goto_button' style='color:white;text-align:left;z-index:-1;display:none'>";
	echo "    <INPUT type='button' value='Goto Group' onClick='select_group_by_dropdown();'/>";
	echo "   </TD>";
	echo "  </TR>\n";

	// Delete
	echo "  <TR>\n";
	echo "   <TD style='color:white;text-align:left;'><B>Delete Group (any that you are an admin):</B></TD>";
	echo "   <TD style='color:white;text-align:left;'>\n";
	echo "<SELECT id='group_delete_select' onChange='show_goto_button(\"group_delete\")'>\n";
	echo "<option name='none'>&nbsp;</option>";
	for ($i = 0; $i < count($group_channels); $i++) {
		 echo "<option name='$group_channels[$i]'>$group_channels[$i]</option>";
	}
	echo "</SELECT>\n";
	echo "</TD>\n";
	echo "   <TD id='group_delete_button' style='color:white;text-align:left;z-index:-1;display:none'>";
	echo "    <INPUT type='button' value='Delete Group' />";
	echo "   </TD>";
	echo "  </TR>\n";

	// Modify
	echo "  <TR>\n";
	echo "   <TD style='color:white;text-align:left;'><B>Modify Group (any that you are an admin):</B></TD>";
	echo "   <TD style='color:white;text-align:left;'>\n";
	echo "<SELECT id='group_modify_select' onChange='show_goto_button(\"group_modify\")'>\n";
	echo "<option name='none'>&nbsp;</option>";
	for ($i = 0; $i < count($group_channels); $i++) {
		 echo "<option name='$group_channels[$i]'>$group_channels[$i]</option>";
	}
	echo "</SELECT>\n";
	echo "</TD>\n";
	echo "   <TD id='group_modify_button' style='color:white;text-align:left;z-index:-1;display:none'>";
	echo "    <INPUT type='button' value='Modify Group' />";
	echo "   </TD>";
	echo "  </TR>\n";

	// Create
	echo "  <TR>\n";
	echo "   <TD style='color:white;text-align:left;'><B>Create Group (any that you are an admin):</B></TD>";
	echo "   <TD style='color:white;text-align:left;'><INPUT type='text' id='group_create_text' onkeypress='show_create_button()' />\n ";

	echo "</TD>\n";
	echo "   <TD id='group_create_button' style='color:white;text-align:left;z-index:-1;display:none'>";
	echo "    <INPUT type='button' value='Create Group' />";
	echo "   </TD>";
	echo "  </TR>\n";

	echo " </TABLE>\n";
	echo " <TABLE style='width:100%;'>\n";
	echo "  <TR>\n";
	echo "   <TD style='color:white;text-align:left;' colspan='2'>\n";
	echo "<DIV style='color:white;border:white solid 1px;padding: 2px 10px 2px'>";
	echo " <B>Create New Group:</B><br /><br />\n";
	echo " <B>Name of Group <INPUT name='new_group_name'></B><br />\n";
	echo " <B>Add Member</B>";
	
	echo "<SELECT name='player_select'>";
	$pd = new PlayerData();
	$player_list = $pd->get_all_player_names();
	$count_players = count($player_list);
	for ($i=0; $i<$count_players; $i++) {
		if ($player_list[$i] != $player_name) 
			echo "<OPTION name='$player_list[$i]'>$player_list[$i]</OPTION>\n";
	}
	echo"</SELECT><br />\n";
	
	
	echo "</TD>\n";
	echo "  </TR>\n";


	echo "  </TABLE>\n";	
	echo " </DIV>\n"; // Group_Additional_pane
}

function build_general_choices() {
	$player_name = $_SESSION['player_name'];

	$cm = new ChatModel();
	$general_channels = $cm->get_general_channels($player_name);
	
	echo "<div id='General_choices' class='choice_selector' style='display:none;'>\n";
	for ($i=0; $i<count($general_channels); $i++) {
		echo "  <DIV class='choice' id='General_{$general_channels[$i]}_choice' onClick='select_general_pane(\"{$general_channels[$i]}\");'>$general_channels[$i]</DIV>\n";
	}
	echo "</div>\n\n";	
}

function build_alliance_choices() {
	$player_name = $_SESSION['player_name'];

	$am = new AllianceModel();
	$cm = new ChatModel();
	$alliance = $am->get_alliance_of_player($player_name);
	$alliance_channels = $cm->get_alliance_channels($alliance);
	
	echo "<div id='Alliance_choices' class='choice_selector' style='display:none;'>\n";
	for ($i=0; $i<count($alliance_channels); $i++) {
		echo "  <DIV class='choice' id='Alliance_{$alliance_channels[$i]}_choice' onClick='select_alliance_pane(\"{$alliance_channels[$i]}\");'>$alliance_channels[$i]</DIV>\n";
	}
	echo "</div>\n\n";	
}

function build_senior_choices() {
	$player_name = $_SESSION['player_name'];

	$am = new AllianceModel();
	$cm = new ChatModel();
	$alliance = $am->get_alliance_of_player($player_name);
	$senior_channels = $cm->get_senior_channels($alliance);
	
	echo "<div id='Senior_choices' class='choice_selector' style='display:none;'>\n";
	for ($i=0; $i<count($senior_channels); $i++) {
		echo "  <DIV class='choice' id='Senior_{$senior_channels[$i]}_choice' onClick='select_senior_pane(\"{$senior_channels[$i]}\");'>$senior_channels[$i]</DIV>\n";
	}
	echo "</div>\n\n";	
}

function build_group_choices() {
	$player_name = $_SESSION['player_name'];

	$cm = new ChatModel();
	$group_channels = $cm->get_group_channels($player_name, 10);
	
	echo "<div id='Group_choices' class='choice_selector' style='display:none;'>\n";
	for ($i=0; $i<count($group_channels); $i++) {
		echo "  <DIV class='choice' id='Group_{$group_channels[$i]}_choice' onClick='select_group_pane(\"{$group_channels[$i]}\");'>$group_channels[$i]</DIV>\n";
	}
	echo "  <DIV class='choice' id='Group_Additional_choice' onClick='select_group_pane(\"Additional\");'>Additional Groups...</DIV>\n";
	echo "</div>\n\n";	
}

function build_personal_choices() {
	$player_name = $_SESSION['player_name'];

	$cm = new ChatModel();
	$personal_channels = $cm->get_personal_channels($player_name, 10);
	
	echo "<div id='Personal_choices' class='choice_selector' style='display:none;'>\n";
	for ($i=0; $i<count($personal_channels); $i++) {
		echo "  <DIV class='choice' id='Personal_{$personal_channels[$i]}_choice' onClick='select_personal_pane(\"{$personal_channels[$i]}\");'>$personal_channels[$i]</DIV>\n";
	}
	echo "  <DIV class='choice' id='Personal_NewContact_choice' onClick='select_personal_pane(\"NewContact\");'>Contact Player ...</DIV>\n";
	echo "</div>\n\n"; // Personal_choices	
}

function build_spans() {
	$ce = new ChatExtensions();
	
	$top_bar_string = $ce->get_top_bar();	
	echo "<DIV id='top_bar' style='display:none' >$top_bar_string</DIV>\n";

	$current_items_box_string = $ce->get_current_items_box();	
	echo "<DIV id='current_items_box' style='display:none' >$current_items_box_string</DIV>\n";

	$fleet_box_string = $ce->get_fleet_box();	
	echo "<DIV id='fleet_box' style='display:none' >$fleet_box_string</DIV>\n";

	$build_box_string = $ce->get_build_box();	
	echo "<DIV id='build_box' style='display:none' >$build_box_string</DIV>\n";
	
}

function build_send_buttons() {
	$player_name = $_SESSION['player_name'];
	$gm = new GameModel();
	$tick = $gm->get_current_tick();
	$ce = new ChatExtensions();

	
	echo " <INPUT name='send_top_bar' id='send_top_bar' type='Button' value='Top Bar for Tick #$tick' ";
	echo "    onmouseover='TagToTip(\"top_bar\", DELAY, 1000, ABOVE, true)' onclick='sendTopBar();return false;'/>";
	$top_bar_string = $ce->get_top_bar_wrapped($player_name, $tick);
	echo " <INPUT name='top_bar_data' id='top_bar_data' type='hidden' value='{$top_bar_string} '/>";

	echo " <INPUT name='send_current_items_box' id='send_current_items_box' type='Button' value='Items for Tick #$tick' ";
	echo "    onmouseover='TagToTip(\"current_items_box\", DELAY, 1000, ABOVE, true)' onclick='sendCurrentItemsBox();return false;'/>";
	$current_items_box_string = $ce->get_current_items_box_wrapped($player_name, $tick);
	echo " <INPUT name='current_items_box_data' id='current_items_box_data' type='hidden' value='{$current_items_box_string} '/>";

	echo " <INPUT name='send_fleet_box' id='send_fleet_box' type='Button' value='Fleet for Tick #$tick' ";
	echo "    onmouseover='TagToTip(\"fleet_box\", DELAY, 1000, ABOVE, true)' onclick='sendFleetBox();return false;'/>";
	$fleet_box_string = $ce->get_fleet_box_wrapped($player_name, $tick);
	echo " <INPUT name='fleet_box_data' id='fleet_box_data' type='hidden' value='{$fleet_box_string} '/>";

	echo " <INPUT name='send_build_box' id='send_build_box' type='Button' value='Builds for Tick #$tick' ";
	echo "    onmouseover='TagToTip(\"build_box\", DELAY, 1000, ABOVE, true)' onclick='sendBuildBox();return false;'/>";
	$build_box_string = $ce->get_build_box_wrapped($player_name, $tick);
	echo " <INPUT name='build_box_data' id='build_box_data' type='hidden' value='{$build_box_string} '/>";

}
?>

