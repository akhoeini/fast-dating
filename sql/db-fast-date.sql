ALTER TABLE `room` DROP FOREIGN KEY `fk_room`;
ALTER TABLE `participants` DROP FOREIGN KEY `fk_participants`;
ALTER TABLE `participants` DROP FOREIGN KEY `fk_participants_1`;
ALTER TABLE `message` DROP FOREIGN KEY `fk_message`;
ALTER TABLE `message` DROP FOREIGN KEY `fk_message_1`;
ALTER TABLE `photo` DROP FOREIGN KEY `fk_photo`;
ALTER TABLE `albums` DROP FOREIGN KEY `fk_albums_1`;
ALTER TABLE `users` DROP FOREIGN KEY `fk_users_1`;
ALTER TABLE `matches` DROP FOREIGN KEY `fk_matches`;
ALTER TABLE `matches` DROP FOREIGN KEY `fk_matches_1`;
ALTER TABLE `userInterest` DROP FOREIGN KEY `fk_userInterest`;
ALTER TABLE `userInterest` DROP FOREIGN KEY `fk_userInterest_1`;
ALTER TABLE `userLookingForId` DROP FOREIGN KEY `fk_userLookingForId`;
ALTER TABLE `userLookingForId` DROP FOREIGN KEY `fk_userLookingForId_1`;

DROP INDEX `` ON `users`;

DROP TABLE `users`;
DROP TABLE `albums`;
DROP TABLE `photo`;
DROP TABLE `message`;
DROP TABLE `room`;
DROP TABLE `participants`;
DROP TABLE `roomType`;
DROP TABLE `genders`;
DROP TABLE `matches`;
DROP TABLE `interests`;
DROP TABLE `userInterest`;
DROP TABLE `userLookingForId`;

CREATE TABLE `users` (
`userName` varchar(255) NOT NULL,
`email` varchar(255) NOT NULL,
`id` int(11) NOT NULL,
`role` varchar(255) NOT NULL,
`birthDate` datetime NOT NULL,
`genderId` int(11) NOT NULL,
`bio` varchar(500) NULL,
`password` varchar(255) NOT NULL,
`userInterestsId` int(11) NULL,
`userLookingForId` int(11) NULL,
PRIMARY KEY (`userName`) ,
INDEX ()
);
CREATE TABLE `albums` (
`id` int(11) NOT NULL,
`name` varchar(255) NULL,
`description` varchar(255) NULL,
`category` varchar(255) NULL,
`ownerId` int(11) NULL,
PRIMARY KEY (`id`) 
);
CREATE TABLE `photo` (
`id` int(11) NOT NULL,
`name` varchar(255) NOT NULL,
`description` varchar(1000) NULL,
`albumId` int(11) NOT NULL,
`fileId` int(11) NULL,
`url` varchar(255) NULL,
PRIMARY KEY (`id`) 
);
CREATE TABLE `message` (
`id` int(11) NOT NULL,
`roomId` int(11) NULL,
`userId` int(11) NULL,
`message` varchar(1000) NULL,
PRIMARY KEY (`id`) 
);
CREATE TABLE `room` (
`id` int(11) NOT NULL,
`name` varchar(255) NULL,
`roomType` int(11) NULL,
PRIMARY KEY (`id`) 
);
CREATE TABLE `participants` (
`id` int(11) NOT NULL,
`userId` int(11) NULL,
`roomId` int(11) NULL,
PRIMARY KEY (`id`) 
);
CREATE TABLE `roomType` (
`id` int(11) NOT NULL,
`name` varchar(255) NULL,
PRIMARY KEY (`id`) 
);
CREATE TABLE `genders` (
`id` int(11) NOT NULL,
`name` varchar(255) NULL,
PRIMARY KEY (`id`) 
);
CREATE TABLE `matches` (
`uId1` int(11) NOT NULL,
`uId2` int(11) NOT NULL,
`Id` int(11) NOT NULL,
`isActive` bit(1) NULL,
PRIMARY KEY (`Id`) 
);
CREATE TABLE `interests` (
`id` int(11) NOT NULL,
`name` varchar(255) NULL,
`description` varchar(500) NULL,
`type` varchar(255) NULL,
PRIMARY KEY (`id`) 
);
CREATE TABLE `userInterest` (
`id` int(11) NOT NULL,
`userId` int(11) NULL,
`interestId` int(11) NULL,
PRIMARY KEY (`id`) 
);
CREATE TABLE `userLookingForId` (
`id` int(11) NOT NULL,
`userId` int(11) NULL,
`genderId` int(11) NULL,
PRIMARY KEY (`id`) 
);

ALTER TABLE `room` ADD CONSTRAINT `fk_room` FOREIGN KEY (`roomType`) REFERENCES `roomType` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `participants` ADD CONSTRAINT `fk_participants` FOREIGN KEY (`roomId`) REFERENCES `room` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `participants` ADD CONSTRAINT `fk_participants_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `message` ADD CONSTRAINT `fk_message` FOREIGN KEY (`roomId`) REFERENCES `room` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `message` ADD CONSTRAINT `fk_message_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `photo` ADD CONSTRAINT `fk_photo` FOREIGN KEY (`albumId`) REFERENCES `albums` (`id`);
ALTER TABLE `albums` ADD CONSTRAINT `fk_albums_1` FOREIGN KEY (`ownerId`) REFERENCES `users` (`id`);
ALTER TABLE `users` ADD CONSTRAINT `fk_users_1` FOREIGN KEY (`genderId`) REFERENCES `genders` (`id`);
ALTER TABLE `matches` ADD CONSTRAINT `fk_matches` FOREIGN KEY (`uId1`) REFERENCES `users` (`id`);
ALTER TABLE `matches` ADD CONSTRAINT `fk_matches_1` FOREIGN KEY (`uId2`) REFERENCES `users` (`id`);
ALTER TABLE `userInterest` ADD CONSTRAINT `fk_userInterest` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);
ALTER TABLE `userInterest` ADD CONSTRAINT `fk_userInterest_1` FOREIGN KEY (`interestId`) REFERENCES `interests` (`id`);
ALTER TABLE `userLookingForId` ADD CONSTRAINT `fk_userLookingForId` FOREIGN KEY (`genderId`) REFERENCES `genders` (`id`);
ALTER TABLE `userLookingForId` ADD CONSTRAINT `fk_userLookingForId_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`);

