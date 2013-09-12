-- phpMyAdmin SQL Dump
-- version 2.9.1.1
-- http://www.phpmyadmin.net
-- 
-- Host: 10.8.11.133
-- Generation Time: Dec 24, 2007 at 07:41 AM
-- Server version: 5.0.45
-- PHP Version: 4.4.4
-- 
-- Database: `dev_rev`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `alliance`
-- 

CREATE TABLE `alliance` (
  `alliance_name` varchar(50) NOT NULL,
  `shorthand` varchar(8) NOT NULL,
  `description` longtext NOT NULL,
  `score` int(20) NOT NULL,
  `members` int(10) NOT NULL,
  `total_structures` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `alliance`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `alliance_application`
-- 

CREATE TABLE `alliance_application` (
  `player_name` varchar(50) NOT NULL,
  `alliance_name` varchar(50) NOT NULL,
  `time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `alliance_application`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `alliance_declarations`
-- 

CREATE TABLE `alliance_declarations` (
  `alliance` varchar(50) NOT NULL,
  `target_alliance` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `until_tick` int(10) NOT NULL,
  `time` datetime NOT NULL,
  `text` longtext NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `alliance_declarations`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `chat`
-- 

CREATE TABLE `chat` (
  `chat_type` varchar(50) NOT NULL,
  `chat_group` varchar(50) NOT NULL,
  `chat_channel` varchar(50) NOT NULL,
  `time_started` datetime NOT NULL,
  PRIMARY KEY  (`chat_type`,`chat_group`,`chat_channel`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `chat`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `chat_last_seen`
-- 

CREATE TABLE `chat_last_seen` (
  `player_name` varchar(50) NOT NULL,
  `last_main` int(10) NOT NULL,
  `last_alliance` int(10) NOT NULL,
  `last_personal` int(10) NOT NULL,
  PRIMARY KEY  (`player_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `chat_last_seen`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `chat_message`
-- 

CREATE TABLE `chat_message` (
  `id` int(10) NOT NULL auto_increment,
  `chat_type` varchar(50) NOT NULL,
  `chat_group` varchar(50) NOT NULL,
  `chat_channel` varchar(50) NOT NULL,
  `player_name` varchar(50) NOT NULL,
  `post_time` datetime NOT NULL,
  `text` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `chat_message`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `chat_player`
-- 

CREATE TABLE `chat_player` (
  `player_name` varchar(50) NOT NULL,
  `last_online` datetime NOT NULL,
  PRIMARY KEY  (`player_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `chat_player`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `conversation_category`
-- 

CREATE TABLE `conversation_category` (
  `conversation_type` varchar(50) NOT NULL,
  `conversation_group` varchar(50) NOT NULL,
  `conversation_category` varchar(50) NOT NULL,
  `creater` varchar(50) NOT NULL,
  PRIMARY KEY  (`conversation_type`,`conversation_group`,`conversation_category`,`creater`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `conversation_category`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `conversation_last_seen`
-- 

CREATE TABLE `conversation_last_seen` (
  `player_name` varchar(50) NOT NULL,
  `topic_id` int(10) NOT NULL,
  `last_message_id` int(10) NOT NULL,
  PRIMARY KEY  (`player_name`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `conversation_last_seen`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `conversation_message`
-- 

CREATE TABLE `conversation_message` (
  `id` int(10) NOT NULL auto_increment,
  `topic_id` int(10) NOT NULL,
  `parent_id` int(10) NOT NULL,
  `author` varchar(50) NOT NULL,
  `post_time` datetime NOT NULL,
  `subject` varchar(250) NOT NULL,
  `message_text` longtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `conversation_message`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `conversation_topic`
-- 

CREATE TABLE `conversation_topic` (
  `id` int(10) NOT NULL auto_increment,
  `conversation_type` varchar(50) NOT NULL,
  `conversation_group` varchar(50) NOT NULL,
  `conversation_category` varchar(50) NOT NULL,
  `creater` varchar(50) NOT NULL,
  `subject` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `conversation_topic`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `creature_items`
-- 

CREATE TABLE `creature_items` (
  `name` varchar(50) NOT NULL,
  `development_item` varchar(50) NOT NULL,
  `mineral` int(10) NOT NULL,
  `organic` int(10) NOT NULL,
  `ticks` int(10) NOT NULL,
  `attack` int(10) NOT NULL,
  `defense` int(10) NOT NULL,
  `intelligence` int(10) NOT NULL,
  `discipline` int(10) NOT NULL,
  `focus` int(10) NOT NULL,
  `weight` varchar(10) NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `creature_items`
-- 

INSERT INTO `creature_items` VALUES ('Imp', 'Imp Technologies', 1000, 1000, 6, 14, 12, 70, 30, 15, '0', 'If its small and breathes fire, its probably an Imp.');
INSERT INTO `creature_items` VALUES ('Wyrm', 'Wyrm Technologies', 5000, 5000, 16, 80, 65, 80, 40, 90, '2', 'A snakelike dragon, this beast can suffocate you or fry you like a crispy muffin');
INSERT INTO `creature_items` VALUES ('Wyvern', 'Wyvern Technologies', 20000, 17500, 20, 275, 200, 80, 40, 420, '3', 'Smaller then a full dragon, but much faster, wyverns are dangerous critters');
INSERT INTO `creature_items` VALUES ('Dragon', 'Dragon Technologies', 60000, 50000, 28, 1000, 900, 75, 25, 950, '5', 'Sometimes the dragon wins');
INSERT INTO `creature_items` VALUES ('Sprite', 'Sprite Technologies', 500, 750, 3, 8, 8, 70, 30, 10, '0', 'Small and fast, these babies are easy to kill, if you can hit them, which you can''t');
INSERT INTO `creature_items` VALUES ('Dryad', 'Dryad Technologies', 2500, 3000, 8, 45, 40, 110, 40, 35, '0', 'Smart and agile, dryads can sneak up on you and stab you while smiling');
INSERT INTO `creature_items` VALUES ('Centaur', 'Centaur Technologies', 14000, 15000, 12, 200, 210, 90, 40, 275, '2', 'Protectors of the land, smart and capable');
INSERT INTO `creature_items` VALUES ('Unicorn', 'Unicorn Technologies', 40000, 45000, 16, 700, 700, 100, 40, 650, '4', 'Smartest beast in the universe, never cross a unicorn, especially not with a horse');
INSERT INTO `creature_items` VALUES ('Ogre', 'Ogre Technologies', 1200, 600, 4, 14, 12, 50, 50, 10, '0', 'Big, mean, and stupid.  What more can you ask for?');
INSERT INTO `creature_items` VALUES ('Troll', 'Troll Technologies', 6000, 3000, 12, 85, 70, 40, 60, 65, '1', 'Bigger, meaner, more stupid.  Trolls are not fun to watch.');
INSERT INTO `creature_items` VALUES ('Giant', 'Giant Technologies', 25000, 12000, 16, 300, 250, 60, 70, 350, '3', 'Biggest humanoid around, these beasts can take a lot and give a lot too.');
INSERT INTO `creature_items` VALUES ('Demon', 'Demon Technologies', 75000, 40000, 24, 1150, 900, 70, 55, 850, '5', 'No bigger then a man, but much much meaner');
INSERT INTO `creature_items` VALUES ('Cheetah', 'Cheetah Technologies', 1000, 500, 4, 12, 7, 70, 30, 10, '0', 'Fastest cat in space. Meeeoooowww. ');
INSERT INTO `creature_items` VALUES ('Panther', 'Panther Technologies', 5000, 2500, 10, 80, 35, 70, 40, 50, '1', 'Try to hunt a panther, or better yet try not to be the one hunted.');
INSERT INTO `creature_items` VALUES ('Tiger', 'Tiger Technologies', 20000, 10000, 14, 260, 150, 60, 50, 275, '1', 'Fierce, large, and agile.  Not a good combination if your its prey.');
INSERT INTO `creature_items` VALUES ('Lion', 'Lion Technologies', 60000, 30000, 20, 1000, 500, 50, 40, 650, '2', 'King of the jungle.  Need I say more?');
INSERT INTO `creature_items` VALUES ('Cyborg', 'Cyborg Technologies', 1200, 500, 6, 15, 10, 50, 20, 9, '0', 'Half man, half machine, stupider then both, but good with weapons.');
INSERT INTO `creature_items` VALUES ('Spider', 'Spider Technologies', 7000, 2500, 15, 110, 55, 60, 10, 60, '2', 'So many legs, so little chance to avoid them.');
INSERT INTO `creature_items` VALUES ('Mantis', 'Mantis Technologies', 28000, 14000, 18, 450, 250, 40, 20, 375, '3', 'Made to look like the insect.  These behemoths can strike fast.');
INSERT INTO `creature_items` VALUES ('Megadon', 'Megadon Technologies', 80000, 35000, 26, 1400, 850, 50, 10, 800, '5', 'A famous saying goes, when you see a Megadon this best course of action is to run away, fast');
INSERT INTO `creature_items` VALUES ('Humvee', 'Humvee Technologies', 1500, 500, 10, 12, 15, 10, 60, 14, '0', 'Part man, part car.  Most of these are named Christine.');
INSERT INTO `creature_items` VALUES ('Tank', 'Tank Technologies', 7500, 2500, 18, 85, 95, 20, 70, 70, '2', 'Part man, part car, all armored.  Good luck.');
INSERT INTO `creature_items` VALUES ('Crusher', 'Crusher Technologies', 32000, 12000, 24, 310, 400, 20, 80, 420, '4', 'As big as a house, your fine as long as you can stay out of its way.');
INSERT INTO `creature_items` VALUES ('Doomcrusher', 'Doomcrusher Technologies', 100000, 40000, 30, 1150, 1600, 10, 80, 1100, '6', 'Much bigger than a house, and there is no way to stay far enough away.');

-- --------------------------------------------------------

-- 
-- Table structure for table `description`
-- 

CREATE TABLE `description` (
  `name` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `author` varchar(50) NOT NULL,
  `text` longtext NOT NULL,
  `last_edited` datetime NOT NULL,
  `ordinality` decimal(3,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `description`
-- 

INSERT INTO `description` VALUES ('Imp Knowledge', 'research', 'color', 'judal', 'Man was never meant to make Imps.  Once we did, did we have to  let them breath fire?', '2007-12-04 23:37:50', 0.00);
INSERT INTO `description` VALUES ('Imp Knowledge', 'research', 'basic', 'judal', 'Imp Knowledge is the first step in the drake path.  It is required for the more advanced drakes.  Additionally, with this Research the Development "Imp Technologies" will be made available.  \r\n\r\n<P> This development will allow you to make Imps.', '2007-12-02 22:17:47', 0.00);
INSERT INTO `description` VALUES ('Basic Genetic Science', 'research', 'color', 'judal', '<p style="text-align: center;"><em>Genetic Science is the foundation <br /> for all things weird.</em></p>', '2007-12-07 23:17:40', 0.00);
INSERT INTO `description` VALUES ('Basic Genetic Science', 'research', 'basic', 'author', 'Genetic Science is a prerequisite for all genetic and hybrid creatures.  It is quick to research and should be done very early in the game.', '2007-12-02 22:35:46', 0.00);
INSERT INTO `description` VALUES ('Basic Cybernetics Science', 'research', 'color', 'judal', '<p style="text-align: center;">If size is power then the extra-small is extra-powerful</p>', '2007-12-08 22:20:43', 0.00);
INSERT INTO `description` VALUES ('Getting Started', 'concept', 'basic', 'judal', 'No Description Available', '2007-12-07 22:46:52', 0.00);
INSERT INTO `description` VALUES ('Hybrid Engineering', 'research', 'color', 'judal', '<p><em>Combine the manipulation of DNA with nanites and you get a powerful weapon, if you can control it<br /><br /></em></p>', '2007-12-05 15:41:43', 0.00);
INSERT INTO `description` VALUES ('Creature Regeneration', 'development', 'color', 'judal', '<p><em>There like Weebles! You knock them down and they get back up!</em></p>', '2007-12-07 20:31:39', 0.00);
INSERT INTO `description` VALUES ('Microwave Blast', 'pulse', 'color', 'judal', '<p><em>Sometimes you cook at home.&nbsp; Sometimes you get to eat out.</em></p>', '2007-12-07 20:55:14', 0.00);
INSERT INTO `description` VALUES ('Fort', 'fort', 'color', 'bob', '<p><em>Forts stop creatures!</em></p>', '2007-12-19 07:04:25', 0.00);
INSERT INTO `description` VALUES ('', '', 'general', '', '<p>blahdy blah</p>', '2007-12-19 12:46:36', 1.00);
INSERT INTO `description` VALUES ('Defense', 'concept', 'basic', 'judal', '<p>Defense will reduce the damage taken in any fight.</p>', '2007-12-07 22:42:45', 0.00);
INSERT INTO `description` VALUES ('Scanning', 'concept', 'basic', 'judal', '<p style="text-align: left;">Scanning comes in three basic categories.</p>\r\n<div style="text-align: left;"><ul><li>Site Surveying</li><li>Remote Scanning</li><li>Monitoring</li></ul></div>\r\n<p style="text-align: left;">Site Surveying is the process of finding new unallocated structures.&nbsp; It is the simplest way to grow.&nbsp; Each Site Survey has a chance to find a new structure.&nbsp; The chance is based on the ratio of the current survey tuners to the number of structures currently owned.</p>\r\n<p style="text-align: left;">Remote Scanning can provide lots of information on the capabilities of other players.&nbsp; See the help section for each type of scan to see how they operate.</p>\r\n<p style="text-align: left;">Monitoring is a new feature (not yet implemented) that allows a player to continuously watch another player for certain actions.&nbsp; This may result in a news report the moment a player acts or it may result in additional information provided on the universe and ranking screens.&nbsp; All monitors have a limited duration, and may need to be "reapplied" to provide continuous coverage.</p>', '2007-12-07 23:09:51', 1.00);
INSERT INTO `description` VALUES ('Allocating', 'concept', 'basic', 'judal', '<p style="text-align: left;">Allocating is the process of changing an unassigned structure into one of the other four types of structures. Allocating cost only mineral.&nbsp; The cost is based on the number of structures currently allocated.&nbsp; The cost is 150* the number of existing structures.&nbsp; The greater the number of structures a player has allocated, the greater the cost of allocating and the more likely that there may be a better way to get new structures.</p>\r\n<p style="text-align: left;">Allocating can be done by going to the "Structures" tab and, if enough mineral and structures are available, simply clicking on one of the the buttons on the left.</p>', '2007-12-07 23:15:59', 2.00);
INSERT INTO `description` VALUES ('Alliances', 'concept', 'basic', 'judal', '<p>Alliances are a key part of the game.&nbsp; An individual can play for a little while without one, but in order to succeed, everyone needs to have the protection that an alliance provides.&nbsp; Additionally there are special forums and ranking associated with alliances.</p>\r\n<p>Frequently an alliance will plan joint attacks where every member (or a decent subset) will launch on the same target or targets at roughly the same time.&nbsp; This will maximize the damage and reduce the risks associated with the attacks.</p>\r\n<p>Many alliances have different focuses and community aspects, some specialize in training newer players, some expect high activity or elite understanfing of the game.&nbsp; Ask around to make sure you choose the best alliance for your playstyle.</p>', '2007-12-08 21:48:38', 3.00);
INSERT INTO `description` VALUES ('Launching Fleets', 'concept', 'basic', 'judal', '<p>Attacks are a key part of the game.&nbsp; Each player is given three fleets to use in upto three seperate simulataneous attacks.&nbsp;</p>\r\n<p>There are many things that must be done before a player can launch their first attack.&nbsp; A player must research, develop, and create at least one creature.&nbsp; A player must also research and develop some form of travel (intercontinental, interplanetary, etc.,).</p>\r\n<p>To attack or defend, a player needs to load a fleet with some creatures, possibly some bombs, and set a destination, set a mission type, and then click "Launch".&nbsp; This will set that fleet on a mission that will take from as few as 4 to as many as 40 or more ticks to complete.</p>', '2007-12-08 22:00:48', 4.00);
INSERT INTO `description` VALUES ('Basic Drake Knowledge', 'research', 'color', 'judal', '<p style="text-align: center;"><em>Dragons can be large, they can be small.&nbsp; But they are never very friendly.<br /></em></p>', '2007-12-08 22:15:41', 1.00);
INSERT INTO `description` VALUES ('faq1', 'faq', 'answer', 'judal', '<p><strong>NOTHING.&nbsp; </strong>This games costs nothing to play.&nbsp; Never has, never will.</p>', '2007-12-08 23:51:52', 1.00);
INSERT INTO `description` VALUES ('faq1', 'faq', 'question', 'judal', '<p>How much does this game cost?</p>', '2007-12-08 23:49:44', 1.00);
INSERT INTO `description` VALUES ('MAIN', 'help', 'main', 'judal', '<p>Revoution is a web-based massively multiplayer strategy game.&nbsp; It is, by design, light on graphics and graphical capabilities.&nbsp; Its strength is in its community.&nbsp; The following help sections will guide a new (or even advanced) player through the basics and advanced concepts of the game.</p>\r\n<p>Topics contain a list of all the Research, Development, and all Items made in the game.&nbsp; Addtionally, there is a section for Game Concepts.&nbsp; That is probably the best place to look first for help.</p>\r\n<p>If you have any questions, you can look in the Frequently Asked Questions Section.&nbsp; Many of the questions people have asked before are answered in that section.&nbsp; If you don''t see the question you are looking for, you can simply make a new question.&nbsp; It will be answered very quickly, usually within a day or two.</p>\r\n<p>New Players Discussion is currently not available, but will be available soon.</p>\r\n<p>If you have any private questions, please feel free to send a note to me, i will try to answer every question asked.</p>\r\n<p>~/judal</p>', '2007-12-08 23:47:47', 1.00);
INSERT INTO `description` VALUES ('faq2', 'faq', 'question', 'judal', '<p>Wait! You are telling me you aren''t going to charge for it?&nbsp; Not even for special content?&nbsp; No donations?&nbsp; Nothing?</p>', '2007-12-08 23:53:18', 2.00);
INSERT INTO `description` VALUES ('faq2', 'faq', 'answer', 'judal', '<p><strong>NOTHING! </strong>This is not designed to be a money maker.&nbsp; I am trying to learn how to make a good viable game.&nbsp; This is my hobby.&nbsp; Why should I charge you for my hobby?</p>', '2007-12-08 23:55:35', 2.00);
INSERT INTO `description` VALUES ('faq3', 'faq', 'question', 'judal', '<p>But doesn''t it cost to run the site?</p>', '2007-12-08 23:56:04', 3.00);
INSERT INTO `description` VALUES ('faq3', 'faq', 'answer', 'judal', '<p>Actually, it is very inexpensive to run this site.&nbsp; I think something less than $100 per year.</p>', '2007-12-08 23:57:05', 3.00);
INSERT INTO `description` VALUES ('faq4', 'faq', 'question', 'judal', '<p>What if I really want to donate?</p>', '2007-12-08 23:58:05', 4.00);
INSERT INTO `description` VALUES ('faq4', 'faq', 'answer', 'judal', '<p>If you really want to donate, then please help improve the site.&nbsp; All the help pages, all the content was (or will be) created by the players.&nbsp; There are plenty of things that need to be done.&nbsp; I could use help with art, with the site layout.&nbsp; If you can program, I would love to expose the output as XML for your program to ingest.&nbsp; You can create new views, new themes, new anything.&nbsp; You can help me test or game balance or provide a background story.&nbsp; Be creative, ask around.</p>\r\n<p>If you really really want to donate cash to this cause, it will be used mostly to pay for the upgrades I cannot do.&nbsp; Artistry and the like.&nbsp; Maybe for some advertisement.&nbsp; I am sure I can find something useful for it.&nbsp; Just ask.</p>\r\n<p>~/judal</p>', '2007-12-09 00:04:43', 4.00);
INSERT INTO `description` VALUES ('Fast Blasts', 'development', 'color', 'janos', '<p style="text-align: center;"><em>Push once to defrost, twice to cook, and three or more times to burn</em></p>', '2007-12-09 22:24:29', 1.00);
INSERT INTO `description` VALUES ('Fast Blasts', 'development', 'basic', 'janos', '<p>Fast Blasts reduces the time before the player can launch another blast, pulse, shield or jammer&nbsp; from six ticks to one.&nbsp; This will significantly improve the effectiveness of blasts.</p>', '2007-12-09 22:27:17', 1.00);
INSERT INTO `description` VALUES ('Advanced Pulses', 'development', 'color', 'janos', '<p style="text-align: center;"><em>Power Fry, nothing can be tastier.</em></p>', '2007-12-09 22:29:31', 2.00);
INSERT INTO `description` VALUES ('Advanced Pulses', 'development', 'basic', 'janos', '<p>Advanced Pulses will triple the effective number of reflectors and modulators a player has.&nbsp; This will result in the player causing higher damages from pulses, blasts, and shields.&nbsp; It will also reduce the damage to the players creatures from pulses, blasts and shields.</p>\r\n<p>Additionally, Advanced Pulses will substitute as the required skill for modulators and reflectors freeing the player to use build other developments.</p>', '2007-12-09 22:33:37', 2.00);
INSERT INTO `description` VALUES ('Advanced Scans', 'development', 'basic', 'janos', '<p>Advanced Scans will triple the effective number of Scan Amplifiers and Survey Tuners a player has.&nbsp; This will result in the player having a greater success rate in Site Scans as well as all Remote and Detailed Scans.<br /><br />Additionally, Advanced Scans will substitute as the required skill for all of the following:</p><ul><li>Survey Tuners<br /></li><li>Scan Amplifiers</li><li>Site Scans</li><li>Research and Development Scans</li><li>Continent Scans</li><li>Creature Scans</li><li>Military Scans</li><li>Planetary Scans</li><li>News Scans</li><li>Full Scans</li></ul>\r\n<p>This will free up many of the basic developments in the energy tree for other technologies.</p>', '2007-12-09 22:38:53', 3.00);
INSERT INTO `description` VALUES ('Advanced Scans', 'development', 'color', 'janos', '<p style="text-align: center;"><em>Seeing everything is the first step in owning everything<br /></em></p>', '2007-12-09 22:39:37', 3.00);
INSERT INTO `description` VALUES ('Advanced Signals', 'development', 'color', 'janos', '<p style="text-align: center;"><em>See no evil, hear no evil, get destroyed by evil</em></p>', '2007-12-09 22:41:03', 4.00);
INSERT INTO `description` VALUES ('Advanced Signals', 'development', 'basic', 'janos', '<p>Advanced Signals will triple the effective number of Noise Generators, Scan Sensors and Scan Filters that a player has.&nbsp; This will results in an greater failure rate for people scanning the player.&nbsp; It will also result in the player having a higher rate of detecting incoming scans as well as reducing the liklihood that the players scans will be detected.</p>\r\n<p>Additionally, Advanced Signals will substitue as the required skil for all of the following:</p><ul><li>Noice Generators</li><li>Scan Sensors</li><li>Scan Filters</li></ul>\r\n<p>This will free up many of the basic developments in the energy tree for other technologies.</p>', '2007-12-09 22:46:41', 4.00);
INSERT INTO `description` VALUES ('Universe Monitors', 'development', 'color', 'janos', '<p style="text-align: center;"><em>Look around you.&nbsp; If you can see in all directions you always know where you are going<br /></em></p>', '2007-12-09 22:47:34', 5.00);
INSERT INTO `description` VALUES ('Universe Monitors', 'development', 'basic', 'judal', '<p>Universe Monitors permanently replaces the structures column in the Player Ranking and Univese pages with the breakdown by type of each structure.</p>\r\n<p>It takes this:</p>\r\n<table class="STD " style="width:100%" border="0">\r\n<tbody>\r\n<tr>\r\n<td class="SIDEBAR" style="background-color: #008000;" onclick="location.href=''main_page.php5?view=rankings&amp;subview=player&amp;order=location''" onmouseover="this.style.backgroundColor=''40B040''" onmouseout="this.style.backgroundColor=''008000''">Location</td>\r\n<td class="SIDEBAR" style="background-color: #008000;" onclick="location.href=''main_page.php5?view=rankings&amp;subview=player&amp;order=alliance''" onmouseover="this.style.backgroundColor=''40B040''" onmouseout="this.style.backgroundColor=''008000''">Alliance</td>\r\n<td class="SIDEBAR" style="background-color: #008000;" onclick="location.href=''main_page.php5?view=rankings&amp;subview=player&amp;order=player''" onmouseover="this.style.backgroundColor=''40B040''" onmouseout="this.style.backgroundColor=''008000''">Player</td>\r\n<td class="SIDEBAR" style="background-color: #008000;" onclick="location.href=''main_page.php5?view=rankings&amp;subview=player&amp;order=structures''" onmouseover="this.style.backgroundColor=''40B040''" onmouseout="this.style.backgroundColor=''008000''">Structures</td>\r\n<td class="SIDEBAR" style="background-color: #008000;" onclick="location.href=''main_page.php5?view=rankings&amp;subview=player&amp;order=score''" onmouseover="this.style.backgroundColor=''40B040''" onmouseout="this.style.backgroundColor=''008000''">Score</td>\r\n<td class="SIDEBAR" style="background-color: #008000;" onclick="location.href=''main_page.php5?view=rankings&amp;subview=player&amp;order=last_online''" onmouseover="this.style.backgroundColor=''40B040''" onmouseout="this.style.backgroundColor=''008000''">Last Online</td>\r\n</tr>\r\n<tr>\r\n<td class="STD">2:5:1:1</td>\r\n<td class="STD">&nbsp;</td>\r\n<td class="STD">judal of the sky</td>\r\n<td class="STD">100</td>\r\n<td class="STD">10000000</td>\r\n<td class="STD">2007-12-01 00:00:00</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p>and replaces it with this:</p>\r\n<table class="STD " style="width:100%" border="0">\r\n<tbody>\r\n<tr>\r\n<td class="SIDEBAR" style="background-color: #008000;" onclick="location.href=''main_page.php5?view=rankings&amp;subview=player&amp;order=location''" onmouseover="this.style.backgroundColor=''40B040''" onmouseout="this.style.backgroundColor=''008000''">Location</td>\r\n<td class="SIDEBAR" style="background-color: #008000;" onclick="location.href=''main_page.php5?view=rankings&amp;subview=player&amp;order=alliance''" onmouseover="this.style.backgroundColor=''40B040''" onmouseout="this.style.backgroundColor=''008000''">Alliance</td>\r\n<td class="SIDEBAR" style="background-color: #008000;" onclick="location.href=''main_page.php5?view=rankings&amp;subview=player&amp;order=player''" onmouseover="this.style.backgroundColor=''40B040''" onmouseout="this.style.backgroundColor=''008000''">Player</td>\r\n<td class="SIDEBAR" style="background-color: #008000;" onclick="location.href=''main_page.php5?view=rankings&amp;subview=player&amp;order=structures''" onmouseover="this.style.backgroundColor=''40B040''" onmouseout="this.style.backgroundColor=''008000''">Structures</td>\r\n<td class="SIDEBAR" style="background-color: #008000;" onclick="location.href=''main_page.php5?view=rankings&amp;subview=player&amp;order=score''" onmouseover="this.style.backgroundColor=''40B040''" onmouseout="this.style.backgroundColor=''008000''">Score</td>\r\n<td class="SIDEBAR" style="background-color: #008000;" onclick="location.href=''main_page.php5?view=rankings&amp;subview=player&amp;order=last_online''" onmouseover="this.style.backgroundColor=''40B040''" onmouseout="this.style.backgroundColor=''008000''">Last Online</td>\r\n</tr>\r\n<tr>\r\n<td class="STD">2:5:1:1</td>\r\n<td class="STD">&nbsp;</td>\r\n<td class="STD">judal of the sky</td>\r\n<td class="STD">25u/0e/0g/0p/75f</td>\r\n<td class="STD">10000000</td>\r\n<td class="STD">2007-12-01 00:00:00</td>\r\n</tr>\r\n</tbody>\r\n</table>', '2007-12-09 22:54:47', 5.00);
INSERT INTO `description` VALUES ('Advanced Fort Survivability', 'development', 'color', 'judal', '<p style="text-align: center;"><em>Once you build up enough metal around your forts theres pretty much nothing any creature can do to harm it</em></p>', '2007-12-11 22:10:00', 6.00);
INSERT INTO `description` VALUES ('Advanced Fort Survivability', 'development', 'basic', 'judal', '<p>This tech completes the final 25% survivability.&nbsp; With all the survivability techs, Forts cannot be harmed by creatures in combat.</p>\r\n<p>Bombs will still work at full effect though.</p>', '2007-12-11 22:11:56', 6.00);
INSERT INTO `description` VALUES ('Advanced Bombs', 'development', 'color', 'judal', '<p style="text-align: center;"><em>BIG BOOM!</em></p>', '2007-12-11 22:12:44', 7.00);
INSERT INTO `description` VALUES ('Bomb', 'bomb', 'color', 'judal', '<p style="text-align: center;"><em>Bombs blow up forts.&nbsp; More bombs, more destroyed forts<br /></em></p>', '2007-12-21 09:10:31', 8.00);
INSERT INTO `description` VALUES ('Advanced Bombs', 'development', 'basic', 'judal', '<p>Normally one bomb will destroy one fort.&nbsp; With this technology one bomb will destroy two forts.</p>', '2007-12-11 22:14:13', 7.00);
INSERT INTO `description` VALUES ('Advanced Traps', 'development', 'color', 'judal', '<p style="text-align: center;"><em>Along comes Mr. Crocodile quiet as can be ...&nbsp; Swish, swish, swish, swish ...</em></p>\r\n<p style="text-align: center;"><strong>SNAP!</strong></p>', '2007-12-11 22:16:41', 9.00);
INSERT INTO `description` VALUES ('Advanced Traps', 'development', 'basic', 'judal', '<p>Doubles the effectiveness of traps.&nbsp; One trap gets two chances to kill upto two creatures.</p>', '2007-12-11 22:17:25', 8.00);
INSERT INTO `description` VALUES ('Power Engines', 'development', 'color', 'judal', '<p style="text-align: center;"><em>Ever put a jet engine on the back of a chevy.&nbsp; Imagine that, but in space.</em></p>', '2007-12-11 22:18:56', 9.99);
INSERT INTO `description` VALUES ('Power Engines', 'development', 'basic', 'judal', '<p>Power engines will reduce the time it takes to travel to and from a target by 5 ticks.</p>', '2007-12-11 22:19:59', 9.00);
INSERT INTO `description` VALUES ('Advanced Antigravity', 'development', 'color', 'judal', '<p style="text-align: center;"><em>Lighter than a feather.&nbsp; Lighter than air.&nbsp; Lighter than nothing.&nbsp; And nothing is lighter the this.</em></p>', '2007-12-11 22:21:50', 9.99);
INSERT INTO `description` VALUES ('Advanced Antigravity', 'development', 'basic', 'judal', '<p>Advanced Antigravity will reduce the weight of all creatures by 4.</p>\r\n<p><em>Note: Any creature with a weight of 4 or less will be effectively a weight 0 creature for computing of all travel times.</em></p>', '2007-12-11 22:24:21', 9.99);
INSERT INTO `description` VALUES ('Improved Antigravity', 'development', 'color', 'judal', '<p style="text-align: center;"><em>Its like going on a really really good diet.</em></p>', '2007-12-11 22:25:41', 9.99);
INSERT INTO `description` VALUES ('Antigravity', 'development', 'color', 'judal', '<p style="text-align: center;"><em>Its like going on a really good diet.</em></p>', '2007-12-11 22:26:08', 9.99);
INSERT INTO `description` VALUES ('Antigravity', 'development', 'basic', 'judal', '<p>Antigravity will reduce the weight and consequently the travel time of all creatures by 1.</p>\r\n<p><em>Note: Any creature with a weight of 0 will be unaffected by this technology<br /></em></p>', '2007-12-11 22:28:01', 9.99);
INSERT INTO `description` VALUES ('Improved Antigravity', 'development', 'basic', 'judal', '<p>Antigravity will reduce the weight and consequently the travel time of all creatures by 2.</p>\r\n<p><em>Note: Any creature with a weight of 0 will be unaffected by this technology, any creature of weight 1 or weight 2 will not add any travel time due to its weight.<br /></em></p>', '2007-12-11 22:29:27', 9.99);
INSERT INTO `description` VALUES ('Ion Drives', 'development', 'color', 'judal', '<p style="text-align: center;"><em>Looks like blue lights on the back of a ship.</em></p>', '2007-12-11 22:32:07', 9.99);
INSERT INTO `description` VALUES ('Ion Drives', 'development', 'basic', 'judal', '<p>Ion drives will reduce the time it takes to travel to and from a target by 1 tick each way.</p>', '2007-12-11 22:34:59', 9.99);
INSERT INTO `description` VALUES ('Nuclear Drives', 'development', 'basic', 'judal', '<p>Nuclear drives will reduce the time it takes to travel to and from a target by 2 ticks each way.</p>', '2007-12-11 22:36:18', 9.99);
INSERT INTO `description` VALUES ('Fusion Drives', 'development', 'basic', 'judal', '<p>Fusion drives will reduce the time it takes to travel to and from a target by 3 ticks each way.</p>', '2007-12-11 22:36:50', 9.99);
INSERT INTO `description` VALUES ('Quantum Drives', 'development', 'basic', 'judal', '<p>Quantum drives will reduce the time it takes to travel to and from a target by 4 ticks each way.</p>', '2007-12-11 22:37:24', 9.99);
INSERT INTO `description` VALUES ('Quantum Drives', 'development', 'color', 'judal', '<p style="text-align: center;"><em>Harness the small and go realy fast</em></p>', '2007-12-11 22:38:27', 9.99);
INSERT INTO `description` VALUES ('Fusion Drives', 'development', 'color', 'judal', '<p style="text-align: center;"><em>And you thought Fusion was only good for bombs.</em></p>', '2007-12-11 22:39:17', 9.99);
INSERT INTO `description` VALUES ('Nuclear Drives', 'development', 'color', 'judal', '<p style="text-align: center;"><em>Nuclear Energy harnessed they right way can do wonders</em></p>', '2007-12-11 22:41:17', 9.99);
INSERT INTO `description` VALUES ('Launch Times', 'concept', 'basic', 'judal', '<p>The base launch time is related to the distance of travel.&nbsp; The times are as follows:</p><ul><li>Intercontinental Travel - 8 ticks to target</li><li>Interplanetary Travel - 10 ticks to target</li><li>Interstellar Travel - 12 ticks to target</li><li>Integalactic Travel - 18 ticks to target</li></ul>\r\n<p>This time can be adjusted based on a few factors including the type of crive developed, the weight of the heaviest creature in the fleet, and the antigravity advancements.</p>\r\n<p>Drives will reduce the times as follows:</p><ul><li>Ion Drives will reduce the travel time by 1 tick</li><li>Nuclear Drives will reduce the travel time by 2 ticks</li><li>Fusion Drives will reduce the travel time by 3 ticks</li><li>Quantum Drives will reduce the time by 4 ticks</li><li>Power Engines will redcue the time by 5 ticks</li></ul>\r\n<p>Addtionally, each creature has a weight.&nbsp; This weight will add ticks to the trave time equal to the weight.&nbsp; (e.g., A Dragon is a 5 weight creature so if there are any dragons in a fleet, then the fleet will be at least 5 ticks slower.)</p>\r\n<p>The are some technologies that reduce the effective weight of all creatures.&nbsp; These are:</p><ul><li>Antigravity will reduce the weight of all creatures by 1</li><li>Improved Antigravity will reduce the weight of all creatures by 2</li><li>Advanced Antigravity will reduce the weight of all creatures by 4</li></ul>\r\n<p>In the fleets view, it will show the travel time for each fleet based on the technologies currently known and the creatures in that fleet.</p>', '2007-12-11 22:58:27', 5.00);
INSERT INTO `description` VALUES ('Combined Attack', 'development', 'color', 'janos', '<p style="text-align: center;"><em>Hey guys, follow me!</em></p>', '2007-12-13 19:54:44', 9.99);
INSERT INTO `description` VALUES ('Combined Attack', 'development', 'basic', 'janos', '<p>If a player has Combined Attack, every creature on the same side of every battle that the player is involved will get a 20% boost in attack power.&nbsp;</p>\r\n<p>The is <strong>not</strong> cumulative.&nbsp; In any combined attack or defense it only requires a single player to have this skill for all players to benefit.&nbsp; Additional players with the skill add nothing.</p>', '2007-12-13 19:58:33', 9.99);
INSERT INTO `description` VALUES ('Combined Defense', 'development', 'basic', 'janos', '<p>If a player has Combined Defense, every creature on the same side of\r\nevery battle that the player is involved will get a 20% boost in defense power.&nbsp;</p>\r\n<p>The is <strong>not</strong> cumulative.&nbsp; In any combined attack\r\nor defense it only requires a single player to have this skill for all\r\nplayers to benefit.&nbsp; Additional players with the skill add nothing.</p>', '2007-12-13 19:59:08', 9.99);
INSERT INTO `description` VALUES ('Combined Focus', 'development', 'basic', 'janos', '<p>If a player has Combined Focus, every creature on the same side of\r\nevery battle that the player is involved will get a 20% boost in focus\r\npower.&nbsp;</p>\r\n<p>The is <strong>not</strong> cumulative.&nbsp; In any combined attack\r\nor defense it only requires a single player to have this skill for all\r\nplayers to benefit.&nbsp; Additional players with the skill add nothing.</p>', '2007-12-13 19:59:34', 9.99);
INSERT INTO `description` VALUES ('Combined Discipline', 'development', 'basic', 'janos', '<p>If a player has Combined Discipline, every creature on the same side of\r\nevery battle that the player is involved will get a 20% boost in discipline.&nbsp;</p>\r\n<p>The is <strong>not</strong> cumulative.&nbsp; In any combined attack\r\nor defense it only requires a single player to have this skill for all\r\nplayers to benefit.&nbsp; Additional players with the skill add nothing.</p>', '2007-12-13 20:01:03', 9.99);
INSERT INTO `description` VALUES ('Combined Intelligence', 'development', 'basic', 'janos', '<p>If a player has Combined Intelligence, every creature on the same side of\r\nevery battle that the player is involved will get a 20% boost in intelligence.&nbsp;</p>\r\n<p>The is <strong>not</strong> cumulative.&nbsp; In any combined attack\r\nor defense it only requires a single player to have this skill for all\r\nplayers to benefit.&nbsp; Additional players with the skill add nothing.</p>', '2007-12-13 20:00:40', 9.99);
INSERT INTO `description` VALUES ('Combined Defense', 'development', 'color', 'janos', '<p style="text-align: center;"><em>Everyone, lock your shields in place to make a single wall</em><br /></p>', '2007-12-13 20:01:53', 9.99);
INSERT INTO `description` VALUES ('Combined Focus', 'development', 'color', 'janos', '<p style="text-align: center;"><em>If we all focus on one task we can get it done better and faster</em></p>', '2007-12-13 20:02:24', 9.99);
INSERT INTO `description` VALUES ('Combined Discipline', 'development', 'color', 'janos', '<p style="text-align: center;"><em>We are only as strong as our weakest link, if no one is weak, then we are strong</em></p>', '2007-12-13 20:03:17', 9.99);
INSERT INTO `description` VALUES ('Combined Intelligence', 'development', 'color', 'janos', '<p style="text-align: center;"><em>Lets all puts our minds together, maybe we ccan figure a way out of this mess</em></p>', '2007-12-13 20:03:48', 9.99);
INSERT INTO `description` VALUES ('Creature Regeneration', 'development', 'basic', 'janos', '<p>Creature regeneration will instantly add new creatures at home for each creature destroyed in a battle.&nbsp; This will not affect any creature captured in battle or any creature killed in any way other than in a battle (e.g., traps, pulses, shields, blasts)</p>', '2007-12-13 21:22:55', 9.99);
INSERT INTO `description` VALUES ('Creature Rescue', 'development', 'color', 'janos', '<p style="text-align: center;"><em>Never leave a friend behind</em></p>', '2007-12-13 21:23:31', 9.99);
INSERT INTO `description` VALUES ('Creature Rescue', 'development', 'basic', 'janos', '<p>In any battle, all of your creatures will be immune to captures.&nbsp; Captures can still occur from psychological traps, however.</p>', '2007-12-13 21:25:13', 9.99);
INSERT INTO `description` VALUES ('Creature Capture', 'development', 'color', 'janos', '<p style="text-align: center;"><em><strong>Zap! </strong>You have been Tazed</em></p>', '2007-12-13 21:26:03', 9.99);
INSERT INTO `description` VALUES ('Creature Capture', 'development', 'basic', 'janos', '<p>Instead of dying, all enemy creatures will be captured by some force on your side of a battle.</p>\r\n<p>Note: If the enemy has Creature Rescue, his forces will not be immune to losses.&nbsp; He will rescue his captured creatures, then you will capture those that should have been killed.</p>', '2007-12-13 21:30:20', 9.99);
INSERT INTO `description` VALUES ('Pulse Immunity', 'development', 'color', 'janos', '<p style="text-align: center;"><em>ZAP! Oooh! That tickles.</em></p>', '2007-12-14 22:12:50', 9.99);
INSERT INTO `description` VALUES ('Pulse Immunity', 'development', 'basic', 'janos', '<p>Your fleets and creatures are completely immune to all pulses, blasts and shield effects.</p>', '2007-12-14 22:13:34', 9.99);
INSERT INTO `description` VALUES ('Continuous Blast', 'development', 'color', 'janos', '<p style="text-align: center;"><em><strong>ZAP! ZAP! ZAP! </strong>This is fun!</em></p>', '2007-12-14 22:14:16', 9.99);
INSERT INTO `description` VALUES ('Continuous Blast', 'development', 'basic', 'janos', '<p>Every blast used is immediately replaced at no cost.</p>', '2007-12-14 22:15:01', 9.99);
INSERT INTO `description` VALUES ('Energy Conservation', 'development', 'color', 'janos', '<p style="text-align: center;"><em>So you finally solved that energy crisis</em></p>', '2007-12-14 22:15:47', 9.99);
INSERT INTO `description` VALUES ('Energy Conservation', 'development', 'basic', 'janos', '<p>Launches do not cost any energy.&nbsp; Scans do not cost any energy.&nbsp; Pulses, Blasts, Shields do not cost any energy.&nbsp; Nothing costs energy.</p>', '2007-12-14 22:16:46', 9.99);
INSERT INTO `description` VALUES ('Continuous Surveying', 'development', 'color', 'janos', '<p style="text-align: center;"><em>Over here, we can use this too.</em></p>', '2007-12-14 22:17:27', 9.99);
INSERT INTO `description` VALUES ('Continuous Surveying', 'development', 'basic', 'janos', '<p>Every single tick player will gain a single structure of a random type at no cost:</p><ul><li>20% chance for an unassigned structure</li><li>20% chance for an extractor</li><li>20% chance for a genetic_lab</li><li>20% chance for a powerplant</li><li>20% chance for a factoy</li></ul>\r\n<p>&nbsp;</p>', '2007-12-14 22:19:42', 9.99);
INSERT INTO `description` VALUES ('Teleportation', 'development', 'color', 'janos', '<p style="text-align: center;"><em>There''s no place like home<br /></em><em>There''s no place like home<br />There''s no place like home</em></p>', '2007-12-14 22:21:35', 9.99);
INSERT INTO `description` VALUES ('Teleportation', 'development', 'basic', 'janos', '<p>With Teleportation all return trips will take a single tick.&nbsp; This will greatly increase the attack rate of your fleets.</p>\r\n<p>Additionally, any recall will return home in 1 tick (Unless it just launched, then it returns instantly.)</p>', '2007-12-14 22:34:15', 9.99);
INSERT INTO `description` VALUES ('Total Fort Protection', 'development', 'color', 'janos', '<p style="text-align: center;"><em>This is the immovable object, but where is that unstoppable force?</em></p>', '2007-12-14 22:35:32', 9.99);
INSERT INTO `description` VALUES ('Total Fort Protection', 'development', 'basic', 'janos', '<p>With Total Fort Protection, Forts cannot be destroyed in any way.&nbsp; Your forts will be immune to bombs and creature damage of any type.&nbsp; (No need for any survivability technology either.)</p>', '2007-12-14 22:37:03', 9.99);
INSERT INTO `description` VALUES ('Example Alliance', 'alliance', 'description', 'janos', '<p><img title="Laughing" src="../tinymce/jscripts/tiny_mce/plugins/emotions/img/smiley-laughing.gif" border="0" alt="Laughing" /> An example of what alliance descriptions can do. I wonder about anything else???</p>\r\n<p><strong>BOLD</strong></p>\r\n<p>MAybe <span style="font-size: large;">BIGGER Fonts</span></p>', '2007-12-16 20:53:53', 1.00);
INSERT INTO `description` VALUES ('Vacation Mode', 'description', 'page', 'judal', '<p>Vacation Mode is a way for players to avoid losing their fleets when they are away from the game for an extended period of time.</p>\r\n<p>Any player can go into vacation mode at any time.&nbsp; Once in vacation mode, the player must stay in for a minimum of 72 ticks.&nbsp; A player may stay in vacation mode indefinitely.</p>\r\n<p>When in vacation mode the player gains no resources and cannot launch any fleet or use any pulse, blast, shield or jammer.&nbsp; In return, no player can launch or use any blast against the player.&nbsp; Fleets already launched will continue on the thier destination and do their normal damage.</p>', '2007-12-17 10:46:05', 1.00);
INSERT INTO `description` VALUES ('Organic Victory Condition', 'development', 'color', 'janos', '<p style="text-align: center;"><em>The key to life is in its genetic code.&nbsp; Own the code, and you own all life</em></p>', '2007-12-17 20:32:32', 9.99);
INSERT INTO `description` VALUES ('Organic Victory Condition', 'development', 'basic', 'janos', '<p>To satisfy the Organic Victory condition a player must achieve two goals</p><ul><li>They must complete the development of this master technology</li><li>They must obtain and hold onto at least 1000 genetic_labs</li></ul>\r\n<p>When that player has both of those conditions, that player''s score will instantly\r\ndouble.&nbsp; If the player loses either, then the condition will be void\r\nuntil that player regains the requirements.</p>\r\n<p>Further, when any single alliance has more members with each of\r\nthe three types of victory conditions, the round will end, and that\r\nalliance will win the round.&nbsp; Alternately, if any single player can\r\nmanage to obtain two full victory conditions, then the round will end\r\nand they will be declare the winner.</p>', '2007-12-17 20:39:11', 9.99);
INSERT INTO `description` VALUES ('Energy Victory Condition', 'development', 'basic', 'janos', '<p>To satisfy the Energy Victory condition a player must achieve two goals</p> <ul><li>They must complete the development of this master technology</li><li>They must obtain and hold onto at least 1000 powerplants</li></ul>\r\n<p>When that player has both of those conditions, that player''s score will instantly\r\ndouble.&nbsp; If the player loses either, then the condition will be void\r\nuntil that player regains the requirements.</p>\r\n<p>Further, when any single alliance has more members with each of\r\nthe three types of victory conditions, the round will end, and that\r\nalliance will win the round.&nbsp; Alternately, if any single player can\r\nmanage to obtain two full victory conditions, then the round will end\r\nand they will be declare the winner.</p>', '2007-12-17 20:39:41', 9.99);
INSERT INTO `description` VALUES ('Mineral Victory Condition', 'development', 'basic', 'janos', '<p>To satisfy the Mineral Victory condition a player must achieve two goals</p> <ul><li>They must complete the development of this master technology</li><li>They must obtain and hold onto at least 1000 extractors</li></ul>\r\n<p>When that player has both of those conditions, that playe''s score will instantly\r\ndouble.&nbsp; If the player loses either, then the condition will be void\r\nuntil that player regains the requirements.</p>\r\n<p>Further, when any single alliance has more members with each of\r\nthe three types of victory conditions, the round will end, and that\r\nalliance will win the round.&nbsp; Alternately, if any single player can\r\nmanage to obtain two full victory conditions, then the round will end\r\nand they will be declare the winner.</p>', '2007-12-17 20:40:05', 9.99);
INSERT INTO `description` VALUES ('Mineral Victory Condition', 'development', 'color', 'janos', '<p style="text-align: center;"><em>Total manipulation of matter has been the goal of mankind since history began<br /></em></p>', '2007-12-17 20:41:58', 9.99);
INSERT INTO `description` VALUES ('Energy Victory Condition', 'development', 'color', 'janos', '<p style="text-align: center;"><em>Control energy and you control everything</em></p>', '2007-12-17 20:43:03', 9.99);
INSERT INTO `description` VALUES ('Mantis Knowledge', 'research', 'color', 'janos', '<p style="text-align: center;"><em>30 feet tall this is a Preying Mantis, very different from the one inch tall Praying Mantis</em></p>', '2007-12-18 14:23:08', 2.00);
INSERT INTO `description` VALUES ('Bomb', 'bomb', 'basic', 'judal', '<p>On the tick when an attacking fleet arrives at the target, it will drop all its bombs.&nbsp; Each bomb will destroy a single fort.&nbsp; If all the forts are destroyed, exatra bombs will be wasted.</p>\r\n<p>Forts belonging to players with Total Fort Protection will be completely immune to destruction by bombs.</p>', '2007-12-21 09:13:54', 1.00);
INSERT INTO `description` VALUES ('motd', 'overview', 'overview', 'janos', '<p>Have a great round</p>', '2007-12-23 10:11:28', 1.00);

-- --------------------------------------------------------

-- 
-- Table structure for table `development_items`
-- 

CREATE TABLE `development_items` (
  `name` varchar(50) NOT NULL,
  `dependent_research` varchar(50) NOT NULL,
  `ticks` int(10) NOT NULL,
  `type` varchar(50) NOT NULL,
  `proficiency` varchar(50) NOT NULL,
  `description` mediumtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `development_items`
-- 

INSERT INTO `development_items` VALUES ('Creature Regeneration', 'Creature Mastery', 150, 'creature', 'master', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Creature Rescue', 'Creature Mastery', 300, 'creature', 'master', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Creature Capture', 'Creature Mastery', 250, 'creature', 'master', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Organic Victory Condition', 'Creature Mastery', 500, 'creature', 'master', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Combined Intelligence', 'Genetic Expertise', 100, 'creature', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Combined Discipline', 'Genetic Expertise', 75, 'creature', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Combined Focus', 'Hybrid Expertise', 150, 'creature', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Combined Defense', 'Cybernetic Expertise', 125, 'creature', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Combined Attack', 'Cybernetic Expertise', 125, 'creature', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Imp Technologies', 'Imp Knowledge', 12, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Sprite Technologies', 'Sprite Knowledge', 8, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Ogre Technologies', 'Ogre Knowledge', 18, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Cheetah Technologies', 'Cheetah Knowledge', 14, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Cyborg Technologies', 'Cyborg Knowledge', 16, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Humvee Technologies', 'Humvee Knowledge', 24, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Wyrm Technologies', 'Wyrm Knowledge', 24, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Dryad Technologies', 'Dryad Knowledge', 16, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Troll Technologies', 'Troll Knowledge', 32, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Panther Technologies', 'Panther Knowledge', 26, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Spider Technologies', 'Spider Knowledge', 30, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Tank Technologies', 'Tank Knowledge', 48, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Wyvern Technologies', 'Wyvern Knowledge', 54, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Centaur Technologies', 'Centaur Knowledge', 36, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Giant Technologies', 'Giant Knowledge', 50, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Tiger Technologies', 'Tiger Knowledge', 48, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Mantis Technologies', 'Mantis Knowledge', 56, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Crusher Technologies', 'Crusher Knowledge', 84, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Dragon Technologies', 'Dragon Knowledge', 84, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Unicorn Technologies', 'Unicorn Knowledge', 72, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Demon Technologies', 'Demon Knowledge', 108, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Lion Technologies', 'Lion Knowledge', 88, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Megadon Technologies', 'Megadon Knowledge', 96, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Doomcrusher Technologies', 'Doomcrusher Knowledge', 132, 'creature', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Pulse Immunity', 'Energy Mastery', 250, 'energy', 'master', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Continuous Blast', 'Energy Mastery', 225, 'energy', 'master', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Energy Conservation', 'Energy Mastery', 200, 'energy', 'master', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Energy Victory Condition', 'Energy Mastery', 500, 'energy', 'master', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Advanced Scans', 'Scan Expertise', 100, 'energy', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Advanced Signals', 'Signal Expertise', 100, 'energy', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Universe Monitors', 'Scan Expertise', 150, 'energy', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Advanced Pulses', 'Pulse Expertise', 150, 'energy', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Fast Blasts', 'Pulse Expertise', 125, 'energy', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Full Scan', 'Detailed Scanning', 100, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('News Scan', 'Detailed Scanning', 84, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Military Scan', 'Detailed Scanning', 72, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Planetary Scan', 'Detailed Scanning', 66, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Creature Scan', 'Remote Scanning', 48, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Continent Scan', 'Remote Scanning', 24, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('R and D Scan', 'Remote Scanning', 18, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Surveying', 'Site Scanning', 4, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Site Scan', 'Site Scanning', 6, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Scan Amplification', 'Remote Scanning', 6, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Scan Sensing', 'Energy Detection', 12, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Noise Generation', 'Noise Generation', 12, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Scan Filtering', 'Energy Filtration', 18, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Command Jammer', 'Signal Jamming', 18, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Monitoring', 'Monitoring', 24, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Modulator', 'Pulse Modulation', 12, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Electromagnetic Pulse', 'Electromagnetic Pulse', 36, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Electromagnetic Shield', 'Electromagnetic Shield', 96, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Electromagnetic Blast', 'Electromagnetic Blast', 150, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Reflector', 'Pulse Reflection', 16, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Microwave Pulse', 'Microwave Pulse', 48, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Microwave Shield', 'Microwave Shield', 108, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Microwave Blast', 'Microwave Blast', 180, 'energy', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Continuous Surveying', 'Materials Mastery', 250, 'materials', 'master', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Total Fort Protection', 'Materials Mastery', 225, 'materials', 'master', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Teleportation', 'Materials Mastery', 150, 'materials', 'master', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Mineral Victory Condition', 'Materials Mastery', 500, 'materials', 'master', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Advanced Antigravity', 'Vehicle Expertise', 150, 'materials', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Power Engines', 'Propulsion Expertise', 150, 'materials', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Advanced Traps', 'Special Effects Expertise', 250, 'materials', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Advanced Bombs', 'Special Effects Expertise', 300, 'materials', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Advanced Fort Survivability', 'Special Effects Expertise', 300, 'materials', 'expert', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Antigravity', 'Interplanetary Travel', 36, 'materials', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Improved Antigravity', 'Intergalactic Travel', 84, 'materials', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Intergalactic Vehicles', 'Intergalactic Travel', 72, 'materials', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Insterstellar Vehicles', 'Interstellar Travel', 48, 'materials', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Interplanetary Vehicles', 'Interplanetary Travel', 24, 'materials', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Intercontinental Vehicles', 'Intercontinental Travel', 12, 'materials', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Quantum Drives', 'Quantum Propulsion', 96, 'materials', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Fusion Drives', 'Fusion Propulsion', 72, 'materials', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Nuclear Drives', 'Nuclear Propulsion', 54, 'materials', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Ion Drives', 'Ion Propulsion', 24, 'materials', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Fort', 'Fortification', 24, 'materials', 'basic', 'Basic Fort');
INSERT INTO `development_items` VALUES ('Basic Fort Survivability', 'Fortification', 24, 'materials', 'basic', 'Reduces fort losses due to battles');
INSERT INTO `development_items` VALUES ('Improved Fort Attack', 'Improved Fortification', 96, 'materials', 'basic', 'Increases the attack value of a fort');
INSERT INTO `development_items` VALUES ('Improved Fort Defense', 'Improved Fortification', 96, 'materials', 'basic', 'Increases the defense value of a fort');
INSERT INTO `development_items` VALUES ('Greater Fort Attack', 'Advanced Fortification', 180, 'materials', 'basic', 'Significantly increases the attack value of a fort');
INSERT INTO `development_items` VALUES ('Greater Fort Defense', 'Advanced Fortification', 180, 'materials', 'basic', 'Significantly increases the defense value of a fort');
INSERT INTO `development_items` VALUES ('Greater Fort Survivability', 'Ultimate Fortification', 200, 'materials', 'basic', 'Further reduces fort losses due to battles');
INSERT INTO `development_items` VALUES ('Bomb', 'Bomb Knowledge', 60, 'materials', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Poison Bomb', 'Poison Knowledge', 120, 'materials', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Trap', 'Trap Knowledge', 150, 'materials', 'basic', 'TBD: Add color text');
INSERT INTO `development_items` VALUES ('Psychological Trap', 'Psychological Effects', 180, 'materials', 'basic', 'TBD: Add color text');

-- --------------------------------------------------------

-- 
-- Table structure for table `fort_technologies`
-- 

CREATE TABLE `fort_technologies` (
  `development_name` varchar(50) NOT NULL,
  `attack_bonus` int(10) NOT NULL,
  `defense_bonus` int(10) NOT NULL,
  `survive` int(2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `fort_technologies`
-- 

INSERT INTO `fort_technologies` VALUES ('Fort', 100, 100, 25);
INSERT INTO `fort_technologies` VALUES ('Improved Fort Attack', 200, 0, 0);
INSERT INTO `fort_technologies` VALUES ('Improved Fort Defense', 0, 400, 0);
INSERT INTO `fort_technologies` VALUES ('Greater Fort Attack', 700, 0, 0);
INSERT INTO `fort_technologies` VALUES ('GreaterFort Defense', 0, 1000, 0);
INSERT INTO `fort_technologies` VALUES ('Greater Fort Survivability', 0, 0, 25);
INSERT INTO `fort_technologies` VALUES ('Basic Fort Survivability', 0, 0, 25);
INSERT INTO `fort_technologies` VALUES ('Advanced Fort Survivability', 0, 0, 25);

-- --------------------------------------------------------

-- 
-- Table structure for table `game`
-- 

CREATE TABLE `game` (
  `gamename` varchar(100) NOT NULL,
  `start_time` datetime NOT NULL,
  `last_updated_time` datetime NOT NULL,
  `current_tick` int(10) NOT NULL,
  `status` varchar(50) NOT NULL,
  `mineral_per_structure` int(10) NOT NULL,
  `organic_per_structure` int(10) NOT NULL,
  `energy_per_structure` int(10) NOT NULL,
  `base_mineral` int(10) NOT NULL,
  `base_organic` int(10) NOT NULL,
  `base_energy` int(10) NOT NULL,
  `base_creature_production` int(10) NOT NULL,
  `starting_mineral` int(10) NOT NULL,
  `starting_organic` int(10) NOT NULL,
  `starting_energy` int(10) NOT NULL,
  `starting_structures` int(10) NOT NULL,
  `increase_per_structure` int(10) NOT NULL,
  `number_ticks_per_day` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `game`
-- 

INSERT INTO `game` VALUES ('Revolution v0.3 - Gettin Real', '2007-11-28 09:00:00', '2007-12-23 09:50:15', 1980, 'Active', 250, 250, 250, 1000, 1000, 1000, 10, 250000, 250000, 250000, 100, 150, 144);

-- --------------------------------------------------------

-- 
-- Table structure for table `invite_key`
-- 

CREATE TABLE `invite_key` (
  `planet_host` varchar(50) NOT NULL,
  `galaxy` int(10) NOT NULL,
  `star` int(10) NOT NULL,
  `planet` int(10) NOT NULL,
  `invite_key` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `invite_key`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `last_seen`
-- 

CREATE TABLE `last_seen` (
  `player_name` varchar(50) NOT NULL,
  `communication_type` varchar(50) NOT NULL,
  `message_category` varchar(50) NOT NULL,
  `message_group` varchar(50) NOT NULL,
  `message_channel` varchar(50) NOT NULL,
  `last_read_id` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `last_seen`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `milestone`
-- 

CREATE TABLE `milestone` (
  `player_name` varchar(50) NOT NULL,
  `tick` int(10) NOT NULL,
  `type` varchar(50) NOT NULL,
  `milestone_name` varchar(50) NOT NULL,
  `amount` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `milestone`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `misc_items`
-- 

CREATE TABLE `misc_items` (
  `name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `development_item` varchar(50) NOT NULL,
  `mineral` int(10) NOT NULL,
  `organic` int(10) NOT NULL,
  `energy` int(10) NOT NULL,
  `ticks` int(10) NOT NULL,
  `description` mediumtext NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `misc_items`
-- 

INSERT INTO `misc_items` VALUES ('Bomb', 'bomb', 'Bomb', 10000, 5000, 0, 4, 'Bombs destroy forts.  It takes lots of bombs to destroy lots of forts.');
INSERT INTO `misc_items` VALUES ('Poison Bomb', 'bomb', 'Poison Bomb', 15000, 5000, 0, 6, 'Poison bombs destroy creatures.  One bomb, one creature');
INSERT INTO `misc_items` VALUES ('Trap', 'bomb', 'Trap', 12000, 6000, 0, 20, 'Traps have a good chance of destroying an incoming creature');
INSERT INTO `misc_items` VALUES ('Psychological Trap', 'bomb', 'Psychological Trap', 18000, 9000, 0, 24, 'Psychological Traps have a good chance to capture an incoming creature');
INSERT INTO `misc_items` VALUES ('Modulator', 'pulse', 'Modulator', 10000, 0, 10000, 4, 'Modulators increase the power of pulses, shields, and blasts.  The more modulators the stronger the effect.');
INSERT INTO `misc_items` VALUES ('Reflector', 'pulse', 'Reflector', 15000, 0, 5000, 4, 'Reflectors reduce the effectiveness of enemy pulses, shields, and blasts');
INSERT INTO `misc_items` VALUES ('Electromagnetic Pulse', 'pulse', 'Electromagnetic Pulse', 500000, 0, 1000000, 24, 'Electromagnetic Pulses damage circuits.  Cybernetics have circuits. Electromagnetic Pulses damage cybernetics.');
INSERT INTO `misc_items` VALUES ('Microwave Pulse', 'pulse', 'Microwave Pulse', 750000, 0, 750000, 24, 'Fry little critters. Fry. Fry Fry.');
INSERT INTO `misc_items` VALUES ('Electromagnetic Shield', 'pulse', 'Electromagnetic Shield', 1500000, 0, 3000000, 30, 'Shields are like pulses, but they last a lot longer');
INSERT INTO `misc_items` VALUES ('Microwave Shield', 'pulse', 'Microwave Shield', 2000000, 0, 2000000, 30, 'Shields are like pulses, but they last a lot longer');
INSERT INTO `misc_items` VALUES ('Electromagnetic Blast', 'pulse', 'Electromagnetic Blast', 750000, 0, 1500000, 36, 'Blasts instantly damage fleets that are at home.');
INSERT INTO `misc_items` VALUES ('Microwave Blast', 'pulse', 'Microwave Blast', 1000000, 0, 1000000, 36, 'Blasts instantly damage fleets that are at home.');
INSERT INTO `misc_items` VALUES ('Command Jammer', 'pulse', 'Command Jammer', 1500000, 0, 3000000, 36, 'The Command Jammer will disrupt all communication to incoming fleets.  When used all incoming fleets will no longer be able to recall.  All new launches will also not be able to recall for a short period of time.');
INSERT INTO `misc_items` VALUES ('Fort', 'fort', 'Fort', 10000, 5000, 0, 4, 'Forts are static defenses that cannot move but provide significant defense and offense');

-- --------------------------------------------------------

-- 
-- Table structure for table `monitor`
-- 

CREATE TABLE `monitor` (
  `player_name` varchar(50) NOT NULL,
  `target_name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `start_tick` int(10) NOT NULL,
  `until_tick` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `monitor`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `news`
-- 

CREATE TABLE `news` (
  `ID` int(10) NOT NULL auto_increment,
  `player_name` varchar(50) NOT NULL,
  `category` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL,
  `subject` varchar(80) NOT NULL,
  `time` datetime NOT NULL,
  `tick` int(11) NOT NULL,
  `has_been_read` tinyint(1) NOT NULL,
  `text` longtext NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `news`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `player_alliance`
-- 

CREATE TABLE `player_alliance` (
  `player_name` varchar(50) NOT NULL,
  `alliance` varchar(50) NOT NULL,
  `rank` varchar(50) NOT NULL,
  PRIMARY KEY  (`player_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `player_alliance`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `player_build`
-- 

CREATE TABLE `player_build` (
  `player_name` varchar(50) NOT NULL,
  `build_type` varchar(50) NOT NULL,
  `build_item` varchar(50) NOT NULL,
  `number` int(10) NOT NULL,
  `tick_started` int(10) NOT NULL,
  `ticks_remaining` int(10) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `player_build`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `player_creatures`
-- 

CREATE TABLE `player_creatures` (
  `player_name` varchar(50) NOT NULL,
  `creature` varchar(50) NOT NULL,
  `number` int(10) NOT NULL,
  `fleet_location` varchar(10) NOT NULL,
  KEY `player_name` (`player_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `player_creatures`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `player_items`
-- 

CREATE TABLE `player_items` (
  `player_name` varchar(50) NOT NULL,
  `item_type` varchar(50) NOT NULL,
  `number` int(10) NOT NULL,
  `status` varchar(50) NOT NULL,
  KEY `player_name` (`player_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `player_items`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `player_orders`
-- 

CREATE TABLE `player_orders` (
  `player_name` varchar(50) NOT NULL,
  `target_name` varchar(50) NOT NULL,
  `mission_type` varchar(50) NOT NULL,
  `mission_ticks` int(10) NOT NULL,
  `fleet` varchar(50) NOT NULL,
  `launch_time` datetime NOT NULL,
  `launch_tick` int(10) NOT NULL,
  `arrival_tick` int(10) NOT NULL,
  `depart_tick` int(10) NOT NULL,
  `return_tick` int(10) NOT NULL,
  `unassigned` varchar(10) NOT NULL,
  `extractors` int(10) NOT NULL default '0',
  `genetic_labs` int(10) NOT NULL default '0',
  `powerplants` int(10) NOT NULL default '0',
  `factories` int(10) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `player_orders`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `player_scans`
-- 

CREATE TABLE `player_scans` (
  `player_name` varchar(50) NOT NULL,
  `scan_type` varchar(50) NOT NULL,
  `number` int(10) NOT NULL,
  PRIMARY KEY  (`player_name`,`scan_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `player_scans`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `pulse_use`
-- 

CREATE TABLE `pulse_use` (
  `player_name` varchar(50) NOT NULL,
  `pulse_type` varchar(50) NOT NULL,
  `tick` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `pulse_use`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `research_items`
-- 

CREATE TABLE `research_items` (
  `name` varchar(50) NOT NULL,
  `mineral` int(10) NOT NULL,
  `organic` int(10) NOT NULL,
  `ticks` int(10) NOT NULL,
  `type` varchar(20) NOT NULL,
  `pre1` varchar(50) default NULL,
  `pre2` varchar(50) default NULL,
  `pre3` varchar(50) default NULL,
  `level` int(3) NOT NULL,
  `lane` int(3) NOT NULL,
  `size` int(3) NOT NULL,
  `color_text` mediumtext NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `research_items`
-- 

INSERT INTO `research_items` VALUES ('Basic Genetic Science', 1000, 5000, 2, 'creature', '', '', '', 1, 1, 3, 'Genetic science involves the alteration of DNA to produce wildly effective creatures');
INSERT INTO `research_items` VALUES ('Basic Cybernetics Science', 5000, 1000, 4, 'creature', '', '', '', 1, 2, 3, 'Cybernetics is the study of the human-machine interface.');
INSERT INTO `research_items` VALUES ('Genetic Engineering', 5000, 15000, 3, 'creature', 'Basic Genetic Science', '', '', 2, 1, 2, 'Genetic engineering fundamentals allows access to more sophisticated genetic engineered creature production');
INSERT INTO `research_items` VALUES ('Hybrid Engineering', 10000, 10000, 9, 'creature', 'Basic Cybernetics Science', 'Basic Genetic Science', '', 2, 2, 2, 'Hybrid engineering fundamentals allows access to more sophisticated genetic engineered and naono-enhanced creature production');
INSERT INTO `research_items` VALUES ('Cybernetic Engineering', 5000, 15000, 6, 'creature', 'Basic Cybernetics Science', '', '', 2, 3, 2, 'Cybernetic engineering fundamentals allows access to more sophisticated nano-enhanced machines.');
INSERT INTO `research_items` VALUES ('Basic Drake Knowledge', 10000, 30000, 5, 'creature', 'Genetic Engineering', '', '', 3, 1, 1, 'Basic drake knowledge teaches the fundamentals behind genetically engineering dragon class creatures');
INSERT INTO `research_items` VALUES ('Basic Fairy Knowledge', 15000, 25000, 4, 'creature', 'Genetic Engineering', '', '', 3, 2, 1, 'Basic fairy knowledge teaches the fundamentals behind genetically engineering humanoid like creatures that can take a lot of damage.');
INSERT INTO `research_items` VALUES ('Basic Humanoid Knowledge', 12500, 25000, 6, 'creature', 'Hybrid Engineering', '', '', 3, 3, 1, 'Basic humanoid knowledge teaches the fundamentals behind genetically engineering and nanoenhanced humanoid class creatures.');
INSERT INTO `research_items` VALUES ('Basic Feline Knowledge', 20000, 20000, 6, 'creature', 'Hybrid Engineering', '', '', 3, 4, 1, 'Basic feline knowledge teaches the fundamentals behind genetically engineering and nano-enhanced cats');
INSERT INTO `research_items` VALUES ('Basic Legged Cybernetic Knowledge', 30000, 15000, 8, 'creature', 'Cybernetic Engineering', '', '', 3, 5, 1, 'Basic legged cybernetic knowledge teaches the fundamentals behind nano-enhanced machines that mimic the most dangerous of creatures.');
INSERT INTO `research_items` VALUES ('Basic Tracked Cybernetic Knowledge', 40000, 10000, 10, 'creature', 'Cybernetic Engineering', '', '', 3, 6, 1, 'Basic tracked cybernetic knowledge extends the human machine interface until there is no way to seperate the two.');
INSERT INTO `research_items` VALUES ('Imp Knowledge', 30000, 100000, 10, 'creature', 'Basic Drake Knowledge', '', '', 4, 1, 1, 'Knowledge of imp  research opens the ability to make Imp.  Imps are the smallest dragons, barely bigger then a cat.  But they can still breathe fire and are very dangerous.');
INSERT INTO `research_items` VALUES ('Sprite Knowledge', 40000, 60000, 6, 'creature', 'Basic Fairy Knowledge', '', '', 4, 2, 1, 'Knowledge of doomcrusher research opens the ability to make Doomcrusher, the most powerful of the tracked cybernetic class creatures.');
INSERT INTO `research_items` VALUES ('Ogre Knowledge', 50000, 65000, 8, 'creature', 'Basic Humanoid Knowledge', '', '', 4, 3, 1, 'Knowledge of ogre research opens the ability to make Ogres,  Ogres are a bit larger and stronger then an ordinary person, but they are a lot dumber.');
INSERT INTO `research_items` VALUES ('Cheetah Knowledge', 50000, 50000, 9, 'creature', 'Basic Feline Knowledge', '', '', 4, 4, 1, 'Knowledge of cheetah research opens the ability to make Cheetahs.  Cheetahs in space.  Very scary.');
INSERT INTO `research_items` VALUES ('Cyborg Knowledge', 70000, 40000, 12, 'creature', 'Basic Legged Cybernetic Knowledge', '', '', 4, 5, 1, 'Knowledge of cyborg research opens the ability to make Cyborg.  Cybors are two legged beasts.  They look like man, but clearly aren''t.');
INSERT INTO `research_items` VALUES ('Humvee Knowledge', 100000, 40000, 14, 'creature', 'Basic Tracked Cybernetic Knowledge', '', '', 4, 6, 1, 'Knowledge of humvee research opens the ability to make Humvees.  Though technically not on a track, these vehicles are a lot faster then tanks and almost as capable.');
INSERT INTO `research_items` VALUES ('Wyrm Knowledge', 100000, 250000, 24, 'creature', 'Imp Knowledge', '', '', 5, 1, 1, 'Knowledge of wyrm research opens the ability to make Wyrms.  Wyrms are snakelike small dragons that can strike fast and hard.');
INSERT INTO `research_items` VALUES ('Dryad Knowledge', 100000, 150000, 18, 'creature', 'Sprite Knowledge', '', '', 5, 2, 1, 'Knowledge of doomcrusher research opens the ability to make Doomcrusher, the most powerful of the tracked cybernetic class creatures.');
INSERT INTO `research_items` VALUES ('Troll Knowledge', 120000, 170000, 21, 'creature', 'Ogre Knowledge', '', '', 5, 3, 1, 'Knowledge of dryad research opens the ability to make Dryads.  Dryads can hide in the land making it much harder to find and kill them.');
INSERT INTO `research_items` VALUES ('Panther Knowledge', 100000, 100000, 22, 'creature', 'Cheetah Knowledge', '', '', 5, 4, 1, 'Knowledge of panther research opens the ability to make Panthers.  Panthers are agile and slient... until they pounce.');
INSERT INTO `research_items` VALUES ('Spider Knowledge', 200000, 125000, 27, 'creature', 'Cyborg Knowledge', '', '', 5, 5, 1, 'Knowledge of spider research opens the ability to make Spider mechbots.  These mechbots are somewhat fragile but can deal swift damage');
INSERT INTO `research_items` VALUES ('Tank Knowledge', 250000, 100000, 30, 'creature', 'Humvee Knowledge', '', '', 5, 6, 1, 'Knowledge of tank research opens the ability to make Tanks, the most powerful of the tracked cybernetic class creatures.');
INSERT INTO `research_items` VALUES ('Wyvern Knowledge', 200000, 400000, 30, 'creature', 'Wyrm Knowledge', '', '', 6, 1, 1, 'Knowledge of wyvern research opens the ability to make Wyverns, a fast small dragon.');
INSERT INTO `research_items` VALUES ('Centaur Knowledge', 200000, 300000, 27, 'creature', 'Dryad Knowledge', '', '', 6, 2, 1, 'Knowledge of centaur research opens the ability to make Centaurs, protector of the helpless');
INSERT INTO `research_items` VALUES ('Giant Knowledge', 300000, 350000, 32, 'creature', 'Troll Knowledge', '', '', 6, 3, 1, 'Knowledge of giant research opens the ability to make Giants.  These beasts genetically altered and nano-ehanced can both withstand and deal large amounts of damage');
INSERT INTO `research_items` VALUES ('Tiger Knowledge', 200000, 200000, 34, 'creature', 'Panther Knowledge', '', '', 6, 4, 1, 'Knowledge of tiger research opens the ability to make Tigers.  Genetically enhanced tigers are powerful and strong');
INSERT INTO `research_items` VALUES ('Mantis Knowledge', 400000, 200000, 36, 'creature', 'Spider Knowledge', '', '', 6, 5, 1, 'Knowledge of mantis research opens the ability to make Mantis Mechbots.  These mechbots deal more damage than all but the strongest creatures, too bad they fail to take what they can deal.');
INSERT INTO `research_items` VALUES ('Crusher Knowledge', 500000, 200000, 42, 'creature', 'Tank Knowledge', '', '', 6, 6, 1, 'Knowledge of crusher research opens the ability to make Crusher Tanks.  Crushers are much larger then the ordinary tank.');
INSERT INTO `research_items` VALUES ('Dragon Knowledge', 500000, 1000000, 70, 'creature', 'Wyvern Knowledge', '', '', 7, 1, 1, 'Knowledge of dragon research opens the ability to make Dragons, the most powerful of the drake class creatures.');
INSERT INTO `research_items` VALUES ('Unicorn Knowledge', 400000, 600000, 64, 'creature', 'Centaur Knowledge', '', '', 7, 2, 1, 'Knowledge of unicorn research opens the ability to make Unicorns, the most powerful of the fairy class creatures.');
INSERT INTO `research_items` VALUES ('Demon Knowledge', 700000, 800000, 72, 'creature', 'Giant Knowledge', '', '', 7, 3, 1, 'Knowledge of demon research opens the ability to make Demons, the most powerful of the humanoid class creatures.');
INSERT INTO `research_items` VALUES ('Lion Knowledge', 400000, 400000, 80, 'creature', 'Tiger Knowledge', '', '', 7, 4, 1, 'Knowledge of lion research opens the ability to make Lions, the most powerful of the feline class creatures.');
INSERT INTO `research_items` VALUES ('Megadon Knowledge', 1000000, 700000, 84, 'creature', 'Mantis Knowledge', '', '', 7, 5, 1, 'Knowledge of megadon research opens the ability to make Megadons, the most powerful of the legged cybernetic class creatures.');
INSERT INTO `research_items` VALUES ('Doomcrusher Knowledge', 1500000, 1000000, 100, 'creature', 'Crusher Knowledge', '', '', 7, 6, 1, 'Knowledge of doomcrusher research opens the ability to make Doomcrusher, the most powerful of the tracked cybernetic class creatures.');
INSERT INTO `research_items` VALUES ('Genetic Expertise', 5000000, 3000000, 75, 'creature', 'Dragon Knowledge', 'Unicorn Knowledge', '', 8, 1, 2, 'The top of the genetic tree.  This research field opens up enhancement technologies for creatures in the genetic tree.');
INSERT INTO `research_items` VALUES ('Hybrid Expertise', 5000000, 5000000, 50, 'creature', 'Demon Knowledge', 'Lion Knowledge', '', 8, 2, 2, 'This is the top of the hybrid research.  This technology opens enhancement technologies for the entire hybrid tree.');
INSERT INTO `research_items` VALUES ('Cybernetic Expertise', 3000000, 5000000, 100, 'creature', 'Megadon Knowledge', 'Doomcrusher Knowledge', '', 8, 3, 2, 'This is the top of the cybernetic tree.  It opes technological enhancements to the entire cybernetic tree.');
INSERT INTO `research_items` VALUES ('Creature Mastery', 10000000, 5000000, 150, 'creature', 'Genetic Expertise', 'Hybrid Expertise', 'Cybernetic Expertise', 9, 1, 6, 'Mastery of all creatures.  This discipline is the ultimate in creature development.  It opens up the highest and strongest of all creature technologies');
INSERT INTO `research_items` VALUES ('Energy Physics', 5000, 2500, 4, 'energy', '', '', '', 1, 1, 4, 'Energy Physics provides the fundamental necessary to study both electromagnetism and microwave radiation');
INSERT INTO `research_items` VALUES ('Basic Electromagnetic Engineering', 10000, 2500, 6, 'energy', 'Energy Physics', '', '', 2, 1, 2, 'Electromagnetic Engineering is the study of the physics behind advanced electromagnetism.  Both scans and EM pulses require this basic knowledge.');
INSERT INTO `research_items` VALUES ('Basic Microwave Engineering', 7500, 5000, 8, 'energy', 'Energy Physics', '', '', 2, 2, 2, 'Microwave Engineering fundamentals are needed to develop microwave pulses and interference pattern technologies.');
INSERT INTO `research_items` VALUES ('Site Scanning', 10000, 5000, 8, 'energy', 'Basic Electromagnetic Engineering', '', '', 3, 1, 1, 'Understanding EM fields is essential for getting any useful information from electromagnetic scans');
INSERT INTO `research_items` VALUES ('Pulse Modulation', 20000, 7500, 24, 'energy', 'Basic Electromagnetic Engineering', '', '', 3, 2, 1, 'With increased power a scan can create a significant pulse of energy.  The field is needed to develop that energy into a weapon');
INSERT INTO `research_items` VALUES ('Pulse Reflection', 15000, 12500, 36, 'energy', 'Basic Microwave Engineering', '', '', 3, 3, 1, 'Low in the Electromagnetic spectrum are microwaves.  These pulses can cause significant radioactive damage');
INSERT INTO `research_items` VALUES ('Energy Detection', 20000, 10000, 12, 'energy', 'Basic Microwave Engineering', '', '', 3, 4, 1, 'By tuning microwaves correctly, the scans can be either blocked or minimized');
INSERT INTO `research_items` VALUES ('Remote Scanning', 25000, 15000, 12, 'energy', 'Site Scanning', '', '', 4, 1, 1, 'Scanning technology can be used to better map the homeworld for proper equipment laydown');
INSERT INTO `research_items` VALUES ('Electromagnetic Pulse', 30000, 12000, 48, 'energy', 'Pulse Modulation', '', '', 4, 2, 1, 'Quick pulses of electormagnetic energy can significcacntly improve some aspects of the performance of the pulse at the cost of others');
INSERT INTO `research_items` VALUES ('Microwave Pulse', 40000, 30000, 60, 'energy', 'Pulse Reflection', '', '', 4, 3, 1, 'Blasting a quick solid energy burst of microwaves it is possible to improve the performance of these pulses');
INSERT INTO `research_items` VALUES ('Noise Generation', 50000, 25000, 18, 'energy', 'Energy Detection', '', '', 4, 4, 1, 'Interference can be improved with a little knowledge of the possible waveforms and patterns to use');
INSERT INTO `research_items` VALUES ('Detailed Scanning', 50000, 30000, 24, 'energy', 'Remote Scanning', '', '', 5, 1, 1, 'Once an understanding of the basic physics of scanning is understood, it will be possible to launch scans to get immediate information about a distant continent');
INSERT INTO `research_items` VALUES ('Electromagnetic Shield', 50000, 40000, 84, 'energy', 'Electromagnetic Pulse', '', '', 5, 2, 1, 'By focusing the pulse greater results can be achieved');
INSERT INTO `research_items` VALUES ('Microwave Shield', 70000, 50000, 96, 'energy', 'Microwave Pulse', '', '', 5, 3, 1, 'By extending the pulses in a longwave waveform, the pulse duration can be extended with dire consequences for its target');
INSERT INTO `research_items` VALUES ('Energy Filtration', 100000, 50000, 36, 'energy', 'Noise Generation', '', '', 5, 4, 1, 'By extending the pattern, it is possible to improve upon the electomagnetic pulses already great capabilities');
INSERT INTO `research_items` VALUES ('Monitoring', 100000, 75000, 72, 'energy', 'Detailed Scanning', '', '', 6, 1, 1, 'Using significant power, it is possible to extend a scan into galactic space');
INSERT INTO `research_items` VALUES ('Electromagnetic Blast', 120000, 100000, 100, 'energy', 'Electromagnetic Shield', '', '', 6, 2, 1, 'By focusing the pulse greater results can be achieved');
INSERT INTO `research_items` VALUES ('Microwave Blast', 200000, 150000, 125, 'energy', 'Microwave Shield', '', '', 6, 3, 1, 'With the application of shortwave pulse it is possible to get the same results as the longwave only significantly quicker');
INSERT INTO `research_items` VALUES ('Signal Jamming', 250000, 125000, 72, 'energy', 'Energy Filtration', '', '', 6, 4, 1, 'By directing and tuning the interference patterns it is possible to completely fool a scan and gain information about the scanner');
INSERT INTO `research_items` VALUES ('Scan Expertise', 500000, 400000, 100, 'energy', 'Monitoring', '', '', 7, 1, 1, 'This discipline is the ultimate in scanning knowledge');
INSERT INTO `research_items` VALUES ('Pulse Expertise', 400000, 300000, 150, 'energy', 'Electromagnetic Blast', 'Microwave Blast', '', 7, 2, 2, 'This discipline is the ultimate in pulse knowledge');
INSERT INTO `research_items` VALUES ('Signal Expertise', 600000, 300000, 100, 'energy', 'Signal Jamming', '', '', 7, 3, 1, 'This discipline is the ultimate in interference knowledge');
INSERT INTO `research_items` VALUES ('Energy Mastery', 10000000, 7000000, 200, 'energy', 'Scan Expertise', 'Pulse Expertise', 'Signal Expertise', 8, 1, 4, 'Mastering energy itself is the ultimate goal of any physicist');
INSERT INTO `research_items` VALUES ('Materials Science', 1000, 200, 2, 'materials', '', '', '', 1, 1, 4, 'Materials Science is useful for ship design as well as creature enhancements');
INSERT INTO `research_items` VALUES ('Nanotechnology', 5000, 1000, 4, 'materials', 'Materials Science', '', '', 2, 1, 2, 'Nanotechnology is the science of small things');
INSERT INTO `research_items` VALUES ('Quantum Mechanics', 7000, 3000, 4, 'materials', 'Materials Science', '', '', 2, 2, 2, 'Quantum Mechanics is the science of really small things');
INSERT INTO `research_items` VALUES ('Intercontinental Travel', 10000, 5000, 8, 'materials', 'Nanotechnology', '', '', 3, 1, 1, 'Before we soared into space we had to float on a boat');
INSERT INTO `research_items` VALUES ('Fortification', 5000, 15000, 12, 'materials', 'Nanotechnology', '', '', 3, 2, 1, 'Basic nanovirus affects all creatures and can significantly reduce their effectiveness, for a time.');
INSERT INTO `research_items` VALUES ('Bomb Knowledge', 10000, 15000, 12, 'materials', 'Quantum Mechanics', '', '', 3, 3, 1, 'Personal shield science will revolutionize troop survival');
INSERT INTO `research_items` VALUES ('Ion Propulsion', 25000, 12000, 16, 'materials', 'Quantum Mechanics', '', '', 3, 4, 1, 'Newton''s second law of motion says if you fire things out your back really fast, you will go forward really fast');
INSERT INTO `research_items` VALUES ('Interplanetary Travel', 20000, 10000, 20, 'materials', 'Intercontinental Travel', '', '', 4, 1, 1, 'Before we traveled to other stars we had to go to our own');
INSERT INTO `research_items` VALUES ('Improved Fortification', 15000, 50000, 30, 'materials', 'Fortification', '', '', 4, 2, 1, 'By creating nanobots that can infect creatures, their discipline can be reduced and they may defect');
INSERT INTO `research_items` VALUES ('Poison Knowledge', 25000, 50000, 36, 'materials', 'Bomb Knowledge', '', '', 4, 3, 1, 'Quantum effects on weapons can increase the attack power of all creatures large and small');
INSERT INTO `research_items` VALUES ('Nuclear Propulsion', 75000, 50000, 50, 'materials', 'Ion Propulsion', '', '', 4, 4, 1, 'If you want to go really fast, go nuclear');
INSERT INTO `research_items` VALUES ('Interstellar Travel', 50000, 20000, 40, 'materials', 'Interplanetary Travel', '', '', 5, 1, 1, 'Its no small feat making a ship that can travel to other stars');
INSERT INTO `research_items` VALUES ('Advanced Fortification', 50000, 150000, 80, 'materials', 'Improved Fortification', '', '', 5, 2, 1, 'Advances in nanoviruses can increase duration and effectiveness of the disease');
INSERT INTO `research_items` VALUES ('Trap Knowledge', 100000, 150000, 90, 'materials', 'Poison Knowledge', '', '', 5, 3, 1, 'Basic shields are good, these shields are better');
INSERT INTO `research_items` VALUES ('Fusion Propulsion', 200000, 100000, 70, 'materials', 'Nuclear Propulsion', '', '', 5, 4, 1, 'If nuclear isn''t fast enough look toward fusion');
INSERT INTO `research_items` VALUES ('Intergalactic Travel', 150000, 60000, 50, 'materials', 'Interstellar Travel', '', '', 6, 1, 1, 'Traveling to other stars is impressive, traveling to other galaxies is amazing');
INSERT INTO `research_items` VALUES ('Ultimate Fortification', 120000, 300000, 100, 'materials', 'Advanced Fortification', '', '', 6, 2, 1, 'Advanced Nanobots can massively reduce the resistence of the enemy');
INSERT INTO `research_items` VALUES ('Psychological Effects', 180000, 300000, 100, 'materials', 'Trap Knowledge', '', '', 6, 3, 1, 'This knowledge can creature creature boosts that can make all creatures much more effective');
INSERT INTO `research_items` VALUES ('Quantum Propulsion', 500000, 300000, 90, 'materials', 'Fusion Propulsion', '', '', 6, 4, 1, 'When things get really small, you can go really fast');
INSERT INTO `research_items` VALUES ('Vehicle Expertise', 1000000, 1000000, 100, 'materials', 'Intergalactic Travel', '', '', 7, 1, 1, 'This discipline is the ultimate in vehicle knowledge');
INSERT INTO `research_items` VALUES ('Special Effects Expertise', 500000, 1500000, 125, 'materials', 'Ultimate Fortification', 'Psychological Effects', '', 7, 2, 2, 'This discipline is the ultimate in creature enhancements');
INSERT INTO `research_items` VALUES ('Propulsion Expertise', 2000000, 750000, 100, 'materials', 'Quantum Propulsion', '', '', 7, 3, 1, 'This discipline is the ultimate in propulsion knowledge');
INSERT INTO `research_items` VALUES ('Materials Mastery', 10000000, 7500000, 200, 'materials', 'Vehicle Expertise', 'Weapons and Shield Expertise', 'Propulsion Expertise', 8, 1, 4, 'Mastering energy itself is the ultimate goal of any engineer');

-- --------------------------------------------------------

-- 
-- Table structure for table `scan_items`
-- 

CREATE TABLE `scan_items` (
  `name` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `subtype` varchar(50) NOT NULL,
  `dependent_development` varchar(50) NOT NULL,
  `mineral` int(10) NOT NULL,
  `energy` int(10) NOT NULL,
  `ticks` int(10) NOT NULL,
  `description` varchar(1024) NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `scan_items`
-- 

INSERT INTO `scan_items` VALUES ('Survey Tuners', 'equipment', 'survey_tuner', 'Surveying', 5000, 5000, 4, 'Each survey enhancer will increase the chances of successfully performing a site survey and getting a new unassigned structure');
INSERT INTO `scan_items` VALUES ('Scan Amplifiers', 'equipment', 'scan_amplifier', 'Scan Amplification', 5000, 5000, 4, 'Each scan amplifier will increase the chances of a successful remote scan');
INSERT INTO `scan_items` VALUES ('Noise Generators', 'equipment', 'noise_generator', 'Noise Generation', 10000, 10000, 8, 'A noise generator reduces the likelihood of a scan on your continent being successful');
INSERT INTO `scan_items` VALUES ('Scan Sensors', 'equipment', 'scan_sensor', 'Scan Sensing', 10000, 10000, 8, 'A scan sensor increases the likelihood that you will detect an incoming scan');
INSERT INTO `scan_items` VALUES ('Scan Filters', 'equipment', 'scan_filter', 'Scan Filtering', 10000, 10000, 8, 'A scan filter reduces the bandwidth of the scan making it hard to be detected');
INSERT INTO `scan_items` VALUES ('Site Scans', 'active', 'site_scan', 'Site Scan', 5000, 10000, 4, 'Each successful site scan will increase the number of unassigned structures');
INSERT INTO `scan_items` VALUES ('Research and Development Scans', 'active', 'r_and_d_scan', 'R and D Scan', 1000, 2000, 4, 'A Research and Development scan will give information on the research and development items of a player');
INSERT INTO `scan_items` VALUES ('Continent Scans', 'active', 'continent_scan', 'Continent Scan', 3000, 6000, 8, 'A continent scan will give basic details about a remote continent');
INSERT INTO `scan_items` VALUES ('Creature Scans', 'active', 'creature_scan', 'Creature Scan', 4000, 8000, 12, 'A creature scan will give basic details the total number of creatures owned by a player');
INSERT INTO `scan_items` VALUES ('Military Scans', 'active', 'military_scan', 'Military Scan', 5000, 10000, 18, 'A military scan will give information on the size and destination of each fleet of a player');
INSERT INTO `scan_items` VALUES ('Planetary Scans', 'active', 'planetary_scan', 'Planetary Scan', 20000, 40000, 24, 'A planetary scan will give basic details of every continent on a planet');
INSERT INTO `scan_items` VALUES ('News Scans', 'active', 'news_scan', 'News Scan', 10000, 20000, 20, 'A news scan will give all news for a player in the last 24 ticks');
INSERT INTO `scan_items` VALUES ('Full Scans', 'active', 'full_scan', 'Full Scan', 25000, 50000, 24, 'A full scan will give detailed information about a single player.  This includes structures, scans, amps, creatures, and buildings');
INSERT INTO `scan_items` VALUES ('Launch Monitor', 'monitor', 'launch_monitor', 'Monitoring', 5000, 10000, 24, 'Will notify you in your news, the moment a player launches a fleet.  This surveillance last for 24 hours');
INSERT INTO `scan_items` VALUES ('Structure Monitor', 'monitor', 'structure_monitor', 'Monitoring', 1000, 1500, 4, 'Shows the exact structure types on the rankings and universe pages for a long time.');

-- --------------------------------------------------------

-- 
-- Table structure for table `scan_results`
-- 

CREATE TABLE `scan_results` (
  `id` int(10) NOT NULL auto_increment,
  `player_name` varchar(50) NOT NULL,
  `target_name` varchar(50) NOT NULL,
  `scan_type` varchar(50) NOT NULL,
  `tick` int(10) NOT NULL,
  `time` datetime NOT NULL,
  `text` longtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `scan_results`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `tick_history`
-- 

CREATE TABLE `tick_history` (
  `player_name` varchar(50) NOT NULL,
  `time` datetime NOT NULL,
  `action` varchar(50) NOT NULL,
  `build_item` varchar(50) default NULL,
  `number` int(10) default NULL,
  `fleet` varchar(50) default NULL,
  `target_name` varchar(50) default NULL,
  `start_tick` int(10) NOT NULL,
  `arrival_tick` int(10) default NULL,
  `depart_tick` int(10) default NULL,
  `end_tick` int(10) NOT NULL,
  `attack` int(10) default NULL,
  `defense` int(10) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `tick_history`
-- 

-- phpMyAdmin SQL Dump
-- version 2.9.1.1
-- http://www.phpmyadmin.net
-- 
-- Host: 10.8.11.195
-- Generation Time: Dec 24, 2007 at 07:59 AM
-- Server version: 5.0.45
-- PHP Version: 4.4.4
-- 
-- Database: `revolution_game`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `player`
-- 

CREATE TABLE `player` (
  `name` varchar(50) NOT NULL,
  `location` varchar(50) NOT NULL,
  `password` varchar(40) NOT NULL,
  `email` varchar(200) NOT NULL,
  `unassigned` int(10) NOT NULL,
  `extractor` int(10) NOT NULL,
  `genetic_lab` int(10) NOT NULL,
  `powerplant` int(10) NOT NULL,
  `factory` int(10) NOT NULL,
  `crystal` int(10) NOT NULL,
  `mineral` int(20) NOT NULL,
  `organic` int(20) NOT NULL,
  `energy` int(20) NOT NULL,
  `mana` int(20) NOT NULL,
  `galaxy` int(4) NOT NULL,
  `star` int(4) NOT NULL,
  `planet` int(4) NOT NULL,
  `continent` int(4) NOT NULL,
  `last_online` datetime NOT NULL,
  `admin` varchar(50) NOT NULL,
  `help` varchar(50) NOT NULL,
  `score` int(20) NOT NULL,
  PRIMARY KEY  (`name`),
  KEY `location` (`galaxy`,`star`,`planet`,`continent`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `player`
-- 

