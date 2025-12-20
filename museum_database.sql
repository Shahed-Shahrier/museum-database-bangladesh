-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: museum_database
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `art_piece`
--

DROP TABLE IF EXISTS `art_piece`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `art_piece` (
  `Art_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) NOT NULL,
  `Creation_date` date DEFAULT NULL,
  `Acquisition_date` date DEFAULT NULL,
  `Medium` varchar(120) DEFAULT NULL,
  `Artist_ID` int(11) NOT NULL,
  `Gallery_ID` int(11) NOT NULL,
  PRIMARY KEY (`Art_ID`),
  KEY `idx_artpiece_artist` (`Artist_ID`),
  KEY `idx_artpiece_gallery` (`Gallery_ID`),
  CONSTRAINT `fk_artpiece_artist` FOREIGN KEY (`Artist_ID`) REFERENCES `artist` (`Artist_ID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_artpiece_gallery` FOREIGN KEY (`Gallery_ID`) REFERENCES `gallery` (`Gallery_ID`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `art_piece`
--

LOCK TABLES `art_piece` WRITE;
/*!40000 ALTER TABLE `art_piece` DISABLE KEYS */;
/*!40000 ALTER TABLE `art_piece` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `artist`
--

DROP TABLE IF EXISTS `artist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `artist` (
  `Artist_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `Museum_ID` int(11) NOT NULL,
  PRIMARY KEY (`Artist_ID`),
  KEY `idx_artist_museum` (`Museum_ID`),
  CONSTRAINT `fk_artist_museum` FOREIGN KEY (`Museum_ID`) REFERENCES `museum` (`Museum_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `artist`
--

LOCK TABLES `artist` WRITE;
/*!40000 ALTER TABLE `artist` DISABLE KEYS */;
/*!40000 ALTER TABLE `artist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `Event_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Date` date DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Event_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery`
--

DROP TABLE IF EXISTS `gallery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gallery` (
  `Gallery_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Floor_no` int(11) DEFAULT NULL,
  `Room_no` varchar(20) DEFAULT NULL,
  `Museum_ID` int(11) NOT NULL,
  PRIMARY KEY (`Gallery_ID`),
  KEY `idx_gallery_museum` (`Museum_ID`),
  CONSTRAINT `fk_gallery_museum` FOREIGN KEY (`Museum_ID`) REFERENCES `museum` (`Museum_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery`
--

LOCK TABLES `gallery` WRITE;
/*!40000 ALTER TABLE `gallery` DISABLE KEYS */;
/*!40000 ALTER TABLE `gallery` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `museum`
--

DROP TABLE IF EXISTS `museum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `museum` (
  `Museum_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `City` varchar(120) DEFAULT NULL,
  `Contact_no` varchar(100) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Museum_ID`),
  UNIQUE KEY `uq_museum_email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `museum`
--

LOCK TABLES `museum` WRITE;
/*!40000 ALTER TABLE `museum` DISABLE KEYS */;
INSERT INTO `museum` VALUES (1,'Bangladesh National Museum','Dhaka','+88-02-8619396-9; +88-02-8619400','dgmuseum@yahoo.com','National / General history'),(2,'Liberation War Museum','Dhaka','02-222248652-3; 02-48114991-3; 09611677223','mukti.jadughar@gmail.com','National / Liberation war history'),(3,'Varendra Research Museum','Rajshahi',NULL,NULL,'Archaeology & history museum'),(4,'Mainamati Museum','Cumilla','+88 02 33 44 37089; +88 01718 787944','rd_chittagong@archaeology.gov.bd','Archaeological site museum'),(5,'Mahasthan Archaeological Museum (Mahasthangarh Museum)','Bogra','+8801884-533141',NULL,'Archaeological site museum'),(6,'Tajhat Palace / Rangpur Museum','Rangpur',NULL,NULL,'Historic palace / regional museum'),(7,'National Museum of Science and Technology (NMST)','Dhaka','0581-60609; 02-58160609; 01986-254991','infonmst@gmail.com','National science & technology museum'),(8,'Ahsan Manzil Museum','Dhaka','+880258315954','info@ahsanmanzil.org.bd','Historic palace / social-history museum'),(9,'Shilpacharya Zainul Abedin Sangrahashala','Mymensingh','880-091-66960; 880-91-64298','dkzainul@yahoo.com','Art museum / single-artist gallery'),(10,'Sonargaon Folk Art and Craft Museum (Lok Shilpa Jadughar)','Sonargaon (Narayanganj)',NULL,NULL,'Folk art & craft museum'),(11,'Ethnological Museum, Chittagong','Chattogram','031-721734',NULL,'Ethnological museum'),(12,'Bangladesh Air Force Museum','Dhaka','9835981 ext 5718; +8801769-975718; 02-9858440','bafmuseum@gmail.com','Military / aviation museum'),(13,'Bangladesh Military Museum (Bangabandhu Military Museum)','Dhaka','+8801769017770','milmuseum.bd@gmail.com','National military history museum'),(14,'Bangladesh Bank Taka Museum','Dhaka','88029028456; +880-255665001-6','webmaster@bb.org.bd','Currency / numismatics museum'),(15,'Postal Museum, Dhaka','Dhaka',NULL,NULL,'Postal & philately museum'),(16,'Fish Museum & Biodiversity Centre (FMBC)','Mymensingh','8809165874; 091-65874','fishmfh56@gmail.com','Fish / aquatic biodiversity museum');
/*!40000 ALTER TABLE `museum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `museum_event`
--

DROP TABLE IF EXISTS `museum_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `museum_event` (
  `Museum_ID` int(11) NOT NULL,
  `Event_ID` int(11) NOT NULL,
  PRIMARY KEY (`Museum_ID`,`Event_ID`),
  KEY `idx_musev_event` (`Event_ID`),
  KEY `idx_musev_museum` (`Museum_ID`),
  CONSTRAINT `fk_musev_event` FOREIGN KEY (`Event_ID`) REFERENCES `events` (`Event_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_musev_museum` FOREIGN KEY (`Museum_ID`) REFERENCES `museum` (`Museum_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `museum_event`
--

LOCK TABLES `museum_event` WRITE;
/*!40000 ALTER TABLE `museum_event` DISABLE KEYS */;
/*!40000 ALTER TABLE `museum_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tickets` (
  `Serial` bigint(20) NOT NULL AUTO_INCREMENT,
  `Type` varchar(50) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Museum_ID` int(11) NOT NULL,
  PRIMARY KEY (`Serial`),
  UNIQUE KEY `uq_ticket_museum_type` (`Museum_ID`,`Type`),
  KEY `idx_tickets_museum` (`Museum_ID`),
  CONSTRAINT `fk_tickets_museum` FOREIGN KEY (`Museum_ID`) REFERENCES `museum` (`Museum_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_ticket_price` CHECK (`Price` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

LOCK TABLES `tickets` WRITE;
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
INSERT INTO `tickets` VALUES (1,'Visiting without guide',40.00,1),(2,'Education pass',20.00,1),(3,'Visiting without guide',50.00,2),(4,'Education pass',20.00,2),(5,'Visiting without guide',20.00,4),(6,'Education pass',5.00,4),(7,'Visiting without guide',20.00,6),(8,'Education pass',5.00,6),(9,'Visiting without guide',40.00,7),(10,'Education pass',20.00,7),(11,'Visiting without guide',40.00,8),(12,'Education pass',20.00,8),(13,'Visiting without guide',50.00,12),(14,'Visiting without guide',150.00,13),(15,'Visiting without guide',0.00,14);
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `User_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Role` enum('admin','guest') NOT NULL DEFAULT 'guest',
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`User_ID`),
  UNIQUE KEY `Username` (`Username`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (2,'Zero','$2y$10$aniyW.t6Rhphcj5WOzdnFuoUG7jBIsVIhJwFl6ZvGLZ4G306Ar2fe','guest','2025-11-16 18:31:25'),(3,'admin77','$2y$10$uqQNey.YKjzDZf8SLSKuHesrqgUEzti6lzmk8Uk1lQr/gFHaqljxC','admin','2025-11-29 14:30:12'),(4,'Zero1','$2y$10$plscGPTL9L3MsM/xN3jY7OH6XxhBq1nH7sE/o85QrFRF91XMHMKdi','guest','2025-11-29 14:34:46');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `visitor`
--

DROP TABLE IF EXISTS `visitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitor` (
  `Serial` bigint(20) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY (`Serial`),
  CONSTRAINT `fk_visitor_ticket` FOREIGN KEY (`Serial`) REFERENCES `tickets` (`Serial`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitor`
--

LOCK TABLES `visitor` WRITE;
/*!40000 ALTER TABLE `visitor` DISABLE KEYS */;
/*!40000 ALTER TABLE `visitor` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-30 22:47:42
