<?php
	require_once('db_fns.php5'); 
	require_once('player_data.php5'); 
	require_once('view_fns.php5'); 
	require_once('conversations_model.php5'); 
	require_once('conversation_topic_data.php5'); 
	require_once('conversation_message_data.php5'); 
	require_once('alliance_model.php5'); 

class ConversationsView {
	function display_conversations($subview) {
		$am = new AllianceModel();
		$cm = new ConversationsModel();
		$player_name = $_SESSION["player_name"];
		$type = $_REQUEST["type"];
		$category = $_REQUEST["category"]; // Equivalent to a group
		$contact = $_REQUEST["contact"];
		$conversation = $_REQUEST["conversation"];
		$message = $_REQUEST["message"];
		$alliance = $am->get_alliance_of_player($player_name);

		if ($subview=='') $subview='list';
		if ($type == '') $type = 'general';
		
		if ($type == 'general') $group = 'general';
		else if ($type == 'alliance') $group = $alliance;
		else $group = 'individual'; // More options here later....  Maybe...

		if ($category == '') {
			$categories = $cm->get_conversation_categories_by_type_and_group($type, $group);
			$category = $categories[0];
		}
		
		$pd = new PlayerData();
		
		if ($type == 'general' && $pd->is_admin($player_name)) $this->display_create_new_category_form($type);
		if ($type == 'alliance' && $alliance != NULL && $am->is_senior($player_name, $alliance)) $this->display_create_new_category_form($type);
		if ($type == 'personal') $this->display_add_player_recipient_form();

		$this->display_conversation_type_bar($type);
		if ($type == 'general' || $type == 'alliance') $this->display_conversation_category_bar($type, $group, $category);
		else $this->display_personal_contacts_bar($contact);
		echo "<BR />";
		
		if ($subview == 'list') {
			if ($type == 'general' || $type == 'alliance') {
				$this->display_topic_list($type, $group, $category);
			} else {
				$this->display_personal_topic_list($contact);
			}
			
			if ($type == 'personal' && $contact != '') $this->display_create_new_topic_form($type, $contact);
			else if (($type == 'general' || $type == 'alliance') && $category != '') $this->display_create_new_topic_form($type, $category);
		}	else if ($subview == 'individual') {
			$topic_id = $_REQUEST["topic_id"];
			
			$this->display_individual_topic();
			$this->display_reply_topic_form($type, $category, $topic_id);
		}
	}


	function display_conversation_top_bar() {
		$player_name = $_SESSION["player_name"];
		$vf = new ViewFunctions();
		$cm = new ConversationsModel();
		$am = new AllianceModel();
		$alliance = $am->get_alliance_of_player($player_name);
		
		$count_new_general_topics = $cm->count_new_general_topics($player_name);
		$count_new_alliance_topics = $cm->count_new_alliance_topics($player_name, $alliance);
		$count_new_personal_topics =  $cm->count_new_personal_topics($player_name);
		
		echo "<TABLE class='STD'><TR>\n";
		echo "    <TH class='STD' style='width:113px;'> Conversations: </TH>";
		$vf->display_button("General Conversations ($count_new_general_topics)", "404060", "707070", "main_page.php5?view=conversations&subview=list&type=general");
		$vf->display_button("Alliance Conversations  ($count_new_alliance_topics)", "402060", "705070", "main_page.php5?view=conversations&subview=list&type=alliance");
		$vf->display_button("Personal Conversations  ($count_new_personal_topics)", "406060", "709070", "main_page.php5?view=conversations&subview=list&type=personal");
		echo "</TR></TABLE>\n";
	}

