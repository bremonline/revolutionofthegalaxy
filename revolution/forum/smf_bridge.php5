<?php
// Let's get this user into SMF...

function smf_register_new_member($username, $password, $password2, $email) {

global $db_name, $db_prefix, $sourcedir, $boarddir, $db_connection;

$db_name = "revo_smf";
$db_prefix = "smf_";
$sourcedir = "[source_dir]";
$boarddir = "[board_dir]";

$db_connection = mysql_connect('[host]', 'revo_smf', '[db_password]');

define ("SMF", true);
	
	mysql_select_db($db_name, $db_connection);  //switch to SMF’s database, in case these softwares are in different databases.

	$possible_strings = array(
		'websiteUrl', 'websiteTitle',
		'AIM', 'YIM',
		'location', 'birthdate',
		'timeFormat',
		'buddy_list',
		'pm_ignore_list',
		'smileySet',
		'signature', 'personalText', 'avatar',
		'lngfile',
		'secretQuestion', 'secretAnswer',
	);
	
	$possible_ints = array(
		'pm_email_notify',
		'notifyTypes',
		'ICQ',
		'gender',
		'ID_THEME',
	);
	
	$possible_floats = array(
		'timeOffset',
	);

	$possible_bools = array(
		'notifyAnnouncements', 'notifyOnce', 'notifySendBody',
		'hideEmail', 'showOnline',
	);

	// Set the options needed for registration.
	$regOptions = array(
		'interface' => 'guest',
		'username' => $username,  //the variable from the form for the inputted username
		'email' => $email,  //likewise, the email address that was inputted in the form
		'password' => $password,  //the password that the user inputted
		'password_check' => $password2,  //the second confirmation password inputted
		'check_reserved_name' => true,  //this will make sure that SMF first checks for a reserved name before writing the user to the database
		'check_password_strength' => true,
		'check_email_ban' => true,  //checks for ban on the email address that was inputted
		'send_welcome_email' => false,  //true if you want SMF to send an email, false if you want your other software to handle it.  I’d recommend false.
		'require' => 'nothing',
		'extra_register_vars' => array(),
		'theme_vars' => array(),
	);

	// Include the additional options that might have been filled in.
	foreach ($possible_strings as $var)
		if (isset($_POST[$var]))
			$regOptions['extra_register_vars'][$var] = '\'' . $_POST[$var] . '\'';
	foreach ($possible_ints as $var)
		if (isset($_POST[$var]))
			$regOptions['extra_register_vars'][$var] = (int) $_POST[$var];
	foreach ($possible_floats as $var)
		if (isset($_POST[$var]))
			$regOptions['extra_register_vars'][$var] = (float) $_POST[$var];
	foreach ($possible_bools as $var)
		if (isset($_POST[$var]))
			$regOptions['extra_register_vars'][$var] = empty($_POST[$var]) ? 0 : 1;

	// Registration options are always default options...
	if (isset($_POST['default_options']))
		$_POST['options'] = isset($_POST['options']) ? $_POST['options'] + $_POST['default_options'] : $_POST['default_options'];
			$regOptions['theme_vars'] = isset($_POST['options']) && is_array($_POST['options']) ? $_POST['options'] : array();

	require_once($sourcedir . '/Subs-Members.php');  //require the file, so that we can call the function
//	require_once($sourcedir . '/Load.php');  //require the file, so that we can call the function
//	require_once($sourcedir . '/Errors.php');  //require the file, so that we can call the function
//	require_once($sourcedir . '/Subs.php');  //require the file, so that we can call the function
	$memberID = registerMember($regOptions);  //call the function.  $memberID should return a value.


}

