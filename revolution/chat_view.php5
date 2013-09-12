<?php
	require_once('view_fns.php5'); 
	require_once('chat_model.php5'); 
	require_once('alliance_model.php5'); 

class ChatView {
	function display_chat_view($subview) {
		$player_name = $_SESSION["player_name"];
		if ($subview=='') $subview='main';
		echo "<BR />";
		
		$type = $subview;
		$channel = $_REQUEST["channel"];
		if ($channel == "") $channel='main';

		// Alliances are a special type of group.  If the chat_type is alliance set the group to the name of that player's alliance
		if ($type == 'main' && $group == '') $group = 'main';
		$am = new AllianceModel();
		if ($type == 'alliance') $group = $am->get_alliance_of_player($player_name);
		if ($type == 'alliance' && $group == '') $group = 'no alliance';
		if ($type == 'personal') $group = "$player_name";
		
		$this->display_shout_form($subview);
		echo "<BR />";

		echo "<INPUT type='button' name='chat_open' value='Open Chat in new Window' onclick='cw = window.open(\"chat/chat_page.php5\", \"chat_window\", \" width=940px,height=720px,resizable=1,scrollbars=1 \"); cw.focus();' />";
		echo "<BR /><A href='chat/chat_page.php5' target='revo_chat' />Open Chat in a new tab </A>";

//		$this->display_chat_type_bar($type);
//		$this->display_chat_group_bar($type, $group);
//		$this->display_chat_channel_bar($type, $group, $channel);
//		$this->display_chat_panel($type, $group, $channel);
//		echo "<BR />";
//		$this->display_create_new_channel($subview);
//		echo "<DIV id='dbg'></DIV>";
	}
	
	function display_chat_type_bar($type) {
		
		$vf = new ViewFunctions();
		echo "<TABLE class='STD'><TR>\n";
		echo "    <TH class='STD' style='width:113px;'> Type: </TH>";
		if ($type == 'main') $vf->display_button("Main", "C04040", "F06060", "main_page.php5?view=chat&subview=main");
		else $vf->display_button("Main", "404040", "707070", "main_page.php5?view=chat&subview=main");
		
		if ($type == 'alliance') $vf->display_button("Alliance", "C04040", "F06060", "main_page.php5?view=chat&subview=alliance");
		else $vf->display_button("Alliance", "402040", "705070", "main_page.php5?view=chat&subview=alliance");
		
		if ($type == 'personal') $vf->display_button("Personal", "C04040", "F06060", "main_page.php5?view=chat&subview=personal");
		else $vf->display_button("Personal", "406040", "709070", "main_page.php5?view=chat&subview=personal");
		echo "</TR></TABLE>\n";
	}

	function display_chat_group_bar($type, $group) {
		
		$vf = new ViewFunctions();
		echo "<TABLE class='STD'><TR>\n";
		echo "    <TH class='STD' style='width:113px;'> Group: </TH>";
		$vf->display_button("$group", "C04040", "F06060", "main_page.php5?view=chat&subview=$type");
		echo "</TR></TABLE>\n";
	}

	function display_chat_channel_bar($type, $group, $channel) {
		
		$player_name = $_SESSION["player_name"];
		$vf = new ViewFunctions();
		$am = new AllianceModel();
		$cm = new ChatModel();

		$normal_color = "404040"; $normal_highlight_color="606060";
		
		$channels = $cm->get_chat_channels($type, $group);
		
		echo "<TABLE class='STD'><TR>\n";
		echo "<TH class='STD' style='width:113px;'>Channel:</TH>";
		// Always have a main channel
		$unread_message_count = $cm->count_unread_messages_by_channel($player_name, $type, $group, "main");
		if ($channel == 'main') $vf->display_button("main(0)", 'C04040', 'F06060', "main_page.php5?view=chat&subview=$type&type=$type&group=$group&channel=main");
		else $vf->display_button("main($unread_message_count)", '404040', '606060', "main_page.php5?view=chat&subview=$type&type=$type&group=$group&channel=main");
		for ($i=0;$i<count($channels);$i++) {
			$unread_message_count = $cm->count_unread_messages_by_channel($player_name, $type, $group, $channels[$i]);
			
			if ($channel == $channels[$i]) $vf->display_button("{$channels[$i]}(0)", 'C04040', 'F06060', "main_page.php5?view=chat&subview=$type&type=$type&group=$group&channel=$channels[$i]");
			else $vf->display_button("{$channels[$i]}($unread_message_count)", '404040', '606060', "main_page.php5?view=chat&subview=$type&type=$type&group=$group&channel=$channels[$i]");
			if ( $i % 5 == 4) echo "</TR><TR><TH class='STD'>&nbsp;</TH>";
		}
		echo "</TR></TABLE>\n";
	}
	
