<?php 

class FleetData {
	var $fleet;
	var $mission;
	var $target;

	var $att=0;
	var $def=0;
	var $foc=0;
	var $int=0;
	var $dis=0;

	var $damage=0; // in percent
	var $captured=0; // in percent
	var $structures_captured=0;
	
	var $new_creatures_fighting;
	
	var $creatures; // array
	var $creatures_killed; // array
	var $creatures_lost; // array
	var $creatures_gained; // array
	var $creatures_after; // array
	
	var $unassigned=0;
	var $extractors=0;
	var $genetic_labs=0;
	var $powerplants=0;
	var $factories=0;
	
	var $doubled=false;
	var $capped=false;
	
	function get_fleet_info() {
		return "{$this->att}A {$this->def}D {$this->foc}F {$this->int}i {$this->dis}d";
	}
	
	function get_fleet_structures() {
		return "{$this->unassigned}u {$this->extractors}e {$this->genetic_labs}g {$this->powerplants}p {$this->factories}f";
	}

	function get_total_structures() {
		return $this->unassigned + $this->extractors + $this->genetic_labs + $this->powerplants + $this->factories;
	}
	
}

class FleetCreatureNumbers {
	var $imp;
	var $wyrm;
	var $wyvern;
	var $dragon;
	
	var $sprite;
	var $dryad;
	var $centaur;
	var $unicorn;
	
	var $ogre;
	var $troll;
	var $giant;
	var $demon;
	
	var $cheetah;
	var $panther;
	var $tiger;
	var $lion;
	
	var $cyborg;
	var $spider;
	var $mantis;
	var $megadon;
	
	var $humvee;
	var $tank;
	var $crusher;
	var $doomcrusher;
}