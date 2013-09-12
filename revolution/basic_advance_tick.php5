<?php
	require_once("game_model.php5");
	
$gm = new GameModel();
$gm->advance_tick_automated();
//echo "Ticker Stopped";

?>