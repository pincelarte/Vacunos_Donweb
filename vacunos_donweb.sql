-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: vacunos_donweb
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
-- Table structure for table `establecimientos`
--

DROP TABLE IF EXISTS `establecimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `establecimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `establecimientos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `establecimientos`
--

LOCK TABLES `establecimientos` WRITE;
/*!40000 ALTER TABLE `establecimientos` DISABLE KEYS */;
INSERT INTO `establecimientos` VALUES (13,'la esperanza','tandil',7),(14,'el hervidero','vela',8);
/*!40000 ALTER TABLE `establecimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pesajes`
--

DROP TABLE IF EXISTS `pesajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pesajes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_vaca` int(11) DEFAULT NULL,
  `caravana_vacuno` varchar(50) DEFAULT NULL,
  `peso` float DEFAULT NULL,
  `fecha_pesaje` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pesajes_ibfk_1` (`caravana_vacuno`),
  KEY `fk_vaca_pesaje` (`id_vaca`),
  CONSTRAINT `fk_vaca_pesaje` FOREIGN KEY (`id_vaca`) REFERENCES `vacunos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pesajes_ibfk_1` FOREIGN KEY (`caravana_vacuno`) REFERENCES `vacunos` (`caravana`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pesajes`
--

LOCK TABLES `pesajes` WRITE;
/*!40000 ALTER TABLE `pesajes` DISABLE KEYS */;
INSERT INTO `pesajes` VALUES (1,11,'1',39,'2026-02-06 16:29:18'),(3,12,'1',50,'2026-02-06 16:30:25'),(4,12,'1',100,'2026-02-06 16:30:36');
/*!40000 ALTER TABLE `pesajes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre_usuario` (`nombre_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (7,'juan','$2y$10$.5LM7SbApMNdliSSkVy34.33SuKKxKYtDEXx5qhl4.Nsz9jPRprxS'),(8,'admin','$2y$10$WcyjVCojjbqG2d6Tmca3aeJXpJB3qIfkiXXM42G.bcCWTdCHgvrs2');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vacunos`
--

DROP TABLE IF EXISTS `vacunos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vacunos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `caravana` varchar(50) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `raza` varchar(50) DEFAULT NULL,
  `edad` date DEFAULT NULL,
  `peso_actual` float DEFAULT NULL,
  `historial` text DEFAULT NULL,
  `id_establecimiento` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_establecimiento` (`id_establecimiento`),
  KEY `caravana` (`caravana`),
  CONSTRAINT `vacunos_ibfk_1` FOREIGN KEY (`id_establecimiento`) REFERENCES `establecimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vacunos`
--

LOCK TABLES `vacunos` WRITE;
/*!40000 ALTER TABLE `vacunos` DISABLE KEYS */;
INSERT INTO `vacunos` VALUES (11,'1','Vaca','Holando Argentino','2026-01-11',39,'lala',13),(12,'1','Vaca','Holando Argentino','2024-03-06',100,'',14);
/*!40000 ALTER TABLE `vacunos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-12 13:43:23
