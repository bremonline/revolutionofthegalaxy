<?php
	require_once('db_fns.php5'); 
	require_once('player_data.php5'); 
	require_once('view_fns.php5'); 
	require_once('alliance_model.php5'); 
	require_once('development_model.php5'); 
	require_once('scans_model.php5'); 
	require_once('pulses_model.php5'); 
	require_once('fleet_model.php5'); 
	require_once('game_model.php5'); 
	require_once('quick_box.php5'); 
	require_once('forts_model.php5'); 
	require_once('bombs_model.php5'); 

class RankingView {
	function display_ranking($subview) {
		$this->display_ranking_bar();
		$am = new AllianceModel();
		if ($subview == '') $subview = 'player';

		if (strcmp($subview, "player") == 0) $this->display_player_ranking();
		else if (strcmp($subview, "alliance") == 0) $am->display_alliance_ranking("main_page.php5?view=rankings&subview=alliance");
	}

	function display_ranking_bar() {
		echo "<TABLE class='STD'><TR>\n";
		$view_fns = new ViewFunctions();
		$view_fns->display_button('Player Rankings', 'A04080', 'E080B0', 'main_page.php5?view=rankings&subview=player');
		$view_fns->display_button('Alliance Rankings', '704080', 'B080B0', 'main_page.php5?view=rankings&subview=alliance');
		$view_fns->display_button('Planet Rankings', '504080', '808080', 'main_page.php5?view=rankings&subview=planet');
		$view_fns->display_button('Star Rankings', '204080', '608080', 'main_page.php5?view=rankings&subview=galaxy');
		echo "</TR></TABLE>\n";
		
	}

	function display_player_ranking() {
		$player_name = $_SESSION['player_name'];
		$pd = new PlayerData();

 		$order=$_REQUEST['order'];
 		if ($order == '') $order = 'score'; 
 		$dm = new DevelopmentModel();
 		$sm = new ScansModel();
 		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		
		$vf = new ViewFunctions();
	  $conn = db_connect();
	  if (strcmp($order, "location") == 0) $order_by = "ORDER BY galaxy, star, planet, continent";
	  else if (strcmp($order, "player") == 0) $order_by = "ORDER BY name";
	  else if (strcmp($order, "alliance") == 0) $order_by = "ORDER BY alliance";
	  else if (strcmp($order, "structures") == 0) $order_by = "ORDER BY (unassigned + extractor + genetic_lab + powerplant + factory) desc";
	  else if (strcmp($order, "score") == 0) $order_by = "ORDER BY score desc";
	  else if (strcmp($order, "last_online") == 0) $order_by = "ORDER BY last_online desc";
	  else "ORDER BY score desc";
	  
		$query = "select * from player p left join player_alliance pa on p.name = pa.player_name where p.status != 'inactive' $order_by";
		$result = $conn->query($query);
		echo "<TABLE class='STD' id='ranking'>\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='5'>Rankings</TH>\n";
		echo " </TR>\n";
		
		
		
		if ($result->num_rows == 0) {
			echo "  <TD class='STD' colspan='5'> No Players in the game ??? </TD>\n";
		} else {
			$color = "606060";
			echo " <TR>\n";
			$vf->display_button('Location', '008000', '40B040', 'main_page.php5?view=rankings&subview=player&order=location');
			$vf->display_button('Alliance', '008000', '40B040', 'main_page.php5?view=rankings&subview=player&order=alliance');
			$vf->display_button('Player', '008000', '40B040', 'main_page.php5?view=rankings&subview=player&order=player');
			$vf->display_button('Structures', '008000', '40B040', 'main_page.php5?view=rankings&subview=player&order=structures');
			$vf->display_button('Score', '008000', '40B040', 'main_page.php5?view=rankings&subview=player&order=score');
			$vf->display_button('Last Online', '008000', '40B040', 'main_page.php5?view=rankings&subview=player&order=last_online');
			echo " </TR>\n";
			for ($count=0; $row = $result->fetch_object(); $count++) {
				if ($pd->is_player_active($row->name) ) { $color = "606060"; $hcolor = "808080"; }
				else { $color = "A02060"; $hcolor = "C04080"; }
				if ($dm->does_player_know_development($player_name, "Universe Monitors")) {
					// Universe Monitors should also display forts/bombs/pbombs/traps/psy-traps
					$fm = new FortsModel();
					$forts = $fm->get_number_forts($row->name);
					if ($forts < 0) $forts = 0;
					$bm = new BombsModel();
					$bombs = $bm->get_number_bombs($row->name, "Bomb");
					if (!$bombs) $bombs = 0;
					$poison = $bm->get_number_bombs($row->name, "Poison Bomb");
					if (!$poison) $poison = 0;
					$traps = $bm->get_number_bombs($row->name, "Trap");
					if (!$traps) $traps = 0;
					$psy = $bm->get_number_bombs($row->name, "Psychological Trap");
					if (!$psy) $psy = 0;
					
					$total = $row->unassigned + $row->extractor + $row->genetic_lab + $row->powerplant + $row->factory;
					$structures = "{$total} - {$row->unassigned}u/{$row->extractor}e/{$row->genetic_lab}g/{$row->powerplant}p/{$row->factory}f <br/>" .
							"{$forts}f/{$bombs}b/{$poison}p/{$traps}t/{$psy}psy";					
				} 
				else if ($sm->check_monitor($player_name, $row->name, "structure", $current_tick)) {
					$total = $row->unassigned + $row->extractor + $row->genetic_lab + $row->powerplant + $row->factory;
					$structures = "{$total} - {$row->unassigned}u/{$row->extractor}e/{$row->genetic_lab}g/{$row->powerplant}p/{$row->factory}f";					
				} else {
					$structures = $row->unassigned + $row->extractor + $row->genetic_lab + $row->powerplant + $row->factory;
				}
				echo " <TR>\n";
				echo "  <TD class='STD' style='background-color:$color' > {$row->galaxy}:{$row->star}:{$row->planet}:{$row->continent} </TD>\n";
				if (is_null($row->alliance)) $alliance = "&nbsp;";
				else $alliance = $row->alliance;
				if ($alliance != '&nbsp;') $vf->display_button("$alliance", "$color", "$hcolor", "main_page.php5?view=alliances&subview=details&alliance=$alliance");
				else $vf->display_inactive_button("$alliance", "$color");
				echo "  <TD class='STD player' style='background-color:$color' player='$row->name' > $row->name ";
				display_quick_action_box($row->name, $row->galaxy, $row->star, $row->planet, $row->continent, $row->smf_id);
				echo " </TD>\n";
				echo "  <TD class='STD' style='background-color:$color'> {$structures} </TD>\n";
				$format_score = number_format($row->score);
				echo "  <TD class='STD' style='background-color:$color'> {$format_score} </TD>\n";
				echo "  <TD class='STD' style='background-color:$color'> {$row->last_online}</TD>\n";
				echo " </TR>\n";
			}
		}
		echo "</TABLE>\n";
	}
	
