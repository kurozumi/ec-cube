# ************************************************************
# Sequel Pro SQL dump
# Version 5446
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.44)
# Database: eccube_demo
# Generation Time: 2024-09-07 06:49:28 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table dtb_member
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dtb_member`;

CREATE TABLE `dtb_member` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `work_id` smallint(5) unsigned DEFAULT NULL,
  `authority_id` smallint(5) unsigned DEFAULT NULL,
  `creator_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `login_id` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `salt` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `sort_no` smallint(5) unsigned NOT NULL,
  `two_factor_auth_key` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `two_factor_auth_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `login_date` datetime DEFAULT NULL COMMENT '(DC2Type:datetimetz)',
  `discriminator_type` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_10BC3BE6BB3453DB` (`work_id`),
  KEY `IDX_10BC3BE681EC865B` (`authority_id`),
  KEY `IDX_10BC3BE661220EA6` (`creator_id`),
  CONSTRAINT `FK_10BC3BE661220EA6` FOREIGN KEY (`creator_id`) REFERENCES `dtb_member` (`id`),
  CONSTRAINT `FK_10BC3BE681EC865B` FOREIGN KEY (`authority_id`) REFERENCES `mtb_authority` (`id`),
  CONSTRAINT `FK_10BC3BE6BB3453DB` FOREIGN KEY (`work_id`) REFERENCES `mtb_work` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

LOCK TABLES `dtb_member` WRITE;
/*!40000 ALTER TABLE `dtb_member` DISABLE KEYS */;

INSERT INTO `dtb_member` (`id`, `work_id`, `authority_id`, `creator_id`, `name`, `department`, `login_id`, `password`, `salt`, `sort_no`, `two_factor_auth_key`, `two_factor_auth_enabled`, `create_date`, `update_date`, `login_date`, `discriminator_type`)
VALUES
	(2,1,1,3,X'64656D6F',X'64656D6F',X'64656D6F',X'24327924313324754C78676B392E6F6276484639387977542F694F674F31456F7873337A6E65466D5147595348532F61437561703667726B2F417965',NULL,1,NULL,0,'2024-09-07 06:00:54','2024-09-07 06:00:54',NULL,X'6D656D626572'),
	(3,1,0,3,X'E7AEA1E79086E88085',X'E7AEA1E79086E88085',X'615F7A756D69',X'243279243133246677704A504D357A38454B4F4C6E4E72736C506E642E6875774C6B44766E6E374F3730762E475970757439574C6E3176454C6B7A4F',NULL,2,X'4F594347343642343653585A50333746',1,'2024-09-07 06:40:44','2024-09-07 06:44:12','2024-09-07 06:43:27',X'6D656D626572');

/*!40000 ALTER TABLE `dtb_member` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