	function display_create_new_channel($subview) {
		$player_name = $_SESSION["player_name"];
		$am = new AllianceModel();

		echo "<FORM method='post' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_new_chat'/>\n";
		echo "     <INPUT type='hidden' name='view' value='chat'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='$subview'/>\n";
		echo "     <INPUT type='hidden' name='type' value='$subview'/>\n";
		echo "  <TABLE class='STD'><TR>\n";
		if ($subview == 'main') $group = 'main';
		if ($subview == 'alliance') $group = $am->get_alliance_of_player($player_name);
		if ($subview == 'personal') $group = "$player_name";
		if ($group == '') $group = 'no alliance';
		echo "     <INPUT type='hidden' name='group' value='$group'/>\n";
		echo "<TD class='STD'><INPUT type='text' name='channel' size='50'/></TD>\n";
		echo "<TD class='STD'><INPUT type='submit' value='Create New Channel' /></TD>\n";
		echo "</TR></TABLE>\n";
		echo "</FORM>\n";
	}
	
	function display_chat_panel($type, $group, $channel) {
		$player_name = $_SESSION['player_name'];
		
		$cm = new ChatModel();
		$max = $cm->get_max_message_number();
		$cm->set_last_seen($player_name, $type, $group, $channel);
		
		echo "<TABLE class='STD'>\n";
		echo "<TR>\n";
		echo " <TH class='STD' id='title_panel' style='text-align:left'>";
		echo "  Chat Window for: $type:$group:$channel </TH>\n";
		echo " <TH class='STD'><div id='status'> 0 </div></TH>\n";
		echo "</TR>\n";
		echo "<TR><TD class='STD' colspan='2'>";
		echo "<DIV id='chat_panel' style='height:500px;width:830px;overflow:auto;text-align:left;'> </DIV> </TD></TR>\n";
		echo "<FORM method='POST' action='main_page.php?view=chat' onsubmit='return blockSubmit()'>";
		echo "<TR><TD class='STD'><INPUT TYPE='text' id='message' style='width: 730px;' /></TD>
			<TD class='STD'><INPUT TYPE='button' id='send_button' value='Send' onclick='javascript:sendChatText();' /></TD></TR>\n";
		echo "</TABLE>\n";
		echo "</FORM>";
	}
	
	function display_click_button($name, $color, $over_color, $script, $href) {
		echo "<TD class='SIDEBAR' colspan='$colspan' style='background-color:$color' onClick='$script;location.href=\"$href\"'
		onMouseOver='this.style.backgroundColor=\"$over_color\"' onMouseOut='this.style.backgroundColor=\"$color\"'>$name</TD>";		
	}
	

// *** Shout functions

	function display_shout_form($subview) {
		$player_name = $_SESSION["player_name"];
		$pd = new PlayerData();
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_new_personal_shout'/>\n";
		echo "     <INPUT type='hidden' name='view' value='chat'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='$subview'/>\n";
		echo "     <INPUT type='hidden' name='type' value='$subview'/>\n";
		echo "  <TABLE class='STD'><TR>\n";
		echo "<TH class='STD'>Shout</TH>\n";
		echo "</TR>\n";
		echo "<TD class='STD'><SELECT name='recipient'>";
		$player_list = $pd->get_all_player_names();
		$count_players = count($player_list);
		for ($i=0; $i<$count_players; $i++) {
			if ($player_list[$i] != $player_name) 
				echo "<OPTION name='$player_list[$i]'>$player_list[$i]</OPTION>\n";
		}
		echo"</SELECT></TD>\n";
		
		echo "<TD class='STD'><INPUT type='text' name='shout_text' size='100'/></TD>\n";
		echo "<TD class='STD'><INPUT type='submit' value='Shout' /></TD>\n";
		echo "</TR></TABLE>\n";
		echo "</FORM>\n";
	}
	
	function display_most_recent_shout($view, $subview) {
		$player_name = $_SESSION["player_name"];
		$cm = new ChatModel();
		$return_value = $cm->get_most_recent_shout_for_player($player_name);
		if ($return_value == false) $style='display:none;';
		list($sender, $shout_text) = $return_value;
		
		echo "<TABLE class='STD' id='shout_panel' style='$style'>\n";
		echo "<TR>\n";
		echo " <TH class='STD' style='width:113px;'>Shout:</TH>\n";
		echo " <TD class='STD' id='shout_sender' style='width:100px;' >&nbsp;$sender&nbsp;</TD>\n";
		echo " <TD class='STD' id='shout_text' style='background-color:E0A010; color:201000;'><B>&nbsp;$shout_text&nbsp;</B></TD>\n";
		$vf = new ViewFunctions();
		$vf->display_id_button("Clear", '101080', '3030A0', "shout_clear_button", "80px", "main_page.php5?action=clear_shout&view=overview"); 
		echo "</TR>\n";
		echo "</TABLE>\n";
	}
}
?>