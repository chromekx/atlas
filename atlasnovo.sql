-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17/09/2026 às 16:20
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `atlas`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `etapas`
--

CREATE TABLE `etapas` (
  `id_etapa` int(11) NOT NULL,
  `id_guia` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descricao` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `favoritos`
--

CREATE TABLE `favoritos` (
  `id_usuario` int(11) NOT NULL,
  `id_guia` int(11) NOT NULL,
  `data_favorito` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `guia`
--

CREATE TABLE `guia` (
  `id_guia` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descricao` text NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `dificuldade` enum('dificil','normal','facil') NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `data_criacao` datetime NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` datetime NOT NULL DEFAULT current_timestamp(),
  `midia1` blob NOT NULL,
  `midia2` blob NOT NULL,
  `midia3` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `guia_materiais`
--

CREATE TABLE `guia_materiais` (
  `idguia_material` int(11) NOT NULL,
  `id_guia` int(11) NOT NULL,
  `id_material` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_guias`
--

CREATE TABLE `historico_guias` (
  `id_historico_guia` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_guia` int(11) NOT NULL,
  `data_visualizacao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `materiais`
--

CREATE TABLE `materiais` (
  `id_material` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `foto` blob DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `preferencia` enum('esportes','musica','livros','cinema') NOT NULL,
  `nivel` enum('1','2','3','') NOT NULL DEFAULT '3',
  `estado` enum('ativo','inativo','','') NOT NULL DEFAULT 'ativo',
  `data_criacao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `foto`, `nome`, `email`, `senha`, `preferencia`, `nivel`, `estado`, `data_criacao`) VALUES
(1, 0x313738373537393430315f67617469726f2e61766966, 'Paola Carosella', 'victorhsouza001@gmail.com', '$2y$10$jo.jURxYOvJDvpHdhUo9geaL5XgSLnhYsqXVKR2K.4wnNUIHxxLtq', 'esportes', '3', 'inativo', '2026-07-15 16:45:43'),
(2, NULL, 'Victor', 'victor.souza1@alunos.sc.senac.br', '$2y$10$7s5/cD5e4pbu22LLbeoz5ukaJDpCsoFbULggp3wanBDikmlQA4UB.', 'esportes', '1', 'ativo', '2026-08-10 09:25:53'),
(3, NULL, 'Claudinha', 'claudia.werlich@gmail.com', '$2y$10$vIGTKaRhgijlEnIzViVA6.7NA3PqOcthVR.whI7OcdnnpjFs/kakO', 'livros', '1', 'ativo', '2026-08-28 08:09:24'),
(4, 0x313738373931373233365f313738373532343834375f6761746f6c6172616e6a612e6a7065, 'Victor', 'victor@gmail.com', '$2y$10$9yK1FK4ndJikrj/tBSyE2eYWUYXFZx38E.opyTHK210ICq.RVlHuW', 'musica', '1', 'ativo', '2026-08-14 10:39:11'),
(5, 0x313738373931353138305f313738373532343938375f6761746f6272616e636f2e6a7065, 'Luyza', 'luyza@gmail.com', '$2y$10$L.M./AaCzjfn2IdlTdzsguR1Sqs24WKcMVNoHft8vAR9USrIdARga', 'musica', '3', 'ativo', '2026-08-28 08:06:20'),
(6, 0x313738373931353230385f313738373532343933335f6761746f707265746f2e6a7065, 'Davi', 'davi@gmail.com', '$2y$10$koAWHESsRZ9OkEn5QIDXW.Wjr.ot.PrUyk9COLTu729nzpUJqgtDm', 'musica', '3', 'ativo', '2026-08-28 08:06:48'),
(7, 0x313738373931353233325f313738373532353132365f6761746f74757865646f2e6a7067, 'Felipe', 'felipe@gmail.com', '$2y$10$RH4Nx4bFoL/hNg6BhaGc5uMjoh9jJtQEV/EWUR/ioUluxBRCxTPHO', 'cinema', '3', 'ativo', '2026-08-28 08:07:12'),
(10, NULL, 'teste', 'teste@gmail.com', '$2y$10$dnCs2R/bOtv1UAegk4niXOAa/wwrfEUi9JOxMaG/cl2hb1xTk6Wyq', 'esportes', '3', 'ativo', '2026-08-28 08:17:39'),
(11, NULL, 'teste2', 'teste2@gmail.com', '$2y$10$d2ZP2cwCTttXTfAa/dxKBeuuITpt3UgvISQaCuMCp3HZnl4AB.5eK', 'musica', '3', 'ativo', '2026-08-28 08:36:26');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Índices de tabela `etapas`
--
ALTER TABLE `etapas`
  ADD PRIMARY KEY (`id_etapa`),
  ADD KEY `fk_etapas_guia` (`id_guia`);

--
-- Índices de tabela `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id_usuario`,`id_guia`),
  ADD KEY `fk_favoritos_guia` (`id_guia`);

--
-- Índices de tabela `guia`
--
ALTER TABLE `guia`
  ADD PRIMARY KEY (`id_guia`),
  ADD KEY `fk_guias_categoria` (`id_categoria`),
  ADD KEY `fk_guia_usuario` (`id_usuario`);

--
-- Índices de tabela `guia_materiais`
--
ALTER TABLE `guia_materiais`
  ADD PRIMARY KEY (`idguia_material`),
  ADD KEY `fk_guia_materiais_guia` (`id_guia`),
  ADD KEY `fk_guia_materiais_material` (`id_material`);

--
-- Índices de tabela `historico_guias`
--
ALTER TABLE `historico_guias`
  ADD PRIMARY KEY (`id_historico_guia`),
  ADD KEY `fk_historico_usuario` (`id_usuario`),
  ADD KEY `fk_historico_guia` (`id_guia`);

--
-- Índices de tabela `materiais`
--
ALTER TABLE `materiais`
  ADD PRIMARY KEY (`id_material`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `etapas`
--
ALTER TABLE `etapas`
  MODIFY `id_etapa` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `guia`
--
ALTER TABLE `guia`
  MODIFY `id_guia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `guia_materiais`
--
ALTER TABLE `guia_materiais`
  MODIFY `idguia_material` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `historico_guias`
--
ALTER TABLE `historico_guias`
  MODIFY `id_historico_guia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `materiais`
--
ALTER TABLE `materiais`
  MODIFY `id_material` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `etapas`
--
ALTER TABLE `etapas`
  ADD CONSTRAINT `fk_etapas_guia` FOREIGN KEY (`id_guia`) REFERENCES `guia` (`id_guia`);

--
-- Restrições para tabelas `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `fk_favoritos_guia` FOREIGN KEY (`id_guia`) REFERENCES `guia` (`id_guia`),
  ADD CONSTRAINT `fk_favoritos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Restrições para tabelas `guia`
--
ALTER TABLE `guia`
  ADD CONSTRAINT `fk_guia_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `fk_guias_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`);

--
-- Restrições para tabelas `guia_materiais`
--
ALTER TABLE `guia_materiais`
  ADD CONSTRAINT `fk_guia_materiais_guia` FOREIGN KEY (`id_guia`) REFERENCES `guia` (`id_guia`),
  ADD CONSTRAINT `fk_guia_materiais_material` FOREIGN KEY (`id_material`) REFERENCES `materiais` (`id_material`);

--
-- Restrições para tabelas `historico_guias`
--
ALTER TABLE `historico_guias`
  ADD CONSTRAINT `fk_historico_guia` FOREIGN KEY (`id_guia`) REFERENCES `guia` (`id_guia`),
  ADD CONSTRAINT `fk_historico_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
