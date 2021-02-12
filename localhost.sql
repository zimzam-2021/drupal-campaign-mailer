-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 12, 2021 at 03:30 PM
-- Server version: 8.0.20
-- PHP Version: 7.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bitnami_drupal8`
--
CREATE DATABASE IF NOT EXISTS `bitnami_drupal8` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `bitnami_drupal8`;

-- --------------------------------------------------------

--
-- Table structure for table `batch`
--

CREATE TABLE `batch` (
  `bid` int UNSIGNED NOT NULL COMMENT 'Primary Key: Unique batch ID.',
  `token` varchar(64) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'A string token generated against the current user''s session id and the batch id, used to ensure that only the user who submitted the batch can effectively access it.',
  `timestamp` int NOT NULL COMMENT 'A Unix timestamp indicating when this batch was submitted for processing. Stale batches are purged at cron time.',
  `batch` longblob COMMENT 'A serialized array containing the processing data for the batch.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Stores details about batches (processes that run in…';

-- --------------------------------------------------------

--
-- Table structure for table `block_content`
--

CREATE TABLE `block_content` (
  `id` int UNSIGNED NOT NULL,
  `revision_id` int UNSIGNED DEFAULT NULL,
  `type` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The ID of the target entity.',
  `uuid` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The base table for block_content entities.';

-- --------------------------------------------------------

--
-- Table structure for table `block_content_field_data`
--

CREATE TABLE `block_content_field_data` (
  `id` int UNSIGNED NOT NULL,
  `revision_id` int UNSIGNED NOT NULL,
  `type` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The ID of the target entity.',
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `status` tinyint NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `changed` int DEFAULT NULL,
  `reusable` tinyint DEFAULT NULL,
  `default_langcode` tinyint NOT NULL,
  `revision_translation_affected` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The data table for block_content entities.';

-- --------------------------------------------------------

--
-- Table structure for table `block_content_field_revision`
--

CREATE TABLE `block_content_field_revision` (
  `id` int UNSIGNED NOT NULL,
  `revision_id` int UNSIGNED NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `status` tinyint NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `changed` int DEFAULT NULL,
  `default_langcode` tinyint NOT NULL,
  `revision_translation_affected` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The revision data table for block_content entities.';

-- --------------------------------------------------------

--
-- Table structure for table `block_content_revision`
--

CREATE TABLE `block_content_revision` (
  `id` int UNSIGNED NOT NULL,
  `revision_id` int UNSIGNED NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `revision_user` int UNSIGNED DEFAULT NULL COMMENT 'The ID of the target entity.',
  `revision_created` int DEFAULT NULL,
  `revision_log` longtext,
  `revision_default` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The revision table for block_content entities.';

-- --------------------------------------------------------

--
-- Table structure for table `block_content_revision__body`
--

CREATE TABLE `block_content_revision__body` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `body_value` longtext NOT NULL,
  `body_summary` longtext,
  `body_format` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Revision archive storage for block_content field body.';

-- --------------------------------------------------------

--
-- Table structure for table `block_content__body`
--

CREATE TABLE `block_content__body` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `body_value` longtext NOT NULL,
  `body_summary` longtext,
  `body_format` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Data storage for block_content field body.';

-- --------------------------------------------------------

--
-- Table structure for table `cachetags`
--

CREATE TABLE `cachetags` (
  `tag` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'Namespace-prefixed tag string.',
  `invalidations` int NOT NULL DEFAULT '0' COMMENT 'Number incremented when the tag is invalidated.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Cache table for tracking cache tag invalidations.';

-- --------------------------------------------------------

--
-- Table structure for table `cache_bootstrap`
--

CREATE TABLE `cache_bootstrap` (
  `cid` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique cache ID.',
  `data` longblob COMMENT 'A collection of data to cache.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire, or -1 for never.',
  `created` decimal(14,3) NOT NULL DEFAULT '0.000' COMMENT 'A timestamp with millisecond precision indicating when the cache entry was created.',
  `serialized` smallint NOT NULL DEFAULT '0' COMMENT 'A flag to indicate whether content is serialized (1) or not (0).',
  `tags` longtext COMMENT 'Space-separated list of cache tags for this entry.',
  `checksum` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The tag invalidation checksum when this entry was saved.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Storage for the cache API.';

-- --------------------------------------------------------

--
-- Table structure for table `cache_config`
--

CREATE TABLE `cache_config` (
  `cid` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique cache ID.',
  `data` longblob COMMENT 'A collection of data to cache.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire, or -1 for never.',
  `created` decimal(14,3) NOT NULL DEFAULT '0.000' COMMENT 'A timestamp with millisecond precision indicating when the cache entry was created.',
  `serialized` smallint NOT NULL DEFAULT '0' COMMENT 'A flag to indicate whether content is serialized (1) or not (0).',
  `tags` longtext COMMENT 'Space-separated list of cache tags for this entry.',
  `checksum` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The tag invalidation checksum when this entry was saved.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Storage for the cache API.';

-- --------------------------------------------------------

--
-- Table structure for table `cache_container`
--

CREATE TABLE `cache_container` (
  `cid` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique cache ID.',
  `data` longblob COMMENT 'A collection of data to cache.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire, or -1 for never.',
  `created` decimal(14,3) NOT NULL DEFAULT '0.000' COMMENT 'A timestamp with millisecond precision indicating when the cache entry was created.',
  `serialized` smallint NOT NULL DEFAULT '0' COMMENT 'A flag to indicate whether content is serialized (1) or not (0).',
  `tags` longtext COMMENT 'Space-separated list of cache tags for this entry.',
  `checksum` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The tag invalidation checksum when this entry was saved.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Storage for the cache API.';

-- --------------------------------------------------------

--
-- Table structure for table `cache_data`
--

CREATE TABLE `cache_data` (
  `cid` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique cache ID.',
  `data` longblob COMMENT 'A collection of data to cache.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire, or -1 for never.',
  `created` decimal(14,3) NOT NULL DEFAULT '0.000' COMMENT 'A timestamp with millisecond precision indicating when the cache entry was created.',
  `serialized` smallint NOT NULL DEFAULT '0' COMMENT 'A flag to indicate whether content is serialized (1) or not (0).',
  `tags` longtext COMMENT 'Space-separated list of cache tags for this entry.',
  `checksum` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The tag invalidation checksum when this entry was saved.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Storage for the cache API.';

-- --------------------------------------------------------

--
-- Table structure for table `cache_default`
--

CREATE TABLE `cache_default` (
  `cid` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique cache ID.',
  `data` longblob COMMENT 'A collection of data to cache.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire, or -1 for never.',
  `created` decimal(14,3) NOT NULL DEFAULT '0.000' COMMENT 'A timestamp with millisecond precision indicating when the cache entry was created.',
  `serialized` smallint NOT NULL DEFAULT '0' COMMENT 'A flag to indicate whether content is serialized (1) or not (0).',
  `tags` longtext COMMENT 'Space-separated list of cache tags for this entry.',
  `checksum` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The tag invalidation checksum when this entry was saved.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Storage for the cache API.';

-- --------------------------------------------------------

--
-- Table structure for table `cache_discovery`
--

CREATE TABLE `cache_discovery` (
  `cid` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique cache ID.',
  `data` longblob COMMENT 'A collection of data to cache.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire, or -1 for never.',
  `created` decimal(14,3) NOT NULL DEFAULT '0.000' COMMENT 'A timestamp with millisecond precision indicating when the cache entry was created.',
  `serialized` smallint NOT NULL DEFAULT '0' COMMENT 'A flag to indicate whether content is serialized (1) or not (0).',
  `tags` longtext COMMENT 'Space-separated list of cache tags for this entry.',
  `checksum` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The tag invalidation checksum when this entry was saved.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Storage for the cache API.';

-- --------------------------------------------------------

--
-- Table structure for table `cache_dynamic_page_cache`
--

CREATE TABLE `cache_dynamic_page_cache` (
  `cid` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique cache ID.',
  `data` longblob COMMENT 'A collection of data to cache.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire, or -1 for never.',
  `created` decimal(14,3) NOT NULL DEFAULT '0.000' COMMENT 'A timestamp with millisecond precision indicating when the cache entry was created.',
  `serialized` smallint NOT NULL DEFAULT '0' COMMENT 'A flag to indicate whether content is serialized (1) or not (0).',
  `tags` longtext COMMENT 'Space-separated list of cache tags for this entry.',
  `checksum` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The tag invalidation checksum when this entry was saved.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Storage for the cache API.';

-- --------------------------------------------------------

--
-- Table structure for table `cache_entity`
--

CREATE TABLE `cache_entity` (
  `cid` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique cache ID.',
  `data` longblob COMMENT 'A collection of data to cache.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire, or -1 for never.',
  `created` decimal(14,3) NOT NULL DEFAULT '0.000' COMMENT 'A timestamp with millisecond precision indicating when the cache entry was created.',
  `serialized` smallint NOT NULL DEFAULT '0' COMMENT 'A flag to indicate whether content is serialized (1) or not (0).',
  `tags` longtext COMMENT 'Space-separated list of cache tags for this entry.',
  `checksum` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The tag invalidation checksum when this entry was saved.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Storage for the cache API.';

-- --------------------------------------------------------

--
-- Table structure for table `cache_jsonapi_normalizations`
--

CREATE TABLE `cache_jsonapi_normalizations` (
  `cid` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique cache ID.',
  `data` longblob COMMENT 'A collection of data to cache.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire, or -1 for never.',
  `created` decimal(14,3) NOT NULL DEFAULT '0.000' COMMENT 'A timestamp with millisecond precision indicating when the cache entry was created.',
  `serialized` smallint NOT NULL DEFAULT '0' COMMENT 'A flag to indicate whether content is serialized (1) or not (0).',
  `tags` longtext COMMENT 'Space-separated list of cache tags for this entry.',
  `checksum` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The tag invalidation checksum when this entry was saved.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Storage for the cache API.';

-- --------------------------------------------------------

--
-- Table structure for table `cache_menu`
--

CREATE TABLE `cache_menu` (
  `cid` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique cache ID.',
  `data` longblob COMMENT 'A collection of data to cache.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire, or -1 for never.',
  `created` decimal(14,3) NOT NULL DEFAULT '0.000' COMMENT 'A timestamp with millisecond precision indicating when the cache entry was created.',
  `serialized` smallint NOT NULL DEFAULT '0' COMMENT 'A flag to indicate whether content is serialized (1) or not (0).',
  `tags` longtext COMMENT 'Space-separated list of cache tags for this entry.',
  `checksum` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The tag invalidation checksum when this entry was saved.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Storage for the cache API.';

-- --------------------------------------------------------

--
-- Table structure for table `cache_page`
--

CREATE TABLE `cache_page` (
  `cid` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique cache ID.',
  `data` longblob COMMENT 'A collection of data to cache.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire, or -1 for never.',
  `created` decimal(14,3) NOT NULL DEFAULT '0.000' COMMENT 'A timestamp with millisecond precision indicating when the cache entry was created.',
  `serialized` smallint NOT NULL DEFAULT '0' COMMENT 'A flag to indicate whether content is serialized (1) or not (0).',
  `tags` longtext COMMENT 'Space-separated list of cache tags for this entry.',
  `checksum` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The tag invalidation checksum when this entry was saved.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Storage for the cache API.';

-- --------------------------------------------------------

--
-- Table structure for table `cache_render`
--

CREATE TABLE `cache_render` (
  `cid` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique cache ID.',
  `data` longblob COMMENT 'A collection of data to cache.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire, or -1 for never.',
  `created` decimal(14,3) NOT NULL DEFAULT '0.000' COMMENT 'A timestamp with millisecond precision indicating when the cache entry was created.',
  `serialized` smallint NOT NULL DEFAULT '0' COMMENT 'A flag to indicate whether content is serialized (1) or not (0).',
  `tags` longtext COMMENT 'Space-separated list of cache tags for this entry.',
  `checksum` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The tag invalidation checksum when this entry was saved.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Storage for the cache API.';

-- --------------------------------------------------------

--
-- Table structure for table `cache_rest`
--

CREATE TABLE `cache_rest` (
  `cid` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique cache ID.',
  `data` longblob COMMENT 'A collection of data to cache.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire, or -1 for never.',
  `created` decimal(14,3) NOT NULL DEFAULT '0.000' COMMENT 'A timestamp with millisecond precision indicating when the cache entry was created.',
  `serialized` smallint NOT NULL DEFAULT '0' COMMENT 'A flag to indicate whether content is serialized (1) or not (0).',
  `tags` longtext COMMENT 'Space-separated list of cache tags for this entry.',
  `checksum` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The tag invalidation checksum when this entry was saved.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Storage for the cache API.';

-- --------------------------------------------------------

--
-- Table structure for table `cache_toolbar`
--

CREATE TABLE `cache_toolbar` (
  `cid` varchar(255) CHARACTER SET ascii COLLATE ascii_bin NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique cache ID.',
  `data` longblob COMMENT 'A collection of data to cache.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'A Unix timestamp indicating when the cache entry should expire, or -1 for never.',
  `created` decimal(14,3) NOT NULL DEFAULT '0.000' COMMENT 'A timestamp with millisecond precision indicating when the cache entry was created.',
  `serialized` smallint NOT NULL DEFAULT '0' COMMENT 'A flag to indicate whether content is serialized (1) or not (0).',
  `tags` longtext COMMENT 'Space-separated list of cache tags for this entry.',
  `checksum` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The tag invalidation checksum when this entry was saved.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Storage for the cache API.';

-- --------------------------------------------------------

--
-- Table structure for table `campaign`
--

CREATE TABLE `campaign` (
  `campaign_id` int NOT NULL,
  `campaign_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `description` varchar(1000) NOT NULL,
  `active` int NOT NULL DEFAULT '0',
  `activeDays` int NOT NULL DEFAULT '0',
  `activeTime` json NOT NULL,
  `sitename` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `siteurl` varchar(255) DEFAULT NULL,
  `siteemail` varchar(255) DEFAULT NULL,
  `startlimit` int DEFAULT NULL,
  `endlimit` int DEFAULT NULL,
  `logourl` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `siteaddress` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `currency` varchar(3) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `products` varchar(1000) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `product_start_range` int DEFAULT NULL,
  `product_end_range` int DEFAULT NULL,
  `customercare_no` varchar(20) DEFAULT NULL,
  `customercare_email` varchar(255) NOT NULL,
  `tracker_link` varchar(255) NOT NULL,
  `start_paragraph` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `end_paragraph` text NOT NULL,
  `call_to_action_link` varchar(255) NOT NULL,
  `call_to_action_text` varchar(255) NOT NULL,
  `filename_prefix` varchar(50) NOT NULL,
  `css_template` varchar(255) NOT NULL,
  `subject_template` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `api_credentials` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `default_campaign` int DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `campaign__time`
