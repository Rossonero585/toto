
--
-- База данных: `toto`
--
CREATE DATABASE IF NOT EXISTS `toto` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `toto`;

-- --------------------------------------------------------

--
-- Структура таблицы `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `p1` float(7,5) DEFAULT NULL,
  `px` float(7,5) DEFAULT NULL,
  `p2` float(7,5) DEFAULT NULL,
  `s1` float(7,5) DEFAULT NULL,
  `sx` float(7,5) DEFAULT NULL,
  `s2` float(7,5) DEFAULT NULL,
  `league` varchar(255) DEFAULT NULL,
  `result` VARCHAR(1) DEFAULT NULL,
  `toto_id` varchar(255) NOT NULL,
  PRIMARY KEY (id, toto_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `pool`
--

CREATE TABLE IF NOT EXISTS `pool` (
  `result` varchar(20) NOT NULL,
  `money` decimal(8,2) DEFAULT NULL,
  `code` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `toto_id` varchar(255) NOT NULL,
  PRIMARY KEY (code, toto_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `toto`
--

CREATE TABLE IF NOT EXISTS `toto` (
  `id` varchar(255) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `pot` float(15,2) DEFAULT NULL,
  `jackpot` float(15,2) DEFAULT NULL,
  `event_count` int(11) DEFAULT NULL,
  `winner_counts` varchar(255) DEFAULT NULL,
  `pool_deviation` float(15,14) DEFAULT NULL,
  `bookmaker` varchar(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Структура таблицы `bet_items`
--

CREATE TABLE IF NOT EXISTS `bet_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bet_id` int(11) NOT NULL,
  `bet` varchar(20) DEFAULT NULL,
  `money` decimal(8,2) DEFAULT NULL,
  `ev` float(10,2) DEFAULT NULL,
  `probability` float(7,5) DEFAULT NULL,
  `count_match` TINYINT(1) DEFAULT NULL,
  `income` float(15,2) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Структура таблицы `bets`
--

CREATE TABLE IF NOT EXISTS `bets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `money` decimal(8,2) DEFAULT NULL,
  `probability` float(7,5) DEFAULT NULL,
  `ev` float(10,2) DEFAULT NULL,
  `income` float(15,2) DEFAULT NULL,
  `bet_time` datetime DEFAULT NULL,
  `last_bet_ev` float(10,2) DEFAULT NULL,
  `toto_id` varchar(255) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
