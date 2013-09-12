delete from chat_last_online;
delete from chat_message;
delete from chat_player;
delete from click_history;
delete from conversation_message;
delete from conversation_topic;
delete from last_seen;
delete from login_history;
delete from milestone;
delete from monitor;
delete from news;
delete from player_build;
delete from player_creatures;
delete from player_items;
delete from player_orders;
delete from player_scans;
delete from pulse_use;
delete from scan_results;
delete from shout;

update player set unassigned=300, extractor=0, genetic_lab=0, powerplant=0, factory=0,
	mineral=1000000, organic=1000000, energy=1000000,
	status="inactive";
	
update game set gamename="Revolution v1.4m1<br/>Better Late Than Never", start_time="2008-12-30 16:00:00", current_tick=0, status="Pre-Game",
	starting_mineral=1000000, starting_organic=1000000, starting_energy=1000000, starting_structures=300,
	number_ticks_per_day=244;