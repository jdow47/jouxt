-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 26/08/2025 às 20:50
-- Versão do servidor: 10.6.19-MariaDB
-- Versão do PHP: 8.3.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `voidplay_xtreamvip`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `admin` varchar(255) DEFAULT NULL,
  `creditos` varchar(255) NOT NULL,
  `creditos_usados` varchar(255) NOT NULL DEFAULT '0',
  `criado_por` varchar(255) DEFAULT '0',
  `servidores` varchar(255) DEFAULT '0',
  `importacao` varchar(255) DEFAULT 'nao',
  `plano` varchar(255) NOT NULL,
  `email` text DEFAULT NULL,
  `telegram` text DEFAULT NULL,
  `whatsapp` text DEFAULT NULL,
  `tipo_link` varchar(20) DEFAULT NULL,
  `saldo_devedor` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `data_criado` date DEFAULT NULL,
  `Vencimento` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `admin`
--

INSERT INTO `admin` (`id`, `user`, `pass`, `admin`, `creditos`, `creditos_usados`, `criado_por`, `servidores`, `importacao`, `plano`, `email`, `telegram`, `whatsapp`, `tipo_link`, `saldo_devedor`, `token`, `data_criado`, `Vencimento`) VALUES
(11, 'admin', 'admin', '1', '0', '0', '0', '0', 'nao', '4', 'teste@gmail.com', 'efr', '43543534', 'padrao', '315', 'ef8af841429ff360989c5270923b6f21865fb2920bd91e959fd3d2599f74bc31', NULL, '2025-08-31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT 0,
  `type` text DEFAULT NULL,
  `is_adult` int(11) DEFAULT 0,
  `bg` text DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  `ordem` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `name` text DEFAULT NULL,
  `usuario` varchar(255) DEFAULT NULL,
  `senha` varchar(255) DEFAULT NULL,
  `Criado_em` datetime DEFAULT NULL,
  `Ultimo_pagamento` datetime DEFAULT NULL,
  `Vencimento` timestamp NULL DEFAULT NULL,
  `is_trial` int(11) NOT NULL DEFAULT 0,
  `adulto` int(11) NOT NULL DEFAULT 0,
  `conexoes` int(11) NOT NULL DEFAULT 1,
  `bloqueio_conexao` varchar(255) NOT NULL DEFAULT 'sim',
  `admin_id` varchar(255) NOT NULL DEFAULT '0',
  `ip` varchar(255) DEFAULT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `ultimo_ip` varchar(255) DEFAULT NULL,
  `Dispositivo` varchar(255) NOT NULL DEFAULT 'Deconhecido',
  `App` varchar(255) NOT NULL DEFAULT 'Deconhecido',
  `Forma_de_pagamento` text DEFAULT NULL,
  `nome_do_pagador` varchar(255) DEFAULT NULL,
  `Whatsapp` varchar(255) DEFAULT NULL,
  `plano` varchar(255) NOT NULL,
  `V_total` varchar(255) NOT NULL DEFAULT '20',
  `c_ocultar_fonte` varchar(255) NOT NULL DEFAULT 'nao',
  `msg` varchar(255) DEFAULT NULL,
  `indicado_por` varchar(255) DEFAULT NULL,
  `device_mac` varchar(17) DEFAULT NULL,
  `device_key` char(6) DEFAULT NULL,
  `email_app` varchar(255) DEFAULT NULL,
  `senha_app` varchar(255) DEFAULT NULL,
  `validade_app` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`id`, `name`, `usuario`, `senha`, `Criado_em`, `Ultimo_pagamento`, `Vencimento`, `is_trial`, `adulto`, `conexoes`, `bloqueio_conexao`, `admin_id`, `ip`, `ultimo_acesso`, `user_agent`, `ultimo_ip`, `Dispositivo`, `App`, `Forma_de_pagamento`, `nome_do_pagador`, `Whatsapp`, `plano`, `V_total`, `c_ocultar_fonte`, `msg`, `indicado_por`, `device_mac`, `device_key`, `email_app`, `senha_app`, `validade_app`) VALUES
