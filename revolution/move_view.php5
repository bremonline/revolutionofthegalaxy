<?php
	require_once("player_data.php5");

class MoveView {
	function display_move_form() {
  	$player_name=$_SESSION['player_name'];
		
		$pd = new PlayerData();
		$pd->db_fill($player_name);
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='move_player'/>\n";
		echo "     <INPUT type='hidden' name='view' value='move'/>\n";


		echo "<TABLE class='STD' >\n";
		echo "   <TR><TH class='STD' colspan='5' style='color:red'>Note: All moves now take 24 ticks to complete, all fleets will be unavailable during the move.</TH></TR>\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='5'>Current Location</TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "   <TH class='STD' >Player</TH>\n";
		echo "   <TH class='STD' >Galaxy</TH>\n";
		echo "   <TH class='STD' >Star</TH>\n";
		echo "   <TH class='STD' >Planet</TH>\n";
		echo "   <TH class='STD' >Continent</TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD'> $pd->name of $pd->location</TD>\n";
		echo "  <TD class='STD'> $pd->galaxy</TD>\n";
		echo "  <TD class='STD'> $pd->star </TD>\n";
		echo "  <TD class='STD'> $pd->planet</TD>\n";
		echo "  <TD class='STD'> $pd->continent </TD>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='5'>New Location</TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "   <TH class='STD' >Player</TH>\n";
		echo "   <TH class='STD' >Galaxy</TH>\n";
		echo "   <TH class='STD' >Star</TH>\n";
		echo "   <TH class='STD' >Planet</TH>\n";
		echo "   <TH class='STD' >Continent</TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD'> $pd->name of $pd->location</TD>\n";
		echo "  <TD class='STD'>\n ";
		echo "  <SELECT name='galaxy'>\n";
		echo "   <OPTION value='1'>1</OPTION>\n";
		echo "   <OPTION value='2'>2</OPTION>\n";
		echo "  </SELECT>\n";
		echo "  </TD>\n";
//		echo "     <INPUT type='hidden' name='galaxy' value='2'/>\n";
		echo "  <TD class='STD'>\n";
		echo "  <SELECT name='star'>\n";
		for ($i=1;$i<=29;$i++) {
			echo "   <OPTION value='$i'>$i</OPTION>\n";
		}
		echo "  </SELECT>\n";
		echo "  </TD>\n";
		echo "  <TD class='STD'>\n";
		echo "  <SELECT name='planet'>\n";
		for ($i=1;$i<=9;$i++) {
			echo "   <OPTION value='$i'>$i</OPTION>\n";
		}
		echo "  </SELECT>\n";
		echo "  </TD>\n";
		echo "  <TD class='STD'>\n";
		echo "  <SELECT name='continent'>\n";
		for ($i=1;$i<=9;$i++) {
			echo "   <OPTION value='$i'>$i</OPTION>\n";
		}
		echo "  </SELECT>\n";
		echo "  </TD>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD'> Invite Key </TD>\n";
		echo "  <TD class='STD' colspan='4'>";
		echo "<INPUT type='text' name='invite_key' size='30' /> <I> * required if not first on planet</I> </TD>\n";
		echo "</TABLE>\n";
		echo "<INPUT type='submit' value='Move Player' />\n";
		echo "</FORM>\n";

	}
}
?>