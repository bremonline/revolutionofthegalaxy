<?php
	require_once('view_fns.php5'); 
	require_once('alliance_model.php5'); 
	require_once('conversations_model.php5'); 
	
	
class ConversationsController {
	function create_new_conversation_category() {
 		$am = new AllianceModel();
	 	$player_name=$_SESSION['player_name'];
  	$type = $_REQUEST['type'];
  	$category = $_REQUEST['category'];

		$category = strtr($category, "'", " "); // removes ' 
		if ($category == '') $category = 'default';
				  	
  	if ($type == 'general') $group = 'general';
  	else if ($type == 'alliance') $group = $am->get_alliance_of_player($player_name);
  	else $group = 'individual';
  	
  	$cm = new ConversationsModel();
  	if ($cm->check_conversation_category_with_creater($type, $group, $category, $player_name)) {
  		show_warning("There is already a category by that name");
  		return;
  	} else {
			$cm = new ConversationsModel();
			$cm->create_new_conversation_category($player_name, $type, $group, $category);  	
		}
	}

	function create_new_conversation_topic() {
 		$am = new AllianceModel();
  	$player_name=$_SESSION['player_name'];
  	$type = $_REQUEST['type'];
  	$category = $_REQUEST['category'];
  	$subject = $_REQUEST['subject'];
  	if ($type == 'general') $group = 'general';
  	else if ($type == 'alliance') $group = $am->get_alliance_of_player($player_name);
  	else $group = 'individual';
  	
		$cm = new ConversationsModel();
		$cm->create_new_conversation_topic($player_name, $type, $group, $category, $subject);  	
		
	}

	function create_new_conversation() {
  	$player_name=$_SESSION['player_name'];
  	
  	$this->create_conversation();
	}
	
	function create_conversation() {
  	$player_name=$_SESSION['player_name'];
  	$member_list = $_REQUEST['member_list'];
		$subject = $_REQUEST['subject'];
		$text = $_REQUEST['text'];

		$cm = new ConversationsModel();
		$id = $cm->db_insert_conversation($player_name, $subject);
		
		$members = split(";", $member_list);
		
		$cm->db_insert_conversation_members($id, $members);
		$cm->db_insert_individual_conversation_member($id, $player_name, 'author');
		$cm->db_insert_conversation_message($id, $player_name, $subject, $text);
	}

	function reply() {
  	$player_name=$_SESSION['player_name'];
  	$id = $_REQUEST['id'];
		$subject = $_REQUEST['subject'];
		$text = $_REQUEST['text'];

		if (strlen($text) == 0) {
			show_error("No content in the text field of the reply");
			return;
		}
		
		$cm = new ConversationsModel();
		$cm->db_insert_conversation_message($id, $player_name, $subject, $text);
		$cm->set_conversation_last_updated($id);
		
	}

	function add_member_to_conversation() {
  	$player_name=$_SESSION['player_name'];
  	$id = $_REQUEST['id'];
		$new_conversation_member_name	= $_REQUEST['new_conversation_member_name'];
		
		$cm = new ConversationsModel();
		$cm->db_insert_individual_conversation_member($id, $new_conversation_member_name, 'invitee');
	}
}
?>