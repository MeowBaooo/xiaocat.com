-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1:3307
-- 產生時間： 2025-05-12 11:21:20
-- 伺服器版本： 10.4.24-MariaDB
-- PHP 版本： 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `petlogbook`
--

-- --------------------------------------------------------

--
-- 資料表結構 `food_logs`
--

CREATE TABLE `food_logs` (
  `id` int(11) NOT NULL,
  `pet_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `meal_time` enum('早餐','午餐','晚餐','點心') DEFAULT NULL,
  `food_name` varchar(100) DEFAULT NULL,
  `qty` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `food_logs`
--

INSERT INTO `food_logs` (`id`, `pet_id`, `date`, `meal_time`, `food_name`, `qty`, `notes`, `created_at`) VALUES
(1, 2, '2025-05-05', '早餐', '燻雞蛋', '1', '', '2025-05-05 11:51:22'),
(2, 1, '2025-05-09', '早餐', '培根蛋餅', '10', '', '2025-05-09 17:25:23'),
(3, 5, '2025-05-12', '早餐', '鮪魚蛋餅', '2', '因為沒有培根吃了鮪魚', '2025-05-12 09:19:19');

-- --------------------------------------------------------

--
-- 資料表結構 `health_logs`
--

CREATE TABLE `health_logs` (
  `id` int(11) NOT NULL,
  `pet_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `item_type` enum('疫苗','體重','看診') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `value` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `health_logs`
--

INSERT INTO `health_logs` (`id`, `pet_id`, `date`, `item_type`, `notes`, `value`, `created_at`) VALUES
(1, 1, '2025-05-01', '體重', '', '5.7', '2025-05-05 11:37:27'),
(2, 1, '2025-05-02', '體重', '', '6.2', '2025-05-05 11:37:42'),
(3, 1, '2025-05-03', '體重', '', '6.8', '2025-05-05 11:37:55'),
(4, 1, '2025-05-04', '體重', '', '5.5', '2025-05-05 11:38:03'),
(5, 1, '2025-05-05', '體重', '', '5', '2025-05-05 11:38:08'),
(6, 1, '2025-05-06', '體重', '', '7.1', '2025-05-05 11:39:04'),
(7, 4, '2025-05-09', '疫苗', '', '100m', '2025-05-09 14:53:57'),
(8, 5, '2025-05-12', '體重', '好胖', '10kg', '2025-05-12 09:16:49'),
(9, 5, '2025-05-13', '體重', '太胖了', '16kg', '2025-05-12 09:17:20'),
(10, 5, '2025-05-14', '體重', '變瘦了', '14kg', '2025-05-12 09:17:42');

-- --------------------------------------------------------

--
-- 資料表結構 `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `type` varchar(30) DEFAULT NULL,
  `gender` enum('公','母') DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `pets`
--

INSERT INTO `pets` (`id`, `user_id`, `name`, `type`, `gender`, `birth_date`, `photo_path`, `created_at`) VALUES
(1, 1, '橘橘', '貓', '公', '2024-05-20', 'uploads/1746416212_S__112861192.jpg', '2025-05-05 11:36:52'),
(2, 1, '饒焉婷', 'repo', '母', '2005-11-17', 'uploads/1746417049_20250427004737_1.jpg', '2025-05-05 11:50:49'),
(3, 1, '朵彤', 'repo', '母', '2005-09-06', 'uploads/1746431824_20250420011432_1.jpg', '2025-05-05 14:11:54'),
(4, 4, '阿毛', '哈士貓', '公', '2024-05-20', 'uploads/1746598189_黃小貓.jpg', '2025-05-07 14:09:49'),
(5, 4, '包子', '貓', '公', '2024-12-12', 'uploads/1747012567_S__112336916.jpg', '2025-05-12 09:16:07');

-- --------------------------------------------------------

--
-- 資料表結構 `pet_diary`
--

CREATE TABLE `pet_diary` (
  `id` int(11) NOT NULL,
  `pet_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `pet_diary`
--

INSERT INTO `pet_diary` (`id`, `pet_id`, `date`, `title`, `content`, `image_path`, `created_at`) VALUES
(1, 1, NULL, '陪我上廁所', '躺在地上\r\n', 'uploads/1746781998_S__112877600.jpg', '2025-05-09 17:13:18'),
(2, 4, NULL, '居然!!', '主人刺了包子在身上', 'uploads/1746782200_刺青3.png', '2025-05-09 17:16:40');

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT '使用者',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 傾印資料表的資料 `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'A', '$2y$10$PWju0Xbe4K57tuBwA2oG6uGZW4RYqdXD7So7zHtgumoOWf3Xwtm66', 'user', '2025-05-05 11:33:01'),
(3, 'admin', '$2y$10$WW8zNemAysnjtmtbwXQ8Cuzq8d2Zag4vjIPIR74n4m/.2w1YvARci', 'admin', '2025-05-05 11:35:16'),
(4, 'B', '$2y$10$zW1ptgBvKZi8viRu/Od8JeqhgBrk9w2qgKwi.i9DySsl4ohcpTDwa', 'user', '2025-05-07 14:08:30'),
(5, '阿娟', '$2y$10$hjuRJymFYGDILqVnMNuzbubwQi4SxC7X7AUcEsGqtFh6xsv8VUdOm', 'admin', '2025-05-09 15:42:07');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `food_logs`
--
ALTER TABLE `food_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_id` (`pet_id`);

--
-- 資料表索引 `health_logs`
--
ALTER TABLE `health_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_id` (`pet_id`);

--
-- 資料表索引 `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- 資料表索引 `pet_diary`
--
ALTER TABLE `pet_diary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pet_id` (`pet_id`);

--
-- 資料表索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `food_logs`
--
ALTER TABLE `food_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `health_logs`
--
ALTER TABLE `health_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `pet_diary`
--
ALTER TABLE `pet_diary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `food_logs`
--
ALTER TABLE `food_logs`
  ADD CONSTRAINT `food_logs_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`);

--
-- 資料表的限制式 `health_logs`
--
ALTER TABLE `health_logs`
  ADD CONSTRAINT `health_logs_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`);

--
-- 資料表的限制式 `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- 資料表的限制式 `pet_diary`
--
ALTER TABLE `pet_diary`
  ADD CONSTRAINT `pet_diary_ibfk_1` FOREIGN KEY (`pet_id`) REFERENCES `pets` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
