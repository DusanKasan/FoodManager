-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Hostiteľ: localhost
-- Vygenerované: Po 10.Jún 2013, 06:35
-- Verzia serveru: 5.5.24-log
-- Verzia PHP: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Databáza: `food_manager`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id_comment` int(11) NOT NULL AUTO_INCREMENT,
  `comment` text COLLATE utf8_bin NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id_comment`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `foods`
--

CREATE TABLE IF NOT EXISTS `foods` (
  `id_food` int(11) NOT NULL AUTO_INCREMENT,
  `food` text CHARACTER SET utf8 NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `id_user` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_finished` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_food`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=89 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `foods_comments`
--

CREATE TABLE IF NOT EXISTS `foods_comments` (
  `id_food` int(11) NOT NULL,
  `id_comment` int(11) NOT NULL,
  PRIMARY KEY (`id_food`,`id_comment`),
  KEY `id_comment` (`id_comment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `foods_ingredients`
--

CREATE TABLE IF NOT EXISTS `foods_ingredients` (
  `id_food_ingredient` int(11) NOT NULL AUTO_INCREMENT,
  `id_food` int(11) NOT NULL,
  `id_ingredient` int(11) NOT NULL,
  `amount` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_food_ingredient`),
  KEY `id_food` (`id_food`,`id_ingredient`),
  KEY `id_ingredient` (`id_ingredient`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=59 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `foods_pictures`
--

CREATE TABLE IF NOT EXISTS `foods_pictures` (
  `id_food_picture` int(11) NOT NULL AUTO_INCREMENT,
  `id_food` int(11) NOT NULL,
  `id_uploaded_file` int(11) NOT NULL,
  PRIMARY KEY (`id_food_picture`),
  KEY `id_food` (`id_food`),
  KEY `id_uploaded_file` (`id_uploaded_file`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `foods_tags`
--

CREATE TABLE IF NOT EXISTS `foods_tags` (
  `id_food_tag` int(11) NOT NULL AUTO_INCREMENT,
  `id_food` int(11) NOT NULL,
  `id_tag` int(11) NOT NULL,
  PRIMARY KEY (`id_food_tag`),
  KEY `id_tag` (`id_tag`),
  KEY `foods_tags_ibfk_1` (`id_food`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `ingredients`
--

CREATE TABLE IF NOT EXISTS `ingredients` (
  `id_ingredient` int(11) NOT NULL AUTO_INCREMENT,
  `ingredient` text COLLATE utf8_bin NOT NULL,
  `match` text COLLATE utf8_bin NOT NULL COMMENT 'Match this after iconv ASCII//TRANSLIT',
  PRIMARY KEY (`id_ingredient`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=35 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `roles_resources_privileges`
--

CREATE TABLE IF NOT EXISTS `roles_resources_privileges` (
  `id_role_resource_privilege` int(11) NOT NULL AUTO_INCREMENT,
  `id_role` int(11) NOT NULL,
  `resource` varchar(255) COLLATE utf8_bin NOT NULL,
  `privilege` enum('create','view','edit','remove') COLLATE utf8_bin NOT NULL DEFAULT 'view',
  PRIMARY KEY (`id_role_resource_privilege`),
  UNIQUE KEY `unique_role_resource_privilege` (`id_role`,`resource`,`privilege`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id_tag` int(11) NOT NULL AUTO_INCREMENT,
  `tag` text COLLATE utf8_bin NOT NULL,
  `match` text COLLATE utf8_bin NOT NULL COMMENT 'Match this after iconv to ASCII//TRANSLIT',
  `is_category` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'is this tag a category',
  PRIMARY KEY (`id_tag`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table of tags and category flags' AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `uploaded_files`
--

CREATE TABLE IF NOT EXISTS `uploaded_files` (
  `id_file` int(11) NOT NULL AUTO_INCREMENT,
  `filename` text COLLATE utf8_bin NOT NULL,
  `original_filename` text COLLATE utf8_bin NOT NULL,
  `size` int(11) NOT NULL COMMENT 'in bytes',
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_file`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_bin NOT NULL,
  `password` varchar(255) COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_file_avatar` int(11) NOT NULL COMMENT 'avatar image in uploaded_files',
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=137 ;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `users_roles`
--

CREATE TABLE IF NOT EXISTS `users_roles` (
  `id_user` int(11) NOT NULL,
  `id_role` int(11) NOT NULL,
  PRIMARY KEY (`id_user`,`id_role`),
  KEY `id_role` (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `foods`
--
ALTER TABLE `foods`
  ADD CONSTRAINT `foods_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `foods_comments`
--
ALTER TABLE `foods_comments`
  ADD CONSTRAINT `foods_comments_ibfk_1` FOREIGN KEY (`id_food`) REFERENCES `foods` (`id_food`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `foods_comments_ibfk_2` FOREIGN KEY (`id_comment`) REFERENCES `comments` (`id_comment`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `foods_ingredients`
--
ALTER TABLE `foods_ingredients`
  ADD CONSTRAINT `foods_ingredients_ibfk_1` FOREIGN KEY (`id_food`) REFERENCES `foods` (`id_food`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `foods_ingredients_ibfk_2` FOREIGN KEY (`id_ingredient`) REFERENCES `ingredients` (`id_ingredient`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `foods_pictures`
--
ALTER TABLE `foods_pictures`
  ADD CONSTRAINT `foods_pictures_ibfk_1` FOREIGN KEY (`id_food`) REFERENCES `foods` (`id_food`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `foods_pictures_ibfk_2` FOREIGN KEY (`id_uploaded_file`) REFERENCES `uploaded_files` (`id_file`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `foods_tags`
--
ALTER TABLE `foods_tags`
  ADD CONSTRAINT `foods_tags_ibfk_1` FOREIGN KEY (`id_food`) REFERENCES `foods` (`id_food`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `foods_tags_ibfk_2` FOREIGN KEY (`id_tag`) REFERENCES `tags` (`id_tag`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `roles_resources_privileges`
--
ALTER TABLE `roles_resources_privileges`
  ADD CONSTRAINT `roles_resources_privileges_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `uploaded_files`
--
ALTER TABLE `uploaded_files`
  ADD CONSTRAINT `uploaded_files_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `users_roles`
--
ALTER TABLE `users_roles`
  ADD CONSTRAINT `users_roles_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_roles_ibfk_2` FOREIGN KEY (`id_role`) REFERENCES `roles` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
