-- News Site CMS — schema + sample content with real images
SET NAMES utf8mb4;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `news_site` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `news_site`;

DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `category`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `password` varchar(40) DEFAULT NULL,
  `role` int(11) NOT NULL COMMENT '1=Admin, 0=Normal',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  `post` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- post_content stores category id (legacy field name)
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_title` varchar(200) NOT NULL,
  `post_keywords` text NOT NULL,
  `post_content` int(11) NOT NULL,
  `post_date` varchar(50) NOT NULL,
  `post_author` int(11) NOT NULL,
  `post_image` varchar(255) NOT NULL DEFAULT 'post-format.jpg',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`, `first_name`, `last_name`, `username`, `password`, `role`) VALUES
(1, 'Muhammad', 'Adil', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1),
(2, 'News', 'Editor', 'editor', '5aee9dbd2a188839105073571bee1b1f', 0);

INSERT INTO `category` (`id`, `category_name`, `post`) VALUES
(1, 'Politics', 2),
(2, 'Sports', 2),
(3, 'Business', 1),
(4, 'Entertainment', 1),
(5, 'Technology', 1);

INSERT INTO `posts` (`id`, `post_title`, `post_keywords`, `post_content`, `post_date`, `post_author`, `post_image`) VALUES
(1, 'City council passes new transit plan', 'Local leaders approved a multi-year transit expansion that will add new bus routes and upgrade existing stations across the metro area. Residents can expect phased construction starting next spring, with priority corridors opening first.', 1, '15 Nov, 2026', 1, 'post-politics.jpg'),
(2, 'National team clinches tournament berth', 'A late equalizer and a dramatic penalty shootout sent the national side into the continental finals. Fans gathered downtown to celebrate the hard-fought victory as coaches praised the squad resilience.', 2, '16 Nov, 2026', 1, 'post-sports.jpg'),
(3, 'Markets steady after policy update', 'Equity markets closed mixed after the central bank held rates steady. Analysts say consumer spending and jobs data remain the key watchpoints for investors this quarter.', 3, '18 Nov, 2026', 2, 'post-business.jpg'),
(4, 'Streaming hit renews for another season', 'The breakout drama series has been renewed after strong viewership numbers. Cast and creators confirmed production will resume early next year with new story arcs.', 4, '19 Nov, 2026', 1, 'post-entertainment.jpg'),
(5, 'Startups race to ship AI tools for newsrooms', 'Newsrooms are testing assistants that help with research, summaries, and metadata. Editors stress that human review stays essential for accuracy and public trust.', 5, '20 Nov, 2026', 2, 'post-tech.jpg'),
(6, 'Weekend league finals draw record crowd', 'Stadium attendance hit a new high as two undefeated clubs met in the championship. Highlights and player interviews dominate sports coverage this week.', 2, '21 Nov, 2026', 1, 'post-sports-2.jpg'),
(7, 'Election commission confirms poll timetable', 'The commission published the official calendar for voter registration, candidate filing, and election day. Civic groups welcomed the early clarity for campaign planning.', 1, '22 Nov, 2026', 2, 'post-featured.jpg');

COMMIT;
