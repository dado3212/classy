-- Create medians table
CREATE TABLE `medians` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department` varchar(50) NOT NULL,
  `number` varchar(50) NOT NULL,
  `median` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create ORC table
CREATE TABLE `orc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department` varchar(50) NOT NULL,
  `number` varchar(50) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `teachers` varchar(150) NOT NULL,
  `prereqs` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create timetable table
CREATE TABLE `timetable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department` varchar(50) DEFAULT NULL,
  `number` varchar(50) DEFAULT NULL,
  `fys` varchar(50) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `description` text CHARACTER SET latin1,
  `crosslisted` varchar(150) DEFAULT NULL,
  `period` varchar(150) DEFAULT NULL,
  `room` varchar(150) DEFAULT NULL,
  `building` varchar(150) DEFAULT NULL,
  `teacher` varchar(150) DEFAULT NULL,
  `culture` varchar(150) DEFAULT NULL,
  `distrib` text NOT NULL,
  `limit` varchar(50) DEFAULT NULL,
  `term` varchar(50) DEFAULT 'W2016',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
