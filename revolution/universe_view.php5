<?php
	require_once('db_fns.php5'); 
	require_once('player_data.php5'); 
	require_once('view_fns.php5'); 
	require_once('development_model.php5'); 
	require_once('scans_model.php5'); 
	require_once('game_model.php5'); 
	require_once('forts_model.php5'); 
	require_once('bombs_model.php5'); 
 
class UniverseView {
	var $galaxy;
	var $star;
	var $planet;
	
	function display_universe_view() {
  	$player_name=$_SESSION['player_name'];

		$this->galaxy = $_REQUEST["galaxy"];
		$this->star = $_REQUEST["star"];
		$this->planet = $_REQUEST["planet"];
		
		if (strlen($this->galaxy) == 0) {
			$pd = new PlayerData();
			$pd->db_fill($player_name);
			$this->galaxy = $pd->galaxy;
			$this->star = $pd->star;
			$this->planet = $pd->planet;
			
		}
		
		$this->display_galaxy_selector();
		$this->display_star_selector();
		$this->display_planet_selector();
		$this->display_continent_information();
		
		// First continent on a planet always holds the key to invites
		if ($pd->continent == 1) {
			$this->display_invite_key();
		}
	}
	
	function display_galaxy_selector() {
		echo "<TABLE class='STD' >\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='9'>Galaxy Selector</TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		for ($i=1;$i<=3;$i++) {
			$this->display_universe_button("galaxy", $i, $this->galaxy, 1);
		}
		echo " </TR>\n";
		echo "</TABLE>\n";
	}

	function display_star_selector() {
		$vf = new ViewFunctions();
		echo "<TABLE class='STD'>\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='19'>Star Selector</TH>\n";
		echo " </TR>\n";
		for ($i=0;$i<14;$i++) {
			echo " <TR>\n";
			for ($j=0; $j<19; $j++) {
				     if ($i==6 && $j==8) $this->display_universe_button("star", 1, $this->star, 3);
				else if ($i==6 && $j==9) ;
				else if ($i==6 && $j==10) ;
				else if ($i==5 && $j==10) $this->display_universe_button("star", 2, $this->star, 3);
				else if ($i==5 && $j==11) ;
				else if ($i==5 && $j==12) ;
				else if ($i==4 && $j==11) $this->display_universe_button("star", 3, $this->star, 3);
				else if ($i==4 && $j==12) ;
				else if ($i==4 && $j==13) ;
				else if ($i==3 && $j==11) $this->display_universe_button("star", 4, $this->star, 3);
				else if ($i==3 && $j==12) ;
				else if ($i==3 && $j==13) ;
				else if ($i==2 && $j==10) $this->display_universe_button("star", 5, $this->star, 3);
				else if ($i==2 && $j==11) ;
				else if ($i==2 && $j==12) ;
				else if ($i==1 && $j==9) $this->display_universe_button("star", 6, $this->star, 3);
				else if ($i==1 && $j==10) ;
				else if ($i==1 && $j==11) ;
				else if ($i==0 && $j==7) $this->display_universe_button("star", 7, $this->star, 3);
				else if ($i==0 && $j==8) ;
				else if ($i==0 && $j==9) ;
				else if ($i==0 && $j==4) $this->display_universe_button("star", 8, $this->star, 3);
				else if ($i==0 && $j==5) ;
				else if ($i==0 && $j==6) ;

				else if ($i==5 && $j==6) $this->display_universe_button("star", 9, $this->star, 3);
				else if ($i==5 && $j==7) ;
				else if ($i==5 && $j==8) ;
				else if ($i==5 && $j==3) $this->display_universe_button("star", 10, $this->star, 3);
				else if ($i==5 && $j==4) ;
				else if ($i==5 && $j==5) ;
				else if ($i==6 && $j==1) $this->display_universe_button("star", 11, $this->star, 3);
				else if ($i==6 && $j==2) ;
				else if ($i==6 && $j==3) ;
				else if ($i==7 && $j==0) $this->display_universe_button("star", 12, $this->star, 3);
				else if ($i==7 && $j==1) ;
				else if ($i==7 && $j==2) ;
				else if ($i==8 && $j==0) $this->display_universe_button("star", 13, $this->star, 3);
				else if ($i==8 && $j==1) ;
				else if ($i==8 && $j==2) ;
				else if ($i==9 && $j==0) $this->display_universe_button("star", 14, $this->star, 3);
				else if ($i==9 && $j==1) ;
				else if ($i==9 && $j==2) ;
				else if ($i==10 && $j==1) $this->display_universe_button("star", 15, $this->star, 3);
				else if ($i==10 && $j==2) ;
				else if ($i==10 && $j==3) ;
				
				else if ($i==7 && $j==6) $this->display_universe_button("star", 16, $this->star, 3);
				else if ($i==7 && $j==7) ;
				else if ($i==7 && $j==8) ;
				else if ($i==8 && $j==5) $this->display_universe_button("star", 17, $this->star, 3);
				else if ($i==8 && $j==6) ;
				else if ($i==8 && $j==7) ;
				else if ($i==9 && $j==5) $this->display_universe_button("star", 18, $this->star, 3);
				else if ($i==9 && $j==6) ;
				else if ($i==9 && $j==7) ;
				else if ($i==10 && $j==6) $this->display_universe_button("star", 19, $this->star, 3);
				else if ($i==10 && $j==7) ;
				else if ($i==10 && $j==8) ;
				else if ($i==11 && $j==7) $this->display_universe_button("star", 20, $this->star, 3);
				else if ($i==11 && $j==8) ;
				else if ($i==11 && $j==9) ;
				else if ($i==12 && $j==9) $this->display_universe_button("star", 21, $this->star, 3);
				else if ($i==12 && $j==10) ;
				else if ($i==12 && $j==11) ;
				else if ($i==12 && $j==12) $this->display_universe_button("star", 22, $this->star, 3);
				else if ($i==12 && $j==13) ;
				else if ($i==12 && $j==14) ;

				else if ($i==7 && $j==10) $this->display_universe_button("star", 23, $this->star, 3);
				else if ($i==7 && $j==11) ;
				else if ($i==7 && $j==12) ;
				else if ($i==7 && $j==13) $this->display_universe_button("star", 24, $this->star, 3);
				else if ($i==7 && $j==14) ;
				else if ($i==7 && $j==15) ;
				else if ($i==6 && $j==15) $this->display_universe_button("star", 25, $this->star, 3);
				else if ($i==6 && $j==16) ;
				else if ($i==6 && $j==17) ;
				else if ($i==5 && $j==16) $this->display_universe_button("star", 26, $this->star, 3);
				else if ($i==5 && $j==17) ;
				else if ($i==5 && $j==18) ;
				else if ($i==4 && $j==16) $this->display_universe_button("star", 27, $this->star, 3);
				else if ($i==4 && $j==17) ;
				else if ($i==4 && $j==18) ;
				else if ($i==3 && $j==16) $this->display_universe_button("star", 28, $this->star, 3);
				else if ($i==3 && $j==17) ;
				else if ($i==3 && $j==18) ;
				else if ($i==2 && $j==15) $this->display_universe_button("star", 29, $this->star, 3);
				else if ($i==2 && $j==16) ;
				else if ($i==2 && $j==17) ;

				else echo " <TH class='STD'> &nbsp; </TH>\n";
			}
			echo " </TR>\n";
		}
		echo "</TABLE>\n";
	}
	
	function display_planet_selector() {
		echo "<TABLE class='STD' >\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='9'>Planet Selector</TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		for ($i=1;$i<=9;$i++) {
			$this->display_universe_button("planet", $i, $this->planet, 1);
		}
		echo " </TR>\n";
		echo "</TABLE>\n";
	}
	
	
	function display_universe_button($level, $current, $selected, $colspan) {
		$vf = new ViewFunctions();
		
		$link="main_page.php5?view=universe";
		if (strcmp($level, "galaxy") == 0) 	
			$link="main_page.php5?view=universe&galaxy=$current&star={$this->star}&planet={$this->planet}";
		else if (strcmp($level, "star") == 0) 
			$link="main_page.php5?view=universe&galaxy={$this->galaxy}&star=$current&planet={$this->planet}";
		else if (strcmp($level, "planet") == 0) 
			$link="main_page.php5?view=universe&galaxy={$this->galaxy}&star={$this->star}&planet=$current";

		$current_color="#A0A040";
		$current_highlight="#E0E080";
		$other_color="#808080";
		$other_highlight="#A0A040";
		if ($current == $selected) {
			$vf->display_colspan_button($current, $current_color, $colspan, $current_highlight, $link);
		} else {
			$vf->display_colspan_button($current, $other_color, $colspan, $other_highlight, $link);			
		}
	}
	function display_continent_information() {
		$player_name = $_SESSION['player_name'];
		$pd = new PlayerData();
		$dm = new DevelopmentModel();
		$sm = new ScansModel();
		$gm = new GameModel();
		$current_tick = $gm->get_current_tick();
		
	  $conn = db_connect();
		$query = "select * from player where galaxy=$this->galaxy and star=$this->star and planet=$this->planet";
		$result = $conn->query($query);
		echo "<TABLE class='STD' >\n";
		echo " <TR>\n";
		echo "   <TH class='STD' colspan='5'>Continent</TH>\n";
		echo " </TR>\n";
		if ($result->num_rows == 0) {
			echo "  <TD class='STD' colspan='5'> No Players on this planet </TD>\n";
		} else {
			echo " <TR>\n";
			echo "   <TH class='STD' >Location</TH>\n";
			echo "   <TH class='STD' >Player</TH>\n";
			echo "   <TH class='STD' >Structures</TH>\n";
			echo "   <TH class='STD' >Score</TH>\n";
			echo "   <TH class='STD' >Last Online</TH>\n";
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
				echo "  <TD class='STD' style='background-color:$color'> {$row->galaxy}:{$row->star}:{$row->planet}:{$row->continent} </TD>\n";
				echo "  <TD class='STD' style='background-color:$color'> $row->name of $row->location </TD>\n";
				echo "  <TD class='STD' style='background-color:$color'> {$structures}</TD>\n";
				echo "  <TD class='STD' style='background-color:$color'> $row->score </TD>\n";
				echo "  <TD class='STD' style='background-color:$color'> {$row->last_online}</TD>\n";
				echo " </TR>\n";
			}
		}
		echo "</TABLE>\n";
	}

	function display_invite_key() {
	  $conn = db_connect();
		$query = "select invite_key from invite_key where galaxy=$this->galaxy and star=$this->star and planet=$this->planet";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		// Note this form goes to the MoveController, not UniverseController
		echo "<BR />\n";
		echo "<FORM method='get' action='main_page.php5'>\n";
		echo "     <INPUT type='hidden' name='action' value='set_invite_key'/>\n";
		echo "     <INPUT type='hidden' name='view' value='universe'/>\n";
		echo "     <INPUT type='hidden' name='galaxy' value='$this->galaxy'/>\n";
		echo "     <INPUT type='hidden' name='star' value='$this->star'/>\n";
		echo "     <INPUT type='hidden' name='planet' value='$this->planet'/>\n";
		echo "<TABLE class='STD' >\n";
		echo " <TR>\n";
		echo "   <TH class='STD' >Invite Key</TH>\n";
		echo "   <TH class='STD' > Current</TH>\n";
		echo "   <TH class='STD' > Set to: </TH>\n";
		echo " </TR>\n";
		echo " <TR>\n";
		echo "  <TD class='STD'> Invite Key </TD>\n";
		echo "  <TD class='STD'> $row->invite_key </TD>\n";
		echo "  <TD class='STD'> <INPUT type='text' name='invite_key' size='30' /> </TD>\n";
		echo "</TABLE>\n";
		echo "<INPUT type='submit' value='Set Invite Key' />\n";
		echo "</FORM>\n";
		
	}

}
?>