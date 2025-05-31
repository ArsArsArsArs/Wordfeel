-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 30, 2025 at 03:07 PM
-- Server version: 8.0.41-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Wordfeel`
--

-- --------------------------------------------------------

--
-- Table structure for table `Languages`
--

CREATE TABLE `Languages` (
  `LanguageCode` varchar(2) NOT NULL,
  `LanguageName` varchar(50) NOT NULL,
  `CountryCode` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Languages`
--

INSERT INTO `Languages` (`LanguageCode`, `LanguageName`, `CountryCode`) VALUES
('aa', 'Афар', 'dj'),
('ab', 'Абхазский', 'ge'),
('af', 'Африкаанс', 'za'),
('ak', 'Акан', 'gh'),
('am', 'Амхарский', 'et'),
('an', 'Арагонский', 'es'),
('ar', 'Арабский', 'sa'),
('as', 'Ассамский', 'in'),
('av', 'Аварский', 'ru'),
('ay', 'Аймара', 'bo'),
('az', 'Азербайджанский', 'az'),
('ba', 'Башкирский', 'ru'),
('be', 'Белорусский', 'by'),
('bg', 'Болгарский', 'bg'),
('bh', 'Бихари', 'in'),
('bi', 'Бислама', 'vu'),
('bm', 'Бамбарский', 'ml'),
('bn', 'Бенгальский', 'bd'),
('bo', 'Тибетский', 'cn'),
('br', 'Бретонский', 'fr'),
('bs', 'Боснийский', 'ba'),
('ca', 'Каталанский', 'es'),
('ce', 'Чеченский', 'ru'),
('ch', 'Чаморро', 'gu'),
('co', 'Корсиканский', 'fr'),
('cr', 'Кри', 'ca'),
('cs', 'Чешский', 'cz'),
('cv', 'Чувашский', 'ru'),
('cy', 'Валлийский', 'gb'),
('da', 'Датский', 'dk'),
('de', 'Немецкий', 'de'),
('dv', 'Дивехи', 'mv'),
('dz', 'Дзонг-кэ', 'bt'),
('ee', 'Эве', 'gh'),
('el', 'Греческий', 'gr'),
('en', 'Английский', 'us'),
('es', 'Испанский', 'es'),
('et', 'Эстонский', 'ee'),
('eu', 'Баскский', 'es'),
('fa', 'Персидский', 'ir'),
('ff', 'Фулах', 'sn'),
('fi', 'Финский', 'fi'),
('fj', 'Фиджийский', 'fj'),
('fo', 'Фарерский', 'fo'),
('fr', 'Французский', 'fr'),
('fy', 'Западнофризский', 'nl'),
('ga', 'Ирландский', 'ie'),
('gd', 'Шотландский гэльский', 'gb'),
('gl', 'Галисийский', 'es'),
('gn', 'Гуарани', 'py'),
('gu', 'Гуджарати', 'in'),
('gv', 'Мэнский', 'im'),
('ha', 'Хауса', 'ng'),
('he', 'Иврит', 'il'),
('hi', 'Хинди', 'in'),
('ho', 'Хири моту', 'pg'),
('hr', 'Хорватский', 'hr'),
('ht', 'Гаитянский креольский', 'ht'),
('hu', 'Венгерский', 'hu'),
('hy', 'Армянский', 'am'),
('hz', 'Гереро', 'na'),
('id', 'Индонезийский', 'id'),
('ig', 'Игбо', 'ng'),
('ii', 'Сычуаньский Yi', 'cn'),
('ik', 'Инуйпик', 'us'),
('is', 'Исландский', 'is'),
('it', 'Итальянский', 'it'),
('iu', 'Инуктитут', 'ca'),
('ja', 'Японский', 'jp'),
('jv', 'Яванский', 'id'),
('ka', 'Грузинский', 'ge'),
('kg', 'Конго', 'cd'),
('ki', 'Кикуйю', 'ke'),
('kj', 'Кваньяма', 'ao'),
('kk', 'Казахский', 'kz'),
('kl', 'Гренландский', 'gl'),
('km', 'Кхмерский', 'kh'),
('kn', 'Каннада', 'in'),
('ko', 'Корейский', 'kr'),
('kr', 'Канури', 'ng'),
('ks', 'Кашмири', 'in'),
('ku', 'Курдский', 'iq'),
('kv', 'Коми', 'ru'),
('kw', 'Корнский', 'gb'),
('ky', 'Киргизский', 'kg'),
('la', 'Латинский', 'va'),
('lb', 'Люксембургский', 'lu'),
('lg', 'Ганда', 'ug'),
('li', 'Лимбургский', 'nl'),
('ln', 'Лингала', 'cd'),
('lo', 'Лаосский', 'la'),
('lt', 'Литовский', 'lt'),
('lu', 'Луба-катанга', 'cd'),
('lv', 'Латвийский', 'lv'),
('mg', 'Малагасийский', 'mg'),
('mh', 'Маршалльский', 'mh'),
('mi', 'Маори', 'nz'),
('mk', 'Македонский', 'mk'),
('ml', 'Малаялам', 'in'),
('mn', 'Монгольский', 'mn'),
('mr', 'Маратхи', 'in'),
('ms', 'Малайский', 'my'),
('mt', 'Мальтийский', 'mt'),
('my', 'Бирманский', 'mm'),
('na', 'Науру', 'nr'),
('nb', 'Норвежский букмол', 'no'),
('nd', 'Северный ндебеле', 'zw'),
('ne', 'Непальский', 'np'),
('ng', 'Ндонга', 'na'),
('nl', 'Нидерландский', 'nl'),
('nn', 'Норвежский нюнорск', 'no'),
('no', 'Норвежский', 'no'),
('nr', 'Южный ндебеле', 'za'),
('nv', 'Навахо', 'us'),
('ny', 'Чичева', 'mw'),
('oc', 'Окситанский', 'fr'),
('oj', 'Оджибве', 'ca'),
('om', 'Оромо', 'et'),
('or', 'Ория', 'in'),
('os', 'Осетинский', 'ru'),
('pa', 'Панджаби', 'in'),
('pi', 'Пали', 'in'),
('pl', 'Польский', 'pl'),
('ps', 'Пушту', 'af'),
('pt', 'Португальский', 'pt'),
('qu', 'Кечуа', 'pe'),
('rm', 'Ретороманский', 'ch'),
('rn', 'Кирунди', 'bi'),
('ro', 'Румынский', 'ro'),
('ru', 'Русский', 'ru'),
('rw', 'Киньяруанда', 'rw'),
('sa', 'Санскрит', 'in'),
('sc', 'Сардинский', 'it'),
('sd', 'Синдхи', 'pk'),
('se', 'Северносаамский', 'no'),
('sg', 'Санго', 'cf'),
('si', 'Сингальский', 'lk'),
('sk', 'Словацкий', 'sk'),
('sl', 'Словенский', 'si'),
('sm', 'Самоанский', 'ws'),
('sn', 'Шона', 'zw'),
('so', 'Сомали', 'so'),
('sq', 'Албанский', 'al'),
('sr', 'Сербский', 'rs'),
('ss', 'Свати', 'sz'),
('st', 'Южный сото', 'ls'),
('su', 'Сунданский', 'id'),
('sv', 'Шведский', 'se'),
('sw', 'Суахили', 'tz'),
('ta', 'Тамильский', 'in'),
('te', 'Телугу', 'in'),
('tg', 'Таджикский', 'tj'),
('th', 'Тайский', 'th'),
('ti', 'Тигринья', 'er'),
('tk', 'Туркменский', 'tm'),
('tl', 'Тагальский', 'ph'),
('tn', 'Тсвана', 'bw'),
('to', 'Тонга', 'to'),
('tr', 'Турецкий', 'tr'),
('ts', 'Тсонга', 'za'),
('tt', 'Татарский', 'ru'),
('tw', 'Тви', 'gh'),
('ty', 'Таитянский', 'pf'),
('ug', 'Уйгурский', 'cn'),
('uk', 'Украинский', 'ua'),
('ur', 'Урду', 'pk'),
('uz', 'Узбекский', 'uz'),
('ve', 'Венда', 'za'),
('vi', 'Вьетнамский', 'vn'),
('wa', 'Валлонский', 'be'),
('wo', 'Волоф', 'sn'),
('xh', 'Коса', 'za'),
('yo', 'Йоруба', 'ng'),
('za', 'Чжуан', 'cn'),
('zh', 'Китайский', 'cn'),
('zu', 'Зулу', 'za');

