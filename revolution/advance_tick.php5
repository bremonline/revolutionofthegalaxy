<?php
	require_once("game_model.php5");
	
$gm = new GameModel();

$check_time = $gm->check_tick_for_advancement();
if ($check_time < 500) {
	echo "<BR /><BR />Too Soon to update.  Above number has to be greater then 500 (5 minutes)";
} else {
//	$gm->advance_single_tick();
//	echo "<BR /><BR />Tick is updated";
	echo "<BR /><BR />System deactivated";
}
?>