(6, '', '746932', '520694', '2025-04-23 16:11:36', '2025-04-23 16:11:36', '2025-05-24 02:59:59', 0, 0, 1, 'sim', '74', NULL, NULL, NULL, NULL, '', '', 'PIX', '', NULL, '55', '30', 'nao', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `devices_apps`
--

CREATE TABLE `devices_apps` (
  `id` int(11) NOT NULL,
  `device_name` varchar(50) DEFAULT NULL,
  `app_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `devices_apps`
--

INSERT INTO `devices_apps` (`id`, `device_name`, `app_name`) VALUES
(1, 'TV SMART', 'SS-IPTV'),
(2, 'TV SMART', 'STB'),
(3, 'TV SMART', 'SMART ONE'),
(4, 'TV SMART', 'SETIPTV'),
(5, 'TV SMART', 'WOWTV'),
(6, 'TV SMART', 'ClouddY'),
(7, 'TV SMART AOC', 'SS-IPTV'),
(8, 'TV SMART AOC', 'ClouddY'),
(9, 'TV SMART AOC', 'SmartUP'),
(10, 'TV PHILIPS', 'SS-IPTV'),
(11, 'TV BOX', 'XCIPTV'),
(12, 'TV BOX', 'SMARTERS PLAYER LITE'),
(13, 'TV BOX', 'SMARTERS PLAYER PRO'),
(14, 'TV BOX', 'EASYPLAY LITE'),
(15, 'TV BOX', 'Stream Player'),
(16, 'TV BOX', 'Smart IPTV Xtream'),
(17, 'TV ANDROID', 'XCIPTV'),
(18, 'TV ANDROID', 'SMARTERS PLAYER LITE'),
(19, 'TV ANDROID', 'SMARTERS PLAYER PRO'),
(20, 'TV ANDROID', 'EASYPLAY LITE'),
(21, 'TV ANDROID', 'Stream Player'),
(22, 'TV ANDROID', 'Smart IPTV Xtream'),
(23, 'TV LG', 'IPTV SMARTERES PRO'),
(24, 'TV LG', 'SS-IPTV'),
(25, 'TV LG', 'STB'),
(26, 'TV LG', 'SMART ONE'),
(27, 'TV LG', 'SETIPTV'),
(28, 'TV LG', 'ClouddY'),
(29, 'TV LG', 'SmartUP'),
(30, 'TV SANSUNG', 'IPTV SMARTERES PRO'),
(31, 'TV SANSUNG', 'SS-IPTV'),
(32, 'TV SANSUNG', 'STB'),
(33, 'TV SANSUNG', 'SMART ONE'),
(34, 'TV SANSUNG', 'SETIPTV'),
(35, 'TV SANSUNG', 'ClouddY'),
(36, 'TV SANSUNG', 'SmartUP'),
(37, 'PC/COMPUTADOR', 'SMARTERS PLAYER PRO'),
(40, 'Roku TV', 'SIMPLE TV'),
(41, 'TV ANDROID', 'Sky Glass+'),
(42, 'TV ANDROID', 'FURIA PLAY SM V3'),
(43, 'TV ANDROID', 'FURIA PLAY SM V4'),
(44, 'Roku TV', 'Quick Player'),
(45, 'Roku TV', 'Meta Player'),
(46, 'TV BOX', 'FURIA PLAY SM V3'),
(47, 'TV BOX', 'FURIA PLAY SM V4'),
(49, 'Roku TV', 'IBO PLAYER PRO'),
(50, 'Roku TV', 'IBO PRO'),
(51, 'TV SANSUNG', 'IBO PLAYER PRO'),
(52, 'TV SANSUNG', 'IBO PRO'),
(53, 'TV LG', 'IBO PLAYER PRO'),
(54, 'TV LG', 'IBO PRO'),
(55, 'TV SMART', 'IBO PLAYER PRO'),
(56, 'TV SMART', 'IBO PRO');

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos`
--

CREATE TABLE `planos` (
  `id` int(11) NOT NULL,
  `nome` text NOT NULL,
  `valor` varchar(255) NOT NULL DEFAULT '20',
  `custo_por_credito` varchar(255) DEFAULT NULL,
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `planos`
--

INSERT INTO `planos` (`id`, `nome`, `valor`, `custo_por_credito`, `admin_id`) VALUES
(55, 'Completo', '30', '2', 74),
(56, 'teste', '30', '0', 11);

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos_admin`
--

CREATE TABLE `planos_admin` (
  `id` int(11) NOT NULL,
  `nome` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `planos_admin`
--

INSERT INTO `planos_admin` (`id`, `nome`) VALUES
(1, 'Nivel 1: Sub-Revenda'),
(2, 'Nivel 2: Revenda'),
(3, 'Nivel 3: Master'),
(4, 'Nivel 4: Master-Pro');

-- --------------------------------------------------------

--
-- Estrutura para tabela `series`
--

CREATE TABLE `series` (
  `id` int(11) NOT NULL,
  `is_adult` int(11) DEFAULT 0,
  `name` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `year` text DEFAULT NULL,
  `stream_type` varchar(255) NOT NULL DEFAULT 'series',
  `cover` text DEFAULT NULL,
  `plot` text DEFAULT NULL,
  `cast` text DEFAULT NULL,
  `director` text DEFAULT NULL,
  `genre` text DEFAULT NULL,
  `release_date` text DEFAULT NULL,
  `releaseDate` text DEFAULT NULL,
  `last_modified` int(11) DEFAULT NULL,
  `rating` text DEFAULT NULL,
  `rating_5based` text DEFAULT NULL,
  `backdrop_path` text DEFAULT NULL,
  `youtube_trailer` text DEFAULT NULL,
  `episode_run_time` text DEFAULT NULL,
  `tmdb_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `series_episodes`
--

CREATE TABLE `series_episodes` (
  `id` int(11) NOT NULL,
  `situacao` text DEFAULT NULL,
  `tipo_link` varchar(20) DEFAULT 'padrao',
  `link` varchar(300) DEFAULT NULL,
  `series_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `episode_num` int(11) DEFAULT NULL,
  `title` text DEFAULT NULL,
  `container_extension` varchar(255) NOT NULL DEFAULT 'mp4',
  `duration_secs` int(11) DEFAULT NULL,
  `duration` text DEFAULT NULL,
  `bitrate` int(11) DEFAULT NULL,
  `cover_big` text DEFAULT NULL,
  `plot` text DEFAULT NULL,
  `movie_image` text DEFAULT NULL,
  `subtitles` text DEFAULT NULL,
  `custom_sid` text DEFAULT NULL,
  `added` int(11) DEFAULT NULL,
  `season` int(11) DEFAULT NULL,
  `tmdb_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `series_seasons`
--

CREATE TABLE `series_seasons` (
  `id` int(11) NOT NULL,
  `series_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `air_date` text DEFAULT NULL,
  `episode_count` int(11) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `overview` text DEFAULT NULL,
  `season_number` int(11) DEFAULT NULL,
  `cover` text DEFAULT NULL,
  `cover_big` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `streams`
--

CREATE TABLE `streams` (
  `id` int(11) NOT NULL,
  `situacao` text DEFAULT NULL,
  `tipo_link` varchar(20) DEFAULT 'padrao',
  `link` varchar(300) DEFAULT NULL,
  `name` text DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `stream_type` varchar(255) DEFAULT 'movie',
  `epg_channel_id` varchar(20) DEFAULT NULL,
  `stream_icon` text DEFAULT NULL,
  `rating` text DEFAULT NULL,
  `rating_5based` text DEFAULT NULL,
  `added` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `container_extension` varchar(10) NOT NULL DEFAULT 'ts',
  `custom_sid` text DEFAULT NULL,
  `direct_source` text DEFAULT NULL,
  `kinopoisk_url` text DEFAULT NULL,
  `tmdb_id` text DEFAULT NULL,
  `cover_big` text DEFAULT NULL,
  `release_date` text DEFAULT NULL,
  `episode_run_time` text DEFAULT NULL,
  `youtube_trailer` text DEFAULT NULL,
  `director` text DEFAULT NULL,
  `actors` text DEFAULT NULL,
  `cast` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `plot` text DEFAULT NULL,
  `age` text DEFAULT NULL,
  `rating_count_kinopoisk` text DEFAULT NULL,
  `country` text DEFAULT NULL,
  `genre` text DEFAULT NULL,
  `backdrop_path` text DEFAULT NULL,
  `duration_secs` text DEFAULT NULL,
  `duration` text DEFAULT NULL,
  `bitrate` text DEFAULT NULL,
  `releasedate` text DEFAULT NULL,
  `subtitles` int(11) DEFAULT NULL,
  `is_adult` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ultimos_acessos`
--

CREATE TABLE `ultimos_acessos` (
  `id` int(11) NOT NULL,
  `id_user` varchar(255) DEFAULT NULL,
  `usuario` varchar(255) DEFAULT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `data` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user` (`user`);

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria_tipo_ordem` (`type`(100),`ordem`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Índices de tabela `devices_apps`
--
ALTER TABLE `devices_apps`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `planos`
--
ALTER TABLE `planos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `planos_admin`
--
ALTER TABLE `planos_admin`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `series_id` (`id`);

--
-- Índices de tabela `series_episodes`
--
ALTER TABLE `series_episodes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_series_episodes_series` (`series_id`);

--
-- Índices de tabela `series_seasons`
--
ALTER TABLE `series_seasons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_series_seasons_series` (`series_id`);

--
-- Índices de tabela `streams`
--
ALTER TABLE `streams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stream_id` (`id`);

--
-- Índices de tabela `ultimos_acessos`
--
ALTER TABLE `ultimos_acessos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `devices_apps`
--
ALTER TABLE `devices_apps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de tabela `planos`
--
ALTER TABLE `planos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de tabela `planos_admin`
--
ALTER TABLE `planos_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `series`
--
ALTER TABLE `series`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `series_episodes`
--
ALTER TABLE `series_episodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `series_seasons`
--
ALTER TABLE `series_seasons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `streams`
--
ALTER TABLE `streams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `ultimos_acessos`
--
ALTER TABLE `ultimos_acessos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
