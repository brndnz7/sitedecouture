-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 22 déc. 2024 à 15:32
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `denizb_sae301`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

CREATE TABLE `article` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ordre` int(11) DEFAULT NULL,
  `titre` varchar(128) DEFAULT NULL,
  `redacteur` varchar(512) DEFAULT NULL,
  `accroche` text DEFAULT NULL,
  `image` varchar(512) DEFAULT NULL,
  `id_theme` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE `cours` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00,
  `difficulte` varchar(50) NOT NULL,
  `places_restantes` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `cours`
--

INSERT INTO `cours` (`id`, `titre`, `description`, `prix`, `difficulte`, `places_restantes`, `image`, `url`) VALUES
(1, 'Cours de Couture - Débutants', 'Apprenez les bases de la couture pour débuter en toute simplicité.', 49.99, 'Débutant', 10, 'https://storage.makerist.de/uploads/course/192/featured_image/carousel_large_featured_image_787d4c04.jpg', '/cours/couture-debutants'),
(2, 'Atelier Couture Créative', 'Libérez votre créativité avec nos ateliers pratiques et ludiques.', 29.99, 'Intermédiaire', 11, 'https://www.stecker.be/attachments/0000000173/cours-formations-couture.jpg', '/cours/couture-creative'),
(3, 'Techniques de Couture Avancées', 'Perfectionnez vos compétences avec des techniques avancées.', 19.99, 'Avancé', 0, 'https://coupdecoudre.com/wp-content/uploads/2017/11/cours-couture.jpg?w=600', '/cours/couture-avancees'),
(4, 'Cours de Couture - Débutants', 'Apprenez les bases de la couture pour débuter en toute simplicité.', 59.99, 'Débutant', 10, '', '/cours/couture-debutants'),
(5, 'Atelier Couture Créative', 'Libérez votre créativité avec nos ateliers pratiques et ludiques.', 75.99, 'Intermédiaire', 15, 'https://www.stecker.be/attachments/0000000173/cours-formations-couture.jpg', '/cours/couture-creative'),
(6, 'Techniques de Couture Avancées', 'Perfectionnez vos compétences avec des techniques avancées.', 109.99, 'Avancé', 8, 'https://coupdecoudre.com/wp-content/uploads/2017/11/cours-couture.jpg?w=600', '/cours/couture-avancees'),
(7, 'Cours Spécial Couture et Design', 'Apprenez à intégrer des techniques de couture au design moderne.', 129.99, 'Avancé', 12, 'https://example.com/images/couture-design.jpg', '/cours/couture-design'),
(8, 'Initiation à la Machine à Coudre', 'Un cours pour apprendre à utiliser une machine à coudre.', 39.99, 'Débutant', 20, '', '/cours/initiation-machine'),
(11, 'testtt', 'azfzfzecvz', 0.00, '', 12, '', ''),
(12, 'test final', 'test final', 0.00, 'Avancé', 99, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `element`
--

CREATE TABLE `element` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ordre` int(11) DEFAULT NULL,
  `type` varchar(128) DEFAULT NULL,
  `contenu` text DEFAULT NULL,
  `image` varchar(512) DEFAULT NULL,
  `id_article` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `theme`
--

CREATE TABLE `theme` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(128) DEFAULT NULL,
  `description` varchar(512) DEFAULT NULL,
  `image` varchar(512) DEFAULT NULL,
  `prix` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `theme`
--

INSERT INTO `theme` (`id`, `nom`, `description`, `image`, `prix`) VALUES
(1, 'Cône de Fil Blanc', 'Cône de fil polyester blanc pour machines à coudre.', '6766dade9b4746.22773722.jpg', 9.99),
(2, 'Aiguilles Universelles', 'Pack de 10 aiguilles universelles pour tous types de tissus.', '6766daf43a9b42.79743539.jpg', 19.99),
(3, 'Pieds-de-biche Multiples', 'Kit de 5 pieds-de-biche pour diverses fonctions.', '6766db35b20b27.71676899.jpg', 49.99),
(4, 'Canettes Transparentes', 'Lot de 20 canettes compatibles avec plusieurs machines.', '6766db6c0b3b38.85782509.jpg', 14.99),
(5, 'Cone machine SINGER', 'Noir', '67634338960838.73952843.jpg', 19.99),
(6, 'Housse de Protection', 'Housse universelle pour protéger votre machine à coudre.', '6766db563bc490.81104026.jpg', 109.99),
(8, 'Ciseaux de Couture', 'Ciseaux professionnels pour une découpe précise.', '6766db984d18c1.75377650.png', 29.99),
(9, 'Boîte de Rangement', 'Boîte de rangement pour accessoires de couture.', '6766dbaf400775.35410293.jpg', 10.99),
(10, 'Cône de Fil bleu', 'Cône de fil polyester bleu pour machines à coudre.', '6766dbe58ccc70.91751929.jpg', 5.99);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_theme` (`id_theme`);

--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `element`
--
ALTER TABLE `element`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_article` (`id_article`);

--
-- Index pour la table `theme`
--
ALTER TABLE `theme`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `article`
--
ALTER TABLE `article`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `element`
--
ALTER TABLE `element`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `theme`
--
ALTER TABLE `theme`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT `article_ibfk_1` FOREIGN KEY (`id_theme`) REFERENCES `theme` (`id`);

--

-- Contraintes pour la table `element`
--
ALTER TABLE `element`
  ADD CONSTRAINT `element_ibfk_1` FOREIGN KEY (`id_article`) REFERENCES `article` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
