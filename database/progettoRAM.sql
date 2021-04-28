-- Copyright 2021 Roberto Basla

-- This file is part of progettoRAM.

-- progettoRAM is free software: you can redistribute it and/or modify
-- it under the terms of the GNU Affero General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.

-- progettoRAM is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU Affero General Public License for more details.

-- You should have received a copy of the GNU Affero General Public License
-- along with progettoRAM.  If not, see <http://www.gnu.org/licenses/>.

-- Collection of functions related to the administrative section

CREATE DATABASE  IF NOT EXISTS `progettoram` /*!40100 DEFAULT CHARACTER SET utf8 */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `progettoram`;
-- MySQL dump 10.13  Distrib 8.0.22, for Win64 (x86_64)
--
-- Host: localhost    Database: progettoram
-- ------------------------------------------------------
-- Server version	8.0.22

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `admindata`
--

DROP TABLE IF EXISTS `admindata`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `admindata` (
  `index_text` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `index_compiled` text,
  `project_text` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `project_compiled` text,
  `data_id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`data_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admindata`
--

LOCK TABLES `admindata` WRITE;
/*!40000 ALTER TABLE `admindata` DISABLE KEYS */;
INSERT INTO `admindata` VALUES (NULL,NULL,NULL,NULL,1);
/*!40000 ALTER TABLE `admindata` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `ADMINDATA_COUNT` BEFORE INSERT ON `admindata` FOR EACH ROW IF((SELECT COUNT(*) FROM admindata) > 0) 

    THEN SIGNAL SQLSTATE '45001' SET MESSAGE_TEXT = 'Admin data table already defined';

END IF */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `ADMINDATA_ABORT_DELETE` BEFORE DELETE ON `admindata` FOR EACH ROW SIGNAL SQLSTATE '45002' SET MESSAGE_TEXT = 'Cannot delete admin data' */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `class`
--

DROP TABLE IF EXISTS `class`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `class` (
  `class_id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `class` tinyint unsigned NOT NULL,
  `section` varchar(5) NOT NULL,
  `class_year` year NOT NULL,
  `user_fk` smallint unsigned DEFAULT NULL,
  `school_fk` smallint unsigned NOT NULL,
  PRIMARY KEY (`class_id`),
  UNIQUE KEY `class_section_year_school_uq` (`class`,`section`,`class_year`,`school_fk`) USING BTREE,
  KEY `user_fk` (`user_fk`),
  KEY `school_fk` (`school_fk`),
  KEY `year` (`class_year`),
  CONSTRAINT `class_school_fk` FOREIGN KEY (`school_fk`) REFERENCES `school` (`school_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `class_user_fk` FOREIGN KEY (`user_fk`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `datatype`
--

DROP TABLE IF EXISTS `datatype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `datatype` (
  `datatype_id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `datatype_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `step` float unsigned NOT NULL,
  PRIMARY KEY (`datatype_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `datatype`
--

LOCK TABLES `datatype` WRITE;
/*!40000 ALTER TABLE `datatype` DISABLE KEYS */;
INSERT INTO `datatype` VALUES (1,'Interi',1),(3,'Frazionari',0.01),(4,'Selezionati (Passo 5)',5);
/*!40000 ALTER TABLE `datatype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `favourites`
--

DROP TABLE IF EXISTS `favourites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `favourites` (
  `user_fk` smallint unsigned NOT NULL,
  `test_fk` tinyint unsigned NOT NULL,
  PRIMARY KEY (`test_fk`,`user_fk`),
  KEY `fav_user_fk` (`user_fk`),
  CONSTRAINT `fav_test_fk` FOREIGN KEY (`test_fk`) REFERENCES `test` (`test_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fav_user_fk` FOREIGN KEY (`user_fk`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `grade`
--

DROP TABLE IF EXISTS `grade`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grade` (
  `grade_id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `grade` float unsigned NOT NULL,
  `color` varchar(6) NOT NULL,
  PRIMARY KEY (`grade_id`),
  UNIQUE KEY `grade_uq` (`grade`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grade`
--

LOCK TABLES `grade` WRITE;
/*!40000 ALTER TABLE `grade` DISABLE KEYS */;
INSERT INTO `grade` VALUES (1,4,'FF0000'),(2,4.5,'FF7777'),(3,5,'FFAAAA'),(4,5.5,'FFD4D4'),(5,6,'D6FFD6'),(6,6.5,'C1FFC1'),(7,7,'AFFFAF'),(8,7.5,'96FF96'),(9,8,'7CFF7C'),(10,8.5,'60FF60'),(11,9,'49FF49'),(12,9.5,'2DFF2D'),(13,10,'00FF00');
/*!40000 ALTER TABLE `grade` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grading`
--

DROP TABLE IF EXISTS `grading`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grading` (
  `grading_id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `user_fk` smallint unsigned NOT NULL,
  `grade_fk` tinyint unsigned NOT NULL,
  `percentile` float NOT NULL,
  PRIMARY KEY (`grading_id`),
  UNIQUE KEY `user_grade_uq` (`user_fk`,`grade_fk`) USING BTREE,
  KEY `user_fk` (`user_fk`),
  KEY `grade_fk` (`grade_fk`),
  CONSTRAINT `gr_grade_fk` FOREIGN KEY (`grade_fk`) REFERENCES `grade` (`grade_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `gr_user_fk` FOREIGN KEY (`user_fk`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grading`
--

LOCK TABLES `grading` WRITE;
/*!40000 ALTER TABLE `grading` DISABLE KEYS */;
INSERT INTO `grading` VALUES (1,1,1,10),(2,1,2,15),(3,1,3,25),(4,1,4,30),(5,1,5,40),(6,1,6,45),(7,1,7,55),(8,1,8,60),(9,1,9,70),(10,1,10,75),(11,1,11,85),(12,1,12,90),(13,1,13,100);
/*!40000 ALTER TABLE `grading` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `instance`
--

DROP TABLE IF EXISTS `instance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `instance` (
  `instance_id` int unsigned NOT NULL AUTO_INCREMENT,
  `student_fk` smallint unsigned NOT NULL,
  `class_fk` smallint unsigned NOT NULL,
  PRIMARY KEY (`instance_id`),
  UNIQUE KEY `student_class_uq` (`student_fk`,`class_fk`) USING BTREE,
  KEY `fk_ids` (`student_fk`),
  KEY `fk_idcl` (`class_fk`),
  CONSTRAINT `inst_class_fk` FOREIGN KEY (`class_fk`) REFERENCES `class` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inst_student_fk` FOREIGN KEY (`student_fk`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `NO_INST_SAME_YEAR` BEFORE INSERT ON `instance` FOR EACH ROW BEGIN
	IF (EXISTS(
        SELECT * FROM instance 
        JOIN class ON class_fk=class_id
        WHERE student_fk=new.student_fk
    	AND class_year=(
        	SELECT class_year FROM class
            WHERE class_id=new.class_fk
        )
    )) THEN
    		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Errore: classi multiple specificate nello stesso anno per uno studente';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `NO_INST_SAME_YEAR_UPDATE` BEFORE UPDATE ON `instance` FOR EACH ROW BEGIN
	IF (EXISTS(
        SELECT * FROM instance 
        JOIN class ON class_fk=class_id
        WHERE student_fk=new.student_fk
    	AND class_year=(
        	SELECT class_year FROM class
            WHERE class_id=new.class_fk
        )
    )) THEN
    		SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Errore: classi multiple specificate nello stesso anno per uno studente';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `DLT_ST_ON_NO_INST_UPDATE` AFTER UPDATE ON `instance` FOR EACH ROW BEGIN
	IF (NOT EXISTS(SELECT * FROM instance WHERE
        student_fk=old.student_fk)) THEN
    	DELETE FROM student 
    		WHERE student_id=old.student_fk;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `DELETE_ST_ON_NO_INST` AFTER DELETE ON `instance` FOR EACH ROW BEGIN
	IF (NOT EXISTS(SELECT * FROM instance WHERE
        student_fk=old.student_fk)) THEN
    	DELETE FROM student 
    		WHERE student_id=old.student_fk;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `results`
--

DROP TABLE IF EXISTS `results`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `results` (
  `result_id` int unsigned NOT NULL AUTO_INCREMENT,
  `date` date DEFAULT NULL,
  `test_fk` tinyint unsigned NOT NULL,
  `instance_fk` int unsigned DEFAULT NULL,
  `value` float(10,4) NOT NULL,
  PRIMARY KEY (`result_id`),
  UNIQUE KEY `test_fk_instance_fk` (`test_fk`,`instance_fk`),
  KEY `instance_fk` (`instance_fk`),
  KEY `test` (`test_fk`),
  CONSTRAINT `res_instance_fk` FOREIGN KEY (`instance_fk`) REFERENCES `instance` (`instance_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `res_test_fk` FOREIGN KEY (`test_fk`) REFERENCES `test` (`test_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `school`
--

DROP TABLE IF EXISTS `school`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `school` (
  `school_id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `school_name` varchar(40) NOT NULL,
  `city` varchar(40) NOT NULL,
  PRIMARY KEY (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `student` (
  `student_id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `lastname` varchar(30) NOT NULL,
  `firstname` varchar(30) DEFAULT NULL,
  `gender` enum('m','f') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test`
--

DROP TABLE IF EXISTS `test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `test` (
  `test_id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `test_name` varchar(50) NOT NULL,
  `positive_values` enum('Maggiori','Minori') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `datatype_fk` tinyint unsigned NOT NULL,
  `testtype_fk` tinyint unsigned NOT NULL,
  `unit_fk` tinyint unsigned NOT NULL,
  `position` tinytext NOT NULL,
  `equipment` tinytext NOT NULL,
  `execution` text NOT NULL,
  `suggestions` text NOT NULL,
  `test_limit` tinytext NOT NULL,
  `assessment` tinytext NOT NULL,
  PRIMARY KEY (`test_id`),
  KEY `unit_fk` (`unit_fk`),
  KEY `datatype_fk` (`datatype_fk`),
  KEY `testtype_fk` (`testtype_fk`),
  CONSTRAINT `test_datatype_fk` FOREIGN KEY (`datatype_fk`) REFERENCES `datatype` (`datatype_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `test_testtype_fk` FOREIGN KEY (`testtype_fk`) REFERENCES `testtype` (`testtype_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `test_unit_fk` FOREIGN KEY (`unit_fk`) REFERENCES `unit` (`unit_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testtype`
--

DROP TABLE IF EXISTS `testtype`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testtype` (
  `testtype_id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `testtype_name` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`testtype_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testtype`
--

LOCK TABLES `testtype` WRITE;
/*!40000 ALTER TABLE `testtype` DISABLE KEYS */;
INSERT INTO `testtype` VALUES (1,'Forza core'),(2,'Mobilità'),(3,'Forza arti inferiori'),(4,'Velocità'),(5,'Resistenza'),(6,'Forza arti superiori');
/*!40000 ALTER TABLE `testtype` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unit`
--

DROP TABLE IF EXISTS `unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unit` (
  `unit_id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `unit_name` varchar(20) NOT NULL,
  `symbol` varchar(5) NOT NULL,
  PRIMARY KEY (`unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unit`
--

LOCK TABLES `unit` WRITE;
/*!40000 ALTER TABLE `unit` DISABLE KEYS */;
INSERT INTO `unit` VALUES (1,'Nessuna',''),(2,'Secondi','s'),(3,'Metri','m'),(4,'Centimetri','cm'),(5,'Gradi','°');
/*!40000 ALTER TABLE `unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `user_id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(32) NOT NULL,
  `privileges` tinyint unsigned NOT NULL DEFAULT '5',
  `granted_by` smallint unsigned DEFAULT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `email` varchar(50) NOT NULL,
  `contact_info` text,
  `show_email` bit(1) NOT NULL DEFAULT b'0',
  `last_password` date DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `school_fk` smallint unsigned DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `school_fk` (`school_fk`),
  KEY `GRANTER` (`granted_by`),
  CONSTRAINT `granter` FOREIGN KEY (`granted_by`) REFERENCES `user` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_school_fk` FOREIGN KEY (`school_fk`) REFERENCES `school` (`school_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'admin',MD5('$PASSWORD'),0,NULL,'','','',NULL,_binary '','',NULL,1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'progettoram'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
