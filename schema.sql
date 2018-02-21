-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 21, 2018 at 12:29 PM
-- Server version: 5.7.20-0ubuntu0.16.04.1
-- PHP Version: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mangadex`
--

-- --------------------------------------------------------

--
-- Table structure for table `batoto_comics_bk`
--
-- Creation: Jan 21, 2018 at 02:31 AM
--

CREATE TABLE `batoto_comics_bk` (
  `id` int(11) NOT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mature` tinyint(1) DEFAULT NULL,
  `rating` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `viewcount` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `followcount` int(11) DEFAULT NULL,
  `lastupdate` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_names` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `authors` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `artists` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `genres` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `chapters` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_chapters`
--
-- Creation: Feb 18, 2018 at 11:48 PM
-- Last update: Feb 21, 2018 at 11:29 AM
--

CREATE TABLE `mangadex_chapters` (
  `chapter_id` int(10) UNSIGNED NOT NULL,
  `chapter_hash` char(32) NOT NULL,
  `manga_id` smallint(5) UNSIGNED NOT NULL,
  `volume` varchar(8) NOT NULL,
  `chapter` varchar(8) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `upload_timestamp` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) NOT NULL DEFAULT '1',
  `chapter_views` int(10) UNSIGNED NOT NULL,
  `lang_id` tinyint(3) UNSIGNED NOT NULL,
  `authorised` tinyint(1) UNSIGNED NOT NULL,
  `group_id` smallint(5) UNSIGNED NOT NULL,
  `group_id_2` smallint(5) UNSIGNED NOT NULL,
  `group_id_3` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
  `server` tinyint(3) UNSIGNED NOT NULL,
  `page_order` text NOT NULL,
  `chapter_deleted` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_chapter_reports`
--
-- Creation: Feb 09, 2018 at 12:54 AM
-- Last update: Feb 21, 2018 at 11:26 AM
--

CREATE TABLE `mangadex_chapter_reports` (
  `report_id` mediumint(8) UNSIGNED NOT NULL,
  `report_chapter_id` int(10) UNSIGNED NOT NULL,
  `report_timestamp` int(10) UNSIGNED NOT NULL,
  `report_type` tinyint(1) NOT NULL,
  `report_info` varchar(255) NOT NULL,
  `report_user_id` int(10) UNSIGNED NOT NULL,
  `report_mod_user_id` int(10) UNSIGNED NOT NULL,
  `report_conclusion` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_chapter_views`
--
-- Creation: Feb 19, 2018 at 12:46 AM
-- Last update: Feb 21, 2018 at 11:29 AM
--

