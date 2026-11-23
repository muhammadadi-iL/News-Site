-- Pulse Markets — Crypto & Forex news CMS
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
(2, 'Market', 'Editor', 'editor', '5aee9dbd2a188839105073571bee1b1f', 0);

INSERT INTO `category` (`id`, `category_name`, `post`) VALUES
(1, 'Crypto', 2),
(2, 'Forex', 2),
(3, 'Bitcoin', 1),
(4, 'Ethereum', 1),
(5, 'Analysis', 1);

INSERT INTO `posts` (`id`, `post_title`, `post_keywords`, `post_content`, `post_date`, `post_author`, `post_image`) VALUES
(1, 'Bitcoin holds key support as ETF inflows steady', 'Spot bitcoin ETFs saw another week of net inflows while BTC defended the psychological support zone. Traders are watching funding rates and weekend liquidity for the next directional move across major crypto pairs.', 3, '18 Nov, 2026', 2, 'post-featured.jpg'),
(2, 'EUR/USD traders brace for central bank speeches', 'The euro hovered near session highs ahead of scheduled policy remarks. Forex desks flagged thinner liquidity into the New York open, with dollar strength still tied to rate-cut timing bets.', 2, '19 Nov, 2026', 1, 'post-business.jpg'),
(3, 'Ethereum network activity climbs on L2 growth', 'Layer-2 throughput and fee compression continue to support ETH narrative into the quarter. Analysts note rising stablecoin settlement and DeFi TVL as constructive signals for medium-term holders.', 4, '20 Nov, 2026', 2, 'post-tech.jpg'),
(4, 'USD/JPY volatility spikes after rate speculation', 'Yen pairs swung as markets priced shifting odds for policy divergence. Risk desks tightened stops around Tokyo fix levels while options skew pointed to two-way risk into the week.', 2, '21 Nov, 2026', 1, 'post-politics.jpg'),
(5, 'Altcoin liquidity thins as majors dominate volume', 'Outside BTC and ETH, order books looked thinner on mid-cap tokens. Market makers warned of wider spreads during off-peak hours as speculative flow clustered around high-cap crypto.', 1, '21 Nov, 2026', 2, 'post-entertainment.jpg'),
(6, 'Weekly FX outlook: dollar index and risk appetite', 'A compact guide to the week ahead covering DXY levels, commodity currencies, and event risk. Desk strategists highlight data releases most likely to move G10 pairs.', 5, '22 Nov, 2026', 1, 'post-sports.jpg'),
(7, 'Crypto market wrap: funding, open interest, flows', 'Open interest cooled after a busy session while perpetual funding normalized. On-chain dashboards showed exchange reserves edging lower — a setup traders often watch before trend continuation.', 1, '23 Nov, 2026', 2, 'post-tech-2.jpg');

COMMIT;