	function display_just_player_ranking() {
		$player_name = $_SESSION['player_name'];
		$pd = new PlayerData();

 		$order=$_REQUEST['order'];
 		if ($order == '') $order = 'score'; 
 		$dm = new DevelopmentModel();
 		$sm = new ScansModel();
 		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		
		$vf = new ViewFunctions();
	  $conn = db_connect();
	  if (strcmp($order, "location") == 0) $order_by = "ORDER BY galaxy, star, planet, continent";
	  else if (strcmp($order, "player") == 0) $order_by = "ORDER BY name";
	  else if (strcmp($order, "alliance") == 0) $order_by = "ORDER BY alliance";
	  else if (strcmp($order, "structures") == 0) $order_by = "ORDER BY (unassigned + extractor + genetic_lab + powerplant + factory) desc";
	  else if (strcmp($order, "score") == 0) $order_by = "ORDER BY score desc";
	  else if (strcmp($order, "last_online") == 0) $order_by = "ORDER BY last_online desc";
	  else "ORDER BY score desc";
	  
		$query = "select * from player p left join player_alliance pa on p.name = pa.player_name where p.status != 'inactive' $order_by";
		$result = $conn->query($query);
		echo "<TABLE class='STD' id='ranking'>\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='5'>Rankings</TH>\n";
		echo " </TR>\n";
		
		
		
		if ($result->num_rows == 0) {
			echo "  <TD class='STD' colspan='5'> No Players in the game ??? </TD>\n";
		} else {
			$color = "606060";
			for ($count=0; $row = $result->fetch_object(); $count++) {
				if ($pd->is_player_active($row->name) ) { $color = "606060"; $hcolor = "808080"; }
				else { $color = "A02060"; $hcolor = "C04080"; }
					$fm = new FortsModel();
					$forts = $fm->get_number_forts($row->name);
					if ($forts < 0) $forts = 0;
					$bm = new BombsModel();
					$bombs = $bm->get_number_bombs($row->name, "Bomb");
					if (!$bombs) $bombs = 0;
					$poison = $bm->get_number_bombs($row->name, "Poison Bomb");
					if (!$poison) $poison = 0;
					$traps = $bm->get_number_bombs($row->name, "Trap");
					if (!$traps) $traps = 0;
					$psy = $bm->get_number_bombs($row->name, "Psychological Trap");
					if (!$psy) $psy = 0;
					
					$total = $row->unassigned + $row->extractor + $row->genetic_lab + $row->powerplant + $row->factory;
					$structures = "{$total} - {$row->unassigned}u/{$row->extractor}e/{$row->genetic_lab}g/{$row->powerplant}p/{$row->factory}f <br/>" .
							"{$forts}f/{$bombs}b/{$poison}p/{$traps}t/{$psy}psy";					
				echo " <TR>\n";
				echo "  <TD class='STD' style='background-color:$color' > {$row->galaxy}:{$row->star}:{$row->planet}:{$row->continent} </TD>\n";
				if (is_null($row->alliance)) $alliance = "&nbsp;";
				else $alliance = $row->alliance;
				$vf->display_inactive_button("$alliance", "$color");
				echo "  <TD class='STD' style='background-color:$color' player='$row->name' > $row->name ";
				echo " </TD>\n";
				echo "  <TD class='STD' style='background-color:$color'> {$structures} </TD>\n";
				$format_score = number_format($row->score);
				echo "  <TD class='STD' style='background-color:$color'> {$format_score} </TD>\n";
				echo "  <TD class='STD' style='background-color:$color'> {$row->last_online}</TD>\n";
				echo " </TR>\n";
			}
		}
		echo "</TABLE>\n";
	}
	

}
?>