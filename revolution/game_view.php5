<?php
	require_once('db_fns.php5'); 

class GameView {
	function display_game_bar() {
		$conn = db_connect();	
	  $query = "select* from game";
	  $result = $conn->query($query);
		$row = $result->fetch_object();
		
	
		echo "<TABLE class='STD' style='width:150px;' name='gameview'>\n";
		echo "  <TR><TD class='COMMANDBAR'>$row->gamename</TD></TR>\n";
		echo "  <TR><TD class='COMMANDBAR'>Tick: $row->current_tick</TD></TR>\n";
		echo "  <TR><TD class='COMMANDBAR'>Last Updated:<br /> $row->last_updated_time</TD></TR>\n";
		echo "  <TR><TD class='COMMANDBAR'>Page Updated:<br /> " . get_db_time()  . "</TD></TR>\n";
		echo "  <TR><TD class='COMMANDBAR' id='clock' name='clock'>Current Time:<br /> " . get_db_time()  . "</TD></TR>\n";
		echo "  <TR><TD class='COMMANDBAR'><B>$row->number_ticks_per_day ticks per day</B> </TD></TR>\n";
		echo "  <TR><TD class='COMMANDBAR'>Round Started <br />$row->start_time</TD></TR>\n";
		echo "  <TR><TD class='COMMANDBAR'>Status: <B> $row->status </B> </TD></TR>\n";
		echo "</TABLE>\n";
	}
}

?>