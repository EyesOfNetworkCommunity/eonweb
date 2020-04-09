-- MySQL dump 10.14  Distrib 5.5.60-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: eonweb
-- ------------------------------------------------------
-- Server version	5.5.60-MariaDB

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
-- Table structure for table `itsm_champ_ged`
--

/*DROP TABLE IF EXISTS `itsm_champ_ged`;*/
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itsm_champ_ged` (
  `champ_ged_id` int(11) NOT NULL AUTO_INCREMENT,
  `champ_ged_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`champ_ged_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itsm_champ_ged`
--

--
-- Table structure for table `itsm`
--

/*DROP TABLE IF EXISTS `itsm`;*/
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itsm` (
  `itsm_id` int(11) NOT NULL AUTO_INCREMENT,
  `itsm_url` varchar(100) NOT NULL,
  `itsm_file` varchar(100) DEFAULT NULL,
  `itsm_ordre` int(11) DEFAULT NULL,
  `itsm_parent` int(11) DEFAULT NULL,
  `itsm_return_champ` varchar(25) DEFAULT NULL,
  `itsm_type_request` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`itsm_id`),
  KEY `parent_id_fk` (`itsm_parent`),
  CONSTRAINT `parent_id_fk` FOREIGN KEY (`itsm_parent`) REFERENCES `itsm` (`itsm_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itsm`
--

--
-- Table structure for table `itsm_header`
--

/*DROP TABLE IF EXISTS `itsm_header`;*/
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itsm_header` (
  `itsm_header_id` int(11) NOT NULL AUTO_INCREMENT,
  `itsm_id` int(11) DEFAULT NULL,
  `header` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`itsm_header_id`),
  KEY `url_id_fk` (`itsm_id`),
  CONSTRAINT `url_id_fk` FOREIGN KEY (`itsm_id`) REFERENCES `itsm` (`itsm_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itsm_header`
--


--
-- Table structure for table `itsm_var`
--

/*DROP TABLE IF EXISTS `itsm_var`;*/
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itsm_var` (
  `itsm_var_id` int(11) NOT NULL AUTO_INCREMENT,
  `itsm_var_name` varchar(25) DEFAULT NULL,
  `itsm_id` int(11) DEFAULT NULL,
  `champ_ged_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`itsm_var_id`),
  KEY `itsm_fk` (`itsm_id`),
  KEY `champ_ged_fk` (`champ_ged_id`),
  CONSTRAINT `champ_ged_fk` FOREIGN KEY (`champ_ged_id`) REFERENCES `itsm_champ_ged` (`champ_ged_id`) ON DELETE CASCADE,
  CONSTRAINT `itsm_fk` FOREIGN KEY (`itsm_id`) REFERENCES `itsm` (`itsm_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itsm_var`
--




LOCK TABLES `itsm_champ_ged` WRITE;
/*!40000 ALTER TABLE `itsm_champ_ged` DISABLE KEYS */;
INSERT INTO `itsm_champ_ged` VALUES (1,'comments'),(2,'description'),(3,'ip_address'),(4,'equipment'),(5,'service'),(6,'hostgroups'),(7,'servicegroups');
/*!40000 ALTER TABLE `itsm_champ_ged` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-09-06  9:56:25
