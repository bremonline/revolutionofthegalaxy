<?php
	require_once ("/home/content/b/r/e/bremonline/html/revo_smf/SSI.php");
	require("forum/smf_bridge.php5");
	require("player_data.php5");

  $conn = db_connect();

// get a list of all the players.
$pd = new PlayerData();
$player_list = $pd->get_all_player_names();

foreach ($player_list as $player_name) {
	$player_id = smf_get_id_of_player($player_name);
	if (!($player_id > 0)) echo "$player_name: <BR/>\n";

}


?>