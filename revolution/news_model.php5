<?php
	require_once('db_fns.php5');
	require_once('game_model.php5');
	require_once('news_data.php5');

class NewsModel {
	function add_new_news($player_name, $category, $type, $subject, $text) {
		
		$gm = new GameModel();
		$ct = $gm->get_current_tick();
		
		$conn = db_connect();
		$query = "insert into news values (0, '$player_name', '$category', '$type', '$subject', NOW(), $ct, false, '$text')";
		$result = $conn->query($query);

	}
	
	function get_player_news($player_name) {
	  $conn = db_connect();
		$query = "select * from news where player_name='$player_name' and category='player' 
			order by time desc limit 0, 20";
		$result = $conn->query($query);
		
		$text = " <TABLE class='NEWS'>\n";
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$class='NEWS-GOOD';
			
			$text = $text . "  
				<TR>
			  	<TH class='$class'>#{$row->ID} </TH>
			  	<TH class='$class'>$row->subject </TH>
			  	<TH class='$class'>$row->time </TH>
			  </TR>\n
			  <TR>\n
			  	<TD class='$class' colspan='3' style='text-align:left' >$row->text </TD>
			  </TR>\n";
		}
		$text = $text . " </TABLE>\n";
		return $text;
	}
	
	function mark_news_read($player_name, $id) {
		$conn = db_connect();
		$query = "update news set has_been_read=true where player_name='$player_name' and id=$id";
		$result = $conn->query($query);
	}
	
	function get_player_news_by_type($player_name, $type) {
		$news_list = array();
	  $conn = db_connect();
	  if ($type == 'all') $query = "select * from news where player_name='$player_name' and category='player' order by ID desc limit 0, 50";
		else $query = "select * from news where player_name='$player_name' and category='player' and type='$type' order by ID desc limit 0, 50";
		
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$news_item = new NewsData();
			$news_item->id = $row->ID;
			$news_item->player_name = $row->player_name;
			$news_item->category = $row->category;
			$news_item->type = $row->type;
			$news_item->subject = $row->subject;
			$news_item->time = $row->time;
			$news_item->tick = $row->tick;
			$news_item->has_been_read = $row->has_been_read;
			$news_item->text = $row->text;


			$news_list[$count] = $news_item;
		}
		
		return $news_list;		
	}

	function get_universe_news_by_type($player_name, $type) {
		$news_list = array();
	  $conn = db_connect();
	  if ($type == 'all') $query = "select * from news where category='universe' order by ID desc limit 0, 20";
		else $query = "select * from news where category='universe' and type='$type' order by ID desc limit 0, 20";
		
		$result = $conn->query($query);
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$news_item = new NewsData();
			$news_item->id = $row->ID;
			$news_item->player_name = $row->player_name;
			$news_item->category = $row->category;
			$news_item->type = $row->type;
			$news_item->subject = $row->subject;
			$news_item->time = $row->time;
			$news_item->tick = $row->tick;
			$news_item->has_been_read = $row->has_been_read;
			$news_item->text = $row->text;


			$news_list[$count] = $news_item;
		}
		
		return $news_list;		
	}
	
	function get_individual_news_item($player_name, $category, $id) {
		$conn = db_connect();
		if ($category == 'player') $query = "select * from news where player_name='$player_name' and id=$id";
		else $query = "select * from news where id=$id";
		$result = $conn->query($query);
		$row = $result->fetch_object();

		$news_item = new NewsData();
		$news_item->id = $row->ID;
		$news_item->player_name = $row->player_name;
		$news_item->category = $row->category;
		$news_item->type = $row->type;
		$news_item->subject = $row->subject;
		$news_item->time = $row->time;
		$news_item->tick = $row->tick;
		$news_item->has_been_read = $row->has_been_read;
		$news_item->text = $row->text;
		
		return $news_item;		
	}
	
	function get_unread_player_news_by_type($player_name, $type) {
	  $conn = db_connect();
	  if ($type == 'all') $query = "select count(*) as count from news where player_name='$player_name' and category='player' and has_been_read=false";
		else $query = "select count(*) as count from news where player_name='$player_name' and category='player' and has_been_read=false and type='$type'";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->count;
	}
	
	function get_new_universe_news_by_type($player_name, $type) {
		return 0;
	}
	
	function get_max_news_number() {
	  $conn = db_connect();
		$query = "select max(id) as max from news";
		$result = $conn->query($query);
		$row = $result->fetch_object();
		return $row->max;
	}
	
	function set_last_seen_news($player_name, $type) {
		$max = $this->get_max_news_number();
		
		//If there is a row, update it
		if ($this->get_last_seen_news_by_type($player_name, $type)) {
			$this->update_last_seen_news_by_type($player_name, $type, $max);
		} else {
			$this->insert_last_seen_news_by_type($player_name, $type, $max);
		}
	}

	function get_last_seen_news_by_type($player_name, $type) {
	  $conn = db_connect();
		$query = "SELECT last_read_id FROM last_seen 
			WHERE player_name = '$player_name'
			  AND communication_type = 'news'
			  AND message_category = 'universe'
			  AND message_group = '$type'
			  AND message_channel = '$type'";
		$result = $conn->query($query);
		if ($result->num_rows == 0) return 0;
		$row = $result->fetch_object();
//		echo "<BR />Last Seen: $query [$row->last_read_id]";
		return $row->last_read_id;
	}
	
	function update_last_seen_news_by_type($player_name, $type, $max) {
	  $conn = db_connect();
		$query = "UPDATE last_seen SET last_read_id=$max 
			WHERE player_name = '$player_name'
			  AND communication_type = 'news'
			  AND message_category = 'universe'
			  AND message_group = '$type'
			  AND message_channel = '$type'";		
		$result = $conn->query($query);
	}
	
	function insert_last_seen_news_by_type($player_name, $type, $max) {
	  $conn = db_connect();
		$query = "insert into last_seen values ('$player_name', 'news', 'universe', '$type', '$type', $max )";		
		$result = $conn->query($query);		
	}

	function count_unread_universe_news_by_type($player_name, $type) {
		$last_read_id = $this->get_last_seen_news_by_type($player_name, $type);

	  $conn = db_connect();
		$query = "SELECT count(id) as count FROM news 
			WHERE category = 'universe'
			  AND type = '$type'
			  AND id > $last_read_id
		 	  "; 
		 	 
		$result = $conn->query($query);
		if ($result->num_rows == 0) return 0;
		$row = $result->fetch_object();
//		echo "\n<BR />Count Unread $type, $group, $channel :  $query [$row->count]";
		return $row->count;
	}

	
}

?>