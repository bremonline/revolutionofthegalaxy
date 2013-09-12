<?php
	require_once('db_fns.php5'); 
	require_once('view_fns.php5'); 
	require_once('player_data.php5'); 

class StructuresView {
	
	function display_structures() {
		$gm = new GameModel();
		
		$ticks_per_day = $gm->get_game_parameter("number_ticks_per_day");
		$base_mineral = $gm->get_game_parameter("base_mineral");
		$base_organic = $gm->get_game_parameter("base_organic");
		$base_energy = $gm->get_game_parameter("base_energy");

		$mineral_per_structure = $gm->get_game_parameter("mineral_per_structure");
		$organic_per_structure = $gm->get_game_parameter("organic_per_structure");
		$energy_per_structure = $gm->get_game_parameter("energy_per_structure");

		$base_creature_production = $gm->get_game_parameter("base_creature_production");;
		$factory_per_structure = 1;
		
		$player_name = $_SESSION['player_name'];

		$pd = new PlayerData();				
		$pd->db_fill($player_name);

		$new_structure_increase = $gm->get_game_parameter("increase_per_structure");
		$cost = $new_structure_increase * (1 + $pd->extractor + $pd->genetic_lab + $pd->powerplant + $pd->factory);

		$extractor = $pd->extractor;
		$extractor_production = $extractor * $mineral_per_structure;
		$full_extractor_production = $extractor_production + $base_mineral;
		$daily_extractor_production = $full_extractor_production * $ticks_per_day;
		
		$genetic_lab = $pd->genetic_lab;
		$genetic_lab_production = $genetic_lab * $organic_per_structure;
		$full_genetic_lab_production = $genetic_lab_production + $base_organic;
		$daily_genetic_lab_production = $full_genetic_lab_production * $ticks_per_day;

		$powerplant = $pd->powerplant;
		$powerplant_production = $powerplant * $energy_per_structure;
		$full_powerplant_production = $powerplant_production + $base_energy;
		$daily_powerplant_production = $full_powerplant_production * $ticks_per_day;

		$factory = $pd->factory;
		$factory_production = $factory * $factory_per_structure;
		$full_factory_production = $factory_production + $base_creature_production;
		$daily_factory_production = $full_factory_production;
		
		$vf = new ViewFunctions();
		echo "<TABLE class='STD' id='structures_table'>\n";
		echo "<TR>\n";
		echo "<TH class='STD'>&nbsp;</TH><TH class='STD'>Structures</TH><TH class='STD'>Production*</TH>";
		echo "<TH class='STD'>Base Production</TH><TH class='STD'>Total</TH><TH class='STD'>Per Day</TH>";
		echo "<TH class='STD'>&nbsp;</TH>";
		echo "<TR>\n";
		echo "  <TH class='STD'>Extractor</TH>\n";
		echo "  <TD class='STD'>$extractor structures</TD>\n";
		echo "  <TD class='STD'>$extractor_production mineral</TD>\n";
		echo "  <TD class='STD'>$base_mineral m</TD>\n";
		echo "  <TD class='STD'>$full_extractor_production m</TD>\n";
		echo "  <TD class='STD'>$daily_extractor_production m</TD>\n";
		$vf->display_button('Allocate', '602080', 'D0D040', 
				'main_page.php5?view=structures&action=allocate&structure_type=extractor&number=1');
		$vf->display_button('Allocate 5', '602080', 'D0D040', 
				'main_page.php5?view=structures&action=allocate&structure_type=extractor&number=5');
		echo "</TR>\n";
		echo "<TR>\n";
		echo "  <TH class='STD'>Genetic Lab</TH>\n";
		echo "  <TD class='STD'>$genetic_lab structures</TD>\n";
		echo "  <TD class='STD'>$genetic_lab_production organics</TD>\n";
		echo "  <TD class='STD'>$base_organic o</TD>\n";
		echo "  <TD class='STD'>$full_genetic_lab_production o</TD>\n";
		echo "  <TD class='STD'>$daily_genetic_lab_production o</TD>\n";
		$vf->display_button('Allocate', '602080', 'D0D040', 
				'main_page.php5?view=structures&action=allocate&structure_type=genetic_lab&number=1');
		$vf->display_button('Allocate 5', '602080', 'D0D040', 
				'main_page.php5?view=structures&action=allocate&structure_type=genetic_lab&number=5');
		echo "</TR>\n";
		echo "<TR>\n";
		echo "  <TH class='STD'>Powerplant</TH>\n";
		echo "  <TD class='STD'>$powerplant structures</TD>\n";
		echo "  <TD class='STD'>$powerplant_production energy</TD>\n";
		echo "  <TD class='STD'>$base_energy e</TD>\n";
		echo "  <TD class='STD'>$full_powerplant_production e</TD>\n";
		echo "  <TD class='STD'>$daily_powerplant_production e</TD>\n";
		$vf->display_button('Allocate', '602080', 'D0D040', 
				'main_page.php5?view=structures&action=allocate&structure_type=powerplant&number=1');
		$vf->display_button('Allocate 5', '602080', 'D0D040', 
				'main_page.php5?view=structures&action=allocate&structure_type=powerplant&number=5');
		echo "</TR>\n";
		echo "<TR>\n";
		echo "  <TH class='STD'>Factory</TH>\n";
		echo "  <TD class='STD'>$factory structures</TD>\n";
		echo "  <TD class='STD'>$factory_production creatures</TD>\n";
		echo "  <TD class='STD'>$base_creature_production c</TD>\n";
		echo "  <TD class='STD'>$full_factory_production c</TD>\n";
		echo "  <TD class='STD'>$daily_factory_production c</TD>\n";
		$vf->display_button('Allocate', '602080', 'D0D040', 
				'main_page.php5?view=structures&action=allocate&structure_type=factory&number=1');
		$vf->display_button('Allocate 5', '602080', 'D0D040', 
				'main_page.php5?view=structures&action=allocate&structure_type=factory&number=5');
		echo "</TR>\n";
		echo "<TR>\n";
		echo "<TH class='STD'>&nbsp;</TH>";

		echo "  <TH class='STD' colspan='4' style='text-align:left'>Cost to allocate 1 more structure: $cost mineral</TH>\n";
		echo "  <TH class='STD' colspan='3' style='text-align:right'>Number unallocated structures: $pd->unassigned</TH>\n";
		echo "</TR>\n";
		echo "<TR>\n";
		$five = (5 * $cost) + (15 * $new_structure_increase);
		echo "  <TH class='STD'>&nbsp;</TH>";
		echo "  <TH class='STD' colspan='2' style='text-align:left'>Cost to allocate 5 more structures: $five mineral</TH>\n";
		echo "</TR>\n";
		echo "</TABLE>\n";

		echo "<TABLE class='STD'>\n";
		echo "  <TR><TH class='STD' style='text-align:left'>* Production estimates are based on 250 per tick, actual results may be lower if any victory conditions have been met.<BR />";
		echo "  <A href='main_page.php5?view=victory'>See Here for victory condtions </A></TH></TR>\n";
		echo "</TABLE>\n";
			
	}
}

?>