	function display_conversation_type_bar($type) {
		$player_name = $_SESSION["player_name"];
		$vf = new ViewFunctions();
		$cm = new ConversationsModel();
		$am = new AllianceModel();
		$alliance = $am->get_alliance_of_player($player_name);
		
		$count_new_general_topics = $cm->count_new_general_topics($player_name);
		$count_new_alliance_topics = $cm->count_new_alliance_topics($player_name, $alliance);
		$count_new_personal_topics =  $cm->count_new_personal_topics($player_name);
		
		echo "<TABLE class='STD'><TR>\n";
		echo "    <TH class='STD' style='width:113px;'> Type: </TH>";
		if ($type == 'general') $vf->display_button("General ($count_new_general_topics)", "C04040", "F06060", "main_page.php5?view=conversations&subview=list&type=general");
		else $vf->display_button("General ($count_new_general_topics)", "404060", "707070", "main_page.php5?view=conversations&subview=list&type=general");
		
		if ($type == 'alliance') $vf->display_button("Alliance ($count_new_alliance_topics)", "C04040", "F06060", "main_page.php5?view=conversations&subview=list&type=alliance");
		else $vf->display_button("Alliance ($count_new_alliance_topics)", "402060", "705070", "main_page.php5?view=conversations&subview=list&type=alliance");
		
		if ($type == 'personal') $vf->display_button("Personal ($count_new_personal_topics)", "C04040", "F06060", "main_page.php5?view=conversations&subview=list&type=personal");
		else $vf->display_button("Personal ($count_new_personal_topics)", "406060", "709070", "main_page.php5?view=conversations&subview=list&type=personal");
		echo "</TR></TABLE>\n";
	}

	function display_conversation_category_bar($type, $group, $category) {	
		$player_name = $_SESSION["player_name"];
		$vf = new ViewFunctions();
		$cm = new ConversationsModel();
		
		echo "<TABLE class='STD'><TR>\n";
		echo "    <TH class='STD' style='width:113px;'> Category: </TH>";
		
		$categories = $cm->get_conversation_categories_by_type_and_group($type, $group);
		if (count($categories) == 0) {
			echo "    <TH class='STD' style='width:740px;'> <I> No categories available </I> </TH>";
		} else {
			if ($category == '') $category = $categories[0];
			
			$count_categories = count($categories);
			for ($i=0; $i < $count_categories; $i++) {
				$count_new_topics = $cm->count_new_conversation_topics_by_type_group_and_category($player_name, $type, $group, $categories[$i]);
				if ($category == $categories[$i]) $vf->display_button("$categories[$i]($count_new_topics)", "C04040", "F06060", "main_page.php5?view=conversations&subview=list&type=$type&category=$categories[$i]");
				else $vf->display_button("$categories[$i]($count_new_topics)", "404040", "606060", "main_page.php5?view=conversations&subview=list&type=$type&category=$categories[$i]");
				if ( $i % 5 == 4) echo "</TR><TR><TH class='STD'>&nbsp;</TH>";
			}
		}
		
		echo "</TR></TABLE>\n";

	}

	function display_personal_contacts_bar($contact) {	
		$player_name = $_SESSION["player_name"];
		$vf = new ViewFunctions();
		$cm = new ConversationsModel();
		
		echo "<TABLE class='STD'><TR>\n";
		echo "    <TH class='STD' style='width:113px;'> Contacts: </TH>";
		
		$contacts = $cm->get_personal_conversation_contacts($player_name);
		if (count($contacts) == 0) {
			echo "    <TH class='STD' style='width:740px;'> <I> No contacts available </I> </TH>";
		} else {
			
			$count_contacts = count($contacts);
			for ($i=0; $i < $count_contacts; $i++) {
				$count_new_topics = $cm->count_new_conversation_topics_by_personal_contact($player_name, $contacts[$i]);
				if ($contact == $contacts[$i]) $vf->display_button("$contacts[$i]($count_new_topics)", "C04040", "F06060", "main_page.php5?view=conversations&subview=list&type=personal&contact=$contacts[$i]&category=$contacts[$i]");
				else $vf->display_button("$contacts[$i]($count_new_topics)", "404040", "606060", "main_page.php5?view=conversations&subview=list&type=personal&contact=$contacts[$i]&category=$contacts[$i]");
				if ( $i % 5 == 4) echo "</TR><TR><TH class='STD'>&nbsp;</TH>";
			}
		}
		
		echo "</TR></TABLE>\n";

	}

