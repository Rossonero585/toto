
--
-- База данных: `toto_4479270`
--
CREATE DATABASE IF NOT EXISTS `%toto_db_name%` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `%toto_db_name%`;

-- --------------------------------------------------------

--
-- Структура таблицы `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `p1` float(7,5) DEFAULT NULL,
  `px` float(7,5) DEFAULT NULL,
  `p2` float(7,5) DEFAULT NULL,
  `s1` float(7,5) DEFAULT NULL,
  `sx` float(7,5) DEFAULT NULL,
  `s2` float(7,5) DEFAULT NULL,
  `league` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `pool`
--

DROP TABLE IF EXISTS `pool`;
CREATE TABLE `pool` (
  `r1` enum('X','1','2') DEFAULT NULL,
  `r2` enum('X','1','2') NOT NULL,
  `r3` enum('X','1','2') NOT NULL,
  `r4` enum('X','1','2') NOT NULL,
  `r5` enum('X','1','2') NOT NULL,
  `r6` enum('X','1','2') NOT NULL,
  `r7` enum('X','1','2') NOT NULL,
  `r8` enum('X','1','2') NOT NULL,
  `r9` enum('X','1','2') NOT NULL,
  `r10` enum('X','1','2') NOT NULL,
  `r11` enum('X','1','2') NOT NULL,
  `r12` enum('X','1','2') NOT NULL,
  `r13` enum('X','1','2') NOT NULL,
  `r14` enum('X','1','2') NOT NULL,
  `money` decimal(8,2) DEFAULT NULL,
  `code` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `toto`
--

DROP TABLE IF EXISTS `toto`;
CREATE TABLE `toto` (
  `id` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `pot` float(15,2) DEFAULT NULL,
  `jackpot` float(15,2) DEFAULT NULL,
  `event_count` int(11) DEFAULT NULL,
  `winner_counts` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



--
-- Структура таблицы `bet_items`
--

DROP TABLE IF EXISTS `bet_items`;
CREATE TABLE `bet_items` (
  `bet_id` int(11) NOT NULL,
  `bet` varchar(20) DEFAULT NULL,
  `money` float(7,5) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Структура таблицы `bets`
--

DROP TABLE IF EXISTS `bets`;
CREATE TABLE `bets` (
  `id` int(11) NOT NULL,
  `money` decimal(8,2) DEFAULT NULL,
  `probability` float(7,5) DEFAULT NULL,
  `expected_ev` float(7,5) DEFAULT NULL,
  `income` float(7,5) DEFAULT NULL,
  `bet_time` datetime DEFAULT NULL,
  `last_bet_ev` float(7,5) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `pool`
--
ALTER TABLE `pool`
  ADD UNIQUE KEY `code` (`code`);

--
-- Индексы таблицы `toto`
--
ALTER TABLE `toto`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT для таблицы `toto`
--
ALTER TABLE `toto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
