--
-- Host: localhost    Database: cacti
-- ------------------------------------------------------
-- Server version	10.6.3-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


--
-- Table structure for table `aggregate_graph_templates`
--
CREATE TABLE `aggregate_graph_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `graph_template_id` int(10) unsigned NOT NULL,
  `gprint_prefix` varchar(64) NOT NULL,
  `gprint_format` char(2) DEFAULT '',
  `graph_type` int(10) unsigned NOT NULL,
  `total` int(10) unsigned NOT NULL,
  `total_type` int(10) unsigned NOT NULL,
  `total_prefix` varchar(64) NOT NULL,
  `order_type` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `graph_template_id` (`graph_template_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Template Definitions for Aggregate Graphs';


--
-- Table structure for table `aggregate_graph_templates_graph`
--
CREATE TABLE `aggregate_graph_templates_graph` (
  `aggregate_template_id` int(10) unsigned NOT NULL,
  `t_image_format_id` char(2) DEFAULT '',
  `image_format_id` tinyint(1) NOT NULL DEFAULT '0',
  `t_height` char(2) DEFAULT '',
  `height` mediumint(8) NOT NULL DEFAULT '0',
  `t_width` char(2) DEFAULT '',
  `width` mediumint(8) NOT NULL DEFAULT '0',
  `t_upper_limit` char(2) DEFAULT '',
  `upper_limit` varchar(20) NOT NULL DEFAULT '0',
  `t_lower_limit` char(2) DEFAULT '',
  `lower_limit` varchar(20) NOT NULL DEFAULT '0',
  `t_vertical_label` char(2) DEFAULT '',
  `vertical_label` varchar(200) DEFAULT '',
  `t_slope_mode` char(2) DEFAULT '',
  `slope_mode` char(2) DEFAULT 'on',
  `t_auto_scale` char(2) DEFAULT '',
  `auto_scale` char(2) DEFAULT '',
  `t_auto_scale_opts` char(2) DEFAULT '',
  `auto_scale_opts` tinyint(1) NOT NULL DEFAULT '0',
  `t_auto_scale_log` char(2) DEFAULT '',
  `auto_scale_log` char(2) DEFAULT '',
  `t_scale_log_units` char(2) DEFAULT '',
  `scale_log_units` char(2) DEFAULT '',
  `t_auto_scale_rigid` char(2) DEFAULT '',
  `auto_scale_rigid` char(2) DEFAULT '',
  `t_auto_padding` char(2) DEFAULT '',
  `auto_padding` char(2) DEFAULT '',
  `t_base_value` char(2) DEFAULT '',
  `base_value` mediumint(8) NOT NULL DEFAULT '0',
  `t_grouping` char(2) DEFAULT '',
  `grouping` char(2) NOT NULL DEFAULT '',
  `t_unit_value` char(2) DEFAULT '',
  `unit_value` varchar(20) DEFAULT '',
  `t_unit_exponent_value` char(2) DEFAULT '',
  `unit_exponent_value` varchar(5) NOT NULL DEFAULT '',
  `t_alt_y_grid` char(2) default '',
  `alt_y_grid` char(2) default NULL,
  `t_right_axis` char(2) DEFAULT '',
  `right_axis` varchar(20) DEFAULT NULL,
  `t_right_axis_label` char(2) DEFAULT '',
  `right_axis_label` varchar(200) DEFAULT NULL,
  `t_right_axis_format` char(2) DEFAULT '',
  `right_axis_format` mediumint(8) DEFAULT NULL,
  `t_right_axis_formatter` char(2) DEFAULT '',
  `right_axis_formatter` varchar(10) DEFAULT NULL,
  `t_left_axis_formatter` char(2) DEFAULT '',
  `left_axis_formatter` varchar(10) DEFAULT NULL,
  `t_no_gridfit` char(2) DEFAULT '',
  `no_gridfit` char(2) DEFAULT NULL,
  `t_unit_length` char(2) DEFAULT '',
  `unit_length` varchar(10) DEFAULT NULL,
  `t_tab_width` char(2) DEFAULT '',
  `tab_width` varchar(20) DEFAULT '30',
  `t_dynamic_labels` char(2) default '',
  `dynamic_labels` char(2) default NULL,
  `t_force_rules_legend` char(2) DEFAULT '',
  `force_rules_legend` char(2) DEFAULT NULL,
  `t_legend_position` char(2) DEFAULT '',
  `legend_position` varchar(10) DEFAULT NULL,
  `t_legend_direction` char(2) DEFAULT '',
  `legend_direction` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`aggregate_template_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Aggregate Template Graph Data';


--
-- Table structure for table `aggregate_graph_templates_item`
--
CREATE TABLE `aggregate_graph_templates_item` (
  `aggregate_template_id` int(10) unsigned NOT NULL,
  `graph_templates_item_id` int(10) unsigned NOT NULL,
  `sequence` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `color_template` int(11) NOT NULL,
  `t_graph_type_id` char(2) DEFAULT '',
  `graph_type_id` tinyint(3) NOT NULL DEFAULT 0,
  `t_cdef_id` char(2) DEFAULT '',
  `cdef_id` mediumint(8) unsigned DEFAULT NULL,
  `item_skip` char(2) NOT NULL,
  `item_total` char(2) NOT NULL,
  PRIMARY KEY (`aggregate_template_id`,`graph_templates_item_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Aggregate Template Graph Items';


--
-- Table structure for table `aggregate_graphs`
--
CREATE TABLE `aggregate_graphs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `aggregate_template_id` int(10) unsigned NOT NULL,
  `template_propogation` char(2) NOT NULL DEFAULT '',
  `local_graph_id` int(10) unsigned NOT NULL,
  `title_format` varchar(128) NOT NULL,
  `graph_template_id` int(10) unsigned NOT NULL,
  `gprint_prefix` varchar(64) NOT NULL,
  `gprint_format` char(2) DEFAULT '',
  `graph_type` int(10) unsigned NOT NULL,
  `total` int(10) unsigned NOT NULL,
  `total_type` int(10) unsigned NOT NULL,
  `total_prefix` varchar(64) NOT NULL,
  `order_type` int(10) unsigned NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `aggregate_template_id` (`aggregate_template_id`),
  KEY `local_graph_id` (`local_graph_id`),
  KEY `title_format` (`title_format`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Aggregate Graph Definitions';


--
-- Table structure for table `aggregate_graphs_graph_item`
--
CREATE TABLE `aggregate_graphs_graph_item` (
  `aggregate_graph_id` int(10) unsigned NOT NULL,
  `graph_templates_item_id` int(10) unsigned NOT NULL,
  `sequence` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `color_template` int(11) unsigned NOT NULL,
  `t_graph_type_id` char(2) DEFAULT '',
  `graph_type_id` tinyint(3) NOT NULL DEFAULT '0',
  `t_cdef_id` char(2) DEFAULT '',
  `cdef_id` mediumint(8) unsigned DEFAULT NULL,
  `item_skip` char(2) NOT NULL,
  `item_total` char(2) NOT NULL,
  PRIMARY KEY (`aggregate_graph_id`,`graph_templates_item_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Aggregate Graph Graph Items';


--
-- Table structure for table `aggregate_graphs_items`
--
CREATE TABLE `aggregate_graphs_items` (
  `aggregate_graph_id` int(10) unsigned NOT NULL,
  `local_graph_id` int(10) unsigned NOT NULL,
  `sequence` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`aggregate_graph_id`,`local_graph_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Aggregate Graph Items';


--
-- Table structure for table `automation_devices`
--
CREATE TABLE `automation_devices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `network_id` int(10) unsigned NOT NULL DEFAULT '0',
  `hostname` varchar(100) NOT NULL DEFAULT '',
  `ip` varchar(17) NOT NULL DEFAULT '',
  `snmp_community` varchar(100) NOT NULL DEFAULT '',
  `snmp_version` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `snmp_port` mediumint(5) unsigned NOT NULL DEFAULT '161',
  `snmp_username` varchar(50) DEFAULT NULL,
  `snmp_password` varchar(50) DEFAULT NULL,
  `snmp_auth_protocol` char(6) DEFAULT '',
  `snmp_priv_passphrase` varchar(200) DEFAULT '',
  `snmp_priv_protocol` char(6) DEFAULT '',
  `snmp_context` varchar(64) DEFAULT '',
  `snmp_engine_id` varchar(64) DEFAULT '',
  `sysName` varchar(100) NOT NULL DEFAULT '',
  `sysLocation` varchar(255) NOT NULL DEFAULT '',
  `sysContact` varchar(255) NOT NULL DEFAULT '',
  `sysDescr` varchar(255) NOT NULL DEFAULT '',
  `sysUptime` int(32) NOT NULL DEFAULT '0',
  `os` varchar(64) NOT NULL DEFAULT '',
  `snmp` tinyint(4) NOT NULL DEFAULT '0',
  `known` tinyint(4) NOT NULL DEFAULT '0',
  `up` tinyint(4) NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`),
  KEY `hostname` (`hostname`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Table of Discovered Devices';


--
-- Table structure for table `automation_graph_rule_items`
--
CREATE TABLE `automation_graph_rule_items` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `sequence` smallint(3) unsigned NOT NULL DEFAULT '0',
  `operation` smallint(3) unsigned NOT NULL DEFAULT '0',
  `field` varchar(255) NOT NULL DEFAULT '',
  `operator` smallint(3) unsigned NOT NULL DEFAULT '0',
  `pattern` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Automation Graph Rule Items';

LOCK TABLES `automation_graph_rule_items` WRITE;
INSERT INTO `automation_graph_rule_items` VALUES (1,1,1,0,'ifOperStatus',7,'Up'),(2,1,2,1,'ifIP',16,''),(3,1,3,1,'ifHwAddr',16,''),(4,2,1,0,'ifOperStatus',7,'Up'),(5,2,2,1,'ifIP',16,''),(6,2,3,1,'ifHwAddr',16,'');
UNLOCK TABLES;


--
-- Table structure for table `automation_graph_rules`
--
CREATE TABLE `automation_graph_rules` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `snmp_query_id` smallint(3) unsigned NOT NULL DEFAULT '0',
  `graph_type_id` smallint(3) unsigned NOT NULL DEFAULT '0',
  `enabled` char(2) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `name` (`name`(171))
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Automation Graph Rules';

LOCK TABLES `automation_graph_rules` WRITE;
INSERT INTO `automation_graph_rules` VALUES (1,'Traffic 64 bit Server',1,12,'on'),(2,'Traffic 64 bit Server Linux',1,12,'on'),(3,'Disk Space',3,17,'on');
UNLOCK TABLES;

--
-- Table structure for table `automation_ips`
--
CREATE TABLE `automation_ips` (
  `ip_address` varchar(20) NOT NULL DEFAULT '',
  `hostname` varchar(100) DEFAULT '',
  `network_id` int(10) unsigned DEFAULT NULL,
  `pid` int(10) unsigned DEFAULT NULL,
  `status` int(10) unsigned DEFAULT NULL,
  `thread` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`ip_address`),
  KEY `pid` (`pid`)
) ENGINE=MEMORY COMMENT='List of discoverable ip addresses used for scanning';


--
-- Table structure for table `automation_match_rule_items`
--
CREATE TABLE `automation_match_rule_items` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `rule_type` smallint(3) unsigned NOT NULL DEFAULT '0',
  `sequence` smallint(3) unsigned NOT NULL DEFAULT '0',
  `operation` smallint(3) unsigned NOT NULL DEFAULT '0',
  `field` varchar(255) NOT NULL DEFAULT '',
  `operator` smallint(3) unsigned NOT NULL DEFAULT '0',
  `pattern` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Automation Match Rule Items';
--
-- Dumping data for table `automation_match_rule_items`
--
LOCK TABLES `automation_match_rule_items` WRITE;
INSERT INTO `automation_match_rule_items` VALUES (1,1,1,1,0,'h.snmp_sysDescr',8,''),(2,1,1,2,1,'h.snmp_version',12,'2'),(3,1,3,1,0,'ht.name',1,'Linux'),(4,2,1,1,0,'ht.name',1,'Linux'),(5,2,1,2,1,'h.snmp_version',12,'2'),(6,2,3,1,0,'ht.name',1,'SNMP'),(7,2,3,2,1,'gt.name',1,'Traffic'),(8,1,1,3,1,'h.snmp_sysDescr',2,'Windows');
UNLOCK TABLES;


--
-- Table structure for table `automation_networks`
--
CREATE TABLE `automation_networks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poller_id` int(10) unsigned DEFAULT '1',
  `site_id` int(10) unsigned DEFAULT '1',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT 'The name for this network',
  `subnet_range` varchar(1024) NOT NULL DEFAULT '' COMMENT 'Defined subnet ranges for discovery',
  `dns_servers` varchar(128) NOT NULL DEFAULT '' COMMENT 'DNS Servers to use for name resolution',
  `enabled` char(2) DEFAULT '',
  `notification_enabled` char(2) DEFAULT '',
  `notification_email` varchar(255) DEFAULT '',
  `notification_fromname` varchar(32) DEFAULT '',
  `notification_fromemail` varchar(128) DEFAULT '',
  `snmp_id` int(10) unsigned DEFAULT NULL,
  `enable_netbios` char(2) DEFAULT '',
  `add_to_cacti` char(2) DEFAULT '',
  `same_sysname` char(2) DEFAULT '',
  `total_ips` int(10) unsigned DEFAULT '0',
  `up_hosts` int(10) unsigned NOT NULL DEFAULT '0',
  `snmp_hosts` int(10) unsigned NOT NULL DEFAULT '0',
  `ping_method` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The ping method (ICMP:TCP:UDP)',
  `ping_port` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'For TCP:UDP the port to ping',
  `ping_timeout` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The ping timeout in seconds',
  `ping_retries` int(10) unsigned DEFAULT '0',
  `sched_type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Schedule type: manual or automatic',
  `threads` int(10) unsigned DEFAULT '1',
  `run_limit` int(10) unsigned DEFAULT '0' COMMENT 'The maximum runtime for the discovery',
  `start_at` varchar(20) DEFAULT NULL,
  `next_start` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `recur_every` int(10) unsigned DEFAULT '1',
  `day_of_week` varchar(45) DEFAULT NULL COMMENT 'The days of week to run in crontab format',
  `month` varchar(45) DEFAULT NULL COMMENT 'The months to run in crontab format',
  `day_of_month` varchar(45) DEFAULT NULL COMMENT 'The days of month to run in crontab format',
  `monthly_week` varchar(45) DEFAULT NULL,
  `monthly_day` varchar(45) DEFAULT NULL,
  `last_runtime` double NOT NULL DEFAULT '0' COMMENT 'The last runtime for discovery',
  `last_started` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The time the discovery last started',
  `last_status` varchar(128) NOT NULL DEFAULT '' COMMENT 'The last exit message if any',
  `rerun_data_queries` char(2) DEFAULT NULL COMMENT 'Rerun data queries or not for existing hosts',
  PRIMARY KEY (`id`),
  KEY `poller_id` (`poller_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Stores scanning subnet definitions';
--
-- Dumping data for table `automation_networks`
--
LOCK TABLES `automation_networks` WRITE;
INSERT INTO `automation_networks` VALUES (1,1,0,'Test Network','192.168.1.0/24','','on','','','','',1,'on','on','',254,0,0,1,22,400,1,2,10,1200,'0000-00-00 00:00:00','0000-00-00 00:00:00',2,'4','','','','',0,'0000-00-00 00:00:00','','on');
UNLOCK TABLES;

--
-- Table structure for table `automation_processes`
--
CREATE TABLE `automation_processes` (
  `pid` int(8) unsigned NOT NULL,
  `poller_id` int(10) unsigned DEFAULT '1',
  `network_id` int(10) unsigned NOT NULL DEFAULT '0',
  `task` varchar(20) DEFAULT '',
  `status` varchar(20) DEFAULT NULL,
  `command` varchar(20) DEFAULT NULL,
  `up_hosts` int(10) unsigned DEFAULT '0',
  `snmp_hosts` int(10) unsigned DEFAULT '0',
  `heartbeat` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`pid`,`network_id`)
) ENGINE=MEMORY COMMENT='Table tracking active poller processes';

--
-- Table structure for table `automation_snmp`
--
DROP TABLE IF EXISTS `automation_snmp`;
CREATE TABLE `automation_snmp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Group of SNMP Option Sets';

LOCK TABLES `automation_snmp` WRITE;
INSERT INTO `automation_snmp` VALUES (1,'Default Option Set');
UNLOCK TABLES;


--
-- Table structure for table `automation_snmp_items`
--
CREATE TABLE `automation_snmp_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `snmp_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sequence` int(10) unsigned NOT NULL DEFAULT '0',
  `snmp_version` tinyint(1) unsigned NOT NULL DEFAULT "1",
  `snmp_community` varchar(50) NOT NULL,
  `snmp_port` int(10) NOT NULL DEFAULT '161',
  `snmp_timeout` int(10) unsigned NOT NULL DEFAULT '500',
  `snmp_retries` tinyint(11) unsigned NOT NULL DEFAULT '3',
  `max_oids` int(12) unsigned DEFAULT '10',
  `bulk_walk_size` int(11) DEFAULT '-1',
  `snmp_username` varchar(50) DEFAULT NULL,
  `snmp_password` varchar(50) DEFAULT NULL,
  `snmp_auth_protocol` char(6) DEFAULT '',
  `snmp_priv_passphrase` varchar(200) DEFAULT '',
  `snmp_priv_protocol` char(6) DEFAULT '',
  `snmp_context` varchar(64) DEFAULT '',
  `snmp_engine_id` varchar(64) DEFAULT '',
  PRIMARY KEY (`id`,`snmp_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Set of SNMP Options';

LOCK TABLES `automation_snmp_items` WRITE;
INSERT INTO `automation_snmp_items` VALUES (1,1,1,'2','public',161,1000,3,10,-1,'admin','baseball','MD5','','DES','',''),(2,1,2,'2','private',161,1000,3,10,-1,'admin','baseball','MD5','','DES','','');
UNLOCK TABLES;


--
-- Table structure for table `automation_templates`
--
CREATE TABLE `automation_templates` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `host_template` int(8) NOT NULL DEFAULT '0',
  `availability_method` int(10) unsigned DEFAULT '2',
  `sysDescr` varchar(255) DEFAULT '',
  `sysName` varchar(255) DEFAULT '',
  `sysOid` varchar(60) DEFAULT '',
  `sequence` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Templates of SNMP Sys variables used for automation';

LOCK TABLES `automation_templates` WRITE;
INSERT INTO `automation_templates` VALUES (1,3,2,'Linux','','',2),(2,1,2,'HP ETHERNET','','',1);
UNLOCK TABLES;


--
-- Table structure for table `automation_tree_rule_items`
--
CREATE TABLE `automation_tree_rule_items` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `rule_id` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `sequence` smallint(3) unsigned NOT NULL DEFAULT 0,
  `field` varchar(255) NOT NULL DEFAULT '',
  `sort_type` smallint(3) unsigned NOT NULL DEFAULT 0,
  `propagate_changes` char(2) DEFAULT '',
  `search_pattern` varchar(255) NOT NULL DEFAULT '',
  `replace_pattern` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Automation Tree Rule Items';

LOCK TABLES `automation_tree_rule_items` WRITE;
INSERT INTO `automation_tree_rule_items` VALUES (1,1,1,'ht.name',1,'','^(.*)\\s*Linux\\s*(.*)$','${1}\\n${2}'),(2,1,2,'h.hostname',1,'','^(\\w*)\\s*(\\w*)\\s*(\\w*).*$',''),(3,2,1,'0',2,'on','Traffic',''),(4,2,2,'gtg.title_cache',1,'','^(.*)\\s*-\\s*Traffic -\\s*(.*)$','${1}\\n${2}');
UNLOCK TABLES;


--
-- Table structure for table `automation_tree_rules`
--
DROP TABLE IF EXISTS `automation_tree_rules`;
CREATE TABLE `automation_tree_rules` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255)  NOT NULL DEFAULT '',
  `tree_id` smallint(3) unsigned NOT NULL DEFAULT '0',
  `tree_item_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `leaf_type` smallint(3) unsigned NOT NULL DEFAULT '0',
  `host_grouping_type` smallint(3) unsigned NOT NULL DEFAULT '0',
  `enabled` char(2) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `name` (`name`(171))
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Automation Tree Rules';

LOCK TABLES `automation_tree_rules` WRITE;
INSERT INTO `automation_tree_rules` VALUES (1,'New Device',1,0,3,1,'on'),(2,'New Graph',1,0,2,1,'');
UNLOCK TABLES;


--
-- Alter table `cdef`
--
ALTER TABLE `cdef`
    ADD COLUMN `system` mediumint(8) unsigned NOT NULL DEFAULT '0' AFTER `hash`,
    ADD INDEX `hash` (`hash`),
    ADD INDEX `name` (`name`(171));
ALTER TABLE `cdef` ENGINE=InnoDB;

LOCK TABLES `cdef` WRITE;
UPDATE cdef SET `system` = 1 WHERE `name` LIKE '\_%';
UNLOCK TABLES;


--
-- Alter table `cdef_items`
--
ALTER TABLE `cdef_items`
    DROP INDEX `cdef_id`,
    ADD INDEX `cdef_id_sequence` (`cdef_id`,`sequence`);
ALTER TABLE `cdef_items` ENGINE=InnoDB;


--
-- Table structure for table `color_template_items`
--
DROP TABLE IF EXISTS `color_template_items`;
CREATE TABLE `color_template_items` (
  `color_template_item_id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `color_template_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `color_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `sequence` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`color_template_item_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Color Items for Color Templates';

LOCK TABLES `color_template_items` WRITE;
INSERT INTO `color_template_items` VALUES (1,1,4,1),(2,1,24,2),(3,1,98,3),(4,1,25,4),(5,2,25,1),(6,2,29,2),(7,2,30,3),(8,2,31,4),(9,2,33,5),(10,2,35,6),(11,2,41,7),(12,2,9,8),(13,3,15,1),(14,3,31,2),(15,3,28,3),(16,3,8,4),(17,3,34,5),(18,3,33,6),(19,3,35,7),(20,3,41,8),(21,3,36,9),(22,3,42,10),(23,3,44,11),(24,3,48,12),(25,3,9,13),(26,3,49,14),(27,3,51,15),(28,3,52,16),(29,4,76,1),(30,4,84,2),(31,4,89,3),(32,4,17,4),(33,4,86,5),(34,4,88,6),(35,4,90,7),(36,4,94,8),(37,4,96,9),(38,4,93,10),(39,4,91,11),(40,4,22,12),(41,4,12,13),(42,4,95,14),(43,4,6,15),(44,4,92,16);
UNLOCK TABLES;


--
-- Table structure for table `color_templates`
--
RENAME TABLE `plugin_aggregate_color_templates` TO `color_templates`;


--
-- Alter table `colors`
--
ALTER TABLE `colors`
    ADD COLUMN `name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT '' AFTER `id`,
    ADD COLUMN `read_only` char(2) COLLATE utf8mb4_unicode_ci DEFAULT '' AFTER `hex`,
    ADD UNIQUE INDEX `hex` (`hex`);
ALTER TABLE `colors` ENGINE=InnoDB;

LOCK TABLES `colors` WRITE;
REPLACE INTO `colors` VALUES (1,'Black','000000','on'),(2,'White','FFFFFF','on'),(4,'','FAFD9E',''),(5,'Silver','C0C0C0','on'),(6,'','74C366',''),(7,'','6DC8FE',''),(8,'','EA8F00',''),(9,'Red','FF0000','on'),(10,'','4444FF',''),(11,'Magenta','FF00FF','on'),(12,'Lime Green','00FF00','on'),(13,'','8D85F3',''),(14,'','AD3B6E',''),(15,'','EACC00',''),(16,'','12B3B5',''),(17,'','157419',''),(18,'','C4FD3D',''),(19,'','817C4E',''),(20,'','002A97',''),(21,'Blue','0000FF','on'),(22,'','00CF00',''),(24,'','F9FD5F',''),(25,'','FFF200',''),(26,'','CCBB00',''),(27,'','837C04',''),(28,'','EAAF00',''),(29,'','FFD660',''),(30,'','FFC73B',''),(31,'','FFAB00',''),(33,'','FF7D00',''),(34,'','ED7600',''),(35,'','FF5700',''),(36,'','EE5019',''),(37,'','B1441E',''),(38,'','FFC3C0',''),(39,'','FF897C',''),(40,'','FF6044',''),(41,'','FF4105',''),(42,'','DA4725',''),(43,'','942D0C',''),(44,'','FF3932',''),(45,'','862F2F',''),(46,'','FF5576',''),(47,'','562B29',''),(48,'','F51D30',''),(49,'','DE0056',''),(50,'','ED5394',''),(51,'','B90054',''),(52,'','8F005C',''),(53,'','F24AC8',''),(54,'','E8CDEF',''),(55,'','D8ACE0',''),(56,'','A150AA',''),(57,'','750F7D',''),(58,'','8D00BA',''),(59,'','623465',''),(60,'','55009D',''),(61,'','3D168B',''),(62,'','311F4E',''),(63,'','D2D8F9',''),(64,'','9FA4EE',''),(65,'','6557D0',''),(66,'','4123A1',''),(67,'','4668E4',''),(68,'','0D006A',''),(69,'','00004D',''),(70,'','001D61',''),(71,'','00234B',''),(72,'','002A8F',''),(73,'','2175D9',''),(74,'','7CB3F1',''),(75,'','005199',''),(76,'','004359',''),(77,'','00A0C1',''),(78,'','007283',''),(79,'','00BED9',''),(80,'','AFECED',''),(81,'','55D6D3',''),(82,'','00BBB4',''),(83,'','009485',''),(84,'','005D57',''),(85,'','008A77',''),(86,'','008A6D',''),(87,'','00B99B',''),(88,'','009F67',''),(89,'','00694A',''),(90,'','00A348',''),(91,'','00BF47',''),(92,'','96E78A',''),(93,'','00BD27',''),(94,'','35962B',''),(95,'','7EE600',''),(96,'','6EA100',''),(97,'','CAF100',''),(98,'','F5F800',''),(99,'','CDCFC4',''),(100,'','BCBEB3',''),(101,'','AAABA1',''),(102,'','8F9286',''),(103,'','797C6E',''),(104,'','2E3127',''),(105,'Night','0C090A','on'),(106,'Gunmetal','2C3539','on'),(107,'Midnight','2B1B17','on'),(108,'Charcoal','34282C','on'),(109,'Dark Slate Grey','25383C','on'),(110,'Oil','3B3131','on'),(111,'Black Cat','413839','on'),(112,'Iridium','3D3C3A','on'),(113,'Black Eel','463E3F','on'),(114,'Black Cow','4C4646','on'),(115,'Gray Wolf','504A4B','on'),(116,'Vampire Gray','565051','on'),(117,'Gray Dolphin','5C5858','on'),(118,'Carbon Gray','625D5D','on'),(119,'Ash Gray','666362','on'),(120,'Cloudy Gray','6D6968','on'),(121,'Smokey Gray','726E6D','on'),(122,'Gray','736F6E','on'),(123,'Granite','837E7C','on'),(124,'Battleship Gray','848482','on'),(125,'Gray Cloud','B6B6B4','on'),(126,'Gray Goose','D1D0CE','on'),(127,'Platinum','E5E4E2','on'),(128,'Metallic Silver','BCC6CC','on'),(129,'Blue Gray','98AFC7','on'),(130,'Light Slate Gray','6D7B8D','on'),(131,'Slate Gray','657383','on'),(132,'Jet Gray','616D7E','on'),(133,'Mist Blue','646D7E','on'),(134,'Marble Blue','566D7E','on'),(135,'Slate Blue','737CA1','on'),(136,'Steel Blue','4863A0','on'),(137,'Blue Jay','2B547E','on'),(138,'Dark Slate Blue','2B3856','on'),(139,'Midnight Blue','151B54','on'),(140,'Navy Blue','000080','on'),(141,'Blue Whale','342D7E','on'),(142,'Lapis Blue','15317E','on'),(143,'Cornflower Blue','151B8D','on'),(144,'Earth Blue','0000A0','on'),(145,'Cobalt Blue','0020C2','on'),(146,'Blueberry Blue','0041C2','on'),(147,'Sapphire Blue','2554C7','on'),(148,'Blue Eyes','1569C7','on'),(149,'Royal Blue','2B60DE','on'),(150,'Blue Orchid','1F45FC','on'),(151,'Blue Lotus','6960EC','on'),(152,'Light Slate Blue','736AFF','on'),(153,'Slate Blue','357EC7','on'),(154,'Glacial Blue Ice','368BC1','on'),(155,'Silk Blue','488AC7','on'),(156,'Blue Ivy','3090C7','on'),(157,'Blue Koi','659EC7','on'),(158,'Columbia Blue','87AFC7','on'),(159,'Baby Blue','95B9C7','on'),(160,'Light Steel Blue','728FCE','on'),(161,'Ocean Blue','2B65EC','on'),(162,'Blue Ribbon','306EFF','on'),(163,'Blue Dress','157DEC','on'),(164,'Dodger Blue','1589FF','on'),(165,'Cornflower Blue','6495ED','on'),(166,'Sky Blue','6698FF','on'),(167,'Butterfly Blue','38ACEC','on'),(168,'Iceberg','56A5EC','on'),(169,'Crystal Blue','5CB3FF','on'),(170,'Deep Sky Blue','3BB9FF','on'),(171,'Denim Blue','79BAEC','on'),(172,'Light Sky Blue','82CAFA','on'),(173,'Day Sky Blue','82CAFF','on'),(174,'Jeans Blue','A0CFEC','on'),(175,'Blue Angel','B7CEEC','on'),(176,'Pastel Blue','B4CFEC','on'),(177,'Sea Blue','C2DFFF','on'),(178,'Powder Blue','C6DEFF','on'),(179,'Coral Blue','AFDCEC','on'),(180,'Light Blue','ADDFFF','on'),(181,'Robin Egg Blue','BDEDFF','on'),(182,'Pale Blue Lily','CFECEC','on'),(183,'Light Cyan','E0FFFF','on'),(184,'Water','EBF4FA','on'),(185,'Alice Blue','F0F8FF','on'),(186,'Azure','F0FFFF','on'),(187,'Light Slate','CCFFFF','on'),(188,'Light Aquamarine','93FFE8','on'),(189,'Electric Blue','9AFEFF','on'),(190,'Aquamarine','7FFFD4','on'),(191,'Cyan or Aqua','00FFFF','on'),(192,'Tron Blue','7DFDFE','on'),(193,'Blue Zircon','57FEFF','on'),(194,'Blue Lagoon','8EEBEC','on'),(195,'Celeste','50EBEC','on'),(196,'Blue Diamond','4EE2EC','on'),(197,'Tiffany Blue','81D8D0','on'),(198,'Cyan Opaque','92C7C7','on'),(199,'Blue Hosta','77BFC7','on'),(200,'Northern Lights Blue','78C7C7','on'),(201,'Medium Turquoise','48CCCD','on'),(202,'Turquoise','43C6DB','on'),(203,'Jellyfish','46C7C7','on'),(204,'Macaw Blue Green','43BFC7','on'),(205,'Light Sea Green','3EA99F','on'),(206,'Dark Turquoise','3B9C9C','on'),(207,'Sea Turtle Green','438D80','on'),(208,'Medium Aquamarine','348781','on'),(209,'Greenish Blue','307D7E','on'),(210,'Grayish Turquoise','5E7D7E','on'),(211,'Beetle Green','4C787E','on'),(212,'Teal','008080','on'),(213,'Sea Green','4E8975','on'),(214,'Camouflage Green','78866B','on'),(215,'Sage Green','848b79','on'),(216,'Hazel Green','617C58','on'),(217,'Venom Green','728C00','on'),(218,'Fern Green','667C26','on'),(219,'Dark Forrest Green','254117','on'),(220,'Medium Sea Green','306754','on'),(221,'Medium Forest Green','347235','on'),(222,'Seaweed Green','437C17','on'),(223,'Pine Green','387C44','on'),(224,'Jungle Green','347C2C','on'),(225,'Shamrock Green','347C17','on'),(226,'Medium Spring Green','348017','on'),(227,'Forest Green','4E9258','on'),(228,'Green Onion','6AA121','on'),(229,'Spring Green','4AA02C','on'),(230,'Lime Green','41A317','on'),(231,'Clover Green','3EA055','on'),(232,'Green Snake','6CBB3C','on'),(233,'Alien Green','6CC417','on'),(234,'Green Apple','4CC417','on'),(235,'Yellow Green','52D017','on'),(236,'Kelly Green','4CC552','on'),(237,'Zombie Green','54C571','on'),(238,'Frog Green','99C68E','on'),(239,'Green Peas','89C35C','on'),(240,'Dollar Bill Green','85BB65','on'),(241,'Dark Sea Green','8BB381','on'),(242,'Iguana Green','9CB071','on'),(243,'Avocado Green','B2C248','on'),(244,'Pistachio Green','9DC209','on'),(245,'Salad Green','A1C935','on'),(246,'Hummingbird Green','7FE817','on'),(247,'Nebula Green','59E817','on'),(248,'Stoplight Go Green','57E964','on'),(249,'Algae Green','64E986','on'),(250,'Jade Green','5EFB6E','on'),(251,'Emerald Green','5FFB17','on'),(252,'Lawn Green','87F717','on'),(253,'Chartreuse','8AFB17','on'),(254,'Dragon Green','6AFB92','on'),(255,'Mint Green','98FF98','on'),(256,'Green Thumb','B5EAAA','on'),(257,'Light Jade','C3FDB8','on'),(258,'Tea Green','CCFB5D','on'),(259,'Green Yellow','B1FB17','on'),(260,'Slime Green','BCE954','on'),(261,'Goldenrod','EDDA74','on'),(262,'Harvest Gold','EDE275','on'),(263,'Sun Yellow','FFE87C','on'),(264,'Yellow','FFFF00','on'),(265,'Corn Yellow','FFF380','on'),(266,'Parchment','FFFFC2','on'),(267,'Cream','FFFFCC','on'),(268,'Lemon Chiffon','FFF8C6','on'),(269,'Cornsilk','FFF8DC','on'),(270,'Beige','F5F5DC','on'),(271,'Blonde','FBF6D9','on'),(272,'Antique White','FAEBD7','on'),(273,'Champagne','F7E7CE','on'),(274,'Blanched Almond','FFEBCD','on'),(275,'Vanilla','F3E5AB','on'),(276,'Tan Brown','ECE5B6','on'),(277,'Peach','FFE5B4','on'),(278,'Mustard','FFDB58','on'),(279,'Rubber Ducky Yellow','FFD801','on'),(280,'Bright Gold','FDD017','on'),(281,'Golden Brown','EAC117','on'),(282,'Macaroni and Cheese','F2BB66','on'),(283,'Saffron','FBB917','on'),(284,'Beer','FBB117','on'),(285,'Cantaloupe','FFA62F','on'),(286,'Bee Yellow','E9AB17','on'),(287,'Brown Sugar','E2A76F','on'),(288,'Burly Wood','DEB887','on'),(289,'Deep Peach','FFCBA4','on'),(290,'Ginger Brown','C9BE62','on'),(291,'School Bus Yellow','E8A317','on'),(292,'Sandy Brown','EE9A4D','on'),(293,'Fall Leaf Brown','C8B560','on'),(294,'Orange Gold','D4A017','on'),(295,'Sand','C2B280','on'),(296,'Cookie Brown','C7A317','on'),(297,'Caramel','C68E17','on'),(298,'Brass','B5A642','on'),(299,'Khaki','ADA96E','on'),(300,'Camel Brown','C19A6B','on'),(301,'Bronze','CD7F32','on'),(302,'Tiger Orange','C88141','on'),(303,'Cinnamon','C58917','on'),(304,'Bullet Shell','AF9B60','on'),(305,'Dark Goldenrod','AF7817','on'),(306,'Copper','B87333','on'),(307,'Wood','966F33','on'),(308,'Oak Brown','806517','on'),(309,'Moccasin','827839','on'),(310,'Army Brown','827B60','on'),(311,'Sandstone','786D5F','on'),(312,'Mocha','493D26','on'),(313,'Taupe','483C32','on'),(314,'Coffee','6F4E37','on'),(315,'Brown Bear','835C3B','on'),(316,'Red Dirt','7F5217','on'),(317,'Sepia','7F462C','on'),(318,'Orange Salmon','C47451','on'),(319,'Rust','C36241','on'),(320,'Red Fox','C35817','on'),(321,'Chocolate','C85A17','on'),(322,'Sedona','CC6600','on'),(323,'Papaya Orange','E56717','on'),(324,'Halloween Orange','E66C2C','on'),(325,'Pumpkin Orange','F87217','on'),(326,'Construction Cone Orange','F87431','on'),(327,'Sunrise Orange','E67451','on'),(328,'Mango Orange','FF8040','on'),(329,'Dark Orange','F88017','on'),(330,'Coral','FF7F50','on'),(331,'Basket Ball Orange','F88158','on'),(332,'Light Salmon','F9966B','on'),(333,'Tangerine','E78A61','on'),(334,'Dark Salmon','E18B6B','on'),(335,'Light Coral','E77471','on'),(336,'Bean Red','F75D59','on'),(337,'Valentine Red','E55451','on'),(338,'Shocking Orange','E55B3C','on'),(339,'Scarlet','FF2400','on'),(340,'Ruby Red','F62217','on'),(341,'Ferrari Red','F70D1A','on'),(342,'Fire Engine Red','F62817','on'),(343,'Lava Red','E42217','on'),(344,'Love Red','E41B17','on'),(345,'Grapefruit','DC381F','on'),(346,'Chestnut Red','C34A2C','on'),(347,'Cherry Red','C24641','on'),(348,'Mahogany','C04000','on'),(349,'Chilli Pepper','C11B17','on'),(350,'Cranberry','9F000F','on'),(351,'Red Wine','990012','on'),(352,'Burgundy','8C001A','on'),(353,'Chestnut','954535','on'),(354,'Blood Red','7E3517','on'),(355,'Sienna','8A4117','on'),(356,'Sangria','7E3817','on'),(357,'Firebrick','800517','on'),(358,'Maroon','810541','on'),(359,'Plum Pie','7D0541','on'),(360,'Velvet Maroon','7E354D','on'),(361,'Plum Velvet','7D0552','on'),(362,'Rosy Finch','7F4E52','on'),(363,'Puce','7F5A58','on'),(364,'Dull Purple','7F525D','on'),(365,'Rosy Brown','B38481','on'),(366,'Khaki Rose','C5908E','on'),(367,'Pink Bow','C48189','on'),(368,'Lipstick Pink','C48793','on'),(369,'Rose','E8ADAA','on'),(370,'Desert Sand','EDC9AF','on'),(371,'Pig Pink','FDD7E4','on'),(372,'Cotton Candy','FCDFFF','on'),(373,'Pink Bubblegum','FFDFDD','on'),(374,'Misty Rose','FBBBB9','on'),(375,'Pink','FAAFBE','on'),(376,'Light Pink','FAAFBA','on'),(377,'Flamingo Pink','F9A7B0','on'),(378,'Pink Rose','E7A1B0','on'),(379,'Pink Daisy','E799A3','on'),(380,'Cadillac Pink','E38AAE','on'),(381,'Carnation Pink','F778A1','on'),(382,'Blush Red','E56E94','on'),(383,'Hot Pink','F660AB','on'),(384,'Watermelon Pink','FC6C85','on'),(385,'Violet Red','F6358A','on'),(386,'Deep Pink','F52887','on'),(387,'Pink Cupcake','E45E9D','on'),(388,'Pink Lemonade','E4287C','on'),(389,'Neon Pink','F535AA','on'),(390,'Dimorphotheca Magenta','E3319D','on'),(391,'Bright Neon Pink','F433FF','on'),(392,'Pale Violet Red','D16587','on'),(393,'Tulip Pink','C25A7C','on'),(394,'Medium Violet Red','CA226B','on'),(395,'Rogue Pink','C12869','on'),(396,'Burnt Pink','C12267','on'),(397,'Bashful Pink','C25283','on'),(398,'Carnation Pink','C12283','on'),(399,'Plum','B93B8F','on'),(400,'Viola Purple','7E587E','on'),(401,'Purple Iris','571B7E','on'),(402,'Plum Purple','583759','on'),(403,'Indigo','4B0082','on'),(404,'Purple Monster','461B7E','on'),(405,'Purple Haze','4E387E','on'),(406,'Eggplant','614051','on'),(407,'Grape','5E5A80','on'),(408,'Purple Jam','6A287E','on'),(409,'Dark Orchid','7D1B7E','on'),(410,'Purple Flower','A74AC7','on'),(411,'Medium Orchid','B048B5','on'),(412,'Purple Amethyst','6C2DC7','on'),(413,'Dark Violet','842DCE','on'),(414,'Violet','8D38C9','on'),(415,'Purple Sage Bush','7A5DC7','on'),(416,'Lovely Purple','7F38EC','on'),(417,'Purple','8E35EF','on'),(418,'Aztech Purple','893BFF','on'),(419,'Medium Purple','8467D7','on'),(420,'Jasmine Purple','A23BEC','on'),(421,'Purple Daffodil','B041FF','on'),(422,'Tyrian Purple','C45AEC','on'),(423,'Crocus Purple','9172EC','on'),(424,'Purple Mimosa','9E7BFF','on'),(425,'Heliotrope Purple','D462FF','on'),(426,'Crimson','E238EC','on'),(427,'Purple Dragon','C38EC7','on'),(428,'Lilac','C8A2C8','on'),(429,'Blush Pink','E6A9EC','on'),(430,'Mauve','E0B0FF','on'),(431,'Wisteria Purple','C6AEC7','on'),(432,'Blossom Pink','F9B7FF','on'),(433,'Thistle','D2B9D3','on'),(434,'Periwinkle','E9CFEC','on'),(435,'Lavender Pinocchio','EBDDE2','on'),(436,'Lavender Blue','E3E4FA','on'),(437,'Pearl','FDEEF4','on'),(438,'SeaShell','FFF5EE','on'),(439,'Milk White','FEFCFF','on'),(440,'Green','008000','on'),(441,'Olive','808000','on'),(442,'Grey','808080','on'),(443,'Purple','800080','on'),(444,'Maroon','800000','on');
UNLOCK TABLES;


--
-- Table structure for table `data_debug`
--
DROP TABLE IF EXISTS `data_debug`;
CREATE TABLE `data_debug` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `started` int(11) NOT NULL default '0',
  `done` int(11) NOT NULL default '0',
  `user` int(11) NOT NULL default '0',
  `datasource` int(11) NOT NULL default '0',
  `info` text NOT NULL default '',
  `issue` text NOT NULL NULL default '',
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `done` (`done`),
  KEY `datasource` (`datasource`),
  KEY `started` (`started`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Datasource Debugger Information';


--
-- Alter table `data_input`
--
ALTER TABLE `data_input`
	MODIFY `input_string` varchar(512) default NULL,
	DROP KEY `name`,
	ADD KEY `name_type_id` (`name`(171), `type_id`);
ALTER TABLE `data_input` ENGINE=InnoDB;


--
-- Alter table `data_input_data`
--
ALTER TABLE `data_input_data`
	MODIFY `data_template_data_id` int(10) unsigned NOT NULL default '0',
	ADD INDEX `data_template_data_id` (`data_template_data_id`),
ENGINE=InnoDB;
 
DELETE FROM `data_input_data` WHERE `data_input_field_id`= 0;


--
-- Alter table `data_input_fields`
--
ALTER TABLE `data_input_fields`
  MODIFY COLUMN `update_rra` char(2) DEFAULT '0',
  MODIFY COLUMN `type_code` varchar(40) DEFAULT '',
  MODIFY COLUMN `allow_nulls` char(2) DEFAULT '',
	DROP INDEX `type_code`,
	ADD INDEX `input_output` (`input_output`),
  ADD INDEX `type_code_data_input_id` (`type_code`, `data_input_id`),
  -- USING 'BTREE'
ENGINE=InnoDB;


--
-- Alter table `data_local`
--
ALTER TABLE `data_local`
	ADD COLUMN `orphan` tinyint(1) unsigned NOT NULL default '0' AFTER `snmp_index`,
	ADD INDEX `data_template_id` (`data_template_id`),
  ADD INDEX `snmp_query_id` (`snmp_query_id`),
  ADD INDEX `snmp_index` (`snmp_index`),
  ADD INDEX `host_id_snmp_query_id` (`host_id`, `snmp_query_id`),
	ENGINE=InnoDB,
  ROW_FORMAT=DYNAMIC;


--
-- Table structure for table `data_source_profiles`
--
CREATE TABLE `data_source_profiles` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `hash` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `step` int(10) unsigned NOT NULL DEFAULT '300',
  `heartbeat` int(10) unsigned NOT NULL DEFAULT '600',
  `x_files_factor` double DEFAULT '0.5',
  `default` char(2) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `name` (`name`(171))
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Stores Data Source Profiles';
--
-- Dumping data for table `data_source_profiles`
--
LOCK TABLES `data_source_profiles` WRITE;
REPLACE INTO `data_source_profiles` VALUES (1,'d62c52891f4f9688729a5bc9fad91b18','5 Minute Collection',300,600,0.5,'on'), (2,'c0dd0e46b9ca268e7ed4162d329f9215','30 Second Collection',30,1200,0.5,''), (3,'66d35da8f75c912ede3dbe901fedcae0','1 Minute Collection',60,600,0.5,'');
UNLOCK TABLES;


--
-- Table structure for table `data_source_profiles_cf`
--
DROP TABLE IF EXISTS `data_source_profiles_cf`;
CREATE TABLE `data_source_profiles_cf` (
  `data_source_profile_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `consolidation_function_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`data_source_profile_id`,`consolidation_function_id`),
  KEY `data_source_profile_id` (`data_source_profile_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Maps the Data Source Profile Consolidation Functions';

INSERT INTO `data_source_profiles_cf` VALUES (1,1), (1,2), (1,3), (1,4), (2,1), (2,2), (2,3), (2,4), (3,1), (3,2), (3,3), (3,4);


--
-- Table structure for table `data_source_profiles_rra`
--
DROP TABLE IF EXISTS `data_source_profiles_rra`;
CREATE TABLE `data_source_profiles_rra` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `data_source_profile_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `steps` int(10) unsigned DEFAULT '1',
  `rows` int(10) unsigned NOT NULL DEFAULT '700',
  `timespan` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `data_source_profile_id` (`data_source_profile_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Stores RRA Definitions for Data Source Profiles';

INSERT INTO `data_source_profiles_rra` VALUES (1,1,'Daily (5 Minute Average)',1,600,86400), (2,1,'Weekly (30 Minute Average)',6,700,604800), (3,1,'Monthly (2 Hour Average)',24,775,2618784), (4,1,'Yearly (1 Day Average)',288,797,31536000), (5,2,'Daily (30 Second Average)',1,2900,86400), (6,2,'Weekly (15 Minute Average)',30,1346,604800), (7,2,'Monthly (1 Hour Average)',120,1445,2618784), (8,2,'Yearly (4 Hour Average)',480,4380,31536000), (9,3,'Daily (1 Minute Average)',1,2900,86400), (10,3,'Weekly (15 Minute Average)',15,1440,604800), (11,3,'Monthly (1 Hour Average)',60,8784,2618784), (12,3,'Yearly (12 Hour Average)',720,7305,31536000);


--
-- Table structure for table `data_source_purge_action`
--
DROP TABLE IF EXISTS `data_source_purge_action`;
CREATE TABLE `data_source_purge_action` (
  `id` integer UNSIGNED auto_increment,
  `name` varchar(128) NOT NULL default '',
  `local_data_id` int(10) unsigned NOT NULL default '0',
  `action` tinyint(2) NOT NULL default 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='RRD Cleaner File Actions';


--
-- Table structure for table `data_source_purge_temp`
--
DROP TABLE IF EXISTS `data_source_purge_temp`;
CREATE TABLE `data_source_purge_temp` (
  `id` integer UNSIGNED auto_increment,
  `name_cache` varchar(255) NOT NULL default '',
  `local_data_id` int(10) unsigned NOT NULL default '0',
  `name` varchar(128) NOT NULL default '',
  `size` integer UNSIGNED NOT NULL default '0',
  `last_mod` TIMESTAMP NOT NULL default '0000-00-00 00:00:00',
  `in_cacti` tinyint NOT NULL default '0',
  `data_template_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY name (`name`),
  KEY `local_data_id` (`local_data_id`),
  KEY `in_cacti` (`in_cacti`),
  KEY `data_template_id` (`data_template_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='RRD Cleaner File Repository';


--
-- Table structure for table `data_source_stats_daily`
--
DROP TABLE IF EXISTS `data_source_stats_daily`;
CREATE TABLE `data_source_stats_daily` (
  `local_data_id` int(10) unsigned NOT NULL,
  `rrd_name` varchar(19) NOT NULL,
  `average` DOUBLE DEFAULT NULL,
  `peak` DOUBLE DEFAULT NULL,
  PRIMARY KEY (`local_data_id`,`rrd_name`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic;


--
-- Table structure for table `data_source_stats_hourly`
--
DROP TABLE IF EXISTS `data_source_stats_hourly`;
CREATE TABLE `data_source_stats_hourly` (
  `local_data_id` int(10) unsigned NOT NULL,
  `rrd_name` varchar(19) NOT NULL,
  `average` DOUBLE DEFAULT NULL,
  `peak` DOUBLE DEFAULT NULL,
  PRIMARY KEY (`local_data_id`,`rrd_name`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic;


--
-- Table structure for table `data_source_stats_hourly_cache`
--
DROP TABLE IF EXISTS `data_source_stats_hourly_cache`;
CREATE TABLE `data_source_stats_hourly_cache` (
  `local_data_id` int(10) unsigned NOT NULL,
  `rrd_name` varchar(19) NOT NULL,
  `time` timestamp NOT NULL default '0000-00-00 00:00:00',
  `value` DOUBLE DEFAULT NULL,
  PRIMARY KEY (`local_data_id`,`time`,`rrd_name`),
  KEY `time` USING BTREE (`time`)
) ENGINE=MEMORY;


--
-- Table structure for table `data_source_stats_hourly_last`
--
DROP TABLE IF EXISTS `data_source_stats_hourly_last`;
CREATE TABLE `data_source_stats_hourly_last` (
  `local_data_id` int(10) unsigned NOT NULL,
  `rrd_name` varchar(19) NOT NULL,
  `value` DOUBLE DEFAULT NULL,
  `calculated` DOUBLE DEFAULT NULL,
  PRIMARY KEY (`local_data_id`,`rrd_name`)
) ENGINE=MEMORY;


--
-- Table structure for table `data_source_stats_monthly`
--
DROP TABLE IF EXISTS `data_source_stats_monthly`;
CREATE TABLE `data_source_stats_monthly` (
  `local_data_id` int(10) unsigned NOT NULL,
  `rrd_name` varchar(19) NOT NULL,
  `average` DOUBLE DEFAULT NULL,
  `peak` DOUBLE DEFAULT NULL,
  PRIMARY KEY (`local_data_id`,`rrd_name`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic;


--
-- Table structure for table `data_source_stats_weekly`
--
DROP TABLE IF EXISTS `data_source_stats_weekly`;
CREATE TABLE `data_source_stats_weekly` (
  `local_data_id` int(10) unsigned NOT NULL,
  `rrd_name` varchar(19) NOT NULL,
  `average` DOUBLE DEFAULT NULL,
  `peak` DOUBLE DEFAULT NULL,
  PRIMARY KEY (`local_data_id`,`rrd_name`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic;


--
-- Table structure for table `data_source_stats_yearly`
--
DROP TABLE IF EXISTS `data_source_stats_yearly`;
CREATE TABLE `data_source_stats_yearly` (
  `local_data_id` int(10) unsigned NOT NULL,
  `rrd_name` varchar(19) NOT NULL,
  `average` DOUBLE DEFAULT NULL,
  `peak` DOUBLE DEFAULT NULL,
  PRIMARY KEY (`local_data_id`,`rrd_name`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic;


--
-- Alter table `data_template`
--
ALTER TABLE `data_template`
	ADD KEY `name` (`name`),
	ENGINE=InnoDB;


--
-- Alter table `data_template_data`
--
ALTER TABLE `data_template_data`
	ADD COLUMN `t_data_source_profile_id` char(2) default '',
  ADD COLUMN `data_source_profile_id` mediumint(8) unsigned NOT NULL default '0',
	MODIFY COLUMN `local_data_template_data_id` int(10) unsigned NOT NULL default '0',
	MODIFY COLUMN `local_data_id` int(10) unsigned NOT NULL default '0',
	MODIFY COLUMN `data_source_path` varchar(255) default '',
	MODIFY COLUMN `t_active` char(2) default '',
	MODIFY COLUMN `t_rrd_step` char(2) default '',
  MODIFY COLUMN `t_name` char(2) DEFAULT '',
	DROP COLUMN `t_rra_id`,
	ADD KEY `name_cache` (`name_cache`),
ENGINE=InnoDB;


--
-- Alter table `data_template_rrd`
--
ALTER TABLE `data_template_rrd`
  MODIFY COLUMN `id` int(10) unsigned NOT NULL auto_increment,
  MODIFY COLUMN `local_data_template_rrd_id` int(10) unsigned NOT NULL default '0',
  MODIFY COLUMN `local_data_id` int(10) unsigned NOT NULL default '0',
  MODIFY COLUMN `t_rrd_maximum` char(2) DEFAULT '',
  MODIFY COLUMN `t_rrd_minimum` char(2) DEFAULT '',
  MODIFY COLUMN `t_rrd_heartbeat` char(2) DEFAULT '',
  MODIFY COLUMN `t_data_source_type_id` char(2) DEFAULT '',
  MODIFY COLUMN `t_data_source_name` char(2) DEFAULT '',
  MODIFY COLUMN `t_data_input_field_id` char(2) DEFAULT '',
  -- ADD INDEX `local_data_template_rrd_id` (`local_data_template_rrd_id`) USING BTREE,
ENGINE=InnoDB;

UPDATE `data_template_rrd`
  SET `rrd_maximum`='U'
  WHERE `rrd_maximum` = '0' AND `rrd_minimum` = '0' AND `data_source_type_id` IN(1,4);

UPDATE `data_template_rrd`
  SET `rrd_maximum`='U', `rrd_minimum`='U'
  WHERE (`rrd_maximum` = '0' OR `rrd_minimum` = '0') AND `data_source_type_id` IN(3,7);


--
-- Table structure for table `external_links`
--
DROP TABLE IF EXISTS `external_links`;
CREATE TABLE `external_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sortorder` int(11) NOT NULL DEFAULT '0',
  `enabled` char(2) DEFAULT 'on',
  `contentfile` varchar(255) NOT NULL default '',
  `title` varchar(20) NOT NULL default '',
  `style` varchar(10) NOT NULL DEFAULT '',
  `extendedstyle` varchar(50) NOT NULL DEFAULT '',
  `refresh` int unsigned default NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Contains external links that are embedded into Cacti';

--
-- Alter table `graph_local`
--
ALTER TABLE `graph_local`
  ADD COLUMN `snmp_query_graph_id` mediumint(8) NOT NULL default '0' AFTER `snmp_query_id`,
  MODIFY COLUMN `id` int(10) unsigned NOT NULL auto_increment,
  MODIFY COLUMN `snmp_query_id` mediumint(8) NOT NULL DEFAULT '0',
  -- ADD INDEX `graph_template_id` (`graph_template_id`),
  -- ADD INDEX `snmp_index` (`snmp_index`),
  -- ADD INDEX `snmp_query_id` (`snmp_query_id`),
  ENGINE=InnoDB,
  ROW_FORMAT=DYNAMIC;


--
-- Alter table `graph_template_input`
--
ALTER TABLE `graph_template_input_defs`
  MODIFY COLUMN `graph_template_input_id` int(10) unsigned NOT NULL default '0',
  COMMENT='Stores the relationship for what graph items are associated',
  ENGINE=InnoDB;


--
-- Alter table `graph_templates`
--
ALTER TABLE `graph_templates`
  ADD COLUMN `multiple` CHAR(2) NOT NULL DEFAULT '' AFTER `name`,
  ADD KEY `multiple_name` (`multiple`, `name`(171)),
  -- ADD KEY  `name` (`name`),
ENGINE=InnoDB;

UPDATE `graph_templates`
  SET `multiple` = 'on'
  WHERE `hash` = '010b90500e1fc6a05abfd542940584d0';


--
-- Alter table `graph_templates_gprint`
--

ALTER TABLE `graph_templates_gprint`
  ADD KEY `name` (`name`),
ENGINE=InnoDB;


--
-- Alter table `graph_templates_graph`
--
ALTER TABLE `graph_templates_graph`
  ADD COLUMN `t_alt_y_grid` char(2) DEFAULT '' AFTER `unit_exponent_value`,
  ADD COLUMN `alt_y_grid` char(2) DEFAULT NULL AFTER `t_alt_y_grid`,
  ADD COLUMN `t_right_axis` char(2) DEFAULT '' AFTER `alt_y_grid`,
  ADD COLUMN `right_axis` varchar(20) DEFAULT NULL AFTER `t_right_axis`,
  ADD COLUMN `t_right_axis_label` char(2) DEFAULT '' AFTER `right_axis`,
  ADD COLUMN `right_axis_label` varchar(200) DEFAULT NULL AFTER `t_right_axis_label`,
  ADD COLUMN `t_right_axis_format` char(2) DEFAULT '' AFTER `right_axis_label`,
  ADD COLUMN `right_axis_format` mediumint(8) DEFAULT NULL AFTER `t_right_axis_format`,
  ADD COLUMN `t_right_axis_formatter` char(2) DEFAULT '' AFTER `right_axis_format`,
  ADD COLUMN `right_axis_formatter` varchar(10) DEFAULT NULL AFTER `t_right_axis_formatter`,
  ADD COLUMN `t_left_axis_formatter` char(2) DEFAULT '' AFTER `right_axis_formatter`,
  ADD COLUMN `left_axis_formatter` varchar(10) DEFAULT NULL AFTER `t_left_axis_formatter`,
  ADD COLUMN `t_no_gridfit` char(2) DEFAULT '' AFTER `left_axis_formatter`,
  ADD COLUMN `no_gridfit` char(2) DEFAULT NULL AFTER `t_no_gridfit`,
  ADD COLUMN `t_unit_length` char(2) DEFAULT '' AFTER `no_gridfit`,
  ADD COLUMN `unit_length` varchar(10) DEFAULT NULL AFTER `t_unit_length`,
  ADD COLUMN `t_tab_width` char(2) DEFAULT '' AFTER `unit_length`,
  ADD COLUMN `tab_width` varchar(20) DEFAULT '30' AFTER `t_tab_width`,
  ADD COLUMN `t_dynamic_labels` char(2) DEFAULT '' AFTER `tab_width`,
  ADD COLUMN `dynamic_labels` char(2) DEFAULT NULL AFTER `t_dynamic_labels`,
  ADD COLUMN `t_force_rules_legend` char(2) DEFAULT '' AFTER `dynamic_labels`,
  ADD COLUMN `force_rules_legend` char(2) DEFAULT NULL AFTER `t_force_rules_legend`,
  ADD COLUMN `t_legend_position` char(2) DEFAULT '' AFTER `force_rules_legend`,
  ADD COLUMN `legend_position` varchar(10) DEFAULT NULL AFTER `t_legend_position`,
  ADD COLUMN `t_legend_direction` char(2) DEFAULT '' AFTER `legend_position`,
  ADD COLUMN `legend_direction` varchar(10) DEFAULT NULL AFTER `t_legend_direction`,
  DROP COLUMN `export`,
  DROP COLUMN `t_export`,
  MODIFY COLUMN `t_image_format_id` char(2) DEFAULT '',
  MODIFY COLUMN `t_title` char(2) DEFAULT '',
  MODIFY COLUMN `t_height` char(2) DEFAULT '',
  MODIFY COLUMN `t_width` char(2) DEFAULT '',
  MODIFY COLUMN `t_upper_limit` char(2) DEFAULT '',
  MODIFY COLUMN `t_lower_limit` char(2) DEFAULT '',
  MODIFY COLUMN `t_vertical_label` char(2) DEFAULT '',
  MODIFY COLUMN `t_slope_mode` char(2) DEFAULT '',
  MODIFY COLUMN `slope_mode` char(2) DEFAULT 'on',
  MODIFY COLUMN `t_auto_scale` char(2) DEFAULT '',
  MODIFY COLUMN `auto_scale` char(2) DEFAULT '',
  MODIFY COLUMN `t_auto_scale_opts` char(2) DEFAULT '',
  MODIFY COLUMN `t_auto_scale_log` char(2) DEFAULT '',
  MODIFY COLUMN `auto_scale_log` char(2) DEFAULT '',
  MODIFY COLUMN `t_scale_log_units` char(2) DEFAULT '',
  MODIFY COLUMN `t_auto_scale_rigid` char(2) DEFAULT '',
  MODIFY COLUMN `auto_scale_rigid` char(2) DEFAULT '',
  MODIFY COLUMN `t_auto_padding` char(2) DEFAULT '',
  MODIFY COLUMN `auto_padding` char(2) DEFAULT '',
  MODIFY COLUMN `t_base_value` char(2) DEFAULT '',
  MODIFY COLUMN `t_grouping` char(2) DEFAULT '',
  MODIFY COLUMN `grouping` char(2) NOT NULL DEFAULT '',
  MODIFY COLUMN `t_unit_value` char(2) DEFAULT '',
  MODIFY COLUMN `t_unit_exponent_value` char(2) DEFAULT '',
  MODIFY COLUMN `id` int(10) unsigned NOT NULL auto_increment, 
  MODIFY COLUMN `local_graph_id` int(10) unsigned NOT NULL default '0', 
  MODIFY COLUMN `local_graph_template_graph_id` int(10) unsigned NOT NULL default '0',
  DROP INDEX `title_cache`, 
  ADD INDEX `title_cache`(`title_cache`),
ENGINE=InnoDB;

UPDATE `graph_templates_graph` SET `t_title`='' WHERE `t_title` IS NULL or `t_title`='0';


--
-- Alter table `graph_templates_item`
--
ALTER TABLE `graph_templates_item`
  ADD COLUMN `vdef_id` mediumint(8) unsigned NOT NULL DEFAULT 0 AFTER `cdef_id`,
  ADD COLUMN `line_width` decimal(4,2) DEFAULT 0.00 AFTER `graph_type_id`,
  ADD COLUMN `dashes` varchar(20) DEFAULT NULL AFTER `line_width`, 
  ADD COLUMN `dash_offset` mediumint(4) DEFAULT NULL AFTER `dashes`,
  ADD COLUMN `shift` char(2) DEFAULT NULL AFTER `vdef_id`,
  ADD COLUMN `textalign` varchar(10) DEFAULT NULL AFTER `consolidation_function_id`,
  MODIFY COLUMN `task_item_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  MODIFY COLUMN `alpha` char(2) DEFAULT 'FF',
  MODIFY COLUMN `hard_return` char(2) DEFAULT '',
  MODIFY COLUMN `id` int(10) unsigned NOT NULL auto_increment,
  MODIFY COLUMN `local_graph_template_item_id` int(10) unsigned NOT NULL default '0',
  MODIFY COLUMN `local_graph_id` int(10) unsigned NOT NULL default '0',
  DROP KEY `local_graph_id`,
  ADD INDEX `local_graph_id_sequence` (`local_graph_id`, `sequence`),
  ADD INDEX `lgi_gti` (`local_graph_id`,`graph_template_id`),
  -- ADD INDEX `task_item_id` (`task_item_id`) USING BTREE,
ENGINE=InnoDB;

UPDATE `graph_templates_item` SET hash='' WHERE `local_graph_id`>0;


--
-- Alter table `graph_tree`
--
ALTER TABLE `graph_tree`
  ADD COLUMN `enabled` char(2) DEFAULT 'on' AFTER `id`,
  ADD COLUMN `locked` tinyint(4) DEFAULT 0 AFTER `enabled`,
  ADD COLUMN `locked_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `locked`,
  ADD COLUMN `sequence` int(10) unsigned DEFAULT '1' AFTER `name`,
  ADD COLUMN `user_id` int(10) unsigned DEFAULT 1 AFTER `sequence`,
  ADD COLUMN `last_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `user_id`,
  ADD COLUMN `modified_by` int(10) unsigned DEFAULT 1 AFTER `last_modified`,
  ADD INDEX `sequence` (`sequence`),
  ADD KEY `name` (`name`),
ENGINE=InnoDB;


--
-- Alter table `graph_tree_items`
--
ALTER TABLE `graph_tree_items`
  ADD COLUMN `parent` bigint(20) unsigned DEFAULT NULL AFTER `id`,
  ADD COLUMN `position` int(10) unsigned DEFAULT NULL AFTER `parent`,
  ADD COLUMN `graph_regex` VARCHAR(60) DEFAULT '' AFTER `sort_children_type`,
  ADD COLUMN `host_regex` VARCHAR(60) DEFAULT '' AFTER `graph_regex`,
  MODIFY COLUMN `id` BIGINT UNSIGNED NOT NULL auto_increment,
  MODIFY COLUMN `sort_children_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  MODIFY COLUMN `local_graph_id` int(10) unsigned NOT NULL DEFAULT '0',
  DROP COLUMN `rra_id`,
  -- ADD INDEX `parent` (`parent`),
  -- DROP INDEX `parent`,
  ADD INDEX `parent_position`(`parent`, `position`), 
  ADD COLUMN `site_id` INT UNSIGNED DEFAULT "0" AFTER `host_id`,
  ADD INDEX `site_id` (`site_id`),
	ENGINE=InnoDB;

UPDATE `graph_tree_items`	SET `host_grouping_type` = 1 WHERE `host_id` > 0;


--
-- Alter table `host`
--
ALTER TABLE `host`
  MODIFY COLUMN `status_fail_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  MODIFY COLUMN `status_rec_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  ADD COLUMN `snmp_sysDescr` varchar(300) NOT NULL DEFAULT '' AFTER `snmp_timeout`,
  ADD COLUMN `snmp_sysObjectID` varchar(128) NOT NULL DEFAULT '' AFTER `snmp_sysDescr`,
  ADD COLUMN `snmp_sysUpTimeInstance` int(10) unsigned NOT NULL DEFAULT '0' AFTER `snmp_sysObjectID`,
  ADD COLUMN `snmp_sysContact` varchar(300) NOT NULL DEFAULT '' AFTER `snmp_sysUpTimeInstance`,
  ADD COLUMN `snmp_sysName` varchar(300) NOT NULL DEFAULT '' AFTER `snmp_sysContact`,
  ADD COLUMN `snmp_sysLocation` varchar(300) NOT NULL DEFAULT '' AFTER `snmp_sysName`,
  ADD COLUMN `polling_time` double DEFAULT '0' AFTER `avg_time`,
  ADD COLUMN `poller_id` int(10) unsigned NOT NULL default '1' AFTER `id`,
  ADD COLUMN `site_id` int(10) unsigned NOT NULL default '1' AFTER `poller_id`,
  ADD COLUMN `snmp_engine_id` varchar(64) DEFAULT '' AFTER `snmp_context`,
  ADD COLUMN `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP AFTER `availability`,
  ADD COLUMN `external_id` VARCHAR(40) DEFAULT NULL AFTER `notes`,
  ADD COLUMN `location` varchar(40) default NULL AFTER `hostname`,
  ADD COLUMN `deleted` char(3) default '' AFTER `device_threads`,
  ADD COLUMN `bulk_walk_size` INT(11) DEFAULT '-1' AFTER `max_oids`,
  MODIFY COLUMN `snmp_auth_protocol` char(6) DEFAULT '',
  MODIFY COLUMN `hostname` varchar(100) DEFAULT '',
  MODIFY COLUMN `snmp_priv_protocol` char(6) DEFAULT '',
  MODIFY COLUMN `disabled` char(2) DEFAULT '',
  ADD INDEX `poller_id_disabled` (`poller_id`, `disabled`),
  ADD INDEX `site_id` (`site_id`),
  ADD INDEX `external_id` (`external_id`),
  ADD INDEX `hostname` (`hostname`),
  ADD KEY `status` (`status`),
  ADD INDEX `poller_id_last_updated` (`poller_id`, `last_updated`),
  ADD INDEX `site_id_location` (`site_id`, `location`),
ENGINE=InnoDB;

UPDATE `host` SET status = 0 WHERE disabled = 'on';


--
-- Alter table `host_graph`
--
ALTER TABLE `host_graph` ENGINE=InnoDB;


--
-- Alter table `host_snmp_cache`
--
ALTER TABLE `host_snmp_cache`
  ADD COLUMN `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `present`,
  MODIFY COLUMN `field_value` varchar(512) DEFAULT NULL,
  -- ADD INDEX `last_updated` (`last_updated`),
  -- ADD INDEX `snmp_query_id` (`snmp_query_id`) USING BTREE,
ROW_FORMAT=Dynamic,
ENGINE=InnoDB;


--
-- Alter table `host_snmp_query`
--
ALTER TABLE `host_snmp_query`	ENGINE=InnoDB;


--
-- Alter table `host_template`
--
ALTER TABLE `host_template`
  ADD KEY `name` (`name`),
ENGINE=InnoDB;


--
-- Alter table `host_template_graph`
--
ALTER TABLE `host_template_graph` ENGINE=InnoDB;


--
-- Alter table `host_template_snmp_query`
--
ALTER TABLE `host_template_snmp_query` ENGINE=InnoDB;


--
-- Alter table `plugin_config`
--
ALTER TABLE `plugin_config`
  MODIFY COLUMN `id` mediumint(8) unsigned NOT NULL auto_increment,
  MODIFY COLUMN `version` VARCHAR(10) NOT NULL default '',
ENGINE=InnoDB;


--
-- Alter table `plugin_db_changes`
--
ALTER TABLE `plugin_db_changes`
  MODIFY COLUMN `id` mediumint(8) unsigned NOT NULL auto_increment,
ENGINE=InnoDB;


--
-- Alter table `plugin_realms`
--
ALTER TABLE `plugin_realms` 
  MODIFY COLUMN `id` mediumint(8) unsigned NOT NULL auto_increment,
ENGINE=InnoDB;

--
-- Alter table `poller`
--
ALTER TABLE `poller`
  ADD COLUMN `disabled` char(2) DEFAULT '' AFTER `id`,
  ADD COLUMN `name` varchar(30) DEFAULT NULL AFTER `disabled`,
  ADD COLUMN `notes` varchar(1024) DEFAULT '' AFTER `name`,
  ADD COLUMN `status` int(10) unsigned NOT NULL DEFAULT 0 AFTER `notes`,
  ADD COLUMN `dbdefault` varchar(20) NOT NULL DEFAULT 'cacti' AFTER `hostname`,
  ADD COLUMN `dbhost` varchar(64) NOT NULL DEFAULT 'cacti' AFTER `dbdefault`,
  ADD COLUMN `dbuser` varchar(20) NOT NULL DEFAULT '' AFTER `dbhost`,
  ADD COLUMN `dbpass` varchar(64) NOT NULL DEFAULT '' AFTER `dbuser`,
  ADD COLUMN `dbport` int(10) unsigned DEFAULT 3306 AFTER `dbpass`,
  ADD COLUMN `dbssl` char(3) DEFAULT '' AFTER `dbport`,
  ADD COLUMN `total_time` double DEFAULT 0 AFTER `dbssl`,
  ADD COLUMN `snmp` mediumint(8) unsigned DEFAULT 0 AFTER `total_time`,
  ADD COLUMN `script` mediumint(8) unsigned DEFAULT 0 AFTER `snmp`,
  ADD COLUMN `server` mediumint(8) unsigned DEFAULT 0 AFTER `script`,
  ADD COLUMN `last_status` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `last_update`,
  ADD COLUMN `max_time` double DEFAULT NULL AFTER `total_time`,
  ADD COLUMN `min_time` double DEFAULT NULL AFTER `max_time`,
  ADD COLUMN `avg_time` double DEFAULT NULL AFTER `min_time`,
  ADD COLUMN `total_polls` int(10) unsigned DEFAULT '0' AFTER `avg_time`,
  ADD COLUMN `processes` int(10) unsigned DEFAULT '1' AFTER `total_polls`,
  ADD COLUMN `threads` int(10) unsigned DEFAULT '1' AFTER `processes`,
  ADD COLUMN `sync_interval` int(10) unsigned DEFAULT '7200' AFTER `threads`,
  ADD COLUMN `timezone` varchar(40) DEFAULT '' AFTER `status`,
  ADD COLUMN `dbsslkey` varchar(255) DEFAULT NULL AFTER `dbssl`,
  ADD COLUMN `dbsslcert` varchar(255) DEFAULT NULL AFTER `dbsslkey`,
  ADD COLUMN `dbsslca` varchar(255) DEFAULT NULL AFTER `dbsslcert`,
  ADD COLUMN `requires_sync` char(2) DEFAULT '' AFTER `last_status`,
  ADD COLUMN `last_sync` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `requires_sync`,
  MODIFY COLUMN `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  MODIFY COLUMN `hostname` varchar(100) NOT NULL DEFAULT '',
  ADD KEY `name` (`name`),
	ADD INDEX `disabled` (`disabled`), 
ENGINE=InnoDB;

UPDATE poller SET requires_sync = "on" WHERE id != 1;


--
-- Alter table `poller_command`
--
ALTER TABLE `poller_command`
  ADD COLUMN `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `command`,
  MODIFY COLUMN `time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  MODIFY COLUMN `poller_id` smallint(5) unsigned NOT NULL default '1',
  MODIFY COLUMN `command` varchar(191) NOT NULL default '',
  ADD INDEX `poller_id_last_updated` (`poller_id`, `last_updated`),	
ENGINE=InnoDB;

UPDATE `poller_command` SET `poller_id`=1 WHERE `poller_id`=0;


--
-- Table structure for table `poller_data_template_field_mappings`
--
CREATE TABLE `poller_data_template_field_mappings` (
  `data_template_id` int(10) unsigned NOT NULL DEFAULT '0',
  `data_name` varchar(40) NOT NULL DEFAULT '',
  `data_source_names` varchar(125) NOT NULL DEFAULT '',
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`data_template_id`,`data_name`,`data_source_names`)
)	ENGINE=InnoDB	ROW_FORMAT=Dynamic COMMENT='Tracks mapping of Data Templates to their Data Source Names';

INSERT IGNORE INTO `poller_data_template_field_mappings`
  SELECT dtr.`data_template_id`, dif.`data_name`, GROUP_CONCAT(dtr.`data_source_name` ORDER BY dtr.`data_source_name`) AS `data_source_names`, NOW()
  FROM `data_template_rrd` AS dtr
  INNER JOIN `data_input_fields` AS dif ON dtr.`data_input_field_id` = dif.`id`
  WHERE dtr.`local_data_id`=0 GROUP BY dtr.`data_template_id`, dif.`data_name`;


--
-- Alter table `poller_item`
--
ALTER TABLE `poller_item`
  ADD COLUMN `snmp_engine_id` varchar(64) default '' AFTER `snmp_context`,
	ADD COLUMN `last_updated` timestamp  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `present`,
  MODIFY COLUMN `poller_id` int(10) unsigned NOT NULL default '1', 
  MODIFY COLUMN `snmp_auth_protocol` char(6) NOT NULL DEFAULT '',
  MODIFY COLUMN `snmp_priv_protocol` char(6) NOT NULL DEFAULT '',
  MODIFY COLUMN `local_data_id` int(10) unsigned NOT NULL default '0',
  MODIFY COLUMN `arg1` TEXT default NULL,
  ADD INDEX `poller_id_host_id` (`poller_id`,`host_id`),
  ADD INDEX `poller_id_action` (`poller_id`,`action`),
  ADD INDEX `poller_id_last_updated` (`poller_id`, `last_updated`),
  ADD INDEX `poller_id_rrd_next_step` (`poller_id`,`rrd_next_step`),
  -- DROP INDEX `last_updated`,
ENGINE=InnoDB;

UPDATE `poller_item` SET `poller_id`=1 WHERE `poller_id`=0;


--
-- Alter table `poller_output`
--
ALTER TABLE `poller_output`
	MODIFY COLUMN `time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  MODIFY COLUMN `output` VARCHAR(512) NOT NULL default '',
  MODIFY COLUMN `local_data_id` int(10) unsigned NOT NULL default '0',
ENGINE=MEMORY,
ENGINE=InnoDB;


--
-- Table structure for table `poller_output_boost`
--
CREATE TABLE  `poller_output_boost` (
  `local_data_id` int(10) unsigned NOT NULL default '0',
  `rrd_name` varchar(19) NOT NULL default '',
  `time` timestamp NOT NULL default '0000-00-00 00:00:00',
  `output` varchar(512) NOT NULL,
  PRIMARY KEY (`local_data_id`, `time`, `rrd_name`) USING BTREE
) ENGINE=InnoDB ROW_FORMAT=Dynamic;


--
-- Table structure for table `poller_output_boost_processes`
--
CREATE TABLE  `poller_output_boost_processes` (
  `sock_int_value` bigint(20) unsigned NOT NULL auto_increment,
  `status` varchar(255) default NULL,
  PRIMARY KEY (`sock_int_value`)
) ENGINE=MEMORY;


--
-- Table structure for table `poller_output_realtime`
--
CREATE TABLE `poller_output_realtime` (
  `local_data_id` int(10) unsigned NOT NULL default '1',
  `rrd_name` varchar(19) NOT NULL default '',
  `time` timestamp NOT NULL default '0000-00-00 00:00:00',
  `output` text NOT NULL,
  `poller_id` varchar(256) NOT NULL default '1',
  PRIMARY KEY (`local_data_id`, `rrd_name`, `time`, `poller_id`),
  KEY `poller_id` (`poller_id`(191)),
  KEY `time` (`time`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic;


--
-- Alter table `poller_reindex`
--
ALTER TABLE `poller_reindex`
  DROP PRIMARY KEY,
  ADD PRIMARY KEY (`host_id`, `data_query_id`, `arg1`(187)),
ENGINE=InnoDB;


--
-- Table structure for table `poller_resource_cache`
--

CREATE TABLE `poller_resource_cache` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `resource_type` varchar(20) DEFAULT NULL,
  `md5sum` varchar(32) DEFAULT NULL,
  `path` varchar(191) DEFAULT NULL,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `contents` longblob,
  `attributes` INT unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `path` (`path`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Caches all scripts, resources files, and plugins';


--
-- Alter table `poller_time`
--
ALTER TABLE `poller_time`
	MODIFY COLUMN `start_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  MODIFY COLUMN `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  MODIFY COLUMN `poller_id` int(10) unsigned DEFAULT '1',
  ADD KEY `poller_id_end_time` (`poller_id`, `end_time`),
ENGINE=InnoDB;

UPDATE `poller_time` SET `poller_id`=1 WHERE `poller_id`=0;


--
-- Table structure for table `processes`
--
CREATE TABLE `processes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT 0,
  `tasktype` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `taskname` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `taskid` int(10) unsigned NOT NULL DEFAULT 0,
  `timeout` int(11) DEFAULT 300,
  `started` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`pid`,`tasktype`,`taskname`,`taskid`),
  KEY `tasktype` (`tasktype`),
  KEY `pid` (`pid`),
  KEY `id` (`id`)
) ENGINE=MEMORY COMMENT='Stores Process Status for Cacti Background Processes';


--
-- Table structure for table `reports`
--
CREATE TABLE `reports` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `cformat` char(2) NOT NULL DEFAULT '',
  `format_file` varchar(255) NOT NULL DEFAULT '',
  `font_size` smallint(2) unsigned NOT NULL DEFAULT '0',
  `alignment` smallint(2) unsigned NOT NULL DEFAULT '0',
  `graph_linked` char(2) NOT NULL DEFAULT '',
  `intrvl` smallint(2) unsigned NOT NULL DEFAULT '0',
  `count` smallint(2) unsigned NOT NULL DEFAULT '0',
  `offset` int(12) unsigned NOT NULL DEFAULT '0',
  `mailtime` bigint(20) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(64) NOT NULL DEFAULT '',
  `from_name` varchar(40) NOT NULL,
  `from_email` text NOT NULL,
  `email` text NOT NULL,
  `bcc` text NOT NULL,
  `attachment_type` smallint(2) unsigned NOT NULL DEFAULT '1',
  `graph_height` smallint(2) unsigned NOT NULL DEFAULT '0',
  `graph_width` smallint(2) unsigned NOT NULL DEFAULT '0',
  `graph_columns` smallint(2) unsigned NOT NULL DEFAULT '0',
  `thumbnails` char(2) NOT NULL DEFAULT '',
  `lastsent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `enabled` char(2) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `mailtime` (`mailtime`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Cacri Reporting Reports';


--
-- Table structure for table `reports_items`
--

CREATE TABLE `reports_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `report_id` int(10) unsigned NOT NULL DEFAULT '0',
  `item_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `tree_id` int(10) unsigned NOT NULL DEFAULT '0',
  `branch_id` int(10) unsigned NOT NULL DEFAULT '0',
  `tree_cascade` char(2) NOT NULL DEFAULT '',
  `graph_name_regexp` varchar(128) NOT NULL DEFAULT '',
  `host_template_id` int(10) unsigned NOT NULL DEFAULT '0',
  `host_id` int(10) unsigned NOT NULL DEFAULT '0',
  `graph_template_id` int(10) unsigned NOT NULL DEFAULT '0',
  `local_graph_id` int(10) unsigned NOT NULL DEFAULT '0',
  `timespan` int(10) unsigned NOT NULL DEFAULT '0',
  `align` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `item_text` text NOT NULL,
  `font_size` smallint(2) unsigned NOT NULL DEFAULT '10',
  `sequence` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `report_id` (`report_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Cacti Reporting Items';


--
-- Table structure for table `sessions`
--
CREATE TABLE `sessions` (
  `id` varchar(32) NOT NULL,
  `remote_addr` varchar(25) NOT NULL DEFAULT '',
  `access` int(10) unsigned DEFAULT NULL,
  `data` mediumblob,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_agent` varchar(128) NOT NULL DEFAULT '',
  `start_time` timestamp NOT NULL DEFAULT current_timestamp,
  `transactions` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Used for Database based Session Storage';


--
-- Alter table `settings`
--
ALTER TABLE `settings`
  MODIFY COLUMN `value` varchar(2048) NOT NULL default '',
ENGINE=InnoDB;

UPDATE IGNORE `settings` SET `name` = REPLACE(name, 'autom8', 'automation') WHERE `name` LIKE 'autom8%';
UPDATE `settings` 
  SET `value` = IF(value = '1', 'on', '')
	WHERE `name` = 'hide_console' AND `value` != 'on';
REPLACE INTO `settings` (name, value) VALUES ('max_display_rows', '1000'), ('realtime_cache_path','/srv/eyesofnetwork/cacti/cache/realtime/');


--
-- Alter table `settings_tree`
--
ALTER TABLE `settings_tree`
	MODIFY COLUMN `graph_tree_item_id` int(10) unsigned NOT NULL default '0',
ENGINE=InnoDB;


--
-- Table structure for table `settings_user`
--
ALTER TABLE `settings_graphs` RENAME TO `settings_user`;
ALTER TABLE `settings_user`
  MODIFY COLUMN `value` varchar(2048) NOT NULL default '',
ENGINE=InnoDB;


--
-- Table structure for table `settings_user_group`
--

CREATE TABLE `settings_user_group` (
  `group_id` smallint(8) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `value` varchar(2048) NOT NULL DEFAULT '',
  PRIMARY KEY (`group_id`, `name`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Stores the Default User Group Graph Settings';



--
-- Table structure for table `sites`
--
CREATE TABLE `sites` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `address1` varchar(100) DEFAULT '',
  `address2` varchar(100) DEFAULT '',
  `city` varchar(50) DEFAULT '',
  `state` varchar(20) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT '',
  `country` varchar(30) DEFAULT '',
  `timezone` varchar(40) DEFAULT '',
  `latitude` decimal(13,10) NOT NULL DEFAULT '0.0000000000',
  `longitude` decimal(13,10) NOT NULL DEFAULT '0.0000000000',
  `zoom` tinyint unsigned default NULL,
  `alternate_id` varchar(30) DEFAULT '',
  `notes` varchar(1024),
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `city` (`city`),
  KEY `state` (`state`),
  KEY `postal_code` (`postal_code`),
  KEY `country` (`country`),
  KEY `alternate_id` (`alternate_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Contains information about customer sites';


--
-- Alter table `snmp_query`
--
ALTER TABLE `snmp_query` ENGINE=InnoDB;


--
-- Alter table `snmp_query_graph`
--
ALTER TABLE `snmp_query_graph`
  ADD KEY `graph_template_id_name` (`graph_template_id`, `name`),
	ADD KEY `snmp_query_id_name` (`snmp_query_id`, `name`),
ENGINE=InnoDB;


--
-- Alter table `snmp_query_graph_rrd`
--
ALTER TABLE `snmp_query_graph_rrd`
  MODIFY COLUMN `data_template_rrd_id` int(10) unsigned NOT NULL default '0',
  -- ADD INDEX `data_template_rrd_id` (`data_template_rrd_id`) USING BTREE,
ENGINE=InnoDB;

DELETE FROM `snmp_query_graph_rrd` WHERE `data_template_id`=0 OR `data_template_rrd_id`=0;


--
-- Alter table `snmp_query_graph_rrd_sv`
--
ALTER TABLE `snmp_query_graph_rrd_sv`
  -- ADD INDEX `data_template_id` (`data_template_id`) USING BTREE,
ENGINE=InnoDB;

DELETE FROM `snmp_query_graph_rrd_sv`	WHERE `data_template_id`=0;


--
-- Alter table `snmp_query_graph_sv`
--
ALTER TABLE `snmp_query_graph_sv` ENGINE=InnoDB;


--
-- Table structure for table `snmpagent_cache`
--
CREATE TABLE `snmpagent_cache` (
  `oid` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `mib` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT '',
  `otype` varchar(50) NOT NULL DEFAULT '',
  `kind` varchar(50) NOT NULL DEFAULT '',
  `max-access` varchar(50) NOT NULL DEFAULT 'not-accessible',
  `value` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(5000) NOT NULL DEFAULT '',
  PRIMARY KEY (`oid`),
  KEY `name` (`name`),
  KEY `mib_name` (`mib`,`name`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='SNMP MIB CACHE';


--
-- Table structure for table `snmpagent_cache_notifications`
--
CREATE TABLE `snmpagent_cache_notifications` (
  `name` varchar(50) NOT NULL,
  `mib` varchar(50) NOT NULL,
  `attribute` varchar(50) NOT NULL,
  `sequence_id` smallint(6) NOT NULL,
  PRIMARY KEY (`name`,`mib`,`attribute`,`sequence_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Notifcations and related attributes';


--
-- Table structure for table `snmpagent_cache_textual_conventions`
--
CREATE TABLE `snmpagent_cache_textual_conventions` (
  `name` varchar(50) NOT NULL,
  `mib` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(5000) NOT NULL DEFAULT '',
  PRIMARY KEY (`name`,`mib`,`type`),
  KEY `name` (`name`),
  KEY `mib` (`mib`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Textual conventions';


--
-- Table structure for table `snmpagent_managers`
--
CREATE TABLE `snmpagent_managers` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `hostname` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `disabled` char(2) DEFAULT NULL,
  `max_log_size` tinyint(1) NOT NULL,
  `snmp_version` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `snmp_community` varchar(100) NOT NULL DEFAULT '',
  `snmp_username` varchar(50) NOT NULL DEFAULT '',
  `snmp_password` varchar(50) NOT NULL DEFAULT '',
  `snmp_auth_protocol` char(6) NOT NULL DEFAULT '',
  `snmp_priv_passphrase` varchar(200) NOT NULL DEFAULT '',
  `snmp_priv_protocol` char(6) NOT NULL,
  `snmp_engine_id` varchar(64) DEFAULT '',
  `snmp_port` mediumint(5) unsigned NOT NULL DEFAULT '161',
  `snmp_message_type` tinyint(1) NOT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `hostname` (`hostname`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='snmp notification receivers';


--
-- Table structure for table `snmpagent_managers_notifications`
--
CREATE TABLE `snmpagent_managers_notifications` (
  `manager_id` int(8) NOT NULL,
  `notification` varchar(50) NOT NULL,
  `mib` varchar(50) NOT NULL,
  PRIMARY KEY(`manager_id`,`notification`,`mib`),
  KEY `mib` (`mib`),
  KEY `manager_id_notification` (`manager_id`,`notification`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='snmp notifications to receivers';



--
-- Table structure for table `snmpagent_mibs`
--
CREATE TABLE `snmpagent_mibs` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `file` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Registered MIB files';


--
-- Table structure for table `snmpagent_notifications_log`
--
CREATE TABLE `snmpagent_notifications_log` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `time` int(24) NOT NULL,
  `severity` tinyint(1) NOT NULL,
  `manager_id` int(8) NOT NULL,
  `notification` varchar(190) NOT NULL DEFAULT '',
  `mib` varchar(50) NOT NULL DEFAULT '',
  `varbinds` varchar(5000) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`),
  KEY `severity` (`severity`),
  KEY `manager_id_notification` (`manager_id`,`notification`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='logs snmp notifications to receivers';


--
-- Alter table `user_auth`
--
ALTER TABLE `user_auth`
  ADD COLUMN `lastchange` int(12) NOT NULL DEFAULT -1 AFTER `enabled`,
  ADD COLUMN `lastlogin` int(12) NOT NULL DEFAULT -1 AFTER `lastchange`,
  ADD COLUMN `password_history` varchar(4096) NOT NULL DEFAULT '-1' AFTER `lastlogin`,
  ADD COLUMN `locked` varchar(3) NOT NULL DEFAULT '' AFTER `password_history`,
  ADD COLUMN `failed_attempts` int(5) NOT NULL DEFAULT 0 AFTER `locked`,
  ADD COLUMN `lastfail` int(12) NOT NULL DEFAULT 0 AFTER `failed_attempts`,
  ADD COLUMN `reset_perms` int(12) UNSIGNED NOT NULL DEFAULT '0' AFTER `lastfail`,
  ADD COLUMN `email_address` varchar(128) NULL AFTER `full_name`,
  ADD COLUMN `password_change` char(2) default 'on' AFTER `must_change_password`,
  MODIFY COLUMN `password` varchar(256) NOT NULL default '',
  MODIFY COLUMN `realm` mediumint(8) NOT NULL DEFAULT '0',
  MODIFY COLUMN `must_change_password` char(2) DEFAULT '',
  MODIFY COLUMN `show_tree` char(2) DEFAULT 'on',
  MODIFY COLUMN `show_list` char(2) DEFAULT 'on',
  MODIFY COLUMN `show_preview` char(2) NOT NULL DEFAULT 'on',
  MODIFY COLUMN `graph_settings` char(2) DEFAULT '',
ENGINE=InnoDB;

UPDATE `user_auth` SET `realm`=3 WHERE `realm`=1;


--
-- Table structure for table `user_auth_cache`
--
CREATE TABLE `user_auth_cache` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `hostname` varchar(100) NOT NULL DEFAULT '',
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `token` varchar(191) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tokenkey` (`token`),
  KEY `hostname` (`hostname`),
  KEY `user_id` (`user_id`),
  KEY `last_update` (`last_update`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Caches Remember Me Details';


--
-- Table structure for table `user_auth_group`
--
CREATE TABLE `user_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  `graph_settings` varchar(2) DEFAULT NULL,
  `login_opts` tinyint(1) NOT NULL DEFAULT '1',
  `show_tree` varchar(2) DEFAULT 'on',
  `show_list` varchar(2) DEFAULT 'on',
  `show_preview` varchar(2) NOT NULL DEFAULT 'on',
  `policy_graphs` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `policy_trees` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `policy_hosts` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `policy_graph_templates` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `enabled` char(2) NOT NULL DEFAULT 'on',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Table that Contains User Groups';


--
-- Table structure for table `user_auth_group_members`
--
CREATE TABLE `user_auth_group_members` (
  `group_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`group_id`,`user_id`),
  KEY `realm_id` (`user_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Table that Contains User Group Members';


--
-- Table structure for table `user_auth_group_perms`
--
CREATE TABLE `user_auth_group_perms` (
  `group_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `item_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`,`item_id`,`type`),
  KEY `group_id` (`group_id`,`type`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Table that Contains User Group Permissions';


--
-- Table structure for table `user_auth_group_realm`
--
CREATE TABLE `user_auth_group_realm` (
  `group_id` int(10) unsigned NOT NULL,
  `realm_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`group_id`,`realm_id`),
  KEY `realm_id` (`realm_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Table that Contains User Group Realm Permissions';


--
-- Alter table `user_auth_perms`
--
ALTER TABLE `user_auth_perms` ENGINE=InnoDB;


--
-- Alter table `user_auth_realm`
--
ALTER TABLE `user_auth_realm` ENGINE=InnoDB;

INSERT IGNORE INTO `user_auth_realm` VALUES (18,1), (20,1), (21,1), (23,1);

-- SELECT `username` INTO `_user` FROM `user_auth` WHERE `id`= 1 AND `username` = 'admin';
-- REPLACE INTO `user_auth_realm` VALUES (19,1), (22,1) WHERE ((SELECT `username` FROM `user_auth` WHERE `id`=1 LIMIT 1)='admin' );
-- REPLACE INTO `user_auth_realm` VALUES (19,1) WHERE EXISTS(SELECT * FROM `user_auth` WHERE `id`=1 AND `username`='admin');
-- REPLACE INTO `user_auth_realm` VALUES (22,1) WHERE EXISTS(SELECT * FROM `user_auth` WHERE `id`=1 AND `username`='admin');
-- IF EXISTS(SELECT * FROM `user_auth` WHERE `id`=1 AND `username`='admin')   
-- REPLACE INTO `user_auth_realm` VALUES (19,1), (22,1);
-- END IF;
-- IF EXISTS(SELECT * FROM `user_auth` WHERE `id`=1 AND `username`='admin') THEN
--   select * from settings;
-- END IF;


--
-- Table structure for table `user_domains`
--
CREATE TABLE `user_domains` (
  `domain_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `domain_name` varchar(20) NOT NULL,
  `type` int(10) unsigned NOT NULL DEFAULT '0',
  `enabled` char(2) NOT NULL DEFAULT 'on',
  `defdomain` tinyint(3) NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`domain_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Table to Hold Login Domains';


--
-- Table structure for table `user_domains_ldap`
--
CREATE TABLE `user_domains_ldap` (
  `domain_id` int(10) unsigned NOT NULL,
  `server` varchar(128) NOT NULL,
  `port` int(10) unsigned NOT NULL,
  `port_ssl` int(10) unsigned NOT NULL,
  `proto_version` tinyint(3) unsigned NOT NULL,
  `encryption` tinyint(3) unsigned NOT NULL,
  `referrals` tinyint(3) unsigned NOT NULL,
  `mode` tinyint(3) unsigned NOT NULL,
  `dn` varchar(128) NOT NULL,
  `group_require` char(2) NOT NULL,
  `group_dn` varchar(128) NOT NULL,
  `group_attrib` varchar(128) NOT NULL,
  `group_member_type` tinyint(3) unsigned NOT NULL,
  `search_base` varchar(128) NOT NULL,
  `search_filter` varchar(512) NOT NULL DEFAULT '',
  `specific_dn` varchar(128) NOT NULL,
  `specific_password` varchar(128) NOT NULL,
  `cn_full_name` varchar(50) NULL DEFAULT '',
  `cn_email` varchar (50) NULL DEFAULT '',
  PRIMARY KEY (`domain_id`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='Table to Hold Login Domains for LDAP';


--
-- Alter table `user_log`
--
ALTER TABLE `user_log`
  MODIFY COLUMN `time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  DROP INDEX `username`,
ENGINE=InnoDB;


--
-- Table structure for table `vdef`
--
CREATE TABLE `vdef` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `hash` varchar(32) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`),
  KEY `name` (`name`(171))
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='vdef';

INSERT INTO `vdef` VALUES(1, 'e06ed529238448773038601afb3cf278', 'Maximum'), (2, 'e4872dda82092393d6459c831a50dc3b', 'Minimum'), (3, '5ce1061a46bb62f36840c80412d2e629', 'Average'), (4, '06bd3cbe802da6a0745ea5ba93af554a', 'Last (Current)'), (5, '631c1b9086f3979d6dcf5c7a6946f104', 'First'), (6, '6b5335843630b66f858ce6b7c61fc493', 'Total: Current Data Source'), (7, 'c80d12b0f030af3574da68b28826cd39', '95th Percentage: Current Data Source');


--
-- Table structure for table `vdef_items`
--
CREATE TABLE `vdef_items` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `hash` varchar(32) NOT NULL default '',
  `vdef_id` mediumint(8) unsigned NOT NULL default '0',
  `sequence` mediumint(8) unsigned NOT NULL default '0',
  `type` tinyint(2) NOT NULL default '0',
  `value` varchar(150) NOT NULL default '',
  PRIMARY KEY (id),
  KEY `vdef_id_sequence` (`vdef_id`,`sequence`)
) ENGINE=InnoDB ROW_FORMAT=Dynamic COMMENT='vdef items';

INSERT INTO `vdef_items` VALUES(1, '88d33bf9271ac2bdf490cf1784a342c1', 1, 1, 4, 'CURRENT_DATA_SOURCE'), (2, 'a307afab0c9b1779580039e3f7c4f6e5', 1, 2, 1, '1'), (3, '0945a96068bb57c80bfbd726cf1afa02', 2, 1, 4, 'CURRENT_DATA_SOURCE'), (4, '95a8df2eac60a89e8a8ca3ea3d019c44', 2, 2, 1, '2'), (5, 'cc2e1c47ec0b4f02eb13708cf6dac585', 3, 1, 4, 'CURRENT_DATA_SOURCE'), (6, 'a2fd796335b87d9ba54af6a855689507', 3, 2, 1, '3'), (7, 'a1d7974ee6018083a2053e0d0f7cb901', 4, 1, 4, 'CURRENT_DATA_SOURCE'), (8, '26fccba1c215439616bc1b83637ae7f3', 4, 2, 1, '5'), (9, 'a8993b265f4c5398f4a47c44b5b37a07', 5, 1, 4, 'CURRENT_DATA_SOURCE'), (10, '5a380d469d611719057c3695ce1e4eee', 5, 2, 1, '6'), (11, '65cfe546b17175fad41fcca98c057feb', 6, 1, 4, 'CURRENT_DATA_SOURCE'), (12, 'f330b5633c3517d7c62762cef091cc9e', 6, 2, 1, '7'), (13, 'f1bf2ecf54ca0565cf39c9c3f7e5394b', 7, 1, 4, 'CURRENT_DATA_SOURCE'), (14, '11a26f18feba3919be3af426670cba95', 7, 2, 6, '95'), (15, 'e7ae90275bc1efada07c19ca3472d9db', 7, 3, 1, '8');


--
-- Alter table `version`
--
ALTER TABLE `version`
  MODIFY COLUMN `cacti` char(20) NOT NULL DEFAULT '',
  ADD PRIMARY KEY (`cacti`),
ENGINE=InnoDB;

UPDATE `version` SET `cacti`='1.2.18';




-- 
-- Other updates
-- 

UPDATE `data_template_data` SET `data_source_profile_id` = (SELECT `id` FROM `data_source_profiles` ORDER BY 'default' DESC LIMIT 1) WHERE `data_source_profile_id` = 0;


UPDATE `graph_local` AS gl
    INNER JOIN (
        SELECT DISTINCT `local_graph_id`, `task_item_id`
        FROM `graph_templates_item`
    ) AS gti ON gl.`id`=gti.`local_graph_id`
    INNER JOIN `data_template_rrd` AS dtr ON gti.`task_item_id`=dtr.`id`
    INNER JOIN `data_template_data` AS dtd ON dtr.`local_data_id`=dtd.`local_data_id`
    INNER JOIN `data_input_fields` AS dif ON dif.`data_input_id`=dtd.`data_input_id`
    INNER JOIN `data_input_data` AS did ON did.`data_template_data_id`=dtd.`id` AND did.`data_input_field_id`=dif.`id`
    INNER JOIN `snmp_query_graph_rrd` AS sqgr ON sqgr.`snmp_query_graph_id`=did.`value`
  SET gl.`snmp_query_graph_id`= did.`value`
  WHERE `input_output`='in' AND `type_code`='output_type' AND gl.`snmp_query_id`>0;


DELETE FROM `plugin_config` WHERE `directory` IN ('aggregate', 'autom8', 'clog', 'discovery', 'domains', 'dsstats', 'nectar', 'realtime', 'rrdclean', 'settings', 'snmpagent', 'spikekill', 'superlinks', 'ugroup');
DELETE FROM `plugin_realms` WHERE `plugin` IN ('aggregate', 'autom8', 'clog', 'discovery', 'domains', 'dsstats', 'nectar', 'realtime', 'rrdclean', 'settings', 'snmpagent', 'spikekill', 'superlinks', 'ugroup');
DELETE FROM `plugin_db_changes` WHERE `plugin` IN ('aggregate', 'autom8', 'clog', 'discovery', 'domains', 'dsstats', 'nectar', 'realtime', 'rrdclean', 'settings', 'snmpagent', 'spikekill', 'superlinks', 'ugroup');
DELETE FROM `plugin_hooks` WHERE `name` IN ('aggregate', 'autom8', 'clog', 'discovery', 'domains', 'dsstats', 'nectar', 'realtime', 'rrdclean', 'settings', 'snmpagent', 'spikekill', 'superlinks', 'ugroup');


UPDATE `graph_local` AS gl
    INNER JOIN `graph_templates_item` AS gti
    ON gti.`local_graph_id` = gl.`id`
    INNER JOIN `data_template_rrd` AS dtr
    ON gti.`task_item_id` = dtr.`id`
    INNER JOIN `data_local` AS dl
    ON dl.`id` = dtr.`local_data_id`
  SET gl.`snmp_query_id` = dl.`snmp_query_id`, gl.`snmp_index` = dl.`snmp_index`
  WHERE gl.`graph_template_id` IN (SELECT `graph_template_id` FROM `snmp_query_graph`) AND gl.`snmp_query_id` = 0;


UPDATE `graph_local` AS gl
    INNER JOIN (
        SELECT DISTINCT `local_graph_id`, `task_item_id`
        FROM `graph_templates_item`
    ) AS gti
    ON gl.`id` = gti.`local_graph_id`
    INNER JOIN `data_template_rrd` AS dtr
    ON gti.`task_item_id` = dtr.`id`
    INNER JOIN `data_template_data` AS dtd
    ON dtr.`local_data_id` = dtd.`local_data_id`
    INNER JOIN `data_input_fields` AS dif
    ON dif.`data_input_id` = dtd.`data_input_id`
    INNER JOIN `data_input_data` AS did
    ON did.`data_template_data_id` = dtd.`id`
    AND did.`data_input_field_id` = dif.`id`
    INNER JOIN `snmp_query_graph_rrd` AS sqgr
    ON sqgr.`snmp_query_graph_id` = did.`value`
  SET gl.`snmp_query_graph_id` = did.`value`
  WHERE `input_output` = 'in' AND `type_code` = 'output_type' AND gl.`graph_template_id` IN (SELECT `graph_template_id` FROM `snmp_query_graph`);


UPDATE `graph_local` AS gl
    INNER JOIN (
        SELECT DISTINCT `local_graph_id`, `task_item_id`
        FROM `graph_templates_item`
    ) AS gti
    ON gl.`id` = gti.`local_graph_id`
    INNER JOIN `data_template_rrd` AS dtr
    ON gti.`task_item_id` = dtr.`id`
    INNER JOIN `data_template_data` AS dtd
    ON dtr.`local_data_id` = dtd.`local_data_id`
    INNER JOIN `data_input_fields` AS dif
    ON dif.`data_input_id` = dtd.`data_input_id`
    INNER JOIN (
        SELECT *
        FROM `data_input_data`
        WHERE `value` RLIKE '^([0-9]+)$'
    ) AS did
    ON did.`data_template_data_id` = dtd.`id`
    AND did.`data_input_field_id` = dif.`id`
    INNER JOIN `snmp_query_graph_rrd` AS sqgr
    ON sqgr.`snmp_query_graph_id` = did.`value`
  SET gl.`snmp_query_graph_id` = did.`value`
  WHERE `input_output` = 'in' AND `type_code` = 'output_type' AND gl.`snmp_query_id` > 0 AND gl.`snmp_query_graph_id` = 0;


UPDATE `graph_templates_graph` SET `t_auto_scale_opts` = '' WHERE  `t_auto_scale_opts` IS NULL OR `t_auto_scale_opts` = 0;
UPDATE `graph_templates_graph` SET `t_auto_scale_log` = '' WHERE  `t_auto_scale_log` IS NULL OR `t_auto_scale_log` = 0;
UPDATE `graph_templates_graph` SET `t_scale_log_units` = '' WHERE `t_scale_log_units` IS NULL OR `t_scale_log_units` = 0;
UPDATE `graph_templates_graph` SET `t_auto_scale_rigid` = '' WHERE `t_auto_scale_rigid` IS NULL OR `t_auto_scale_rigid` = 0;
UPDATE `graph_templates_graph` SET `t_auto_padding` = '' WHERE  `t_auto_padding` IS NULL OR `t_auto_padding` = 0;
UPDATE `graph_templates_graph` SET `t_base_value` = '' WHERE `t_base_value` IS NULL OR `t_base_value` = 0;
UPDATE `graph_templates_graph` SET `t_grouping` = '' WHERE  `t_grouping` IS NULL OR `t_grouping` = 0;
UPDATE `graph_templates_graph` SET `t_unit_value` = '' WHERE  `t_unit_value` IS NULL OR `t_unit_value` = 0;
UPDATE `graph_templates_graph` SET `t_unit_exponent_value` = '' WHERE  `t_unit_exponent_value` IS NULL OR `t_unit_exponent_value` = 0;
UPDATE `graph_templates_graph` SET `t_alt_y_grid` = '' WHERE  `t_alt_y_grid` IS NULL OR `t_alt_y_grid` = 0;
UPDATE `graph_templates_graph` SET `t_right_axis` = '' WHERE  `t_right_axis` IS NULL OR `t_right_axis` = 0;
UPDATE `graph_templates_graph` SET `t_right_axis_label` = '' WHERE  `t_right_axis_label` IS NULL OR `t_right_axis_label` = 0;
UPDATE `graph_templates_graph` SET `t_right_axis_format` = '' WHERE  `t_right_axis_format` IS NULL OR `t_right_axis_format` = 0;
UPDATE `graph_templates_graph` SET `t_right_axis_formatter` = '' WHERE  `t_right_axis_formatter` IS NULL OR `t_right_axis_formatter` = 0;
UPDATE `graph_templates_graph` SET `t_left_axis_formatter` = '' WHERE  `t_left_axis_formatter` IS NULL OR `t_left_axis_formatter` = 0;
UPDATE `graph_templates_graph` SET `t_no_gridfit` = '' WHERE  `t_no_gridfit` IS NULL OR `t_no_gridfit` = 0;
UPDATE `graph_templates_graph` SET `t_unit_length` = '' WHERE  `t_unit_length` IS NULL OR `t_unit_length` = 0;
UPDATE `graph_templates_graph` SET `t_tab_width` = '' WHERE  `t_tab_width` IS NULL OR `t_tab_width` = 0;
UPDATE `graph_templates_graph` SET `t_dynamic_labels` = '' WHERE  `t_dynamic_labels` IS NULL OR `t_dynamic_labels` = 0;
UPDATE `graph_templates_graph` SET `t_force_rules_legend` = '' WHERE  `t_force_rules_legend` IS NULL OR `t_force_rules_legend` = 0;
UPDATE `graph_templates_graph` SET `t_legend_position` = '' WHERE  `t_legend_position` IS NULL OR `t_legend_position` = 0;
UPDATE `graph_templates_graph` SET `t_legend_direction` = '' WHERE  `t_legend_direction` IS NULL OR `t_legend_direction` = 0;
UPDATE `graph_templates_graph` SET `t_image_format_id` = '' WHERE  `t_image_format_id` IS NULL OR `t_image_format_id` = 0;
UPDATE `graph_templates_graph` SET `t_title` = '' WHERE  `t_title` IS NULL OR `t_title` = 0;
UPDATE `graph_templates_graph` SET `t_height` = '' WHERE  `t_height` IS NULL OR `t_height` = 0;
UPDATE `graph_templates_graph` SET `t_width` = '' WHERE  `t_width` IS NULL OR `t_width` = 0;
UPDATE `graph_templates_graph` SET `t_upper_limit` = '' WHERE  `t_upper_limit` IS NULL OR `t_upper_limit` = 0;
UPDATE `graph_templates_graph` SET `t_lower_limit` = '' WHERE  `t_lower_limit` IS NULL OR `t_lower_limit` = 0;
UPDATE `graph_templates_graph` SET `t_vertical_label` = '' WHERE  `t_vertical_label` IS NULL OR `t_vertical_label` = 0;
UPDATE `graph_templates_graph` SET `t_slope_mode` = '' WHERE `t_slope_mode` IS NULL OR `t_slope_mode` = 0;
UPDATE `graph_templates_graph` SET `t_auto_scale` = '' WHERE `t_auto_scale` IS NULL OR `t_auto_scale` = 0;

UPDATE `graph_templates_graph` SET `unit_value`='' WHERE `unit_value`='on';


SET @sequence=0;
UPDATE `graph_tree` SET `sequence`= @sequence:= (@sequence+1) ORDER BY `name`;