	function display_topic_list($type, $group, $category) {	
		$player_name = $_SESSION["player_name"];
 		$vf = new ViewFunctions();
		$cm = new ConversationsModel();

		$color = "206020"; $hcolor = "60A060";
		
		echo "<TABLE class='STD'><TR>\n";
		echo "    <TR><TH class='STD' style='width:80%'> Subject </TH><TH class='STD'> Last Updated</TH></TR>";
		
		$topics = $cm->get_conversation_topics_by_type_group_and_category($type, $group, $category);
		$count_topics = count($topics);
		if ($count_topics == 0) {
			echo "    <TD class='STD' colspan='2'> <I> No topics </I> </TD>";
		} else {
			for ($i=0; $i < $count_topics; $i++) {
				$ctd = $topics[$i];
				if ( $cm->is_new_messages_for_topic($player_name, $ctd->id) ) { $color = "602020"; $hcolor = "A06060"; }
				else { 		$color = "206020"; $hcolor = "60A060"; }
				
				$last_message = $cm->get_last_message_by_topic_id($ctd->id);
				echo "<TR>";
				$vf->display_left_button("<B>$ctd->subject</B><br /><I>Started by: </I>$ctd->creater", "$color", "$hcolor", "main_page.php5?view=conversations&subview=individual&type=$type&category=$category&topic_id=$ctd->id");
				$vf->display_right_button("$last_message->post_time<br />$last_message->author", "$color", "$hcolor", "main_page.php5?view=conversations&subview=individual&type=$type&category=$category&topic_id=$ctd->id&message_id=$last_message->id");
				echo "</TR>";
			}
		}
					
		echo "</TABLE>\n";

	}

	function display_personal_topic_list($contact_name) {	
		$player_name = $_SESSION["player_name"];
		$vf = new ViewFunctions();
		$cm = new ConversationsModel();
		
		
		
		echo "<TABLE class='STD'><TR>\n";
		echo "    <TR><TH class='STD' style='width:80%'> Subject </TH><TH class='STD'> Last Updated</TH></TR>";
		
		$topics = $cm->get_personal_topics($player_name, $contact_name);
		$count_topics = count($topics);
		if ($count_topics == 0) {
			echo "    <TD class='STD' colspan='2'> <I> No topics </I> </TD>";
		} else {
			foreach($topics as $ctd) {

				if ( $cm->is_new_messages_for_topic($player_name, $ctd->id) ) { $color = "602020"; $hcolor = "A06060"; }
				else { 		$color = "206020"; $hcolor = "60A060"; }

				$last_message = $cm->get_last_message_by_topic_id($ctd->id);
				echo "<TR>";
				$vf->display_left_button("$ctd->last_message_id: <B>$ctd->subject</B><br /><I>Started by: </I>$ctd->creater", "$color", "$hcolor", "main_page.php5?view=conversations&subview=individual&type=personal&category=$contact_name&contact=$contact_name&topic_id=$ctd->id");
				$vf->display_right_button("$last_message->post_time<br />$last_message->author", "$color", "$hcolor", "main_page.php5?view=conversations&subview=individual&type=personal&category=$contact_name&contact=$contact_name&topic_id=$ctd->id&message_id=$last_message->id");
				echo "</TR>";
			}
		}
					
		echo "</TABLE>\n";

	}
	
