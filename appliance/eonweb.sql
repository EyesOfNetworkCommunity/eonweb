-- MySQL dump 10.11
--
-- Host: localhost    Database: eonweb
-- ------------------------------------------------------
-- Server version	5.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `auth_settings`
--

DROP TABLE IF EXISTS `auth_settings`;
CREATE TABLE `auth_settings` (
  `auth_type` tinyint(1) NOT NULL default '0',
  `ldap_ip` varchar(255) default NULL,
  `ldap_port` int(11) default NULL,
  `ldap_search` varchar(255) default NULL,
  `ldap_user` varchar(255) default NULL,
  `ldap_password` varchar(255) default NULL,
  `ldap_rdn` varchar(255) default NULL,
  `ldap_filter` varchar(255) default NULL,
  PRIMARY KEY  (`auth_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `auth_settings`
--

LOCK TABLES `auth_settings` WRITE;
/*!40000 ALTER TABLE `auth_settings` DISABLE KEYS */;
INSERT INTO `auth_settings` VALUES (0,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `auth_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groupright`
--

DROP TABLE IF EXISTS `groupright`;
CREATE TABLE `groupright` (
  `group_id` int(11) NOT NULL,
  `tab_1` enum('0','1') NOT NULL default '0',
  `tab_2` enum('0','1') NOT NULL default '0',
  `tab_3` enum('0','1') NOT NULL default '0',
  `tab_4` enum('0','1') NOT NULL default '0',
  `tab_5` enum('0','1') NOT NULL default '0',
  `tab_6` enum('0','1') NOT NULL default '0',
  `tab_7` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `groupright`
--

LOCK TABLES `groupright` WRITE;
/*!40000 ALTER TABLE `groupright` DISABLE KEYS */;
INSERT INTO `groupright` VALUES (1,'1','1','1','1','1','1','1');
/*!40000 ALTER TABLE `groupright` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE `groups` (
  `group_id` int(11) unsigned NOT NULL auto_increment,
  `group_name` varchar(255) NOT NULL,
  `group_descr` text,
  `group_dn` varchar(255),
  `group_type` tinyint(1),
  PRIMARY KEY  (`group_id`,`group_name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `groups`
--

LOCK TABLES `groups` WRITE;
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` VALUES (1,'admins','Administrator group',NULL,NULL);
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ldap_groups_extended`
--

DROP TABLE IF EXISTS `ldap_groups_extended`;
CREATE TABLE `ldap_groups_extended` (
  `dn` varchar(255) NOT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `checked` smallint(6) NOT NULL,
  PRIMARY KEY (`dn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ldap_groups_extended`
--

LOCK TABLES `ldap_groups_extended` WRITE;
/*!40000 ALTER TABLE `ldap_groups_extended` DISABLE KEYS */;
/*!40000 ALTER TABLE `ldap_groups_extended` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ldap_users`
--

DROP TABLE IF EXISTS `ldap_users`;
CREATE TABLE `ldap_users` (
  `dn` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  PRIMARY KEY  (`dn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ldap_users`
--

LOCK TABLES `ldap_users` WRITE;
/*!40000 ALTER TABLE `ldap_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `ldap_users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ldap_users_extended`
--

DROP TABLE IF EXISTS `ldap_users_extended`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `ldap_users_extended` (
  `dn` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `checked` smallint(6) NOT NULL,
  PRIMARY KEY  (`dn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;

--
-- Dumping data for table `ldap_users_extended`
--

LOCK TABLES `ldap_users_extended` WRITE;
/*!40000 ALTER TABLE `ldap_users_extended` DISABLE KEYS */;
/*!40000 ALTER TABLE `ldap_users_extended` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
CREATE TABLE `logs` (
  `id` mediumint(9) NOT NULL auto_increment,
  `date` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `session_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) unsigned NOT NULL auto_increment,
  `group_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_passwd` varchar(255) NOT NULL,
  `user_descr` varchar(255) default NULL,
  `user_type` tinyint(1) NOT NULL,
  `user_location` varchar(255) default NULL,
  `user_limitation` tinyint(1) NOT NULL,
  PRIMARY KEY  (`user_id`,`user_name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'admin','21232f297a57a5a743894a0e4a801fc3','default user',0,'',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

--
-- EONWEB USER RIGHTS
--

GRANT ALL ON eonweb.* TO eonweb@localhost IDENTIFIED BY 'root66';
GRANT ALL ON lilac.* TO eonweb@localhost IDENTIFIED BY 'root66';
GRANT ALL ON ged.* TO eonweb@localhost IDENTIFIED BY 'root66';
GRANT ALL ON cacti.* TO eonweb@localhost IDENTIFIED BY 'root66';
