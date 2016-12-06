--
-- Tabellenstruktur für Tabelle `tree_test`
--

CREATE TABLE `tree_test` (
  `id` int(11) NOT NULL,
  `parentId` int(11) DEFAULT '0',
  `name` varchar(30) NOT NULL,
  `position` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `tree_test`
--

INSERT INTO `tree_test` (`id`, `parentId`, `name`, `position`, `type`) VALUES
(1, NULL, 'Root Item Test', NULL, NULL),
(2, 1, 'Test', NULL, NULL),
(3, 1, 'Test New', NULL, NULL),
(4, 1, 'A Test Item', 2, NULL),
(5, 4, 'Another Sub Test Item', 0, NULL);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `tree_test`
--
ALTER TABLE `tree_test`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `tree_test`
--
ALTER TABLE `tree_test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;