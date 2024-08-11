-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 25, 2024 at 11:35 AM
-- Server version: 10.6.18-MariaDB
-- PHP Version: 8.1.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `newsaeeh_saeeh`
--

-- --------------------------------------------------------

--
-- Stand-in structure for view `category_with_items`
-- (See below for the actual view)
--
CREATE TABLE `category_with_items` (
`id` bigint(20) unsigned
,`name_ar` varchar(255)
,`name_en` varchar(255)
,`parent_id` bigint(20) unsigned
,`city_id` varchar(255)
,`country_id` bigint(20) unsigned
,`type` varchar(20)
,`image` varchar(255)
,`icon` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `city_with_items`
-- (See below for the actual view)
--
CREATE TABLE `city_with_items` (
`id` bigint(20) unsigned
,`name_ar` varchar(255)
,`name_en` varchar(255)
,`country_id` bigint(20) unsigned
,`type` varchar(20)
,`image` varchar(255)
,`latitude` double
,`longitude` double
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `countries_with_items`
-- (See below for the actual view)
--
CREATE TABLE `countries_with_items` (
`id` bigint(20) unsigned
,`name_ar` varchar(255)
,`name_en` varchar(255)
,`type` varchar(20)
,`flag_image` varchar(255)
,`image` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `subcategory_with_items`
-- (See below for the actual view)
--
CREATE TABLE `subcategory_with_items` (
`id` bigint(20) unsigned
,`name_ar` varchar(255)
,`name_en` varchar(255)
,`parent_id` bigint(20) unsigned
,`city_id` varchar(255)
,`country_id` bigint(20) unsigned
,`type` varchar(20)
,`image` varchar(255)
,`icon` varchar(255)
);

-- --------------------------------------------------------

--
-- Structure for view `category_with_items`
--
DROP TABLE IF EXISTS `category_with_items`;

CREATE ALGORITHM=UNDEFINED DEFINER=`newsaeeh`@`localhost` SQL SECURITY DEFINER VIEW `category_with_items`  AS SELECT `categories`.`id` AS `id`, `categories`.`name_ar` AS `name_ar`, `categories`.`name_en` AS `name_en`, `categories`.`parent_id` AS `parent_id`, `aqars`.`city_id` AS `city_id`, `aqars`.`country_id` AS `country_id`, 'aqar' FROM (`categories` join `aqars` on(`aqars`.`category_id` = `categories`.`id`)) WHERE `categories`.`active` = 1 AND `categories`.`deleted_at` is null AND `aqars`.`deleted_at` is null GROUP BY `categories`.`id`union select `categories`.`id` AS `id`,`categories`.`name_ar` AS `name_ar`,`categories`.`name_en` AS `name_en`,`categories`.`parent_id` AS `parent_id`,`cars`.`city_id` AS `city_id`,`cars`.`country_id` AS `country_id`,'cars' collate utf8mb4_unicode_ci AS `type`,`categories`.`image` AS `image`,`categories`.`icon` AS `icon` from (`categories` join `cars` on(`cars`.`category_id` = `categories`.`id`)) where `categories`.`parent_id` = 2 and `categories`.`deleted_at` is null and `cars`.`deleted_at` is null group by `cars`.`city_id`,`categories`.`id` union select `categories`.`id` AS `id`,`categories`.`name_ar` AS `name_ar`,`categories`.`name_en` AS `name_en`,`categories`.`parent_id` AS `parent_id`,if(`places`.`flag_of_multicities` = 1,`places`.`multicities`,`places`.`city_id`) AS `city_id`,`places`.`country_id` AS `country_id`,'place' collate utf8mb4_unicode_ci AS `type`,`categories`.`image` AS `image`,`categories`.`icon` AS `icon` from (`categories` join `places` on(`places`.`category_id` = `categories`.`id`)) where `categories`.`active` = 1 and `categories`.`parent_id` is null and `categories`.`deleted_at` is null and `places`.`active` = 1 and `places`.`deleted_at` is null group by `places`.`city_id`  ;

-- --------------------------------------------------------

--
-- Structure for view `city_with_items`
--
DROP TABLE IF EXISTS `city_with_items`;

CREATE ALGORITHM=UNDEFINED DEFINER=`newsaeeh`@`localhost` SQL SECURITY DEFINER VIEW `city_with_items`  AS SELECT `cities`.`id` AS `id`, `cities`.`name_ar` AS `name_ar`, `cities`.`name_en` AS `name_en`, `cities`.`country_id` AS `country_id`, 'aqar' FROM (`cities` join `aqars` on(`aqars`.`city_id` = `cities`.`id`)) WHERE `cities`.`active` = 1 AND `cities`.`deleted_at` is null AND `aqars`.`deleted_at` is null GROUP BY `cities`.`id`union select `cities`.`id` AS `id`,`cities`.`name_ar` AS `name_ar`,`cities`.`name_en` AS `name_en`,`cities`.`country_id` AS `country_id`,'cars' collate utf8mb4_unicode_ci AS `type`,`cities`.`image` AS `image`,`cities`.`latitude` AS `latitude`,`cities`.`longitude` AS `longitude` from (`cities` join `cars` on(`cars`.`city_id` = `cities`.`id`)) where `cities`.`active` = 1 and `cities`.`deleted_at` is null and `cities`.`deleted_at` is null and `cars`.`active` = 1 and `cars`.`deleted_at` is null group by `cities`.`id` union select `cities`.`id` AS `id`,`cities`.`name_ar` AS `name_ar`,`cities`.`name_en` AS `name_en`,`cities`.`country_id` AS `country_id`,'place' collate utf8mb4_unicode_ci AS `type`,`cities`.`image` AS `image`,`cities`.`latitude` AS `latitude`,`cities`.`longitude` AS `longitude` from (`cities` join `places` on(`places`.`city_id` = `cities`.`id` or find_in_set(`cities`.`id`,`places`.`multicities`))) where `cities`.`active` = 1 and `cities`.`deleted_at` is null and `places`.`active` = 1 and `places`.`deleted_at` is null group by `cities`.`id`  ;

-- --------------------------------------------------------

--
-- Structure for view `countries_with_items`
--
DROP TABLE IF EXISTS `countries_with_items`;

CREATE ALGORITHM=UNDEFINED DEFINER=`newsaeeh`@`localhost` SQL SECURITY DEFINER VIEW `countries_with_items`  AS SELECT `countries`.`id` AS `id`, `countries`.`name_ar` AS `name_ar`, `countries`.`name_en` AS `name_en`, 'aqar' FROM (`countries` join `aqars` on(`aqars`.`country_id` = `countries`.`id`)) WHERE `countries`.`active` = 1 AND `countries`.`deleted_at` is null AND `aqars`.`deleted_at` is null GROUP BY `countries`.`id`union select `countries`.`id` AS `id`,`countries`.`name_ar` AS `name_ar`,`countries`.`name_en` AS `name_en`,'cars' collate utf8mb4_unicode_ci AS `type`,`countries`.`flag_image` AS `flag_image`,`countries`.`image` AS `image` from (`countries` join `cars` on(`cars`.`country_id` = `countries`.`id`)) where `countries`.`active` = 1 and `countries`.`deleted_at` is null and `cars`.`active` = 1 and `cars`.`deleted_at` is null group by `countries`.`id` union select `countries`.`id` AS `id`,`countries`.`name_ar` AS `name_ar`,`countries`.`name_en` AS `name_en`,'place' collate utf8mb4_unicode_ci AS `type`,`countries`.`flag_image` AS `flag_image`,`countries`.`image` AS `image` from (`countries` join `places` on(`places`.`country_id` = `countries`.`id`)) where `countries`.`active` = 1 and `countries`.`deleted_at` is null and `places`.`active` = 1 and `places`.`deleted_at` is null group by `countries`.`id`  ;

-- --------------------------------------------------------

--
-- Structure for view `subcategory_with_items`
--
DROP TABLE IF EXISTS `subcategory_with_items`;

CREATE ALGORITHM=UNDEFINED DEFINER=`newsaeeh`@`localhost` SQL SECURITY DEFINER VIEW `subcategory_with_items`  AS SELECT `categories`.`id` AS `id`, `categories`.`name_ar` AS `name_ar`, `categories`.`name_en` AS `name_en`, `categories`.`parent_id` AS `parent_id`, `cars`.`city_id` AS `city_id`, `cars`.`country_id` AS `country_id`, 'cars' FROM (`categories` join `cars` on(`cars`.`sub_category_id` = `categories`.`id`)) WHERE `categories`.`active` = 1 AND `categories`.`deleted_at` is null AND `cars`.`deleted_at` is null GROUP BY `cars`.`city_id`, `categories`.`id`union select `categories`.`id` AS `id`,`categories`.`name_ar` AS `name_ar`,`categories`.`name_en` AS `name_en`,`categories`.`parent_id` AS `parent_id`,if(`places`.`flag_of_multicities` = 1,`places`.`multicities`,`places`.`city_id`) AS `city_id`,`places`.`country_id` AS `country_id`,'place' collate utf8mb4_unicode_ci AS `type`,`categories`.`image` AS `image`,`categories`.`icon` AS `icon` from (`categories` join `places` on(`places`.`sub_category_id` = `categories`.`id`)) where `categories`.`deleted_at` is null and `places`.`deleted_at` is null group by if(`places`.`flag_of_multicities` = 1,`places`.`multicities`,`places`.`city_id`),`categories`.`id`  ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
