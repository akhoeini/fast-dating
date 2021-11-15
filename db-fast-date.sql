-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2021 at 04:22 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.3.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `new_dating`
--

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE `albums` (
  `id` int(11) NOT NULL,
  `name` enum('profile','general') DEFAULT 'general',
  `description` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `ownerId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `albums`
--

INSERT INTO `albums` (`id`, `name`, `description`, `category`, `ownerId`) VALUES
(1, '', 'testimgtestimgtestimg', 'general', 1);

-- --------------------------------------------------------

--
-- Table structure for table `interests`
--

CREATE TABLE `interests` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `type` enum('other','indoor','outdoor','tech','animals') NOT NULL DEFAULT 'other'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `interests`
--

INSERT INTO `interests` (`id`, `name`, `description`, `type`) VALUES
(1, 'Technology', NULL, 'tech'),
(2, 'Cooking', NULL, 'other');

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `matched_user_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`id`, `user_id`, `matched_user_id`, `time`) VALUES
(1, 11, 13, '2021-11-15 01:56:38'),
(2, 13, 11, '2021-11-15 01:56:38');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `mtype` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `from_id` int(10) NOT NULL,
  `to_id` int(10) NOT NULL,
  `msg` text NOT NULL,
  `sent` timestamp NOT NULL DEFAULT current_timestamp(),
  `recd` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `mtype`, `from_id`, `to_id`, `msg`, `sent`, `recd`) VALUES
(1, 0, 0, 13, '<strong>Congratulations!</strong> You have a new matches.', '2021-11-15 01:56:38', 1),
(3, 1, 11, 13, 'aaaaa', '2021-11-15 04:45:31', 1);

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `id` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `roomId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `photo`
--

CREATE TABLE `photo` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `albumId` int(11) NOT NULL,
  `url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `photo`
--

INSERT INTO `photo` (`id`, `name`, `description`, `albumId`, `url`) VALUES
(1, 'pexels-brandan-saviour-2741701\r\n', 'test image', 1, 'img/pexels-brandan-saviour-2741701.jpg'),
(2, 'pexels-brandan-saviour-2741701\r\n', 'test image', 1, 'img/pexels-brandan-saviour-2741701.jpg'),
(3, 'pexels-marcio-bordin-1840608.jpg', NULL, 1, 'img/pexels-marcio-bordin-1840608.jpg'),
(4, 'pexels-mateus-souza-3586798.jopg', NULL, 1, 'img/pexels-mateus-souza-3586798.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `swipe_photos`
--

CREATE TABLE `swipe_photos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `swipe_photos`
--

INSERT INTO `swipe_photos` (`id`, `user_id`, `image_name`) VALUES
(1, 1, 'bace1bae4b07b159.png'),
(2, 2, '3ecfe82763dd940c.png'),
(3, 3, 'f0c86700a0dbf505.png'),
(4, 4, '323d6a445cdc18b4.png'),
(5, 5, '247a7f14ae0d07f4.png'),
(6, 6, 'bace1bae4b07b159.png'),
(7, 7, '3ecfe82763dd940c.png'),
(8, 8, 'f0c86700a0dbf505.png'),
(9, 9, '323d6a445cdc18b4.png'),
(24, 11, '11.jpeg'),
(25, 10, '10.jpeg'),
(26, 13, '13.jpeg'),
(27, 12, '12.jpeg'),
(28, 14, '14.jpeg'),
(29, 15, '15.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `userinterest`
--

CREATE TABLE `userinterest` (
  `id` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `interestId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userinterest`
--

INSERT INTO `userinterest` (`id`, `userId`, `interestId`) VALUES
(1, 1, 2),
(2, 1, 1),
(3, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `userlookingforid`
--

CREATE TABLE `userlookingforid` (
  `id` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `gender` enum('male','female','other') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `id` int(11) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `firstName` varchar(100),
  `birthDate` datetime DEFAULT NULL,
  `location` varchar(100),
  `genderId` int(11) DEFAULT NULL,
  `bio` varchar(500) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `userInterestsId` int(11) DEFAULT NULL,
  `userLookingForId` int(11) DEFAULT NULL,
  `token` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userName`, `email`, `id`, `role`, `birthDate`, `genderId`, `bio`, `password`, `userInterestsId`, `userLookingForId`, `token`) VALUES
