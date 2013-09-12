<?php
	$conn = new mysqli('[host]', '[db_user]', '[db_password]', '[database]');
	
	$result = $conn->query("TRUNCATE TABLE chat");
	$result = $conn->query("TRUNCATE TABLE chat_last_online");
	$result = $conn->query("TRUNCATE TABLE chat_message");
	
	$result = $conn->query("TRUNCATE TABLE last_seen");
	$result = $conn->query("TRUNCATE TABLE milestone");
	$result = $conn->query("TRUNCATE TABLE monitor");
	$result = $conn->query("TRUNCATE TABLE news");
	$result = $conn->query("TRUNCATE TABLE player_build");
	$result = $conn->query("TRUNCATE TABLE player_creatures");
	$result = $conn->query("TRUNCATE TABLE player_items");
	$result = $conn->query("TRUNCATE TABLE player_orders");
	$result = $conn->query("TRUNCATE TABLE player_scans");
	$result = $conn->query("TRUNCATE TABLE pulse_use");
	$result = $conn->query("TRUNCATE TABLE scan_results");
	$result = $conn->query("TRUNCATE TABLE shout");
	$result = $conn->query("TRUNCATE TABLE tick history");
	 
	$result = $conn->query("UPDATE player set unassigned=200, extractor=0, genetic_lab=0, powerplant=0, factory=0,
		mineral=100000, organic=100000, energy=100000, score=0, status='inactive'");
		
	$result = $conn->query("UPDATE game set gamename='Revolution v1.3<br/>Rosebud', start_time='2008-09-16 10:00:00', 
		current_tick=1, status='Pre-Round', number_ticks_per_day=24");


?>
