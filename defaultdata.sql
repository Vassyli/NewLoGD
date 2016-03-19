--
-- Daten für Tabelle `scenes`
--

INSERT INTO `scenes` (`id`, `title`, `body`) VALUES
(1, 'The Village Square', 'The square in the center of the village is THE meet&greet point. Local folk are wandering around, buying and selling groceries, fetching water from the well or just talking.'),
(2, 'Walter''s Weapon Workshop', 'Walter is a broad-shouldered, bigger-than-average dwarf. His well-tended, dark-brown beard is the only hair source on his head. He stands behind the counter and appears to pay little attention to you as you enter, but you know from from experience that he has his eye on every move you make.\r\n\r\nHe doesn''t quite fit the merchant archetype. Instead of expensive clothing, he wears leather armor. The axe on his back leave no question open - he knows have to fight and he surely knows how to deal with thieves.\r\n\r\nWalter finally nods to you. Within a heartbeat he''s scrutinized you and points with a movement of his head to a part of his workshop where he keeps weapons suited for your experience.'),
(3, 'Albert''s Armors', 'Albert is a stunning and tall elf. His long, blond hair has been tied together forming a plait that nearly touches the ground. He has no other facial hair besides his thin eyebrows. He greets you with a warm smile as you enter the shop directly besides the Weapon workshop.\r\n\r\nHis outfit shows the big wealth he must have collected over the years due to his monopoly on armors. Completely in white with golden ornaments, he looks just like you imagine someone from the elven aristocracy to look like. Despite his looks, his eyes are showing that he is quite capable of defending himself (and his wares).\r\n\r\nHe shows you with a inviting gesture to a wardrobe made from oak containing armor fitting for your experience and needs.'),
(4, 'The Forest', 'The forest is all around the village. It might have had a name once, but since it is everywhere, the name was dropped and forgotten eventually. Everyone calls it just "The Forest", since there is only one.\r\n\r\nThe paths that are leading in different directions are relatively safe. Monsters tend to attack people from time-to-time, but only if they are desperate or fleeing from something more dangerous.\r\n\r\nThe danger lies off the path, within the darker parts of the forest where the sun is mostly blocked by the dense canopy. Outlaws, dangerous monsters and terrific ghosts roam these eerie parts where only a few brave warriors mange to survive and return to the village, unharmed. Most of the people just find a meaningless death and arrive in the shades as immaterial entities.\r\n\r\nAre you sure you don''t want to return back to the cozy village?'),
(5, 'Ye Olde Bank', 'Ye Olde Bank is the oldest bank in the village. And the only one, since competitors tend to... disappear after a few accidents fail to convince them that everything should they as it is. Nonetheless, the building itself is impressive and a great indicator of the fortune stored inside the caverns.\r\n\r\nA big stairway carved from the most expensive marble lead to a pair of magic crystal doors that open automatically as one approaches them. The round hall itself is made from marble as well with a ceiling as high as ten men crowned by a dome made from unbreakable crystal glass. A second set of doors opposite of the entrance protects the vault from anyone to enter except the gnomes that bring the gold stock needed for the day-to-day business. The black meteorite steel which the door is made of shows that even thinking about to break in is futile.');

--
-- Daten für Tabelle `scene_actions`
--

INSERT INTO `scene_actions` (`id`, `scene`, `parent`, `title`, `sorting`, `target_scene`) VALUES
(1, 1, NULL, 'Market Street', 0, NULL),
(2, NULL, 1, 'Walter''s Weapon Workshop', 0, 2),
(3, 1, NULL, 'Village Gates', -10, NULL),
(4, NULL, 3, 'The Forest', 0, 4),
(5, NULL, 1, 'Albert''s Armors', 10, 3),
(6, NULL, 13, 'Sabrina''s Stables', 0, NULL),
(7, 2, NULL, 'Back to the village', 0, 1),
(8, 1, NULL, 'Blades Boulevard', -5, NULL),
(9, NULL, 8, 'Tamara''s Training Camp', 0, NULL),
(10, NULL, 8, 'The Lodge', 5, NULL),
(11, NULL, 1, 'Ye Olde Bank', 20, 5),
(12, NULL, 1, 'Ze Gypsy Tent', 30, NULL),
(13, 1, NULL, 'Tavern Street', 5, NULL),
(14, NULL, 13, 'The Golden Stir Inn', -10, NULL),
(15, 3, NULL, 'Back to the village', 0, 1),
(16, 4, NULL, 'Back to the village', 0, 1),
(17, 5, NULL, 'Back to the village', 0, 1);
