-- MySQL dump 10.11
--
-------------------------- DB nagiosbp ------------------------------
CREATE DATABASE IF NOT EXISTS nagiosbp;
USE nagiosbp;

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
-- Table structure for table `bp`
--

DROP TABLE IF EXISTS `bp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bp` (
  `name` varchar(255) NOT NULL,
  `description` varchar(255) default NULL,
  `priority` varchar(5) default NULL,
  `type` varchar(3) NOT NULL,
  `command` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `min_value` varchar(5) default NULL,
  `is_define` tinyint(1) default '0',
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bp_links`
--

DROP TABLE IF EXISTS `bp_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bp_links` (
  `id` mediumint(9) NOT NULL auto_increment,
  `bp_name` varchar(255) NOT NULL,
  `bp_link` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `bp_services`
--

DROP TABLE IF EXISTS `bp_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bp_services` (
  `id` mediumint(9) NOT NULL auto_increment,
  `bp_name` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `service` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

GRANT ALL ON nagiosbp.* TO eonweb@localhost IDENTIFIED BY 'root66';

-------------------------- DB eonweb ------------------------------
USE eonweb;

ALTER TABLE auth_settings ADD COLUMN ldap_group_filter varchar(255);
ALTER TABLE auth_settings CHANGE ldap_filter ldap_user_filter varchar(255);
ALTER TABLE groups ADD COLUMN group_type tinyint(1);
ALTER TABLE groups ADD COLUMN group_dn varchar(255);

--
-- Table structure for table `ldap_groups_extended`
--


CREATE TABLE `ldap_groups_extended` (
  `dn` varchar(255) NOT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `checked` smallint(6) NOT NULL,
  PRIMARY KEY (`dn`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

ALTER TABLE users ADD user_language CHAR(2) DEFAULT '0';

CREATE TABLE IF NOT EXISTS configs (
  `name` varchar(255) NOT NULL,   
  `value` text NOT NULL,
  PRIMARY KEY (`name`) 
);



-------------------------- DB notifier ------------------------------
CREATE DATABASE IF NOT EXISTS notifier;
USE notifier;

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
-- Table structure for table `configs`
--

DROP TABLE IF EXISTS `configs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `configs` (
  `id` bigint unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configs`
--

LOCK TABLES `configs` WRITE;
/*!40000 ALTER TABLE `configs` DISABLE KEYS */;
INSERT INTO `configs` VALUES (1,'debug','cfg','0'),(2,'debug_rules','rules','2'),(3,'log_file','cfg','/srv/eyesofnetwork/notifier/log/notifier.log'),(4,'logrules_file','rules','/srv/eyesofnetwork/notifier/log/notifier_rules.log'),(5,'notifsent_file','rules','/srv/eyesofnetwork/notifier/log/notifier_send.log');
/*!40000 ALTER TABLE `configs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contacts` (
  `name` varchar(255) NOT NULL,
  `debug` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `methods`
--

DROP TABLE IF EXISTS `methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `methods` (
  `id` bigint unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `line` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `methods`
--

LOCK TABLES `methods` WRITE;
/*!40000 ALTER TABLE `methods` DISABLE KEYS */;
INSERT INTO `methods` VALUES (1,'email-host','host','/usr/bin/printf \"%b\" \"***** EyesOfNetwork  *****\\\\n\\\\nNotification Type: $NOTIFICATIONTYPE$\\\\nHost: $HOSTNAME$\\\\nState: $HOSTSTATE$\\\\nAddress: $HOSTADDRESS$\\\\nInfo: $HOSTOUTPUT$\\\\n\\\\nDate/Time: $LONGDATETIME$\\\\n\" | /bin/mail -s \"Host $HOSTSTATE$ alert for $HOSTNAME$!\" $CONTACTEMAIL$'),(2,'email-service','service','/usr/bin/printf \"%b\" \"*****  EyesOfNetwork *****\\\\n\\\\nNotification Type: $NOTIFICATIONTYPE$\\\\n\\\\nService: $SERVICEDESC$\\\\nHost: $HOSTALIAS$\\\\nAddress: $HOSTADDRESS$\\\\nState: $SERVICESTATE$\\\\n\\\\nDate/Time: $LONGDATETIME$\\\\n\\\\nAdditional Info:\\\\n\\\\n$SERVICEOUTPUT$\" | /bin/mail -s \"Services $SERVICESTATE$ alert for $HOSTNAME$/$SERVICEDESC$!\" $CONTACTEMAIL$');
/*!40000 ALTER TABLE `methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rule_method`
--

DROP TABLE IF EXISTS `rule_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rule_method` (
  `rule_id` bigint unsigned NOT NULL,
  `method_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`rule_id`,`method_id`),
  KEY `rule_id` (`rule_id`),
  KEY `method_id` (`method_id`),
  CONSTRAINT `rule_method_ibfk_1` FOREIGN KEY (`rule_id`) REFERENCES `rules` (`id`),
  CONSTRAINT `rule_method_ibfk_2` FOREIGN KEY (`method_id`) REFERENCES `methods` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rule_method`
--

LOCK TABLES `rule_method` WRITE;
/*!40000 ALTER TABLE `rule_method` DISABLE KEYS */;
INSERT INTO `rule_method` VALUES (1,1),(2,1),(3,2),(4,2);
/*!40000 ALTER TABLE `rule_method` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rules`
--

DROP TABLE IF EXISTS `rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rules` (
  `id` bigint unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `debug` tinyint(1) NOT NULL DEFAULT '0',
  `contact` varchar(255) NOT NULL DEFAULT '*',
  `host` varchar(255) NOT NULL DEFAULT '*',
  `service` varchar(255) NOT NULL DEFAULT '*',
  `state` varchar(255) NOT NULL DEFAULT '*',
  `notificationnumber` varchar(255) NOT NULL DEFAULT '*',
  `timeperiod_id` bigint unsigned NOT NULL,
  `sort_key` int(32) NOT NULL default 0,
  PRIMARY KEY (`id`),
  KEY `timeperiod_id` (`timeperiod_id`),
  CONSTRAINT `rules_ibfk_1` FOREIGN KEY (`timeperiod_id`) REFERENCES `timeperiods` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rules`
--

LOCK TABLES `rules` WRITE;
/*!40000 ALTER TABLE `rules` DISABLE KEYS */;
INSERT INTO `rules` VALUES (1,'HOSTS UP (24x7)','host',0,'*','*','-','UP','*',1,0),(2,'HOSTS ALERTS (24x7)','host',0,'*','*','-','*','1',1,1),(3,'SERVICES OK (24x7)','service',0,'*','*','*','OK','*',1,0),(4,'SERVICES ALERTS (24x7)','service',0,'*','*','*','*','1',1,1);
/*!40000 ALTER TABLE `rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timeperiods`
--

DROP TABLE IF EXISTS `timeperiods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timeperiods` (
  `id` bigint unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `daysofweek` varchar(255) NOT NULL DEFAULT '*',
  `timeperiod` varchar(255) NOT NULL DEFAULT '*',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timeperiods`
--

LOCK TABLES `timeperiods` WRITE;
/*!40000 ALTER TABLE `timeperiods` DISABLE KEYS */;
INSERT INTO `timeperiods` VALUES (1,'24x7','*','*');
/*!40000 ALTER TABLE `timeperiods` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-03-13 15:55:32
GRANT ALL ON notifier.* TO eonweb@localhost IDENTIFIED BY 'root66';