CREATE TABLE `mangadex_chapter_views` (
  `chapter_id` mediumint(8) UNSIGNED NOT NULL,
  `user_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_comments_groups`
--
-- Creation: Jan 12, 2018 at 05:31 PM
-- Last update: Feb 21, 2018 at 10:30 AM
--

CREATE TABLE `mangadex_comments_groups` (
  `comment_id` int(10) UNSIGNED NOT NULL,
  `group_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `comment_text` text NOT NULL,
  `comment_timestamp` int(10) UNSIGNED NOT NULL,
  `comment_del` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_comments_manga`
--
-- Creation: Jan 21, 2018 at 04:30 AM
-- Last update: Feb 21, 2018 at 11:14 AM
--

CREATE TABLE `mangadex_comments_manga` (
  `comment_id` int(10) UNSIGNED NOT NULL,
  `manga_id` smallint(5) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `comment_text` text NOT NULL,
  `comment_timestamp` int(10) UNSIGNED NOT NULL,
  `comment_del` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_countries`
--
-- Creation: Jan 11, 2018 at 01:34 AM
--

CREATE TABLE `mangadex_countries` (
  `country_id` tinyint(3) UNSIGNED NOT NULL,
  `country_name` varchar(48) DEFAULT NULL,
  `country_flagpic` varchar(8) DEFAULT NULL,
  `country_domain` char(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_follow_user_manga`
--
-- Creation: Feb 19, 2018 at 12:44 AM
-- Last update: Feb 21, 2018 at 11:29 AM
--

CREATE TABLE `mangadex_follow_user_manga` (
  `user_id` mediumint(8) UNSIGNED NOT NULL,
  `manga_id` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_forums`
--
-- Creation: Feb 11, 2018 at 05:40 PM
--

CREATE TABLE `mangadex_forums` (
  `forum_id` smallint(5) UNSIGNED NOT NULL,
  `forum_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `forum_parent` smallint(5) UNSIGNED NOT NULL,
  `forum_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_forum_posts`
--
-- Creation: Feb 11, 2018 at 05:55 PM
--

CREATE TABLE `mangadex_forum_posts` (
  `post_id` int(10) UNSIGNED NOT NULL,
  `thread_id` mediumint(8) UNSIGNED NOT NULL,
  `user_id` smallint(5) UNSIGNED NOT NULL,
  `post_timestamp` int(10) UNSIGNED NOT NULL,
  `post_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_deleted` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_forum_threads`
--
-- Creation: Feb 12, 2018 at 12:07 AM
--

CREATE TABLE `mangadex_forum_threads` (
  `thread_id` mediumint(8) UNSIGNED NOT NULL,
  `thread_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `forum_id` smallint(5) UNSIGNED NOT NULL,
  `user_id` mediumint(8) UNSIGNED NOT NULL,
  `thread_timestamp` int(10) UNSIGNED NOT NULL,
  `thread_posts` smallint(5) UNSIGNED NOT NULL,
  `thread_views` mediumint(8) UNSIGNED NOT NULL,
  `thread_locked` tinyint(1) UNSIGNED NOT NULL,
  `thread_sticky` tinyint(1) UNSIGNED NOT NULL,
  `thread_deleted` tinyint(3) UNSIGNED NOT NULL,
  `last_post_user_id` mediumint(8) UNSIGNED NOT NULL,
  `last_post_timestamp` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_genres`
--
-- Creation: Jan 12, 2018 at 01:18 AM
--

CREATE TABLE `mangadex_genres` (
  `genre_id` tinyint(3) UNSIGNED NOT NULL,
  `genre_name` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_groups`
--
-- Creation: Feb 20, 2018 at 11:36 PM
-- Last update: Feb 21, 2018 at 11:29 AM
--

CREATE TABLE `mangadex_groups` (
  `group_id` smallint(5) UNSIGNED NOT NULL,
  `group_name` varchar(64) NOT NULL,
  `group_leader_id` int(10) UNSIGNED NOT NULL,
  `group_website` varchar(128) NOT NULL,
  `group_irc_channel` varchar(64) NOT NULL,
  `group_irc_server` varchar(64) NOT NULL,
  `group_discord` varchar(128) NOT NULL,
  `group_email` varchar(128) NOT NULL,
  `group_lang_id` tinyint(3) UNSIGNED NOT NULL,
  `group_founded` date NOT NULL,
  `group_banner` varchar(4) NOT NULL,
  `group_comments` smallint(6) NOT NULL,
  `group_likes` smallint(5) UNSIGNED NOT NULL,
  `group_follows` mediumint(8) UNSIGNED NOT NULL,
  `group_views` int(10) UNSIGNED NOT NULL,
  `group_description` text NOT NULL,
  `group_control` tinyint(3) UNSIGNED NOT NULL,
  `group_delay` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_group_likes`
--
-- Creation: Jan 12, 2018 at 05:22 PM
--

CREATE TABLE `mangadex_group_likes` (
  `id` int(10) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_import`
--
-- Creation: Jan 26, 2018 at 01:21 AM
--

CREATE TABLE `mangadex_import` (
  `id` int(10) UNSIGNED NOT NULL,
  `json` mediumtext NOT NULL,
  `user_id` smallint(5) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_languages`
--
-- Creation: Jan 25, 2018 at 06:16 PM
--

CREATE TABLE `mangadex_languages` (
  `lang_id` int(10) NOT NULL,
  `lang_name` varchar(32) NOT NULL DEFAULT '',
  `lang_flag` varchar(32) NOT NULL,
  `has_chapters` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_link_user_group`
--
-- Creation: Feb 09, 2018 at 04:07 AM
-- Last update: Feb 21, 2018 at 07:14 AM
--

CREATE TABLE `mangadex_link_user_group` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `group_id` smallint(5) UNSIGNED NOT NULL,
  `role` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_logs`
--
-- Creation: Jan 12, 2018 at 05:33 PM
-- Last update: Feb 21, 2018 at 11:29 AM
--

CREATE TABLE `mangadex_logs` (
  `log_ip` varchar(45) CHARACTER SET latin1 NOT NULL,
  `log_visit` int(11) NOT NULL,
  `log_dl` int(11) NOT NULL,
  `log_rss` int(11) NOT NULL,
  `log_timestamp` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_logs_actions`
--
-- Creation: Jan 12, 2018 at 05:07 PM
-- Last update: Feb 21, 2018 at 11:29 AM
--

CREATE TABLE `mangadex_logs_actions` (
  `action_id` int(10) UNSIGNED NOT NULL,
  `action_name` varchar(64) NOT NULL,
  `action_user_id` int(10) UNSIGNED NOT NULL,
  `action_timestamp` int(10) NOT NULL,
  `action_ip` varchar(45) NOT NULL,
  `action_result` tinyint(1) UNSIGNED NOT NULL,
  `action_details` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_logs_cron`
--
-- Creation: Jan 12, 2018 at 05:33 PM
--

CREATE TABLE `mangadex_logs_cron` (
  `id` int(10) UNSIGNED NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `result` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_logs_rss`
--
-- Creation: Jan 12, 2018 at 05:34 PM
--

CREATE TABLE `mangadex_logs_rss` (
  `visit_id` int(10) UNSIGNED NOT NULL,
  `visit_ip` varchar(45) NOT NULL,
  `visit_user_id` mediumint(8) UNSIGNED NOT NULL,
  `visit_user_agent` varchar(255) NOT NULL,
  `visit_referrer` varchar(255) NOT NULL,
  `visit_timestamp` int(10) UNSIGNED NOT NULL,
  `visit_page` varchar(255) NOT NULL,
  `visit_h_toggle` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_logs_visits`
--
-- Creation: Jan 12, 2018 at 05:34 PM
-- Last update: Feb 21, 2018 at 11:29 AM
--

CREATE TABLE `mangadex_logs_visits` (
  `visit_id` int(10) UNSIGNED NOT NULL,
  `visit_ip` varchar(45) NOT NULL,
  `visit_user_id` mediumint(8) UNSIGNED NOT NULL,
  `visit_user_agent` varchar(255) NOT NULL,
  `visit_referrer` varchar(255) NOT NULL,
  `visit_timestamp` int(10) UNSIGNED NOT NULL,
  `visit_page` varchar(255) NOT NULL,
  `visit_h_toggle` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_mangas`
--
-- Creation: Feb 21, 2018 at 02:10 AM
-- Last update: Feb 21, 2018 at 11:29 AM
--

CREATE TABLE `mangadex_mangas` (
  `manga_id` smallint(5) UNSIGNED NOT NULL,
  `manga_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manga_alt_names` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manga_author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manga_artist` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manga_lang_id` tinyint(2) NOT NULL,
  `manga_status_id` tinyint(1) NOT NULL,
  `manga_hentai` tinyint(1) NOT NULL,
  `manga_description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `manga_image` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `manga_rating` smallint(3) UNSIGNED NOT NULL,
  `manga_views` int(10) UNSIGNED NOT NULL,
  `manga_follows` smallint(6) UNSIGNED NOT NULL,
  `manga_last_updated` int(10) UNSIGNED NOT NULL,
  `manga_comments` smallint(5) UNSIGNED NOT NULL,
  `manga_mal_id` mediumint(8) NOT NULL,
  `manga_mu_id` mediumint(8) NOT NULL,
  `manga_locked` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_manga_alt_names`
--
-- Creation: Feb 11, 2018 at 01:00 AM
--

CREATE TABLE `mangadex_manga_alt_names` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `manga_id` smallint(5) UNSIGNED NOT NULL,
  `alt_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lang_id` tinyint(3) UNSIGNED NOT NULL,
  `user_id` mediumint(8) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_manga_genres`
--
-- Creation: Feb 04, 2018 at 10:19 PM
-- Last update: Feb 21, 2018 at 11:25 AM
--

CREATE TABLE `mangadex_manga_genres` (
  `manga_id` smallint(5) UNSIGNED NOT NULL,
  `genre_id` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_manga_rating`
--
-- Creation: Jan 12, 2018 at 05:41 PM
--

CREATE TABLE `mangadex_manga_rating` (
  `id` int(10) UNSIGNED NOT NULL,
  `manga_id` mediumint(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(45) NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(2) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_manga_reports`
--
-- Creation: Feb 14, 2018 at 09:45 PM
--

CREATE TABLE `mangadex_manga_reports` (
  `report_id` mediumint(8) UNSIGNED NOT NULL,
  `report_chapter_id` int(10) UNSIGNED NOT NULL,
  `report_timestamp` int(10) UNSIGNED NOT NULL,
  `report_type` tinyint(1) NOT NULL,
  `report_info` varchar(255) NOT NULL,
  `report_user_id` int(10) UNSIGNED NOT NULL,
  `report_mod_user_id` int(10) UNSIGNED NOT NULL,
  `report_conclusion` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_pm_msgs`
--
-- Creation: Jan 12, 2018 at 05:34 PM
-- Last update: Feb 21, 2018 at 10:56 AM
--

CREATE TABLE `mangadex_pm_msgs` (
  `msg_id` mediumint(8) UNSIGNED NOT NULL,
  `thread_id` mediumint(8) UNSIGNED NOT NULL,
  `user_id` mediumint(8) UNSIGNED NOT NULL,
  `msg_timestamp` int(10) UNSIGNED NOT NULL,
  `msg_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_pm_threads`
--
-- Creation: Jan 12, 2018 at 05:35 PM
-- Last update: Feb 21, 2018 at 11:01 AM
--

CREATE TABLE `mangadex_pm_threads` (
  `thread_id` mediumint(8) UNSIGNED NOT NULL,
  `thread_subject` varchar(128) NOT NULL,
  `sender_id` mediumint(8) UNSIGNED NOT NULL,
  `recipient_id` mediumint(8) UNSIGNED NOT NULL,
  `thread_timestamp` int(10) UNSIGNED NOT NULL,
  `sender_read` tinyint(1) UNSIGNED NOT NULL,
  `recipient_read` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_timezone`
--
-- Creation: Jan 12, 2018 at 05:35 PM
--

CREATE TABLE `mangadex_timezone` (
  `difference` varchar(4) NOT NULL DEFAULT '0',
  `timezone` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_users`
--
-- Creation: Feb 20, 2018 at 10:32 PM
-- Last update: Feb 21, 2018 at 11:29 AM
--

CREATE TABLE `mangadex_users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `password_smf` char(32) NOT NULL,
  `token` char(32) NOT NULL DEFAULT '',
  `level_id` tinyint(2) UNSIGNED NOT NULL DEFAULT '1',
  `email` varchar(64) NOT NULL DEFAULT '',
  `language` tinyint(4) NOT NULL DEFAULT '1',
  `display_lang_id` int(11) NOT NULL,
  `default_lang_ids` varchar(255) NOT NULL,
  `style` tinyint(4) NOT NULL DEFAULT '1',
  `joined_timestamp` int(10) UNSIGNED NOT NULL,
  `last_seen_timestamp` int(10) UNSIGNED NOT NULL,
  `avatar` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cip` varchar(45) DEFAULT NULL,
  `time_offset` varchar(4) NOT NULL DEFAULT '0',
  `activation_key` char(32) NOT NULL,
  `activated` tinyint(1) UNSIGNED NOT NULL,
  `user_website` varchar(128) NOT NULL,
  `user_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `upload_group_id` smallint(5) UNSIGNED NOT NULL,
  `upload_lang_id` tinyint(3) UNSIGNED NOT NULL,
  `read_announcement` tinyint(1) UNSIGNED NOT NULL,
  `user_views` mediumint(8) UNSIGNED NOT NULL,
  `user_uploads` mediumint(8) UNSIGNED NOT NULL,
  `hentai_mode` tinyint(1) UNSIGNED NOT NULL,
  `swipe_direction` tinyint(1) UNSIGNED NOT NULL,
  `swipe_sensitivity` tinyint(3) UNSIGNED NOT NULL,
  `reader_mode` tinyint(3) UNSIGNED NOT NULL,
  `reader_click` tinyint(1) UNSIGNED NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_user_levels`
--
-- Creation: Feb 18, 2018 at 08:20 PM
--

CREATE TABLE `mangadex_user_levels` (
  `level_id` tinyint(2) UNSIGNED NOT NULL,
  `level_name` varchar(32) NOT NULL DEFAULT '',
  `level_colour` char(3) NOT NULL,
  `edit_torrents` tinyint(1) NOT NULL,
  `delete_torrents` tinyint(1) NOT NULL,
  `edit_users` tinyint(1) NOT NULL,
  `delete_users` tinyint(1) NOT NULL,
  `edit_news` tinyint(1) NOT NULL,
  `delete_news` tinyint(1) NOT NULL,
  `can_upload` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mangadex_views_cumulative`
--
-- Creation: Feb 01, 2018 at 03:59 AM
-- Last update: Feb 21, 2018 at 11:29 AM
--

CREATE TABLE `mangadex_views_cumulative` (
  `ip_type_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` smallint(5) UNSIGNED NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL,
  `count` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batoto_comics_bk`
--
ALTER TABLE `batoto_comics_bk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mangadex_chapters`
--
ALTER TABLE `mangadex_chapters`
  ADD PRIMARY KEY (`chapter_id`),
  ADD UNIQUE KEY `unique` (`chapter_hash`);

--
-- Indexes for table `mangadex_chapter_reports`
--
ALTER TABLE `mangadex_chapter_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD UNIQUE KEY `unique` (`report_chapter_id`,`report_type`);

--
-- Indexes for table `mangadex_chapter_views`
--
ALTER TABLE `mangadex_chapter_views`
  ADD UNIQUE KEY `unique` (`chapter_id`,`user_id`);

--
-- Indexes for table `mangadex_comments_groups`
--
ALTER TABLE `mangadex_comments_groups`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `mangadex_comments_manga`
--
ALTER TABLE `mangadex_comments_manga`
  ADD PRIMARY KEY (`comment_id`);

--
-- Indexes for table `mangadex_countries`
--
ALTER TABLE `mangadex_countries`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `mangadex_follow_user_manga`
--
ALTER TABLE `mangadex_follow_user_manga`
  ADD UNIQUE KEY `unique` (`user_id`,`manga_id`);

--
-- Indexes for table `mangadex_forums`
--
ALTER TABLE `mangadex_forums`
  ADD PRIMARY KEY (`forum_id`);

--
-- Indexes for table `mangadex_forum_posts`
--
ALTER TABLE `mangadex_forum_posts`
  ADD PRIMARY KEY (`post_id`);

--
-- Indexes for table `mangadex_forum_threads`
--
ALTER TABLE `mangadex_forum_threads`
  ADD PRIMARY KEY (`thread_id`);

--
-- Indexes for table `mangadex_genres`
--
ALTER TABLE `mangadex_genres`
  ADD PRIMARY KEY (`genre_id`);

--
-- Indexes for table `mangadex_groups`
--
ALTER TABLE `mangadex_groups`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `mangadex_group_likes`
--
ALTER TABLE `mangadex_group_likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mangadex_import`
--
ALTER TABLE `mangadex_import`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mangadex_languages`
--
ALTER TABLE `mangadex_languages`
  ADD PRIMARY KEY (`lang_id`);

--
-- Indexes for table `mangadex_link_user_group`
--
ALTER TABLE `mangadex_link_user_group`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique` (`user_id`,`group_id`,`role`);

--
-- Indexes for table `mangadex_logs`
--
ALTER TABLE `mangadex_logs`
  ADD PRIMARY KEY (`log_ip`);

--
-- Indexes for table `mangadex_logs_actions`
--
ALTER TABLE `mangadex_logs_actions`
  ADD PRIMARY KEY (`action_id`);

--
-- Indexes for table `mangadex_logs_cron`
--
ALTER TABLE `mangadex_logs_cron`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mangadex_logs_rss`
--
ALTER TABLE `mangadex_logs_rss`
  ADD PRIMARY KEY (`visit_id`);

--
-- Indexes for table `mangadex_logs_visits`
--
ALTER TABLE `mangadex_logs_visits`
  ADD PRIMARY KEY (`visit_id`);

--
-- Indexes for table `mangadex_mangas`
--
ALTER TABLE `mangadex_mangas`
  ADD PRIMARY KEY (`manga_id`);

--
-- Indexes for table `mangadex_manga_alt_names`
--
ALTER TABLE `mangadex_manga_alt_names`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index` (`manga_id`);

--
-- Indexes for table `mangadex_manga_genres`
--
ALTER TABLE `mangadex_manga_genres`
  ADD UNIQUE KEY `unique` (`manga_id`,`genre_id`);

--
-- Indexes for table `mangadex_manga_rating`
--
ALTER TABLE `mangadex_manga_rating`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mangadex_manga_reports`
--
ALTER TABLE `mangadex_manga_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD UNIQUE KEY `unique` (`report_chapter_id`,`report_type`);

--
-- Indexes for table `mangadex_pm_msgs`
--
ALTER TABLE `mangadex_pm_msgs`
  ADD PRIMARY KEY (`msg_id`);

--
-- Indexes for table `mangadex_pm_threads`
--
ALTER TABLE `mangadex_pm_threads`
  ADD PRIMARY KEY (`thread_id`);

--
-- Indexes for table `mangadex_timezone`
--
ALTER TABLE `mangadex_timezone`
  ADD PRIMARY KEY (`difference`);

--
-- Indexes for table `mangadex_users`
--
ALTER TABLE `mangadex_users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `mangadex_user_levels`
--
ALTER TABLE `mangadex_user_levels`
  ADD UNIQUE KEY `base` (`level_id`);

--
-- Indexes for table `mangadex_views_cumulative`
--
ALTER TABLE `mangadex_views_cumulative`
  ADD PRIMARY KEY (`ip_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batoto_comics_bk`
--
ALTER TABLE `batoto_comics_bk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23040;

--
-- AUTO_INCREMENT for table `mangadex_chapters`
--
ALTER TABLE `mangadex_chapters`
  MODIFY `chapter_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102690;

--
-- AUTO_INCREMENT for table `mangadex_chapter_reports`
--
ALTER TABLE `mangadex_chapter_reports`
  MODIFY `report_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=943;

--
-- AUTO_INCREMENT for table `mangadex_comments_groups`
--
ALTER TABLE `mangadex_comments_groups`
  MODIFY `comment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=264;

--
-- AUTO_INCREMENT for table `mangadex_comments_manga`
--
ALTER TABLE `mangadex_comments_manga`
  MODIFY `comment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7348;

--
-- AUTO_INCREMENT for table `mangadex_countries`
--
ALTER TABLE `mangadex_countries`
  MODIFY `country_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=245;

--
-- AUTO_INCREMENT for table `mangadex_forums`
--
ALTER TABLE `mangadex_forums`
  MODIFY `forum_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `mangadex_forum_posts`
--
ALTER TABLE `mangadex_forum_posts`
  MODIFY `post_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `mangadex_forum_threads`
--
ALTER TABLE `mangadex_forum_threads`
  MODIFY `thread_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `mangadex_genres`
--
ALTER TABLE `mangadex_genres`
  MODIFY `genre_id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `mangadex_groups`
--
ALTER TABLE `mangadex_groups`
  MODIFY `group_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1941;

--
-- AUTO_INCREMENT for table `mangadex_group_likes`
--
ALTER TABLE `mangadex_group_likes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3211;

--
-- AUTO_INCREMENT for table `mangadex_import`
--
ALTER TABLE `mangadex_import`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mangadex_languages`
--
ALTER TABLE `mangadex_languages`
  MODIFY `lang_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `mangadex_link_user_group`
--
ALTER TABLE `mangadex_link_user_group`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=738;

--
-- AUTO_INCREMENT for table `mangadex_logs_actions`
--
ALTER TABLE `mangadex_logs_actions`
  MODIFY `action_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1395230;

--
-- AUTO_INCREMENT for table `mangadex_logs_cron`
--
ALTER TABLE `mangadex_logs_cron`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mangadex_logs_rss`
--
ALTER TABLE `mangadex_logs_rss`
  MODIFY `visit_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mangadex_logs_visits`
--
ALTER TABLE `mangadex_logs_visits`
  MODIFY `visit_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2537701;

--
-- AUTO_INCREMENT for table `mangadex_mangas`
--
ALTER TABLE `mangadex_mangas`
  MODIFY `manga_id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23527;

--
-- AUTO_INCREMENT for table `mangadex_manga_alt_names`
--
ALTER TABLE `mangadex_manga_alt_names`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54242;

--
-- AUTO_INCREMENT for table `mangadex_manga_rating`
--
ALTER TABLE `mangadex_manga_rating`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mangadex_manga_reports`
--
ALTER TABLE `mangadex_manga_reports`
  MODIFY `report_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mangadex_pm_msgs`
--
ALTER TABLE `mangadex_pm_msgs`
  MODIFY `msg_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=994;

--
-- AUTO_INCREMENT for table `mangadex_pm_threads`
--
ALTER TABLE `mangadex_pm_threads`
  MODIFY `thread_id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=386;

--
-- AUTO_INCREMENT for table `mangadex_users`
--
ALTER TABLE `mangadex_users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20985;

--
-- AUTO_INCREMENT for table `mangadex_user_levels`
--
ALTER TABLE `mangadex_user_levels`
  MODIFY `level_id` tinyint(2) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
