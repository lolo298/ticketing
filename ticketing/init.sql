/*!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.18-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 192.168.1.115    Database: ticketing
-- ------------------------------------------------------
-- Server version	8.1.0

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
-- Current Database: `ticketing`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `ticketing` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `ticketing`;

--
-- Table structure for table `assoc_utilisateur_role`
--

DROP TABLE IF EXISTS `assoc_utilisateur_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assoc_utilisateur_role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `id_role` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_role` (`id_role`)
) ENGINE=InnoDB AUTO_INCREMENT=1082 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assoc_utilisateur_role`
--

LOCK TABLES `assoc_utilisateur_role` WRITE;
/*!40000 ALTER TABLE `assoc_utilisateur_role` DISABLE KEYS */;
INSERT INTO `assoc_utilisateur_role` VALUES (1,1,1);
/*!40000 ALTER TABLE `assoc_utilisateur_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` int DEFAULT NULL,
  `id_entreprise` int DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_entreprise` (`id_entreprise`),
  CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`id_entreprise`) REFERENCES `enterprises` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enterprises`
--

DROP TABLE IF EXISTS `enterprises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `enterprises` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enterprises`
--

LOCK TABLES `enterprises` WRITE;
/*!40000 ALTER TABLE `enterprises` DISABLE KEYS */;
/*!40000 ALTER TABLE `enterprises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `priority`
--

DROP TABLE IF EXISTS `priority`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `priority` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `priority`
--

LOCK TABLES `priority` WRITE;
/*!40000 ALTER TABLE `priority` DISABLE KEYS */;
INSERT INTO `priority` VALUES (1,'CRITICAL'),(2,'URGENT'),(3,'LOW');
/*!40000 ALTER TABLE `priority` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'ADMIN'),(2,'USER'),(3,'CLIENT');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `state`
--

DROP TABLE IF EXISTS `state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `state` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `state`
--

LOCK TABLES `state` WRITE;
/*!40000 ALTER TABLE `state` DISABLE KEYS */;
INSERT INTO `state` VALUES (1,'OPEN'),(2,'CLOSED'),(3,'WAITING');
/*!40000 ALTER TABLE `state` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Ticket`
--

DROP TABLE IF EXISTS `Ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ticket` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int NOT NULL,
  `id_type` int NOT NULL,
  `id_priority` int NOT NULL,
  `id_state` int NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `filepath` varchar(255) DEFAULT NULL,
  `creation_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_priority` (`id_priority`),
  KEY `id_state` (`id_state`),
  KEY `id_type` (`id_type`),
  KEY `id_utilisateur` (`id_utilisateur`),
  CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`id_priority`) REFERENCES `priority` (`id`),
  CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`id_state`) REFERENCES `state` (`id`),
  CONSTRAINT `ticket_ibfk_3` FOREIGN KEY (`id_type`) REFERENCES `type` (`id`),
  CONSTRAINT `ticket_ibfk_4` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Ticket`
--

LOCK TABLES `Ticket` WRITE;
/*!40000 ALTER TABLE `Ticket` DISABLE KEYS */;
INSERT INTO `Ticket` VALUES (1,1,1,3,1,'test','test',NULL,'2024-11-03 12:28:30','2024-11-04 09:15:43'),(2,1,1,3,3,'test','new ticket',NULL,'2024-11-03 12:44:27','2024-11-04 09:15:43'),(3,1,1,1,1,'test orm',NULL,NULL,'2024-11-04 12:05:32','2024-11-04 12:05:32'),(36,97,1,1,1,'test lolo','sss','','2025-01-14 17:06:45','2025-01-14 17:06:45');
/*!40000 ALTER TABLE `Ticket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `traitements`
--

DROP TABLE IF EXISTS `traitements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `traitements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_ticket` int DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_ticket` (`id_ticket`),
  CONSTRAINT `traitements_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `Ticket` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `traitements`
--

LOCK TABLES `traitements` WRITE;
/*!40000 ALTER TABLE `traitements` DISABLE KEYS */;
/*!40000 ALTER TABLE `traitements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `type`
--

DROP TABLE IF EXISTS `type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `type`
--

LOCK TABLES `type` WRITE;
/*!40000 ALTER TABLE `type` DISABLE KEYS */;
INSERT INTO `type` VALUES (1,'Generic help');
/*!40000 ALTER TABLE `type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT NULL,
  `creation_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_role` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role` (`id_role`),
  CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilisateur`
--

LOCK TABLES `utilisateur` WRITE;
/*!40000 ALTER TABLE `utilisateur` DISABLE KEYS */;
INSERT INTO `utilisateur` VALUES (1,'admin','$2y$10$fTdLg.fTAKDD7cPRZo1Kfuz0Mc8C72iamhO.o8L7Ei7iMX5BBi2Dy',1,'2024-11-03 12:27:47',1),(97,'lolo','$2y$10$CiM99aNYa0.SVUFN5klR0uW7CW1kArspNQL7gq8YxtNqaHgFUx5SC',1,'2025-01-14 16:36:05',2);
/*!40000 ALTER TABLE `utilisateur` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-01-14 18:08:55
