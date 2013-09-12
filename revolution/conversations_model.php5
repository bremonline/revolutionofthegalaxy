<?php
	require_once('conversation_topic_data.php5'); 
	require_once('conversation_message_data.php5'); 
	
class ConversationsModel {
	function create_new_conversation_category($player_name, $type, $group, $category) {
		$conn = db_connect();
		$query = "insert into conversation_category values ('$type', '$group', '$category', '$player_name')";
		$result = $conn->query($query);			
	}

	function create_new_conversation_topic($player_name, $type, $group, $category, $subject) {
		$conn = db_connect();
		$query = "insert into conversation_topic values (0, '$type', '$group', '$category', '$player_name', '$subject')";
		$result = $conn->query($query);			
	}
	
	function get_conversation_categories_by_type_and_group($type, $group) {
		$categories = array();
		$conn = db_connect();
		$query = "select * from conversation_category where conversation_type='$type' and conversation_group='$group'";
		$result = $conn->query($query);			
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$categories[$count] = $row->conversation_category;
		}
		
		return $categories;
	}
	
	function check_conversation_category_with_creater($type, $group, $category, $creater) {
		$conn = db_connect();
		$query = "select * from conversation_category where conversation_type='$type' and conversation_group='$group' 
			and conversation_category='$category' and creater='$creater'";
		$result = $conn->query($query);			
		if ($result->num_rows == 0) return false;
		else return true;
	}
	
	function get_personal_conversation_contacts($player_name) {
		$contacts = array();
		$conn = db_connect();

		// First we get those where the player is the category 
		$query = "select * from conversation_category where conversation_type='personal' and conversation_group='individual'
			and conversation_category='$player_name'";
		$result = $conn->query($query);			
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$contacts[$count] = $row->creater;
		}

		// Ok, now the other side we get those that are the creater 
		$query = "select * from conversation_category where conversation_type='personal' and conversation_group='individual'
			and creater='$player_name'";
		$result = $conn->query($query);			
		for ($count=0; $row = $result->fetch_object(); $count++) {
			array_push($contacts, $row->conversation_category);
		}
		
		return $contacts;
	}
	
	function get_personal_topics($from, $to) {
		$topics = array();
		$conn = db_connect();
		$query = "SELECT max(cm.id) as mid, cm.topic_id as id, ct.conversation_type, ct.conversation_group, ct.conversation_category, ct.creater, ct.subject from conversation_topic ct, conversation_message cm
			WHERE cm.topic_id = ct.id
				AND ct.conversation_type='personal' 
				AND ct.conversation_group='individual'
				AND ct.conversation_category='$to' 
				AND ct.creater='$from'
				GROUP BY cm.topic_id
				ORDER BY max(cm.id) DESC";

		$result = $conn->query($query);			
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$ctd = new ConversationTopicData();
			$ctd->id = $row->id;
			$ctd->category = $row->conversation_category;
			$ctd->type = $row->conversation_type;
			$ctd->creater = $row->creater;
			$ctd->subject = $row->subject;
			$ctd->last_message_id = $row->mid;
			$topics[$count] = $ctd;
		}

		$query = "SELECT max(cm.id) as mid, cm.topic_id as id, ct.conversation_type, ct.conversation_group, ct.conversation_category, ct.creater, ct.subject from conversation_topic ct, conversation_message cm
			WHERE cm.topic_id = ct.id
				AND ct.conversation_type='personal' 
				AND ct.conversation_group='individual'
				AND ct.conversation_category='$from' 
				AND ct.creater='$to'
				GROUP BY cm.topic_id
				ORDER BY max(cm.id) DESC";
				
		$result = $conn->query($query);			
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$ctd = new ConversationTopicData();
			$ctd->id = $row->id;
			$ctd->category = $row->conversation_category;
			$ctd->type = $row->conversation_type;
			$ctd->creater = $row->creater;
			$ctd->subject = $row->subject;
			$ctd->last_message_id = $row->mid;
			array_push($topics, $ctd);
		}
	
		usort($topics, "cmp_topics");
		return $topics;
	}

	function get_all_personal_topics_by_player($player_name) {
		$topics = array();
		$conn = db_connect();
		$query = "SELECT id from conversation_topic 
			WHERE conversation_type='personal' 
				AND conversation_group='individual'
				AND conversation_category='$player_name'"; 
		
		$result = $conn->query($query);			
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$ctd = new ConversationTopicData();
			$ctd->id = $row->id;
			$topics[$count] = $ctd;
		}

		$query = "SELECT id from conversation_topic 
			WHERE conversation_type='personal' 
				AND conversation_group='individual'
				AND creater='$player_name'"; 
				
		$result = $conn->query($query);			
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$ctd = new ConversationTopicData();
			$ctd->id = $row->id;
			array_push($topics, $ctd);
		}
		return $topics;
	}

	function count_new_personal_topics($player_name) {
		$count = 0;
		$topics = $this->get_all_personal_topics_by_player($player_name);
		foreach ($topics as $ctd) {
			if ($this->is_new_messages_for_topic($player_name, $ctd->id)) $count++;
		}
		return $count;
	}

	function get_conversation_topics_by_type_group_and_category($type, $group, $category) {
		$topics = array();
		$conn = db_connect();
//		$query = "select * from conversation_topic where conversation_type='$type' and conversation_group='$group' and conversation_category='$category'";
		$query = "select cm.topic_id as id, ct.conversation_type, ct.conversation_group, ct.conversation_category, ct.creater, ct.subject from conversation_topic ct, conversation_message cm 
			WHERE cm.topic_id = ct.id
				AND ct.conversation_type='$type' 
				AND ct.conversation_group='$group' 
				AND ct.conversation_category='$category'
				GROUP BY cm.topic_id
				ORDER BY max(cm.id) DESC";

		$result = $conn->query($query);			
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$ctd = new ConversationTopicData();
			$ctd->id = $row->id;
			$ctd->category = $row->conversation_category;
			$ctd->group = $row->conversation_group;
			$ctd->type = $row->conversation_type;
			$ctd->creater = $row->creater;
			$ctd->subject = $row->subject;
			$topics[$count] = $ctd;
		}
		return $topics;
		
	}

	function get_all_general_conversation_topics() {
		$topics = array();
		$conn = db_connect();
		$query = "SELECT max( cm.id ) AS cmid, ct.id AS id
				FROM conversation_topic ct, conversation_message cm
				WHERE cm.topic_id = ct.id
				AND conversation_type = 'general'
				GROUP BY ct.id";
		$result = $conn->query($query);			
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$ctd = new ConversationTopicData();
			$ctd->id = $row->id;
			$topics[$count] = $ctd;
		}
		return $topics;	
	}
	

	function count_new_general_topics($player_name) {
		$count = 0;
		$topics = $this->get_all_general_conversation_topics();
		foreach ($topics as $ctd) {
			if ($this->is_new_messages_for_topic($player_name, $ctd->id)) $count++;
		}
		return $count;
	}

	function get_all_alliance_conversation_topics($alliance) {
		$topics = array();
		$conn = db_connect();
		$query = "SELECT max( cm.id ) AS cmid, ct.id AS id
				FROM conversation_topic ct, conversation_message cm
				WHERE cm.topic_id = ct.id
				AND conversation_type = 'alliance'
				AND conversation_group = '$alliance'
				GROUP BY ct.id";
		$result = $conn->query($query);			
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$ctd = new ConversationTopicData();
			$ctd->id = $row->id;
			$topics[$count] = $ctd;
		}
		return $topics;
	}

	function count_new_alliance_topics($player_name, $alliance) {
		$count = 0;
		$topics = $this->get_all_alliance_conversation_topics($alliance);
		foreach ($topics as $ctd) {
			if ($this->is_new_messages_for_topic($player_name, $ctd->id)) $count++;
		}
		return $count;
	}

	function count_new_conversation_topics_by_type_group_and_category($player_name, $type, $group, $category) {
		$count = 0;
		$topics = $this->get_conversation_topics_by_type_group_and_category($type, $group, $category);
		foreach ($topics as $ctd) {
			if ($this->is_new_messages_for_topic($player_name, $ctd->id)) $count++;
		}
		return $count;
	}

	function count_new_conversation_topics_by_personal_contact($player_name, $contact) {
		$count = 0;
		$topics = $this->get_personal_topics($player_name, $contact);
		foreach ($topics as $ctd) {
			if ($this->is_new_messages_for_topic($player_name, $ctd->id)) $count++;
		}
		return $count;
	}
	
	function get_topic_by_id($topic_id) {

		$conn = db_connect();
		$query = "select * from conversation_topic where id=$topic_id";
		$result = $conn->query($query);			
		$row = $result->fetch_object();
		
		$ctd = new ConversationTopicData();
		$ctd->id = $row->id;
		$ctd->type = $row->conversation_type;
		$ctd->group = $row->conversation_group;
		$ctd->category = $row->conversation_category;
		$ctd->creater = $row->creater;
		$ctd->subject = $row->subject;
		
		return $ctd;
	}
	
	function get_last_message_by_topic_id($topic_id) {
		$conn = db_connect();
		$query = "select * from conversation_message where id=(select max(id) from conversation_message where topic_id=$topic_id)";
		$result = $conn->query($query);	
		$row = $result->fetch_object();
		$cmd = new ConversationMessageData();
		$cmd->id = $row->id;
		$cmd->topic_id = $row->topic_id;
		$cmd->parent_id = $row->parent_id;
		$cmd->author = $row->author;
		$cmd->post_time = $row->post_time;
		$cmd->subject = $row->subject;
		$cmd->message_text = $row->message_text;
		return $cmd;
	}

	function get_all_messages_by_topic_id($topic_id) {
		$messages = array();
		$conn = db_connect();
		$query = "select id, parent_id, topic_id, author, post_time, subject, message_text from conversation_message where topic_id=$topic_id order by id";
		$result = $conn->query($query);	
		for ($count=0; $row = $result->fetch_object(); $count++) {
			$cmd = new ConversationMessageData();
			$cmd->id = $row->id;
			$cmd->topic_id = $row->topic_id;
			$cmd->parent_id = $row->parent_id;
			$cmd->author = $row->author;
			$cmd->post_time = $row->post_time;
			$cmd->subject = $row->subject;
			$cmd->message_text = $row->message_text;
			$messages[$count] = $cmd;
		}
		return $messages;
	}
	
	
	function set_conversation_last_seen($player_name, $topic_id) {
		$max = $this->get_max_message_id();
		
		if ($this->check_conversation_last_seen($player_name, $topic_id) ) {
			$this->update_conversation_last_seen($player_name, $topic_id, $max);
		} else {
			$this->insert_conversation_last_seen($player_name, $topic_id, $max);
		}
	}

	function get_conversation_last_seen($player_name, $topic_id) {
		$conn = db_connect();	
	  $query = "select last_message_id from conversation_last_seen where player_name='$player_name' and topic_id=$topic_id";
	  $result = $conn->query($query);
		if ($result->nuw_rows > 0) return 0;
	  $row = $result->fetch_object();
		return $row->last_message_id;
	}
	
	function check_conversation_last_seen($player_name, $topic_id) {
		$conn = db_connect();	
	  $query = "select last_message_id from conversation_last_seen where player_name='$player_name' and topic_id=$topic_id";
	  $result = $conn->query($query);
		if ($result->num_rows > 0) return true;
		else return false;
	}
	
	function insert_conversation_last_seen($player_name, $topic_id, $max) {
		$conn = db_connect();	
	  $query = "insert into conversation_last_seen values ('$player_name', $topic_id , $max) ";
	  $result = $conn->query($query);
	}
	
	function update_conversation_last_seen($player_name, $topic_id, $max) {
		$conn = db_connect();	
	  $query = "update conversation_last_seen set last_message_id=$max where player_name='$player_name' and topic_id=$topic_id ";
	  $result = $conn->query($query);
	}

	function get_max_message_id() {
		$conn = db_connect();	
	  $query = "select max(id) as max from conversation_message";
	  $result = $conn->query($query);
	  $row = $result->fetch_object();
		return $row->max;
	}
	
	function get_max_message_of_topic($topic_id) {
		$conn = db_connect();	
	  $query = "select max(id) as max from conversation_message where topic_id=$topic_id";
	  $result = $conn->query($query);
	  $row = $result->fetch_object();
		return $row->max;		
	}
	
	
	function is_new_messages_for_topic($player_name, $topic_id) {
		$max_seen = $this->get_conversation_last_seen($player_name, $topic_id);
		$max_message_of_topic = $this->get_max_message_of_topic($topic_id);
		
		if ($max_message_of_topic > $max_seen) return true;
		else return false;
	}
	
}

// For sorting the personal lists
	function cmp_topics($a, $b) {
		if ($a->last_message_id < $b->last_message_id) return 1;
		else if ($a->last_message_id > $b->last_message_id) return -1;
		else return 0;
	}
	

?>