--

CREATE TABLE `campaign__time` (
  `campaign_id` int NOT NULL,
  `class` varchar(255) NOT NULL DEFAULT 'CampaignMailer',
  `starttime` time NOT NULL DEFAULT '09:00:00',
  `endtime` time NOT NULL DEFAULT '20:30:00',
  `active` int NOT NULL DEFAULT '1',
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `comment`
--

CREATE TABLE `comment` (
  `cid` int UNSIGNED NOT NULL,
  `comment_type` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The ID of the target entity.',
  `uuid` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The base table for comment entities.';

-- --------------------------------------------------------

--
-- Table structure for table `comment_entity_statistics`
--

CREATE TABLE `comment_entity_statistics` (
  `entity_id` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The entity_id of the entity for which the statistics are compiled.',
  `entity_type` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT 'node' COMMENT 'The entity_type of the entity to which this comment is a reply.',
  `field_name` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field_name of the field that was used to add this comment.',
  `cid` int NOT NULL DEFAULT '0' COMMENT 'The "comment".cid of the last comment.',
  `last_comment_timestamp` int NOT NULL DEFAULT '0' COMMENT 'The Unix timestamp of the last comment that was posted within this node, from "comment".changed.',
  `last_comment_name` varchar(60) DEFAULT NULL COMMENT 'The name of the latest author to post a comment on this node, from "comment".name.',
  `last_comment_uid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The user ID of the latest author to post a comment on this node, from "comment".uid.',
  `comment_count` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The total number of comments on this entity.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Maintains statistics of entity and comments posts to show …';

-- --------------------------------------------------------

--
-- Table structure for table `comment_field_data`
--

CREATE TABLE `comment_field_data` (
  `cid` int UNSIGNED NOT NULL,
  `comment_type` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The ID of the target entity.',
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `status` tinyint NOT NULL,
  `uid` int UNSIGNED NOT NULL COMMENT 'The ID of the target entity.',
  `pid` int UNSIGNED DEFAULT NULL COMMENT 'The ID of the target entity.',
  `entity_id` int UNSIGNED DEFAULT NULL COMMENT 'The ID of the target entity.',
  `subject` varchar(64) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  `mail` varchar(254) DEFAULT NULL,
  `homepage` varchar(255) DEFAULT NULL,
  `hostname` varchar(128) DEFAULT NULL,
  `created` int NOT NULL,
  `changed` int DEFAULT NULL,
  `thread` varchar(255) NOT NULL,
  `entity_type` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `field_name` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `default_langcode` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The data table for comment entities.';

-- --------------------------------------------------------

--
-- Table structure for table `comment__comment_body`
--

CREATE TABLE `comment__comment_body` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to, which for an unversioned entity type is the same as the entity id',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `comment_body_value` longtext NOT NULL,
  `comment_body_format` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Data storage for comment field comment_body.';

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `collection` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'Primary Key: Config object collection.',
  `name` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'Primary Key: Config object name.',
  `data` longblob COMMENT 'A serialized configuration object data.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The base table for configuration data.';

-- --------------------------------------------------------

--
-- Table structure for table `email_message`
--

CREATE TABLE `email_message` (
  `message_id` int NOT NULL,
  `to_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `to_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `from_user` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `from_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `subject` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `body` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `attachments` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `mail_sent` int DEFAULT NULL,
  `error_message` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `timelog` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_view_log`
--

CREATE TABLE `email_view_log` (
  `view_id` int NOT NULL,
  `message_id` int NOT NULL,
  `to_user` varchar(255) NOT NULL,
  `from_user` varchar(255) NOT NULL,
  `campaign_id` int NOT NULL DEFAULT '0',
  `sent_date` datetime DEFAULT NULL,
  `referrer` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ip` varchar(255) NOT NULL,
  `timelog` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hours` int GENERATED ALWAYS AS (hour(`timelog`)) STORED,
  `dates` varchar(12) GENERATED ALWAYS AS (cast(`timelog` as date)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email_view_log2`
--

CREATE TABLE `email_view_log2` (
  `view_id` int NOT NULL,
  `message_id` int NOT NULL,
  `to_user` varchar(255) NOT NULL,
  `from_user` varchar(255) NOT NULL,
  `referrer` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ip` varchar(255) NOT NULL,
  `timelog` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `hours` int GENERATED ALWAYS AS (hour(`timelog`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `email__message_template_lines`
--

CREATE TABLE `email__message_template_lines` (
  `template_line_id` int NOT NULL,
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'THANKYOU_LINE',
  `template_group` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'NEW_ORDER',
  `template_subgroup` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'ORDER_PLACED',
  `template` varchar(1000) NOT NULL,
  `usagecount` int NOT NULL DEFAULT '0',
  `active` int NOT NULL DEFAULT '1',
  `added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_used` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `file_managed`
--

CREATE TABLE `file_managed` (
  `fid` int UNSIGNED NOT NULL,
  `uuid` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `uid` int UNSIGNED DEFAULT NULL COMMENT 'The ID of the target entity.',
  `filename` varchar(255) DEFAULT NULL,
  `uri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `filemime` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `filesize` bigint UNSIGNED DEFAULT NULL,
  `status` tinyint NOT NULL,
  `created` int DEFAULT NULL,
  `changed` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The base table for file entities.';

-- --------------------------------------------------------

--
-- Table structure for table `file_usage`
--

CREATE TABLE `file_usage` (
  `fid` int UNSIGNED NOT NULL COMMENT 'File ID.',
  `module` varchar(50) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The name of the module that is using the file.',
  `type` varchar(64) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The name of the object type in which the file is used.',
  `id` varchar(64) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '0' COMMENT 'The primary key of the object using the file.',
  `count` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The number of times this file is used by this object.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Track where a file is used.';

-- --------------------------------------------------------

--
-- Table structure for table `flood`
--

CREATE TABLE `flood` (
  `fid` int NOT NULL COMMENT 'Unique flood event ID.',
  `event` varchar(64) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'Name of event (e.g. contact).',
  `identifier` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'Identifier of the visitor, such as an IP address or hostname.',
  `timestamp` int NOT NULL DEFAULT '0' COMMENT 'Timestamp of the event.',
  `expiration` int NOT NULL DEFAULT '0' COMMENT 'Expiration timestamp. Expired events are purged on cron run.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Flood controls the threshold of events, such as the number…';

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `uid` int NOT NULL DEFAULT '0' COMMENT 'The "users".uid that read the "node" nid.',
  `nid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The "node".nid that was read.',
  `timestamp` int NOT NULL DEFAULT '0' COMMENT 'The Unix timestamp at which the read occurred.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='A record of which "users" have read which "node"s.';

-- --------------------------------------------------------

--
-- Table structure for table `key_value`
--

CREATE TABLE `key_value` (
  `collection` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'A named collection of key and value pairs.',
  `name` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The key of the key-value pair. As KEY is a SQL reserved keyword, name was chosen instead.',
  `value` longblob NOT NULL COMMENT 'The value.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Generic key-value storage table. See the state system for…';

-- --------------------------------------------------------

--
-- Table structure for table `key_value_expire`
--

CREATE TABLE `key_value_expire` (
  `collection` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'A named collection of key and value pairs.',
  `name` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The key of the key/value pair.',
  `value` longblob NOT NULL COMMENT 'The value of the key/value pair.',
  `expire` int NOT NULL DEFAULT '2147483647' COMMENT 'The time since Unix epoch in seconds when this item expires. Defaults to the maximum possible time.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Generic key/value storage table with an expiration.';

-- --------------------------------------------------------

--
-- Table structure for table `leadcampaign`
--

CREATE TABLE `leadcampaign` (
  `lead_id` int NOT NULL,
  `campaign_id` int NOT NULL,
  `message_id` int DEFAULT NULL,
  `mail_sent` int NOT NULL DEFAULT '0',
  `timestamp_log` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `lead_id` int NOT NULL,
  `firstname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'Customer',
  `lastname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `user_exists` int DEFAULT NULL,
  `cron_check_count` int NOT NULL DEFAULT '0',
  `birth_month` int DEFAULT NULL,
  `age` int DEFAULT NULL,
  `estimated_income` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `county` varchar(255) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL,
  `telephone` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mc__call_details_records`
--

CREATE TABLE `mc__call_details_records` (
  `call_id` int NOT NULL,
  `account_id` int NOT NULL,
  `call_status` varchar(255) NOT NULL,
  `caller_name` varchar(255) NOT NULL,
  `caller_number` varchar(255) NOT NULL,
  `caller_destination` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `recording` varchar(255) NOT NULL,
  `start` datetime NOT NULL,
  `tta` varchar(255) NOT NULL,
  `duration` time NOT NULL,
  `pdd` varchar(255) NOT NULL,
  `mos` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `hangup_cause` varchar(255) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mc__did_accounts`
--

CREATE TABLE `mc__did_accounts` (
  `did_id` int NOT NULL,
  `did_no` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `provider` varchar(255) NOT NULL,
  `loginurl` varchar(255) NOT NULL,
  `actionurl` varchar(255) NOT NULL,
  `dataurl` varchar(255) NOT NULL,
  `added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mc__email_replies`
--

CREATE TABLE `mc__email_replies` (
  `message_id` int NOT NULL,
  `account_username` varchar(255) DEFAULT NULL,
  `account_hostname` varchar(255) DEFAULT NULL,
  `account_message_id` varchar(255) DEFAULT NULL,
  `account_message_no` varchar(255) DEFAULT NULL,
  `subject` varchar(1000) DEFAULT NULL,
  `fromEmail` varchar(255) DEFAULT NULL,
  `fromName` varchar(255) DEFAULT NULL,
  `bodyHTML` text,
  `bodyText` text,
  `toEmail` varchar(255) DEFAULT NULL,
  `toName` varchar(255) DEFAULT NULL,
  `answered` int DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mc__imap_access`
--

CREATE TABLE `mc__imap_access` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `active` int NOT NULL DEFAULT '1',
  `provider` varchar(255) NOT NULL DEFAULT 'gmail.com',
  `last_access` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mc__outbound_restrict_email`
--

CREATE TABLE `mc__outbound_restrict_email` (
  `account_id` int NOT NULL,
  `emailid` varchar(255) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mc__test_mx_accounts`
--

CREATE TABLE `mc__test_mx_accounts` (
  `account_id` int NOT NULL,
  `test_email_id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mc__yahoo_mx_accounts`
--

CREATE TABLE `mc__yahoo_mx_accounts` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `max_test_mail` int NOT NULL DEFAULT '70',
  `max_email_count` int NOT NULL DEFAULT '20',
  `min_time_diff` int NOT NULL DEFAULT '2',
  `max_mail_per_batch` int NOT NULL DEFAULT '2',
  `emailid` varchar(255) DEFAULT 'transaction-alert@247itechsolutions.net',
  `active` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mc__yahoo_mx_log`
--

CREATE TABLE `mc__yahoo_mx_log` (
  `username` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `mail_count` int NOT NULL DEFAULT '0',
  `test_mail_count` int NOT NULL DEFAULT '0',
  `last_mail_sent` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mc__yahoo_test_account_log`
--

CREATE TABLE `mc__yahoo_test_account_log` (
  `yahoo_username` varchar(255) NOT NULL,
  `test_email_id` varchar(255) NOT NULL,
  `count` int NOT NULL,
  `last_mail_sent` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `menu_link_content`
--

CREATE TABLE `menu_link_content` (
  `id` int UNSIGNED NOT NULL,
  `revision_id` int UNSIGNED DEFAULT NULL,
  `bundle` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `uuid` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The base table for menu_link_content entities.';

-- --------------------------------------------------------

--
-- Table structure for table `menu_link_content_data`
--

CREATE TABLE `menu_link_content_data` (
  `id` int UNSIGNED NOT NULL,
  `revision_id` int UNSIGNED NOT NULL,
  `bundle` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `enabled` tinyint NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `menu_name` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `link__uri` varchar(2048) DEFAULT NULL COMMENT 'The URI of the link.',
  `link__title` varchar(255) DEFAULT NULL COMMENT 'The link text.',
  `link__options` longblob COMMENT 'Serialized array of options for the link.',
  `external` tinyint DEFAULT NULL,
  `rediscover` tinyint DEFAULT NULL,
  `weight` int DEFAULT NULL,
  `expanded` tinyint DEFAULT NULL,
  `parent` varchar(255) DEFAULT NULL,
  `changed` int DEFAULT NULL,
  `default_langcode` tinyint NOT NULL,
  `revision_translation_affected` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The data table for menu_link_content entities.';

-- --------------------------------------------------------

--
-- Table structure for table `menu_link_content_field_revision`
--

CREATE TABLE `menu_link_content_field_revision` (
  `id` int UNSIGNED NOT NULL,
  `revision_id` int UNSIGNED NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `enabled` tinyint NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `link__uri` varchar(2048) DEFAULT NULL COMMENT 'The URI of the link.',
  `link__title` varchar(255) DEFAULT NULL COMMENT 'The link text.',
  `link__options` longblob COMMENT 'Serialized array of options for the link.',
  `external` tinyint DEFAULT NULL,
  `changed` int DEFAULT NULL,
  `default_langcode` tinyint NOT NULL,
  `revision_translation_affected` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The revision data table for menu_link_content entities.';

-- --------------------------------------------------------

--
-- Table structure for table `menu_link_content_revision`
--

CREATE TABLE `menu_link_content_revision` (
  `id` int UNSIGNED NOT NULL,
  `revision_id` int UNSIGNED NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `revision_user` int UNSIGNED DEFAULT NULL COMMENT 'The ID of the target entity.',
  `revision_created` int DEFAULT NULL,
  `revision_log_message` longtext,
  `revision_default` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The revision table for menu_link_content entities.';

-- --------------------------------------------------------

--
-- Table structure for table `menu_tree`
--

CREATE TABLE `menu_tree` (
  `menu_name` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The menu name. All links with the same menu name (such as ''tools'') are part of the same menu.',
  `mlid` int UNSIGNED NOT NULL COMMENT 'The menu link ID (mlid) is the integer primary key.',
  `id` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'Unique machine name: the plugin ID.',
  `parent` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The plugin ID for the parent of this link.',
  `route_name` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL COMMENT 'The machine name of a defined Symfony Route this menu item represents.',
  `route_param_key` varchar(255) DEFAULT NULL COMMENT 'An encoded string of route parameters for loading by route.',
  `route_parameters` longblob COMMENT 'Serialized array of route parameters of this menu link.',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT 'The external path this link points to (when not using a route).',
  `title` longblob COMMENT 'The serialized title for the link. May be a TranslatableMarkup.',
  `description` longblob COMMENT 'The serialized description of this link - used for admin pages and title attribute. May be a TranslatableMarkup.',
  `class` text COMMENT 'The class for this link plugin.',
  `options` longblob COMMENT 'A serialized array of URL options, such as a query string or HTML attributes.',
  `provider` varchar(50) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT 'system' COMMENT 'The name of the module that generated this link.',
  `enabled` smallint NOT NULL DEFAULT '1' COMMENT 'A flag for whether the link should be rendered in menus. (0 = a disabled menu item that may be shown on admin screens, 1 = a normal, visible link)',
  `discovered` smallint NOT NULL DEFAULT '0' COMMENT 'A flag for whether the link was discovered, so can be purged on rebuild',
  `expanded` smallint NOT NULL DEFAULT '0' COMMENT 'Flag for whether this link should be rendered as expanded in menus - expanded links always have their child links displayed, instead of only when the link is in the active trail (1 = expanded, 0 = not expanded)',
  `weight` int NOT NULL DEFAULT '0' COMMENT 'Link weight among links in the same menu at the same depth.',
  `metadata` longblob COMMENT 'A serialized array of data that may be used by the plugin instance.',
  `has_children` smallint NOT NULL DEFAULT '0' COMMENT 'Flag indicating whether any enabled links have this link as a parent (1 = enabled children exist, 0 = no enabled children).',
  `depth` smallint NOT NULL DEFAULT '0' COMMENT 'The depth relative to the top level. A link with empty parent will have depth == 1.',
  `p1` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The first mlid in the materialized path. If N = depth, then pN must equal the mlid. If depth > 1 then p(N-1) must equal the parent link mlid. All pX where X > depth must equal zero. The columns p1 .. p9 are also called the parents.',
  `p2` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The second mlid in the materialized path. See p1.',
  `p3` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The third mlid in the materialized path. See p1.',
  `p4` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The fourth mlid in the materialized path. See p1.',
  `p5` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The fifth mlid in the materialized path. See p1.',
  `p6` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The sixth mlid in the materialized path. See p1.',
  `p7` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The seventh mlid in the materialized path. See p1.',
  `p8` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The eighth mlid in the materialized path. See p1.',
  `p9` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The ninth mlid in the materialized path. See p1.',
  `form_class` varchar(255) DEFAULT NULL COMMENT 'meh'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Contains the menu tree hierarchy.';

-- --------------------------------------------------------

--
-- Table structure for table `mx_accounts`
--

CREATE TABLE `mx_accounts` (
  `account_id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Kolkata123@',
  `emailid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'support@edufluence.us',
  `hostname` varchar(255) NOT NULL DEFAULT 'smtp.gmail.com',
  `port` int NOT NULL DEFAULT '587',
  `encryption` int NOT NULL DEFAULT '1',
  `ssltype` varchar(255) NOT NULL DEFAULT 'tls',
  `active` int NOT NULL DEFAULT '1',
  `deleted` int NOT NULL DEFAULT '0',
  `max_mail_per_batch` int NOT NULL DEFAULT '5',
  `max_email_count` int NOT NULL DEFAULT '80',
  `min_time_diff` int NOT NULL DEFAULT '30',
  `replace_from` int NOT NULL DEFAULT '0',
  `replace_replyto` int NOT NULL DEFAULT '1',
  `last_check` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mx_accounts_bkup`
--

CREATE TABLE `mx_accounts_bkup` (
  `account_id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'kolkata123@',
  `emailid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'biller@att-eservice.com',
  `hostname` varchar(255) NOT NULL DEFAULT 'smtp.gmail.com',
  `port` int NOT NULL DEFAULT '587',
  `encryption` int NOT NULL DEFAULT '1',
  `ssltype` varchar(255) NOT NULL DEFAULT 'tls',
  `active` int NOT NULL DEFAULT '0',
  `deleted` int NOT NULL DEFAULT '0',
  `max_mail_per_batch` int NOT NULL DEFAULT '5',
  `max_email_count` int NOT NULL DEFAULT '80',
  `min_time_diff` int NOT NULL DEFAULT '30',
  `replace_from` int NOT NULL DEFAULT '0',
  `replace_replyto` int NOT NULL DEFAULT '0',
  `last_check` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mx_accounts_error_log`
--

CREATE TABLE `mx_accounts_error_log` (
  `log_id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `error_message` text NOT NULL,
  `timelog` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mx_accounts_log`
--

CREATE TABLE `mx_accounts_log` (
  `username` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `mail_count` int NOT NULL DEFAULT '0',
  `last_mail_sent` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mx__unsubscribe`
--

CREATE TABLE `mx__unsubscribe` (
  `request_id` int NOT NULL,
  `domain` varchar(255) NOT NULL,
  `email_id` varchar(255) NOT NULL,
  `message_id` int NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `node`
--

CREATE TABLE `node` (
  `nid` int UNSIGNED NOT NULL,
  `vid` int UNSIGNED DEFAULT NULL,
  `type` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The ID of the target entity.',
  `uuid` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The base table for node entities.';

-- --------------------------------------------------------

--
-- Table structure for table `node_access`
--

CREATE TABLE `node_access` (
  `nid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The "node".nid this record affects.',
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The "language".langcode of this node.',
  `fallback` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Boolean indicating whether this record should be used as a fallback if a language condition is not provided.',
  `gid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The grant ID a user must possess in the specified realm to gain this row''s privileges on the node.',
  `realm` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The realm in which the user must possess the grant ID. Modules can define one or more realms by implementing hook_node_grants().',
  `grant_view` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Boolean indicating whether a user with the realm/grant pair can view this node.',
  `grant_update` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Boolean indicating whether a user with the realm/grant pair can edit this node.',
  `grant_delete` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Boolean indicating whether a user with the realm/grant pair can delete this node.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Identifies which realm/grant pairs a user must possess in…';

-- --------------------------------------------------------

--
-- Table structure for table `node_field_data`
--

CREATE TABLE `node_field_data` (
  `nid` int UNSIGNED NOT NULL,
  `vid` int UNSIGNED NOT NULL,
  `type` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The ID of the target entity.',
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `status` tinyint NOT NULL,
  `uid` int UNSIGNED NOT NULL COMMENT 'The ID of the target entity.',
  `title` varchar(255) NOT NULL,
  `created` int NOT NULL,
  `changed` int NOT NULL,
  `promote` tinyint NOT NULL,
  `sticky` tinyint NOT NULL,
  `default_langcode` tinyint NOT NULL,
  `revision_translation_affected` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The data table for node entities.';

-- --------------------------------------------------------

--
-- Table structure for table `node_field_revision`
--

CREATE TABLE `node_field_revision` (
  `nid` int UNSIGNED NOT NULL,
  `vid` int UNSIGNED NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `status` tinyint NOT NULL,
  `uid` int UNSIGNED NOT NULL COMMENT 'The ID of the target entity.',
  `title` varchar(255) DEFAULT NULL,
  `created` int DEFAULT NULL,
  `changed` int DEFAULT NULL,
  `promote` tinyint DEFAULT NULL,
  `sticky` tinyint DEFAULT NULL,
  `default_langcode` tinyint NOT NULL,
  `revision_translation_affected` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The revision data table for node entities.';

-- --------------------------------------------------------

--
-- Table structure for table `node_revision`
--

CREATE TABLE `node_revision` (
  `nid` int UNSIGNED NOT NULL,
  `vid` int UNSIGNED NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `revision_uid` int UNSIGNED DEFAULT NULL COMMENT 'The ID of the target entity.',
  `revision_timestamp` int DEFAULT NULL,
  `revision_log` longtext,
  `revision_default` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The revision table for node entities.';

-- --------------------------------------------------------

--
-- Table structure for table `node_revision__body`
--

CREATE TABLE `node_revision__body` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `body_value` longtext NOT NULL,
  `body_summary` longtext,
  `body_format` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Revision archive storage for node field body.';

-- --------------------------------------------------------

--
-- Table structure for table `node_revision__comment`
--

CREATE TABLE `node_revision__comment` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `comment_status` int NOT NULL DEFAULT '0' COMMENT 'Whether comments are allowed on this entity: 0 = no, 1 = closed (read only), 2 = open (read/write).'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Revision archive storage for node field comment.';

-- --------------------------------------------------------

--
-- Table structure for table `node_revision__field_image`
--

CREATE TABLE `node_revision__field_image` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `field_image_target_id` int UNSIGNED NOT NULL COMMENT 'The ID of the file entity.',
  `field_image_alt` varchar(512) DEFAULT NULL COMMENT 'Alternative image text, for the image''s ''alt'' attribute.',
  `field_image_title` varchar(1024) DEFAULT NULL COMMENT 'Image title text, for the image''s ''title'' attribute.',
  `field_image_width` int UNSIGNED DEFAULT NULL COMMENT 'The width of the image in pixels.',
  `field_image_height` int UNSIGNED DEFAULT NULL COMMENT 'The height of the image in pixels.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Revision archive storage for node field field_image.';

-- --------------------------------------------------------

--
-- Table structure for table `node_revision__field_tags`
--

CREATE TABLE `node_revision__field_tags` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `field_tags_target_id` int UNSIGNED NOT NULL COMMENT 'The ID of the target entity.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Revision archive storage for node field field_tags.';

-- --------------------------------------------------------

--
-- Table structure for table `node__body`
--

CREATE TABLE `node__body` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `body_value` longtext NOT NULL,
  `body_summary` longtext,
  `body_format` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Data storage for node field body.';

-- --------------------------------------------------------

--
-- Table structure for table `node__comment`
--

CREATE TABLE `node__comment` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `comment_status` int NOT NULL DEFAULT '0' COMMENT 'Whether comments are allowed on this entity: 0 = no, 1 = closed (read only), 2 = open (read/write).'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Data storage for node field comment.';

-- --------------------------------------------------------

--
-- Table structure for table `node__field_image`
--

CREATE TABLE `node__field_image` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `field_image_target_id` int UNSIGNED NOT NULL COMMENT 'The ID of the file entity.',
  `field_image_alt` varchar(512) DEFAULT NULL COMMENT 'Alternative image text, for the image''s ''alt'' attribute.',
  `field_image_title` varchar(1024) DEFAULT NULL COMMENT 'Image title text, for the image''s ''title'' attribute.',
  `field_image_width` int UNSIGNED DEFAULT NULL COMMENT 'The width of the image in pixels.',
  `field_image_height` int UNSIGNED DEFAULT NULL COMMENT 'The height of the image in pixels.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Data storage for node field field_image.';

-- --------------------------------------------------------

--
-- Table structure for table `node__field_tags`
--

CREATE TABLE `node__field_tags` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `field_tags_target_id` int UNSIGNED NOT NULL COMMENT 'The ID of the target entity.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Data storage for node field field_tags.';

-- --------------------------------------------------------

--
-- Table structure for table `path_alias`
--

CREATE TABLE `path_alias` (
  `id` int UNSIGNED NOT NULL,
  `revision_id` int UNSIGNED DEFAULT NULL,
  `uuid` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `status` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The base table for path_alias entities.';

-- --------------------------------------------------------

--
-- Table structure for table `path_alias_revision`
--

CREATE TABLE `path_alias_revision` (
  `id` int UNSIGNED NOT NULL,
  `revision_id` int UNSIGNED NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `status` tinyint NOT NULL,
  `revision_default` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The revision table for path_alias entities.';

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE `queue` (
  `item_id` int UNSIGNED NOT NULL COMMENT 'Primary Key: Unique item ID.',
  `name` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The queue name.',
  `data` longblob COMMENT 'The arbitrary data for the item.',
  `expire` int NOT NULL DEFAULT '0' COMMENT 'Timestamp when the claim lease expires on the item.',
  `created` int NOT NULL DEFAULT '0' COMMENT 'Timestamp when the item was created.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Stores items in queues.';

-- --------------------------------------------------------

--
-- Table structure for table `router`
--

CREATE TABLE `router` (
  `name` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'Primary Key: Machine name of this route',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT 'The path for this URI',
  `pattern_outline` varchar(255) NOT NULL DEFAULT '' COMMENT 'The pattern',
  `fit` int NOT NULL DEFAULT '0' COMMENT 'A numeric representation of how specific the path is.',
  `route` longblob COMMENT 'A serialized Route object',
  `number_parts` smallint NOT NULL DEFAULT '0' COMMENT 'Number of parts in this router path.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Maps paths to various callbacks (access, page and title)';

-- --------------------------------------------------------

--
-- Table structure for table `search_dataset`
--

CREATE TABLE `search_dataset` (
  `sid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Search item ID, e.g. node ID for nodes.',
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The "languages".langcode of the item variant.',
  `type` varchar(64) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'Type of item, e.g. node.',
  `data` longtext NOT NULL COMMENT 'List of space-separated words from the item.',
  `reindex` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Set to force node reindexing.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Stores items that will be searched.';

-- --------------------------------------------------------

--
-- Table structure for table `search_index`
--

CREATE TABLE `search_index` (
  `word` varchar(50) NOT NULL DEFAULT '' COMMENT 'The "search_total".word that is associated with the search item.',
  `sid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The "search_dataset".sid of the searchable item to which the word belongs.',
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The "languages".langcode of the item variant.',
  `type` varchar(64) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The "search_dataset".type of the searchable item to which the word belongs.',
  `score` float DEFAULT NULL COMMENT 'The numeric score of the word, higher being more important.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Stores the search index, associating words, items and…';

-- --------------------------------------------------------

--
-- Table structure for table `search_total`
--

CREATE TABLE `search_total` (
  `word` varchar(50) NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique word in the search index.',
  `count` float DEFAULT NULL COMMENT 'The count of the word in the index using Zipf''s law to equalize the probability distribution.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Stores search totals for words.';

-- --------------------------------------------------------

--
-- Table structure for table `semaphore`
--

CREATE TABLE `semaphore` (
  `name` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'Primary Key: Unique name.',
  `value` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'A value for the semaphore.',
  `expire` double NOT NULL COMMENT 'A Unix timestamp with microseconds indicating when the semaphore should expire.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Table for holding semaphores, locks, flags, etc. that…';

-- --------------------------------------------------------

--
-- Table structure for table `sequences`
--

CREATE TABLE `sequences` (
  `value` int UNSIGNED NOT NULL COMMENT 'The value of the sequence.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Stores IDs.';

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `uid` int UNSIGNED NOT NULL COMMENT 'The "users".uid corresponding to a session, or 0 for anonymous user.',
  `sid` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'A session ID (hashed). The value is generated by Drupal''s session handlers.',
  `hostname` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The IP address that last used this session ID (sid).',
  `timestamp` int NOT NULL DEFAULT '0' COMMENT 'The Unix timestamp when this session last requested a page. Old records are purged by PHP automatically.',
  `session` longblob COMMENT 'The serialized contents of $_SESSION, an array of name/value pairs that persists across page requests by this session ID. Drupal loads $_SESSION from here at the start of each request and saves it at the end.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Drupal''s session handlers read and write into the sessions…';

-- --------------------------------------------------------

--
-- Table structure for table `shortcut`
--

CREATE TABLE `shortcut` (
  `id` int UNSIGNED NOT NULL,
  `shortcut_set` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The ID of the target entity.',
  `uuid` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The base table for shortcut entities.';

-- --------------------------------------------------------

--
-- Table structure for table `shortcut_field_data`
--

CREATE TABLE `shortcut_field_data` (
  `id` int UNSIGNED NOT NULL,
  `shortcut_set` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The ID of the target entity.',
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `weight` int DEFAULT NULL,
  `link__uri` varchar(2048) DEFAULT NULL COMMENT 'The URI of the link.',
  `link__title` varchar(255) DEFAULT NULL COMMENT 'The link text.',
  `link__options` longblob COMMENT 'Serialized array of options for the link.',
  `default_langcode` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The data table for shortcut entities.';

-- --------------------------------------------------------

--
-- Table structure for table `shortcut_set_users`
--

CREATE TABLE `shortcut_set_users` (
  `uid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The "users".uid for this set.',
  `set_name` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The "shortcut_set".set_name that will be displayed for this user.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Maps users to shortcut sets.';

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy_index`
--

CREATE TABLE `taxonomy_index` (
  `nid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The "node".nid this record tracks.',
  `tid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The term ID.',
  `status` int NOT NULL DEFAULT '1' COMMENT 'Boolean indicating whether the node is published (visible to non-administrators).',
  `sticky` tinyint DEFAULT '0' COMMENT 'Boolean indicating whether the node is sticky.',
  `created` int NOT NULL DEFAULT '0' COMMENT 'The Unix timestamp when the node was created.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Maintains denormalized information about node/term…';

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy_term_data`
--

CREATE TABLE `taxonomy_term_data` (
  `tid` int UNSIGNED NOT NULL,
  `revision_id` int UNSIGNED DEFAULT NULL,
  `vid` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The ID of the target entity.',
  `uuid` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The base table for taxonomy_term entities.';

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy_term_field_data`
--

CREATE TABLE `taxonomy_term_field_data` (
  `tid` int UNSIGNED NOT NULL,
  `revision_id` int UNSIGNED NOT NULL,
  `vid` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The ID of the target entity.',
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `status` tinyint NOT NULL,
  `name` varchar(255) NOT NULL,
  `description__value` longtext,
  `description__format` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `weight` int NOT NULL,
  `changed` int DEFAULT NULL,
  `default_langcode` tinyint NOT NULL,
  `revision_translation_affected` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The data table for taxonomy_term entities.';

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy_term_field_revision`
--

CREATE TABLE `taxonomy_term_field_revision` (
  `tid` int UNSIGNED NOT NULL,
  `revision_id` int UNSIGNED NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `status` tinyint NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description__value` longtext,
  `description__format` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `changed` int DEFAULT NULL,
  `default_langcode` tinyint NOT NULL,
  `revision_translation_affected` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The revision data table for taxonomy_term entities.';

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy_term_revision`
--

CREATE TABLE `taxonomy_term_revision` (
  `tid` int UNSIGNED NOT NULL,
  `revision_id` int UNSIGNED NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `revision_user` int UNSIGNED DEFAULT NULL COMMENT 'The ID of the target entity.',
  `revision_created` int DEFAULT NULL,
  `revision_log_message` longtext,
  `revision_default` tinyint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The revision table for taxonomy_term entities.';

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy_term_revision__parent`
--

CREATE TABLE `taxonomy_term_revision__parent` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `parent_target_id` int UNSIGNED NOT NULL COMMENT 'The ID of the target entity.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Revision archive storage for taxonomy_term field parent.';

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy_term__parent`
--

CREATE TABLE `taxonomy_term__parent` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `parent_target_id` int UNSIGNED NOT NULL COMMENT 'The ID of the target entity.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Data storage for taxonomy_term field parent.';

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uid` int UNSIGNED NOT NULL,
  `uuid` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The base table for user entities.';

-- --------------------------------------------------------

--
-- Table structure for table `users_data`
--

CREATE TABLE `users_data` (
  `uid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The "users".uid this record affects.',
  `module` varchar(50) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The name of the module declaring the variable.',
  `name` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The identifier of the data.',
  `value` longblob COMMENT 'The value.',
  `serialized` tinyint UNSIGNED DEFAULT '0' COMMENT 'Whether value is serialized.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Stores module data as key/value pairs per user.';

-- --------------------------------------------------------

--
-- Table structure for table `users_field_data`
--

CREATE TABLE `users_field_data` (
  `uid` int UNSIGNED NOT NULL,
  `langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
  `preferred_langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `preferred_admin_langcode` varchar(12) CHARACTER SET ascii COLLATE ascii_general_ci DEFAULT NULL,
  `name` varchar(60) NOT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `mail` varchar(254) DEFAULT NULL,
  `timezone` varchar(32) DEFAULT NULL,
  `status` tinyint DEFAULT NULL,
  `created` int NOT NULL,
  `changed` int DEFAULT NULL,
  `access` int NOT NULL,
  `login` int DEFAULT NULL,
  `init` varchar(254) DEFAULT NULL,
  `default_langcode` tinyint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='The data table for user entities.';

-- --------------------------------------------------------

--
-- Table structure for table `user__roles`
--

CREATE TABLE `user__roles` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to, which for an unversioned entity type is the same as the entity id',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `roles_target_id` varchar(255) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL COMMENT 'The ID of the target entity.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Data storage for user field roles.';

-- --------------------------------------------------------

--
-- Table structure for table `user__user_picture`
--

CREATE TABLE `user__user_picture` (
  `bundle` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The field instance bundle to which this row belongs, used when deleting a field instance',
  `deleted` tinyint NOT NULL DEFAULT '0' COMMENT 'A boolean indicating whether this data item has been deleted',
  `entity_id` int UNSIGNED NOT NULL COMMENT 'The entity id this data is attached to',
  `revision_id` int UNSIGNED NOT NULL COMMENT 'The entity revision id this data is attached to, which for an unversioned entity type is the same as the entity id',
  `langcode` varchar(32) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'The language code for this data item.',
  `delta` int UNSIGNED NOT NULL COMMENT 'The sequence number for this data item, used for multi-value fields',
  `user_picture_target_id` int UNSIGNED NOT NULL COMMENT 'The ID of the file entity.',
  `user_picture_alt` varchar(512) DEFAULT NULL COMMENT 'Alternative image text, for the image''s ''alt'' attribute.',
  `user_picture_title` varchar(1024) DEFAULT NULL COMMENT 'Image title text, for the image''s ''title'' attribute.',
  `user_picture_width` int UNSIGNED DEFAULT NULL COMMENT 'The width of the image in pixels.',
  `user_picture_height` int UNSIGNED DEFAULT NULL COMMENT 'The height of the image in pixels.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Data storage for user field user_picture.';

-- --------------------------------------------------------

--
-- Table structure for table `watchdog`
--

CREATE TABLE `watchdog` (
  `wid` int NOT NULL COMMENT 'Primary Key: Unique watchdog event ID.',
  `uid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The "users".uid of the user who triggered the event.',
  `type` varchar(64) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'Type of log message, for example "user" or "page not found."',
  `message` longtext NOT NULL COMMENT 'Text of log message to be passed into the t() function.',
  `variables` longblob NOT NULL COMMENT 'Serialized array of variables that match the message string and that is passed into the t() function.',
  `severity` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The severity level of the event. ranges from 0 (Emergency) to 7 (Debug)',
  `link` text COMMENT 'Link to view the result of the event.',
  `location` text NOT NULL COMMENT 'URL of the origin of the event.',
  `referer` text COMMENT 'URL of referring page.',
  `hostname` varchar(128) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT '' COMMENT 'Hostname of the user who triggered the event.',
  `timestamp` int NOT NULL DEFAULT '0' COMMENT 'Unix timestamp of when event occurred.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Table that contains logs of all system events.';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batch`
--
ALTER TABLE `batch`
  ADD PRIMARY KEY (`bid`),
  ADD KEY `token` (`token`);

--
-- Indexes for table `block_content`
--
ALTER TABLE `block_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `block_content_field__uuid__value` (`uuid`),
  ADD UNIQUE KEY `block_content__revision_id` (`revision_id`),
  ADD KEY `block_content_field__type__target_id` (`type`);

--
-- Indexes for table `block_content_field_data`
--
ALTER TABLE `block_content_field_data`
  ADD PRIMARY KEY (`id`,`langcode`),
  ADD KEY `block_content__id__default_langcode__langcode` (`id`,`default_langcode`,`langcode`),
  ADD KEY `block_content__revision_id` (`revision_id`),
  ADD KEY `block_content_field__type__target_id` (`type`),
  ADD KEY `block_content__status_type` (`status`,`type`,`id`);

--
-- Indexes for table `block_content_field_revision`
--
ALTER TABLE `block_content_field_revision`
  ADD PRIMARY KEY (`revision_id`,`langcode`),
  ADD KEY `block_content__id__default_langcode__langcode` (`id`,`default_langcode`,`langcode`);

--
-- Indexes for table `block_content_revision`
--
ALTER TABLE `block_content_revision`
  ADD PRIMARY KEY (`revision_id`),
  ADD KEY `block_content__id` (`id`),
  ADD KEY `block_content_field__revision_user__target_id` (`revision_user`);

--
-- Indexes for table `block_content_revision__body`
--
ALTER TABLE `block_content_revision__body`
  ADD PRIMARY KEY (`entity_id`,`revision_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `bundle` (`bundle`),
  ADD KEY `revision_id` (`revision_id`),
  ADD KEY `body_format` (`body_format`);

--
-- Indexes for table `block_content__body`
--
ALTER TABLE `block_content__body`
  ADD PRIMARY KEY (`entity_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `bundle` (`bundle`),
  ADD KEY `revision_id` (`revision_id`),
  ADD KEY `body_format` (`body_format`);

--
-- Indexes for table `cachetags`
--
ALTER TABLE `cachetags`
  ADD PRIMARY KEY (`tag`);

--
-- Indexes for table `cache_bootstrap`
--
ALTER TABLE `cache_bootstrap`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `expire` (`expire`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `cache_config`
--
ALTER TABLE `cache_config`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `expire` (`expire`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `cache_container`
--
ALTER TABLE `cache_container`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `expire` (`expire`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `cache_data`
--
ALTER TABLE `cache_data`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `expire` (`expire`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `cache_default`
--
ALTER TABLE `cache_default`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `expire` (`expire`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `cache_discovery`
--
ALTER TABLE `cache_discovery`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `expire` (`expire`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `cache_dynamic_page_cache`
--
ALTER TABLE `cache_dynamic_page_cache`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `expire` (`expire`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `cache_entity`
--
ALTER TABLE `cache_entity`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `expire` (`expire`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `cache_jsonapi_normalizations`
--
ALTER TABLE `cache_jsonapi_normalizations`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `expire` (`expire`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `cache_menu`
--
ALTER TABLE `cache_menu`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `expire` (`expire`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `cache_page`
--
ALTER TABLE `cache_page`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `expire` (`expire`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `cache_render`
--
ALTER TABLE `cache_render`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `expire` (`expire`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `cache_rest`
--
ALTER TABLE `cache_rest`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `expire` (`expire`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `cache_toolbar`
--
ALTER TABLE `cache_toolbar`
  ADD PRIMARY KEY (`cid`),
  ADD KEY `expire` (`expire`),
  ADD KEY `created` (`created`);

--
-- Indexes for table `campaign`
--
ALTER TABLE `campaign`
  ADD PRIMARY KEY (`campaign_id`);

--
-- Indexes for table `campaign__time`
--
ALTER TABLE `campaign__time`
  ADD PRIMARY KEY (`campaign_id`,`starttime`,`endtime`);

--
-- Indexes for table `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`cid`),
  ADD UNIQUE KEY `comment_field__uuid__value` (`uuid`),
  ADD KEY `comment_field__comment_type__target_id` (`comment_type`);

--
-- Indexes for table `comment_entity_statistics`
--
ALTER TABLE `comment_entity_statistics`
  ADD PRIMARY KEY (`entity_id`,`entity_type`,`field_name`),
  ADD KEY `last_comment_timestamp` (`last_comment_timestamp`),
  ADD KEY `comment_count` (`comment_count`),
  ADD KEY `last_comment_uid` (`last_comment_uid`);

--
-- Indexes for table `comment_field_data`
--
ALTER TABLE `comment_field_data`
  ADD PRIMARY KEY (`cid`,`langcode`),
  ADD KEY `comment__id__default_langcode__langcode` (`cid`,`default_langcode`,`langcode`),
  ADD KEY `comment_field__comment_type__target_id` (`comment_type`),
  ADD KEY `comment_field__uid__target_id` (`uid`),
  ADD KEY `comment_field__created` (`created`),
  ADD KEY `comment__status_comment_type` (`status`,`comment_type`,`cid`),
  ADD KEY `comment__status_pid` (`pid`,`status`),
  ADD KEY `comment__num_new` (`entity_id`,`entity_type`,`comment_type`,`status`,`created`,`cid`,`thread`(191)),
  ADD KEY `comment__entity_langcode` (`entity_id`,`entity_type`,`comment_type`,`default_langcode`);

--
-- Indexes for table `comment__comment_body`
--
ALTER TABLE `comment__comment_body`
  ADD PRIMARY KEY (`entity_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `bundle` (`bundle`),
  ADD KEY `revision_id` (`revision_id`),
  ADD KEY `comment_body_format` (`comment_body_format`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`collection`,`name`);

--
-- Indexes for table `email_message`
--
ALTER TABLE `email_message`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_timelog` (`timelog`);

--
-- Indexes for table `email_view_log`
--
ALTER TABLE `email_view_log`
  ADD PRIMARY KEY (`view_id`),
  ADD KEY `timelog_from_idx` (`timelog`,`from_user`),
  ADD KEY `timelog_idx` (`timelog`),
  ADD KEY `from_user_idx` (`from_user`),
  ADD KEY `idx_dates` (`dates`),
  ADD KEY `idx_hours` (`hours`);

--
-- Indexes for table `email_view_log2`
--
ALTER TABLE `email_view_log2`
  ADD PRIMARY KEY (`view_id`),
  ADD KEY `timelog_from_idx` (`timelog`,`from_user`),
  ADD KEY `timelog_idx` (`timelog`),
  ADD KEY `from_user_idx` (`from_user`);

--
-- Indexes for table `email__message_template_lines`
--
ALTER TABLE `email__message_template_lines`
  ADD PRIMARY KEY (`template_line_id`);

--
-- Indexes for table `file_managed`
--
ALTER TABLE `file_managed`
  ADD PRIMARY KEY (`fid`),
  ADD UNIQUE KEY `file_field__uuid__value` (`uuid`),
  ADD KEY `file_field__uid__target_id` (`uid`),
  ADD KEY `file_field__uri` (`uri`(191)),
  ADD KEY `file_field__status` (`status`),
  ADD KEY `file_field__changed` (`changed`);

--
-- Indexes for table `file_usage`
--
ALTER TABLE `file_usage`
  ADD PRIMARY KEY (`fid`,`type`,`id`,`module`),
  ADD KEY `type_id` (`type`,`id`),
  ADD KEY `fid_count` (`fid`,`count`),
  ADD KEY `fid_module` (`fid`,`module`);

--
-- Indexes for table `flood`
--
ALTER TABLE `flood`
  ADD PRIMARY KEY (`fid`),
  ADD KEY `allow` (`event`,`identifier`,`timestamp`),
  ADD KEY `purge` (`expiration`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`uid`,`nid`),
  ADD KEY `nid` (`nid`);

--
-- Indexes for table `key_value`
--
ALTER TABLE `key_value`
  ADD PRIMARY KEY (`collection`,`name`);

--
-- Indexes for table `key_value_expire`
--
ALTER TABLE `key_value_expire`
  ADD PRIMARY KEY (`collection`,`name`),
  ADD KEY `all` (`name`,`collection`,`expire`),
  ADD KEY `expire` (`expire`);

--
-- Indexes for table `leadcampaign`
--
ALTER TABLE `leadcampaign`
  ADD PRIMARY KEY (`lead_id`,`campaign_id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`lead_id`);

--
-- Indexes for table `mc__call_details_records`
--
ALTER TABLE `mc__call_details_records`
  ADD PRIMARY KEY (`call_id`),
  ADD UNIQUE KEY `caller_unique_idx` (`account_id`,`caller_number`,`caller_destination`,`destination`,`start`);

--
-- Indexes for table `mc__did_accounts`
--
ALTER TABLE `mc__did_accounts`
  ADD PRIMARY KEY (`did_id`);

--
-- Indexes for table `mc__email_replies`
--
ALTER TABLE `mc__email_replies`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `mc__imap_access`
--
ALTER TABLE `mc__imap_access`
  ADD PRIMARY KEY (`username`,`provider`);

--
-- Indexes for table `mc__outbound_restrict_email`
--
ALTER TABLE `mc__outbound_restrict_email`
  ADD PRIMARY KEY (`account_id`);

--
-- Indexes for table `mc__test_mx_accounts`
--
ALTER TABLE `mc__test_mx_accounts`
  ADD PRIMARY KEY (`account_id`);

--
-- Indexes for table `mc__yahoo_mx_accounts`
--
ALTER TABLE `mc__yahoo_mx_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mc__yahoo_mx_log`
--
ALTER TABLE `mc__yahoo_mx_log`
  ADD UNIQUE KEY `idx_unique` (`username`,`date`);

--
-- Indexes for table `mc__yahoo_test_account_log`
--
ALTER TABLE `mc__yahoo_test_account_log`
  ADD UNIQUE KEY `yahoo_username` (`yahoo_username`,`test_email_id`);

--
-- Indexes for table `menu_link_content`
--
ALTER TABLE `menu_link_content`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menu_link_content_field__uuid__value` (`uuid`),
  ADD UNIQUE KEY `menu_link_content__revision_id` (`revision_id`);

--
-- Indexes for table `menu_link_content_data`
--
ALTER TABLE `menu_link_content_data`
  ADD PRIMARY KEY (`id`,`langcode`),
  ADD KEY `menu_link_content__id__default_langcode__langcode` (`id`,`default_langcode`,`langcode`),
  ADD KEY `menu_link_content__revision_id` (`revision_id`),
  ADD KEY `menu_link_content_field__link__uri` (`link__uri`(30)),
  ADD KEY `menu_link_content__enabled_bundle` (`enabled`,`bundle`,`id`);

--
-- Indexes for table `menu_link_content_field_revision`
--
ALTER TABLE `menu_link_content_field_revision`
  ADD PRIMARY KEY (`revision_id`,`langcode`),
  ADD KEY `menu_link_content__id__default_langcode__langcode` (`id`,`default_langcode`,`langcode`),
  ADD KEY `menu_link_content_field__link__uri` (`link__uri`(30));

--
-- Indexes for table `menu_link_content_revision`
--
ALTER TABLE `menu_link_content_revision`
  ADD PRIMARY KEY (`revision_id`),
  ADD KEY `menu_link_content__id` (`id`),
  ADD KEY `menu_link_content__ef029a1897` (`revision_user`);

--
-- Indexes for table `menu_tree`
--
ALTER TABLE `menu_tree`
  ADD PRIMARY KEY (`mlid`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `menu_parents` (`menu_name`,`p1`,`p2`,`p3`,`p4`,`p5`,`p6`,`p7`,`p8`,`p9`),
  ADD KEY `menu_parent_expand_child` (`menu_name`,`expanded`,`has_children`,`parent`(16)),
  ADD KEY `route_values` (`route_name`(32),`route_param_key`(16));

--
-- Indexes for table `mx_accounts`
--
ALTER TABLE `mx_accounts`
  ADD PRIMARY KEY (`account_id`);

--
-- Indexes for table `mx_accounts_bkup`
--
ALTER TABLE `mx_accounts_bkup`
  ADD PRIMARY KEY (`account_id`);

--
-- Indexes for table `mx_accounts_error_log`
--
ALTER TABLE `mx_accounts_error_log`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `mx_accounts_log`
--
ALTER TABLE `mx_accounts_log`
  ADD PRIMARY KEY (`username`,`date`);

--
-- Indexes for table `mx__unsubscribe`
--
ALTER TABLE `mx__unsubscribe`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `node`
--
ALTER TABLE `node`
  ADD PRIMARY KEY (`nid`),
  ADD UNIQUE KEY `node_field__uuid__value` (`uuid`),
  ADD UNIQUE KEY `node__vid` (`vid`),
  ADD KEY `node_field__type__target_id` (`type`);

--
-- Indexes for table `node_access`
--
ALTER TABLE `node_access`
  ADD PRIMARY KEY (`nid`,`gid`,`realm`,`langcode`);

--
-- Indexes for table `node_field_data`
--
ALTER TABLE `node_field_data`
  ADD PRIMARY KEY (`nid`,`langcode`),
  ADD KEY `node__id__default_langcode__langcode` (`nid`,`default_langcode`,`langcode`),
  ADD KEY `node__vid` (`vid`),
  ADD KEY `node_field__type__target_id` (`type`),
  ADD KEY `node_field__uid__target_id` (`uid`),
  ADD KEY `node_field__created` (`created`),
  ADD KEY `node_field__changed` (`changed`),
  ADD KEY `node__status_type` (`status`,`type`,`nid`),
  ADD KEY `node__frontpage` (`promote`,`status`,`sticky`,`created`),
  ADD KEY `node__title_type` (`title`(191),`type`(4));

--
-- Indexes for table `node_field_revision`
--
ALTER TABLE `node_field_revision`
  ADD PRIMARY KEY (`vid`,`langcode`),
  ADD KEY `node__id__default_langcode__langcode` (`nid`,`default_langcode`,`langcode`),
  ADD KEY `node_field__uid__target_id` (`uid`);

--
-- Indexes for table `node_revision`
--
ALTER TABLE `node_revision`
  ADD PRIMARY KEY (`vid`),
  ADD KEY `node__nid` (`nid`),
  ADD KEY `node_field__langcode` (`langcode`),
  ADD KEY `node_field__revision_uid__target_id` (`revision_uid`);

--
-- Indexes for table `node_revision__body`
--
ALTER TABLE `node_revision__body`
  ADD PRIMARY KEY (`entity_id`,`revision_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `bundle` (`bundle`),
  ADD KEY `revision_id` (`revision_id`),
  ADD KEY `body_format` (`body_format`);

--
-- Indexes for table `node_revision__comment`
--
ALTER TABLE `node_revision__comment`
  ADD PRIMARY KEY (`entity_id`,`revision_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `bundle` (`bundle`),
  ADD KEY `revision_id` (`revision_id`);

--
-- Indexes for table `node_revision__field_image`
--
ALTER TABLE `node_revision__field_image`
  ADD PRIMARY KEY (`entity_id`,`revision_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `bundle` (`bundle`),
  ADD KEY `revision_id` (`revision_id`),
  ADD KEY `field_image_target_id` (`field_image_target_id`);

--
-- Indexes for table `node_revision__field_tags`
--
ALTER TABLE `node_revision__field_tags`
  ADD PRIMARY KEY (`entity_id`,`revision_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `bundle` (`bundle`),
  ADD KEY `revision_id` (`revision_id`),
  ADD KEY `field_tags_target_id` (`field_tags_target_id`);

--
-- Indexes for table `node__body`
--
ALTER TABLE `node__body`
  ADD PRIMARY KEY (`entity_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `bundle` (`bundle`),
  ADD KEY `revision_id` (`revision_id`),
  ADD KEY `body_format` (`body_format`);

--
-- Indexes for table `node__comment`
--
ALTER TABLE `node__comment`
  ADD PRIMARY KEY (`entity_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `bundle` (`bundle`),
  ADD KEY `revision_id` (`revision_id`);

--
-- Indexes for table `node__field_image`
--
ALTER TABLE `node__field_image`
  ADD PRIMARY KEY (`entity_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `bundle` (`bundle`),
  ADD KEY `revision_id` (`revision_id`),
  ADD KEY `field_image_target_id` (`field_image_target_id`);

--
-- Indexes for table `node__field_tags`
--
ALTER TABLE `node__field_tags`
  ADD PRIMARY KEY (`entity_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `bundle` (`bundle`),
  ADD KEY `revision_id` (`revision_id`),
  ADD KEY `field_tags_target_id` (`field_tags_target_id`);

--
-- Indexes for table `path_alias`
--
ALTER TABLE `path_alias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `path_alias_field__uuid__value` (`uuid`),
  ADD UNIQUE KEY `path_alias__revision_id` (`revision_id`),
  ADD KEY `path_alias__status` (`status`,`id`),
  ADD KEY `path_alias__alias_langcode_id_status` (`alias`(191),`langcode`,`id`,`status`),
  ADD KEY `path_alias__path_langcode_id_status` (`path`(191),`langcode`,`id`,`status`);

--
-- Indexes for table `path_alias_revision`
--
ALTER TABLE `path_alias_revision`
  ADD PRIMARY KEY (`revision_id`),
  ADD KEY `path_alias__id` (`id`);

--
-- Indexes for table `queue`
--
ALTER TABLE `queue`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `name_created` (`name`,`created`),
  ADD KEY `expire` (`expire`);

--
-- Indexes for table `router`
--
ALTER TABLE `router`
  ADD PRIMARY KEY (`name`),
  ADD KEY `pattern_outline_parts` (`pattern_outline`(191),`number_parts`);

--
-- Indexes for table `search_dataset`
--
ALTER TABLE `search_dataset`
  ADD PRIMARY KEY (`sid`,`langcode`,`type`);

--
-- Indexes for table `search_index`
--
ALTER TABLE `search_index`
  ADD PRIMARY KEY (`word`,`sid`,`langcode`,`type`),
  ADD KEY `sid_type` (`sid`,`langcode`,`type`);

--
-- Indexes for table `search_total`
--
ALTER TABLE `search_total`
  ADD PRIMARY KEY (`word`);

--
-- Indexes for table `semaphore`
--
ALTER TABLE `semaphore`
  ADD PRIMARY KEY (`name`),
  ADD KEY `value` (`value`),
  ADD KEY `expire` (`expire`);

--
-- Indexes for table `sequences`
--
ALTER TABLE `sequences`
  ADD PRIMARY KEY (`value`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`sid`),
  ADD KEY `timestamp` (`timestamp`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `shortcut`
--
ALTER TABLE `shortcut`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shortcut_field__uuid__value` (`uuid`),
  ADD KEY `shortcut_field__shortcut_set__target_id` (`shortcut_set`);

--
-- Indexes for table `shortcut_field_data`
--
ALTER TABLE `shortcut_field_data`
  ADD PRIMARY KEY (`id`,`langcode`),
  ADD KEY `shortcut__id__default_langcode__langcode` (`id`,`default_langcode`,`langcode`),
  ADD KEY `shortcut_field__shortcut_set__target_id` (`shortcut_set`),
  ADD KEY `shortcut_field__link__uri` (`link__uri`(30));

--
-- Indexes for table `shortcut_set_users`
--
ALTER TABLE `shortcut_set_users`
  ADD PRIMARY KEY (`uid`),
  ADD KEY `set_name` (`set_name`);

--
-- Indexes for table `taxonomy_index`
--
ALTER TABLE `taxonomy_index`
  ADD PRIMARY KEY (`nid`,`tid`),
  ADD KEY `term_node` (`tid`,`status`,`sticky`,`created`);

--
-- Indexes for table `taxonomy_term_data`
--
ALTER TABLE `taxonomy_term_data`
  ADD PRIMARY KEY (`tid`),
  ADD UNIQUE KEY `taxonomy_term_field__uuid__value` (`uuid`),
  ADD UNIQUE KEY `taxonomy_term__revision_id` (`revision_id`),
  ADD KEY `taxonomy_term_field__vid__target_id` (`vid`);

--
-- Indexes for table `taxonomy_term_field_data`
--
ALTER TABLE `taxonomy_term_field_data`
  ADD PRIMARY KEY (`tid`,`langcode`),
  ADD KEY `taxonomy_term__id__default_langcode__langcode` (`tid`,`default_langcode`,`langcode`),
  ADD KEY `taxonomy_term__revision_id` (`revision_id`),
  ADD KEY `taxonomy_term_field__name` (`name`(191)),
  ADD KEY `taxonomy_term__status_vid` (`status`,`vid`,`tid`),
  ADD KEY `taxonomy_term__tree` (`vid`,`weight`,`name`(191)),
  ADD KEY `taxonomy_term__vid_name` (`vid`,`name`(191));

--
-- Indexes for table `taxonomy_term_field_revision`
--
ALTER TABLE `taxonomy_term_field_revision`
  ADD PRIMARY KEY (`revision_id`,`langcode`),
  ADD KEY `taxonomy_term__id__default_langcode__langcode` (`tid`,`default_langcode`,`langcode`),
  ADD KEY `taxonomy_term_field__description__format` (`description__format`);

--
-- Indexes for table `taxonomy_term_revision`
--
ALTER TABLE `taxonomy_term_revision`
  ADD PRIMARY KEY (`revision_id`),
  ADD KEY `taxonomy_term__tid` (`tid`),
  ADD KEY `taxonomy_term_field__revision_user__target_id` (`revision_user`);

--
-- Indexes for table `taxonomy_term_revision__parent`
--
ALTER TABLE `taxonomy_term_revision__parent`
  ADD PRIMARY KEY (`entity_id`,`revision_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `bundle` (`bundle`),
  ADD KEY `revision_id` (`revision_id`),
  ADD KEY `parent_target_id` (`parent_target_id`);

--
-- Indexes for table `taxonomy_term__parent`
--
ALTER TABLE `taxonomy_term__parent`
  ADD PRIMARY KEY (`entity_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `revision_id` (`revision_id`),
  ADD KEY `parent_target_id` (`parent_target_id`),
  ADD KEY `bundle_delta_target_id` (`bundle`,`delta`,`parent_target_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `user_field__uuid__value` (`uuid`);

--
-- Indexes for table `users_data`
--
ALTER TABLE `users_data`
  ADD PRIMARY KEY (`uid`,`module`,`name`),
  ADD KEY `module` (`module`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `users_field_data`
--
ALTER TABLE `users_field_data`
  ADD PRIMARY KEY (`uid`,`langcode`),
  ADD UNIQUE KEY `user__name` (`name`,`langcode`),
  ADD KEY `user__id__default_langcode__langcode` (`uid`,`default_langcode`,`langcode`),
  ADD KEY `user_field__mail` (`mail`(191)),
  ADD KEY `user_field__created` (`created`),
  ADD KEY `user_field__access` (`access`);

--
-- Indexes for table `user__roles`
--
ALTER TABLE `user__roles`
  ADD PRIMARY KEY (`entity_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `bundle` (`bundle`),
  ADD KEY `revision_id` (`revision_id`),
  ADD KEY `roles_target_id` (`roles_target_id`);

--
-- Indexes for table `user__user_picture`
--
ALTER TABLE `user__user_picture`
  ADD PRIMARY KEY (`entity_id`,`deleted`,`delta`,`langcode`),
  ADD KEY `bundle` (`bundle`),
  ADD KEY `revision_id` (`revision_id`),
  ADD KEY `user_picture_target_id` (`user_picture_target_id`);

--
-- Indexes for table `watchdog`
--
ALTER TABLE `watchdog`
  ADD PRIMARY KEY (`wid`),
  ADD KEY `type` (`type`),
  ADD KEY `uid` (`uid`),
  ADD KEY `severity` (`severity`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `block_content`
--
ALTER TABLE `block_content`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `block_content_revision`
--
ALTER TABLE `block_content_revision`
  MODIFY `revision_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `campaign`
--
ALTER TABLE `campaign`
  MODIFY `campaign_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comment`
--
ALTER TABLE `comment`
  MODIFY `cid` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_message`
--
ALTER TABLE `email_message`
  MODIFY `message_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_view_log`
--
ALTER TABLE `email_view_log`
  MODIFY `view_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_view_log2`
--
ALTER TABLE `email_view_log2`
  MODIFY `view_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email__message_template_lines`
--
ALTER TABLE `email__message_template_lines`
  MODIFY `template_line_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `file_managed`
--
ALTER TABLE `file_managed`
  MODIFY `fid` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flood`
--
ALTER TABLE `flood`
  MODIFY `fid` int NOT NULL AUTO_INCREMENT COMMENT 'Unique flood event ID.';

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `lead_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mc__call_details_records`
--
ALTER TABLE `mc__call_details_records`
  MODIFY `call_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mc__did_accounts`
--
ALTER TABLE `mc__did_accounts`
  MODIFY `did_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mc__email_replies`
--
ALTER TABLE `mc__email_replies`
  MODIFY `message_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mc__outbound_restrict_email`
--
ALTER TABLE `mc__outbound_restrict_email`
  MODIFY `account_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mc__test_mx_accounts`
--
ALTER TABLE `mc__test_mx_accounts`
  MODIFY `account_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mc__yahoo_mx_accounts`
--
ALTER TABLE `mc__yahoo_mx_accounts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_link_content`
--
ALTER TABLE `menu_link_content`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_link_content_revision`
--
ALTER TABLE `menu_link_content_revision`
  MODIFY `revision_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_tree`
--
ALTER TABLE `menu_tree`
  MODIFY `mlid` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'The menu link ID (mlid) is the integer primary key.';

--
-- AUTO_INCREMENT for table `mx_accounts`
--
ALTER TABLE `mx_accounts`
  MODIFY `account_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mx_accounts_bkup`
--
ALTER TABLE `mx_accounts_bkup`
  MODIFY `account_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mx_accounts_error_log`
--
ALTER TABLE `mx_accounts_error_log`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mx__unsubscribe`
--
ALTER TABLE `mx__unsubscribe`
  MODIFY `request_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `node`
--
ALTER TABLE `node`
  MODIFY `nid` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `node_revision`
--
ALTER TABLE `node_revision`
  MODIFY `vid` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `path_alias`
--
ALTER TABLE `path_alias`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `path_alias_revision`
--
ALTER TABLE `path_alias_revision`
  MODIFY `revision_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `queue`
--
ALTER TABLE `queue`
  MODIFY `item_id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Primary Key: Unique item ID.';

--
-- AUTO_INCREMENT for table `sequences`
--
ALTER TABLE `sequences`
  MODIFY `value` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'The value of the sequence.';

--
-- AUTO_INCREMENT for table `shortcut`
--
ALTER TABLE `shortcut`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `taxonomy_term_data`
--
ALTER TABLE `taxonomy_term_data`
  MODIFY `tid` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `taxonomy_term_revision`
--
ALTER TABLE `taxonomy_term_revision`
  MODIFY `revision_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `watchdog`
--
ALTER TABLE `watchdog`
  MODIFY `wid` int NOT NULL AUTO_INCREMENT COMMENT 'Primary Key: Unique watchdog event ID.';
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