function smf_create_board($board_name, $group_id) {
	
	$db_connection = mysql_connect('[host]', 'revo_smf', '[db_password]');	
	mysql_select_db("revo_smf", $db_connection);  //switch to SMF’s database, in case these softwares are in different databases.

	$boardOptions = array(
		'board_name' => $board_name,
		'target_category' => 3,  // 3 is the alliance category
		'move_to' => 'bottom',  // creating at bottom
		'access_groups' => array(2, $group_id), // Access Group #2 is admin		
	);
	
	db_query("
		INSERT INTO smf_boards
			(ID_CAT, name, description, boardOrder, memberGroups)
		VALUES ($boardOptions[target_category], SUBSTRING('$boardOptions[board_name]', 1, 255), '', 0, '$group_id')", __FILE__, __LINE__);
	$board_id = db_insert_id();
	
	return $board_id;
}

function smf_create_membergroup($group_name) {
	
	$db_connection = mysql_connect('[host]', 'revo_smf', '[db_password]');
	mysql_select_db("revo_smf", $db_connection);  //switch to SMF’s database, in case these softwares are in different databases.
	
	
	mysql_select_db($db_name, $db_connection);  //switch to SMF’s database, in case these softwares are in different databases.

		$request = db_query("
			SELECT MAX(ID_GROUP)
			FROM smf_membergroups"
			, __FILE__, __LINE__);
		list ($ID_GROUP) = mysql_fetch_row($request);
		mysql_free_result($request);
		$ID_GROUP++;

		db_query("
			INSERT INTO smf_membergroups
				(ID_GROUP, groupName, minPosts, stars, onlineColor)
			VALUES ($ID_GROUP, SUBSTRING('$group_name', 1, 80), -1, '1#star.gif', '')", __FILE__, __LINE__);

		return $ID_GROUP;
}

function smf_add_member_to_group($member_id, $group_id) {

	$db_connection = mysql_connect('[host]', 'revo_smf', '[db_password]');
	mysql_select_db("revo_smf", $db_connection);  //switch to SMF’s database, in case these softwares are in different databases.
	
	db_query("
		UPDATE smf_members
		SET additionalGroups = IF(additionalGroups = '', '$group_id', CONCAT(additionalGroups, ',$group_id'))
		WHERE ID_MEMBER = $member_id
			AND ID_GROUP != $group_id
			AND NOT FIND_IN_SET($group_id, additionalGroups)"
		, __FILE__, __LINE__);
}

function smf_delete_member_from_group($member_id, $group_id) {

	$db_connection = mysql_connect('[host]', 'revo_smf', '[db_password]');
	mysql_select_db("revo_smf", $db_connection);  //switch to SMF’s database, in case these softwares are in different databases.
	
	$groups = array((int) $group_id);
	db_query("
			UPDATE smf_members
			SET additionalGroups = '" . implode(',', array_diff(explode(',', $additionalGroups), $groups)) . "'
			WHERE ID_MEMBER = $member_id"
			, __FILE__, __LINE__);
}

function smf_get_id_of_player($player_name) {

	$db_connection = mysql_connect('[host]', 'revo_smf', '[db_password]');
	mysql_select_db("revo_smf", $db_connection);  //switch to SMF’s database, in case these softwares are in different databases.
	
	$request = db_query("
		SELECT ID_MEMBER FROM smf_members WHERE memberName = '$player_name'"
		, __FILE__, __LINE__);
	
	list ($member_id) = mysql_fetch_row($request);
	mysql_free_result($request);
	return $member_id;
}

function smf_get_id_of_group($group_name) {

	$db_connection = mysql_connect('[host]', 'revo_smf', '[db_password]');
	mysql_select_db("revo_smf", $db_connection);  //switch to SMF’s database, in case these softwares are in different databases.
	
	$request = db_query("
		SELECT ID_GROUP FROM smf_membergroups WHERE groupName = '$group_name'"
		, __FILE__, __LINE__);
	
	list ($group_id) = mysql_fetch_row($request);
	mysql_free_result($request);
	return $group_id;
		
}

// Big Function: finds the player_id, creates the membergroup, assigns the player to the group, creates the board, assigns the permission to that board
function smf_create_alliance_board($player_name, $board_name) {
	$member_id = smf_get_id_of_player($player_name);
	$group_id = smf_create_membergroup($board_name);  // Group is same name as board
	smf_add_member_to_group($member_id, $group_id);
	$board_id = smf_create_board($board_name, $group_id);
	return $board_id;
}
 
function smf_add_player_to_group($player_name, $group_name) {
	$member_id = smf_get_id_of_player($player_name);
	$group_id = smf_get_id_of_group($group_name);
	smf_add_member_to_group($member_id, $group_id);
}

function smf_delete_player_from_group($player_name, $group_name) {
	$member_id = smf_get_id_of_player($player_name);
	$group_id = smf_get_id_of_group($group_name);
	smf_delete_member_from_group($member_id, $group_id);
}


?>