	function display_individual_topic() {
		$player_name = $_SESSION["player_name"];
		$cm = new ConversationsModel();
		
		$topic_id = $_REQUEST["topic_id"];
		$message_id = $_REQUEST["message_id"];
		
		$ctd = $cm->get_topic_by_id($topic_id);

		if ($ctd->type == 'personal') {
			if ($ctd->creater != $player_name &&  $ctd->category != $player_name) {
				show_error("You tried to access a page that you were not supposed to access.  
					This infraction has been logged, please explain to an admin");
				$nm = new NewsModel();
				$nm->add_new_news('judal', 'player', 'misc', "$player_name tried to access a personal page they were not supposed to be able to see", "Conversation #$topic_id: $ctd->creater $ctd->category" );
				$nm->add_new_news($player_name, 'player', 'misc', "You tried to access a conversation you were not suppose to have access", "please explain to an admin" );
				return;
			}
		} 
		if ($ctd->type == 'alliance') {
			$am = new AllianceModel();
			if ($am->get_alliance_of_player($player_name) != $ctd->group) {
				show_error("You tried to access a page that you were not supposed to access.  
					This infraction has been logged, please explain to an admin");
				$nm = new NewsModel();
				$nm->add_new_news('judal', 'player', 'misc', "$player_name tried to access an alliance page they were not supposed to be able to see", "Conversation #$topic_id" );
				$nm->add_new_news($player_name, 'player', 'misc', "You tried to access a conversation you were not suppose to have access", "please explain to an admin" );
				return;
			}
		}
		$messages = $cm->get_all_messages_by_topic_id($topic_id);
		$mtd = $messages[0];
		
		echo "<TABLE class='STD'><TR><TD class='STD'> \n";
		echo "<TABLE class='CONVO'><TR>\n";
		echo "    <TR><TH class='CONVO' style='width:80%'>  </TH><TH class='CONVO'> </TH></TR>";
		echo "    <TR><TH class='CONVO' style='text-align:left' > $ctd->creater - $ctd->subject </TH> <TH class='CONVO'> $mtd->post_time  </TH></TR>";
		// First message in a convo is treated differently
		echo "    <TR><TH class='CONVO' style='text-align:left' > $ctd->type </TH> <TH class='CONVO'> &nbsp; </TH></TR>";
		echo "    <TR><TD class='CONVO' style='text-align:left' colspan='2'> $mtd->message_text </TD></TR>";
		
		$count_messages = count($messages);
		for ($i = 1; $i < $count_messages; $i++) {
			$mtd = $messages[$i];
			echo "    <TR><TH class='CONVO' style='text-align:left' > $mtd->author - $mtd->subject </TH> <TH class='CONVO'> $mtd->post_time </TH></TR>";
			echo "    <TR><TD class='CONVO' style='text-align:left' colspan='2'> $mtd->message_text </TD></TR>";
			
		}
		echo "</TABLE>\n";
		
		echo "</TD></TR></TABLE>";
		$cm->set_conversation_last_seen($player_name, $topic_id);	
	}
	
	function display_add_player_recipient_form() {
		$player_name = $_SESSION["player_name"];
		$pd = new PlayerData();
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_new_conversation_category'/>\n";
		echo "     <INPUT type='hidden' name='view' value='conversations'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='list'/>\n";
		echo "     <INPUT type='hidden' name='type' value='personal'/>\n";
		echo "  <TABLE class='STD'><TR>\n";
		echo "<TD class='STD'>personal</TD>\n";
		
		echo "<TD class='STD'><SELECT name='category'>";
		$player_list = $pd->get_all_player_names();
		$count_players = count($player_list);
		for ($i=0; $i<$count_players; $i++) {
			if ($player_list[$i] != $player_name) 
				echo "<OPTION name='$player_list[$i]'>$player_list[$i]</OPTION>\n";
		}
		echo"</SELECT></TD>\n";
		
		echo "<TD class='STD'><INPUT type='submit' value='Create new player contact' /></TD>\n";
		echo "</TR></TABLE>\n";
		echo "</FORM>\n";
		
	}
	
	function display_create_new_category_form($type) {
		echo "<FORM method='post' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_new_conversation_category'/>\n";
		echo "     <INPUT type='hidden' name='view' value='conversations'/>\n";
		echo "     <INPUT type='hidden' name='subview' value='list'/>\n";
		echo "     <INPUT type='hidden' name='type' value='$type'/>\n";
		echo "  <TABLE class='STD'><TR>\n";
		echo "<TD class='STD'>$type</TD>\n";
		echo "<TD class='STD'><INPUT type='category' name='category' size='50'/></TD>\n";
		echo "<TD class='STD'><INPUT type='submit' value='Create new $type category' /></TD>\n";
		echo "</TR></TABLE>\n";
		echo "</FORM>\n";
	}

	function display_create_new_topic_form($type, $category) {
			echo "<FORM method='get' action='main_page.php5'>";
			echo "     <INPUT type='hidden' name='action' value='create_new_conversation_category'/>\n";
			echo "     <INPUT type='hidden' name='view' value='conversations'/>\n";
			echo "     <INPUT type='hidden' name='subview' value='list'/>\n";
			echo "     <INPUT type='hidden' name='type' value='$type'/>\n";
			echo "     <INPUT type='hidden' name='category' value='$category'/>\n";
			echo "<BR /><INPUT type='submit' value='Create new topic' id='concept_button' onClick='conversation_window(\"\", \"\", \"\", \"$type\", \"$category\");return false;'/>";
			echo "</FORM>";
	}

	function display_reply_topic_form($type, $category, $topic_id) {
			echo "<FORM method='get' action='main_page.php5'>";
			echo "     <INPUT type='hidden' name='action' value='create_new_conversation_category'/>\n";
			echo "     <INPUT type='hidden' name='view' value='conversations'/>\n";
			echo "     <INPUT type='hidden' name='subview' value='list'/>\n";
			echo "     <INPUT type='hidden' name='type' value='$type'/>\n";
			echo "     <INPUT type='hidden' name='category' value='$category'/>\n";
			echo "<BR /><INPUT type='submit' value='Reply to topic' id='concept_button' onClick='conversation_window(\"$topic_id\", \"\", \"\", \"$type\", \"$category\");return false;'/>";
			echo "</FORM>";
	}

	// ** old
	
	function show_list_of_conversations($recipient) {
 		$vf = new ViewFunctions();
		$conn = db_connect();	
	  $query = "select * from conversation c, conversation_member cm where 
	  	c.id=cm.conversation_id and
	  	cm.player_name='$recipient'";
	  $result = $conn->query($query);
		echo "<TABLE class='STD' >\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='4'>Messages</TH>\n";
		echo " </TR>\n";
		if ($result->num_rows == 0) {
			echo "  <TD class='STD' colspan='4'> No Messages </TD>\n";
		} else {
			echo " <TR>\n";
			echo "   <TH class='STD'>Conversation Starter</TH>\n";
			echo "   <TH class='STD'>Subject</TH>\n";
			echo "   <TH class='STD'>Last Updated</TH>\n";
			echo "   <TH class='STD'>Last Read</TH>\n";
			echo " </TR>\n";
			for ($count=0; $row = $result->fetch_object(); $count++) {
				if ($row->last_updated > $row->last_read) $clr = "A00000";
				else $clr = "#409040";
				
				echo " <TR>\n";
				echo "  <TD class='STD' style='background-color:$clr'> $row->starter </TD>\n";
				echo "  <TD class='STD' style='background-color:$clr'> $row->subject </TD>\n";
				echo "  <TD class='STD' style='background-color:$clr'> $row->last_updated</TD>\n";
				echo "  <TD class='STD' style='background-color:$clr'> $row->last_read</TD>\n";
				$vf->display_button('View', 'B02000', 'E06040', 
					"main_page.php5?view=conversations&subview=single_conversation&id=$row->id");
				echo " </TR>\n";
			}
		}
		echo "</TABLE>\n";
		
	}

	function list_group_conversations() {
		echo "Group Conversations";
	}

	function list_allaince_conversations() {
		echo "Alliance Conversations";
	}

	function list_broadcast_conversations() {
		$this->show_list_of_conversations('broadcast');
	}

	function display_single_conversation() {
  	$player_name=$_SESSION['player_name'];
		$id=$_REQUEST['id'];
		if (!$this->verify_player_should_see_conversation()) {
			echo "You do not have permission to see this message, please contact the author and have him add you";
			return;
		}
		
		$cm = new ConversationsModel();
		$cm->touch_conversation_member($id, $player_name);
		
 		$conn = db_connect();	
		
	  $query = "select * from conversation where id=$id";
	  $result = $conn->query($query);
		if ($result->num_rows == 0) {
			echo "<TABLE class='CONVO' >\n";
			echo "  <TD class='CONVO' colspan='4'> No message for that ID </TD>\n";
			echo "</TABLE>\n";
		} else {
			$row = $result->fetch_object();
			echo "<TABLE class='CONVO' >\n";
			echo " <TR>\n";
			echo "   <TH class='CONVO' colspan='3'>Message: # $id</TH>\n";
			echo " </TR>\n";
			echo " <TR>\n";
			echo "   <TH class='CONVO' width='160px'>Conversation Members </TH>\n";
			echo "   <TH class='CONVO' width='320px'>";
			$this->display_conversation_members_list();
			echo " </TH>\n";
			echo "   <TH class='CONVO' > ";
	
			echo "<FORM method='get' action='main_page.php5'>\n";
			echo "     <INPUT type='hidden' name='action' value='add_member_to_conversation'/>\n";
			echo "     <INPUT type='hidden' name='view' value='conversations'/>\n";
			echo "     <INPUT type='hidden' name='subview' value='single_conversation'/>\n";
			echo "     <INPUT type='hidden' name='id' value='$id'/>\n";
			echo "	<INPUT type='submit' value='Invite' />\n";
			echo "	<INPUT type='text' name='new_conversation_member_name' size='20' />\n";
			echo "</FORM>\n";
		
			echo " </TH>\n";
			echo " </TR>\n";
			echo " <TR>\n";
			echo "   <TH class='CONVO' width='160px'>Subject</TH>\n";
			echo "   <TH class='CONVO' colspan='2'>$row->subject</TH>\n";
			echo " </TR>\n";
			echo "</TABLE>\n";
			$this->display_messages();
		}
		echo "<BR />\n";
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='reply'/>\n";
		echo "     <INPUT type='hidden' name='view' value='conversations'/>\n";
		echo "     <INPUT type='hidden' name='id' value='$id'/>\n";
		echo "<TABLE class='CONVO' >\n";
		echo " <TR>\n";
		echo "   <TH class='CONVO' colspan='4'> Reply </TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='CONVO'> Subject </TD>\n";
		echo "  <TD class='CONVO'> <INPUT type='text' size='100' name='subject' /> </TD>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='CONVO' colspan='2'> <TEXTAREA rows='20' cols='82' name='text'></TEXTAREA> </TD>\n";
		echo " </TR>\n";
		echo "</TABLE>\n";

		echo "	<INPUT type='submit' value='Reply to Conversation' />\n";
		echo "</FORM>\n";

		
		
	}
	
	function display_messages() {
 		$id=$_REQUEST['id'];
		$conn = db_connect();	
	  $query = "select * from conversation c, conversation_message cm where 
	  	c.id = $id and 
	  	c.id = cm.conversation_id order by cm.time asc";
	  $result = $conn->query($query);
		echo "<TABLE class='CONVO' >\n";
		for ($count=0; $row = $result->fetch_object(); $count++) {
			echo " <TR>\n";
			if (strlen($row->subject) == 0) echo "  <TH class='CONVO'> $row->author </TH>\n";
			else echo "  <TH class='CONVO'> $row->author - $row->subject</TH>\n";
			echo "  <TH class='CONVO' style='text-align:right'> $row->time </TH>\n";
			echo " </TR>\n";
			echo " <TR>\n";
			echo "   <TD class='CONVO' colspan='2'>$row->text </TD>\n";
			echo " </TR>\n";
		}
		echo "</TABLE>\n";
	}
	
	function verify_player_should_see_conversation() {
  	$player_name=$_SESSION['player_name'];
		$id=$_REQUEST['id'];
		$conn = db_connect();	
		$query = "Select player_name from conversation_member where conversation_id=$id";
	  $result = $conn->query($query);
		$row = $result->fetch_object();
		if (strcmp($row->player_name, 'broadcast') == 0) return true; // Everyone can see broadcast messages
		
	  $query = "select * from conversation c, conversation_member cm where 
	  	c.id = $id and cm.player_name='$player_name' and 
	  	c.id = cm.conversation_id ";
	  $result = $conn->query($query);
		if ($result->num_rows > 0) return true;
		else return false;
	}

	function display_conversation_members_list() {
  	$player_name=$_SESSION['player_name'];
		$id=$_REQUEST['id'];

		$conn = db_connect();	
	  $query = "select distinct player_name from conversation_member where conversation_id=$id";
	  $result = $conn->query($query);

		for ($count=0; $row = $result->fetch_object(); $count++) {
			echo "{$row->player_name}; ";
		}
		return $member_list;
	}


	
	function create_conversation_view() {
  	$player_name=$_SESSION['player_name'];

		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='create_conversation'/>\n";
		echo "     <INPUT type='hidden' name='view' value='conversations'/>\n";

		echo "<TABLE class='STD' >\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='2' >Create Conversation</TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD'> Conversation Starter </TD>\n";
		echo "  <TD class='STD' style='text-align:left'> &nbsp; $player_name </TD>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD'> Subject </TD>\n";
		echo "  <TD class='STD'> <INPUT type='text' size='84' name='subject' /> </TD>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD'> To <br /> <DIV style='font-size:10'>names seperated by semi-colon </DIV> </TD>\n";
		echo "  <TD class='STD'> <INPUT type='text' size='84' name='member_list' /> </TD>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD' colspan='2'> <TEXTAREA rows='25' cols='82' name='text'></TEXTAREA> </TD>\n";
		echo " </TR>\n";
		
		echo "</TABLE>\n";
		echo "	<INPUT type='submit' value='Create Conversation' />\n";
		echo "</FORM>\n";
	}
	

}
?>