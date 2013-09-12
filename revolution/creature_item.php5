<?php
	require_once('db_fns.php5'); 

class CreatureItem {
	var $name;
	var $development_item;
	var $mineral;
	var $organic;
	var $att;
	var $def;
	var $foc;
	var $int;
	var $dis;
	var $weight;
	var $ticks;
	var $description;
	var $class;
	var $type;
	var $level;
	
		function db_fill($name) {
		  $conn = db_connect();
			$query = "select * from creature_items where name='$name'";	
			$result = $conn->query($query);
			$row = $result->fetch_object(); // Should be only one row
			$this->populate($row);
		}	
	
		function populate($row) {
			$this->name = $row->name;
			$this->development_item = $row->development_item;
			$this->mineral = $row->mineral;
			$this->organic = $row->organic;
			$this->att = $row->attack;
			$this->def = $row->defense;
			$this->foc = $row->focus;
			$this->int = $row->intelligence;
			$this->dis = $row->discipline;
			$this->weight = $row->weight;
			$this->ticks = $row->ticks;
			$this->description = $row->description;
			
			$this->set_extended_characteristics($row->name);
		}
	
	function set_extended_characteristics($name) {
		if (strcmp($name, "Imp") == 0) { $this->class='drake'; $this->type='genetic'; $this->level=1; }		
		if (strcmp($name, "Wyrm") == 0) { $this->class='drake'; $this->type='genetic'; $this->level=2; }		
		if (strcmp($name, "Wyvern") == 0) { $this->class='drake'; $this->type='genetic'; $this->level=3; }		
		if (strcmp($name, "Dragon") == 0) { $this->class='drake'; $this->type='genetic'; $this->level=4; }		

		if (strcmp($name, "Sprite") == 0) { $this->class='fairy'; $this->type='genetic'; $this->level=1; }		
		if (strcmp($name, "Dryad") == 0) { $this->class='fairy'; $this->type='genetic'; $this->level=2; }		
		if (strcmp($name, "Centaur") == 0) { $this->class='fairy'; $this->type='genetic'; $this->level=3; }		
		if (strcmp($name, "Unicorn") == 0) { $this->class='fairy'; $this->type='genetic'; $this->level=4; }		

		if (strcmp($name, "Ogre") == 0) { $this->class='humanoid'; $this->type='hybrid'; $this->level=1; }		
		if (strcmp($name, "Troll") == 0) { $this->class='humanoid'; $this->type='hybrid'; $this->level=2; }		
		if (strcmp($name, "Giant") == 0) { $this->class='humanoid'; $this->type='hybrid'; $this->level=3; }		
		if (strcmp($name, "Demon") == 0) { $this->class='humanoid'; $this->type='hybrid'; $this->level=4; }		

		if (strcmp($name, "Cheetah") == 0) { $this->class='feline'; $this->type='hybrid'; $this->level=1; }		
		if (strcmp($name, "Panther") == 0) { $this->class='feline'; $this->type='hybrid'; $this->level=2; }		
		if (strcmp($name, "Tiger") == 0) { $this->class='feline'; $this->type='hybrid'; $this->level=3; }		
		if (strcmp($name, "Lion") == 0) { $this->class='feline'; $this->type='hybrid'; $this->level=4; }		

		if (strcmp($name, "Cyborg") == 0) { $this->class='legged'; $this->type='cybernetic'; $this->level=1; }		
		if (strcmp($name, "Spider") == 0) { $this->class='legged'; $this->type='cybernetic'; $this->level=2; }		
		if (strcmp($name, "Mantis") == 0) { $this->class='legged'; $this->type='cybernetic'; $this->level=3; }		
		if (strcmp($name, "Magadon") == 0) { $this->class='legged'; $this->type='cybernetic'; $this->level=4; }		

		if (strcmp($name, "Humvee") == 0) { $this->class='tracked'; $this->type='cybernetic'; $this->level=1; }		
		if (strcmp($name, "Tank") == 0) { $this->class='tracked'; $this->type='cybernetic'; $this->level=2; }		
		if (strcmp($name, "Crusher") == 0) { $this->class='tracked'; $this->type='cybernetic'; $this->level=3; }		
		if (strcmp($name, "Doomcrusher") == 0) { $this->class='tracked'; $this->type='cybernetic'; $this->level=4; }		
	}
}