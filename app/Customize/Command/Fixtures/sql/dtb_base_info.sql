# ************************************************************
# Sequel Pro SQL dump
# Version 5446
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.44)
# Database: eccube_demo
# Generation Time: 2024-09-07 06:05:51 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table dtb_base_info
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dtb_base_info`;

CREATE TABLE `dtb_base_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` smallint(5) unsigned DEFAULT NULL,
  `pref_id` smallint(5) unsigned DEFAULT NULL,
  `company_name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `company_kana` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `postal_code` varchar(8) COLLATE utf8mb4_bin DEFAULT NULL,
  `addr01` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `addr02` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `phone_number` varchar(14) COLLATE utf8mb4_bin DEFAULT NULL,
  `business_hour` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `email01` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `email02` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `email03` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `email04` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `shop_name` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `shop_kana` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `shop_name_eng` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `update_date` datetime NOT NULL COMMENT '(DC2Type:datetimetz)',
  `good_traded` varchar(4000) COLLATE utf8mb4_bin DEFAULT NULL,
  `message` varchar(4000) COLLATE utf8mb4_bin DEFAULT NULL,
  `delivery_free_amount` decimal(12,2) unsigned DEFAULT NULL,
  `delivery_free_quantity` int(10) unsigned DEFAULT NULL,
  `option_mypage_order_status_display` tinyint(1) NOT NULL DEFAULT '1',
  `option_nostock_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `option_favorite_product` tinyint(1) NOT NULL DEFAULT '1',
  `option_product_delivery_fee` tinyint(1) NOT NULL DEFAULT '0',
  `invoice_registration_number` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `option_product_tax_rule` tinyint(1) NOT NULL DEFAULT '0',
  `option_customer_activate` tinyint(1) NOT NULL DEFAULT '1',
  `option_remember_me` tinyint(1) NOT NULL DEFAULT '1',
  `option_mail_notifier` tinyint(1) NOT NULL DEFAULT '0',
  `authentication_key` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `php_path` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `option_point` tinyint(1) NOT NULL DEFAULT '1',
  `basic_point_rate` decimal(10,0) unsigned DEFAULT '1',
  `point_conversion_rate` decimal(10,0) unsigned DEFAULT '1',
  `ga_id` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `discriminator_type` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1D3655F4F92F3E70` (`country_id`),
  KEY `IDX_1D3655F4E171EF5F` (`pref_id`),
  CONSTRAINT `FK_1D3655F4E171EF5F` FOREIGN KEY (`pref_id`) REFERENCES `mtb_pref` (`id`),
  CONSTRAINT `FK_1D3655F4F92F3E70` FOREIGN KEY (`country_id`) REFERENCES `mtb_country` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

LOCK TABLES `dtb_base_info` WRITE;
/*!40000 ALTER TABLE `dtb_base_info` DISABLE KEYS */;

INSERT INTO `dtb_base_info` (`id`, `country_id`, `pref_id`, `company_name`, `company_kana`, `postal_code`, `addr01`, `addr02`, `phone_number`, `business_hour`, `email01`, `email02`, `email03`, `email04`, `shop_name`, `shop_kana`, `shop_name_eng`, `update_date`, `good_traded`, `message`, `delivery_free_amount`, `delivery_free_quantity`, `option_mypage_order_status_display`, `option_nostock_hidden`, `option_favorite_product`, `option_product_delivery_fee`, `invoice_registration_number`, `option_product_tax_rule`, `option_customer_activate`, `option_remember_me`, `option_mail_notifier`, `authentication_key`, `php_path`, `option_point`, `basic_point_rate`, `point_conversion_rate`, `ga_id`, `discriminator_type`)
VALUES
	(1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,X'696E666F40612D7A756D692E6E6574',X'696E666F40612D7A756D692E6E6574',X'696E666F40612D7A756D692E6E6574',X'696E666F40612D7A756D692E6E6574',X'E38182E3819AE381BF2E6E6574E381AEE38397E383A9E382B0E382A4E383B3E7A2BAE8AA8DE794A82045432D43554245342E322F342E3320E38387E383A2E382B5E382A4E38388',NULL,NULL,'2024-09-07 05:44:55',NULL,NULL,NULL,NULL,1,0,1,0,NULL,0,1,1,1,X'625856696A5548326271646D4B6737714E6A5473494D6530446559504D4B736130746D74716F577A',NULL,1,1,1,NULL,X'62617365696E666F');

/*!40000 ALTER TABLE `dtb_base_info` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