-- --------------------------------------------------------

--
-- Table structure for table `PackageWords`
--

CREATE TABLE `PackageWords` (
  `LanguageCode` varchar(2) DEFAULT NULL,
  `Word` varchar(100) DEFAULT NULL,
  `Translation` varchar(100) DEFAULT NULL,
  `Transcription` varchar(250) DEFAULT NULL,
  `Description` text,
  `WordPackageID` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tags`
--

CREATE TABLE `Tags` (
  `TagID` int NOT NULL,
  `UserID` int DEFAULT NULL,
  `LanguageCode` varchar(2) DEFAULT NULL,
  `TagName` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tokens`
--

CREATE TABLE `Tokens` (
  `Token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `UserID` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `UserManaging`
--

CREATE TABLE `UserManaging` (
  `UserID` int DEFAULT NULL,
  `ManagedUserID` int DEFAULT NULL,
  `LinkKey` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `UserID` int NOT NULL,
  `Username` varchar(22) NOT NULL,
  `Password` varchar(60) NOT NULL,
  `CreatedAt` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `UserStats`
--

CREATE TABLE `UserStats` (
  `UserID` int DEFAULT NULL,
  `WordsDone` int DEFAULT NULL,
  `PercentGained` int DEFAULT NULL,
  `Date` datetime DEFAULT NULL,
  `LanguageCode` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `UserWords`
--

CREATE TABLE `UserWords` (
  `WordID` int NOT NULL,
  `UserID` int DEFAULT NULL,
  `LanguageCode` varchar(2) DEFAULT NULL,
  `Word` varchar(100) DEFAULT NULL,
  `Translation` varchar(100) DEFAULT NULL,
  `Transcription` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Description` text,
  `ImageURL` varchar(200) DEFAULT NULL,
  `LastReviewed` datetime DEFAULT NULL,
  `MemorizationPercent` int DEFAULT NULL,
  `CreatedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Triggers `UserWords`
--
DELIMITER $$
CREATE TRIGGER `CheckPercentInsert` BEFORE INSERT ON `UserWords` FOR EACH ROW
BEGIN
    IF new.MemorizationPercent < 0 THEN
        SET new.MemorizationPercent = 0;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `CheckPercentUpdate` BEFORE UPDATE ON `UserWords` FOR EACH ROW
BEGIN
    IF new.MemorizationPercent < 0 THEN
        SET new.MemorizationPercent = 0;
    END IF;
END$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `UpdateToUTC` BEFORE INSERT ON `UserWords` FOR EACH ROW
BEGIN
    SET new.CreatedAt = UTC_TIMESTAMP();
END$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `WordPackages`
--

CREATE TABLE `WordPackages` (
  `WordPackageID` int NOT NULL,
  `Name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `WordTags`
--

CREATE TABLE `WordTags` (
  `WordID` int NOT NULL,
  `TagID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Languages`
--
ALTER TABLE `Languages`
  ADD PRIMARY KEY (`LanguageCode`);

--
-- Indexes for table `PackageWords`
--
ALTER TABLE `PackageWords`
  ADD KEY `LanguageCode` (`LanguageCode`),
  ADD KEY `fk_wordpackage` (`WordPackageID`);

--
-- Indexes for table `Tags`
--
ALTER TABLE `Tags`
  ADD PRIMARY KEY (`TagID`),
  ADD UNIQUE KEY `UniqueTagsForUsers` (`UserID`,`TagName`),
  ADD KEY `LanguageCode` (`LanguageCode`);

--
-- Indexes for table `Tokens`
--
ALTER TABLE `Tokens`
  ADD PRIMARY KEY (`Token`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `UserManaging`
--
ALTER TABLE `UserManaging`
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ManagedUserID` (`ManagedUserID`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `UserStats`
--
ALTER TABLE `UserStats`
  ADD UNIQUE KEY `UniqueDatesForUsers` (`UserID`,`Date`),
  ADD KEY `LanguageCode` (`LanguageCode`);

--
-- Indexes for table `UserWords`
--
ALTER TABLE `UserWords`
  ADD PRIMARY KEY (`WordID`),
  ADD UNIQUE KEY `unique_word_language_user` (`Word`,`LanguageCode`,`UserID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `LanguageCode` (`LanguageCode`);

--
-- Indexes for table `WordPackages`
--
ALTER TABLE `WordPackages`
  ADD PRIMARY KEY (`WordPackageID`);

--
-- Indexes for table `WordTags`
--
ALTER TABLE `WordTags`
  ADD UNIQUE KEY `UniqueWordTag` (`WordID`,`TagID`),
  ADD KEY `TagID` (`TagID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Tags`
--
ALTER TABLE `Tags`
  MODIFY `TagID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `UserID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `UserWords`
--
ALTER TABLE `UserWords`
  MODIFY `WordID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `WordPackages`
--
ALTER TABLE `WordPackages`
  MODIFY `WordPackageID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `PackageWords`
--
ALTER TABLE `PackageWords`
  ADD CONSTRAINT `fk_wordpackage` FOREIGN KEY (`WordPackageID`) REFERENCES `WordPackages` (`WordPackageID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `PackageWords_ibfk_1` FOREIGN KEY (`LanguageCode`) REFERENCES `Languages` (`LanguageCode`);

--
-- Constraints for table `Tags`
--
ALTER TABLE `Tags`
  ADD CONSTRAINT `Tags_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  ADD CONSTRAINT `Tags_ibfk_2` FOREIGN KEY (`LanguageCode`) REFERENCES `Languages` (`LanguageCode`);

--
-- Constraints for table `Tokens`
--
ALTER TABLE `Tokens`
  ADD CONSTRAINT `Tokens_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`);

--
-- Constraints for table `UserManaging`
--
ALTER TABLE `UserManaging`
  ADD CONSTRAINT `UserManaging_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  ADD CONSTRAINT `UserManaging_ibfk_2` FOREIGN KEY (`ManagedUserID`) REFERENCES `Users` (`UserID`);

--
-- Constraints for table `UserStats`
--
ALTER TABLE `UserStats`
  ADD CONSTRAINT `UserStats_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  ADD CONSTRAINT `UserStats_ibfk_2` FOREIGN KEY (`LanguageCode`) REFERENCES `Languages` (`LanguageCode`);

--
-- Constraints for table `UserWords`
--
ALTER TABLE `UserWords`
  ADD CONSTRAINT `UserWords_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`),
  ADD CONSTRAINT `UserWords_ibfk_2` FOREIGN KEY (`LanguageCode`) REFERENCES `Languages` (`LanguageCode`);

--
-- Constraints for table `WordTags`
--
ALTER TABLE `WordTags`
  ADD CONSTRAINT `WordTags_ibfk_1` FOREIGN KEY (`WordID`) REFERENCES `UserWords` (`WordID`) ON DELETE CASCADE,
  ADD CONSTRAINT `WordTags_ibfk_2` FOREIGN KEY (`TagID`) REFERENCES `Tags` (`TagID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
