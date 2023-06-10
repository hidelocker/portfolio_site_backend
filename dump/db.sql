CREATE DATABASE IF NOT EXISTS `portfolio_site` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `portfolio_site`;

CREATE TABLE `admin` (
  `login` varchar(255) NOT NULL,
  `pwh` varchar(255) NOT NULL,
  `id_token` varchar(255) NOT NULL,
  `last_updated` varchar(55) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `admin` (`login`, `pwh`, `id_token`, `last_updated`) VALUES
('akella', '$2y$10$QfYMkS83BTL8CgKcWcAtXuOCNDcHcU2LrbOVFuHbvWCCbc04.lYqO', '89dc4ff2923218b9d75b61d31d2a7ce320b790548a48ad36ad63d274eab831ea38d7623288cacc6812546d8428e8db120082f15b2245807aaac3c78ee463d25c', '2023-04-20 05:58:44');

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'all'),
(2, 'work'),
(3, 'personal'),
(4, 'educational'),
(5, 'other');

CREATE TABLE `contacts` (
  `id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `contacts` (`id`, `title`, `name`, `link`) VALUES
(1, 'github', '@hidelock', 'https://github.com/hidelock'),
(2, 'telegram', '@hidelock', 'https://t.me/hidelock'),
(3, 'mail', 'hidelock@proton.me', 'mailto:hidelock@proton.me'),
(4, 'element', 'hidelock:matrix.org', 'https://matrix.to/#/@hidelock:matrix.org'),
(5, 'jabber', 'hidelock@jabber.fr', 'https://jabberfr.org/');

CREATE TABLE `list_ip` (
  `id` int NOT NULL,
  `ip` varchar(255) NOT NULL,
  `first_visit` varchar(55) NOT NULL,
  `last_visit` varchar(55) NOT NULL,
  `status_blocking_fk` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `news` (
  `id` int NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'default.png',
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `categories_fk` int NOT NULL,
  `createdAt` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `projects` (
  `id` int NOT NULL,
  `image` varchar(255) NOT NULL DEFAULT 'default.png',
  `title` varchar(255) NOT NULL,
  `body` varchar(2000) NOT NULL,
  `stack` varchar(255) NOT NULL,
  `github` varchar(255) NULL,
  `categories_fk` int NOT NULL,
  `createdAt` varchar(55) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `status_blocking` (
  `id` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `status_blocking` (`id`, `name`) VALUES
(1, 'allowed'),
(2, 'denied');


ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `list_ip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_blocking_fk` (`status_blocking_fk`);

ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_fk` (`categories_fk`);

ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categories_fk` (`categories_fk`);

ALTER TABLE `status_blocking`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `contacts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `list_ip`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `news`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `projects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `status_blocking`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `list_ip`
  ADD CONSTRAINT `list_ip_ibfk_1` FOREIGN KEY (`status_blocking_fk`) REFERENCES `status_blocking` (`id`);

ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`categories_fk`) REFERENCES `categories` (`id`);

ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`categories_fk`) REFERENCES `categories` (`id`);
COMMIT;