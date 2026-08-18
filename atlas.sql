-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 13/07/2026 às 20:32
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
-- Estrutura para tabela `acessar_historico`
--

CREATE TABLE `acessar_historico` (
  `id_historico_acesso` int(11) NOT NULL,
  `id_historico` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `admins`
--

CREATE TABLE `admins` (
  `id_admin` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `hash_senha` varchar(255) NOT NULL,
  `data_criacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `autor`
--

CREATE TABLE `autor` (
  `id_autor` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `etapas`
--

CREATE TABLE `etapas` (
  `id_etapa` int(11) NOT NULL,
  `numero_ordem` int(11) NOT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `descricao` text NOT NULL,
  `id_guia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `favoritos`
--

CREATE TABLE `favoritos` (
  `id_favorito` int(11) NOT NULL,
  `data_favorito` datetime DEFAULT current_timestamp(),
  `id_usuario` int(11) NOT NULL,
  `id_guia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `guias`
--

CREATE TABLE `guias` (
  `id_guia` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `data_atualizacao` datetime DEFAULT current_timestamp(),
  `id_categoria` int(11) NOT NULL,
  `id_dificuldade` int(11) NOT NULL,
  `id_autor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `guias_materiais`
--

CREATE TABLE `guias_materiais` (
  `id_guias_materiais` int(11) NOT NULL,
  `id_guia` int(11) NOT NULL,
  `id_material` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_guias`
--

CREATE TABLE `historico_guias` (
  `id_historico_guia` int(11) NOT NULL,
  `data_visualizacao` datetime DEFAULT current_timestamp(),
  `id_usuario` int(11) NOT NULL,
  `id_guia` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `materiais`
--

CREATE TABLE `materiais` (
  `id_material` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `midias`
--

CREATE TABLE `midias` (
  `id_midia` int(11) NOT NULL,
  `tipo` enum('imagem','video') NOT NULL,
  `arquivo` varchar(255) NOT NULL,
  `id_guia` int(11) NOT NULL,
  `id_etapa` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `moderacao_conteudo`
--

CREATE TABLE `moderacao_conteudo` (
  `id_moderacao` int(11) NOT NULL,
  `motivo` text DEFAULT NULL,
  `data_acao` datetime DEFAULT current_timestamp(),
  `id_admin` int(11) NOT NULL,
  `id_guia` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `niveis_dificuldade`
--

CREATE TABLE `niveis_dificuldade` (
  `id_dificuldade` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `hash_senha` varchar(255) NOT NULL,
  `preferencia` varchar(100) DEFAULT NULL,
  `data_criacao` datetime DEFAULT current_timestamp(),
  `data_delete` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `acessar_historico`
--
ALTER TABLE `acessar_historico`
  ADD PRIMARY KEY (`id_historico_acesso`),
  ADD KEY `acessar_historico_fk` (`id_historico`),
  ADD KEY `acessar_historico_usuario_fk` (`id_usuario`);

--
-- Índices de tabela `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id_admin`);

--
-- Índices de tabela `autor`
--
ALTER TABLE `autor`
  ADD PRIMARY KEY (`id_autor`);

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
  ADD KEY `etapas_guia_fk` (`id_guia`);

--
-- Índices de tabela `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id_favorito`),
  ADD KEY `favoritos_usuario_fk` (`id_usuario`),
  ADD KEY `favoritos_guia_fk` (`id_guia`);

--
-- Índices de tabela `guias`
--
ALTER TABLE `guias`
  ADD PRIMARY KEY (`id_guia`),
  ADD KEY `guias_categoria_fk` (`id_categoria`),
  ADD KEY `guias_dificuldade_fk` (`id_dificuldade`),
  ADD KEY `guias_autor_fk` (`id_autor`);

--
-- Índices de tabela `guias_materiais`
--
ALTER TABLE `guias_materiais`
  ADD PRIMARY KEY (`id_guias_materiais`),
  ADD KEY `guias_materiais_guia_fk` (`id_guia`),
  ADD KEY `guias_materiais_material_fk` (`id_material`);

--
-- Índices de tabela `historico_guias`
--
ALTER TABLE `historico_guias`
  ADD PRIMARY KEY (`id_historico_guia`),
  ADD KEY `historico_guias_usuario_fk` (`id_usuario`),
  ADD KEY `historico_guias_guia_fk` (`id_guia`);

--
-- Índices de tabela `materiais`
--
ALTER TABLE `materiais`
  ADD PRIMARY KEY (`id_material`);

--
-- Índices de tabela `midias`
--
ALTER TABLE `midias`
  ADD PRIMARY KEY (`id_midia`),
  ADD KEY `midias_guia_fk` (`id_guia`),
  ADD KEY `midias_etapa_fk` (`id_etapa`);

--
-- Índices de tabela `moderacao_conteudo`
--
ALTER TABLE `moderacao_conteudo`
  ADD PRIMARY KEY (`id_moderacao`),
  ADD KEY `moderacao_admin_fk` (`id_admin`),
  ADD KEY `moderacao_guia_fk` (`id_guia`);

--
-- Índices de tabela `niveis_dificuldade`
--
ALTER TABLE `niveis_dificuldade`
  ADD PRIMARY KEY (`id_dificuldade`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `acessar_historico`
--
ALTER TABLE `acessar_historico`
  MODIFY `id_historico_acesso` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `admins`
--
ALTER TABLE `admins`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT de tabela `guias`
--
ALTER TABLE `guias`
  MODIFY `id_guia` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT de tabela `midias`
--
ALTER TABLE `midias`
  MODIFY `id_midia` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `moderacao_conteudo`
--
ALTER TABLE `moderacao_conteudo`
  MODIFY `id_moderacao` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `acessar_historico`
--
ALTER TABLE `acessar_historico`
  ADD CONSTRAINT `acessar_historico_fk` FOREIGN KEY (`id_historico`) REFERENCES `historico_guias` (`id_historico_guia`),
  ADD CONSTRAINT `acessar_historico_usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Restrições para tabelas `etapas`
--
ALTER TABLE `etapas`
  ADD CONSTRAINT `etapas_guia_fk` FOREIGN KEY (`id_guia`) REFERENCES `guias` (`id_guia`);

--
-- Restrições para tabelas `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_guia_fk` FOREIGN KEY (`id_guia`) REFERENCES `guias` (`id_guia`),
  ADD CONSTRAINT `favoritos_usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Restrições para tabelas `guias`
--
ALTER TABLE `guias`
  ADD CONSTRAINT `guias_autor_fk` FOREIGN KEY (`id_autor`) REFERENCES `autor` (`id_autor`),
  ADD CONSTRAINT `guias_categoria_fk` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  ADD CONSTRAINT `guias_dificuldade_fk` FOREIGN KEY (`id_dificuldade`) REFERENCES `niveis_dificuldade` (`id_dificuldade`);

--
-- Restrições para tabelas `guias_materiais`
--
ALTER TABLE `guias_materiais`
  ADD CONSTRAINT `guias_materiais_guia_fk` FOREIGN KEY (`id_guia`) REFERENCES `guias` (`id_guia`),
  ADD CONSTRAINT `guias_materiais_material_fk` FOREIGN KEY (`id_material`) REFERENCES `materiais` (`id_material`);

--
-- Restrições para tabelas `historico_guias`
--
ALTER TABLE `historico_guias`
  ADD CONSTRAINT `historico_guias_guia_fk` FOREIGN KEY (`id_guia`) REFERENCES `guias` (`id_guia`),
  ADD CONSTRAINT `historico_guias_usuario_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Restrições para tabelas `midias`
--
ALTER TABLE `midias`
  ADD CONSTRAINT `midias_etapa_fk` FOREIGN KEY (`id_etapa`) REFERENCES `etapas` (`id_etapa`),
  ADD CONSTRAINT `midias_guia_fk` FOREIGN KEY (`id_guia`) REFERENCES `guias` (`id_guia`);

--
-- Restrições para tabelas `moderacao_conteudo`
--
ALTER TABLE `moderacao_conteudo`
  ADD CONSTRAINT `moderacao_admin_fk` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`),
  ADD CONSTRAINT `moderacao_guia_fk` FOREIGN KEY (`id_guia`) REFERENCES `guias` (`id_guia`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
