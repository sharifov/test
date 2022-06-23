-- MySQL dump 10.13  Distrib 8.0.26, for Linux (x86_64)
--
-- Host: localhost    Database: crm-local
-- ------------------------------------------------------
-- Server version	8.0.26-0ubuntu0.20.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `abac_doc`
--

DROP TABLE IF EXISTS `abac_doc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `abac_doc` (
  `ad_id` int NOT NULL AUTO_INCREMENT,
  `ad_file` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ad_line` int NOT NULL,
  `ad_subject` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ad_object` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ad_action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ad_description` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ad_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ad_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `abac_policy`
--

DROP TABLE IF EXISTS `abac_policy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `abac_policy` (
  `ap_id` int NOT NULL AUTO_INCREMENT,
  `ap_rule_type` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'p',
  `ap_subject` varchar(10000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ap_subject_json` json DEFAULT NULL,
  `ap_object` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ap_action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ap_action_json` json DEFAULT NULL,
  `ap_effect` tinyint(1) NOT NULL DEFAULT '1',
  `ap_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ap_sort_order` smallint DEFAULT '100',
  `ap_created_dt` datetime DEFAULT NULL,
  `ap_updated_dt` datetime DEFAULT NULL,
  `ap_created_user_id` int DEFAULT NULL,
  `ap_updated_user_id` int DEFAULT NULL,
  `ap_enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`ap_id`),
  KEY `FK-abac_policy-ap_created_user_id` (`ap_created_user_id`),
  KEY `FK-abac_policy-ap_updated_user_id` (`ap_updated_user_id`),
  KEY `IND-abac_policy-ap_sort_order` (`ap_sort_order`),
  KEY `IND-abac_policy-ap_enabled` (`ap_enabled`),
  CONSTRAINT `FK-abac_policy-ap_created_user_id` FOREIGN KEY (`ap_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-abac_policy-ap_updated_user_id` FOREIGN KEY (`ap_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `airlines`
--

DROP TABLE IF EXISTS `airlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `airlines` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `iata` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iaco` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `countryCode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_economy` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_premium_economy` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_business` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_premium_business` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_first` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_premium_first` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_airline_iata` (`iata`)
) ENGINE=InnoDB AUTO_INCREMENT=742 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `airport_lang`
--

DROP TABLE IF EXISTS `airport_lang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `airport_lang` (
  `ail_iata` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ail_lang` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ail_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ail_city` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ail_country` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ail_created_user_id` int DEFAULT NULL,
  `ail_updated_user_id` int DEFAULT NULL,
  `ail_created_dt` datetime DEFAULT NULL,
  `ail_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ail_iata`,`ail_lang`),
  CONSTRAINT `FK-airport_lang-ail_iata` FOREIGN KEY (`ail_iata`) REFERENCES `airports` (`iata`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `airports`
--

DROP TABLE IF EXISTS `airports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `airports` (
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `iata` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(18,14) DEFAULT NULL,
  `longitude` decimal(18,14) DEFAULT NULL,
  `timezone` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dst` smallint DEFAULT NULL,
  `a_created_user_id` int DEFAULT NULL,
  `a_updated_user_id` int DEFAULT NULL,
  `a_icao` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_country_code` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_city_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_state` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `a_rank` decimal(15,12) DEFAULT NULL,
  `a_multicity` tinyint(1) DEFAULT '0',
  `a_close` tinyint(1) DEFAULT '0',
  `a_disabled` tinyint(1) DEFAULT '0',
  `a_created_dt` datetime DEFAULT NULL,
  `a_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`iata`),
  KEY `FK-airports-a_created_user_id` (`a_created_user_id`),
  KEY `FK-airports-a_updated_user_id` (`a_updated_user_id`),
  KEY `IND-airports-a_disabled` (`a_disabled`),
  KEY `IND-airports-a_close` (`a_close`),
  KEY `IND-airports-name` (`name`),
  KEY `IND-airports-city` (`city`),
  KEY `IND-airports-country` (`country`),
  CONSTRAINT `FK-airports-a_created_user_id` FOREIGN KEY (`a_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-airports-a_updated_user_id` FOREIGN KEY (`a_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_log`
--

DROP TABLE IF EXISTS `api_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_log` (
  `al_id` bigint NOT NULL AUTO_INCREMENT,
  `al_request_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `al_request_dt` datetime NOT NULL,
  `al_response_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `al_response_dt` datetime DEFAULT NULL,
  `al_ip_address` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `al_user_id` int DEFAULT NULL,
  `al_action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `al_execution_time` decimal(6,3) DEFAULT NULL,
  `al_memory_usage` int DEFAULT NULL,
  `al_db_execution_time` decimal(6,3) DEFAULT NULL,
  `al_db_query_count` int DEFAULT NULL,
  `al_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`al_id`),
  KEY `api_log_index` (`al_user_id`,`al_request_dt`),
  KEY `IND-api_log_al_action` (`al_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_user`
--

DROP TABLE IF EXISTS `api_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_user` (
  `au_id` int NOT NULL AUTO_INCREMENT,
  `au_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `au_api_username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `au_api_password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `au_email` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `au_project_id` int DEFAULT NULL,
  `au_enabled` tinyint(1) DEFAULT '1',
  `au_updated_dt` datetime DEFAULT NULL,
  `au_updated_user_id` int DEFAULT NULL,
  `au_rate_limit_number` int DEFAULT NULL,
  `au_rate_limit_reset` int DEFAULT NULL,
  PRIMARY KEY (`au_id`),
  UNIQUE KEY `au_api_username` (`au_api_username`),
  KEY `api_user_au_project_id_fkey` (`au_project_id`),
  CONSTRAINT `api_user_au_project_id_fkey` FOREIGN KEY (`au_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `api_user_allowance`
--

DROP TABLE IF EXISTS `api_user_allowance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `api_user_allowance` (
  `aua_user_id` int NOT NULL AUTO_INCREMENT,
  `aua_allowed_number_requests` bigint NOT NULL,
  `aua_last_check_time` bigint NOT NULL,
  PRIMARY KEY (`aua_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `app_project_key`
--

DROP TABLE IF EXISTS `app_project_key`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `app_project_key` (
  `apk_id` int NOT NULL AUTO_INCREMENT,
  `apk_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apk_project_id` int NOT NULL,
  `apk_project_source_id` int NOT NULL,
  `apk_created_dt` datetime DEFAULT NULL,
  `apk_updated_dt` datetime DEFAULT NULL,
  `apk_created_user_id` int DEFAULT NULL,
  `apk_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`apk_id`),
  UNIQUE KEY `apk_key` (`apk_key`),
  KEY `FK-app_project_key-project_id` (`apk_project_id`),
  KEY `FK-app_project_key-project_source_id` (`apk_project_source_id`),
  KEY `FK-app_project_key-created_user_id` (`apk_created_user_id`),
  KEY `FK-app_project_key-updated_user_id` (`apk_updated_user_id`),
  CONSTRAINT `FK-app_project_key-created_user_id` FOREIGN KEY (`apk_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-app_project_key-project_id` FOREIGN KEY (`apk_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-app_project_key-project_source_id` FOREIGN KEY (`apk_project_source_id`) REFERENCES `sources` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-app_project_key-updated_user_id` FOREIGN KEY (`apk_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attraction`
--

DROP TABLE IF EXISTS `attraction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attraction` (
  `atn_id` int NOT NULL AUTO_INCREMENT,
  `atn_product_id` int DEFAULT NULL,
  `atn_date_from` date DEFAULT NULL,
  `atn_date_to` date DEFAULT NULL,
  `atn_destination` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atn_destination_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atn_request_hash_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`atn_id`),
  KEY `FK-attraction-atn_product_id` (`atn_product_id`),
  CONSTRAINT `FK-attraction-atn_product_id` FOREIGN KEY (`atn_product_id`) REFERENCES `product` (`pr_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attraction_pax`
--

DROP TABLE IF EXISTS `attraction_pax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attraction_pax` (
  `atnp_id` int NOT NULL AUTO_INCREMENT,
  `atnp_atn_id` int NOT NULL,
  `atnp_type_id` tinyint NOT NULL,
  `atnp_age` tinyint DEFAULT NULL,
  `atnp_first_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atnp_last_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atnp_dob` date DEFAULT NULL,
  PRIMARY KEY (`atnp_id`),
  KEY `FK-attraction_pax-atnp_atn_id` (`atnp_atn_id`),
  CONSTRAINT `FK-attraction_pax-atnp_atn_id` FOREIGN KEY (`atnp_atn_id`) REFERENCES `attraction` (`atn_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attraction_quote`
--

DROP TABLE IF EXISTS `attraction_quote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attraction_quote` (
  `atnq_id` int NOT NULL AUTO_INCREMENT,
  `atnq_attraction_id` int NOT NULL,
  `atnq_hash_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atnq_product_quote_id` int DEFAULT NULL,
  `atnq_json_response` json DEFAULT NULL,
  `atnq_booking_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atnq_attraction_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atnq_supplier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atnq_type_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atnq_availability_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atnq_availability_product_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atnq_availability_date` date DEFAULT NULL,
  `atnq_availability_is_valid` tinyint DEFAULT '0',
  `atnq_service_fee_percent` decimal(5,2) DEFAULT '0.00',
  `atnq_product_details_json` json DEFAULT NULL,
  PRIMARY KEY (`atnq_id`),
  UNIQUE KEY `atnq_hash_key` (`atnq_hash_key`),
  KEY `FK-attraction_quote-atnq_attraction_id` (`atnq_attraction_id`),
  KEY `FK-attraction_quote-atnq_product_quote_id` (`atnq_product_quote_id`),
  CONSTRAINT `FK-attraction_quote-atnq_attraction_id` FOREIGN KEY (`atnq_attraction_id`) REFERENCES `attraction` (`atn_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-attraction_quote-atnq_product_quote_id` FOREIGN KEY (`atnq_product_quote_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attraction_quote_options`
--

DROP TABLE IF EXISTS `attraction_quote_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attraction_quote_options` (
  `atqo_id` int NOT NULL AUTO_INCREMENT,
  `atqo_attraction_quote_id` int NOT NULL,
  `atqo_answered_value` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atqo_label` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atqo_is_answered` tinyint DEFAULT '0',
  `atqo_answer_formatted_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`atqo_id`),
  KEY `FK-attraction_quote_options_atqo_attraction_quote_id` (`atqo_attraction_quote_id`),
  CONSTRAINT `FK-attraction_quote_options_atqo_attraction_quote_id` FOREIGN KEY (`atqo_attraction_quote_id`) REFERENCES `attraction_quote` (`atnq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attraction_quote_pricing_category`
--

DROP TABLE IF EXISTS `attraction_quote_pricing_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attraction_quote_pricing_category` (
  `atqpc_id` int NOT NULL AUTO_INCREMENT,
  `atqpc_attraction_quote_id` int NOT NULL,
  `atqpc_category_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atqpc_label` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atqpc_min_age` int DEFAULT NULL,
  `atqpc_max_age` int DEFAULT NULL,
  `atqpc_min_participants` int DEFAULT NULL,
  `atqpc_max_participants` int DEFAULT NULL,
  `atqpc_quantity` int DEFAULT NULL,
  `atqpc_price` decimal(10,2) DEFAULT '0.00',
  `atqpc_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `atqpc_system_mark_up` decimal(10,2) DEFAULT '0.00',
  `atqpc_agent_mark_up` decimal(10,2) DEFAULT '0.00',
  PRIMARY KEY (`atqpc_id`),
  KEY `FK-attraction_quote_pricing_category_atqpc_attraction_quote_id` (`atqpc_attraction_quote_id`),
  CONSTRAINT `FK-attraction_quote_pricing_category_atqpc_attraction_quote_id` FOREIGN KEY (`atqpc_attraction_quote_id`) REFERENCES `attraction_quote` (`atnq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_assignment`
--

DROP TABLE IF EXISTS `auth_assignment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` int DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  KEY `auth_assignment_user_id_idx` (`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_import_export`
--

DROP TABLE IF EXISTS `auth_import_export`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_import_export` (
  `aie_id` int NOT NULL AUTO_INCREMENT,
  `aie_type` tinyint(1) DEFAULT NULL,
  `aie_cnt_roles` smallint DEFAULT NULL,
  `aie_cnt_permissions` smallint DEFAULT NULL,
  `aie_cnt_rules` smallint DEFAULT NULL,
  `aie_cnt_child` smallint DEFAULT NULL,
  `aie_file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aie_file_size` int DEFAULT NULL,
  `aie_created_dt` datetime DEFAULT NULL,
  `aie_user_id` int DEFAULT NULL,
  `aie_data` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`aie_id`),
  KEY `FK-auth_import_export-aie_user_id` (`aie_user_id`),
  CONSTRAINT `FK-auth_import_export-aie_user_id` FOREIGN KEY (`aie_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_item`
--

DROP TABLE IF EXISTS `auth_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_item` (
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` smallint NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rule_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_item_child`
--

DROP TABLE IF EXISTS `auth_item_child`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `child` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `auth_rule`
--

DROP TABLE IF EXISTS `auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_rule` (
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int DEFAULT NULL,
  `updated_at` int DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `billing_info`
--

DROP TABLE IF EXISTS `billing_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `billing_info` (
  `bi_id` int NOT NULL AUTO_INCREMENT,
  `bi_first_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bi_last_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bi_middle_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bi_company_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bi_address_line1` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bi_address_line2` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bi_city` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bi_state` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bi_country` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bi_zip` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bi_contact_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bi_contact_email` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bi_contact_name` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bi_payment_method_id` int DEFAULT NULL,
  `bi_cc_id` int DEFAULT NULL,
  `bi_order_id` int DEFAULT NULL,
  `bi_status_id` tinyint(1) DEFAULT NULL,
  `bi_created_user_id` int DEFAULT NULL,
  `bi_updated_user_id` int DEFAULT NULL,
  `bi_created_dt` datetime DEFAULT NULL,
  `bi_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`bi_id`),
  KEY `FK-billing_info-bi_cc_id` (`bi_cc_id`),
  KEY `FK-billing_info-bi_order_id` (`bi_order_id`),
  KEY `FK-billing_info-bi_created_user_id` (`bi_created_user_id`),
  KEY `FK-billing_info-bi_updated_user_id` (`bi_updated_user_id`),
  KEY `FK-billing_info-bi_payment_method_id` (`bi_payment_method_id`),
  CONSTRAINT `FK-billing_info-bi_cc_id` FOREIGN KEY (`bi_cc_id`) REFERENCES `credit_card` (`cc_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-billing_info-bi_created_user_id` FOREIGN KEY (`bi_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-billing_info-bi_order_id` FOREIGN KEY (`bi_order_id`) REFERENCES `order` (`or_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-billing_info-bi_payment_method_id` FOREIGN KEY (`bi_payment_method_id`) REFERENCES `payment_method` (`pm_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-billing_info-bi_updated_user_id` FOREIGN KEY (`bi_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call`
--

DROP TABLE IF EXISTS `call`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call` (
  `c_id` int NOT NULL AUTO_INCREMENT,
  `c_call_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `c_call_type_id` smallint DEFAULT NULL,
  `c_from` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_to` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_call_status` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_forwarded_from` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_caller_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_parent_call_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_call_duration` int DEFAULT NULL,
  `c_recording_url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_recording_duration` int DEFAULT NULL,
  `c_lead_id` int DEFAULT NULL,
  `c_created_user_id` int DEFAULT NULL,
  `c_created_dt` datetime DEFAULT NULL,
  `c_com_call_id` int DEFAULT NULL,
  `c_updated_dt` timestamp NULL DEFAULT NULL,
  `c_project_id` int DEFAULT NULL,
  `c_error_message` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_is_new` tinyint(1) DEFAULT '0',
  `c_price` decimal(10,5) DEFAULT NULL,
  `c_source_type_id` smallint DEFAULT NULL,
  `c_dep_id` int DEFAULT NULL,
  `c_case_id` int DEFAULT NULL,
  `c_client_id` int DEFAULT NULL,
  `c_status_id` smallint DEFAULT NULL,
  `c_parent_id` int DEFAULT NULL,
  `c_offset_gmt` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_from_country` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_from_state` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_from_city` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_recording_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_sequence_number` smallint DEFAULT NULL,
  `c_is_transfer` tinyint(1) DEFAULT '0',
  `c_queue_start_dt` datetime DEFAULT NULL,
  `c_group_id` int DEFAULT NULL,
  `c_language_id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_is_conference` tinyint(1) DEFAULT '0',
  `c_conference_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_conference_id` int DEFAULT NULL,
  `c_data_json` json DEFAULT NULL,
  `c_recording_disabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`c_id`),
  UNIQUE KEY `IND-call_c_call_sid` (`c_call_sid`),
  KEY `IND-call_c_lead_id` (`c_lead_id`),
  KEY `FK-call_c_project_id` (`c_project_id`),
  KEY `IND-call_c_call_status` (`c_call_status`),
  KEY `IND-call_c_created_user_id` (`c_created_user_id`),
  KEY `IND-call_c_source_type_id` (`c_source_type_id`),
  KEY `IND-call_c_parent_call_sid` (`c_parent_call_sid`),
  KEY `FK-call_c_dep_id` (`c_dep_id`),
  KEY `IND-call_c_case_id` (`c_case_id`),
  KEY `FK-call_c_client_id` (`c_client_id`),
  KEY `FK-call_c_parent_id` (`c_parent_id`),
  KEY `IND-call_c_status_id` (`c_status_id`),
  KEY `IND-call-c_from` (`c_from`),
  KEY `IND-call_c_recording_sid` (`c_recording_sid`),
  KEY `IND-call_c_status_id-c_created_dt` (`c_status_id`,`c_created_dt`),
  KEY `FK-call-c_language_id` (`c_language_id`),
  KEY `IND-call-c_conference_id` (`c_conference_id`),
  CONSTRAINT `FK-call-c_language_id` FOREIGN KEY (`c_language_id`) REFERENCES `language` (`language_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-call_c_case_id` FOREIGN KEY (`c_case_id`) REFERENCES `cases` (`cs_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-call_c_client_id` FOREIGN KEY (`c_client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-call_c_created_user_id` FOREIGN KEY (`c_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-call_c_dep_id` FOREIGN KEY (`c_dep_id`) REFERENCES `department` (`dep_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-call_c_lead_id` FOREIGN KEY (`c_lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-call_c_parent_id` FOREIGN KEY (`c_parent_id`) REFERENCES `call` (`c_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-call_c_project_id` FOREIGN KEY (`c_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3368210 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_command`
--

DROP TABLE IF EXISTS `call_command`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_command` (
  `ccom_id` int NOT NULL AUTO_INCREMENT,
  `ccom_parent_id` int DEFAULT NULL,
  `ccom_project_id` int DEFAULT NULL,
  `ccom_lang_id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ccom_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ccom_type_id` smallint NOT NULL,
  `ccom_params_json` json DEFAULT NULL,
  `ccom_sort_order` smallint DEFAULT '5',
  `ccom_user_id` int DEFAULT NULL,
  `ccom_created_user_id` int DEFAULT NULL,
  `ccom_updated_user_id` int DEFAULT NULL,
  `ccom_created_dt` datetime DEFAULT NULL,
  `ccom_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ccom_id`),
  KEY `FK-call_command-ccom_project_id` (`ccom_project_id`),
  KEY `FK-call_command-ccom_user_id` (`ccom_user_id`),
  KEY `FK-call_command-ccom_created_user_id` (`ccom_created_user_id`),
  KEY `FK-call_command-ccom_updated_user_id` (`ccom_updated_user_id`),
  KEY `FK-call_command-ccom_parent_id` (`ccom_parent_id`),
  CONSTRAINT `FK-call_command-ccom_created_user_id` FOREIGN KEY (`ccom_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-call_command-ccom_parent_id` FOREIGN KEY (`ccom_parent_id`) REFERENCES `call_command` (`ccom_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-call_command-ccom_project_id` FOREIGN KEY (`ccom_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-call_command-ccom_updated_user_id` FOREIGN KEY (`ccom_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-call_command-ccom_user_id` FOREIGN KEY (`ccom_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_gather_switch`
--

DROP TABLE IF EXISTS `call_gather_switch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_gather_switch` (
  `cgs_ccom_id` int NOT NULL,
  `cgs_step` int NOT NULL,
  `cgs_case` int NOT NULL,
  `cgs_exec_ccom_id` int NOT NULL,
  PRIMARY KEY (`cgs_ccom_id`,`cgs_step`,`cgs_case`),
  KEY `FK-call_gather_switch-cgs_exec_ccom_id` (`cgs_exec_ccom_id`),
  CONSTRAINT `FK-call_gather_switch-cgs_ccom_id` FOREIGN KEY (`cgs_ccom_id`) REFERENCES `call_command` (`ccom_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-call_gather_switch-cgs_exec_ccom_id` FOREIGN KEY (`cgs_exec_ccom_id`) REFERENCES `call_command` (`ccom_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_log`
--

DROP TABLE IF EXISTS `call_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_log` (
  `cl_id` int NOT NULL,
  `cl_group_id` int DEFAULT NULL,
  `cl_call_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_type_id` tinyint DEFAULT NULL,
  `cl_category_id` tinyint DEFAULT NULL,
  `cl_is_transfer` tinyint(1) DEFAULT NULL,
  `cl_duration` smallint DEFAULT NULL,
  `cl_phone_from` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_phone_to` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_phone_list_id` int DEFAULT NULL,
  `cl_user_id` int DEFAULT NULL,
  `cl_department_id` int DEFAULT NULL,
  `cl_project_id` int DEFAULT NULL,
  `cl_call_created_dt` datetime DEFAULT NULL,
  `cl_call_finished_dt` datetime DEFAULT NULL,
  `cl_status_id` tinyint DEFAULT NULL,
  `cl_client_id` int DEFAULT NULL,
  `cl_price` decimal(9,5) DEFAULT NULL,
  `cl_year` smallint NOT NULL,
  `cl_month` tinyint NOT NULL,
  `cl_conference_id` int DEFAULT NULL,
  PRIMARY KEY (`cl_id`,`cl_year`,`cl_month`),
  KEY `IND-call_log-cl_call_sid` (`cl_call_sid`),
  KEY `IND-call_log-cl_type_id` (`cl_type_id`),
  KEY `IND-call_log-cl_category_id` (`cl_category_id`),
  KEY `IND-call_log-cl_phone_from` (`cl_phone_from`),
  KEY `IND-call_log-cl_phone_to` (`cl_phone_to`),
  KEY `IND-call_log-cl_phone_list_id` (`cl_phone_list_id`),
  KEY `IND-call_log-cl_user_id` (`cl_user_id`),
  KEY `IND-call_log-cl_department_id` (`cl_department_id`),
  KEY `IND-call_log-cl_project_id` (`cl_project_id`),
  KEY `IND-call_log-cl_call_created_dt` (`cl_call_created_dt`),
  KEY `IND-call_log-cl_status_id` (`cl_status_id`),
  KEY `IND-call_log-cl_year` (`cl_year`),
  KEY `IND-call_log-cl_month` (`cl_month`),
  KEY `IND-call_log-cl_group_id` (`cl_group_id`),
  KEY `IND-call_log-cl_client_id` (`cl_client_id`),
  KEY `IND-call_log-cl_conference_id` (`cl_conference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
/*!50100 PARTITION BY RANGE (`cl_year`)
SUBPARTITION BY LINEAR HASH (`cl_month`)
SUBPARTITIONS 12
(PARTITION y19 VALUES LESS THAN (2019) ENGINE = InnoDB,
 PARTITION y20 VALUES LESS THAN (2020) ENGINE = InnoDB,
 PARTITION y21 VALUES LESS THAN (2021) ENGINE = InnoDB,
 PARTITION y22 VALUES LESS THAN (2022) ENGINE = InnoDB,
 PARTITION y23 VALUES LESS THAN (2023) ENGINE = InnoDB,
 PARTITION y VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_log_case`
--

DROP TABLE IF EXISTS `call_log_case`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_log_case` (
  `clc_cl_id` int NOT NULL,
  `clc_case_id` int NOT NULL,
  `clc_case_status_log_id` int DEFAULT NULL,
  PRIMARY KEY (`clc_cl_id`,`clc_case_id`),
  KEY `FK-call_log_case-clc_case_id` (`clc_case_id`),
  KEY `FK-call_log_case-clc_case_status_log_id` (`clc_case_status_log_id`),
  CONSTRAINT `FK-call_log_case-clc_case_id` FOREIGN KEY (`clc_case_id`) REFERENCES `cases` (`cs_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-call_log_case-clc_case_status_log_id` FOREIGN KEY (`clc_case_status_log_id`) REFERENCES `case_status_log` (`csl_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_log_lead`
--

DROP TABLE IF EXISTS `call_log_lead`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_log_lead` (
  `cll_cl_id` int NOT NULL,
  `cll_lead_id` int NOT NULL,
  `cll_lead_flow_id` int DEFAULT NULL,
  PRIMARY KEY (`cll_cl_id`,`cll_lead_id`),
  KEY `FK-call_log_lead-cll_lead_id` (`cll_lead_id`),
  KEY `FK-call_log_lead-cll_lead_flow_id` (`cll_lead_flow_id`),
  CONSTRAINT `FK-call_log_lead-cll_lead_flow_id` FOREIGN KEY (`cll_lead_flow_id`) REFERENCES `lead_flow` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-call_log_lead-cll_lead_id` FOREIGN KEY (`cll_lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_log_queue`
--

DROP TABLE IF EXISTS `call_log_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_log_queue` (
  `clq_cl_id` int NOT NULL,
  `clq_queue_time` smallint DEFAULT NULL,
  `clq_access_count` tinyint DEFAULT NULL,
  `clq_is_transfer` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`clq_cl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_log_record`
--

DROP TABLE IF EXISTS `call_log_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_log_record` (
  `clr_cl_id` int NOT NULL,
  `clr_record_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `clr_duration` smallint DEFAULT NULL,
  PRIMARY KEY (`clr_cl_id`),
  KEY `IND-call_log_record-clr_record_sid` (`clr_record_sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_log_user_access`
--

DROP TABLE IF EXISTS `call_log_user_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_log_user_access` (
  `clua_id` int NOT NULL AUTO_INCREMENT,
  `clua_cl_id` int NOT NULL,
  `clua_user_id` int DEFAULT NULL,
  `clua_access_status_id` int DEFAULT NULL,
  `clua_access_start_dt` datetime DEFAULT NULL,
  `clua_access_finish_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`clua_id`),
  KEY `IND-call_log_user_access-clua_cl_id` (`clua_cl_id`),
  KEY `FK-call_log_user_access-clua_user_id` (`clua_user_id`),
  CONSTRAINT `FK-call_log_user_access-clua_user_id` FOREIGN KEY (`clua_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_note`
--

DROP TABLE IF EXISTS `call_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_note` (
  `cn_id` int NOT NULL AUTO_INCREMENT,
  `cn_call_id` int DEFAULT NULL,
  `cn_note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cn_created_dt` datetime DEFAULT NULL,
  `cn_updated_dt` datetime DEFAULT NULL,
  `cn_created_user_id` int DEFAULT NULL,
  `cn_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`cn_id`),
  KEY `FK-call_note-cn_call_id` (`cn_call_id`),
  KEY `FK-call_note-cn_created_user_id` (`cn_created_user_id`),
  KEY `FK-call_note-cn_updated_user_id` (`cn_updated_user_id`),
  CONSTRAINT `FK-call_note-cn_call_id` FOREIGN KEY (`cn_call_id`) REFERENCES `call` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-call_note-cn_created_user_id` FOREIGN KEY (`cn_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-call_note-cn_updated_user_id` FOREIGN KEY (`cn_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_recording_log`
--

DROP TABLE IF EXISTS `call_recording_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_recording_log` (
  `crl_id` int NOT NULL AUTO_INCREMENT,
  `crl_call_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crl_user_id` int NOT NULL,
  `crl_created_dt` datetime DEFAULT NULL,
  `crl_year` smallint NOT NULL,
  `crl_month` tinyint NOT NULL,
  PRIMARY KEY (`crl_id`,`crl_year`,`crl_month`),
  KEY `IND-call_recording_log-crl_call_sid` (`crl_call_sid`),
  KEY `IND-call_recording_log-crl_year` (`crl_year`),
  KEY `IND-call_recording_log-crl_month` (`crl_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
/*!50100 PARTITION BY RANGE (`crl_year`)
SUBPARTITION BY LINEAR HASH (`crl_month`)
SUBPARTITIONS 12
(PARTITION y21 VALUES LESS THAN (2021) ENGINE = InnoDB,
 PARTITION y22 VALUES LESS THAN (2022) ENGINE = InnoDB,
 PARTITION y23 VALUES LESS THAN (2023) ENGINE = InnoDB,
 PARTITION y24 VALUES LESS THAN (2024) ENGINE = InnoDB,
 PARTITION y VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_session`
--

DROP TABLE IF EXISTS `call_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_session` (
  `cs_id` int NOT NULL AUTO_INCREMENT,
  `cs_call_id` int NOT NULL,
  `cs_cid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cs_step` smallint NOT NULL DEFAULT '1',
  `cs_project_id` int NOT NULL,
  `cs_lang_id` smallint NOT NULL DEFAULT '1',
  `cs_data_params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cs_create_dt` datetime DEFAULT NULL,
  `cs_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cs_id`),
  KEY `PK-call_session_cs_call_id` (`cs_call_id`),
  KEY `PK-call_session_cs_cid` (`cs_cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_terminate_log`
--

DROP TABLE IF EXISTS `call_terminate_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_terminate_log` (
  `ctl_id` int NOT NULL AUTO_INCREMENT,
  `ctl_call_phone_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ctl_call_status_id` int NOT NULL,
  `ctl_project_id` int DEFAULT NULL,
  `ctl_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ctl_id`),
  KEY `IND-call_terminate_log-phone` (`ctl_call_phone_number`),
  KEY `IND-call_terminate_log-status` (`ctl_call_status_id`),
  KEY `FK-call_terminate_log-project` (`ctl_project_id`),
  CONSTRAINT `FK-call_terminate_log-project` FOREIGN KEY (`ctl_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_user_access`
--

DROP TABLE IF EXISTS `call_user_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_user_access` (
  `cua_call_id` int NOT NULL,
  `cua_user_id` int NOT NULL,
  `cua_status_id` smallint DEFAULT NULL,
  `cua_created_dt` datetime DEFAULT NULL,
  `cua_updated_dt` datetime DEFAULT NULL,
  `cua_priority` smallint DEFAULT '0',
  PRIMARY KEY (`cua_call_id`,`cua_user_id`),
  KEY `FK-call_user_access_cua_user_id` (`cua_user_id`),
  KEY `IND-call_user_access_cua_status_id` (`cua_status_id`),
  KEY `IND-call_user_access-priority` (`cua_priority`),
  CONSTRAINT `FK-call_user_access_cua_call_id` FOREIGN KEY (`cua_call_id`) REFERENCES `call` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-call_user_access_cua_user_id` FOREIGN KEY (`cua_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `call_user_group`
--

DROP TABLE IF EXISTS `call_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `call_user_group` (
  `cug_c_id` int NOT NULL,
  `cug_ug_id` int NOT NULL,
  `cug_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cug_c_id`,`cug_ug_id`),
  KEY `FK-call_user_group_cug_ug_id` (`cug_ug_id`),
  CONSTRAINT `FK-call_user_group_cug_c_id` FOREIGN KEY (`cug_c_id`) REFERENCES `call` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-call_user_group_cug_ug_id` FOREIGN KEY (`cug_ug_id`) REFERENCES `user_group` (`ug_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `case_category`
--

DROP TABLE IF EXISTS `case_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `case_category` (
  `cc_id` int NOT NULL AUTO_INCREMENT,
  `cc_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cc_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cc_dep_id` int NOT NULL,
  `cc_system` tinyint(1) DEFAULT '0',
  `cc_created_dt` datetime DEFAULT NULL,
  `cc_updated_dt` datetime DEFAULT NULL,
  `cc_updated_user_id` int DEFAULT NULL,
  `cc_enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`cc_id`),
  UNIQUE KEY `cc_key` (`cc_key`),
  KEY `FK-case_category_cc_user_id` (`cc_updated_user_id`),
  KEY `FK-case_category_cc_dep_id` (`cc_dep_id`),
  KEY `IND-case_category-cc_enabled` (`cc_enabled`),
  CONSTRAINT `FK-case_category_cc_dep_id` FOREIGN KEY (`cc_dep_id`) REFERENCES `department` (`dep_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-case_category_cc_user_id` FOREIGN KEY (`cc_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `case_event_log`
--

DROP TABLE IF EXISTS `case_event_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `case_event_log` (
  `cel_id` int NOT NULL AUTO_INCREMENT,
  `cel_case_id` int DEFAULT NULL,
  `cel_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cel_data_json` json DEFAULT NULL,
  `cel_created_dt` datetime DEFAULT NULL,
  `cel_type_id` tinyint DEFAULT NULL,
  PRIMARY KEY (`cel_id`),
  KEY `FK-case_event_log-cel_case_id` (`cel_case_id`),
  CONSTRAINT `FK-case_event_log-cel_case_id` FOREIGN KEY (`cel_case_id`) REFERENCES `cases` (`cs_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `case_note`
--

DROP TABLE IF EXISTS `case_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `case_note` (
  `cn_id` int NOT NULL AUTO_INCREMENT,
  `cn_cs_id` int NOT NULL,
  `cn_user_id` int DEFAULT NULL,
  `cn_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cn_created_dt` datetime DEFAULT NULL,
  `cn_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cn_id`),
  KEY `FK-case_note_cn_cs_id` (`cn_cs_id`),
  KEY `FK-case_note_cn_user_id` (`cn_user_id`),
  CONSTRAINT `FK-case_note_cn_cs_id` FOREIGN KEY (`cn_cs_id`) REFERENCES `cases` (`cs_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-case_note_cn_user_id` FOREIGN KEY (`cn_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17673 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `case_order`
--

DROP TABLE IF EXISTS `case_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `case_order` (
  `co_order_id` int NOT NULL,
  `co_case_id` int NOT NULL,
  `co_create_dt` datetime DEFAULT NULL,
  `co_created_user_id` int DEFAULT NULL,
  PRIMARY KEY (`co_order_id`,`co_case_id`),
  KEY `PK-case_order-co_case_id` (`co_case_id`),
  KEY `PK-case_order-co_created_user_id` (`co_created_user_id`),
  CONSTRAINT `PK-case_order-co_case_id` FOREIGN KEY (`co_case_id`) REFERENCES `cases` (`cs_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `PK-case_order-co_created_user_id` FOREIGN KEY (`co_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `PK-case_order-co_order_id` FOREIGN KEY (`co_order_id`) REFERENCES `order` (`or_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `case_sale`
--

DROP TABLE IF EXISTS `case_sale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `case_sale` (
  `css_cs_id` int NOT NULL,
  `css_sale_id` int NOT NULL,
  `css_sale_book_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `css_sale_pnr` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `css_sale_pax` smallint DEFAULT NULL,
  `css_sale_created_dt` datetime DEFAULT NULL,
  `css_sale_data` json NOT NULL,
  `css_created_user_id` int DEFAULT NULL,
  `css_updated_user_id` int DEFAULT NULL,
  `css_created_dt` datetime DEFAULT NULL,
  `css_updated_dt` datetime DEFAULT NULL,
  `css_need_sync_bo` tinyint(1) DEFAULT '0',
  `css_sale_data_updated` json DEFAULT NULL,
  `css_charged` decimal(8,2) DEFAULT NULL,
  `css_profit` decimal(8,2) DEFAULT NULL,
  `css_out_departure_airport` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `css_out_arrival_airport` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `css_out_date` datetime DEFAULT NULL,
  `css_in_departure_airport` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `css_in_arrival_airport` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `css_in_date` datetime DEFAULT NULL,
  `css_charge_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `css_fare_rules` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `css_penalty_type` tinyint DEFAULT NULL,
  `css_departure_dt` datetime DEFAULT NULL,
  `css_send_email_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`css_cs_id`,`css_sale_id`),
  KEY `FK-case_sale_css_created_user_id` (`css_created_user_id`),
  KEY `FK-case_sale_css_updated_user_id` (`css_updated_user_id`),
  KEY `IND-case_sale_css_charged` (`css_charged`),
  KEY `IND-case_sale_css_profit` (`css_profit`),
  KEY `IND-case_sale-css_in_date` (`css_in_date`),
  KEY `IND-case_sale-css_out_date` (`css_out_date`),
  KEY `INDEX-st_departure_dt` (`css_departure_dt`),
  KEY `idx-case_sale-css_charge_type` (`css_charge_type`),
  KEY `IND-case_sale-css_sale_book_id` (`css_sale_book_id`),
  KEY `IND-case_sale-css_sale_pnr` (`css_sale_pnr`),
  KEY `IND-case_sale-css_departure_dt` (`css_departure_dt`),
  CONSTRAINT `FK-case_sale_css_cs_id` FOREIGN KEY (`css_cs_id`) REFERENCES `cases` (`cs_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `case_status_log`
--

DROP TABLE IF EXISTS `case_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `case_status_log` (
  `csl_id` int NOT NULL AUTO_INCREMENT,
  `csl_case_id` int NOT NULL,
  `csl_from_status` int DEFAULT NULL,
  `csl_to_status` int NOT NULL,
  `csl_start_dt` datetime NOT NULL,
  `csl_end_dt` datetime DEFAULT NULL,
  `csl_time_duration` int DEFAULT NULL,
  `csl_created_user_id` int DEFAULT NULL,
  `csl_owner_id` int DEFAULT NULL,
  `csl_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`csl_id`),
  KEY `FK-case_status_log_csl_case_id` (`csl_case_id`),
  KEY `FK-case_status_log_csl_owner_id` (`csl_owner_id`),
  CONSTRAINT `FK-case_status_log_csl_case_id` FOREIGN KEY (`csl_case_id`) REFERENCES `cases` (`cs_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-case_status_log_csl_owner_id` FOREIGN KEY (`csl_owner_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=397247 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cases`
--

DROP TABLE IF EXISTS `cases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cases` (
  `cs_id` int NOT NULL AUTO_INCREMENT,
  `cs_gid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cs_subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cs_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cs_category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cs_category_id` int DEFAULT NULL,
  `cs_status` int NOT NULL,
  `cs_user_id` int DEFAULT NULL,
  `cs_lead_id` int DEFAULT NULL,
  `cs_call_id` int DEFAULT NULL,
  `cs_dep_id` int DEFAULT NULL,
  `cs_project_id` int DEFAULT NULL,
  `cs_client_id` int DEFAULT NULL,
  `cs_created_dt` datetime DEFAULT NULL,
  `cs_updated_dt` datetime DEFAULT NULL,
  `cs_last_action_dt` datetime DEFAULT NULL,
  `cs_source_type_id` tinyint DEFAULT NULL,
  `cs_deadline_dt` datetime DEFAULT NULL,
  `cs_need_action` tinyint(1) DEFAULT NULL,
  `cs_order_uid` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cs_is_automate` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`cs_id`),
  UNIQUE KEY `cs_gid` (`cs_gid`),
  UNIQUE KEY `IND-cases_cs_gid` (`cs_gid`),
  KEY `FK-cases_cs_user_id` (`cs_user_id`),
  KEY `FK-cases_cs_lead_id` (`cs_lead_id`),
  KEY `FK-cases_cs_call_id` (`cs_call_id`),
  KEY `FK-cases_cs_dep_id` (`cs_dep_id`),
  KEY `FK-cases_cs_client_id` (`cs_client_id`),
  KEY `IND-cases-cs_source_type_id` (`cs_source_type_id`),
  KEY `FK-cases_cs_category_id` (`cs_category_id`),
  KEY `IND-cases-css_cs_deadline_dt` (`cs_deadline_dt`),
  KEY `IND-cases-cs_category_id` (`cs_category_id`),
  KEY `IND-cases-cs_status` (`cs_status`),
  KEY `IND-cases-cs_user_id` (`cs_user_id`),
  KEY `IND-cases-cs_dep_id` (`cs_dep_id`),
  KEY `IND-cases-cs_project_id` (`cs_project_id`),
  KEY `IND-cases-cs_client_id` (`cs_client_id`),
  KEY `IND-cases-cs_last_action_dt` (`cs_last_action_dt`),
  KEY `IND-cases-cs_order_uid` (`cs_order_uid`),
  KEY `IND-cases-cs_created_dt` (`cs_created_dt`),
  KEY `IND-cases-cs_updated_dt` (`cs_updated_dt`),
  KEY `IND-cases-cs_need_action` (`cs_need_action`),
  KEY `IND-cases-cs_is_automate` (`cs_is_automate`),
  CONSTRAINT `FK-cases_cs_call_id` FOREIGN KEY (`cs_call_id`) REFERENCES `call` (`c_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-cases_cs_category_id` FOREIGN KEY (`cs_category_id`) REFERENCES `case_category` (`cc_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-cases_cs_client_id` FOREIGN KEY (`cs_client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-cases_cs_dep_id` FOREIGN KEY (`cs_dep_id`) REFERENCES `department` (`dep_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-cases_cs_lead_id` FOREIGN KEY (`cs_lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-cases_cs_user_id` FOREIGN KEY (`cs_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=135912 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_account`
--

DROP TABLE IF EXISTS `client_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_account` (
  `ca_id` int NOT NULL AUTO_INCREMENT,
  `ca_project_id` int DEFAULT NULL,
  `ca_uuid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ca_hid` int NOT NULL,
  `ca_username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ca_first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ca_middle_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ca_last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ca_nationality_country_code` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ca_dob` date DEFAULT NULL,
  `ca_gender` tinyint DEFAULT NULL,
  `ca_phone` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ca_subscription` tinyint(1) DEFAULT '0',
  `ca_language_id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ca_currency_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ca_timezone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ca_created_ip` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ca_enabled` tinyint(1) DEFAULT '1',
  `ca_origin_created_dt` datetime DEFAULT NULL,
  `ca_origin_updated_dt` datetime DEFAULT NULL,
  `ca_created_dt` datetime DEFAULT NULL,
  `ca_updated_dt` datetime DEFAULT NULL,
  `ca_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ca_id`),
  UNIQUE KEY `IDX-client_account-uuid` (`ca_uuid`),
  KEY `FK-client_account-ca_project_id` (`ca_project_id`),
  KEY `FK-client_account-ca_language_id` (`ca_language_id`),
  KEY `FK-client_account-ca_currency_code` (`ca_currency_code`),
  KEY `IND-client_account-ca_username` (`ca_username`),
  CONSTRAINT `FK-client_account-ca_currency_code` FOREIGN KEY (`ca_currency_code`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-client_account-ca_language_id` FOREIGN KEY (`ca_language_id`) REFERENCES `language` (`language_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-client_account-ca_project_id` FOREIGN KEY (`ca_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_account_social`
--

DROP TABLE IF EXISTS `client_account_social`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_account_social` (
  `cas_ca_id` int NOT NULL,
  `cas_type_id` int NOT NULL,
  `cas_identity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cas_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cas_ca_id`,`cas_type_id`),
  CONSTRAINT `FK-client_account_social-cas_ca_id` FOREIGN KEY (`cas_ca_id`) REFERENCES `client_account` (`ca_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat`
--

DROP TABLE IF EXISTS `client_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat` (
  `cch_id` int NOT NULL AUTO_INCREMENT,
  `cch_rid` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cch_ccr_id` bigint DEFAULT NULL,
  `cch_title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cch_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cch_project_id` int DEFAULT NULL,
  `cch_dep_id` int DEFAULT NULL,
  `cch_channel_id` int DEFAULT NULL,
  `cch_client_id` int DEFAULT NULL,
  `cch_owner_user_id` int DEFAULT NULL,
  `cch_note` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cch_status_id` tinyint DEFAULT NULL,
  `cch_ip` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cch_ua` int DEFAULT NULL,
  `cch_language_id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cch_created_dt` datetime DEFAULT NULL,
  `cch_updated_dt` datetime DEFAULT NULL,
  `cch_created_user_id` int DEFAULT NULL,
  `cch_updated_user_id` int DEFAULT NULL,
  `cch_client_online` tinyint(1) DEFAULT NULL,
  `cch_source_type_id` tinyint(1) DEFAULT NULL,
  `cch_missed` tinyint(1) DEFAULT NULL,
  `cch_parent_id` int DEFAULT NULL,
  PRIMARY KEY (`cch_id`),
  KEY `IND-cch_rid` (`cch_rid`),
  KEY `FK-cch_ccr_id` (`cch_ccr_id`),
  KEY `FK-cch_project_id` (`cch_project_id`),
  KEY `FK-cch_dep_id` (`cch_dep_id`),
  KEY `FK-cch_client_id` (`cch_client_id`),
  KEY `FK-cch_owner_user_id` (`cch_owner_user_id`),
  KEY `FK-cch_created_user_id` (`cch_created_user_id`),
  KEY `FK-cch_updated_user_id` (`cch_updated_user_id`),
  KEY `FK-cch_channel_id` (`cch_channel_id`),
  KEY `FK-cch_language_id` (`cch_language_id`),
  CONSTRAINT `FK-cch_channel_id` FOREIGN KEY (`cch_channel_id`) REFERENCES `client_chat_channel` (`ccc_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-cch_client_id` FOREIGN KEY (`cch_client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-cch_created_user_id` FOREIGN KEY (`cch_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-cch_dep_id` FOREIGN KEY (`cch_dep_id`) REFERENCES `department` (`dep_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-cch_language_id` FOREIGN KEY (`cch_language_id`) REFERENCES `language` (`language_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-cch_owner_user_id` FOREIGN KEY (`cch_owner_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-cch_project_id` FOREIGN KEY (`cch_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-cch_updated_user_id` FOREIGN KEY (`cch_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_action_reason`
--

DROP TABLE IF EXISTS `client_chat_action_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_action_reason` (
  `ccar_id` int NOT NULL AUTO_INCREMENT,
  `ccar_action_id` int NOT NULL,
  `ccar_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ccar_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ccar_enabled` tinyint(1) DEFAULT NULL,
  `ccar_comment_required` tinyint(1) DEFAULT NULL,
  `ccar_created_user_id` int DEFAULT NULL,
  `ccar_updated_user_id` int DEFAULT NULL,
  `ccar_created_dt` datetime DEFAULT NULL,
  `ccar_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ccar_id`),
  KEY `FK-client_chat_action_reason-ccar_created_user_id` (`ccar_created_user_id`),
  KEY `FK-client_chat_action_reason-ccar_updated_user_id` (`ccar_updated_user_id`),
  CONSTRAINT `FK-client_chat_action_reason-ccar_created_user_id` FOREIGN KEY (`ccar_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_action_reason-ccar_updated_user_id` FOREIGN KEY (`ccar_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_case`
--

DROP TABLE IF EXISTS `client_chat_case`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_case` (
  `cccs_chat_id` int NOT NULL,
  `cccs_case_id` int NOT NULL,
  `cccs_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cccs_chat_id`,`cccs_case_id`),
  CONSTRAINT `FK-client_chat_case-cccs_chat_id` FOREIGN KEY (`cccs_chat_id`) REFERENCES `client_chat` (`cch_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_channel`
--

DROP TABLE IF EXISTS `client_chat_channel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_channel` (
  `ccc_id` int NOT NULL AUTO_INCREMENT,
  `ccc_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ccc_project_id` int DEFAULT NULL,
  `ccc_dep_id` int DEFAULT NULL,
  `ccc_ug_id` int DEFAULT NULL,
  `ccc_disabled` tinyint(1) DEFAULT NULL,
  `ccc_priority` tinyint unsigned DEFAULT NULL,
  `ccc_created_dt` datetime DEFAULT NULL,
  `ccc_updated_dt` datetime DEFAULT NULL,
  `ccc_created_user_id` int DEFAULT NULL,
  `ccc_updated_user_id` int DEFAULT NULL,
  `ccc_default` tinyint(1) DEFAULT '0',
  `ccc_frontend_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ccc_frontend_enabled` tinyint(1) DEFAULT '0',
  `ccc_settings` json DEFAULT NULL,
  `ccc_registered` tinyint(1) DEFAULT NULL,
  `ccc_default_device` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ccc_id`),
  UNIQUE KEY `ccc_name` (`ccc_name`),
  KEY `FK-ccc_project_id` (`ccc_project_id`),
  KEY `FK-ccc_dep_id` (`ccc_dep_id`),
  KEY `FK-ccc_ug_id` (`ccc_ug_id`),
  KEY `FK-ccc_created_user_id` (`ccc_created_user_id`),
  KEY `FK-ccc_updated_user_id` (`ccc_updated_user_id`),
  KEY `IND-client_chat_channel-ccc_frontend_enabled` (`ccc_frontend_enabled`),
  CONSTRAINT `FK-ccc_created_user_id` FOREIGN KEY (`ccc_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-ccc_dep_id` FOREIGN KEY (`ccc_dep_id`) REFERENCES `department` (`dep_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-ccc_project_id` FOREIGN KEY (`ccc_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-ccc_ug_id` FOREIGN KEY (`ccc_ug_id`) REFERENCES `user_group` (`ug_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-ccc_updated_user_id` FOREIGN KEY (`ccc_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_channel_transfer`
--

DROP TABLE IF EXISTS `client_chat_channel_transfer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_channel_transfer` (
  `cctr_from_ccc_id` int NOT NULL,
  `cctr_to_ccc_id` int NOT NULL,
  `cctr_created_user_id` int DEFAULT NULL,
  `cctr_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cctr_from_ccc_id`,`cctr_to_ccc_id`),
  KEY `FK-chat_channel_transfer-cctr_to_ccc_id` (`cctr_to_ccc_id`),
  KEY `FK-chat_channel_transfer-cctr_created_user_id` (`cctr_created_user_id`),
  CONSTRAINT `FK-chat_channel_transfer-cctr_created_user_id` FOREIGN KEY (`cctr_created_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-chat_channel_transfer-cctr_from_ccc_id` FOREIGN KEY (`cctr_from_ccc_id`) REFERENCES `client_chat_channel` (`ccc_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-chat_channel_transfer-cctr_to_ccc_id` FOREIGN KEY (`cctr_to_ccc_id`) REFERENCES `client_chat_channel` (`ccc_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_channel_translate`
--

DROP TABLE IF EXISTS `client_chat_channel_translate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_channel_translate` (
  `ct_channel_id` int NOT NULL,
  `ct_language_id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ct_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ct_created_user_id` int DEFAULT NULL,
  `ct_updated_user_id` int DEFAULT NULL,
  `ct_created_dt` datetime DEFAULT NULL,
  `ct_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ct_channel_id`,`ct_language_id`),
  KEY `FK-client_chat_channel_translate-ct_language_id` (`ct_language_id`),
  KEY `FK-client_chat_channel_translate-ct_created_user_id` (`ct_created_user_id`),
  KEY `FK-client_chat_channel_translate-ct_updated_user_id` (`ct_updated_user_id`),
  CONSTRAINT `FK-client_chat_channel_translate-ct_channel_id` FOREIGN KEY (`ct_channel_id`) REFERENCES `client_chat_channel` (`ccc_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_channel_translate-ct_created_user_id` FOREIGN KEY (`ct_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_channel_translate-ct_language_id` FOREIGN KEY (`ct_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_channel_translate-ct_updated_user_id` FOREIGN KEY (`ct_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_component_event`
--

DROP TABLE IF EXISTS `client_chat_component_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_component_event` (
  `ccce_id` int NOT NULL AUTO_INCREMENT,
  `ccce_chat_channel_id` int DEFAULT NULL,
  `ccce_component` tinyint unsigned NOT NULL,
  `ccce_event_type` tinyint(1) NOT NULL,
  `ccce_component_config` json DEFAULT NULL,
  `ccce_enabled` tinyint(1) DEFAULT NULL,
  `ccce_sort_order` tinyint unsigned DEFAULT NULL,
  `ccce_created_user_id` int DEFAULT NULL,
  `ccce_updated_user_id` int DEFAULT NULL,
  `ccce_created_dt` datetime DEFAULT NULL,
  `ccce_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ccce_id`),
  UNIQUE KEY `UQ-client_chat_component_event-cch_id-component-event_type` (`ccce_chat_channel_id`,`ccce_component`,`ccce_event_type`),
  KEY `FK-client_chat_component_event-ccce_created_user_id` (`ccce_created_user_id`),
  KEY `FK-client_chat_component_event-ccce_updated_user_id` (`ccce_updated_user_id`),
  CONSTRAINT `FK-client_chat_component_event-ccce_chat_channel_id` FOREIGN KEY (`ccce_chat_channel_id`) REFERENCES `client_chat_channel` (`ccc_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_component_event-ccce_created_user_id` FOREIGN KEY (`ccce_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_component_event-ccce_updated_user_id` FOREIGN KEY (`ccce_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_component_rule`
--

DROP TABLE IF EXISTS `client_chat_component_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_component_rule` (
  `cccr_component_event_id` int NOT NULL,
  `cccr_value` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cccr_runnable_component` tinyint unsigned NOT NULL,
  `cccr_component_config` json DEFAULT NULL,
  `cccr_sort_order` tinyint unsigned DEFAULT NULL,
  `cccr_enabled` tinyint(1) DEFAULT NULL,
  `cccr_created_user_id` int DEFAULT NULL,
  `cccr_updated_user_id` int DEFAULT NULL,
  `cccr_created_dt` datetime DEFAULT NULL,
  `cccr_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cccr_component_event_id`,`cccr_value`,`cccr_runnable_component`),
  KEY `FK-client_chat_component_event-cccr_created_user_id` (`cccr_created_user_id`),
  KEY `FK-client_chat_component_event-cccr_updated_user_id` (`cccr_updated_user_id`),
  CONSTRAINT `FK-client_chat_component_event-cccr_created_user_id` FOREIGN KEY (`cccr_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_component_event-cccr_updated_user_id` FOREIGN KEY (`cccr_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_component_rule-component_event_id` FOREIGN KEY (`cccr_component_event_id`) REFERENCES `client_chat_component_event` (`ccce_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_couch_note`
--

DROP TABLE IF EXISTS `client_chat_couch_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_couch_note` (
  `cccn_id` int NOT NULL AUTO_INCREMENT,
  `cccn_cch_id` int DEFAULT NULL,
  `cccn_rid` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cccn_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cccn_alias` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cccn_created_user_id` int DEFAULT NULL,
  `cccn_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cccn_id`),
  KEY `FK-client_chat_couch_note-cccn_cch_id` (`cccn_cch_id`),
  KEY `FK-client_chat_couch_note-cccn_created_user_id` (`cccn_created_user_id`),
  CONSTRAINT `FK-client_chat_couch_note-cccn_cch_id` FOREIGN KEY (`cccn_cch_id`) REFERENCES `client_chat` (`cch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_couch_note-cccn_created_user_id` FOREIGN KEY (`cccn_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_feedback`
--

DROP TABLE IF EXISTS `client_chat_feedback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_feedback` (
  `ccf_id` int NOT NULL AUTO_INCREMENT,
  `ccf_client_chat_id` int DEFAULT NULL,
  `ccf_user_id` int DEFAULT NULL,
  `ccf_client_id` int DEFAULT NULL,
  `ccf_rating` tinyint DEFAULT NULL,
  `ccf_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ccf_created_dt` datetime DEFAULT NULL,
  `ccf_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ccf_id`),
  KEY `FK-client_chat_feedback-ccf_client_chat_id` (`ccf_client_chat_id`),
  KEY `FK-client_chat_feedback-ccf_user_id` (`ccf_user_id`),
  KEY `FK-client_chat_feedback-ccf_client_id` (`ccf_client_id`),
  CONSTRAINT `FK-client_chat_feedback-ccf_client_chat_id` FOREIGN KEY (`ccf_client_chat_id`) REFERENCES `client_chat` (`cch_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_feedback-ccf_client_id` FOREIGN KEY (`ccf_client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_feedback-ccf_user_id` FOREIGN KEY (`ccf_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_form`
--

DROP TABLE IF EXISTS `client_chat_form`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_form` (
  `ccf_id` int NOT NULL AUTO_INCREMENT,
  `ccf_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ccf_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ccf_project_id` int DEFAULT NULL,
  `ccf_dataform_json` json DEFAULT NULL,
  `ccf_enabled` tinyint(1) DEFAULT '1',
  `ccf_created_user_id` int DEFAULT NULL,
  `ccf_updated_user_id` int DEFAULT NULL,
  `ccf_created_dt` datetime DEFAULT NULL,
  `ccf_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ccf_id`),
  UNIQUE KEY `ccf_key` (`ccf_key`),
  KEY `FK-client_chat_form-ccf_project_id` (`ccf_project_id`),
  KEY `FK-client_chat_form-ccf_created_user_id` (`ccf_created_user_id`),
  KEY `FK-client_chat_form-ccf_updated_user_id` (`ccf_updated_user_id`),
  CONSTRAINT `FK-client_chat_form-ccf_created_user_id` FOREIGN KEY (`ccf_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_form-ccf_project_id` FOREIGN KEY (`ccf_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_form-ccf_updated_user_id` FOREIGN KEY (`ccf_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_hold`
--

DROP TABLE IF EXISTS `client_chat_hold`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_hold` (
  `cchd_id` int NOT NULL AUTO_INCREMENT,
  `cchd_cch_id` int NOT NULL,
  `cchd_cch_status_log_id` int DEFAULT NULL,
  `cchd_deadline_dt` datetime NOT NULL,
  `cchd_start_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cchd_id`),
  KEY `FK-client_chat_hold-cchd_cch_id` (`cchd_cch_id`),
  KEY `FK-client_chat_hold-cchd_cch_status_log_id` (`cchd_cch_status_log_id`),
  CONSTRAINT `FK-client_chat_hold-cchd_cch_id` FOREIGN KEY (`cchd_cch_id`) REFERENCES `client_chat` (`cch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_hold-cchd_cch_status_log_id` FOREIGN KEY (`cchd_cch_status_log_id`) REFERENCES `client_chat_status_log` (`csl_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_last_message`
--

DROP TABLE IF EXISTS `client_chat_last_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_last_message` (
  `cclm_id` int NOT NULL AUTO_INCREMENT,
  `cclm_cch_id` int DEFAULT NULL,
  `cclm_type_id` tinyint DEFAULT NULL COMMENT '1 - client, 2 - user',
  `cclm_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cclm_dt` datetime DEFAULT NULL,
  `cclm_platform_id` tinyint DEFAULT '1',
  PRIMARY KEY (`cclm_id`),
  KEY `IND-client_chat_last_message-cclm_type_id` (`cclm_type_id`),
  KEY `FK-client_chat_last_message-cclm_cch_id` (`cclm_cch_id`),
  CONSTRAINT `FK-client_chat_last_message-cclm_cch_id` FOREIGN KEY (`cclm_cch_id`) REFERENCES `client_chat` (`cch_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_lead`
--

DROP TABLE IF EXISTS `client_chat_lead`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_lead` (
  `ccl_chat_id` int NOT NULL,
  `ccl_lead_id` int NOT NULL,
  `ccl_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ccl_chat_id`,`ccl_lead_id`),
  CONSTRAINT `FK-client_chat_lead-ccl_chat_id` FOREIGN KEY (`ccl_chat_id`) REFERENCES `client_chat` (`cch_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_note`
--

DROP TABLE IF EXISTS `client_chat_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_note` (
  `ccn_id` int NOT NULL AUTO_INCREMENT,
  `ccn_chat_id` int DEFAULT NULL,
  `ccn_user_id` int DEFAULT NULL,
  `ccn_note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ccn_deleted` tinyint(1) DEFAULT '0',
  `ccn_created_dt` datetime DEFAULT NULL,
  `ccn_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ccn_id`),
  KEY `FK-client_chat_note-employees` (`ccn_user_id`),
  KEY `IND-client_chat_note-ccn_deleted` (`ccn_deleted`),
  KEY `IND-client_chat_note-ccn_created_dt` (`ccn_created_dt`),
  KEY `FK-client_chat_note-ccn_chat_id` (`ccn_chat_id`),
  CONSTRAINT `FK-client_chat_note-ccn_chat_id` FOREIGN KEY (`ccn_chat_id`) REFERENCES `client_chat` (`cch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_note-employees` FOREIGN KEY (`ccn_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_project_config`
--

DROP TABLE IF EXISTS `client_chat_project_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_project_config` (
  `ccpc_project_id` int NOT NULL,
  `ccpc_params_json` json DEFAULT NULL,
  `ccpc_theme_json` json DEFAULT NULL,
  `ccpc_registration_json` json DEFAULT NULL,
  `ccpc_settings_json` json DEFAULT NULL,
  `ccpc_enabled` tinyint(1) DEFAULT '1',
  `ccpc_created_user_id` int DEFAULT NULL,
  `ccpc_updated_user_id` int DEFAULT NULL,
  `ccpc_created_dt` datetime DEFAULT NULL,
  `ccpc_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ccpc_project_id`),
  KEY `FK-client_chat_project_config-ccpc_created_user_id` (`ccpc_created_user_id`),
  KEY `FK-client_chat_project_config-ccpc_updated_user_id` (`ccpc_updated_user_id`),
  CONSTRAINT `FK-client_chat_project_config-ccpc_created_user_id` FOREIGN KEY (`ccpc_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_project_config-ccpc_project_id` FOREIGN KEY (`ccpc_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_project_config-ccpc_updated_user_id` FOREIGN KEY (`ccpc_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_status_log`
--

DROP TABLE IF EXISTS `client_chat_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_status_log` (
  `csl_id` int NOT NULL AUTO_INCREMENT,
  `csl_cch_id` int NOT NULL,
  `csl_from_status` tinyint DEFAULT NULL,
  `csl_to_status` tinyint DEFAULT NULL,
  `csl_start_dt` datetime DEFAULT NULL,
  `csl_end_dt` datetime DEFAULT NULL,
  `csl_owner_id` int DEFAULT NULL,
  `csl_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `csl_user_id` int DEFAULT NULL,
  `csl_prev_channel_id` int DEFAULT NULL,
  `csl_action_type` tinyint DEFAULT NULL,
  `csl_rid` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`csl_id`),
  KEY `FK-csl_cch_id` (`csl_cch_id`),
  KEY `FK-csl_owner_id` (`csl_owner_id`),
  KEY `FK-client_chat_status_log-csl_user_id` (`csl_user_id`),
  KEY `FK-client_chat_status_log-csl_prev_channel_id` (`csl_prev_channel_id`),
  KEY `IND-csl_action_type` (`csl_action_type`),
  KEY `IND-client_chat_status_log-csl_rid` (`csl_rid`),
  CONSTRAINT `FK-client_chat_status_log-csl_prev_channel_id` FOREIGN KEY (`csl_prev_channel_id`) REFERENCES `client_chat_channel` (`ccc_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_status_log-csl_user_id` FOREIGN KEY (`csl_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-csl_cch_id` FOREIGN KEY (`csl_cch_id`) REFERENCES `client_chat` (`cch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-csl_owner_id` FOREIGN KEY (`csl_owner_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_status_log_reason`
--

DROP TABLE IF EXISTS `client_chat_status_log_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_status_log_reason` (
  `cslr_id` int NOT NULL AUTO_INCREMENT,
  `cslr_status_log_id` int DEFAULT NULL,
  `cslr_action_reason_id` int DEFAULT NULL,
  `cslr_comment` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cslr_id`),
  KEY `FK-cslr_status_log_id` (`cslr_status_log_id`),
  KEY `FK-cslr_action_reason_id` (`cslr_action_reason_id`),
  CONSTRAINT `FK-cslr_action_reason_id` FOREIGN KEY (`cslr_action_reason_id`) REFERENCES `client_chat_action_reason` (`ccar_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-cslr_status_log_id` FOREIGN KEY (`cslr_status_log_id`) REFERENCES `client_chat_status_log` (`csl_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_unread`
--

DROP TABLE IF EXISTS `client_chat_unread`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_unread` (
  `ccu_cc_id` int NOT NULL,
  `ccu_count` int DEFAULT NULL,
  `ccu_created_dt` datetime DEFAULT NULL,
  `ccu_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ccu_cc_id`),
  KEY `IND-client_chat_unread-ccu_created_dt` (`ccu_created_dt`),
  KEY `IND-client_chat_unread-ccu_updated_dt` (`ccu_updated_dt`),
  CONSTRAINT `FK-client_chat_unread-ccu_cc_id` FOREIGN KEY (`ccu_cc_id`) REFERENCES `client_chat` (`cch_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_user_access`
--

DROP TABLE IF EXISTS `client_chat_user_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_user_access` (
  `ccua_id` int NOT NULL AUTO_INCREMENT,
  `ccua_cch_id` int NOT NULL,
  `ccua_user_id` int NOT NULL,
  `ccua_status_id` tinyint(1) DEFAULT NULL,
  `ccua_created_dt` datetime DEFAULT NULL,
  `ccua_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ccua_id`),
  KEY `IND-ccua_status_id` (`ccua_status_id`),
  KEY `FK-ccua_cch_id` (`ccua_cch_id`),
  KEY `IND-ccua_user_id` (`ccua_user_id`),
  CONSTRAINT `FK-ccua_cch_id` FOREIGN KEY (`ccua_cch_id`) REFERENCES `client_chat` (`cch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-ccua_user_id` FOREIGN KEY (`ccua_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_user_channel`
--

DROP TABLE IF EXISTS `client_chat_user_channel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_user_channel` (
  `ccuc_user_id` int NOT NULL,
  `ccuc_channel_id` int NOT NULL,
  `ccuc_created_dt` datetime DEFAULT NULL,
  `ccuc_created_user_id` int DEFAULT NULL,
  PRIMARY KEY (`ccuc_user_id`,`ccuc_channel_id`),
  KEY `FK-ccuc_channel_id` (`ccuc_channel_id`),
  KEY `FK-ccuc_created_user_id` (`ccuc_created_user_id`),
  CONSTRAINT `FK-ccuc_channel_id` FOREIGN KEY (`ccuc_channel_id`) REFERENCES `client_chat_channel` (`ccc_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-ccuc_created_user_id` FOREIGN KEY (`ccuc_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-ccuc_user_id` FOREIGN KEY (`ccuc_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_visitor`
--

DROP TABLE IF EXISTS `client_chat_visitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_visitor` (
  `ccv_id` int NOT NULL AUTO_INCREMENT,
  `ccv_cch_id` int DEFAULT NULL,
  `ccv_cvd_id` int DEFAULT NULL,
  `ccv_client_id` int DEFAULT NULL,
  PRIMARY KEY (`ccv_id`),
  UNIQUE KEY `UNI-client_chat_visitor-cch_id-cvd_id` (`ccv_cch_id`,`ccv_cvd_id`),
  KEY `FK-clients-ccv_client_id` (`ccv_client_id`),
  KEY `FK-client_chat_visitor-ccv_cvd_id` (`ccv_cvd_id`),
  CONSTRAINT `FK-client_chat_visitor-ccv_cch_id` FOREIGN KEY (`ccv_cch_id`) REFERENCES `client_chat` (`cch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-client_chat_visitor-ccv_cvd_id` FOREIGN KEY (`ccv_cvd_id`) REFERENCES `client_chat_visitor_data` (`cvd_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-clients-ccv_client_id` FOREIGN KEY (`ccv_client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_chat_visitor_data`
--

DROP TABLE IF EXISTS `client_chat_visitor_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_chat_visitor_data` (
  `cvd_id` int NOT NULL AUTO_INCREMENT,
  `cvd_country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cvd_region` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cvd_city` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cvd_latitude` float DEFAULT NULL,
  `cvd_longitude` float DEFAULT NULL,
  `cvd_url` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cvd_title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cvd_referrer` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cvd_timezone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cvd_local_time` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cvd_data` json DEFAULT NULL,
  `cvd_created_dt` datetime DEFAULT NULL,
  `cvd_updated_dt` datetime DEFAULT NULL,
  `cvd_visitor_rc_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cvd_id`),
  UNIQUE KEY `cvd_visitor_rc_id` (`cvd_visitor_rc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_email`
--

DROP TABLE IF EXISTS `client_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_email` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT NULL,
  `comments` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type` tinyint DEFAULT '0',
  `ce_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk-clients-client_email` (`client_id`),
  CONSTRAINT `fk-clients-client_email` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=188518 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_phone`
--

DROP TABLE IF EXISTS `client_phone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_phone` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int NOT NULL,
  `phone` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT NULL,
  `comments` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_sms` int DEFAULT '0',
  `validate_dt` datetime DEFAULT NULL,
  `type` tinyint DEFAULT '0',
  `cp_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cp_cpl_uid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk-clients-client_phone` (`client_id`),
  KEY `IND-client_phone` (`phone`),
  CONSTRAINT `fk-clients-client_phone` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=459597 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_project`
--

DROP TABLE IF EXISTS `client_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_project` (
  `cp_client_id` int NOT NULL,
  `cp_project_id` int NOT NULL,
  `cp_created_dt` datetime DEFAULT NULL,
  `cp_unsubscribe` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`cp_client_id`,`cp_project_id`),
  KEY `FK-client_project_cp_project_id` (`cp_project_id`),
  CONSTRAINT `FK-client_project-cp_client_id` FOREIGN KEY (`cp_client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-client_project_cp_project_id` FOREIGN KEY (`cp_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client_visitor`
--

DROP TABLE IF EXISTS `client_visitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_visitor` (
  `cv_id` int NOT NULL AUTO_INCREMENT,
  `cv_client_id` int NOT NULL,
  `cv_visitor_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cv_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cv_id`),
  KEY `IND-client_visitor-cv_visitor_id` (`cv_visitor_id`),
  KEY `FK-client_visitor-cv_client_id` (`cv_client_id`),
  CONSTRAINT `FK-client_visitor-cv_client_id` FOREIGN KEY (`cv_client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT NULL,
  `uuid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `is_company` tinyint(1) DEFAULT '0',
  `is_public` tinyint(1) DEFAULT '0',
  `company_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `disabled` tinyint(1) DEFAULT '0',
  `rating` tinyint DEFAULT NULL,
  `cl_type_id` tinyint DEFAULT '1' COMMENT '1 - Client, 2 - Contact',
  `cl_type_create` tinyint DEFAULT NULL,
  `cl_project_id` int DEFAULT NULL,
  `cl_ca_id` int DEFAULT NULL,
  `cl_ppn` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_excluded` tinyint(1) DEFAULT '0',
  `cl_ip` varchar(39) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_locale` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_marketing_country` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_call_recording_disabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDX-clients-uuid` (`uuid`),
  KEY `IND-clients-first_name` (`first_name`),
  KEY `IND-clients-last_name` (`last_name`),
  KEY `IND-clients-company_name` (`company_name`),
  KEY `FK-clients-id_parent_id` (`parent_id`),
  KEY `IND-clients-cl_type_id` (`cl_type_id`),
  KEY `FK-clients-cl_project_id` (`cl_project_id`),
  KEY `FK-clients-cl_ca_id` (`cl_ca_id`),
  KEY `IND-clients-cl_excluded` (`cl_excluded`),
  CONSTRAINT `FK-clients-cl_ca_id` FOREIGN KEY (`cl_ca_id`) REFERENCES `client_account` (`ca_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-clients-cl_project_id` FOREIGN KEY (`cl_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-clients-id_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=459235 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conference`
--

DROP TABLE IF EXISTS `conference`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conference` (
  `cf_id` int NOT NULL AUTO_INCREMENT,
  `cf_cr_id` int DEFAULT NULL,
  `cf_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cf_status_id` smallint DEFAULT NULL,
  `cf_options` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cf_created_dt` datetime DEFAULT NULL,
  `cf_updated_dt` datetime DEFAULT NULL,
  `cf_recording_url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cf_recording_duration` int DEFAULT NULL,
  `cf_recording_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cf_friendly_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cf_call_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cf_created_user_id` int DEFAULT NULL,
  `cf_start_dt` datetime DEFAULT NULL,
  `cf_end_dt` datetime DEFAULT NULL,
  `cf_duration` smallint DEFAULT NULL,
  `cf_recording_disabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`cf_id`),
  UNIQUE KEY `cf_sid` (`cf_sid`),
  UNIQUE KEY `IND-conference_cf_recording_sid` (`cf_recording_sid`),
  KEY `FK-conference_cf_cr_id` (`cf_cr_id`),
  KEY `IND-conference_cf_sid` (`cf_sid`),
  KEY `FK-conference-cf_created_user_id` (`cf_created_user_id`),
  CONSTRAINT `FK-conference-cf_created_user_id` FOREIGN KEY (`cf_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conference_event_log`
--

DROP TABLE IF EXISTS `conference_event_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conference_event_log` (
  `cel_id` int NOT NULL AUTO_INCREMENT,
  `cel_event_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cel_conference_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cel_sequence_number` smallint DEFAULT NULL,
  `cel_created_dt` datetime NOT NULL,
  `cel_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`cel_id`),
  KEY `IND-conference_event_log-cel_conference_sid` (`cel_conference_sid`),
  KEY `IND-conference_event_log-cel_sequence_number` (`cel_sequence_number`),
  KEY `IND-conference_event_log-cel_created_dt` (`cel_created_dt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conference_log`
--

DROP TABLE IF EXISTS `conference_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conference_log` (
  `cl_id` int NOT NULL AUTO_INCREMENT,
  `cl_cf_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cl_cf_id` int NOT NULL,
  `cl_sequence_number` smallint DEFAULT NULL,
  `cl_status_callback_event` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cl_json_data` json DEFAULT NULL,
  `cl_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cl_id`),
  KEY `FK-conference_cl_cf_id` (`cl_cf_id`),
  KEY `IND-conference_cl_cf_sid` (`cl_cf_sid`),
  KEY `IND-conference_cl_status_callback_event` (`cl_status_callback_event`),
  CONSTRAINT `FK-conference_cl_cf_id` FOREIGN KEY (`cl_cf_id`) REFERENCES `conference` (`cf_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conference_participant`
--

DROP TABLE IF EXISTS `conference_participant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conference_participant` (
  `cp_id` int NOT NULL AUTO_INCREMENT,
  `cp_cf_id` int NOT NULL,
  `cp_call_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cp_call_id` int DEFAULT NULL,
  `cp_status_id` smallint DEFAULT NULL,
  `cp_join_dt` datetime DEFAULT NULL,
  `cp_leave_dt` datetime DEFAULT NULL,
  `cp_type_id` tinyint(1) DEFAULT NULL,
  `cp_hold_dt` datetime DEFAULT NULL,
  `cp_mute` tinyint(1) DEFAULT '0',
  `cp_cf_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cp_user_id` int DEFAULT NULL,
  `cp_identity` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`cp_id`),
  KEY `FK-conference_participant_cp_cf_id` (`cp_cf_id`),
  KEY `FK-conference_participant_cp_call_id` (`cp_call_id`),
  KEY `IND-conference_participant_cp_call_sid` (`cp_call_sid`),
  KEY `IND-conference_participant-cp_cf_sid` (`cp_cf_sid`),
  CONSTRAINT `FK-conference_participant_cp_call_id` FOREIGN KEY (`cp_call_id`) REFERENCES `call` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-conference_participant_cp_cf_id` FOREIGN KEY (`cp_cf_id`) REFERENCES `conference` (`cf_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conference_participant_stats`
--

DROP TABLE IF EXISTS `conference_participant_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conference_participant_stats` (
  `cps_id` int NOT NULL AUTO_INCREMENT,
  `cps_cf_id` int DEFAULT NULL,
  `cps_cf_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cps_participant_identity` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cps_user_id` int DEFAULT NULL,
  `cps_created_dt` datetime NOT NULL,
  `cps_duration` smallint DEFAULT NULL,
  `cps_talk_time` smallint DEFAULT NULL,
  `cps_hold_time` smallint DEFAULT NULL,
  PRIMARY KEY (`cps_id`),
  UNIQUE KEY `IND-cps-cf_sid-identity` (`cps_cf_sid`,`cps_participant_identity`),
  KEY `FK-cps-cps_user_id` (`cps_user_id`),
  KEY `FK-cps-cps_cf_id` (`cps_cf_id`),
  KEY `IND-cps-cps_duration` (`cps_duration`),
  KEY `IND-cps-cps_talk_time` (`cps_talk_time`),
  KEY `IND-cps-cps_hold_time` (`cps_hold_time`),
  CONSTRAINT `FK-cps-cps_cf_id` FOREIGN KEY (`cps_cf_id`) REFERENCES `conference` (`cf_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-cps-cps_user_id` FOREIGN KEY (`cps_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conference_recording_log`
--

DROP TABLE IF EXISTS `conference_recording_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conference_recording_log` (
  `cfrl_id` int NOT NULL AUTO_INCREMENT,
  `cfrl_conference_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cfrl_user_id` int NOT NULL,
  `cfrl_created_dt` datetime DEFAULT NULL,
  `cfrl_year` smallint NOT NULL,
  `cfrl_month` tinyint NOT NULL,
  PRIMARY KEY (`cfrl_id`,`cfrl_year`,`cfrl_month`),
  KEY `IND-conference_recording_log-cfrl_conference_sid` (`cfrl_conference_sid`),
  KEY `IND-conference_recording_log-cfrl_year` (`cfrl_year`),
  KEY `IND-conference_recording_log-cfrl_month` (`cfrl_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
/*!50100 PARTITION BY RANGE (`cfrl_year`)
SUBPARTITION BY LINEAR HASH (`cfrl_month`)
SUBPARTITIONS 12
(PARTITION y21 VALUES LESS THAN (2021) ENGINE = InnoDB,
 PARTITION y22 VALUES LESS THAN (2022) ENGINE = InnoDB,
 PARTITION y23 VALUES LESS THAN (2023) ENGINE = InnoDB,
 PARTITION y24 VALUES LESS THAN (2024) ENGINE = InnoDB,
 PARTITION y VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conference_room`
--

DROP TABLE IF EXISTS `conference_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `conference_room` (
  `cr_id` int NOT NULL AUTO_INCREMENT,
  `cr_key` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cr_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cr_phone_number` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cr_enabled` tinyint(1) DEFAULT '1',
  `cr_start_dt` datetime DEFAULT NULL,
  `cr_end_dt` datetime DEFAULT NULL,
  `cr_param_muted` tinyint(1) DEFAULT '0',
  `cr_param_beep` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'true',
  `cr_param_start_conference_on_enter` tinyint(1) DEFAULT '1',
  `cr_param_end_conference_on_exit` tinyint(1) DEFAULT '0',
  `cr_param_max_participants` smallint DEFAULT '250',
  `cr_param_record` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'record-from-start',
  `cr_param_region` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cr_param_trim` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'trim-silence',
  `cr_param_wait_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cr_moderator_phone_number` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cr_welcome_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cr_created_dt` datetime DEFAULT NULL,
  `cr_updated_dt` datetime DEFAULT NULL,
  `cr_created_user_id` int DEFAULT NULL,
  `cr_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`cr_id`),
  UNIQUE KEY `cr_key` (`cr_key`),
  KEY `IND-conference_room_cr_phone_number_cr_enabled` (`cr_phone_number`,`cr_enabled`),
  KEY `FK-conference_room_cr_created_user_id` (`cr_created_user_id`),
  KEY `FK-conference_room_cr_updated_user_id` (`cr_updated_user_id`),
  CONSTRAINT `FK-conference_room_cr_created_user_id` FOREIGN KEY (`cr_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-conference_room_cr_updated_user_id` FOREIGN KEY (`cr_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact_phone_data`
--

DROP TABLE IF EXISTS `contact_phone_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_phone_data` (
  `cpd_cpl_id` int NOT NULL,
  `cpd_key` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpd_value` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpd_created_dt` datetime DEFAULT NULL,
  `cpd_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cpd_cpl_id`,`cpd_key`),
  CONSTRAINT `FK-contact_phone_data-cpd_cpl_id` FOREIGN KEY (`cpd_cpl_id`) REFERENCES `contact_phone_list` (`cpl_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact_phone_list`
--

DROP TABLE IF EXISTS `contact_phone_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_phone_list` (
  `cpl_id` int NOT NULL AUTO_INCREMENT,
  `cpl_phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpl_uid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpl_title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cpl_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cpl_id`),
  UNIQUE KEY `cpl_phone_number` (`cpl_phone_number`),
  UNIQUE KEY `IND-contact_phone_list-cpl_uid` (`cpl_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contact_phone_service_info`
--

DROP TABLE IF EXISTS `contact_phone_service_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_phone_service_info` (
  `cpsi_cpl_id` int NOT NULL,
  `cpsi_service_id` tinyint NOT NULL,
  `cpsi_data_json` json DEFAULT NULL,
  `cpsi_created_dt` datetime DEFAULT NULL,
  `cpsi_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cpsi_cpl_id`,`cpsi_service_id`),
  CONSTRAINT `FK-contact_phone_service_info-cpsi_cpl_id` FOREIGN KEY (`cpsi_cpl_id`) REFERENCES `contact_phone_list` (`cpl_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coupon`
--

DROP TABLE IF EXISTS `coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon` (
  `c_id` int NOT NULL AUTO_INCREMENT,
  `c_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `c_amount` decimal(8,2) DEFAULT NULL,
  `c_currency_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c_percent` smallint DEFAULT NULL,
  `c_exp_date` datetime DEFAULT NULL,
  `c_start_date` datetime DEFAULT NULL,
  `c_reusable` tinyint(1) DEFAULT NULL,
  `c_reusable_count` int DEFAULT NULL,
  `c_public` tinyint(1) DEFAULT NULL,
  `c_status_id` tinyint DEFAULT NULL,
  `c_disabled` tinyint(1) DEFAULT NULL,
  `c_type_id` smallint DEFAULT NULL,
  `c_created_dt` datetime DEFAULT NULL,
  `c_updated_dt` datetime DEFAULT NULL,
  `c_created_user_id` int DEFAULT NULL,
  `c_updated_user_id` int DEFAULT NULL,
  `c_used_count` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`),
  UNIQUE KEY `IND-coupon-c_code` (`c_code`),
  KEY `FK-coupon-c_created_user_id` (`c_created_user_id`),
  KEY `FK-coupon-c_updated_user_id` (`c_updated_user_id`),
  KEY `IND-coupon-c_used_count` (`c_used_count`),
  KEY `IND-coupon-c_disabled` (`c_disabled`),
  KEY `IND-coupon-c_reusable` (`c_reusable`),
  KEY `IND-coupon-c_reusable_count` (`c_reusable_count`),
  KEY `IND-coupon-c_start_date` (`c_start_date`),
  KEY `IND-coupon-c_exp_date` (`c_exp_date`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coupon_case`
--

DROP TABLE IF EXISTS `coupon_case`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_case` (
  `cc_coupon_id` int NOT NULL,
  `cc_case_id` int NOT NULL,
  `cc_sale_id` int DEFAULT NULL,
  `cc_created_dt` datetime DEFAULT NULL,
  `cc_created_user_id` int DEFAULT NULL,
  PRIMARY KEY (`cc_coupon_id`,`cc_case_id`),
  KEY `FK-coupon_case-cc_case_id` (`cc_case_id`),
  KEY `FK-coupon_case-cc_created_user_id` (`cc_created_user_id`),
  CONSTRAINT `FK-coupon_case-cc_case_id` FOREIGN KEY (`cc_case_id`) REFERENCES `cases` (`cs_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-coupon_case-cc_coupon_id` FOREIGN KEY (`cc_coupon_id`) REFERENCES `coupon` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-coupon_case-cc_created_user_id` FOREIGN KEY (`cc_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coupon_client`
--

DROP TABLE IF EXISTS `coupon_client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_client` (
  `cuc_id` int NOT NULL AUTO_INCREMENT,
  `cuc_coupon_id` int NOT NULL,
  `cuc_client_id` int NOT NULL,
  `cuc_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cuc_id`),
  KEY `FK-coupon_client-cuc_coupon_id` (`cuc_coupon_id`),
  KEY `FK-coupon_client-cuc_client_id` (`cuc_client_id`),
  CONSTRAINT `FK-coupon_client-cuc_client_id` FOREIGN KEY (`cuc_client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-coupon_client-cuc_coupon_id` FOREIGN KEY (`cuc_coupon_id`) REFERENCES `coupon` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coupon_product`
--

DROP TABLE IF EXISTS `coupon_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_product` (
  `cup_coupon_id` int NOT NULL,
  `cup_product_type_id` int NOT NULL,
  `cup_data_json` json NOT NULL,
  PRIMARY KEY (`cup_coupon_id`,`cup_product_type_id`),
  KEY `FK-coupon_product-cup_product_type_id` (`cup_product_type_id`),
  CONSTRAINT `FK-coupon_product-cu_coupon_id` FOREIGN KEY (`cup_coupon_id`) REFERENCES `coupon` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-coupon_product-cup_product_type_id` FOREIGN KEY (`cup_product_type_id`) REFERENCES `product_type` (`pt_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coupon_send`
--

DROP TABLE IF EXISTS `coupon_send`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_send` (
  `cus_id` int NOT NULL AUTO_INCREMENT,
  `cus_coupon_id` int NOT NULL,
  `cus_user_id` int DEFAULT NULL,
  `cus_type_id` tinyint NOT NULL DEFAULT '1',
  `cus_send_to` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cus_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cus_id`),
  KEY `FK-coupon_send-cus_coupon_id` (`cus_coupon_id`),
  KEY `FK-coupon_send-cus_user_id` (`cus_user_id`),
  CONSTRAINT `FK-coupon_send-cus_coupon_id` FOREIGN KEY (`cus_coupon_id`) REFERENCES `coupon` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-coupon_send-cus_user_id` FOREIGN KEY (`cus_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coupon_use`
--

DROP TABLE IF EXISTS `coupon_use`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_use` (
  `cu_id` int NOT NULL AUTO_INCREMENT,
  `cu_coupon_id` int NOT NULL,
  `cu_ip` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cu_user_agent` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cu_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cu_id`),
  KEY `FK-coupon_use-cu_coupon_id` (`cu_coupon_id`),
  CONSTRAINT `FK-coupon_use-cu_coupon_id` FOREIGN KEY (`cu_coupon_id`) REFERENCES `coupon` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `coupon_user_action`
--

DROP TABLE IF EXISTS `coupon_user_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coupon_user_action` (
  `cuu_id` int NOT NULL AUTO_INCREMENT,
  `cuu_coupon_id` int NOT NULL,
  `cuu_action_id` int NOT NULL,
  `cuu_api_user_id` int DEFAULT NULL,
  `cuu_user_id` int DEFAULT NULL,
  `cuu_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cuu_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cuu_id`),
  KEY `FK-coupon_user_action-cuu_coupon_id` (`cuu_coupon_id`),
  KEY `FK-coupon_user_action-cuu_api_user_id` (`cuu_api_user_id`),
  KEY `FK-coupon_user_action-cuu_user_id` (`cuu_user_id`),
  CONSTRAINT `FK-coupon_user_action-cuu_api_user_id` FOREIGN KEY (`cuu_api_user_id`) REFERENCES `api_user` (`au_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-coupon_user_action-cuu_coupon_id` FOREIGN KEY (`cuu_coupon_id`) REFERENCES `coupon` (`c_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-coupon_user_action-cuu_user_id` FOREIGN KEY (`cuu_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `credit_card`
--

DROP TABLE IF EXISTS `credit_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_card` (
  `cc_id` int NOT NULL AUTO_INCREMENT,
  `cc_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cc_display_number` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cc_holder_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cc_expiration_month` tinyint NOT NULL,
  `cc_expiration_year` smallint NOT NULL,
  `cc_cvv` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cc_type_id` tinyint(1) DEFAULT NULL,
  `cc_status_id` tinyint(1) DEFAULT '0',
  `cc_is_expired` tinyint(1) DEFAULT '0',
  `cc_created_user_id` int DEFAULT NULL,
  `cc_updated_user_id` int DEFAULT NULL,
  `cc_created_dt` datetime DEFAULT NULL,
  `cc_updated_dt` datetime DEFAULT NULL,
  `cc_security_hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cc_bo_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cc_is_sync_bo` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`cc_id`),
  KEY `FK-credit_card-cc_created_user_id` (`cc_created_user_id`),
  KEY `FK-credit_card-cc_updated_user_id` (`cc_updated_user_id`),
  CONSTRAINT `FK-credit_card-cc_created_user_id` FOREIGN KEY (`cc_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-credit_card-cc_updated_user_id` FOREIGN KEY (`cc_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cron_scheduler`
--

DROP TABLE IF EXISTS `cron_scheduler`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cron_scheduler` (
  `cs_id` int NOT NULL AUTO_INCREMENT,
  `cs_hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cs_cron_expression` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cs_cron_command` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cs_cron_params` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cs_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cs_enabled` tinyint(1) DEFAULT '1',
  `cs_sort_order` smallint DEFAULT '0',
  `cs_created_dt` datetime DEFAULT NULL,
  `cs_updated_dt` datetime DEFAULT NULL,
  `cs_created_user_id` int DEFAULT NULL,
  `cs_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`cs_id`),
  UNIQUE KEY `cs_hash` (`cs_hash`),
  UNIQUE KEY `IND-cron_scheduler-cs_hash` (`cs_hash`),
  KEY `FK-cron_scheduler-cs_created_user_id` (`cs_created_user_id`),
  KEY `FK-cron_scheduler-cs_updated_user_id` (`cs_updated_user_id`),
  CONSTRAINT `FK-cron_scheduler-cs_created_user_id` FOREIGN KEY (`cs_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-cron_scheduler-cs_updated_user_id` FOREIGN KEY (`cs_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cruise`
--

DROP TABLE IF EXISTS `cruise`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cruise` (
  `crs_id` int NOT NULL AUTO_INCREMENT,
  `crs_product_id` int DEFAULT NULL,
  `crs_departure_date_from` date DEFAULT NULL,
  `crs_arrival_date_to` date DEFAULT NULL,
  `crs_destination_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crs_destination_label` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`crs_id`),
  KEY `FK-cruise-crs_product_id` (`crs_product_id`),
  CONSTRAINT `FK-cruise-crs_product_id` FOREIGN KEY (`crs_product_id`) REFERENCES `product` (`pr_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cruise_cabin`
--

DROP TABLE IF EXISTS `cruise_cabin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cruise_cabin` (
  `crc_id` int NOT NULL AUTO_INCREMENT,
  `crc_cruise_id` int NOT NULL,
  `crc_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`crc_id`),
  KEY `FK-cruise_cabin-crc_cruise_id` (`crc_cruise_id`),
  CONSTRAINT `FK-cruise_cabin-crc_cruise_id` FOREIGN KEY (`crc_cruise_id`) REFERENCES `cruise` (`crs_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cruise_cabin_pax`
--

DROP TABLE IF EXISTS `cruise_cabin_pax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cruise_cabin_pax` (
  `crp_id` int NOT NULL AUTO_INCREMENT,
  `crp_cruise_cabin_id` int NOT NULL,
  `crp_type_id` tinyint NOT NULL,
  `crp_age` tinyint DEFAULT NULL,
  `crp_first_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crp_last_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crp_dob` date DEFAULT NULL,
  PRIMARY KEY (`crp_id`),
  KEY `FK-cruise_cabin_pax-crp_cruise_cabin_id` (`crp_cruise_cabin_id`),
  KEY `IND-cruise_cabin_pax-crp_type_id` (`crp_type_id`),
  CONSTRAINT `FK-cruise_cabin_pax-crp_cruise_cabin_id` FOREIGN KEY (`crp_cruise_cabin_id`) REFERENCES `cruise_cabin` (`crc_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cruise_quote`
--

DROP TABLE IF EXISTS `cruise_quote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cruise_quote` (
  `crq_id` int NOT NULL AUTO_INCREMENT,
  `crq_hash_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crq_product_quote_id` int DEFAULT NULL,
  `crq_cruise_id` int DEFAULT NULL,
  `crq_data_json` json DEFAULT NULL,
  `crq_amount` decimal(10,2) DEFAULT NULL,
  `crq_amount_per_person` decimal(10,2) DEFAULT NULL,
  `crq_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `crq_adults` tinyint DEFAULT NULL,
  `crq_children` tinyint DEFAULT NULL,
  `crq_system_mark_up` decimal(10,2) DEFAULT NULL,
  `crq_agent_mark_up` decimal(10,2) DEFAULT NULL,
  `crq_service_fee_percent` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`crq_id`),
  KEY `FK-cruise_quote-crq_product_quote_id` (`crq_product_quote_id`),
  KEY `FK-cruise_quote-crq_cruise_id` (`crq_cruise_id`),
  CONSTRAINT `FK-cruise_quote-crq_cruise_id` FOREIGN KEY (`crq_cruise_id`) REFERENCES `cruise` (`crs_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-cruise_quote-crq_product_quote_id` FOREIGN KEY (`crq_product_quote_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currency` (
  `cur_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cur_name` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cur_symbol` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cur_base_rate` decimal(8,5) DEFAULT '1.00000',
  `cur_app_rate` decimal(8,5) DEFAULT '1.00000',
  `cur_app_percent` decimal(5,3) DEFAULT '0.000',
  `cur_enabled` tinyint(1) DEFAULT '1',
  `cur_default` tinyint(1) DEFAULT '0',
  `cur_sort_order` smallint DEFAULT '3',
  `cur_created_dt` datetime DEFAULT NULL,
  `cur_updated_dt` datetime DEFAULT NULL,
  `cur_synch_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`cur_code`),
  UNIQUE KEY `cur_code` (`cur_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `currency_history`
--

DROP TABLE IF EXISTS `currency_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currency_history` (
  `ch_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ch_base_rate` decimal(8,5) DEFAULT '1.00000',
  `ch_app_rate` decimal(8,5) DEFAULT '1.00000',
  `ch_app_percent` decimal(5,3) DEFAULT '0.000',
  `ch_created_date` date NOT NULL,
  `ch_main_created_dt` datetime DEFAULT NULL,
  `ch_main_updated_dt` datetime DEFAULT NULL,
  `ch_main_synch_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ch_code`,`ch_created_date`),
  CONSTRAINT `FK-currency_history-ch_code` FOREIGN KEY (`ch_code`) REFERENCES `currency` (`cur_code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `department` (
  `dep_id` int NOT NULL,
  `dep_key` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dep_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `dep_updated_user_id` int DEFAULT NULL,
  `dep_updated_dt` datetime DEFAULT NULL,
  `dep_params` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`dep_id`),
  UNIQUE KEY `dep_id` (`dep_id`),
  UNIQUE KEY `dep_key` (`dep_key`),
  KEY `FK-department_dep_updated_user_id` (`dep_updated_user_id`),
  KEY `IND-department-dep_name` (`dep_name`),
  CONSTRAINT `FK-department_dep_updated_user_id` FOREIGN KEY (`dep_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `department_email_project`
--

DROP TABLE IF EXISTS `department_email_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `department_email_project` (
  `dep_id` int NOT NULL AUTO_INCREMENT,
  `dep_email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dep_project_id` int NOT NULL,
  `dep_dep_id` int DEFAULT NULL,
  `dep_source_id` int DEFAULT NULL,
  `dep_enable` tinyint(1) DEFAULT '1',
  `dep_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dep_default` tinyint(1) DEFAULT '0',
  `dep_updated_user_id` int DEFAULT NULL,
  `dep_updated_dt` datetime DEFAULT NULL,
  `dep_email_list_id` int DEFAULT NULL,
  PRIMARY KEY (`dep_id`),
  UNIQUE KEY `dep_email` (`dep_email`),
  KEY `FK-department_email_project_dep_dep_id` (`dep_dep_id`),
  KEY `FK-department_email_project_dep_project_id` (`dep_project_id`),
  KEY `FK-department_email_project_dep_source_id` (`dep_source_id`),
  KEY `FK-department_email_project_dep_updated_user_id` (`dep_updated_user_id`),
  KEY `FK-department_email_project-dep_email_list_id` (`dep_email_list_id`),
  CONSTRAINT `FK-department_email_project-dep_email_list_id` FOREIGN KEY (`dep_email_list_id`) REFERENCES `email_list` (`el_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-department_email_project_dep_dep_id` FOREIGN KEY (`dep_dep_id`) REFERENCES `department` (`dep_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-department_email_project_dep_project_id` FOREIGN KEY (`dep_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-department_email_project_dep_source_id` FOREIGN KEY (`dep_source_id`) REFERENCES `sources` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-department_email_project_dep_updated_user_id` FOREIGN KEY (`dep_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `department_email_project_user_group`
--

DROP TABLE IF EXISTS `department_email_project_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `department_email_project_user_group` (
  `dug_dep_id` int NOT NULL,
  `dug_ug_id` int NOT NULL,
  `dug_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`dug_dep_id`,`dug_ug_id`),
  KEY `FK-department_email_project_user_group_dug_ug_id` (`dug_ug_id`),
  CONSTRAINT `FK-department_email_project_user_group_dug_dep_id` FOREIGN KEY (`dug_dep_id`) REFERENCES `department_email_project` (`dep_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-department_email_project_user_group_dug_ug_id` FOREIGN KEY (`dug_ug_id`) REFERENCES `user_group` (`ug_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `department_phone_project`
--

DROP TABLE IF EXISTS `department_phone_project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `department_phone_project` (
  `dpp_id` int NOT NULL AUTO_INCREMENT,
  `dpp_phone_number` varchar(18) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dpp_project_id` int NOT NULL,
  `dpp_dep_id` int DEFAULT NULL,
  `dpp_source_id` int DEFAULT NULL,
  `dpp_params` json DEFAULT NULL,
  `dpp_ivr_enable` tinyint(1) DEFAULT '0',
  `dpp_enable` tinyint(1) DEFAULT '1',
  `dpp_updated_user_id` int DEFAULT NULL,
  `dpp_updated_dt` datetime DEFAULT NULL,
  `dpp_redial` tinyint(1) DEFAULT NULL,
  `dpp_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dpp_default` tinyint(1) DEFAULT '0',
  `dpp_show_on_site` tinyint(1) DEFAULT '0',
  `dpp_phone_list_id` int DEFAULT NULL,
  `dpp_language_id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dpp_allow_transfer` tinyint(1) DEFAULT NULL,
  `dpp_priority` smallint DEFAULT '0',
  PRIMARY KEY (`dpp_id`),
  UNIQUE KEY `dpp_phone_number` (`dpp_phone_number`),
  KEY `FK-department_phone_project_dpp_dep_id` (`dpp_dep_id`),
  KEY `FK-department_phone_project_dpp_project_id` (`dpp_project_id`),
  KEY `FK-department_phone_project_dpp_source_id` (`dpp_source_id`),
  KEY `FK-department_phone_project_dpp_updated_user_id` (`dpp_updated_user_id`),
  KEY `FK-department_phone_project-dpp_phone_list_id` (`dpp_phone_list_id`),
  KEY `FK-department_phone_project-dpp_language_id` (`dpp_language_id`),
  KEY `IND-dpp_allow_transfer` (`dpp_allow_transfer`),
  KEY `IND-department_phone_project-priority` (`dpp_priority`),
  CONSTRAINT `FK-department_phone_project-dpp_language_id` FOREIGN KEY (`dpp_language_id`) REFERENCES `language` (`language_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-department_phone_project-dpp_phone_list_id` FOREIGN KEY (`dpp_phone_list_id`) REFERENCES `phone_list` (`pl_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-department_phone_project_dpp_dep_id` FOREIGN KEY (`dpp_dep_id`) REFERENCES `department` (`dep_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-department_phone_project_dpp_project_id` FOREIGN KEY (`dpp_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-department_phone_project_dpp_source_id` FOREIGN KEY (`dpp_source_id`) REFERENCES `sources` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-department_phone_project_dpp_updated_user_id` FOREIGN KEY (`dpp_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `department_phone_project_user_group`
--

DROP TABLE IF EXISTS `department_phone_project_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `department_phone_project_user_group` (
  `dug_dpp_id` int NOT NULL,
  `dug_ug_id` int NOT NULL,
  `dug_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`dug_dpp_id`,`dug_ug_id`),
  KEY `FK-department_phone_project_user_group_dug_ug_id` (`dug_ug_id`),
  CONSTRAINT `FK-department_phone_project_user_group_dug_dpp_id` FOREIGN KEY (`dug_dpp_id`) REFERENCES `department_phone_project` (`dpp_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-department_phone_project_user_group_dug_ug_id` FOREIGN KEY (`dug_ug_id`) REFERENCES `user_group` (`ug_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email`
--

DROP TABLE IF EXISTS `email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email` (
  `e_id` int NOT NULL AUTO_INCREMENT,
  `e_reply_id` int DEFAULT NULL,
  `e_lead_id` int DEFAULT NULL,
  `e_project_id` int DEFAULT NULL,
  `e_email_from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `e_email_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `e_email_cc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_email_bc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_email_subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_email_body_text` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `e_attach` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_email_data` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `e_type_id` smallint DEFAULT '0',
  `e_template_type_id` int DEFAULT NULL,
  `e_language_id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_communication_id` int DEFAULT NULL,
  `e_is_deleted` tinyint(1) DEFAULT '0',
  `e_is_new` tinyint(1) DEFAULT '0',
  `e_delay` int DEFAULT NULL,
  `e_priority` smallint DEFAULT '2',
  `e_status_id` smallint DEFAULT '1',
  `e_status_done_dt` datetime DEFAULT NULL,
  `e_read_dt` datetime DEFAULT NULL,
  `e_error_message` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_created_user_id` int DEFAULT NULL,
  `e_updated_user_id` int DEFAULT NULL,
  `e_created_dt` datetime DEFAULT NULL,
  `e_updated_dt` datetime DEFAULT NULL,
  `e_message_id` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_ref_message_id` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `e_inbox_created_dt` datetime DEFAULT NULL,
  `e_inbox_email_id` int DEFAULT NULL,
  `e_email_from_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_email_to_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e_case_id` int DEFAULT NULL,
  `e_email_body_blob` mediumblob,
  `e_client_id` int DEFAULT NULL,
  PRIMARY KEY (`e_id`),
  KEY `FK-email_e_project_id` (`e_project_id`),
  KEY `FK-email_e_language_id` (`e_language_id`),
  KEY `FK-email_e_template_type_id` (`e_template_type_id`),
  KEY `FK-email_e_created_user_id` (`e_created_user_id`),
  KEY `FK-email_e_updated_user_id` (`e_updated_user_id`),
  KEY `FK-email_e_lead_id` (`e_lead_id`),
  KEY `em_from_idx` (`e_email_from`),
  KEY `em_to_idx` (`e_email_to`),
  KEY `IND-email_e_communication_id` (`e_communication_id`),
  KEY `IND-email_e_inbox_email_id` (`e_inbox_email_id`),
  KEY `IND-email_e_case_id` (`e_case_id`),
  KEY `IND-e_is_deleted` (`e_is_deleted`),
  KEY `IND-email-e_lead_id` (`e_lead_id`),
  KEY `IND-email-e_type_id` (`e_type_id`),
  KEY `IND-email-e_project_id` (`e_project_id`),
  KEY `IND-email-e_created_dt` (`e_created_dt`),
  KEY `IND-email-e_client_id` (`e_client_id`),
  CONSTRAINT `FK-email-e_client_id` FOREIGN KEY (`e_client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `FK-email_e_case_id` FOREIGN KEY (`e_case_id`) REFERENCES `cases` (`cs_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-email_e_created_user_id` FOREIGN KEY (`e_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-email_e_language_id` FOREIGN KEY (`e_language_id`) REFERENCES `language` (`language_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-email_e_lead_id` FOREIGN KEY (`e_lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-email_e_project_id` FOREIGN KEY (`e_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-email_e_template_type_id` FOREIGN KEY (`e_template_type_id`) REFERENCES `email_template_type` (`etp_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-email_e_updated_user_id` FOREIGN KEY (`e_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=600328 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_list`
--

DROP TABLE IF EXISTS `email_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_list` (
  `el_id` int NOT NULL AUTO_INCREMENT,
  `el_email` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `el_title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `el_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `el_created_user_id` int DEFAULT NULL,
  `el_updated_user_id` int DEFAULT NULL,
  `el_created_dt` datetime DEFAULT NULL,
  `el_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`el_id`),
  UNIQUE KEY `IND-email_list-el_email` (`el_email`),
  KEY `IND-email_list-el_enabled` (`el_enabled`),
  KEY `FK-email_list-el_created_user_id` (`el_created_user_id`),
  KEY `FK-email_list-el_updated_user_id` (`el_updated_user_id`),
  CONSTRAINT `FK-email_list-el_created_user_id` FOREIGN KEY (`el_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-email_list-el_updated_user_id` FOREIGN KEY (`el_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1742 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_template_type`
--

DROP TABLE IF EXISTS `email_template_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_template_type` (
  `etp_id` int NOT NULL AUTO_INCREMENT,
  `etp_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `etp_origin_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `etp_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `etp_hidden` tinyint(1) DEFAULT '0',
  `etp_created_user_id` int DEFAULT NULL,
  `etp_updated_user_id` int DEFAULT NULL,
  `etp_created_dt` datetime DEFAULT NULL,
  `etp_updated_dt` datetime DEFAULT NULL,
  `etp_dep_id` int DEFAULT NULL,
  `etp_ignore_unsubscribe` tinyint(1) DEFAULT '0',
  `etp_params_json` json DEFAULT NULL,
  PRIMARY KEY (`etp_id`),
  UNIQUE KEY `etp_key` (`etp_key`),
  KEY `FK-email_template_type_etp_created_user_id` (`etp_created_user_id`),
  KEY `FK-email_template_type_etp_updated_user_id` (`etp_updated_user_id`),
  KEY `FK-email_template_type_etp_dep_id` (`etp_dep_id`),
  CONSTRAINT `FK-email_template_type_etp_created_user_id` FOREIGN KEY (`etp_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-email_template_type_etp_dep_id` FOREIGN KEY (`etp_dep_id`) REFERENCES `department` (`dep_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-email_template_type_etp_updated_user_id` FOREIGN KEY (`etp_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_unsubscribe`
--

DROP TABLE IF EXISTS `email_unsubscribe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_unsubscribe` (
  `eu_email` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `eu_project_id` int NOT NULL,
  `eu_created_user_id` int DEFAULT NULL,
  `eu_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`eu_email`,`eu_project_id`),
  KEY `FK-email_unsubscribe-eu_project_id_fk` (`eu_project_id`),
  CONSTRAINT `FK-email_unsubscribe-eu_project_id_fk` FOREIGN KEY (`eu_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee_acl`
--

DROP TABLE IF EXISTS `employee_acl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_acl` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int DEFAULT NULL,
  `mask` varchar(39) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk-employee_acl-employees` (`employee_id`),
  CONSTRAINT `fk-employee_acl-employees` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=262 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employee_contact_info`
--

DROP TABLE IF EXISTS `employee_contact_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_contact_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `email_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_pass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direct_line` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk-employee_contact_info-employees` (`employee_id`),
  KEY `fk-employee_contact_info-projects` (`project_id`),
  CONSTRAINT `fk-employee_contact_info-employees` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk-employee_contact_info-projects` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auth_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` smallint NOT NULL DEFAULT '10',
  `last_activity` datetime DEFAULT NULL,
  `acl_rules_activated` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=675 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employees_activity`
--

DROP TABLE IF EXISTS `employees_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees_activity` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int DEFAULT NULL,
  `user_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `request` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `request_header` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `fk-employees_activity-employee` (`employee_id`),
  CONSTRAINT `fk-employees_activity-employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight`
--

DROP TABLE IF EXISTS `flight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight` (
  `fl_id` int NOT NULL AUTO_INCREMENT,
  `fl_product_id` int DEFAULT NULL,
  `fl_trip_type_id` tinyint(1) DEFAULT NULL,
  `fl_cabin_class` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fl_adults` tinyint DEFAULT NULL,
  `fl_children` tinyint DEFAULT NULL,
  `fl_infants` tinyint DEFAULT NULL,
  `fl_request_hash_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fl_stops` tinyint(1) DEFAULT NULL,
  `fl_delayed_charge` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`fl_id`),
  KEY `FK-flight-fl_product_id` (`fl_product_id`),
  CONSTRAINT `FK-flight-fl_product_id` FOREIGN KEY (`fl_product_id`) REFERENCES `product` (`pr_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_pax`
--

DROP TABLE IF EXISTS `flight_pax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_pax` (
  `fp_id` int NOT NULL AUTO_INCREMENT,
  `fp_flight_id` int NOT NULL,
  `fp_uid` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fp_pax_id` int DEFAULT NULL,
  `fp_pax_type` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fp_first_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fp_last_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fp_middle_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fp_dob` date DEFAULT NULL,
  `fp_nationality` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fp_gender` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fp_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fp_language` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fp_citizenship` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`fp_id`),
  UNIQUE KEY `fp_uid` (`fp_uid`),
  KEY `FK-flight_pax-fp_flight_id` (`fp_flight_id`),
  KEY `FK-flight_pax-fp_language` (`fp_language`),
  CONSTRAINT `FK-flight_pax-fp_flight_id` FOREIGN KEY (`fp_flight_id`) REFERENCES `flight` (`fl_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_pax-fp_language` FOREIGN KEY (`fp_language`) REFERENCES `language` (`language_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote`
--

DROP TABLE IF EXISTS `flight_quote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote` (
  `fq_id` int NOT NULL AUTO_INCREMENT,
  `fq_flight_id` int NOT NULL,
  `fq_source_id` tinyint(1) DEFAULT NULL,
  `fq_product_quote_id` int DEFAULT NULL,
  `fq_hash_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fq_service_fee_percent` decimal(5,2) DEFAULT '3.50',
  `fq_record_locator` varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fq_gds` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fq_gds_pcc` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fq_gds_offer_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fq_type_id` tinyint(1) DEFAULT NULL,
  `fq_cabin_class` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fq_trip_type_id` tinyint(1) DEFAULT NULL,
  `fq_main_airline` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fq_fare_type_id` tinyint(1) DEFAULT NULL,
  `fq_created_user_id` int DEFAULT NULL,
  `fq_created_expert_id` int DEFAULT NULL,
  `fq_created_expert_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fq_reservation_dump` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fq_pricing_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fq_origin_search_data` json DEFAULT NULL,
  `fq_last_ticket_date` date DEFAULT NULL,
  `fq_request_hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fq_uid` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fq_json_booking` json DEFAULT NULL,
  `fq_flight_request_uid` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fq_ticket_json` json DEFAULT NULL,
  PRIMARY KEY (`fq_id`),
  KEY `FK-flight_quote-fq_product_quote_id` (`fq_product_quote_id`),
  KEY `FK-flight_quote-fq_created_user_id` (`fq_created_user_id`),
  KEY `idx-unique-fq_flight_id-fq_hash_key` (`fq_flight_id`,`fq_hash_key`),
  KEY `IND-flight_quote_fq_uid` (`fq_uid`),
  CONSTRAINT `FK-flight_quote-fq_created_user_id` FOREIGN KEY (`fq_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote-fq_flight_id` FOREIGN KEY (`fq_flight_id`) REFERENCES `flight` (`fl_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote-fq_product_quote_id` FOREIGN KEY (`fq_product_quote_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote_booking`
--

DROP TABLE IF EXISTS `flight_quote_booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote_booking` (
  `fqb_id` int NOT NULL AUTO_INCREMENT,
  `fqb_fqf_id` int NOT NULL,
  `fqb_booking_id` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqb_pnr` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqb_gds` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqb_gds_pcc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqb_validating_carrier` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqb_created_dt` datetime DEFAULT NULL,
  `fqb_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`fqb_id`),
  KEY `FK-flight_quote_booking-fqb_fqf_id` (`fqb_fqf_id`),
  CONSTRAINT `FK-flight_quote_booking-fqb_fqf_id` FOREIGN KEY (`fqb_fqf_id`) REFERENCES `flight_quote_flight` (`fqf_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote_booking_airline`
--

DROP TABLE IF EXISTS `flight_quote_booking_airline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote_booking_airline` (
  `fqba_id` int NOT NULL AUTO_INCREMENT,
  `fqba_fqb_id` int NOT NULL,
  `fqba_record_locator` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqba_airline_code` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqba_created_dt` datetime DEFAULT NULL,
  `fqba_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`fqba_id`),
  KEY `FK-flight_quote_booking_airline-fqba_fqb_id` (`fqba_fqb_id`),
  CONSTRAINT `FK-flight_quote_booking_airline-fqba_fqb_id` FOREIGN KEY (`fqba_fqb_id`) REFERENCES `flight_quote_booking` (`fqb_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote_flight`
--

DROP TABLE IF EXISTS `flight_quote_flight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote_flight` (
  `fqf_id` int NOT NULL AUTO_INCREMENT,
  `fqf_fq_id` int DEFAULT NULL,
  `fqf_trip_type_id` tinyint DEFAULT NULL,
  `fqf_main_airline` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqf_status_id` tinyint DEFAULT NULL,
  `fqf_booking_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqf_pnr` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqf_validating_carrier` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqf_original_data_json` json DEFAULT NULL,
  `fqf_created_dt` datetime DEFAULT NULL,
  `fqf_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`fqf_id`),
  KEY `FK-flight_quote_flight-fqf_fq_id` (`fqf_fq_id`),
  CONSTRAINT `FK-flight_quote_flight-fqf_fq_id` FOREIGN KEY (`fqf_fq_id`) REFERENCES `flight_quote` (`fq_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote_label`
--

DROP TABLE IF EXISTS `flight_quote_label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote_label` (
  `fql_quote_id` int NOT NULL,
  `fql_label_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`fql_quote_id`,`fql_label_key`),
  CONSTRAINT `FK-flight_quote_label-quote_id` FOREIGN KEY (`fql_quote_id`) REFERENCES `flight_quote` (`fq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote_label_list`
--

DROP TABLE IF EXISTS `flight_quote_label_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote_label_list` (
  `fqll_id` int NOT NULL AUTO_INCREMENT,
  `fqll_label_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fqll_origin_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqll_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqll_created_dt` datetime DEFAULT NULL,
  `fqll_updated_dt` datetime DEFAULT NULL,
  `fqll_created_user_id` int DEFAULT NULL,
  `fqll_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`fqll_id`),
  UNIQUE KEY `fqll_label_key` (`fqll_label_key`),
  KEY `FK-flight_quote_label-created_user_id` (`fqll_created_user_id`),
  KEY `FK-flight_quote_label-updated_user_id` (`fqll_updated_user_id`),
  CONSTRAINT `FK-flight_quote_label-created_user_id` FOREIGN KEY (`fqll_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_label-updated_user_id` FOREIGN KEY (`fqll_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote_option`
--

DROP TABLE IF EXISTS `flight_quote_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote_option` (
  `fqo_id` int NOT NULL AUTO_INCREMENT,
  `fqo_product_quote_option_id` int NOT NULL,
  `fqo_flight_pax_id` int DEFAULT NULL,
  `fqo_flight_quote_segment_id` int DEFAULT NULL,
  `fqo_flight_quote_trip_id` int DEFAULT NULL,
  `fqo_display_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqo_markup_amount` decimal(10,2) DEFAULT NULL,
  `fqo_usd_markup_amount` decimal(10,2) DEFAULT NULL,
  `fqo_base_price` decimal(10,2) DEFAULT NULL,
  `fqo_usd_base_price` decimal(10,2) DEFAULT NULL,
  `fqo_total_price` decimal(10,2) DEFAULT NULL,
  `fqo_usd_total_price` decimal(10,2) DEFAULT NULL,
  `fqo_created_dt` datetime DEFAULT NULL,
  `fqo_updated_dt` datetime DEFAULT NULL,
  `fqo_currency` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`fqo_id`),
  KEY `FK-flight_quote_option-fqo_product_quote_option_id` (`fqo_product_quote_option_id`),
  KEY `FK-flight_quote_option-fqo_flight_pax_id` (`fqo_flight_pax_id`),
  KEY `FK-flight_quote_option-fqo_flight_quote_segment_id` (`fqo_flight_quote_segment_id`),
  KEY `FK-flight_quote_option-fqo_flight_quote_trip_id` (`fqo_flight_quote_trip_id`),
  KEY `FK-flight_quote_option-fqo_currency` (`fqo_currency`),
  CONSTRAINT `FK-flight_quote_option-fqo_currency` FOREIGN KEY (`fqo_currency`) REFERENCES `currency` (`cur_code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_option-fqo_flight_pax_id` FOREIGN KEY (`fqo_flight_pax_id`) REFERENCES `flight_pax` (`fp_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_option-fqo_flight_quote_segment_id` FOREIGN KEY (`fqo_flight_quote_segment_id`) REFERENCES `flight_quote_segment` (`fqs_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_option-fqo_flight_quote_trip_id` FOREIGN KEY (`fqo_flight_quote_trip_id`) REFERENCES `flight_quote_trip` (`fqt_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_option-fqo_product_quote_option_id` FOREIGN KEY (`fqo_product_quote_option_id`) REFERENCES `product_quote_option` (`pqo_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote_pax_price`
--

DROP TABLE IF EXISTS `flight_quote_pax_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote_pax_price` (
  `qpp_id` int NOT NULL AUTO_INCREMENT,
  `qpp_flight_quote_id` int NOT NULL,
  `qpp_flight_pax_code_id` tinyint(1) NOT NULL,
  `qpp_fare` decimal(10,2) DEFAULT '0.00',
  `qpp_tax` decimal(10,2) DEFAULT '0.00',
  `qpp_system_mark_up` decimal(10,2) DEFAULT '0.00',
  `qpp_agent_mark_up` decimal(10,2) DEFAULT '0.00',
  `qpp_origin_fare` decimal(10,2) DEFAULT NULL,
  `qpp_origin_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qpp_origin_tax` decimal(10,2) DEFAULT NULL,
  `qpp_client_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qpp_client_fare` decimal(10,2) DEFAULT NULL,
  `qpp_client_tax` decimal(10,2) DEFAULT NULL,
  `qpp_created_dt` datetime DEFAULT NULL,
  `qpp_updated_dt` datetime DEFAULT NULL,
  `qpp_cnt` tinyint(1) DEFAULT NULL,
  `qpp_flight_id` int DEFAULT NULL,
  PRIMARY KEY (`qpp_id`),
  KEY `FK-flight_quote_pax_price-qpp_flight_quote_id` (`qpp_flight_quote_id`),
  KEY `FK-flight_quote_pax_price-qpp_origin_currency` (`qpp_origin_currency`),
  KEY `FK-flight_quote_pax_price-qpp_client_currency` (`qpp_client_currency`),
  KEY `FK-flight_quote_pax_price-qpp_flight_id` (`qpp_flight_id`),
  CONSTRAINT `FK-flight_quote_pax_price-qpp_client_currency` FOREIGN KEY (`qpp_client_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_pax_price-qpp_flight_id` FOREIGN KEY (`qpp_flight_id`) REFERENCES `flight_quote_flight` (`fqf_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_pax_price-qpp_flight_quote_id` FOREIGN KEY (`qpp_flight_quote_id`) REFERENCES `flight_quote` (`fq_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_pax_price-qpp_origin_currency` FOREIGN KEY (`qpp_origin_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote_segment`
--

DROP TABLE IF EXISTS `flight_quote_segment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote_segment` (
  `fqs_id` int NOT NULL AUTO_INCREMENT,
  `fqs_flight_quote_id` int NOT NULL,
  `fqs_flight_quote_trip_id` int DEFAULT NULL,
  `fqs_uid` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqs_departure_dt` datetime NOT NULL,
  `fqs_arrival_dt` datetime NOT NULL,
  `fqs_stop` tinyint(1) DEFAULT '0',
  `fqs_flight_number` smallint DEFAULT NULL,
  `fqs_booking_class` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqs_duration` smallint DEFAULT NULL,
  `fqs_departure_airport_iata` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fqs_departure_airport_terminal` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqs_arrival_airport_iata` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fqs_arrival_airport_terminal` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqs_operating_airline` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqs_marketing_airline` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqs_air_equip_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqs_marriage_group` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqs_cabin_class` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqs_cabin_class_basic` tinyint(1) DEFAULT '0',
  `fqs_meal` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqs_fare_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqs_key` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqs_ticket_id` tinyint(1) DEFAULT NULL,
  `fqs_recheck_baggage` tinyint(1) DEFAULT '0',
  `fqs_mileage` smallint DEFAULT NULL,
  `fqs_flight_id` int DEFAULT NULL,
  PRIMARY KEY (`fqs_id`),
  UNIQUE KEY `fqs_uid` (`fqs_uid`),
  KEY `FK-flight_quote_segment-fqs_flight_quote_id` (`fqs_flight_quote_id`),
  KEY `FK-flight_quote_segment-fqs_flight_quote_trip_id` (`fqs_flight_quote_trip_id`),
  KEY `FK-flight_quote_segment-fqs_flight_id` (`fqs_flight_id`),
  CONSTRAINT `FK-flight_quote_segment-fqs_flight_id` FOREIGN KEY (`fqs_flight_id`) REFERENCES `flight_quote_flight` (`fqf_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_segment-fqs_flight_quote_id` FOREIGN KEY (`fqs_flight_quote_id`) REFERENCES `flight_quote` (`fq_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_segment-fqs_flight_quote_trip_id` FOREIGN KEY (`fqs_flight_quote_trip_id`) REFERENCES `flight_quote_trip` (`fqt_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote_segment_pax_baggage`
--

DROP TABLE IF EXISTS `flight_quote_segment_pax_baggage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote_segment_pax_baggage` (
  `qsb_id` int NOT NULL AUTO_INCREMENT,
  `qsb_flight_pax_code_id` tinyint(1) NOT NULL,
  `qsb_flight_quote_segment_id` int NOT NULL,
  `qsb_airline_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsb_allow_pieces` tinyint(1) DEFAULT NULL,
  `qsb_allow_weight` tinyint DEFAULT NULL,
  `qsb_allow_unit` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsb_allow_max_weight` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsb_allow_max_size` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsb_carry_one` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`qsb_id`),
  KEY `FK-flight_quote_segment_pax_baggage-qsb_flight_quote_segment_id` (`qsb_flight_quote_segment_id`),
  CONSTRAINT `FK-flight_quote_segment_pax_baggage-qsb_flight_quote_segment_id` FOREIGN KEY (`qsb_flight_quote_segment_id`) REFERENCES `flight_quote_segment` (`fqs_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote_segment_pax_baggage_charge`
--

DROP TABLE IF EXISTS `flight_quote_segment_pax_baggage_charge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote_segment_pax_baggage_charge` (
  `qsbc_id` int NOT NULL AUTO_INCREMENT,
  `qsbc_flight_pax_code_id` tinyint(1) DEFAULT NULL,
  `qsbc_flight_quote_segment_id` int NOT NULL,
  `qsbc_first_piece` tinyint(1) DEFAULT NULL,
  `qsbc_last_piece` tinyint(1) DEFAULT NULL,
  `qsbc_origin_price` decimal(10,2) DEFAULT NULL,
  `qsbc_origin_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsbc_price` decimal(10,2) DEFAULT NULL,
  `qsbc_client_price` decimal(10,2) DEFAULT NULL,
  `qsbc_client_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsbc_max_weight` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsbc_max_size` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`qsbc_id`),
  KEY `FK-flight_quote_segment_pax_baggage_charge-qsbc_flight_pax_id` (`qsbc_flight_pax_code_id`),
  KEY `FK-flight_quote_segment_pax_baggage_charge-quote_segment_id` (`qsbc_flight_quote_segment_id`),
  KEY `FK-flight_quote_segment_pax_baggage_charge-qsbc_client_currency` (`qsbc_client_currency`),
  KEY `FK-flight_quote_segment_pax_baggage_charge-qsbc_origin_currency` (`qsbc_origin_currency`),
  CONSTRAINT `FK-flight_quote_segment_pax_baggage_charge-qsbc_client_currency` FOREIGN KEY (`qsbc_client_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_segment_pax_baggage_charge-qsbc_origin_currency` FOREIGN KEY (`qsbc_origin_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_segment_pax_baggage_charge-quote_segment_id` FOREIGN KEY (`qsbc_flight_quote_segment_id`) REFERENCES `flight_quote_segment` (`fqs_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote_segment_stop`
--

DROP TABLE IF EXISTS `flight_quote_segment_stop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote_segment_stop` (
  `qss_id` int NOT NULL AUTO_INCREMENT,
  `qss_quote_segment_id` int NOT NULL,
  `qss_location_iata` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qss_equipment` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qss_elapsed_time` int DEFAULT NULL,
  `qss_duration` int DEFAULT NULL,
  `qss_departure_dt` datetime DEFAULT NULL,
  `qss_arrival_dt` datetime DEFAULT NULL,
  `qss_flight_id` int DEFAULT NULL,
  PRIMARY KEY (`qss_id`),
  KEY `FK-flight_quote_segment_stop-qss_quote_segment_id` (`qss_quote_segment_id`),
  KEY `FK-flight_quote_segment_stop-qss_flight_id` (`qss_flight_id`),
  CONSTRAINT `FK-flight_quote_segment_stop-qss_flight_id` FOREIGN KEY (`qss_flight_id`) REFERENCES `flight_quote_flight` (`fqf_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_segment_stop-qss_quote_segment_id` FOREIGN KEY (`qss_quote_segment_id`) REFERENCES `flight_quote_segment` (`fqs_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote_status_log`
--

DROP TABLE IF EXISTS `flight_quote_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote_status_log` (
  `qsl_id` int NOT NULL AUTO_INCREMENT,
  `qsl_created_user_id` int DEFAULT NULL,
  `qsl_flight_quote_id` int NOT NULL,
  `qsl_status_id` tinyint(1) DEFAULT NULL,
  `qsl_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`qsl_id`),
  KEY `FK-flight_quote_status_log-qsl_created_user_id` (`qsl_created_user_id`),
  KEY `IND-flight_quote_status_log-qsl_flight_quote_id` (`qsl_flight_quote_id`,`qsl_status_id`),
  CONSTRAINT `FK-flight_quote_status_log-qsl_created_user_id` FOREIGN KEY (`qsl_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_status_log-qsl_flight_quote_id` FOREIGN KEY (`qsl_flight_quote_id`) REFERENCES `flight_quote` (`fq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote_ticket`
--

DROP TABLE IF EXISTS `flight_quote_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote_ticket` (
  `fqt_pax_id` int NOT NULL,
  `fqt_ticket_number` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqt_created_dt` datetime DEFAULT NULL,
  `fqt_updated_dt` datetime DEFAULT NULL,
  `fqt_fqb_id` int NOT NULL,
  PRIMARY KEY (`fqt_pax_id`,`fqt_fqb_id`),
  KEY `FK-flight_quote_ticket-fqt_fqb_id` (`fqt_fqb_id`),
  CONSTRAINT `FK-flight_quote_ticket-fqt_fqb_id` FOREIGN KEY (`fqt_fqb_id`) REFERENCES `flight_quote_booking` (`fqb_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_ticket-fqt_pax_id` FOREIGN KEY (`fqt_pax_id`) REFERENCES `flight_pax` (`fp_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_quote_trip`
--

DROP TABLE IF EXISTS `flight_quote_trip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_quote_trip` (
  `fqt_id` int NOT NULL AUTO_INCREMENT,
  `fqt_uid` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqt_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fqt_flight_quote_id` int NOT NULL,
  `fqt_duration` int DEFAULT NULL,
  `fqt_flight_id` int DEFAULT NULL,
  PRIMARY KEY (`fqt_id`),
  UNIQUE KEY `fqt_uid` (`fqt_uid`),
  KEY `FK-flight_quote_trip-fqt_flight_quote_id` (`fqt_flight_quote_id`),
  KEY `FK-flight_quote_trip-fqp_flight_id` (`fqt_flight_id`),
  CONSTRAINT `FK-flight_quote_trip-fqp_flight_id` FOREIGN KEY (`fqt_flight_id`) REFERENCES `flight_quote_flight` (`fqf_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-flight_quote_trip-fqt_flight_quote_id` FOREIGN KEY (`fqt_flight_quote_id`) REFERENCES `flight_quote` (`fq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_request`
--

DROP TABLE IF EXISTS `flight_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_request` (
  `fr_id` int NOT NULL AUTO_INCREMENT,
  `fr_booking_id` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fr_hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fr_type_id` tinyint NOT NULL,
  `fr_data_json` json DEFAULT NULL,
  `fr_created_api_user_id` int DEFAULT NULL,
  `fr_status_id` tinyint DEFAULT NULL,
  `fr_job_id` int DEFAULT NULL,
  `fr_created_dt` datetime NOT NULL,
  `fr_updated_dt` datetime DEFAULT NULL,
  `fr_year` smallint NOT NULL,
  `fr_month` tinyint NOT NULL,
  `fr_project_id` int DEFAULT NULL,
  PRIMARY KEY (`fr_id`,`fr_year`,`fr_month`),
  KEY `IND-flight_request-fr_hash` (`fr_hash`),
  KEY `IND-flight_request-fr_type_id` (`fr_type_id`),
  KEY `IND-flight_request-fr_created_api_user_id` (`fr_created_api_user_id`),
  KEY `IND-flight_request-fr_status_id` (`fr_status_id`),
  KEY `IND-flight_request-fr_created_dt` (`fr_created_dt`),
  KEY `IND-flight_request-fr_year` (`fr_year`),
  KEY `IND-flight_request-fr_month` (`fr_month`),
  KEY `IND-flight_request-fr_booking_id` (`fr_booking_id`),
  KEY `IND-flight_request-fr_project_id` (`fr_project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
/*!50100 PARTITION BY RANGE (`fr_year`)
SUBPARTITION BY LINEAR HASH (`fr_month`)
SUBPARTITIONS 12
(PARTITION y21 VALUES LESS THAN (2021) ENGINE = InnoDB,
 PARTITION y22 VALUES LESS THAN (2022) ENGINE = InnoDB,
 PARTITION y23 VALUES LESS THAN (2023) ENGINE = InnoDB,
 PARTITION y24 VALUES LESS THAN (2024) ENGINE = InnoDB,
 PARTITION y25 VALUES LESS THAN (2025) ENGINE = InnoDB,
 PARTITION y26 VALUES LESS THAN (2026) ENGINE = InnoDB,
 PARTITION y27 VALUES LESS THAN (2027) ENGINE = InnoDB,
 PARTITION y28 VALUES LESS THAN (2028) ENGINE = InnoDB,
 PARTITION y29 VALUES LESS THAN (2029) ENGINE = InnoDB,
 PARTITION y30 VALUES LESS THAN (2030) ENGINE = InnoDB,
 PARTITION y VALUES LESS THAN MAXVALUE ENGINE = InnoDB) */;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_request_log`
--

DROP TABLE IF EXISTS `flight_request_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_request_log` (
  `flr_id` int NOT NULL AUTO_INCREMENT,
  `flr_fr_id` int DEFAULT NULL,
  `flr_status_id_old` tinyint DEFAULT NULL,
  `flr_status_id_new` tinyint DEFAULT NULL,
  `flr_description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `flr_created_dt` datetime DEFAULT NULL,
  `flr_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`flr_id`),
  KEY `IND-flight_request_log-flr_fr_id` (`flr_fr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flight_segment`
--

DROP TABLE IF EXISTS `flight_segment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flight_segment` (
  `fs_id` int NOT NULL AUTO_INCREMENT,
  `fs_flight_id` int NOT NULL,
  `fs_origin_iata` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fs_destination_iata` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fs_departure_date` date NOT NULL,
  `fs_flex_type_id` tinyint(1) DEFAULT NULL,
  `fs_flex_days` tinyint(1) DEFAULT NULL,
  `fs_origin_iata_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fs_destination_iata_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`fs_id`),
  KEY `FK-flight_segment-fs_flight_id` (`fs_flight_id`),
  CONSTRAINT `FK-flight_segment-fs_flight_id` FOREIGN KEY (`fs_flight_id`) REFERENCES `flight` (`fl_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `global_acl`
--

DROP TABLE IF EXISTS `global_acl`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `global_acl` (
  `id` int NOT NULL AUTO_INCREMENT,
  `mask` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `global_log`
--

DROP TABLE IF EXISTS `global_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `global_log` (
  `gl_id` bigint NOT NULL AUTO_INCREMENT,
  `gl_app_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gl_app_user_id` int DEFAULT NULL,
  `gl_model` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `gl_obj_id` int NOT NULL,
  `gl_old_attr` json DEFAULT NULL,
  `gl_new_attr` json DEFAULT NULL,
  `gl_formatted_attr` json DEFAULT NULL,
  `gl_action_type` smallint DEFAULT NULL,
  `gl_created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`gl_id`),
  KEY `idx_gl_model_obj` (`gl_model`,`gl_obj_id`),
  KEY `idx_gl_created_at` (`gl_created_at`),
  KEY `idx_app_id_user_id` (`gl_app_id`,`gl_app_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hotel`
--

DROP TABLE IF EXISTS `hotel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hotel` (
  `ph_id` int NOT NULL AUTO_INCREMENT,
  `ph_product_id` int DEFAULT NULL,
  `ph_check_in_date` date DEFAULT NULL,
  `ph_check_out_date` date DEFAULT NULL,
  `ph_zone_code` int DEFAULT NULL,
  `ph_hotel_code` int DEFAULT NULL,
  `ph_destination_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ph_destination_label` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ph_min_star_rate` tinyint DEFAULT NULL,
  `ph_max_star_rate` tinyint DEFAULT NULL,
  `ph_max_price_rate` decimal(10,2) DEFAULT NULL,
  `ph_min_price_rate` decimal(10,2) DEFAULT NULL,
  `ph_request_hash_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ph_holder_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ph_holder_surname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ph_id`),
  KEY `FK-hotel-ph_product_id` (`ph_product_id`),
  CONSTRAINT `FK-hotel-ph_product_id` FOREIGN KEY (`ph_product_id`) REFERENCES `product` (`pr_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hotel_list`
--

DROP TABLE IF EXISTS `hotel_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hotel_list` (
  `hl_id` int NOT NULL AUTO_INCREMENT,
  `hl_code` int DEFAULT NULL,
  `hl_hash_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hl_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hl_star` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hl_category_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hl_destination_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hl_destination_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hl_zone_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hl_zone_code` smallint DEFAULT NULL,
  `hl_country_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hl_state_code` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hl_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hl_address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hl_postal_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hl_city` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hl_email` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hl_web` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hl_phone_list` json DEFAULT NULL,
  `hl_image_list` json DEFAULT NULL,
  `hl_image_base_url` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hl_board_codes` json DEFAULT NULL,
  `hl_segment_codes` json DEFAULT NULL,
  `hl_latitude` decimal(10,7) DEFAULT NULL,
  `hl_longitude` decimal(10,7) DEFAULT NULL,
  `hl_ranking` smallint DEFAULT NULL,
  `hl_service_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hl_last_update` date DEFAULT NULL,
  `hl_created_dt` datetime DEFAULT NULL,
  `hl_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`hl_id`),
  UNIQUE KEY `hl_code` (`hl_code`),
  UNIQUE KEY `hl_hash_key` (`hl_hash_key`),
  UNIQUE KEY `IND-hotel_list-hl_code` (`hl_code`),
  UNIQUE KEY `IND-hotel_list-hl_hash_key` (`hl_hash_key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hotel_quote`
--

DROP TABLE IF EXISTS `hotel_quote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hotel_quote` (
  `hq_id` int NOT NULL AUTO_INCREMENT,
  `hq_hotel_id` int NOT NULL,
  `hq_hash_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hq_product_quote_id` int DEFAULT NULL,
  `hq_destination_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hq_hotel_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hq_hotel_list_id` int DEFAULT NULL,
  `hq_request_hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hq_booking_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hq_json_booking` json DEFAULT NULL,
  `hq_origin_search_data` json DEFAULT NULL,
  `hq_check_in_date` date DEFAULT NULL,
  `hq_check_out_date` date DEFAULT NULL,
  PRIMARY KEY (`hq_id`),
  KEY `FK-hotel_quote-hq_hotel_id` (`hq_hotel_id`),
  KEY `FK-hotel_quote-hq_product_quote_id` (`hq_product_quote_id`),
  KEY `FK-hotel_quote-hq_hotel_list_id` (`hq_hotel_list_id`),
  KEY `UNIQUE-hq_hotel_id-hq_hash_key` (`hq_hotel_id`,`hq_hash_key`),
  CONSTRAINT `FK-hotel_quote-hq_hotel_id` FOREIGN KEY (`hq_hotel_id`) REFERENCES `hotel` (`ph_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-hotel_quote-hq_hotel_list_id` FOREIGN KEY (`hq_hotel_list_id`) REFERENCES `hotel_list` (`hl_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-hotel_quote-hq_product_quote_id` FOREIGN KEY (`hq_product_quote_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hotel_quote_room`
--

DROP TABLE IF EXISTS `hotel_quote_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hotel_quote_room` (
  `hqr_id` int NOT NULL AUTO_INCREMENT,
  `hqr_hotel_quote_id` int NOT NULL,
  `hqr_room_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hqr_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hqr_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hqr_class` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hqr_amount` decimal(10,2) DEFAULT NULL,
  `hqr_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hqr_payment_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hqr_board_code` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hqr_board_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hqr_rooms` tinyint DEFAULT NULL,
  `hqr_adults` tinyint DEFAULT NULL,
  `hqr_children` tinyint DEFAULT NULL,
  `hqr_children_ages` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hqr_rate_comments_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hqr_rate_comments` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hqr_type` tinyint NOT NULL DEFAULT '0',
  `hqr_service_fee_percent` decimal(5,2) DEFAULT '3.50',
  `hqr_system_mark_up` decimal(10,2) DEFAULT NULL,
  `hqr_agent_mark_up` decimal(10,2) DEFAULT NULL,
  `hqr_cancellation_policies` json DEFAULT NULL,
  PRIMARY KEY (`hqr_id`),
  KEY `FK-hotel_quote_room-hqr_hotel_quote_id` (`hqr_hotel_quote_id`),
  KEY `FK-hotel_quote_room-hqr_currency` (`hqr_currency`),
  KEY `IDX-hotel_quote_room-hqr_type` (`hqr_type`),
  CONSTRAINT `FK-hotel_quote_room-hqr_currency` FOREIGN KEY (`hqr_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-hotel_quote_room-hqr_hotel_quote_id` FOREIGN KEY (`hqr_hotel_quote_id`) REFERENCES `hotel_quote` (`hq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hotel_quote_room_pax`
--

DROP TABLE IF EXISTS `hotel_quote_room_pax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hotel_quote_room_pax` (
  `hqrp_id` int NOT NULL AUTO_INCREMENT,
  `hqrp_hotel_quote_room_id` int NOT NULL,
  `hqrp_type_id` tinyint NOT NULL,
  `hqrp_age` tinyint DEFAULT NULL,
  `hqrp_first_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hqrp_last_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hqrp_dob` date DEFAULT NULL,
  PRIMARY KEY (`hqrp_id`),
  KEY `FK-hotel_quote_room_pax-hqrp_hotel_quote_room_id` (`hqrp_hotel_quote_room_id`),
  KEY `IDX-hotel_quote_room_pax-hqrp_type_id` (`hqrp_type_id`),
  CONSTRAINT `FK-hotel_quote_room_pax-hqrp_hotel_quote_room_id` FOREIGN KEY (`hqrp_hotel_quote_room_id`) REFERENCES `hotel_quote_room` (`hqr_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hotel_quote_service_log`
--

DROP TABLE IF EXISTS `hotel_quote_service_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hotel_quote_service_log` (
  `hqsl_id` int NOT NULL AUTO_INCREMENT,
  `hqsl_hotel_quote_id` int NOT NULL,
  `hqsl_action_type_id` int NOT NULL,
  `hqsl_status_id` int NOT NULL DEFAULT '1',
  `hqsl_message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `hqsl_created_user_id` int DEFAULT NULL,
  `hqsl_created_dt` datetime DEFAULT NULL,
  `hqsl_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`hqsl_id`),
  KEY `FK-hotel-quote-service-log_hotel-quote` (`hqsl_hotel_quote_id`),
  KEY `FK-hotel-quote-service-log_employees` (`hqsl_created_user_id`),
  CONSTRAINT `FK-hotel-quote-service-log_employees` FOREIGN KEY (`hqsl_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-hotel-quote-service-log_hotel-quote` FOREIGN KEY (`hqsl_hotel_quote_id`) REFERENCES `hotel_quote` (`hq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hotel_room`
--

DROP TABLE IF EXISTS `hotel_room`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hotel_room` (
  `hr_id` int NOT NULL AUTO_INCREMENT,
  `hr_hotel_id` int NOT NULL,
  `hr_room_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`hr_id`),
  KEY `FK-hotel_room-hr_hotel_id` (`hr_hotel_id`),
  CONSTRAINT `FK-hotel_room-hr_hotel_id` FOREIGN KEY (`hr_hotel_id`) REFERENCES `hotel` (`ph_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hotel_room_pax`
--

DROP TABLE IF EXISTS `hotel_room_pax`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hotel_room_pax` (
  `hrp_id` int NOT NULL AUTO_INCREMENT,
  `hrp_hotel_room_id` int NOT NULL,
  `hrp_type_id` tinyint NOT NULL,
  `hrp_age` tinyint unsigned DEFAULT NULL,
  `hrp_first_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hrp_last_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hrp_dob` date DEFAULT NULL,
  PRIMARY KEY (`hrp_id`),
  KEY `FK-hotel_room_pax-hrp_hotel_room_id` (`hrp_hotel_room_id`),
  KEY `IND-hotel_room_pax-hrp_type_id` (`hrp_type_id`),
  CONSTRAINT `FK-hotel_room_pax-hrp_hotel_room_id` FOREIGN KEY (`hrp_hotel_room_id`) REFERENCES `hotel_room` (`hr_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice`
--

DROP TABLE IF EXISTS `invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice` (
  `inv_id` int NOT NULL AUTO_INCREMENT,
  `inv_gid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `inv_uid` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inv_order_id` int NOT NULL,
  `inv_status_id` tinyint DEFAULT NULL,
  `inv_sum` decimal(8,2) NOT NULL,
  `inv_client_sum` decimal(8,2) NOT NULL,
  `inv_client_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inv_currency_rate` decimal(8,5) DEFAULT NULL,
  `inv_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `inv_created_user_id` int DEFAULT NULL,
  `inv_updated_user_id` int DEFAULT NULL,
  `inv_created_dt` datetime DEFAULT NULL,
  `inv_updated_dt` datetime DEFAULT NULL,
  `inv_billing_id` int DEFAULT NULL,
  PRIMARY KEY (`inv_id`),
  UNIQUE KEY `inv_gid` (`inv_gid`),
  UNIQUE KEY `IND-invoice-inv_gid` (`inv_gid`),
  UNIQUE KEY `inv_uid` (`inv_uid`),
  KEY `FK-invoice-inv_lead_id` (`inv_order_id`),
  KEY `FK-invoice-inv_client_currency` (`inv_client_currency`),
  KEY `FK-invoice-inv_created_user_id` (`inv_created_user_id`),
  KEY `FK-invoice-inv_updated_user_id` (`inv_updated_user_id`),
  KEY `IND-invoice-inv_status_id` (`inv_status_id`),
  KEY `FK-invoice-inv_billing_id` (`inv_billing_id`),
  CONSTRAINT `FK-invoice-inv_billing_id` FOREIGN KEY (`inv_billing_id`) REFERENCES `billing_info` (`bi_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-invoice-inv_client_currency` FOREIGN KEY (`inv_client_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-invoice-inv_created_user_id` FOREIGN KEY (`inv_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-invoice-inv_lead_id` FOREIGN KEY (`inv_order_id`) REFERENCES `order` (`or_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-invoice-inv_updated_user_id` FOREIGN KEY (`inv_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `invoice_status_log`
--

DROP TABLE IF EXISTS `invoice_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_status_log` (
  `invsl_id` int NOT NULL AUTO_INCREMENT,
  `invsl_invoice_id` int NOT NULL,
  `invsl_start_status_id` tinyint DEFAULT NULL,
  `invsl_end_status_id` tinyint NOT NULL,
  `invsl_start_dt` datetime NOT NULL,
  `invsl_end_dt` datetime DEFAULT NULL,
  `invsl_duration` int DEFAULT NULL,
  `invsl_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invsl_action_id` tinyint DEFAULT NULL,
  `invsl_created_user_id` int DEFAULT NULL,
  PRIMARY KEY (`invsl_id`),
  KEY `FK-invoice_status_log_invsl_invoice_id` (`invsl_invoice_id`),
  KEY `FK-invoice_status_log_invsl_created_user_id` (`invsl_created_user_id`),
  CONSTRAINT `FK-invoice_status_log_invsl_created_user_id` FOREIGN KEY (`invsl_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-invoice_status_log_invsl_invoice_id` FOREIGN KEY (`invsl_invoice_id`) REFERENCES `invoice` (`inv_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kpi_history`
--

DROP TABLE IF EXISTS `kpi_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kpi_history` (
  `kh_id` int NOT NULL AUTO_INCREMENT,
  `kh_user_id` int NOT NULL,
  `kh_date_dt` date DEFAULT NULL,
  `kh_created_dt` datetime DEFAULT NULL,
  `kh_updated_dt` datetime DEFAULT NULL,
  `kh_agent_approved_dt` datetime DEFAULT NULL,
  `kh_super_approved_dt` datetime DEFAULT NULL,
  `kh_super_id` int DEFAULT NULL,
  `kh_base_amount` decimal(10,2) DEFAULT '0.00',
  `kh_bonus_active` tinyint(1) DEFAULT '0',
  `kh_commission_percent` int DEFAULT '0',
  `kh_profit_bonus` decimal(10,2) DEFAULT '0.00',
  `kh_manual_bonus` decimal(10,2) DEFAULT '0.00',
  `kh_estimation_profit` decimal(10,2) DEFAULT '0.00',
  `kh_description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`kh_id`),
  KEY `fk-kh-user` (`kh_user_id`),
  KEY `fk-kh-super` (`kh_super_id`),
  CONSTRAINT `fk-kh-super` FOREIGN KEY (`kh_super_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-kh-user` FOREIGN KEY (`kh_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7969 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kpi_product_commission`
--

DROP TABLE IF EXISTS `kpi_product_commission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kpi_product_commission` (
  `pc_product_type_id` int NOT NULL,
  `pc_performance` int NOT NULL,
  `pc_commission_percent` tinyint NOT NULL,
  `pc_created_user_id` int DEFAULT NULL,
  `pc_updated_user_id` int DEFAULT NULL,
  `pc_created_dt` datetime DEFAULT NULL,
  `pc_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`pc_product_type_id`,`pc_performance`,`pc_commission_percent`),
  KEY `FK-kpi_product_commission-pc_created_user_id` (`pc_created_user_id`),
  KEY `FK-kpi_product_commission-pc_updated_user_id` (`pc_updated_user_id`),
  CONSTRAINT `FK-kpi_product_commission-pc_created_user_id` FOREIGN KEY (`pc_created_user_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `FK-kpi_product_commission-pc_product_type_id` FOREIGN KEY (`pc_product_type_id`) REFERENCES `product_type` (`pt_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-kpi_product_commission-pc_updated_user_id` FOREIGN KEY (`pc_updated_user_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kpi_user_performance`
--

DROP TABLE IF EXISTS `kpi_user_performance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kpi_user_performance` (
  `up_user_id` int NOT NULL,
  `up_year` smallint NOT NULL,
  `up_month` tinyint NOT NULL,
  `up_performance` smallint DEFAULT NULL,
  `up_created_user_id` int DEFAULT NULL,
  `up_updated_user_id` int DEFAULT NULL,
  `up_created_dt` datetime DEFAULT NULL,
  `up_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`up_user_id`,`up_year`,`up_month`),
  KEY `FK-kpi_user_performance-up_created_user_id` (`up_created_user_id`),
  KEY `FK-kpi_user_performance-up_updated_user_id` (`up_updated_user_id`),
  CONSTRAINT `FK-kpi_user_performance-up_created_user_id` FOREIGN KEY (`up_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-kpi_user_performance-up_updated_user_id` FOREIGN KEY (`up_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-kpi_user_performance-up_user_id` FOREIGN KEY (`up_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `kpi_user_product_commission`
--

DROP TABLE IF EXISTS `kpi_user_product_commission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kpi_user_product_commission` (
  `upc_product_type_id` int NOT NULL,
  `upc_user_id` int NOT NULL,
  `upc_year` smallint NOT NULL,
  `upc_month` tinyint NOT NULL,
  `upc_performance` smallint DEFAULT NULL,
  `upc_commission_percent` smallint DEFAULT NULL,
  `upc_created_user_id` int DEFAULT NULL,
  `upc_updated_user_id` int DEFAULT NULL,
  `upc_created_dt` datetime DEFAULT NULL,
  `upc_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`upc_product_type_id`,`upc_user_id`,`upc_year`,`upc_month`),
  KEY `FK-kpi_user_product_commission-upc_user_id` (`upc_user_id`),
  KEY `FK-kpi_user_product_commission-upc_created_user_id` (`upc_created_user_id`),
  KEY `FK-kpi_user_product_commission-upc_updated_user_id` (`upc_updated_user_id`),
  CONSTRAINT `FK-kpi_user_product_commission-product_type_id` FOREIGN KEY (`upc_product_type_id`) REFERENCES `product_type` (`pt_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-kpi_user_product_commission-upc_created_user_id` FOREIGN KEY (`upc_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-kpi_user_product_commission-upc_updated_user_id` FOREIGN KEY (`upc_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-kpi_user_product_commission-upc_user_id` FOREIGN KEY (`upc_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `language` (
  `language_id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_ascii` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` smallint NOT NULL,
  PRIMARY KEY (`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `language_source`
--

DROP TABLE IF EXISTS `language_source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `language_source` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=577 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `language_translate`
--

DROP TABLE IF EXISTS `language_translate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `language_translate` (
  `id` int NOT NULL,
  `language` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `translation` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`,`language`),
  KEY `language_translate_idx_language` (`language`),
  CONSTRAINT `language_translate_ibfk_1` FOREIGN KEY (`language`) REFERENCES `language` (`language_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `language_translate_ibfk_2` FOREIGN KEY (`id`) REFERENCES `language_source` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_call_expert`
--

DROP TABLE IF EXISTS `lead_call_expert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_call_expert` (
  `lce_id` int NOT NULL AUTO_INCREMENT,
  `lce_lead_id` int NOT NULL,
  `lce_request_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lce_request_dt` datetime DEFAULT NULL,
  `lce_response_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `lce_response_lead_quotes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `lce_response_dt` datetime DEFAULT NULL,
  `lce_status_id` smallint DEFAULT NULL,
  `lce_agent_user_id` int DEFAULT NULL,
  `lce_expert_user_id` int DEFAULT NULL,
  `lce_expert_username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lce_updated_dt` datetime DEFAULT NULL,
  `lce_product_id` int DEFAULT NULL,
  PRIMARY KEY (`lce_id`),
  KEY `FK-lead_call_expert_lce_lead_id` (`lce_lead_id`),
  KEY `FK-lead_call_expert_lce_agent_user_id` (`lce_agent_user_id`),
  KEY `FK-lead_call_expert-lce_product_id` (`lce_product_id`),
  CONSTRAINT `FK-lead_call_expert-lce_product_id` FOREIGN KEY (`lce_product_id`) REFERENCES `product` (`pr_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-lead_call_expert_lce_agent_user_id` FOREIGN KEY (`lce_agent_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-lead_call_expert_lce_lead_id` FOREIGN KEY (`lce_lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=84278 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_checklist`
--

DROP TABLE IF EXISTS `lead_checklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_checklist` (
  `lc_type_id` int NOT NULL,
  `lc_lead_id` int NOT NULL,
  `lc_user_id` int NOT NULL,
  `lc_notes` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lc_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`lc_type_id`,`lc_lead_id`,`lc_user_id`),
  KEY `FK-lead_checklist_lc_user_id` (`lc_user_id`),
  KEY `FK-lead_checklist_lc_lead_id` (`lc_lead_id`),
  CONSTRAINT `FK-lead_checklist_lc_lct_id` FOREIGN KEY (`lc_type_id`) REFERENCES `lead_checklist_type` (`lct_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-lead_checklist_lc_lead_id` FOREIGN KEY (`lc_lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-lead_checklist_lc_user_id` FOREIGN KEY (`lc_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_checklist_type`
--

DROP TABLE IF EXISTS `lead_checklist_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_checklist_type` (
  `lct_id` int NOT NULL AUTO_INCREMENT,
  `lct_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lct_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lct_description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lct_enabled` tinyint(1) DEFAULT '1',
  `lct_sort_order` int DEFAULT '5',
  `lct_updated_dt` datetime DEFAULT NULL,
  `lct_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`lct_id`),
  UNIQUE KEY `lct_key` (`lct_key`),
  KEY `FK-lead_checklist_type_lct_updated_user_id` (`lct_updated_user_id`),
  CONSTRAINT `FK-lead_checklist_type_lct_updated_user_id` FOREIGN KEY (`lct_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_data`
--

DROP TABLE IF EXISTS `lead_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_data` (
  `ld_id` int NOT NULL AUTO_INCREMENT,
  `ld_lead_id` int DEFAULT NULL,
  `ld_field_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ld_field_value` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ld_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ld_id`),
  UNIQUE KEY `IND-lead_data-lead_id-field_key` (`ld_lead_id`,`ld_field_key`),
  KEY `IND-lead_data-field_key-field_value` (`ld_field_key`,`ld_field_value`),
  KEY `IND-lead_data-lead_id` (`ld_lead_id`),
  CONSTRAINT `FK-lead_data-lead` FOREIGN KEY (`ld_lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_data_key`
--

DROP TABLE IF EXISTS `lead_data_key`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_data_key` (
  `ldk_id` int NOT NULL AUTO_INCREMENT,
  `ldk_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ldk_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ldk_enable` tinyint(1) DEFAULT '1',
  `ldk_created_dt` datetime DEFAULT NULL,
  `ldk_updated_dt` datetime DEFAULT NULL,
  `ldk_created_user_id` int DEFAULT NULL,
  `ldk_updated_user_id` int DEFAULT NULL,
  `ldk_is_system` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ldk_id`),
  UNIQUE KEY `ldk_key` (`ldk_key`),
  KEY `IND-lead_data_key-ldk_enable` (`ldk_enable`),
  KEY `FK-lead_data_key-created_user_id` (`ldk_created_user_id`),
  KEY `FK-lead_data_key-updated_user_id` (`ldk_updated_user_id`),
  CONSTRAINT `FK-lead_data_key-created_user_id` FOREIGN KEY (`ldk_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-lead_data_key-updated_user_id` FOREIGN KEY (`ldk_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_flight_segments`
--

DROP TABLE IF EXISTS `lead_flight_segments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_flight_segments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lead_id` int DEFAULT NULL,
  `origin` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `departure` date NOT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT NULL,
  `flexibility` int DEFAULT NULL,
  `flexibility_type` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `origin_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination_label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk-leadfs-lead` (`lead_id`),
  KEY `IND-lead_flight_segments-destination` (`destination`),
  KEY `IND-lead_flight_segments-origin` (`origin`),
  CONSTRAINT `fk-leadfs-lead` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=879801 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_flow`
--

DROP TABLE IF EXISTS `lead_flow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_flow` (
  `id` int NOT NULL AUTO_INCREMENT,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `employee_id` int DEFAULT NULL,
  `lf_owner_id` int DEFAULT NULL,
  `lead_id` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `lf_from_status_id` int DEFAULT NULL,
  `lf_end_dt` datetime DEFAULT NULL,
  `lf_time_duration` int DEFAULT NULL,
  `lf_description` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lf_out_calls` smallint DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk-lead_flow-employee` (`employee_id`),
  KEY `fk-lead_flow-lead` (`lead_id`),
  KEY `lead_flow_index` (`employee_id`,`status`,`created`),
  KEY `fk-lead_flow-owner` (`lf_owner_id`),
  KEY `ind-lead_flow_status` (`lf_owner_id`,`lf_from_status_id`,`status`),
  KEY `ind-lead_flow_status_lf_from_status_id` (`lf_owner_id`,`lf_from_status_id`,`status`),
  KEY `IND-lead_flow-status` (`status`),
  CONSTRAINT `fk-lead_flow-employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk-lead_flow-lead` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5238172 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_flow_checklist`
--

DROP TABLE IF EXISTS `lead_flow_checklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_flow_checklist` (
  `lfc_lf_id` int NOT NULL,
  `lfc_lc_type_id` int NOT NULL,
  `lfc_lc_user_id` int DEFAULT NULL,
  PRIMARY KEY (`lfc_lf_id`,`lfc_lc_type_id`),
  KEY `FK-lead_flow_checklist_lfc_lc_user_id` (`lfc_lc_user_id`),
  CONSTRAINT `FK-lead_flow_checklist_lfc_lc_user_id` FOREIGN KEY (`lfc_lc_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-lead_flow_checklist_lfc_lf_id` FOREIGN KEY (`lfc_lf_id`) REFERENCES `lead_flow` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_order`
--

DROP TABLE IF EXISTS `lead_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_order` (
  `lo_order_id` int NOT NULL,
  `lo_lead_id` int NOT NULL,
  `lo_create_dt` datetime DEFAULT NULL,
  `lo_created_user_id` int DEFAULT NULL,
  PRIMARY KEY (`lo_order_id`,`lo_lead_id`),
  KEY `PK-lead_order-lo_lead_id` (`lo_lead_id`),
  KEY `PK-lead_order-lo_created_user_id` (`lo_created_user_id`),
  CONSTRAINT `PK-lead_order-lo_created_user_id` FOREIGN KEY (`lo_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `PK-lead_order-lo_lead_id` FOREIGN KEY (`lo_lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `PK-lead_order-lo_order_id` FOREIGN KEY (`lo_order_id`) REFERENCES `order` (`or_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_preferences`
--

DROP TABLE IF EXISTS `lead_preferences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_preferences` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lead_id` int DEFAULT NULL,
  `notes` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pref_language` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pref_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pref_airline` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `number_stops` int DEFAULT NULL,
  `clients_budget` float DEFAULT NULL,
  `market_price` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk-leadpref-lead` (`lead_id`),
  KEY `FK-lead_preferences-pref_currency` (`pref_currency`),
  CONSTRAINT `FK-lead_preferences-pref_currency` FOREIGN KEY (`pref_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-leadpref-lead` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=179460 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_product`
--

DROP TABLE IF EXISTS `lead_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_product` (
  `lp_lead_id` int NOT NULL,
  `lp_product_id` int NOT NULL,
  PRIMARY KEY (`lp_lead_id`,`lp_product_id`),
  KEY `FK-lead_product-lp_product_id` (`lp_product_id`),
  CONSTRAINT `FK-lead_product-lp_lead_id` FOREIGN KEY (`lp_lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-lead_product-lp_product_id` FOREIGN KEY (`lp_product_id`) REFERENCES `product` (`pr_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_profit_type`
--

DROP TABLE IF EXISTS `lead_profit_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_profit_type` (
  `lpt_profit_type_id` smallint NOT NULL,
  `lpt_diff_rule` tinyint DEFAULT NULL,
  `lpt_commission_min` tinyint DEFAULT NULL,
  `lpt_commission_max` tinyint DEFAULT NULL,
  `lpt_commission_fix` tinyint DEFAULT NULL,
  `lpt_created_user_id` int DEFAULT NULL,
  `lpt_updated_user_id` int DEFAULT NULL,
  `lpt_created_dt` datetime DEFAULT NULL,
  `lpt_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`lpt_profit_type_id`),
  KEY `FK-lead_profit_type-create_user` (`lpt_created_user_id`),
  KEY `FK-lead_profit_type-update_user` (`lpt_updated_user_id`),
  CONSTRAINT `FK-lead_profit_type-create_user` FOREIGN KEY (`lpt_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-lead_profit_type-update_user` FOREIGN KEY (`lpt_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_qcall`
--

DROP TABLE IF EXISTS `lead_qcall`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_qcall` (
  `lqc_lead_id` int NOT NULL,
  `lqc_dt_from` datetime NOT NULL,
  `lqc_dt_to` datetime NOT NULL,
  `lqc_weight` int DEFAULT '0',
  `lqc_created_dt` datetime DEFAULT NULL,
  `lqc_call_from` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lqc_reservation_time` datetime DEFAULT NULL,
  `lqc_reservation_user_id` int DEFAULT NULL,
  PRIMARY KEY (`lqc_lead_id`),
  CONSTRAINT `FK-lead_qcall_lqc_lead_id` FOREIGN KEY (`lqc_lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_request`
--

DROP TABLE IF EXISTS `lead_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_request` (
  `lr_id` int NOT NULL AUTO_INCREMENT,
  `lr_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lr_job_id` int DEFAULT NULL,
  `lr_json_data` json DEFAULT NULL,
  `lr_created_dt` datetime DEFAULT NULL,
  `lr_project_id` int DEFAULT NULL,
  `lr_source_id` int DEFAULT NULL,
  `lr_lead_id` int DEFAULT NULL,
  PRIMARY KEY (`lr_id`),
  KEY `FK-lead_request-project_id` (`lr_project_id`),
  KEY `FK-lead_request-source_id` (`lr_source_id`),
  CONSTRAINT `FK-lead_request-project_id` FOREIGN KEY (`lr_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-lead_request-source_id` FOREIGN KEY (`lr_source_id`) REFERENCES `sources` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_task`
--

DROP TABLE IF EXISTS `lead_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_task` (
  `lt_lead_id` int NOT NULL,
  `lt_task_id` int NOT NULL,
  `lt_user_id` int NOT NULL,
  `lt_date` date NOT NULL,
  `lt_notes` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lt_completed_dt` datetime DEFAULT NULL,
  `lt_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`lt_user_id`,`lt_lead_id`,`lt_task_id`,`lt_date`),
  KEY `lead_task_lt_lead_id_fkey` (`lt_lead_id`),
  KEY `lead_task_lt_task_id_fkey` (`lt_task_id`),
  KEY `IND_lead_task` (`lt_user_id`,`lt_completed_dt`,`lt_date`),
  KEY `IND-lead_task_lt_date` (`lt_date`),
  CONSTRAINT `lead_task_lt_lead_id_fkey` FOREIGN KEY (`lt_lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `lead_task_lt_task_id_fkey` FOREIGN KEY (`lt_task_id`) REFERENCES `task` (`t_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `lead_task_lt_user_id_fkey` FOREIGN KEY (`lt_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lead_user_conversion`
--

DROP TABLE IF EXISTS `lead_user_conversion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lead_user_conversion` (
  `luc_lead_id` int NOT NULL,
  `luc_user_id` int NOT NULL,
  `luc_description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `luc_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`luc_lead_id`,`luc_user_id`),
  KEY `FK-lead_user_conversion-luc_user_id` (`luc_user_id`),
  CONSTRAINT `FK-lead_user_conversion-luc_lead_id` FOREIGN KEY (`luc_lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-lead_user_conversion-luc_user_id` FOREIGN KEY (`luc_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `leads` (
  `id` int NOT NULL AUTO_INCREMENT,
  `client_id` int DEFAULT NULL,
  `employee_id` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `uid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `source_id` int DEFAULT NULL,
  `trip_type` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cabin` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adults` int DEFAULT NULL,
  `children` int DEFAULT NULL,
  `infants` int DEFAULT NULL,
  `notes_for_experts` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT NULL,
  `request_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `request_ip_detail` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `offset_gmt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `snooze_for` datetime DEFAULT NULL,
  `rating` int DEFAULT '0',
  `called_expert` tinyint(1) DEFAULT '0',
  `discount_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bo_flight_id` int DEFAULT NULL,
  `additional_information` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `l_answered` tinyint(1) DEFAULT '0',
  `clone_id` int DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_profit` float DEFAULT NULL,
  `tips` decimal(10,2) DEFAULT '0.00',
  `gid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agents_processing_fee` float DEFAULT NULL,
  `l_call_status_id` tinyint DEFAULT '0',
  `l_pending_delay_dt` datetime DEFAULT NULL,
  `l_client_first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_client_last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_client_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_client_email` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_client_lang` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_client_ua` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `l_request_hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_duplicate_lead_id` int DEFAULT NULL,
  `l_init_price` decimal(10,2) DEFAULT NULL,
  `l_last_action_dt` datetime DEFAULT NULL,
  `l_dep_id` int DEFAULT NULL,
  `l_delayed_charge` tinyint(1) DEFAULT '0',
  `l_type_create` smallint DEFAULT NULL,
  `l_is_test` tinyint(1) NOT NULL DEFAULT '0',
  `hybrid_uid` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `l_visitor_log_id` int DEFAULT NULL,
  `l_status_dt` datetime DEFAULT NULL,
  `l_expiration_dt` datetime DEFAULT NULL,
  `l_type` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gid` (`gid`),
  UNIQUE KEY `IND-leads_gii` (`gid`),
  KEY `fk-lead-employees` (`employee_id`),
  KEY `fk-lead-clients` (`client_id`),
  KEY `tbl_leads_status_project_id_ind` (`status`,`project_id`),
  KEY `fk-lead-clone` (`clone_id`),
  KEY `IND-leads_l_call_status_id` (`l_call_status_id`),
  KEY `IND-leads_l_pending_delay_dt` (`l_pending_delay_dt`),
  KEY `FK-leads-l_duplicate_lead_id` (`l_duplicate_lead_id`),
  KEY `IND-leads-l_request_hash` (`l_request_hash`),
  KEY `IND-leads_request_ip` (`request_ip`),
  KEY `FK-leads_project_id` (`project_id`),
  KEY `FK-leads_source_id` (`source_id`),
  KEY `IND-leads_l_last_action_dt` (`l_last_action_dt`),
  KEY `IND-leads_uid_source_id` (`uid`,`source_id`),
  KEY `FK-leads_l_dep_id` (`l_dep_id`),
  KEY `IND-leads_l_is_test` (`l_is_test`),
  KEY `FK-leads-l_visitor_log_id` (`l_visitor_log_id`),
  KEY `FK-leads-l_client_lang` (`l_client_lang`),
  KEY `IND-leads_l_status_dt` (`l_status_dt`),
  KEY `IND-leads-l_expiration_dt` (`l_expiration_dt`),
  KEY `IND-leads-bo_flight_id` (`bo_flight_id`),
  KEY `IND-leads-status` (`status`),
  CONSTRAINT `fk-lead-clients` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`),
  CONSTRAINT `fk-lead-clone` FOREIGN KEY (`clone_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-lead-employees` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `FK-leads-l_client_lang` FOREIGN KEY (`l_client_lang`) REFERENCES `language` (`language_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-leads-l_duplicate_lead_id` FOREIGN KEY (`l_duplicate_lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-leads-l_visitor_log_id` FOREIGN KEY (`l_visitor_log_id`) REFERENCES `visitor_log` (`vl_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-leads_l_dep_id` FOREIGN KEY (`l_dep_id`) REFERENCES `department` (`dep_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-leads_project_id` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-leads_source_id` FOREIGN KEY (`source_id`) REFERENCES `sources` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=513056 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `level` int DEFAULT NULL,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_time` double DEFAULT NULL,
  `prefix` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_log_level` (`level`),
  KEY `idx_log_category` (`category`),
  KEY `log_index` (`log_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migration` (
  `version` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `apply_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notes`
--

DROP TABLE IF EXISTS `notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int DEFAULT NULL,
  `lead_id` int DEFAULT NULL,
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk-notes-employee` (`employee_id`),
  KEY `fk-notes-lead` (`lead_id`),
  CONSTRAINT `fk-notes-employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk-notes-lead` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=179855 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `n_id` int NOT NULL AUTO_INCREMENT,
  `n_unique_id` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_user_id` int NOT NULL,
  `n_type_id` smallint NOT NULL,
  `n_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `n_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `n_new` tinyint(1) DEFAULT '1',
  `n_deleted` tinyint(1) DEFAULT '0',
  `n_popup` tinyint(1) DEFAULT '0',
  `n_popup_show` tinyint(1) DEFAULT '0',
  `n_read_dt` datetime DEFAULT NULL,
  `n_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`n_id`),
  KEY `IND-notifications-n_deleted` (`n_deleted`),
  KEY `IND-notifications-n_user_id` (`n_user_id`),
  KEY `IND-notifications-n_user_id-n_new-n_deleted` (`n_user_id`,`n_new`,`n_deleted`),
  KEY `IND-notifications-user-deleted` (`n_user_id`,`n_deleted`),
  CONSTRAINT `FK-notifications_n_user_id` FOREIGN KEY (`n_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1694784 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offer`
--

DROP TABLE IF EXISTS `offer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offer` (
  `of_id` int NOT NULL AUTO_INCREMENT,
  `of_gid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `of_uid` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `of_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `of_lead_id` int NOT NULL,
  `of_status_id` tinyint DEFAULT NULL,
  `of_owner_user_id` int DEFAULT NULL,
  `of_created_user_id` int DEFAULT NULL,
  `of_updated_user_id` int DEFAULT NULL,
  `of_created_dt` datetime DEFAULT NULL,
  `of_updated_dt` datetime DEFAULT NULL,
  `of_client_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `of_client_currency_rate` decimal(8,5) DEFAULT NULL,
  `of_app_total` decimal(8,2) DEFAULT NULL,
  `of_client_total` decimal(8,2) DEFAULT NULL,
  `of_profit_amount` decimal(8,2) DEFAULT '0.00',
  PRIMARY KEY (`of_id`),
  UNIQUE KEY `of_gid` (`of_gid`),
  UNIQUE KEY `IND-offer-of_gid` (`of_gid`),
  UNIQUE KEY `of_uid` (`of_uid`),
  UNIQUE KEY `IND-offer-of_uid` (`of_uid`),
  KEY `FK-offer-of_lead_id` (`of_lead_id`),
  KEY `FK-offer-of_owner_user_id` (`of_owner_user_id`),
  KEY `FK-offer-of_created_user_id` (`of_created_user_id`),
  KEY `FK-offer-of_updated_user_id` (`of_updated_user_id`),
  KEY `IND-offer-of_status_id` (`of_status_id`),
  KEY `FK-offer-of_client_currency` (`of_client_currency`),
  CONSTRAINT `FK-offer-of_client_currency` FOREIGN KEY (`of_client_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-offer-of_created_user_id` FOREIGN KEY (`of_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-offer-of_lead_id` FOREIGN KEY (`of_lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-offer-of_owner_user_id` FOREIGN KEY (`of_owner_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-offer-of_updated_user_id` FOREIGN KEY (`of_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offer_product`
--

DROP TABLE IF EXISTS `offer_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offer_product` (
  `op_offer_id` int NOT NULL,
  `op_product_quote_id` int NOT NULL,
  `op_created_user_id` int DEFAULT NULL,
  `op_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`op_offer_id`,`op_product_quote_id`),
  KEY `FK-offer_product-op_product_quote_id` (`op_product_quote_id`),
  KEY `FK-offer_product-op_created_user_id` (`op_created_user_id`),
  CONSTRAINT `FK-offer_product-op_created_user_id` FOREIGN KEY (`op_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-offer_product-op_offer_id` FOREIGN KEY (`op_offer_id`) REFERENCES `offer` (`of_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-offer_product-op_product_quote_id` FOREIGN KEY (`op_product_quote_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offer_send_log`
--

DROP TABLE IF EXISTS `offer_send_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offer_send_log` (
  `ofsndl_id` int NOT NULL AUTO_INCREMENT,
  `ofsndl_offer_id` int NOT NULL,
  `ofsndl_type_id` tinyint NOT NULL,
  `ofsndl_send_to` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ofsndl_created_user_id` int DEFAULT NULL,
  `ofsndl_created_dt` datetime NOT NULL,
  PRIMARY KEY (`ofsndl_id`),
  KEY `FK-offer_send_log_ofsndl_offer_id` (`ofsndl_offer_id`),
  KEY `FK-offer_send_log_ofsndl_created_user_id` (`ofsndl_created_user_id`),
  CONSTRAINT `FK-offer_send_log_ofsndl_created_user_id` FOREIGN KEY (`ofsndl_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-offer_send_log_ofsndl_offer_id` FOREIGN KEY (`ofsndl_offer_id`) REFERENCES `offer` (`of_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offer_status_log`
--

DROP TABLE IF EXISTS `offer_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offer_status_log` (
  `osl_id` int NOT NULL AUTO_INCREMENT,
  `osl_offer_id` int NOT NULL,
  `osl_start_status_id` tinyint DEFAULT NULL,
  `osl_end_status_id` tinyint NOT NULL,
  `osl_start_dt` datetime NOT NULL,
  `osl_end_dt` datetime DEFAULT NULL,
  `osl_duration` int DEFAULT NULL,
  `osl_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `osl_owner_user_id` int DEFAULT NULL,
  `osl_created_user_id` int DEFAULT NULL,
  `osl_action_id` tinyint DEFAULT NULL,
  PRIMARY KEY (`osl_id`),
  KEY `FK-offer_status_log_osl_offer_id` (`osl_offer_id`),
  KEY `FK-offer_status_log_osl_owner_user_id` (`osl_owner_user_id`),
  KEY `FK-offer_status_log_osl_created_user_id` (`osl_created_user_id`),
  CONSTRAINT `FK-offer_status_log_osl_created_user_id` FOREIGN KEY (`osl_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-offer_status_log_osl_offer_id` FOREIGN KEY (`osl_offer_id`) REFERENCES `offer` (`of_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-offer_status_log_osl_owner_user_id` FOREIGN KEY (`osl_owner_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `offer_view_log`
--

DROP TABLE IF EXISTS `offer_view_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `offer_view_log` (
  `ofvwl_id` int NOT NULL AUTO_INCREMENT,
  `ofvwl_offer_id` int NOT NULL,
  `ofvwl_visitor_id` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ofvwl_ip_address` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ofvwl_user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ofvwl_created_dt` datetime NOT NULL,
  PRIMARY KEY (`ofvwl_id`),
  KEY `FK-offer_view_log_ofvwl_offer_id` (`ofvwl_offer_id`),
  CONSTRAINT `FK-offer_view_log_ofvwl_offer_id` FOREIGN KEY (`ofvwl_offer_id`) REFERENCES `offer` (`of_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order` (
  `or_id` int NOT NULL AUTO_INCREMENT,
  `or_gid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `or_uid` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `or_fare_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `or_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `or_lead_id` int DEFAULT NULL,
  `or_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `or_status_id` tinyint DEFAULT NULL,
  `or_pay_status_id` tinyint DEFAULT NULL,
  `or_app_total` decimal(8,2) DEFAULT NULL,
  `or_app_markup` decimal(8,2) DEFAULT NULL,
  `or_agent_markup` decimal(8,2) DEFAULT NULL,
  `or_client_total` decimal(8,2) DEFAULT NULL,
  `or_client_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `or_client_currency_rate` decimal(8,5) DEFAULT NULL,
  `or_owner_user_id` int DEFAULT NULL,
  `or_created_user_id` int DEFAULT NULL,
  `or_updated_user_id` int DEFAULT NULL,
  `or_created_dt` datetime DEFAULT NULL,
  `or_updated_dt` datetime DEFAULT NULL,
  `or_profit_amount` decimal(8,2) DEFAULT '0.00',
  `or_request_data` json DEFAULT NULL,
  `or_request_id` int DEFAULT NULL,
  `or_project_id` int DEFAULT NULL,
  `or_type_id` tinyint(1) DEFAULT NULL,
  `or_sale_id` int DEFAULT NULL,
  PRIMARY KEY (`or_id`),
  UNIQUE KEY `or_gid` (`or_gid`),
  UNIQUE KEY `IND-order-or_gid` (`or_gid`),
  UNIQUE KEY `or_uid` (`or_uid`),
  UNIQUE KEY `IND-order-or_uid` (`or_uid`),
  UNIQUE KEY `IND-order-or_sale_id` (`or_sale_id`),
  KEY `FK-order-or_lead_id` (`or_lead_id`),
  KEY `FK-order-pr_owner_user_id` (`or_owner_user_id`),
  KEY `FK-order-pr_created_user_id` (`or_created_user_id`),
  KEY `FK-order-pr_updated_user_id` (`or_updated_user_id`),
  KEY `IND-order-or_status_id` (`or_status_id`),
  KEY `IND-order-or_pay_status_id` (`or_pay_status_id`),
  KEY `FK-order-or_client_currency` (`or_client_currency`),
  KEY `FK-order-or_project_id` (`or_project_id`),
  KEY `FK-order-or_request_id` (`or_request_id`),
  CONSTRAINT `FK-order-leads` FOREIGN KEY (`or_lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-order-or_client_currency` FOREIGN KEY (`or_client_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-order-or_project_id` FOREIGN KEY (`or_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-order-or_request_id` FOREIGN KEY (`or_request_id`) REFERENCES `order_request` (`orr_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-order-pr_created_user_id` FOREIGN KEY (`or_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-order-pr_owner_user_id` FOREIGN KEY (`or_owner_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-order-pr_updated_user_id` FOREIGN KEY (`or_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_contact`
--

DROP TABLE IF EXISTS `order_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_contact` (
  `oc_id` int NOT NULL AUTO_INCREMENT,
  `oc_order_id` int DEFAULT NULL,
  `oc_first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oc_last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oc_middle_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oc_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oc_phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oc_created_dt` datetime DEFAULT NULL,
  `oc_updated_dt` datetime DEFAULT NULL,
  `oc_client_id` int DEFAULT NULL,
  PRIMARY KEY (`oc_id`),
  KEY `FK-order_contact-oc_order_id` (`oc_order_id`),
  KEY `IND-order_contact-oc_email` (`oc_email`),
  KEY `IND-order_contact-oc_phone_number` (`oc_phone_number`),
  KEY `FK-order_contact_oc_client_id` (`oc_client_id`),
  CONSTRAINT `FK-order_contact-oc_order_id` FOREIGN KEY (`oc_order_id`) REFERENCES `order` (`or_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-order_contact_oc_client_id` FOREIGN KEY (`oc_client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_data`
--

DROP TABLE IF EXISTS `order_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_data` (
  `od_id` int NOT NULL AUTO_INCREMENT,
  `od_order_id` int NOT NULL,
  `od_display_uid` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `od_source_id` int DEFAULT NULL,
  `od_created_by` int DEFAULT NULL,
  `od_updated_by` int DEFAULT NULL,
  `od_created_dt` datetime DEFAULT NULL,
  `od_updated_dt` datetime DEFAULT NULL,
  `od_language_id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `od_market_country` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`od_id`),
  KEY `FK-order_data-od_order_id` (`od_order_id`),
  KEY `FK-order_data-od_created_by` (`od_created_by`),
  KEY `FK-order_data-od_updated_by` (`od_updated_by`),
  KEY `FK-order_data-od_source_id` (`od_source_id`),
  KEY `FK-order_data-od_language_id` (`od_language_id`),
  CONSTRAINT `FK-order_data-od_created_by` FOREIGN KEY (`od_created_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-order_data-od_language_id` FOREIGN KEY (`od_language_id`) REFERENCES `language` (`language_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-order_data-od_order_id` FOREIGN KEY (`od_order_id`) REFERENCES `order` (`or_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-order_data-od_source_id` FOREIGN KEY (`od_source_id`) REFERENCES `sources` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-order_data-od_updated_by` FOREIGN KEY (`od_updated_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_email`
--

DROP TABLE IF EXISTS `order_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_email` (
  `oe_id` int NOT NULL AUTO_INCREMENT,
  `oe_order_id` int DEFAULT NULL,
  `oe_email_id` int DEFAULT NULL,
  `oe_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`oe_id`),
  KEY `FK-order_email-oe_order_id` (`oe_order_id`),
  KEY `FK-order_email-oe_email_id` (`oe_email_id`),
  CONSTRAINT `FK-order_email-oe_email_id` FOREIGN KEY (`oe_email_id`) REFERENCES `email` (`e_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-order_email-oe_order_id` FOREIGN KEY (`oe_order_id`) REFERENCES `order` (`or_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_process_manager`
--

DROP TABLE IF EXISTS `order_process_manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_process_manager` (
  `opm_id` int NOT NULL,
  `opm_status` tinyint NOT NULL,
  `opm_created_dt` datetime DEFAULT NULL,
  `opm_type` tinyint DEFAULT NULL,
  PRIMARY KEY (`opm_id`),
  CONSTRAINT `FK-order_process_manager-opm_id` FOREIGN KEY (`opm_id`) REFERENCES `order` (`or_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_refund`
--

DROP TABLE IF EXISTS `order_refund`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_refund` (
  `orr_id` int NOT NULL AUTO_INCREMENT,
  `orr_uid` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `orr_order_id` int NOT NULL,
  `orr_selling_price` decimal(8,2) DEFAULT NULL,
  `orr_penalty_amount` decimal(8,2) DEFAULT NULL,
  `orr_processing_fee_amount` decimal(8,2) DEFAULT NULL,
  `orr_charge_amount` decimal(8,2) DEFAULT NULL,
  `orr_refund_amount` decimal(8,2) DEFAULT NULL,
  `orr_client_status_id` tinyint DEFAULT NULL,
  `orr_status_id` tinyint DEFAULT NULL,
  `orr_client_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orr_client_currency_rate` decimal(8,2) DEFAULT NULL,
  `orr_client_selling_price` decimal(8,2) DEFAULT NULL,
  `orr_client_charge_amount` decimal(8,2) DEFAULT NULL,
  `orr_client_refund_amount` decimal(8,2) DEFAULT NULL,
  `orr_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `orr_expiration_dt` datetime DEFAULT NULL,
  `orr_created_user_id` int DEFAULT NULL,
  `orr_updated_user_id` int DEFAULT NULL,
  `orr_created_dt` datetime DEFAULT NULL,
  `orr_updated_dt` datetime DEFAULT NULL,
  `orr_case_id` int DEFAULT NULL,
  PRIMARY KEY (`orr_id`),
  KEY `FK-order_refund-orr_order_id` (`orr_order_id`),
  KEY `FK-order_refund-orr_client_currency` (`orr_client_currency`),
  KEY `FK-order_refund-orr_created_user_id` (`orr_created_user_id`),
  KEY `FK-order_refund-orr_updated_user_id` (`orr_updated_user_id`),
  KEY `IND-order_refund-order_id` (`orr_uid`),
  KEY `IND-order_refund-orr_client_status_id` (`orr_client_status_id`),
  KEY `IND-order_refund-orr_status_id` (`orr_status_id`),
  KEY `IND-order_refund-orr_expiration_dt` (`orr_expiration_dt`),
  KEY `FK-order_refund-case` (`orr_case_id`),
  CONSTRAINT `FK-order_refund-case` FOREIGN KEY (`orr_case_id`) REFERENCES `cases` (`cs_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-order_refund-orr_client_currency` FOREIGN KEY (`orr_client_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-order_refund-orr_created_user_id` FOREIGN KEY (`orr_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-order_refund-orr_order_id` FOREIGN KEY (`orr_order_id`) REFERENCES `order` (`or_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-order_refund-orr_updated_user_id` FOREIGN KEY (`orr_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_request`
--

DROP TABLE IF EXISTS `order_request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_request` (
  `orr_id` int NOT NULL AUTO_INCREMENT,
  `orr_request_data_json` json DEFAULT NULL,
  `orr_response_data_json` json DEFAULT NULL,
  `orr_source_type_id` tinyint(1) DEFAULT NULL,
  `orr_response_type_id` tinyint(1) DEFAULT NULL,
  `orr_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`orr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_status_log`
--

DROP TABLE IF EXISTS `order_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_status_log` (
  `orsl_id` int NOT NULL AUTO_INCREMENT,
  `orsl_order_id` int NOT NULL,
  `orsl_start_status_id` tinyint DEFAULT NULL,
  `orsl_end_status_id` tinyint NOT NULL,
  `orsl_start_dt` datetime NOT NULL,
  `orsl_end_dt` datetime DEFAULT NULL,
  `orsl_duration` int DEFAULT NULL,
  `orsl_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `orsl_action_id` tinyint DEFAULT NULL,
  `orsl_owner_user_id` int DEFAULT NULL,
  `orsl_created_user_id` int DEFAULT NULL,
  PRIMARY KEY (`orsl_id`),
  KEY `FK-order_status_log_orsl_order_id` (`orsl_order_id`),
  KEY `FK-order_status_log_orsl_owner_user_id` (`orsl_owner_user_id`),
  KEY `FK-order_status_log_orsl_created_user_id` (`orsl_created_user_id`),
  CONSTRAINT `FK-order_status_log_orsl_created_user_id` FOREIGN KEY (`orsl_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-order_status_log_orsl_order_id` FOREIGN KEY (`orsl_order_id`) REFERENCES `order` (`or_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-order_status_log_orsl_owner_user_id` FOREIGN KEY (`orsl_owner_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_tips`
--

DROP TABLE IF EXISTS `order_tips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_tips` (
  `ot_order_id` int NOT NULL AUTO_INCREMENT,
  `ot_client_amount` decimal(8,2) DEFAULT NULL,
  `ot_amount` decimal(8,2) DEFAULT NULL,
  `ot_user_profit_percent` smallint DEFAULT NULL,
  `ot_user_profit` decimal(8,2) DEFAULT NULL,
  `ot_description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ot_created_dt` datetime DEFAULT NULL,
  `ot_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ot_order_id`),
  CONSTRAINT `FK-order_tips-ot_order_id` FOREIGN KEY (`ot_order_id`) REFERENCES `order` (`or_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_tips_user_profit`
--

DROP TABLE IF EXISTS `order_tips_user_profit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_tips_user_profit` (
  `otup_order_id` int NOT NULL,
  `otup_user_id` int NOT NULL,
  `otup_percent` tinyint NOT NULL,
  `otup_amount` decimal(8,2) DEFAULT NULL,
  `otup_created_dt` datetime DEFAULT NULL,
  `otup_updated_dt` datetime DEFAULT NULL,
  `otup_created_user_id` int DEFAULT NULL,
  `otup_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`otup_order_id`,`otup_user_id`),
  KEY `fk-order_tips_user_profit-otup_user_id` (`otup_user_id`),
  KEY `fk-order_tips_user_profit-otup_created_user_id` (`otup_created_user_id`),
  KEY `fk-order_tips_user_profit-otup_updated_user_id` (`otup_updated_user_id`),
  CONSTRAINT `fk-order_tips_user_profit-otup_created_user_id` FOREIGN KEY (`otup_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-order_tips_user_profit-otup_order_id` FOREIGN KEY (`otup_order_id`) REFERENCES `order` (`or_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-order_tips_user_profit-otup_updated_user_id` FOREIGN KEY (`otup_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-order_tips_user_profit-otup_user_id` FOREIGN KEY (`otup_user_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `order_user_profit`
--

DROP TABLE IF EXISTS `order_user_profit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_user_profit` (
  `oup_order_id` int NOT NULL,
  `oup_user_id` int NOT NULL,
  `oup_percent` tinyint NOT NULL,
  `oup_amount` decimal(8,2) DEFAULT NULL,
  `oup_created_dt` datetime DEFAULT NULL,
  `oup_updated_dt` datetime DEFAULT NULL,
  `oup_created_user_id` int DEFAULT NULL,
  `oup_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`oup_order_id`,`oup_user_id`),
  KEY `fk-order_user_profit-oup_user_id` (`oup_user_id`),
  KEY `fk-order_user_profit-oup_created_user_id` (`oup_created_user_id`),
  KEY `fk-order_user_profit-oup_updated_user_id` (`oup_updated_user_id`),
  CONSTRAINT `fk-order_user_profit-oup_created_user_id` FOREIGN KEY (`oup_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-order_user_profit-oup_order_id` FOREIGN KEY (`oup_order_id`) REFERENCES `order` (`or_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-order_user_profit-oup_updated_user_id` FOREIGN KEY (`oup_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-order_user_profit-oup_user_id` FOREIGN KEY (`oup_user_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment` (
  `pay_id` int NOT NULL AUTO_INCREMENT,
  `pay_type_id` tinyint(1) DEFAULT NULL,
  `pay_method_id` int DEFAULT NULL,
  `pay_status_id` tinyint(1) DEFAULT '0',
  `pay_date` date NOT NULL,
  `pay_amount` decimal(8,2) NOT NULL,
  `pay_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_invoice_id` int DEFAULT NULL,
  `pay_order_id` int DEFAULT NULL,
  `pay_created_user_id` int DEFAULT NULL,
  `pay_updated_user_id` int DEFAULT NULL,
  `pay_created_dt` datetime DEFAULT NULL,
  `pay_updated_dt` datetime DEFAULT NULL,
  `pay_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pay_billing_id` int DEFAULT NULL,
  PRIMARY KEY (`pay_id`),
  KEY `FK-payment-pay_method_id` (`pay_method_id`),
  KEY `FK-payment-pay_currency` (`pay_currency`),
  KEY `FK-payment-pay_invoice_id` (`pay_invoice_id`),
  KEY `FK-payment-pay_order_id` (`pay_order_id`),
  KEY `FK-payment-pay_created_user_id` (`pay_created_user_id`),
  KEY `FK-payment-pay_updated_user_id` (`pay_updated_user_id`),
  KEY `FK-payment-pay_billing_id` (`pay_billing_id`),
  CONSTRAINT `FK-payment-pay_billing_id` FOREIGN KEY (`pay_billing_id`) REFERENCES `billing_info` (`bi_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-payment-pay_created_user_id` FOREIGN KEY (`pay_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-payment-pay_currency` FOREIGN KEY (`pay_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-payment-pay_invoice_id` FOREIGN KEY (`pay_invoice_id`) REFERENCES `invoice` (`inv_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-payment-pay_method_id` FOREIGN KEY (`pay_method_id`) REFERENCES `payment_method` (`pm_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-payment-pay_order_id` FOREIGN KEY (`pay_order_id`) REFERENCES `order` (`or_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-payment-pay_updated_user_id` FOREIGN KEY (`pay_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_method`
--

DROP TABLE IF EXISTS `payment_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_method` (
  `pm_id` int NOT NULL AUTO_INCREMENT,
  `pm_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pm_short_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_enabled` tinyint(1) DEFAULT '1',
  `pm_category_id` tinyint(1) DEFAULT NULL,
  `pm_updated_user_id` int DEFAULT NULL,
  `pm_updated_dt` datetime DEFAULT NULL,
  `pm_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`pm_id`),
  UNIQUE KEY `pm_name` (`pm_name`),
  KEY `FK-payment_method-pm_updated_user_id` (`pm_updated_user_id`),
  CONSTRAINT `FK-payment_method-pm_updated_user_id` FOREIGN KEY (`pm_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phone_blacklist`
--

DROP TABLE IF EXISTS `phone_blacklist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phone_blacklist` (
  `pbl_id` int NOT NULL AUTO_INCREMENT,
  `pbl_phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pbl_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pbl_enabled` tinyint(1) DEFAULT NULL,
  `pbl_created_dt` datetime DEFAULT NULL,
  `pbl_updated_dt` datetime DEFAULT NULL,
  `pbl_updated_user_id` int DEFAULT NULL,
  `pbl_expiration_date` datetime DEFAULT NULL,
  PRIMARY KEY (`pbl_id`),
  UNIQUE KEY `pbl_phone` (`pbl_phone`),
  KEY `FK-phone_blacklist_pbl_updated_user_id` (`pbl_updated_user_id`),
  CONSTRAINT `FK-phone_blacklist_pbl_updated_user_id` FOREIGN KEY (`pbl_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=245 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phone_blacklist_log`
--

DROP TABLE IF EXISTS `phone_blacklist_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phone_blacklist_log` (
  `pbll_id` int NOT NULL AUTO_INCREMENT,
  `pbll_phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pbll_created_dt` datetime DEFAULT NULL,
  `pbll_created_user_id` int DEFAULT NULL,
  PRIMARY KEY (`pbll_id`),
  KEY `IDX-phone_blacklist_log-pbll_phone` (`pbll_phone`),
  KEY `FK-phone_blacklist_log-pbll_created_user_id` (`pbll_created_user_id`),
  CONSTRAINT `FK-phone_blacklist_log-pbll_created_user_id` FOREIGN KEY (`pbll_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phone_line`
--

DROP TABLE IF EXISTS `phone_line`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phone_line` (
  `line_id` int NOT NULL AUTO_INCREMENT,
  `line_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `line_project_id` int NOT NULL,
  `line_dep_id` int DEFAULT NULL,
  `line_language_id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `line_settings_json` json DEFAULT NULL,
  `line_personal_user_id` int DEFAULT NULL,
  `line_uvm_id` int DEFAULT NULL,
  `line_allow_in` tinyint(1) DEFAULT '1',
  `line_allow_out` tinyint(1) DEFAULT '1',
  `line_enabled` tinyint(1) DEFAULT '1',
  `line_created_user_id` int DEFAULT NULL,
  `line_updated_user_id` int DEFAULT NULL,
  `line_created_dt` datetime DEFAULT NULL,
  `line_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`line_id`),
  KEY `FK-line_project_id` (`line_project_id`),
  KEY `FK-line_dep_id` (`line_dep_id`),
  KEY `FK-line_personal_user_id` (`line_personal_user_id`),
  KEY `FK-line_uvm_id` (`line_uvm_id`),
  KEY `FK-line_created_user_id` (`line_created_user_id`),
  KEY `FK-line_updated_user_id` (`line_updated_user_id`),
  KEY `IND-line_name` (`line_name`),
  CONSTRAINT `FK-line_created_user_id` FOREIGN KEY (`line_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-line_dep_id` FOREIGN KEY (`line_dep_id`) REFERENCES `department` (`dep_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-line_personal_user_id` FOREIGN KEY (`line_personal_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-line_project_id` FOREIGN KEY (`line_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-line_updated_user_id` FOREIGN KEY (`line_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-line_uvm_id` FOREIGN KEY (`line_uvm_id`) REFERENCES `user_voice_mail` (`uvm_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phone_line_command`
--

DROP TABLE IF EXISTS `phone_line_command`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phone_line_command` (
  `plc_id` int NOT NULL AUTO_INCREMENT,
  `plc_line_id` int DEFAULT NULL,
  `plc_ccom_id` int DEFAULT NULL,
  `plc_sort_order` int DEFAULT '5',
  `plc_created_user_id` int DEFAULT NULL,
  `plc_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`plc_id`),
  KEY `FK-phone_line_command-plc_line_id` (`plc_line_id`),
  KEY `FK-phone_line_command-plc_ccom_id` (`plc_ccom_id`),
  KEY `FK-phone_line_command-plc_created_user_id` (`plc_created_user_id`),
  CONSTRAINT `FK-phone_line_command-plc_ccom_id` FOREIGN KEY (`plc_ccom_id`) REFERENCES `call_command` (`ccom_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-phone_line_command-plc_created_user_id` FOREIGN KEY (`plc_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-phone_line_command-plc_line_id` FOREIGN KEY (`plc_line_id`) REFERENCES `phone_line` (`line_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phone_line_phone_number`
--

DROP TABLE IF EXISTS `phone_line_phone_number`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phone_line_phone_number` (
  `plpn_line_id` int NOT NULL,
  `plpn_pl_id` int NOT NULL,
  `plpn_default` tinyint(1) DEFAULT '0',
  `plpn_enabled` tinyint(1) DEFAULT '1',
  `plpn_settings_json` json DEFAULT NULL,
  `plpn_created_user_id` int DEFAULT NULL,
  `plpn_updated_user_id` int DEFAULT NULL,
  `plpn_created_dt` datetime DEFAULT NULL,
  `plpn_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`plpn_line_id`,`plpn_pl_id`),
  UNIQUE KEY `plpn_pl_id` (`plpn_pl_id`),
  KEY `FK-plpn_created_user_id` (`plpn_created_user_id`),
  KEY `FK-plpn_updated_user_id` (`plpn_updated_user_id`),
  CONSTRAINT `FK-plpn_created_user_id` FOREIGN KEY (`plpn_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-plpn_line_id` FOREIGN KEY (`plpn_line_id`) REFERENCES `phone_line` (`line_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-plpn_pl_id` FOREIGN KEY (`plpn_pl_id`) REFERENCES `phone_list` (`pl_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-plpn_updated_user_id` FOREIGN KEY (`plpn_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phone_line_user_assign`
--

DROP TABLE IF EXISTS `phone_line_user_assign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phone_line_user_assign` (
  `plus_line_id` int NOT NULL,
  `plus_user_id` int NOT NULL,
  `plus_allow_in` tinyint(1) DEFAULT '1',
  `plus_allow_out` tinyint(1) DEFAULT '1',
  `plus_uvm_id` int DEFAULT NULL,
  `plus_enabled` tinyint(1) DEFAULT '1',
  `plus_settings_json` json DEFAULT NULL,
  `plus_created_user_id` int DEFAULT NULL,
  `plus_updated_user_id` int DEFAULT NULL,
  `plus_created_dt` datetime DEFAULT NULL,
  `plus_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`plus_line_id`,`plus_user_id`),
  KEY `FK-plus_user_id` (`plus_user_id`),
  KEY `FK-plus_uvm_id` (`plus_uvm_id`),
  KEY `FK-plus_created_user_id` (`plus_created_user_id`),
  KEY `FK-plus_updated_user_id` (`plus_updated_user_id`),
  CONSTRAINT `FK-plus_created_user_id` FOREIGN KEY (`plus_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-plus_line_id` FOREIGN KEY (`plus_line_id`) REFERENCES `phone_line` (`line_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-plus_updated_user_id` FOREIGN KEY (`plus_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-plus_user_id` FOREIGN KEY (`plus_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-plus_uvm_id` FOREIGN KEY (`plus_uvm_id`) REFERENCES `user_voice_mail` (`uvm_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phone_line_user_group`
--

DROP TABLE IF EXISTS `phone_line_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phone_line_user_group` (
  `plug_line_id` int NOT NULL,
  `plug_ug_id` int NOT NULL,
  `plug_created_dt` datetime DEFAULT NULL,
  `plug_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`plug_line_id`,`plug_ug_id`),
  KEY `FK-plug_ug_id` (`plug_ug_id`),
  CONSTRAINT `FK-plug_line_id` FOREIGN KEY (`plug_line_id`) REFERENCES `phone_line` (`line_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-plug_ug_id` FOREIGN KEY (`plug_ug_id`) REFERENCES `user_group` (`ug_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `phone_list`
--

DROP TABLE IF EXISTS `phone_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phone_list` (
  `pl_id` int NOT NULL AUTO_INCREMENT,
  `pl_phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pl_title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pl_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `pl_created_user_id` int DEFAULT NULL,
  `pl_updated_user_id` int DEFAULT NULL,
  `pl_created_dt` datetime DEFAULT NULL,
  `pl_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`pl_id`),
  UNIQUE KEY `IND-phone_list-pl_phone_number` (`pl_phone_number`),
  KEY `IND-phone_list-pl_enabled` (`pl_enabled`),
  KEY `FK-phone_list-pl_created_user_id` (`pl_created_user_id`),
  KEY `FK-phone_list-pl_updated_user_id` (`pl_updated_user_id`),
  CONSTRAINT `FK-phone_list-pl_created_user_id` FOREIGN KEY (`pl_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-phone_list-pl_updated_user_id` FOREIGN KEY (`pl_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1528 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product` (
  `pr_id` int NOT NULL AUTO_INCREMENT,
  `pr_gid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pr_type_id` int NOT NULL,
  `pr_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pr_lead_id` int DEFAULT NULL,
  `pr_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pr_status_id` tinyint DEFAULT NULL,
  `pr_service_fee_percent` decimal(5,2) DEFAULT NULL,
  `pr_created_user_id` int DEFAULT NULL,
  `pr_updated_user_id` int DEFAULT NULL,
  `pr_created_dt` datetime DEFAULT NULL,
  `pr_updated_dt` datetime DEFAULT NULL,
  `pr_market_price` decimal(8,2) DEFAULT NULL,
  `pr_client_budget` decimal(8,2) DEFAULT NULL,
  `pr_project_id` int DEFAULT NULL,
  PRIMARY KEY (`pr_id`),
  UNIQUE KEY `pr_gid` (`pr_gid`),
  KEY `FK-product-pr_lead_id` (`pr_lead_id`),
  KEY `FK-product-pr_created_user_id` (`pr_created_user_id`),
  KEY `FK-product-pr_updated_user_id` (`pr_updated_user_id`),
  KEY `IND-product-pr_status_id` (`pr_status_id`),
  KEY `FK-product-pr_type_id` (`pr_type_id`),
  KEY `FK-product-projects` (`pr_project_id`),
  CONSTRAINT `FK-product-pr_created_user_id` FOREIGN KEY (`pr_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product-pr_lead_id` FOREIGN KEY (`pr_lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-product-pr_type_id` FOREIGN KEY (`pr_type_id`) REFERENCES `product_type` (`pt_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-product-pr_updated_user_id` FOREIGN KEY (`pr_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product-projects` FOREIGN KEY (`pr_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_holder`
--

DROP TABLE IF EXISTS `product_holder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_holder` (
  `ph_id` int NOT NULL AUTO_INCREMENT,
  `ph_product_id` int DEFAULT NULL,
  `ph_first_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ph_last_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ph_middle_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ph_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ph_phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ph_created_dt` datetime DEFAULT NULL,
  `ph_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ph_id`),
  KEY `FK-product_holder-ph_product_id` (`ph_product_id`),
  CONSTRAINT `FK-product_holder-ph_product_id` FOREIGN KEY (`ph_product_id`) REFERENCES `product` (`pr_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_option`
--

DROP TABLE IF EXISTS `product_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_option` (
  `po_id` int NOT NULL AUTO_INCREMENT,
  `po_key` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `po_product_type_id` int NOT NULL,
  `po_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `po_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `po_price_type_id` tinyint(1) DEFAULT '1',
  `po_max_price` decimal(8,2) DEFAULT NULL,
  `po_min_price` decimal(8,2) DEFAULT NULL,
  `po_price` decimal(8,2) DEFAULT NULL,
  `po_enabled` tinyint(1) DEFAULT '1',
  `po_created_user_id` int DEFAULT NULL,
  `po_updated_user_id` int DEFAULT NULL,
  `po_created_dt` datetime DEFAULT NULL,
  `po_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`po_id`),
  UNIQUE KEY `po_key` (`po_key`),
  KEY `IND-product_option-po_key` (`po_key`),
  KEY `FK-product_option-po_product_type_id` (`po_product_type_id`),
  KEY `FK-product_option-po_created_user_id` (`po_created_user_id`),
  KEY `FK-product_option-po_updated_user_id` (`po_updated_user_id`),
  CONSTRAINT `FK-product_option-po_created_user_id` FOREIGN KEY (`po_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_option-po_product_type_id` FOREIGN KEY (`po_product_type_id`) REFERENCES `product_type` (`pt_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-product_option-po_updated_user_id` FOREIGN KEY (`po_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_quote`
--

DROP TABLE IF EXISTS `product_quote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_quote` (
  `pq_id` int NOT NULL AUTO_INCREMENT,
  `pq_gid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pq_name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pq_product_id` int NOT NULL,
  `pq_order_id` int DEFAULT NULL,
  `pq_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pq_status_id` tinyint DEFAULT NULL,
  `pq_price` decimal(8,2) DEFAULT NULL,
  `pq_origin_price` decimal(8,2) DEFAULT NULL,
  `pq_client_price` decimal(8,2) DEFAULT NULL,
  `pq_service_fee_sum` decimal(8,2) DEFAULT NULL,
  `pq_origin_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pq_client_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pq_origin_currency_rate` decimal(8,5) DEFAULT NULL,
  `pq_client_currency_rate` decimal(8,5) DEFAULT NULL,
  `pq_owner_user_id` int DEFAULT NULL,
  `pq_created_user_id` int DEFAULT NULL,
  `pq_updated_user_id` int DEFAULT NULL,
  `pq_created_dt` datetime DEFAULT NULL,
  `pq_updated_dt` datetime DEFAULT NULL,
  `pq_profit_amount` decimal(8,2) DEFAULT '0.00',
  `pq_clone_id` int DEFAULT NULL,
  `pq_app_markup` decimal(10,2) DEFAULT NULL,
  `pq_agent_markup` decimal(10,2) DEFAULT NULL,
  `pq_service_fee_percent` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`pq_id`),
  UNIQUE KEY `pq_gid` (`pq_gid`),
  UNIQUE KEY `IND-product_quote-pq_gid` (`pq_gid`),
  KEY `IND-product_quote-pq_status_id` (`pq_status_id`),
  KEY `FK-product_quote-pq_origin_currency` (`pq_origin_currency`),
  KEY `FK-product_quote-pq_client_currency` (`pq_client_currency`),
  KEY `FK-product_quote-pq_product_id` (`pq_product_id`),
  KEY `FK-product_quote-pq_order_id` (`pq_order_id`),
  KEY `FK-product_quote-pq_owner_user_id` (`pq_owner_user_id`),
  KEY `FK-product_quote-pq_created_user_id` (`pq_created_user_id`),
  KEY `FK-product_quote-pq_updated_user_id` (`pq_updated_user_id`),
  KEY `FK-product_quote_pq_clone_id` (`pq_clone_id`),
  CONSTRAINT `FK-product_quote-pq_client_currency` FOREIGN KEY (`pq_client_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote-pq_created_user_id` FOREIGN KEY (`pq_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote-pq_order_id` FOREIGN KEY (`pq_order_id`) REFERENCES `order` (`or_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote-pq_origin_currency` FOREIGN KEY (`pq_origin_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote-pq_owner_user_id` FOREIGN KEY (`pq_owner_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote-pq_product_id` FOREIGN KEY (`pq_product_id`) REFERENCES `product` (`pr_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote-pq_updated_user_id` FOREIGN KEY (`pq_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_pq_clone_id` FOREIGN KEY (`pq_clone_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_quote_change`
--

DROP TABLE IF EXISTS `product_quote_change`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_quote_change` (
  `pqc_id` int NOT NULL AUTO_INCREMENT,
  `pqc_pq_id` int NOT NULL,
  `pqc_case_id` int DEFAULT NULL,
  `pqc_decision_user` int DEFAULT NULL,
  `pqc_status_id` tinyint DEFAULT NULL,
  `pqc_decision_type_id` int DEFAULT NULL,
  `pqc_created_dt` datetime DEFAULT NULL,
  `pqc_updated_dt` datetime DEFAULT NULL,
  `pqc_decision_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`pqc_id`),
  KEY `FK-product_quote_change-pqc_case_id` (`pqc_case_id`),
  KEY `FK-product_quote_change-pqc_decision_user` (`pqc_decision_user`),
  KEY `FK-product_quote_change-pqc_pq_id` (`pqc_pq_id`),
  KEY `IND-product_quote_change-pqc_status_id` (`pqc_status_id`),
  KEY `IND-product_quote_change-pqc_decision_type_id` (`pqc_decision_type_id`),
  KEY `IND-product_quote_change-pqc_created_dt` (`pqc_created_dt`),
  KEY `IND-product_quote_change-pqc_decision_dt` (`pqc_decision_dt`),
  CONSTRAINT `FK-product_quote_change-pqc_case_id` FOREIGN KEY (`pqc_case_id`) REFERENCES `cases` (`cs_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_change-pqc_decision_user` FOREIGN KEY (`pqc_decision_user`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_change-pqc_pq_id` FOREIGN KEY (`pqc_pq_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_quote_lead`
--

DROP TABLE IF EXISTS `product_quote_lead`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_quote_lead` (
  `pql_product_quote_id` int NOT NULL,
  `pql_lead_id` int NOT NULL,
  PRIMARY KEY (`pql_product_quote_id`,`pql_lead_id`),
  KEY `FK-product_quote_lead-pql_lead_id` (`pql_lead_id`),
  CONSTRAINT `FK-product_quote_lead-pql_lead_id` FOREIGN KEY (`pql_lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_lead-pql_product_quote_id` FOREIGN KEY (`pql_product_quote_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_quote_object_refund`
--

DROP TABLE IF EXISTS `product_quote_object_refund`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_quote_object_refund` (
  `pqor_id` int NOT NULL AUTO_INCREMENT,
  `pqor_product_quote_refund_id` int NOT NULL,
  `pqor_selling_price` decimal(8,2) DEFAULT NULL,
  `pqor_penalty_amount` decimal(8,2) DEFAULT NULL,
  `pqor_processing_fee_amount` decimal(8,2) DEFAULT NULL,
  `pqor_refund_amount` decimal(8,2) DEFAULT NULL,
  `pqor_status_id` tinyint DEFAULT NULL,
  `pqor_client_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pqor_client_currency_rate` decimal(8,2) DEFAULT NULL,
  `pqor_client_selling_price` decimal(8,2) DEFAULT NULL,
  `pqor_client_refund_amount` decimal(8,2) DEFAULT NULL,
  `pqor_created_user_id` int DEFAULT NULL,
  `pqor_updated_user_id` int DEFAULT NULL,
  `pqor_created_dt` datetime DEFAULT NULL,
  `pqor_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`pqor_id`),
  KEY `FK-product_quote_object_refund-pqor_product_quote_refund_id` (`pqor_product_quote_refund_id`),
  KEY `FK-product_quote_object_refund-pqor_created_user_id` (`pqor_created_user_id`),
  KEY `FK-product_quote_object_refund-pqor_updated_user_id` (`pqor_updated_user_id`),
  KEY `FK-product_quote_object_refund-pqor_client_currency` (`pqor_client_currency`),
  KEY `IND-product_quote_object_refund-pqor_status_id` (`pqor_status_id`),
  CONSTRAINT `FK-product_quote_object_refund-pqor_client_currency` FOREIGN KEY (`pqor_client_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_object_refund-pqor_created_user_id` FOREIGN KEY (`pqor_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_object_refund-pqor_product_quote_refund_id` FOREIGN KEY (`pqor_product_quote_refund_id`) REFERENCES `product_quote_refund` (`pqr_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_object_refund-pqor_updated_user_id` FOREIGN KEY (`pqor_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_quote_option`
--

DROP TABLE IF EXISTS `product_quote_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_quote_option` (
  `pqo_id` int NOT NULL AUTO_INCREMENT,
  `pqo_product_quote_id` int NOT NULL,
  `pqo_product_option_id` int DEFAULT NULL,
  `pqo_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pqo_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pqo_status_id` tinyint(1) DEFAULT NULL,
  `pqo_price` decimal(8,2) DEFAULT NULL,
  `pqo_client_price` decimal(8,2) DEFAULT NULL,
  `pqo_extra_markup` decimal(8,2) DEFAULT NULL,
  `pqo_created_user_id` int DEFAULT NULL,
  `pqo_updated_user_id` int DEFAULT NULL,
  `pqo_created_dt` datetime DEFAULT NULL,
  `pqo_updated_dt` datetime DEFAULT NULL,
  `pqo_request_data` json DEFAULT NULL,
  PRIMARY KEY (`pqo_id`),
  KEY `IND-product_quote_option-pqo_status_id` (`pqo_status_id`),
  KEY `IND-product_quote_option-pqo_created_user_id` (`pqo_created_user_id`),
  KEY `FK-product_quote_option-pqo_product_quote_id` (`pqo_product_quote_id`),
  KEY `FK-product_quote_option-pqo_product_option_id` (`pqo_product_option_id`),
  KEY `FK-product_quote_option-pqo_updated_user_id` (`pqo_updated_user_id`),
  CONSTRAINT `FK-product_quote_option-pqo_created_user_id` FOREIGN KEY (`pqo_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_option-pqo_product_option_id` FOREIGN KEY (`pqo_product_option_id`) REFERENCES `product_option` (`po_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_option-pqo_product_quote_id` FOREIGN KEY (`pqo_product_quote_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_option-pqo_updated_user_id` FOREIGN KEY (`pqo_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_quote_option_refund`
--

DROP TABLE IF EXISTS `product_quote_option_refund`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_quote_option_refund` (
  `pqor_id` int NOT NULL AUTO_INCREMENT,
  `pqor_product_quote_refund_id` int NOT NULL,
  `pqor_product_quote_option_id` int DEFAULT NULL,
  `pqor_selling_price` decimal(8,2) DEFAULT NULL,
  `pqor_penalty_amount` decimal(8,2) DEFAULT NULL,
  `pqor_processing_fee_amount` decimal(8,2) DEFAULT NULL,
  `pqor_refund_amount` decimal(8,2) DEFAULT NULL,
  `pqor_status_id` tinyint DEFAULT NULL,
  `pqor_client_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pqor_client_currency_rate` decimal(8,2) DEFAULT NULL,
  `pqor_client_selling_price` decimal(8,2) DEFAULT NULL,
  `pqor_client_refund_amount` decimal(8,2) DEFAULT NULL,
  `pqor_created_user_id` int DEFAULT NULL,
  `pqor_updated_user_id` int DEFAULT NULL,
  `pqor_created_dt` datetime DEFAULT NULL,
  `pqor_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`pqor_id`),
  KEY `FK-product_quote_option_refund-pqor_product_quote_refund_id` (`pqor_product_quote_refund_id`),
  KEY `FK-product_quote_option_refund-pqor_product_quote_option_id` (`pqor_product_quote_option_id`),
  KEY `FK-product_quote_option_refund-pqor_created_user_id` (`pqor_created_user_id`),
  KEY `FK-product_quote_option_refund-pqor_updated_user_id` (`pqor_updated_user_id`),
  KEY `FK-product_quote_option_refund-pqor_client_currency` (`pqor_client_currency`),
  KEY `IND-product_quote_option_refund-pqor_status_id` (`pqor_status_id`),
  CONSTRAINT `FK-product_quote_option_refund-pqor_client_currency` FOREIGN KEY (`pqor_client_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_option_refund-pqor_created_user_id` FOREIGN KEY (`pqor_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_option_refund-pqor_product_quote_option_id` FOREIGN KEY (`pqor_product_quote_option_id`) REFERENCES `product_quote_option` (`pqo_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_option_refund-pqor_product_quote_refund_id` FOREIGN KEY (`pqor_product_quote_refund_id`) REFERENCES `product_quote_refund` (`pqr_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_option_refund-pqor_updated_user_id` FOREIGN KEY (`pqor_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_quote_origin`
--

DROP TABLE IF EXISTS `product_quote_origin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_quote_origin` (
  `pqo_product_id` int NOT NULL,
  `pqo_quote_id` int NOT NULL,
  PRIMARY KEY (`pqo_product_id`,`pqo_quote_id`),
  KEY `FK-product_quote_origin-pqo_quote_id` (`pqo_quote_id`),
  CONSTRAINT `FK-product_quote_origin-pqo_product_id` FOREIGN KEY (`pqo_product_id`) REFERENCES `product` (`pr_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_origin-pqo_quote_id` FOREIGN KEY (`pqo_quote_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_quote_refund`
--

DROP TABLE IF EXISTS `product_quote_refund`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_quote_refund` (
  `pqr_id` int NOT NULL AUTO_INCREMENT,
  `pqr_order_refund_id` int NOT NULL,
  `pqr_product_quote_id` int NOT NULL,
  `pqr_selling_price` decimal(8,2) DEFAULT NULL,
  `pqr_penalty_amount` decimal(8,2) DEFAULT NULL,
  `pqr_processing_fee_amount` decimal(8,2) DEFAULT NULL,
  `pqr_refund_amount` decimal(8,2) DEFAULT NULL,
  `pqr_status_id` tinyint DEFAULT NULL,
  `pqr_client_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pqr_client_currency_rate` decimal(8,2) DEFAULT NULL,
  `pqr_client_selling_price` decimal(8,2) DEFAULT NULL,
  `pqr_client_refund_amount` decimal(8,2) DEFAULT NULL,
  `pqr_created_user_id` int DEFAULT NULL,
  `pqr_updated_user_id` int DEFAULT NULL,
  `pqr_created_dt` datetime DEFAULT NULL,
  `pqr_updated_dt` datetime DEFAULT NULL,
  `pqr_case_id` int DEFAULT NULL,
  PRIMARY KEY (`pqr_id`),
  KEY `FK-product_quote_refund-pqr_order_refund_id` (`pqr_order_refund_id`),
  KEY `FK-product_quote_refund-pqr_client_currency` (`pqr_client_currency`),
  KEY `FK-product_quote_refund-pqr_created_user_id` (`pqr_created_user_id`),
  KEY `FK-product_quote_refund-pqr_updated_user_id` (`pqr_updated_user_id`),
  KEY `FK-product_quote_refund-pqr_product_quote_id` (`pqr_product_quote_id`),
  KEY `IND-product_quote_refund-pqr_status_id` (`pqr_status_id`),
  KEY `FK-product_quote_refund-case` (`pqr_case_id`),
  CONSTRAINT `FK-product_quote_refund-case` FOREIGN KEY (`pqr_case_id`) REFERENCES `cases` (`cs_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_refund-pqr_client_currency` FOREIGN KEY (`pqr_client_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_refund-pqr_created_user_id` FOREIGN KEY (`pqr_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_refund-pqr_order_refund_id` FOREIGN KEY (`pqr_order_refund_id`) REFERENCES `order_refund` (`orr_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_refund-pqr_product_quote_id` FOREIGN KEY (`pqr_product_quote_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_refund-pqr_updated_user_id` FOREIGN KEY (`pqr_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_quote_relation`
--

DROP TABLE IF EXISTS `product_quote_relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_quote_relation` (
  `pqr_parent_pq_id` int NOT NULL,
  `pqr_related_pq_id` int NOT NULL,
  `pqr_type_id` tinyint NOT NULL COMMENT '1 - replace, 2 - clone',
  `pqr_created_user_id` int DEFAULT NULL,
  `pqr_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`pqr_parent_pq_id`,`pqr_related_pq_id`,`pqr_type_id`),
  KEY `FK-product_quote_relation-pqr_related_pq_id` (`pqr_related_pq_id`),
  KEY `FK-product_quote_relation-pqr_created_user_id` (`pqr_created_user_id`),
  CONSTRAINT `FK-product_quote_relation-pqr_created_user_id` FOREIGN KEY (`pqr_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_relation-pqr_parent_pq_id` FOREIGN KEY (`pqr_parent_pq_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_relation-pqr_related_pq_id` FOREIGN KEY (`pqr_related_pq_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_quote_status_log`
--

DROP TABLE IF EXISTS `product_quote_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_quote_status_log` (
  `pqsl_id` int NOT NULL AUTO_INCREMENT,
  `pqsl_product_quote_id` int NOT NULL,
  `pqsl_start_status_id` tinyint DEFAULT NULL,
  `pqsl_end_status_id` tinyint NOT NULL,
  `pqsl_start_dt` datetime NOT NULL,
  `pqsl_end_dt` datetime DEFAULT NULL,
  `pqsl_duration` int DEFAULT NULL,
  `pqsl_description` varchar(700) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pqsl_owner_user_id` int DEFAULT NULL,
  `pqsl_created_user_id` int DEFAULT NULL,
  `pqsl_action_id` tinyint DEFAULT NULL,
  PRIMARY KEY (`pqsl_id`),
  KEY `FK-product_quote_status_log_pqsl_product_quote_id` (`pqsl_product_quote_id`),
  KEY `FK-product_quote_status_log_pqsl_owner_user_id` (`pqsl_owner_user_id`),
  KEY `FK-product_quote_status_log_pqsl_created_user_id` (`pqsl_created_user_id`),
  CONSTRAINT `FK-product_quote_status_log_pqsl_created_user_id` FOREIGN KEY (`pqsl_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_status_log_pqsl_owner_user_id` FOREIGN KEY (`pqsl_owner_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-product_quote_status_log_pqsl_product_quote_id` FOREIGN KEY (`pqsl_product_quote_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_type`
--

DROP TABLE IF EXISTS `product_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_type` (
  `pt_id` int NOT NULL,
  `pt_key` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pt_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pt_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pt_settings` json DEFAULT NULL,
  `pt_enabled` tinyint(1) DEFAULT '0',
  `pt_service_fee_percent` decimal(5,2) DEFAULT NULL,
  `pt_created_dt` datetime DEFAULT NULL,
  `pt_updated_dt` datetime DEFAULT NULL,
  `pt_sort_order` smallint DEFAULT '1',
  `pt_icon_class` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`pt_id`),
  UNIQUE KEY `pt_id` (`pt_id`),
  UNIQUE KEY `pt_key` (`pt_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `product_type_payment_method`
--

DROP TABLE IF EXISTS `product_type_payment_method`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_type_payment_method` (
  `ptpm_produt_type_id` int NOT NULL,
  `ptpm_payment_method_id` int NOT NULL,
  `ptpm_payment_fee_percent` decimal(5,2) DEFAULT NULL,
  `ptpm_payment_fee_amount` decimal(8,2) DEFAULT NULL,
  `ptpm_enabled` tinyint(1) DEFAULT '0',
  `ptpm_default` tinyint(1) DEFAULT '0',
  `ptpm_created_user_id` int DEFAULT NULL,
  `ptpm_updated_user_id` int DEFAULT NULL,
  `ptpm_created_dt` datetime DEFAULT NULL,
  `ptpm_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ptpm_produt_type_id`,`ptpm_payment_method_id`),
  KEY `fk-product_type_payment_method-payment_method_id` (`ptpm_payment_method_id`),
  KEY `fk-product_type_payment_method-created_user_id` (`ptpm_created_user_id`),
  KEY `fk-product_type_payment_method-updated_user_id` (`ptpm_updated_user_id`),
  CONSTRAINT `fk-product_type_payment_method-created_user_id` FOREIGN KEY (`ptpm_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-product_type_payment_method-payment_method_id` FOREIGN KEY (`ptpm_payment_method_id`) REFERENCES `payment_method` (`pm_id`) ON DELETE CASCADE,
  CONSTRAINT `fk-product_type_payment_method-product_type_id` FOREIGN KEY (`ptpm_produt_type_id`) REFERENCES `product_type` (`pt_id`) ON DELETE CASCADE,
  CONSTRAINT `fk-product_type_payment_method-updated_user_id` FOREIGN KEY (`ptpm_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profit_bonus`
--

DROP TABLE IF EXISTS `profit_bonus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profit_bonus` (
  `pb_id` int NOT NULL AUTO_INCREMENT,
  `pb_user_id` int NOT NULL,
  `pb_min_profit` int NOT NULL,
  `pb_bonus` int NOT NULL,
  `pb_updated_dt` datetime DEFAULT NULL,
  `pb_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`pb_id`),
  UNIQUE KEY `uniq_idx_user_profit` (`pb_user_id`,`pb_min_profit`),
  KEY `fk-pb-updated_by` (`pb_updated_user_id`),
  CONSTRAINT `fk-pb-updated_by` FOREIGN KEY (`pb_updated_user_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk-pb-user` FOREIGN KEY (`pb_user_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `profit_split`
--

DROP TABLE IF EXISTS `profit_split`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profit_split` (
  `ps_id` int NOT NULL AUTO_INCREMENT,
  `ps_lead_id` int NOT NULL,
  `ps_user_id` int NOT NULL,
  `ps_percent` int DEFAULT NULL,
  `ps_amount` int DEFAULT NULL,
  `ps_updated_dt` datetime DEFAULT NULL,
  `ps_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`ps_id`),
  UNIQUE KEY `uniq_idx_user_profit_split` (`ps_user_id`,`ps_lead_id`),
  KEY `fk-ps-updated_by` (`ps_updated_user_id`),
  KEY `fk-ps-lead` (`ps_lead_id`),
  CONSTRAINT `fk-ps-lead` FOREIGN KEY (`ps_lead_id`) REFERENCES `leads` (`id`),
  CONSTRAINT `fk-ps-updated_by` FOREIGN KEY (`ps_updated_user_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk-ps-user` FOREIGN KEY (`ps_user_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=939 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_email_templates`
--

DROP TABLE IF EXISTS `project_email_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_email_templates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `template` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `layout_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk-project_email_templates-projects` (`project_id`),
  CONSTRAINT `fk-project_email_templates-projects` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_employee_access`
--

DROP TABLE IF EXISTS `project_employee_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_employee_access` (
  `employee_id` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  KEY `fk-project_employee_access-projects` (`project_id`),
  KEY `fk-project_employee_access-employees` (`employee_id`),
  CONSTRAINT `fk-project_employee_access-employees` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-project_employee_access-projects` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_locale`
--

DROP TABLE IF EXISTS `project_locale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_locale` (
  `pl_project_id` int NOT NULL,
  `pl_language_id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pl_default` tinyint(1) DEFAULT '0',
  `pl_enabled` tinyint(1) DEFAULT '1',
  `pl_params` json DEFAULT NULL,
  `pl_created_user_id` int DEFAULT NULL,
  `pl_updated_user_id` int DEFAULT NULL,
  `pl_created_dt` datetime DEFAULT NULL,
  `pl_updated_dt` datetime DEFAULT NULL,
  `pl_id` int NOT NULL AUTO_INCREMENT,
  `pl_market_country` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`pl_id`),
  KEY `IDX-project_locale-pl_enabled` (`pl_enabled`),
  KEY `IDX-project_locale-pl_default` (`pl_default`),
  KEY `FK-project_locale-pl_created_user_id` (`pl_created_user_id`),
  KEY `FK-project_locale-pl_updated_user_id` (`pl_updated_user_id`),
  KEY `FK-project_locale-pl_language_id` (`pl_language_id`),
  KEY `IND-project_locale` (`pl_project_id`,`pl_language_id`,`pl_market_country`),
  CONSTRAINT `FK-project_locale-pl_created_user_id` FOREIGN KEY (`pl_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-project_locale-pl_language_id` FOREIGN KEY (`pl_language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-project_locale-pl_project_id` FOREIGN KEY (`pl_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-project_locale-pl_updated_user_id` FOREIGN KEY (`pl_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_relation`
--

DROP TABLE IF EXISTS `project_relation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_relation` (
  `prl_project_id` int NOT NULL,
  `prl_related_project_id` int NOT NULL,
  `prl_created_user_id` int DEFAULT NULL,
  `prl_updated_user_id` int DEFAULT NULL,
  `prl_created_dt` datetime DEFAULT NULL,
  `prl_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`prl_project_id`,`prl_related_project_id`),
  KEY `FK-project_relation-prl_related_project_id-id` (`prl_related_project_id`),
  CONSTRAINT `FK-project_relation-prl_project_id-id` FOREIGN KEY (`prl_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-project_relation-prl_related_project_id-id` FOREIGN KEY (`prl_related_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_weight`
--

DROP TABLE IF EXISTS `project_weight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_weight` (
  `pw_project_id` int NOT NULL,
  `pw_weight` int DEFAULT '0',
  PRIMARY KEY (`pw_project_id`),
  CONSTRAINT `FK-project_weight_pw_weight` FOREIGN KEY (`pw_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_info` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `closed` tinyint(1) DEFAULT '0',
  `last_update` datetime DEFAULT CURRENT_TIMESTAMP,
  `sort_order` tinyint DEFAULT '0',
  `email_postfix` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ga_tracking_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `project_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `p_update_user_id` int DEFAULT NULL,
  `p_params_json` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_key` (`project_key`),
  KEY `IDX-projects-sort_order` (`sort_order`),
  KEY `IND-projects-project_key` (`project_key`),
  KEY `FK-projects-p_update_user_id` (`p_update_user_id`),
  CONSTRAINT `FK-projects-p_update_user_id` FOREIGN KEY (`p_update_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `qa_task`
--

DROP TABLE IF EXISTS `qa_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qa_task` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `t_gid` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `t_project_id` int DEFAULT NULL,
  `t_object_type_id` tinyint NOT NULL,
  `t_object_id` int NOT NULL,
  `t_category_id` int DEFAULT NULL,
  `t_status_id` tinyint NOT NULL,
  `t_rating` tinyint(1) DEFAULT NULL,
  `t_create_type_id` tinyint DEFAULT NULL,
  `t_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `t_department_id` tinyint(1) DEFAULT NULL,
  `t_deadline_dt` datetime DEFAULT NULL,
  `t_assigned_user_id` int DEFAULT NULL,
  `t_created_user_id` int DEFAULT NULL,
  `t_updated_user_id` int DEFAULT NULL,
  `t_created_dt` datetime DEFAULT NULL,
  `t_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`t_id`),
  UNIQUE KEY `t_gid` (`t_gid`),
  KEY `FK-qa_task-t_assigned_user_id` (`t_assigned_user_id`),
  KEY `FK-qa_task-t_created_user_id` (`t_created_user_id`),
  KEY `FK-qa_task-t_updated_user_id` (`t_updated_user_id`),
  KEY `FK-qa_task-t_category_id` (`t_category_id`),
  KEY `FK-qa_task-t_project_id` (`t_project_id`),
  CONSTRAINT `FK-qa_task-t_assigned_user_id` FOREIGN KEY (`t_assigned_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-qa_task-t_category_id` FOREIGN KEY (`t_category_id`) REFERENCES `qa_task_category` (`tc_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-qa_task-t_created_user_id` FOREIGN KEY (`t_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-qa_task-t_project_id` FOREIGN KEY (`t_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-qa_task-t_updated_user_id` FOREIGN KEY (`t_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `qa_task_action_reason`
--

DROP TABLE IF EXISTS `qa_task_action_reason`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qa_task_action_reason` (
  `tar_id` int NOT NULL AUTO_INCREMENT,
  `tar_object_type_id` tinyint NOT NULL,
  `tar_action_id` tinyint NOT NULL,
  `tar_key` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tar_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tar_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tar_comment_required` tinyint(1) NOT NULL DEFAULT '0',
  `tar_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `tar_created_user_id` int DEFAULT NULL,
  `tar_updated_user_id` int DEFAULT NULL,
  `tar_created_dt` datetime DEFAULT NULL,
  `tar_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`tar_id`),
  UNIQUE KEY `tsr_key` (`tar_key`),
  UNIQUE KEY `idx-unique-qa_task_action_reason-object-action-name` (`tar_object_type_id`,`tar_action_id`,`tar_name`),
  KEY `FK-qa_task_action_reason-tar_created_user_id` (`tar_created_user_id`),
  KEY `FK-qa_task_action_reason-tar_updated_user_id` (`tar_updated_user_id`),
  CONSTRAINT `FK-qa_task_action_reason-tar_created_user_id` FOREIGN KEY (`tar_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-qa_task_action_reason-tar_updated_user_id` FOREIGN KEY (`tar_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `qa_task_category`
--

DROP TABLE IF EXISTS `qa_task_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qa_task_category` (
  `tc_id` int NOT NULL AUTO_INCREMENT,
  `tc_key` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tc_object_type_id` tinyint NOT NULL,
  `tc_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tc_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tc_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `tc_default` tinyint(1) NOT NULL DEFAULT '0',
  `tc_created_user_id` int DEFAULT NULL,
  `tc_updated_user_id` int DEFAULT NULL,
  `tc_created_dt` datetime DEFAULT NULL,
  `tc_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`tc_id`),
  UNIQUE KEY `tc_key` (`tc_key`),
  KEY `FK-qa_task_category-tc_created_user_id` (`tc_created_user_id`),
  KEY `FK-qa_task_category-tc_updated_user_id` (`tc_updated_user_id`),
  CONSTRAINT `FK-qa_task_category-tc_created_user_id` FOREIGN KEY (`tc_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-qa_task_category-tc_updated_user_id` FOREIGN KEY (`tc_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `qa_task_rules`
--

DROP TABLE IF EXISTS `qa_task_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qa_task_rules` (
  `tr_id` int NOT NULL AUTO_INCREMENT,
  `tr_key` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tr_type` tinyint NOT NULL,
  `tr_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tr_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tr_parameters` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `tr_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `tr_created_user_id` int DEFAULT NULL,
  `tr_updated_user_id` int DEFAULT NULL,
  `tr_created_dt` datetime DEFAULT NULL,
  `tr_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`tr_id`),
  UNIQUE KEY `tr_key` (`tr_key`),
  KEY `FK-qa_task_rules-tr_created_user_id` (`tr_created_user_id`),
  KEY `FK-qa_task_rules-tr_updated_user_id` (`tr_updated_user_id`),
  CONSTRAINT `FK-qa_task_rules-tr_created_user_id` FOREIGN KEY (`tr_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-qa_task_rules-tr_updated_user_id` FOREIGN KEY (`tr_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `qa_task_status`
--

DROP TABLE IF EXISTS `qa_task_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qa_task_status` (
  `ts_id` tinyint NOT NULL,
  `ts_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ts_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `ts_css_class` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ts_created_user_id` int DEFAULT NULL,
  `ts_updated_user_id` int DEFAULT NULL,
  `ts_created_dt` datetime DEFAULT NULL,
  `ts_updated_dt` datetime DEFAULT NULL,
  UNIQUE KEY `ts_id` (`ts_id`),
  KEY `FK-qa_task_status-ts_created_user_id` (`ts_created_user_id`),
  KEY `FK-qa_task_status-ts_updated_user_id` (`ts_updated_user_id`),
  CONSTRAINT `FK-qa_task_status-ts_created_user_id` FOREIGN KEY (`ts_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-qa_task_status-ts_updated_user_id` FOREIGN KEY (`ts_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `qa_task_status_log`
--

DROP TABLE IF EXISTS `qa_task_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qa_task_status_log` (
  `tsl_id` int NOT NULL AUTO_INCREMENT,
  `tsl_task_id` int NOT NULL,
  `tsl_start_status_id` int DEFAULT NULL,
  `tsl_end_status_id` int NOT NULL,
  `tsl_start_dt` datetime NOT NULL,
  `tsl_end_dt` datetime DEFAULT NULL,
  `tsl_duration` int DEFAULT NULL,
  `tsl_reason_id` int DEFAULT NULL,
  `tsl_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tsl_assigned_user_id` int DEFAULT NULL,
  `tsl_created_user_id` int DEFAULT NULL,
  `tsl_action_id` int DEFAULT NULL,
  PRIMARY KEY (`tsl_id`),
  KEY `FK-qa_task_status_log-tsl_task_id` (`tsl_task_id`),
  KEY `FK-qa_task_status_log-tsl_assigned_user_id` (`tsl_assigned_user_id`),
  KEY `FK-qa_task_status_log-tsl_created_user_id` (`tsl_created_user_id`),
  CONSTRAINT `FK-qa_task_status_log-tsl_assigned_user_id` FOREIGN KEY (`tsl_assigned_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-qa_task_status_log-tsl_created_user_id` FOREIGN KEY (`tsl_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-qa_task_status_log-tsl_task_id` FOREIGN KEY (`tsl_task_id`) REFERENCES `qa_task` (`t_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `qcall_config`
--

DROP TABLE IF EXISTS `qcall_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `qcall_config` (
  `qc_status_id` int NOT NULL,
  `qc_call_att` int NOT NULL,
  `qc_client_time_enable` tinyint(1) DEFAULT '0',
  `qc_time_from` int NOT NULL,
  `qc_time_to` int NOT NULL,
  `qc_created_dt` datetime DEFAULT NULL,
  `qc_updated_dt` datetime DEFAULT NULL,
  `qc_created_user_id` int DEFAULT NULL,
  `qc_updated_user_id` int DEFAULT NULL,
  `qc_phone_switch` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`qc_status_id`,`qc_call_att`),
  KEY `FK-qcall_config_qc_created_user_id` (`qc_created_user_id`),
  KEY `FK-qcall_config_qc_updated_user_id` (`qc_updated_user_id`),
  CONSTRAINT `FK-qcall_config_qc_created_user_id` FOREIGN KEY (`qc_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-qcall_config_qc_updated_user_id` FOREIGN KEY (`qc_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quote_label`
--

DROP TABLE IF EXISTS `quote_label`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quote_label` (
  `ql_quote_id` int NOT NULL,
  `ql_label_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ql_quote_id`,`ql_label_key`),
  CONSTRAINT `FK-quote_label-quote_id` FOREIGN KEY (`ql_quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quote_price`
--

DROP TABLE IF EXISTS `quote_price`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quote_price` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quote_id` int DEFAULT NULL,
  `passenger_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selling` float DEFAULT '0',
  `net` float DEFAULT '0',
  `fare` float DEFAULT '0',
  `taxes` float DEFAULT '0',
  `mark_up` float DEFAULT '0',
  `extra_mark_up` float DEFAULT '0',
  `service_fee` float DEFAULT '0',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL,
  `uid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk-quote_price-quotes` (`quote_id`),
  KEY `IND-quote_price_uid` (`uid`),
  CONSTRAINT `fk-quote_price-quotes` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1735172 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quote_segment`
--

DROP TABLE IF EXISTS `quote_segment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quote_segment` (
  `qs_id` int NOT NULL AUTO_INCREMENT,
  `qs_departure_time` datetime DEFAULT NULL,
  `qs_arrival_time` datetime DEFAULT NULL,
  `qs_stop` int DEFAULT NULL,
  `qs_flight_number` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qs_booking_class` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qs_duration` int DEFAULT NULL,
  `qs_departure_airport_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qs_departure_airport_terminal` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qs_arrival_airport_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qs_arrival_airport_terminal` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qs_operating_airline` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qs_marketing_airline` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qs_air_equip_type` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qs_marriage_group` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qs_mileage` int DEFAULT NULL,
  `qs_cabin` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qs_cabin_basic` tinyint(1) DEFAULT '0',
  `qs_meal` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qs_fare_code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qs_trip_id` int DEFAULT NULL,
  `qs_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qs_created_dt` datetime DEFAULT CURRENT_TIMESTAMP,
  `qs_updated_dt` datetime DEFAULT NULL,
  `qs_updated_user_id` int DEFAULT NULL,
  `qs_ticket_id` smallint DEFAULT NULL,
  `qs_recheck_baggage` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`qs_id`),
  KEY `fk_quote_segment_trip` (`qs_trip_id`),
  KEY `fk_updated_user` (`qs_updated_user_id`),
  KEY `fk-quote_segment_departure` (`qs_departure_airport_code`),
  KEY `fk-quote_segment_arrival` (`qs_arrival_airport_code`),
  CONSTRAINT `fk-quote_segment_arrival` FOREIGN KEY (`qs_arrival_airport_code`) REFERENCES `airports` (`iata`),
  CONSTRAINT `fk-quote_segment_departure` FOREIGN KEY (`qs_departure_airport_code`) REFERENCES `airports` (`iata`),
  CONSTRAINT `fk_quote_segment_trip` FOREIGN KEY (`qs_trip_id`) REFERENCES `quote_trip` (`qt_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_updated_user` FOREIGN KEY (`qs_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3533180 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quote_segment_baggage`
--

DROP TABLE IF EXISTS `quote_segment_baggage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quote_segment_baggage` (
  `qsb_id` int NOT NULL AUTO_INCREMENT,
  `qsb_pax_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsb_segment_id` int DEFAULT NULL,
  `qsb_airline_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsb_allow_pieces` int DEFAULT NULL,
  `qsb_allow_weight` int DEFAULT NULL,
  `qsb_allow_unit` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsb_allow_max_weight` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsb_allow_max_size` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsb_created_dt` datetime DEFAULT CURRENT_TIMESTAMP,
  `qsb_updated_dt` datetime DEFAULT NULL,
  `qsb_updated_user_id` int DEFAULT NULL,
  `qsb_carry_one` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`qsb_id`),
  KEY `fk_segment_baggage_updated_user` (`qsb_updated_user_id`),
  KEY `fk_quote_segment_baggage` (`qsb_segment_id`),
  CONSTRAINT `fk_quote_segment_baggage` FOREIGN KEY (`qsb_segment_id`) REFERENCES `quote_segment` (`qs_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_segment_baggage_updated_user` FOREIGN KEY (`qsb_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3467234 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quote_segment_baggage_charge`
--

DROP TABLE IF EXISTS `quote_segment_baggage_charge`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quote_segment_baggage_charge` (
  `qsbc_id` int NOT NULL AUTO_INCREMENT,
  `qsbc_pax_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsbc_segment_id` int DEFAULT NULL,
  `qsbc_first_piece` int DEFAULT NULL,
  `qsbc_last_piece` int DEFAULT NULL,
  `qsbc_price` float DEFAULT NULL,
  `qsbc_currency` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsbc_max_weight` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsbc_max_size` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qsbc_created_dt` datetime DEFAULT CURRENT_TIMESTAMP,
  `qsbc_updated_dt` datetime DEFAULT NULL,
  `qsbc_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`qsbc_id`),
  KEY `fk_segment_baggage_charge_updated_user` (`qsbc_updated_user_id`),
  KEY `fk_quote_segment_baggage_charge` (`qsbc_segment_id`),
  CONSTRAINT `fk_quote_segment_baggage_charge` FOREIGN KEY (`qsbc_segment_id`) REFERENCES `quote_segment` (`qs_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_segment_baggage_charge_updated_user` FOREIGN KEY (`qsbc_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2314424 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quote_segment_stop`
--

DROP TABLE IF EXISTS `quote_segment_stop`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quote_segment_stop` (
  `qss_id` int NOT NULL AUTO_INCREMENT,
  `qss_location_code` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qss_departure_dt` datetime DEFAULT NULL,
  `qss_arrival_dt` datetime DEFAULT NULL,
  `qss_duration` int DEFAULT NULL,
  `qss_elapsed_time` int DEFAULT NULL,
  `qss_equipment` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qss_segment_id` int DEFAULT NULL,
  PRIMARY KEY (`qss_id`),
  KEY `fk_quote_segment_stops` (`qss_segment_id`),
  CONSTRAINT `fk_quote_segment_stops` FOREIGN KEY (`qss_segment_id`) REFERENCES `quote_segment` (`qs_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14671 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quote_status_log`
--

DROP TABLE IF EXISTS `quote_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quote_status_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int DEFAULT NULL,
  `quote_id` int DEFAULT NULL,
  `status` int DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk-quote_status_log-employee` (`employee_id`),
  KEY `idx-quote_status_log-status` (`status`,`quote_id`),
  KEY `fk-quote_status_log-quote` (`quote_id`),
  CONSTRAINT `fk-quote_status_log-employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk-quote_status_log-quote` FOREIGN KEY (`quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1859981 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quote_trip`
--

DROP TABLE IF EXISTS `quote_trip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quote_trip` (
  `qt_id` int NOT NULL AUTO_INCREMENT,
  `qt_duration` int DEFAULT NULL,
  `qt_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qt_quote_id` int DEFAULT NULL,
  PRIMARY KEY (`qt_id`),
  KEY `fk_quote_trip_quotes` (`qt_quote_id`),
  CONSTRAINT `fk_quote_trip_quotes` FOREIGN KEY (`qt_quote_id`) REFERENCES `quotes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1735815 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `quotes`
--

DROP TABLE IF EXISTS `quotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `quotes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `uid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lead_id` int DEFAULT NULL,
  `employee_id` int DEFAULT NULL,
  `record_locator` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pcc` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cabin` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gds` varchar(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trip_type` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `main_airline_code` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reservation_dump` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int DEFAULT NULL,
  `check_payment` tinyint(1) DEFAULT NULL,
  `fare_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime NOT NULL,
  `created_by_seller` tinyint(1) DEFAULT '1',
  `employee_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_ticket_date` datetime DEFAULT NULL,
  `service_fee_percent` float DEFAULT NULL,
  `pricing_info` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `type_id` tinyint(1) DEFAULT '0',
  `tickets` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `origin_search_data` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `gds_offer_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent_processing_fee` float DEFAULT NULL,
  `provider_project_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk-quotes-leads` (`lead_id`),
  KEY `fk-quotes-employees` (`employee_id`),
  KEY `tbl_quotes_status_ind` (`status`),
  KEY `IND-uid_quotes` (`uid`),
  KEY `IND-quotes-created` (`created`),
  KEY `FK-quotes-provider_project_id` (`provider_project_id`),
  CONSTRAINT `fk-quotes-employees` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk-quotes-leads` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-quotes-provider_project_id` FOREIGN KEY (`provider_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=916860 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rent_car`
--

DROP TABLE IF EXISTS `rent_car`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rent_car` (
  `prc_id` int NOT NULL AUTO_INCREMENT,
  `prc_product_id` int NOT NULL,
  `prc_pick_up_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prc_drop_off_code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prc_request_hash_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prc_pick_up_date` date DEFAULT NULL,
  `prc_drop_off_date` date DEFAULT NULL,
  `prc_pick_up_time` time DEFAULT NULL,
  `prc_drop_off_time` time DEFAULT NULL,
  `prc_created_dt` datetime DEFAULT NULL,
  `prc_updated_dt` datetime DEFAULT NULL,
  `prc_created_user_id` int DEFAULT NULL,
  `prc_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`prc_id`),
  KEY `FK-rent_car-prc_product_id` (`prc_product_id`),
  CONSTRAINT `FK-rent_car-prc_product_id` FOREIGN KEY (`prc_product_id`) REFERENCES `product` (`pr_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rent_car_quote`
--

DROP TABLE IF EXISTS `rent_car_quote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rent_car_quote` (
  `rcq_id` int NOT NULL AUTO_INCREMENT,
  `rcq_rent_car_id` int NOT NULL,
  `rcq_product_quote_id` int NOT NULL,
  `rcq_hash_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rcq_json_response` json DEFAULT NULL,
  `rcq_model_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rcq_category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rcq_image_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rcq_vendor_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rcq_vendor_logo_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rcq_transmission` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rcq_seats` int DEFAULT NULL,
  `rcq_doors` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rcq_options` json DEFAULT NULL,
  `rcq_days` int DEFAULT '1',
  `rcq_price_per_day` decimal(10,2) NOT NULL,
  `rcq_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `rcq_advantages` json DEFAULT NULL,
  `rcq_pick_up_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rcq_drop_of_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rcq_offer_token` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rcq_request_hash_key` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rcq_created_dt` datetime DEFAULT NULL,
  `rcq_updated_dt` datetime DEFAULT NULL,
  `rcq_created_user_id` int DEFAULT NULL,
  `rcq_updated_user_id` int DEFAULT NULL,
  `rcq_system_mark_up` decimal(10,2) DEFAULT '0.00',
  `rcq_agent_mark_up` decimal(10,2) DEFAULT '0.00',
  `rcq_service_fee_percent` decimal(10,2) DEFAULT '0.00',
  `rcq_car_reference_id` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `rcq_booking_json` json DEFAULT NULL,
  `rcq_booking_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rcq_contract_request_json` json DEFAULT NULL,
  `rcq_pick_up_dt` datetime DEFAULT NULL,
  `rcq_drop_off_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`rcq_id`),
  UNIQUE KEY `rcq_hash_key` (`rcq_hash_key`),
  KEY `FK-rent_car_quote-rcq_rent_car_id` (`rcq_rent_car_id`),
  KEY `FK-rent_car_quote-rcq_product_quote_id` (`rcq_product_quote_id`),
  CONSTRAINT `FK-rent_car_quote-rcq_product_quote_id` FOREIGN KEY (`rcq_product_quote_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-rent_car_quote-rcq_rent_car_id` FOREIGN KEY (`rcq_rent_car_id`) REFERENCES `rent_car` (`prc_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sale_credit_card`
--

DROP TABLE IF EXISTS `sale_credit_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_credit_card` (
  `scc_sale_id` int NOT NULL,
  `scc_cc_id` int NOT NULL,
  `scc_created_dt` datetime DEFAULT NULL,
  `scc_created_user_id` int DEFAULT NULL,
  PRIMARY KEY (`scc_sale_id`,`scc_cc_id`),
  KEY `FK-sale_credit_card-scc_cc_id` (`scc_cc_id`),
  KEY `FK-sale_credit_card-scc_created_user_id` (`scc_created_user_id`),
  CONSTRAINT `FK-sale_credit_card-scc_cc_id` FOREIGN KEY (`scc_cc_id`) REFERENCES `credit_card` (`cc_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-sale_credit_card-scc_created_user_id` FOREIGN KEY (`scc_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sale_ticket`
--

DROP TABLE IF EXISTS `sale_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_ticket` (
  `st_id` int NOT NULL AUTO_INCREMENT,
  `st_case_id` int NOT NULL,
  `st_case_sale_id` int NOT NULL,
  `st_ticket_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `st_client_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `st_record_locator` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `st_original_fop` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `st_charge_system` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `st_penalty_type` tinyint DEFAULT NULL,
  `st_penalty_amount` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `st_selling` decimal(8,2) DEFAULT NULL,
  `st_service_fee` decimal(8,2) DEFAULT NULL,
  `st_recall_commission` decimal(8,2) DEFAULT NULL,
  `st_markup` decimal(8,2) DEFAULT NULL,
  `st_upfront_charge` decimal(8,2) DEFAULT NULL,
  `st_refundable_amount` decimal(8,2) DEFAULT NULL,
  `st_created_dt` datetime DEFAULT NULL,
  `st_updated_dt` datetime DEFAULT NULL,
  `st_created_user_id` int DEFAULT NULL,
  `st_updated_user_id` int DEFAULT NULL,
  `st_transaction_ids` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`st_id`),
  KEY `FK-st_case_sale_id_st_case_id` (`st_case_id`,`st_case_sale_id`),
  KEY `FK-sale_ticket_st_created_user_id` (`st_created_user_id`),
  KEY `FK-sale_ticket_st_updated_user_id` (`st_updated_user_id`),
  CONSTRAINT `FK-sale_ticket_st_created_user_id` FOREIGN KEY (`st_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-sale_ticket_st_updated_user_id` FOREIGN KEY (`st_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-st_case_id` FOREIGN KEY (`st_case_id`) REFERENCES `cases` (`cs_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-st_case_sale_id_st_case_id` FOREIGN KEY (`st_case_id`, `st_case_sale_id`) REFERENCES `case_sale` (`css_cs_id`, `css_sale_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `setting`
--

DROP TABLE IF EXISTS `setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `setting` (
  `s_id` int NOT NULL AUTO_INCREMENT,
  `s_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `s_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `s_value` varchar(700) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_updated_dt` datetime DEFAULT NULL,
  `s_updated_user_id` int DEFAULT NULL,
  `s_category_id` int DEFAULT NULL,
  `s_description` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`s_id`),
  UNIQUE KEY `s_key` (`s_key`),
  KEY `FK-setting_s_updated_user_id` (`s_updated_user_id`),
  KEY `IND-setting_s_updated_dt` (`s_updated_dt`),
  KEY `FK-setting-setting_category` (`s_category_id`),
  CONSTRAINT `FK-setting-setting_category` FOREIGN KEY (`s_category_id`) REFERENCES `setting_category` (`sc_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-setting_s_updated_user_id` FOREIGN KEY (`s_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=192 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `setting_category`
--

DROP TABLE IF EXISTS `setting_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `setting_category` (
  `sc_id` int NOT NULL AUTO_INCREMENT,
  `sc_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sc_enabled` tinyint(1) DEFAULT '1',
  `sc_created_dt` datetime DEFAULT NULL,
  `sc_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`sc_id`),
  UNIQUE KEY `sc_name` (`sc_name`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shift`
--

DROP TABLE IF EXISTS `shift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shift` (
  `sh_id` int NOT NULL AUTO_INCREMENT,
  `sh_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sh_enabled` tinyint(1) NOT NULL,
  `sh_color` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sh_sort_order` smallint DEFAULT NULL,
  `sh_created_dt` datetime DEFAULT NULL,
  `sh_updated_dt` datetime DEFAULT NULL,
  `sh_created_user_id` int DEFAULT NULL,
  `sh_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`sh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shift_schedule_rule`
--

DROP TABLE IF EXISTS `shift_schedule_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `shift_schedule_rule` (
  `ssr_id` int NOT NULL AUTO_INCREMENT,
  `ssr_shift_id` int NOT NULL,
  `ssr_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ssr_timezone` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ssr_start_time_loc` time NOT NULL,
  `ssr_end_time_loc` time DEFAULT NULL,
  `ssr_duration_time` int DEFAULT NULL,
  `ssr_cron_expression` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ssr_cron_expression_exclude` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ssr_enabled` tinyint(1) NOT NULL,
  `ssr_start_time_utc` time NOT NULL,
  `ssr_end_time_utc` time DEFAULT NULL,
  `ssr_created_dt` datetime DEFAULT NULL,
  `ssr_updated_dt` datetime DEFAULT NULL,
  `ssr_created_user_id` int DEFAULT NULL,
  `ssr_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`ssr_id`),
  KEY `FK-shift_schedule_rule-ssr_shift_id` (`ssr_shift_id`),
  CONSTRAINT `FK-shift_schedule_rule-ssr_shift_id` FOREIGN KEY (`ssr_shift_id`) REFERENCES `shift` (`sh_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms`
--

DROP TABLE IF EXISTS `sms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms` (
  `s_id` int NOT NULL AUTO_INCREMENT,
  `s_reply_id` int DEFAULT NULL,
  `s_lead_id` int DEFAULT NULL,
  `s_project_id` int DEFAULT NULL,
  `s_phone_from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `s_phone_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `s_sms_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `s_sms_data` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `s_type_id` smallint DEFAULT '0',
  `s_template_type_id` int DEFAULT NULL,
  `s_language_id` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_communication_id` int DEFAULT NULL,
  `s_is_deleted` tinyint(1) DEFAULT '0',
  `s_is_new` tinyint(1) DEFAULT '0',
  `s_delay` int DEFAULT NULL,
  `s_priority` tinyint(1) DEFAULT '2',
  `s_status_id` tinyint(1) DEFAULT '1',
  `s_status_done_dt` datetime DEFAULT NULL,
  `s_read_dt` datetime DEFAULT NULL,
  `s_error_message` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_tw_price` decimal(10,5) DEFAULT NULL,
  `s_tw_sent_dt` datetime DEFAULT NULL,
  `s_tw_account_sid` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_tw_message_sid` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_tw_num_segments` smallint DEFAULT '1',
  `s_tw_to_country` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_tw_to_state` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_tw_to_city` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_tw_to_zip` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_tw_from_country` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_tw_from_state` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_tw_from_city` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_tw_from_zip` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `s_created_user_id` int DEFAULT NULL,
  `s_updated_user_id` int DEFAULT NULL,
  `s_created_dt` datetime DEFAULT NULL,
  `s_updated_dt` datetime DEFAULT NULL,
  `s_case_id` int DEFAULT NULL,
  `s_client_id` int DEFAULT NULL,
  PRIMARY KEY (`s_id`),
  KEY `FK-sms_s_project_id` (`s_project_id`),
  KEY `FK-sms_s_language_id` (`s_language_id`),
  KEY `FK-sms_s_template_type_id` (`s_template_type_id`),
  KEY `FK-sms_s_created_user_id` (`s_created_user_id`),
  KEY `FK-sms_s_updated_user_id` (`s_updated_user_id`),
  KEY `FK-sms_s_lead_id` (`s_lead_id`),
  KEY `IND-sms_s_tw_message_sid` (`s_tw_message_sid`),
  KEY `IND-sms_s_communication_id` (`s_communication_id`),
  KEY `IND-sms_s_case_id` (`s_case_id`),
  KEY `FK-sms_s_client_id` (`s_client_id`),
  CONSTRAINT `FK-sms_s_case_id` FOREIGN KEY (`s_case_id`) REFERENCES `cases` (`cs_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-sms_s_client_id` FOREIGN KEY (`s_client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-sms_s_created_user_id` FOREIGN KEY (`s_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-sms_s_language_id` FOREIGN KEY (`s_language_id`) REFERENCES `language` (`language_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-sms_s_lead_id` FOREIGN KEY (`s_lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-sms_s_project_id` FOREIGN KEY (`s_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-sms_s_template_type_id` FOREIGN KEY (`s_template_type_id`) REFERENCES `sms_template_type` (`stp_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-sms_s_updated_user_id` FOREIGN KEY (`s_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=398674 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_distribution_list`
--

DROP TABLE IF EXISTS `sms_distribution_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_distribution_list` (
  `sdl_id` int NOT NULL AUTO_INCREMENT,
  `sdl_com_id` int DEFAULT NULL,
  `sdl_project_id` int DEFAULT NULL,
  `sdl_phone_from` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sdl_phone_to` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sdl_client_id` int DEFAULT NULL,
  `sdl_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sdl_start_dt` datetime DEFAULT NULL,
  `sdl_end_dt` datetime DEFAULT NULL,
  `sdl_status_id` tinyint DEFAULT NULL,
  `sdl_priority` smallint DEFAULT '0',
  `sdl_error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `sdl_message_sid` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sdl_num_segments` smallint DEFAULT NULL,
  `sdl_price` decimal(5,2) DEFAULT NULL,
  `sdl_created_user_id` int DEFAULT NULL,
  `sdl_updated_user_id` int DEFAULT NULL,
  `sdl_created_dt` datetime DEFAULT NULL,
  `sdl_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`sdl_id`),
  KEY `FK-sms_distribution_list-sdl_client_id` (`sdl_client_id`),
  KEY `FK-sms_distribution_list-sdl_project_id` (`sdl_project_id`),
  KEY `FK-sms_distribution_list-sdl_created_user_id` (`sdl_created_user_id`),
  KEY `FK-sms_distribution_list-sdl_updated_user_id` (`sdl_updated_user_id`),
  KEY `IND-sms_distribution_list-sdl_status_id` (`sdl_status_id`),
  KEY `IND-sms_distribution_list-sdl_phone_from` (`sdl_phone_from`),
  KEY `IND-sms_distribution_list-sdl_phone_to` (`sdl_phone_to`),
  KEY `IND-sms_distribution_list-sdl_created_dt` (`sdl_created_dt`),
  KEY `IND-sms_distribution_list-sdl_priority` (`sdl_priority`),
  KEY `IND-sms_distribution_list-sdl_start_dt` (`sdl_start_dt`),
  KEY `IND-sms_distribution_list-sdl_end_dt` (`sdl_end_dt`),
  CONSTRAINT `FK-sms_distribution_list-sdl_client_id` FOREIGN KEY (`sdl_client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-sms_distribution_list-sdl_created_user_id` FOREIGN KEY (`sdl_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-sms_distribution_list-sdl_project_id` FOREIGN KEY (`sdl_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `FK-sms_distribution_list-sdl_updated_user_id` FOREIGN KEY (`sdl_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20970 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sms_template_type`
--

DROP TABLE IF EXISTS `sms_template_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sms_template_type` (
  `stp_id` int NOT NULL AUTO_INCREMENT,
  `stp_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `stp_origin_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `stp_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `stp_hidden` tinyint(1) DEFAULT '0',
  `stp_created_user_id` int DEFAULT NULL,
  `stp_updated_user_id` int DEFAULT NULL,
  `stp_created_dt` datetime DEFAULT NULL,
  `stp_updated_dt` datetime DEFAULT NULL,
  `stp_dep_id` int DEFAULT NULL,
  PRIMARY KEY (`stp_id`),
  UNIQUE KEY `stp_key` (`stp_key`),
  KEY `FK-sms_template_type_stp_created_user_id` (`stp_created_user_id`),
  KEY `FK-sms_template_type_stp_updated_user_id` (`stp_updated_user_id`),
  KEY `FK-sms_template_type_stp_dep_id` (`stp_dep_id`),
  CONSTRAINT `FK-sms_template_type_stp_created_user_id` FOREIGN KEY (`stp_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-sms_template_type_stp_dep_id` FOREIGN KEY (`stp_dep_id`) REFERENCES `department` (`dep_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-sms_template_type_stp_updated_user_id` FOREIGN KEY (`stp_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sources`
--

DROP TABLE IF EXISTS `sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sources` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int DEFAULT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_update` datetime DEFAULT CURRENT_TIMESTAMP,
  `default` tinyint(1) DEFAULT '0',
  `hidden` tinyint(1) DEFAULT '0',
  `rule` int DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk-sources-projects` (`project_id`),
  CONSTRAINT `fk-sources-projects` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=303 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `status_weight`
--

DROP TABLE IF EXISTS `status_weight`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `status_weight` (
  `sw_status_id` int NOT NULL,
  `sw_weight` int DEFAULT '0',
  PRIMARY KEY (`sw_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task` (
  `t_id` int NOT NULL AUTO_INCREMENT,
  `t_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `t_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `t_description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `t_hidden` tinyint(1) DEFAULT '0',
  `t_category_id` tinyint DEFAULT NULL,
  `t_sort_order` tinyint DEFAULT '10',
  PRIMARY KEY (`t_id`),
  UNIQUE KEY `t_key` (`t_key`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tips_split`
--

DROP TABLE IF EXISTS `tips_split`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tips_split` (
  `ts_id` int NOT NULL AUTO_INCREMENT,
  `ts_lead_id` int NOT NULL,
  `ts_user_id` int NOT NULL,
  `ts_percent` int DEFAULT NULL,
  `ts_amount` int DEFAULT NULL,
  `ts_updated_dt` datetime DEFAULT NULL,
  `ts_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`ts_id`),
  UNIQUE KEY `uniq_idx_user_tips_split` (`ts_user_id`,`ts_lead_id`),
  KEY `fk-tps-updated_by` (`ts_updated_user_id`),
  KEY `fk-ts-lead` (`ts_lead_id`),
  CONSTRAINT `fk-tps-updated_by` FOREIGN KEY (`ts_updated_user_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk-ts-lead` FOREIGN KEY (`ts_lead_id`) REFERENCES `leads` (`id`),
  CONSTRAINT `fk-ts-user` FOREIGN KEY (`ts_user_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=257 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transaction`
--

DROP TABLE IF EXISTS `transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaction` (
  `tr_id` int NOT NULL AUTO_INCREMENT,
  `tr_code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tr_invoice_id` int DEFAULT NULL,
  `tr_payment_id` int DEFAULT NULL,
  `tr_type_id` tinyint(1) DEFAULT NULL,
  `tr_date` date NOT NULL,
  `tr_amount` decimal(8,2) NOT NULL,
  `tr_currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tr_created_dt` datetime DEFAULT NULL,
  `tr_comment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`tr_id`),
  KEY `FK-transaction-tr_currency` (`tr_currency`),
  KEY `FK-transaction-tr_invoice_id` (`tr_invoice_id`),
  KEY `FK-transaction-tr_payment_id` (`tr_payment_id`),
  CONSTRAINT `FK-transaction-tr_currency` FOREIGN KEY (`tr_currency`) REFERENCES `currency` (`cur_code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-transaction-tr_invoice_id` FOREIGN KEY (`tr_invoice_id`) REFERENCES `invoice` (`inv_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-transaction-tr_payment_id` FOREIGN KEY (`tr_payment_id`) REFERENCES `payment` (`pay_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twilio_jwt_token`
--

DROP TABLE IF EXISTS `twilio_jwt_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `twilio_jwt_token` (
  `jt_id` int NOT NULL AUTO_INCREMENT,
  `jt_agent` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jt_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jt_app_sid` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jt_expire_dt` datetime DEFAULT NULL,
  `jt_created_dt` datetime DEFAULT NULL,
  `jt_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`jt_id`),
  UNIQUE KEY `jt_agent` (`jt_agent`),
  KEY `IND-twilio_jwt_token_agent` (`jt_agent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_bonus_rules`
--

DROP TABLE IF EXISTS `user_bonus_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_bonus_rules` (
  `ubr_exp_month` smallint NOT NULL,
  `ubr_kpi_percent` smallint NOT NULL,
  `ubr_order_profit` int NOT NULL,
  `ubr_value` decimal(8,2) DEFAULT NULL,
  `ubr_created_user_id` int DEFAULT NULL,
  `ubr_updated_user_id` int DEFAULT NULL,
  `ubr_created_dt` datetime DEFAULT NULL,
  `ubr_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ubr_exp_month`,`ubr_kpi_percent`,`ubr_order_profit`),
  KEY `FK-user_bonus_rules-created_user` (`ubr_created_user_id`),
  KEY `FK-user_bonus_rules-updated_user` (`ubr_updated_user_id`),
  CONSTRAINT `FK-user_bonus_rules-created_user` FOREIGN KEY (`ubr_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-user_bonus_rules-updated_user` FOREIGN KEY (`ubr_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_call_status`
--

DROP TABLE IF EXISTS `user_call_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_call_status` (
  `us_id` int NOT NULL AUTO_INCREMENT,
  `us_type_id` tinyint(1) DEFAULT NULL,
  `us_user_id` int DEFAULT NULL,
  `us_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`us_id`),
  KEY `IND-user_call_status_us_type_id` (`us_type_id`),
  KEY `FK-user_call_status_us_user_id` (`us_user_id`),
  CONSTRAINT `FK-user_call_status_us_user_id` FOREIGN KEY (`us_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=79648 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_client_chat_data`
--

DROP TABLE IF EXISTS `user_client_chat_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_client_chat_data` (
  `uccd_id` int NOT NULL AUTO_INCREMENT,
  `uccd_employee_id` int NOT NULL,
  `uccd_active` tinyint(1) NOT NULL DEFAULT '1',
  `uccd_created_dt` datetime DEFAULT NULL,
  `uccd_updated_dt` datetime DEFAULT NULL,
  `uccd_created_user_id` int DEFAULT NULL,
  `uccd_updated_user_id` int DEFAULT NULL,
  `uccd_auth_token` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uccd_rc_user_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uccd_password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uccd_token_expired` datetime DEFAULT NULL,
  `uccd_username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uccd_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uccd_chat_status_id` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`uccd_id`),
  UNIQUE KEY `uccd_employee_id` (`uccd_employee_id`),
  CONSTRAINT `FK-user_client_chat_data_employees` FOREIGN KEY (`uccd_employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_commission_rules`
--

DROP TABLE IF EXISTS `user_commission_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_commission_rules` (
  `ucr_exp_month` smallint NOT NULL,
  `ucr_kpi_percent` smallint NOT NULL,
  `ucr_order_profit` int NOT NULL,
  `ucr_value` decimal(5,2) DEFAULT NULL,
  `ucr_created_user_id` int DEFAULT NULL,
  `ucr_updated_user_id` int DEFAULT NULL,
  `ucr_created_dt` datetime DEFAULT NULL,
  `ucr_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ucr_exp_month`,`ucr_kpi_percent`,`ucr_order_profit`),
  KEY `FK-user_commission_rules-created_user` (`ucr_created_user_id`),
  KEY `FK-user_commission_rules-updated_user` (`ucr_updated_user_id`),
  CONSTRAINT `FK-user_commission_rules-created_user` FOREIGN KEY (`ucr_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-user_commission_rules-updated_user` FOREIGN KEY (`ucr_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_connection`
--

DROP TABLE IF EXISTS `user_connection`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_connection` (
  `uc_id` bigint NOT NULL AUTO_INCREMENT,
  `uc_connection_id` int NOT NULL,
  `uc_user_id` int DEFAULT NULL,
  `uc_lead_id` int DEFAULT NULL,
  `uc_user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uc_controller_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uc_action_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uc_page_url` varchar(1400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uc_ip` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uc_created_dt` datetime DEFAULT NULL,
  `uc_case_id` int DEFAULT NULL,
  `uc_connection_uid` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uc_app_instance` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uc_sub_list` varchar(1400) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uc_window_state` tinyint(1) DEFAULT '1',
  `uc_window_state_dt` datetime DEFAULT NULL,
  `uc_idle_state` tinyint(1) DEFAULT '1',
  `uc_idle_state_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`uc_id`),
  UNIQUE KEY `uc_connection_uid` (`uc_connection_uid`),
  UNIQUE KEY `IND-user_connection-uc_connection_uid` (`uc_connection_uid`),
  KEY `IND-user_connection_uc_connection_id` (`uc_connection_id`),
  KEY `FK-user_connection_uc_user_id` (`uc_user_id`),
  KEY `FK-user_connection_uc_lead_id` (`uc_lead_id`),
  KEY `FK-user_connection_uc_case_id` (`uc_case_id`),
  KEY `IND-user_connection-uc_app_instance` (`uc_app_instance`),
  KEY `IND-user_connection-uc_window_state` (`uc_window_state`),
  KEY `IND-user_connection-uc_idle_state` (`uc_idle_state`),
  CONSTRAINT `FK-user_connection_uc_case_id` FOREIGN KEY (`uc_case_id`) REFERENCES `cases` (`cs_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-user_connection_uc_lead_id` FOREIGN KEY (`uc_lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-user_connection_uc_user_id` FOREIGN KEY (`uc_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7639 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_connection_active_chat`
--

DROP TABLE IF EXISTS `user_connection_active_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_connection_active_chat` (
  `ucac_conn_id` bigint NOT NULL,
  `ucac_chat_id` int NOT NULL,
  PRIMARY KEY (`ucac_conn_id`,`ucac_chat_id`),
  KEY `FK-user_connection_active_chat-ucac_chat_id` (`ucac_chat_id`),
  CONSTRAINT `FK-user_connection_active_chat-ucac_chat_id` FOREIGN KEY (`ucac_chat_id`) REFERENCES `client_chat` (`cch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-user_connection_active_chat-ucac_conn_id` FOREIGN KEY (`ucac_conn_id`) REFERENCES `user_connection` (`uc_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_contact_list`
--

DROP TABLE IF EXISTS `user_contact_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_contact_list` (
  `ucl_user_id` int NOT NULL,
  `ucl_client_id` int NOT NULL,
  `ucl_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ucl_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ucl_created_dt` datetime DEFAULT NULL,
  `ucl_favorite` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ucl_user_id`,`ucl_client_id`),
  KEY `FK-user_contact_list-ucl_client_id` (`ucl_client_id`),
  KEY `IND-user_contact_list-ucl_favorite` (`ucl_favorite`),
  CONSTRAINT `FK-user_contact_list-ucl_client_id` FOREIGN KEY (`ucl_client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-user_contact_list-ucl_ucl_user_id` FOREIGN KEY (`ucl_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_department`
--

DROP TABLE IF EXISTS `user_department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_department` (
  `ud_user_id` int NOT NULL,
  `ud_dep_id` int NOT NULL,
  `ud_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ud_user_id`,`ud_dep_id`),
  KEY `FK-user_department_ud_dep_id` (`ud_dep_id`),
  CONSTRAINT `FK-user_department_ud_dep_id` FOREIGN KEY (`ud_dep_id`) REFERENCES `department` (`dep_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-user_department_ud_user_id` FOREIGN KEY (`ud_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_failed_login`
--

DROP TABLE IF EXISTS `user_failed_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_failed_login` (
  `ufl_id` int NOT NULL AUTO_INCREMENT,
  `ufl_username` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ufl_user_id` int DEFAULT NULL,
  `ufl_ua` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ufl_ip` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ufl_session_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ufl_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ufl_id`),
  KEY `IND-user_failed_login-ufl_ip` (`ufl_ip`),
  KEY `FK-user_failed_login-ufl_user_id` (`ufl_user_id`),
  CONSTRAINT `FK-user_failed_login-ufl_user_id` FOREIGN KEY (`ufl_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_group`
--

DROP TABLE IF EXISTS `user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_group` (
  `ug_id` int NOT NULL AUTO_INCREMENT,
  `ug_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ug_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ug_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ug_disable` tinyint(1) DEFAULT '0',
  `ug_updated_dt` datetime DEFAULT NULL,
  `ug_processing_fee` int DEFAULT '0',
  `ug_on_leaderboard` tinyint(1) DEFAULT '0',
  `ug_user_group_set_id` int DEFAULT NULL,
  PRIMARY KEY (`ug_id`),
  UNIQUE KEY `ug_key` (`ug_key`),
  KEY `FK-user_group_ug_user_group_set_id` (`ug_user_group_set_id`),
  CONSTRAINT `FK-user_group_ug_user_group_set_id` FOREIGN KEY (`ug_user_group_set_id`) REFERENCES `user_group_set` (`ugs_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_group_assign`
--

DROP TABLE IF EXISTS `user_group_assign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_group_assign` (
  `ugs_user_id` int NOT NULL,
  `ugs_group_id` int NOT NULL,
  `ugs_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ugs_user_id`,`ugs_group_id`),
  KEY `user_group_assign_ugs_group_id_fkey` (`ugs_group_id`),
  CONSTRAINT `user_group_assign_ugs_group_id_fkey` FOREIGN KEY (`ugs_group_id`) REFERENCES `user_group` (`ug_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_group_assign_ugs_user_id_fkey` FOREIGN KEY (`ugs_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_group_set`
--

DROP TABLE IF EXISTS `user_group_set`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_group_set` (
  `ugs_id` int NOT NULL AUTO_INCREMENT,
  `ugs_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ugs_enabled` tinyint(1) DEFAULT '0',
  `ugs_created_dt` datetime DEFAULT NULL,
  `ugs_updated_dt` datetime DEFAULT NULL,
  `ugs_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`ugs_id`),
  KEY `FK-user_group_set_updated_user_id` (`ugs_updated_user_id`),
  CONSTRAINT `FK-user_group_set_updated_user_id` FOREIGN KEY (`ugs_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_model_setting`
--

DROP TABLE IF EXISTS `user_model_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_model_setting` (
  `ums_id` int NOT NULL AUTO_INCREMENT,
  `ums_user_id` int NOT NULL,
  `ums_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ums_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ums_type` int DEFAULT NULL,
  `ums_class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ums_settings_json` json DEFAULT NULL,
  `ums_sort_order_json` json DEFAULT NULL,
  `ums_per_page` int DEFAULT '30',
  `ums_enabled` tinyint(1) DEFAULT '1',
  `ums_created_dt` datetime DEFAULT NULL,
  `ums_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ums_id`),
  KEY `FK-user_model_setting-ums_user_id` (`ums_user_id`),
  CONSTRAINT `FK-user_model_setting-ums_user_id` FOREIGN KEY (`ums_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_monitor`
--

DROP TABLE IF EXISTS `user_monitor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_monitor` (
  `um_id` int NOT NULL AUTO_INCREMENT,
  `um_user_id` int NOT NULL,
  `um_type_id` smallint NOT NULL,
  `um_start_dt` datetime DEFAULT NULL,
  `um_end_dt` datetime DEFAULT NULL,
  `um_period_sec` int DEFAULT NULL,
  `um_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`um_id`),
  KEY `IND-user_monitor-um_user_id-um_type_id` (`um_user_id`,`um_type_id`),
  CONSTRAINT `FK-user_monitor-um_user_id` FOREIGN KEY (`um_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3179 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_online`
--

DROP TABLE IF EXISTS `user_online`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_online` (
  `uo_user_id` int NOT NULL AUTO_INCREMENT,
  `uo_updated_dt` datetime DEFAULT NULL,
  `uo_idle_state` tinyint(1) DEFAULT '0',
  `uo_idle_state_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`uo_user_id`),
  KEY `IND-user_online-all` (`uo_user_id`,`uo_idle_state`,`uo_idle_state_dt`),
  CONSTRAINT `FK-user_online-uo_user_id` FOREIGN KEY (`uo_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=658 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_params`
--

DROP TABLE IF EXISTS `user_params`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_params` (
  `up_user_id` int NOT NULL,
  `up_commission_percent` int DEFAULT '10',
  `up_base_amount` decimal(10,2) DEFAULT '200.00',
  `up_updated_dt` datetime DEFAULT NULL,
  `up_updated_user_id` int DEFAULT NULL,
  `up_bonus_active` tinyint(1) DEFAULT '1',
  `up_work_start_tm` time DEFAULT '17:00:00',
  `up_work_minutes` int DEFAULT '480',
  `up_timezone` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Europe/Chisinau',
  `up_inbox_show_limit_leads` tinyint DEFAULT '10',
  `up_default_take_limit_leads` tinyint DEFAULT '5',
  `up_min_percent_for_take_leads` tinyint DEFAULT '70',
  `up_frequency_minutes` int DEFAULT '10',
  `up_call_expert_limit` smallint DEFAULT '-1',
  `up_leaderboard_enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`up_user_id`),
  KEY `user_params_up_updated_user_id_fkey` (`up_updated_user_id`),
  CONSTRAINT `user_params_up_updated_user_id_fkey` FOREIGN KEY (`up_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_params_up_user_id_fkey` FOREIGN KEY (`up_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_payment`
--

DROP TABLE IF EXISTS `user_payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_payment` (
  `upt_id` int NOT NULL AUTO_INCREMENT,
  `upt_assigned_user_id` int NOT NULL,
  `upt_category_id` int DEFAULT NULL,
  `upt_status_id` tinyint(1) DEFAULT NULL,
  `upt_amount` decimal(8,2) DEFAULT NULL,
  `upt_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upt_date` date DEFAULT NULL,
  `upt_created_user_id` int DEFAULT NULL,
  `upt_updated_user_id` int DEFAULT NULL,
  `upt_created_dt` datetime DEFAULT NULL,
  `upt_updated_dt` datetime DEFAULT NULL,
  `upt_payroll_id` int DEFAULT NULL,
  PRIMARY KEY (`upt_id`),
  KEY `fk-user_payment-upt_assigned_user_id` (`upt_assigned_user_id`),
  KEY `fk-user_payment-upt_category_id` (`upt_category_id`),
  KEY `fk-user_payment-upt_created_user_id` (`upt_created_user_id`),
  KEY `fk-user_payment-upt_updated_user_id` (`upt_updated_user_id`),
  KEY `fk-user_payment-upt_payroll_id` (`upt_payroll_id`),
  CONSTRAINT `fk-user_payment-upt_assigned_user_id` FOREIGN KEY (`upt_assigned_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk-user_payment-upt_category_id` FOREIGN KEY (`upt_category_id`) REFERENCES `user_payment_category` (`upc_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-user_payment-upt_created_user_id` FOREIGN KEY (`upt_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-user_payment-upt_payroll_id` FOREIGN KEY (`upt_payroll_id`) REFERENCES `user_payroll` (`ups_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-user_payment-upt_updated_user_id` FOREIGN KEY (`upt_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_payment_category`
--

DROP TABLE IF EXISTS `user_payment_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_payment_category` (
  `upc_id` int NOT NULL AUTO_INCREMENT,
  `upc_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upc_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upc_enabled` tinyint(1) DEFAULT NULL,
  `upc_created_user_id` int DEFAULT NULL,
  `upc_updated_user_id` int DEFAULT NULL,
  `upc_created_dt` datetime DEFAULT NULL,
  `upc_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`upc_id`),
  KEY `fk-user_payment_category-upc_created_user_id` (`upc_created_user_id`),
  KEY `fk-user_payment_category-upc_updated_user_id` (`upc_updated_user_id`),
  CONSTRAINT `fk-user_payment_category-upc_created_user_id` FOREIGN KEY (`upc_created_user_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `fk-user_payment_category-upc_updated_user_id` FOREIGN KEY (`upc_updated_user_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_payroll`
--

DROP TABLE IF EXISTS `user_payroll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_payroll` (
  `ups_id` int NOT NULL AUTO_INCREMENT,
  `ups_user_id` int NOT NULL,
  `ups_month` tinyint NOT NULL,
  `ups_year` smallint NOT NULL,
  `ups_base_amount` decimal(8,2) DEFAULT NULL,
  `ups_profit_amount` decimal(8,2) DEFAULT NULL,
  `ups_tax_amount` decimal(8,2) DEFAULT NULL,
  `ups_payment_amount` decimal(8,2) DEFAULT NULL,
  `ups_total_amount` decimal(8,2) DEFAULT NULL,
  `ups_agent_status_id` tinyint(1) DEFAULT NULL,
  `ups_status_id` tinyint(1) DEFAULT NULL,
  `ups_created_dt` datetime DEFAULT NULL,
  `ups_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`ups_id`),
  UNIQUE KEY `unique-user_payroll-ups_user_id-ups_month-ups_year` (`ups_user_id`,`ups_month`,`ups_year`),
  CONSTRAINT `fk-user_payroll-ups_user_id` FOREIGN KEY (`ups_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_personal_phone_number`
--

DROP TABLE IF EXISTS `user_personal_phone_number`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_personal_phone_number` (
  `upn_id` int NOT NULL AUTO_INCREMENT,
  `upn_user_id` int NOT NULL,
  `upn_phone_number` int DEFAULT NULL,
  `upn_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upn_approved` tinyint(1) DEFAULT '0',
  `upn_enabled` tinyint(1) DEFAULT '1',
  `upn_created_user_id` int DEFAULT NULL,
  `upn_updated_user_id` int DEFAULT NULL,
  `upn_created_dt` datetime DEFAULT NULL,
  `upn_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`upn_id`),
  KEY `FK-upn_user_id` (`upn_user_id`),
  KEY `FK-upn_created_user_id` (`upn_created_user_id`),
  KEY `FK-upn_updated_user_id` (`upn_updated_user_id`),
  KEY `FK-user_personal_phone_number-upn_phone_number` (`upn_phone_number`),
  CONSTRAINT `FK-upn_created_user_id` FOREIGN KEY (`upn_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-upn_updated_user_id` FOREIGN KEY (`upn_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-upn_user_id` FOREIGN KEY (`upn_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-user_personal_phone_number-upn_phone_number` FOREIGN KEY (`upn_phone_number`) REFERENCES `phone_list` (`pl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_product_type`
--

DROP TABLE IF EXISTS `user_product_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_product_type` (
  `upt_user_id` int NOT NULL,
  `upt_product_type_id` int NOT NULL,
  `upt_commission_percent` decimal(5,2) DEFAULT NULL,
  `upt_product_enabled` tinyint(1) DEFAULT '1',
  `upt_created_user_id` int DEFAULT NULL,
  `upt_updated_user_id` int DEFAULT NULL,
  `upt_created_dt` datetime DEFAULT NULL,
  `upt_updated_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`upt_user_id`,`upt_product_type_id`),
  KEY `FK-user_product_type-upt_product_type_id` (`upt_product_type_id`),
  KEY `FK-user_product_type-upt_created_user_id` (`upt_created_user_id`),
  KEY `FK-user_product_type-upt_updated_user_id` (`upt_updated_user_id`),
  CONSTRAINT `FK-user_product_type-upt_created_user_id` FOREIGN KEY (`upt_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-user_product_type-upt_product_type_id` FOREIGN KEY (`upt_product_type_id`) REFERENCES `product_type` (`pt_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-user_product_type-upt_updated_user_id` FOREIGN KEY (`upt_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-user_product_type-upt_user_id` FOREIGN KEY (`upt_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_profile`
--

DROP TABLE IF EXISTS `user_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_profile` (
  `up_user_id` int NOT NULL,
  `up_call_type_id` tinyint DEFAULT '0',
  `up_sip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `up_telegram` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `up_telegram_enable` tinyint(1) DEFAULT '0',
  `up_updated_dt` datetime DEFAULT NULL,
  `up_auto_redial` tinyint(1) DEFAULT '0',
  `up_kpi_enable` tinyint(1) DEFAULT '1',
  `up_skill` tinyint DEFAULT '0',
  `up_2fa_enable` tinyint(1) DEFAULT '0',
  `up_2fa_secret` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `up_join_date` date DEFAULT NULL,
  `up_show_in_contact_list` tinyint(1) DEFAULT '0',
  `up_rc_auth_token` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `up_rc_user_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `up_rc_user_password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `up_rc_token_expired` datetime DEFAULT NULL,
  `up_call_recording_disabled` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`up_user_id`),
  CONSTRAINT `FK-user_profile_up_user_id` FOREIGN KEY (`up_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_profit`
--

DROP TABLE IF EXISTS `user_profit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_profit` (
  `up_id` int NOT NULL AUTO_INCREMENT,
  `up_user_id` int NOT NULL,
  `up_lead_id` int DEFAULT NULL,
  `up_order_id` int DEFAULT NULL,
  `up_product_quote_id` int DEFAULT NULL,
  `up_percent` smallint DEFAULT NULL,
  `up_profit` decimal(8,2) DEFAULT NULL,
  `up_split_percent` smallint DEFAULT NULL,
  `up_amount` decimal(8,2) DEFAULT NULL,
  `up_status_id` tinyint(1) DEFAULT NULL,
  `up_created_dt` datetime DEFAULT NULL,
  `up_updated_dt` datetime DEFAULT NULL,
  `up_payroll_id` int DEFAULT NULL,
  `up_type_id` tinyint DEFAULT NULL,
  PRIMARY KEY (`up_id`),
  KEY `fk-user_profit-up_user_id` (`up_user_id`),
  KEY `fk-user_profit-up_lead_id` (`up_lead_id`),
  KEY `fk-user_profit-up_order_id` (`up_order_id`),
  KEY `fk-user_profit-up_product_quote_id` (`up_product_quote_id`),
  KEY `fk-user_profit-up_payroll_id` (`up_payroll_id`),
  CONSTRAINT `fk-user_profit-up_lead_id` FOREIGN KEY (`up_lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-user_profit-up_order_id` FOREIGN KEY (`up_order_id`) REFERENCES `order` (`or_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-user_profit-up_payroll_id` FOREIGN KEY (`up_payroll_id`) REFERENCES `user_payroll` (`ups_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-user_profit-up_product_quote_id` FOREIGN KEY (`up_product_quote_id`) REFERENCES `product_quote` (`pq_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk-user_profit-up_user_id` FOREIGN KEY (`up_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_project_params`
--

DROP TABLE IF EXISTS `user_project_params`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_project_params` (
  `upp_user_id` int NOT NULL,
  `upp_project_id` int NOT NULL,
  `upp_email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upp_phone_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upp_tw_phone_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upp_created_dt` datetime DEFAULT NULL,
  `upp_updated_dt` datetime DEFAULT NULL,
  `upp_updated_user_id` int DEFAULT NULL,
  `upp_allow_general_line` tinyint(1) DEFAULT '1',
  `upp_dep_id` int DEFAULT NULL,
  `upp_phone_list_id` int DEFAULT NULL,
  `upp_email_list_id` int DEFAULT NULL,
  `upp_vm_enabled` tinyint(1) DEFAULT NULL,
  `upp_vm_user_status_id` tinyint(1) DEFAULT NULL,
  `upp_vm_id` int DEFAULT NULL,
  PRIMARY KEY (`upp_user_id`,`upp_project_id`),
  KEY `user_project_params_upp_project_id_fkey` (`upp_project_id`),
  KEY `user_project_params_upp_updated_user_id_fkey` (`upp_updated_user_id`),
  KEY `FK-user_project_params_upp_dep_id` (`upp_dep_id`),
  KEY `FK-user_project_params-upp_phone_list_id` (`upp_phone_list_id`),
  KEY `FK-user_project_params-upp_email_list_id` (`upp_email_list_id`),
  KEY `FK-upp-upp_vm_id` (`upp_vm_id`),
  CONSTRAINT `FK-upp-upp_vm_id` FOREIGN KEY (`upp_vm_id`) REFERENCES `user_voice_mail` (`uvm_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-user_project_params-upp_email_list_id` FOREIGN KEY (`upp_email_list_id`) REFERENCES `email_list` (`el_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-user_project_params-upp_phone_list_id` FOREIGN KEY (`upp_phone_list_id`) REFERENCES `phone_list` (`pl_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-user_project_params_upp_dep_id` FOREIGN KEY (`upp_dep_id`) REFERENCES `department` (`dep_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_project_params_upp_project_id_fkey` FOREIGN KEY (`upp_project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_project_params_upp_updated_user_id_fkey` FOREIGN KEY (`upp_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `user_project_params_upp_user_id_fkey` FOREIGN KEY (`upp_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_shift_assign`
--

DROP TABLE IF EXISTS `user_shift_assign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_shift_assign` (
  `usa_user_id` int NOT NULL,
  `usa_ssr_id` int NOT NULL,
  `usa_created_dt` datetime DEFAULT NULL,
  `usa_created_user_id` int DEFAULT NULL,
  PRIMARY KEY (`usa_user_id`,`usa_ssr_id`),
  KEY `FK-user_shift_assign-usa_ssr_id` (`usa_ssr_id`),
  CONSTRAINT `FK-user_shift_assign-usa_ssr_id` FOREIGN KEY (`usa_ssr_id`) REFERENCES `shift_schedule_rule` (`ssr_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-user_shift_assign-usa_user_id` FOREIGN KEY (`usa_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_shift_schedule`
--

DROP TABLE IF EXISTS `user_shift_schedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_shift_schedule` (
  `uss_id` int NOT NULL AUTO_INCREMENT,
  `uss_user_id` int NOT NULL,
  `uss_shift_id` int NOT NULL,
  `uss_ssr_id` int DEFAULT NULL,
  `uss_description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uss_start_utc_dt` datetime NOT NULL,
  `uss_end_utc_dt` datetime DEFAULT NULL,
  `uss_duration` int DEFAULT NULL,
  `uss_status_id` tinyint NOT NULL,
  `uss_type_id` tinyint NOT NULL,
  `uss_customized` tinyint DEFAULT NULL,
  `uss_created_dt` datetime DEFAULT NULL,
  `uss_updated_dt` datetime DEFAULT NULL,
  `uss_created_user_id` int DEFAULT NULL,
  `uss_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`uss_id`),
  KEY `FK-user_shift_schedule-uss_user_id` (`uss_user_id`),
  KEY `FK-user_shift_schedule-uss_shift_id` (`uss_shift_id`),
  KEY `FK-user_shift_schedule-uss_ssr_id` (`uss_ssr_id`),
  CONSTRAINT `FK-user_shift_schedule-uss_shift_id` FOREIGN KEY (`uss_shift_id`) REFERENCES `shift` (`sh_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK-user_shift_schedule-uss_ssr_id` FOREIGN KEY (`uss_ssr_id`) REFERENCES `shift_schedule_rule` (`ssr_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-user_shift_schedule-uss_user_id` FOREIGN KEY (`uss_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_site_activity`
--

DROP TABLE IF EXISTS `user_site_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_site_activity` (
  `usa_id` int NOT NULL AUTO_INCREMENT,
  `usa_user_id` int DEFAULT NULL,
  `usa_request_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usa_page_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usa_ip` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usa_request_type` smallint DEFAULT NULL,
  `usa_request_get` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `usa_request_post` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `usa_created_dt` datetime DEFAULT NULL,
  PRIMARY KEY (`usa_id`),
  KEY `IND-user_site_activity` (`usa_user_id`,`usa_created_dt`,`usa_request_url`),
  KEY `IND-user_site_activity_usa_user_id_usa_created_dt` (`usa_user_id`,`usa_created_dt`),
  KEY `IND-user_site_activity_usa_page_url` (`usa_page_url`),
  CONSTRAINT `FK-user_site_activity_usa_user_id` FOREIGN KEY (`usa_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19250131 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_status`
--

DROP TABLE IF EXISTS `user_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_status` (
  `us_user_id` int NOT NULL AUTO_INCREMENT,
  `us_gl_call_count` int DEFAULT '0',
  `us_call_phone_status` tinyint(1) DEFAULT '1',
  `us_is_on_call` tinyint(1) DEFAULT '1',
  `us_has_call_access` tinyint(1) DEFAULT '1',
  `us_updated_dt` datetime DEFAULT NULL,
  `us_phone_ready_time` int DEFAULT NULL,
  PRIMARY KEY (`us_user_id`),
  KEY `IND-user_status-us_gl_call_count` (`us_gl_call_count`),
  CONSTRAINT `FK-user_status-us_user_id` FOREIGN KEY (`us_user_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=658 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_voice_mail`
--

DROP TABLE IF EXISTS `user_voice_mail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_voice_mail` (
  `uvm_id` int NOT NULL AUTO_INCREMENT,
  `uvm_user_id` int DEFAULT NULL,
  `uvm_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uvm_say_text_message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `uvm_say_language` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uvm_say_voice` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'alice',
  `uvm_voice_file_message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `uvm_record_enable` tinyint(1) DEFAULT NULL,
  `uvm_max_recording_time` int DEFAULT '60',
  `uvm_transcribe_enable` tinyint(1) DEFAULT NULL,
  `uvm_enabled` tinyint(1) DEFAULT NULL,
  `uvm_created_dt` datetime DEFAULT NULL,
  `uvm_updated_dt` datetime DEFAULT NULL,
  `uvm_created_user_id` int DEFAULT NULL,
  `uvm_updated_user_id` int DEFAULT NULL,
  PRIMARY KEY (`uvm_id`),
  KEY `FK-voice_mail-user_id` (`uvm_user_id`),
  KEY `FK-voice_mail-uvm_created_user_id` (`uvm_created_user_id`),
  KEY `FK-voice_mail-uvm_updated_user_id` (`uvm_updated_user_id`),
  KEY `FK-voice_mail-uvm_say_language` (`uvm_say_language`),
  CONSTRAINT `FK-voice_mail-user_id` FOREIGN KEY (`uvm_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-voice_mail-uvm_created_user_id` FOREIGN KEY (`uvm_created_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-voice_mail-uvm_say_language` FOREIGN KEY (`uvm_say_language`) REFERENCES `language` (`language_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-voice_mail-uvm_updated_user_id` FOREIGN KEY (`uvm_updated_user_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visitor_log`
--

DROP TABLE IF EXISTS `visitor_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visitor_log` (
  `vl_id` int NOT NULL AUTO_INCREMENT,
  `vl_project_id` int DEFAULT NULL,
  `vl_source_cid` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vl_ga_client_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vl_ga_user_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vl_customer_id` int DEFAULT NULL,
  `vl_client_id` int DEFAULT NULL,
  `vl_lead_id` int DEFAULT NULL,
  `vl_gclid` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vl_dclid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vl_utm_source` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vl_utm_medium` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vl_utm_campaign` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vl_utm_term` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vl_utm_content` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vl_referral_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vl_location_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vl_user_agent` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vl_ip_address` varchar(39) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vl_visit_dt` datetime DEFAULT NULL,
  `vl_created_dt` datetime DEFAULT NULL,
  `vl_cvd_id` int DEFAULT NULL,
  PRIMARY KEY (`vl_id`),
  KEY `FK-visitor_log-vl_project_id` (`vl_project_id`),
  KEY `IND-visitor_log-vl_lead_id` (`vl_lead_id`),
  KEY `IND-visitor_log-vl_client_id` (`vl_client_id`),
  KEY `IND-visitor_log-vl_visit_dt` (`vl_visit_dt`),
  KEY `IND-visitor_log-vl_cvd_id` (`vl_cvd_id`),
  CONSTRAINT `FK-visitor_log-vl_client_id` FOREIGN KEY (`vl_client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-visitor_log-vl_lead_id` FOREIGN KEY (`vl_lead_id`) REFERENCES `leads` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK-visitor_log-vl_project_id` FOREIGN KEY (`vl_project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1578 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `voice_mail_record`
--

DROP TABLE IF EXISTS `voice_mail_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voice_mail_record` (
  `vmr_call_id` int NOT NULL,
  `vmr_record_sid` varchar(34) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vmr_client_id` int DEFAULT NULL,
  `vmr_user_id` int DEFAULT NULL,
  `vmr_created_dt` datetime DEFAULT NULL,
  `vmr_duration` smallint DEFAULT NULL,
  `vmr_new` tinyint(1) DEFAULT NULL,
  `vmr_deleted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`vmr_call_id`),
  KEY `FK-voice_mail_record-vmr_client_id` (`vmr_client_id`) USING BTREE,
  KEY `FK-voice_mail_record-vmr_user_id` (`vmr_user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-09-20  9:14:28