('test2093', 'test@gmail.com', 1, 'user', '1994-11-13 00:00:00', 1, 'fiawdifhu aiuwhfoia aweihufwoi aiwuh iaweiofh iahwefiuewf iiwhefi u', 'muffin', 2, 2, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJmYXN0ZGF0aW5nLm9yZyIsImF1ZCI6ImZhc3RkYXRpbmcuY2EiLCJpYXQiOiIxIiwibmJmIjoidGVzdDIwOTMiLCJleGQiOiIyMDIxLTEyLTEyIn0.PCSCcbgROxbvcwCtkASknVkqC_yloCI6YQ1W5ghE3_eX-RDcAcHzHFcathh--9Vup8LL4CnBTeCHnzoCsUmBKxo-tF3D-nT84LeZbUF2h_A-3btU9cvoy6aMFsQ52u6YRUx3QlaiGQAtz3kR6UB6Z2zEMwf3OCAaxWaFRWz0I6U'),
('signuptest', 'signup@signup.com', 2, 'user', '1994-11-13 00:00:00', 1994, 'abcd', 'Min123456', 2, 2, NULL),
('tokentest', 'signup', 3, 'user', NULL, NULL, NULL, 'Min123456', NULL, NULL, NULL),
('tokentest2', 'signup@token.com', 4, 'user', NULL, NULL, NULL, 'Min123456', NULL, NULL, NULL),
('tokentest3', 'signup@token3.com', 5, 'user', NULL, NULL, NULL, 'Min123456', NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJmYXN0ZGF0aW5nLm9yZyIsImF1ZCI6ImZhc3RkYXRpbmcuY2EiLCJpYXQiOjUsIm5iZiI6InRva2VudGVzdDMiLCJleGQiOiIyMDIxLTEyLTExIn0.BpZRis7toDKdXPlkMv1LBk1tt0GHwu65ANyp5sA7XkYgnZqSb-c4OJwUaAAUPsNsXm8QNzIUx5s8xbD5YA7_-3DgRBGR4eX3MCBYsd7OsXBK1CRN_bbtczE9GHAK5Hi8l2xRQW1jkgZJtrsLYJJ4kp3elyPEsjvUnpzXmlzY4tE'),
('testtoken 6', 'testtoken6@test.com', 6, 'user', NULL, NULL, NULL, 'Min123456', NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJmYXN0ZGF0aW5nLm9yZyIsImF1ZCI6ImZhc3RkYXRpbmcuY2EiLCJpYXQiOjYsIm5iZiI6InRlc3R0b2tlbiA2IiwiZXhkIjoiMjAyMS0xMi0xMSJ9.CaNzz950_3BHdl9SQes4I79O06HIrrivn-i_ET8paUA--WZQHucJMnlGN0DXZQABZea05skMKBCnhnvTXjor84Y7uA6Tuu_0J642E-4P0b0GTh8mJJAjA0p39Upcd3oMSv37oGNjtS3m9sZPHGvyndVnku9gJjkQbPEcEkCQt7M'),
('testtoken7', 'testtoken7@test.com', 7, 'user', NULL, NULL, NULL, 'Min123456', NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJmYXN0ZGF0aW5nLm9yZyIsImF1ZCI6ImZhc3RkYXRpbmcuY2EiLCJpYXQiOjcsIm5iZiI6InRlc3R0b2tlbjciLCJleGQiOiIyMDIxLTEyLTExIn0.GiJlib1pc5IKwy1yLEJQOQPmrcASBs9_Bg-e_clgbN5yOoPXpN8pLjjch40LTLXo0a7pweAL7F-8RpjyXROuc-RhhJWpXQ9F__w5AyXehggaWdr5K3uzppzQ1rfwQx-k-WO7FoTuAhdEHIG4NixeK4ZQ_f0C6HGy1NThoMR5iW4'),
('token10', 'token10@token10.com', 8, 'user', NULL, NULL, NULL, 'token10', NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJmYXN0ZGF0aW5nLm9yZyIsImF1ZCI6ImZhc3RkYXRpbmcuY2EiLCJpYXQiOjgsIm5iZiI6InRva2VuMTAiLCJleGQiOiIyMDIxLTEyLTExIn0.EcJEXuP8VaDXH9F4DAg3wfgi9-XmW53OT7nT2sMQV1fMcV5cvCWNrV7Tcx-x8Y3Tdenub9TtuhPxaRVBEgH1tuphkm-g0PHgObqteULaaWR1KL1BKFbJ9gq-hISX7gLDMZnjRGy4ID1t4GbF3Y0rINzgFf710Ma_Oehs1pz6vLg'),
('token12', 'token12@token12.com', 9, 'user', NULL, NULL, NULL, 'token12@A', NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJmYXN0ZGF0aW5nLm9yZyIsImF1ZCI6ImZhc3RkYXRpbmcuY2EiLCJpYXQiOjksIm5iZiI6InRva2VuMTIiLCJleGQiOiIyMDIxLTEyLTExIn0.XflyD9FZ5DBBJ0bBwVO0fxv1daDeEbrFSq-w4FAuHwwBndgSfx10KX3hlNUJdDQSqDhlFppyf_4HbRA8rG9z9qR0zU7F3vSbrSFSjkpSEj4qK9FyOO9Q86gBaT24JZRlNUM4UY3Lt8InzxU8xJWEuy9ZIS2tv1mIhjUYpsxgEXg'),
('mike1', 'test1@gmail.com', 10, 'user', NULL, NULL, NULL, '111111', NULL, NULL, NULL),
('John Smith', 'test2@gmail.com', 11, 'user', NULL, NULL, NULL, '111111', NULL, NULL, NULL),
('Tom Hanks', 'test3@gmail.com', 12, 'user', NULL, NULL, NULL, '111111', NULL, NULL, NULL),
('Hello Kitty', 'test4@gmail.com', 13, 'user', NULL, NULL, NULL, '111111', NULL, NULL, NULL),
('Jerry Wang', 'test5@gmail.com', 14, 'user', NULL, NULL, NULL, '111111', NULL, NULL, NULL),
('Maria', 'test6@gmail.com', 15, 'user', NULL, NULL, NULL, '111111', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_likes`
--

CREATE TABLE `user_likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `liked_user_id` int(11) NOT NULL,
  `operation` enum('like','pass') NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_likes`
--

INSERT INTO `user_likes` (`id`, `user_id`, `liked_user_id`, `operation`, `time`) VALUES
(1, 13, 11, 'like', '2021-11-15 01:56:34'),
(2, 11, 8, 'pass', '2021-11-15 01:56:35'),
(3, 11, 1, 'pass', '2021-11-15 01:56:35'),
(4, 11, 9, 'pass', '2021-11-15 01:56:36'),
(5, 11, 15, 'pass', '2021-11-15 01:56:37'),
(6, 11, 13, 'like', '2021-11-15 01:56:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `albums`
--
ALTER TABLE `albums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkOwenerId` (`ownerId`);

--
-- Indexes for table `interests`
--
ALTER TABLE `interests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkParticipantsUId` (`userId`),
  ADD KEY `fkParticipantsRoom` (`roomId`);

--
-- Indexes for table `photo`
--
ALTER TABLE `photo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkAlbumPhoto` (`albumId`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `swipe_photos`
--
ALTER TABLE `swipe_photos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `userinterest`
--
ALTER TABLE `userinterest`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_userInterest_1` (`interestId`),
  ADD KEY `fkUserInterestUsers` (`userId`);

--
-- Indexes for table `userlookingforid`
--
ALTER TABLE `userlookingforid`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkUserLookingFor` (`userId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_likes`
--
ALTER TABLE `user_likes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `albums`
--
ALTER TABLE `albums`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `photo`
--
ALTER TABLE `photo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `swipe_photos`
--
ALTER TABLE `swipe_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `userinterest`
--
ALTER TABLE `userinterest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `userlookingforid`
--
ALTER TABLE `userlookingforid`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_likes`
--
ALTER TABLE `user_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `albums`
--
ALTER TABLE `albums`
  ADD CONSTRAINT `fkOwenerId` FOREIGN KEY (`ownerId`) REFERENCES `users` (`id`);

--
-- Constraints for table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `fkParticipantsRoom` FOREIGN KEY (`roomId`) REFERENCES `room` (`id`),
  ADD CONSTRAINT `fkParticipantsUId` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

--
-- Constraints for table `photo`
--
ALTER TABLE `photo`
  ADD CONSTRAINT `fkAlbumPhoto` FOREIGN KEY (`albumId`) REFERENCES `albums` (`id`);

--
-- Constraints for table `userinterest`
--
ALTER TABLE `userinterest`
  ADD CONSTRAINT `fkUserInterestUsers` FOREIGN KEY (`userId`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_userInterest_1` FOREIGN KEY (`interestId`) REFERENCES `interests` (`id`);

--
-- Constraints for table `userlookingforid`
--
ALTER TABLE `userlookingforid`
  ADD CONSTRAINT `fkUserLookingFor` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
