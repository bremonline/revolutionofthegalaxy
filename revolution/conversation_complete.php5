<?php
	require_once('db_fns.php5'); 
	require_once('alliance_model.php5'); 
	
	session_start();

	$am = new AllianceModel();
	$player_name = $_SESSION['player_name'];
	
	$parent_id=$_REQUEST['parent_id'];
	$message_id=$_REQUEST['message_id'];
	$topic_id=$_REQUEST['topic_id'];
	$type=$_REQUEST['type'];
	$category=$_REQUEST['category'];
	$subject=$_REQUEST['subject'];
	$text=$_REQUEST['text'];

	
	if ($type == 'general') $group = 'general';
	else if ($type == 'alliance') $group = $am->get_alliance_of_player($player_name);
	else $group = 'individual';

	do_header();

	// Check if new topic.  If so, this field will be blank
	if ($topic_id == '') {
		if ($subject == '') $subject = substr(strip_tags($text), 0, 100);
		$topic_id = create_new_topic($type, $group, $category, $player_name, $subject);	
	}
	
	if ($message_id == '') {
		$message_id = create_new_message($topic_id, $parent_id, $player_name, $subject, $text);
	}

	
	echo "<body onload='javascript:close_edit_window()'>";
//	echo "<body>";
//  echo "Message Updated";

	echo "</body>";
	echo "</html>";
	


function do_header() {
	echo "
<html>
<head>
  <title>Revolution - Conversation</title>
  <link rel='Stylesheet' href='revolution.css' title='Style' type='text/css'/> 
  <meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'> 
<script src='revolution.js' type='text/javascript'></script>
</head>
";
}

function create_new_topic($type, $group, $category, $player_name, $subject) {
  $conn = db_connect();
	$query = "insert into conversation_topic values (0, '$type', '$group', '$category', '$player_name', '$subject') "; 
	$result = $conn->query($query);
	return $conn->insert_id;
	
}

function create_new_message($topic_id, $parent_id, $player_name, $subject, $text) {
	if ($parent_id == '') $parent_id = 0;
  $conn = db_connect();
	$query = "insert into conversation_message values (0, '$topic_id', '$parent_id', '$player_name', NOW(), '$subject', '$text')"; 
	$result = $conn->query($query);
	
	return $conn->insert_id;
	
}

?>