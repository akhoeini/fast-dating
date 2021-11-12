-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 11, 2021 at 07:40 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.0.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dating`
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

CREATE TABLE `user_likes` (
  `id` int(11) NOT NULL primary key auto_increment,
  `user_id` int(11) NOT NULL,
  `liked_user_id` int(11) NOT NULL,
  `operation` enum('like','pass') NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
);

CREATE TABLE swipe_photos (
  id int(11) not null primary key auto_increment,
  user_id int(11) NOT NULL,  
  image_name varchar(200) NOT NULL
);

CREATE TABLE `matches` (
  `id` int(11) NOT NULL primary key auto_increment,
  `user_id` int(11) NOT NULL,
  `matched_user_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT current_timestamp()
);

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `roomId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `message` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
(1, 'pexels-brandan-saviour-2741701\r\n', 'test image', 1, 'img/pexels-brandan-saviour-2741701.jpg');

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
-- Table structure for table `userInterest`
--

CREATE TABLE `userInterest` (
  `id` int(11) NOT NULL,
  `userId` int(11) DEFAULT NULL,
  `interestId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userInterest`
--

INSERT INTO `userInterest` (`id`, `userId`, `interestId`) VALUES
(1, 1, 2),
(2, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `userLookingForId`
--

CREATE TABLE `userLookingForId` (
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
('test2093', 'test@gmail.com', 1, 'user', '1994-11-13 00:00:00', 1, 'fiawdifhu aiuwhfoia aweihufwoi aiwuh iaweiofh iahwefiuewf iiwhefi u', 'muffin', 2, 2, NULL),
('signuptest', 'signup@signup.com', 2, 'user', NULL, NULL, NULL, 'Min123456', NULL, NULL, NULL),
('tokentest', 'signup', 3, 'user', NULL, NULL, NULL, 'Min123456', NULL, NULL, NULL),
('tokentest2', 'signup@token.com', 4, 'user', NULL, NULL, NULL, 'Min123456', NULL, NULL, NULL),
('tokentest3', 'signup@token3.com', 5, 'user', NULL, NULL, NULL, 'Min123456', NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJmYXN0ZGF0aW5nLm9yZyIsImF1ZCI6ImZhc3RkYXRpbmcuY2EiLCJpYXQiOjUsIm5iZiI6InRva2VudGVzdDMiLCJleGQiOiIyMDIxLTEyLTExIn0.BpZRis7toDKdXPlkMv1LBk1tt0GHwu65ANyp5sA7XkYgnZqSb-c4OJwUaAAUPsNsXm8QNzIUx5s8xbD5YA7_-3DgRBGR4eX3MCBYsd7OsXBK1CRN_bbtczE9GHAK5Hi8l2xRQW1jkgZJtrsLYJJ4kp3elyPEsjvUnpzXmlzY4tE'),
('testtoken 6', 'testtoken6@test.com', 6, 'user', NULL, NULL, NULL, 'Min123456', NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJmYXN0ZGF0aW5nLm9yZyIsImF1ZCI6ImZhc3RkYXRpbmcuY2EiLCJpYXQiOjYsIm5iZiI6InRlc3R0b2tlbiA2IiwiZXhkIjoiMjAyMS0xMi0xMSJ9.CaNzz950_3BHdl9SQes4I79O06HIrrivn-i_ET8paUA--WZQHucJMnlGN0DXZQABZea05skMKBCnhnvTXjor84Y7uA6Tuu_0J642E-4P0b0GTh8mJJAjA0p39Upcd3oMSv37oGNjtS3m9sZPHGvyndVnku9gJjkQbPEcEkCQt7M'),
('testtoken7', 'testtoken7@test.com', 7, 'user', NULL, NULL, NULL, 'Min123456', NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJmYXN0ZGF0aW5nLm9yZyIsImF1ZCI6ImZhc3RkYXRpbmcuY2EiLCJpYXQiOjcsIm5iZiI6InRlc3R0b2tlbjciLCJleGQiOiIyMDIxLTEyLTExIn0.GiJlib1pc5IKwy1yLEJQOQPmrcASBs9_Bg-e_clgbN5yOoPXpN8pLjjch40LTLXo0a7pweAL7F-8RpjyXROuc-RhhJWpXQ9F__w5AyXehggaWdr5K3uzppzQ1rfwQx-k-WO7FoTuAhdEHIG4NixeK4ZQ_f0C6HGy1NThoMR5iW4'),
('token10', 'token10@token10.com', 8, 'user', NULL, NULL, NULL, 'token10', NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJmYXN0ZGF0aW5nLm9yZyIsImF1ZCI6ImZhc3RkYXRpbmcuY2EiLCJpYXQiOjgsIm5iZiI6InRva2VuMTAiLCJleGQiOiIyMDIxLTEyLTExIn0.EcJEXuP8VaDXH9F4DAg3wfgi9-XmW53OT7nT2sMQV1fMcV5cvCWNrV7Tcx-x8Y3Tdenub9TtuhPxaRVBEgH1tuphkm-g0PHgObqteULaaWR1KL1BKFbJ9gq-hISX7gLDMZnjRGy4ID1t4GbF3Y0rINzgFf710Ma_Oehs1pz6vLg'),
('token12', 'token12@token12.com', 9, 'user', NULL, NULL, NULL, 'token12@A', NULL, NULL, 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJmYXN0ZGF0aW5nLm9yZyIsImF1ZCI6ImZhc3RkYXRpbmcuY2EiLCJpYXQiOjksIm5iZiI6InRva2VuMTIiLCJleGQiOiIyMDIxLTEyLTExIn0.XflyD9FZ5DBBJ0bBwVO0fxv1daDeEbrFSq-w4FAuHwwBndgSfx10KX3hlNUJdDQSqDhlFppyf_4HbRA8rG9z9qR0zU7F3vSbrSFSjkpSEj4qK9FyOO9Q86gBaT24JZRlNUM4UY3Lt8InzxU8xJWEuy9ZIS2tv1mIhjUYpsxgEXg');

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
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkMessageUId` (`userId`),
  ADD KEY `fkMessageRoom` (`roomId`);

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
-- Indexes for table `userInterest`
--
ALTER TABLE `userInterest`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_userInterest_1` (`interestId`),
  ADD KEY `fkUserInterestUsers` (`userId`);

--
-- Indexes for table `userLookingForId`
--
ALTER TABLE `userLookingForId`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fkUserLookingFor` (`userId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
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
--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `photo`
--
ALTER TABLE `photo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userInterest`
--
ALTER TABLE `userInterest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `userLookingForId`
--
ALTER TABLE `userLookingForId`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `albums`
--
ALTER TABLE `albums`
  ADD CONSTRAINT `fkOwenerId` FOREIGN KEY (`ownerId`) REFERENCES `users` (`id`);

--
--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `fkMessageRoom` FOREIGN KEY (`roomId`) REFERENCES `room` (`id`),
  ADD CONSTRAINT `fkMessageUId` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

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
-- Constraints for table `userInterest`
--
ALTER TABLE `userInterest`
  ADD CONSTRAINT `fkUserInterestUsers` FOREIGN KEY (`userId`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_userInterest_1` FOREIGN KEY (`interestId`) REFERENCES `interests` (`id`);

--
-- Constraints for table `userLookingForId`
--
ALTER TABLE `userLookingForId`
  ADD CONSTRAINT `fkUserLookingFor` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
