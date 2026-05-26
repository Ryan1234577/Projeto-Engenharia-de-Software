-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/05/2026 às 03:28
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
-- Banco de dados: `access_fit`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) DEFAULT NULL,
  `turma_id` int(11) DEFAULT NULL,
  `data_aula` date DEFAULT NULL,
  `status` enum('confirmado','cancelado') DEFAULT 'confirmado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `aluno_id`, `turma_id`, `data_aula`, `status`) VALUES
(3, 2, 2, '2026-05-11', 'confirmado'),
(4, 2, 2, '2026-05-12', 'confirmado'),
(6, 20, 6, '2026-05-16', 'confirmado'),
(8, 21, 2, '2026-05-16', 'confirmado'),
(9, 25, 5, '2026-05-17', 'confirmado'),
(10, 27, 8, '2026-05-18', 'confirmado'),
(11, 29, 5, '2026-05-19', 'confirmado');

-- --------------------------------------------------------

--
-- Estrutura para tabela `alunos`
--

CREATE TABLE `alunos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `data_nascimento` date DEFAULT NULL,
  `telefone` varchar(15) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `historico_saude` text DEFAULT NULL,
  `status` enum('ativo','inativo') DEFAULT 'ativo',
  `instrutor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `alunos`
--

INSERT INTO `alunos` (`id`, `usuario_id`, `nome`, `cpf`, `data_nascimento`, `telefone`, `endereco`, `historico_saude`, `status`, `instrutor_id`) VALUES
(2, 2, '', '123.456.789-00', '2000-01-01', '(11) 99999-9999', 'Rua Exemplo, 123', 'Nenhuma restrição', 'ativo', 17),
(3, 1, 'Ryan Marcos da Silva Costa', '535.435.008-56', '2007-02-12', '11976318140', NULL, NULL, 'ativo', 19),
(4, 4, 'Orlando Costa', '184.865.698.00', '1976-05-14', '11984569877', 'Rua dos Palmares, Vila São Paulo, Ferraz de Vasconcelos', 'Saúde estável', 'ativo', 20),
(5, 17, 'Kaua Mendes', '111222333-45', '2006-03-01', '11988559966', 'Rua Itaqua, Vila Nova, Poá', 'autismo alto', 'ativo', 18),
(6, 22, 'Matheus Araujo', '88855566698', '2005-03-16', '11988664334', 'Rua ferraz, 44, Vila São Paulo, Ferraz de Vacscon', 'Saúde estável', 'ativo', NULL),
(7, 24, '', '', NULL, '', NULL, NULL, 'inativo', 17),
(9, 27, 'Kaue Sergiooooo', '35156988748202', '2007-02-15', '1198545711720', 'Rua  Mariana Alves de Moraes, Vila São Paulo, 33, Ferraz', 'Muito bem avaliado', 'ativo', 22),
(10, 31, 'Julio Silva', '58746523189', '2006-06-07', '11976543212', 'Rua Laranja, Vila sp, Ferraz', 'O aluno está bem fisicamente', 'inativo', 17),
(11, 35, 'José Silva', '45678932156888', '1996-07-24', '11985647521', 'Rua kkkk', 'Sou um pouco debilitado', 'ativo', 19),
(15, 50, 'Natalia Ferreira', '78954526585', '1998-06-18', '11945877525', 'Rua ............, Vila ...................., Ferraz', 'Não possuo nenhuma alergia!', 'inativo', 19),
(16, 51, 'Josias Silva', '12345698756', '2001-02-14', '11988556936', 'Rua .......', 'Saudde estavel', 'ativo', 19),
(20, 56, 'Guerino Paz', '85698745689', '2009-05-29', '11975874656666', 'Rua..., Vila ............, 33', 'Não possuo nenhuma alergia ', 'ativo', 18),
(21, 57, 'Julia Mendes', '78998745685', '2007-01-27', '11985858585', 'Rua medeiros, ....., ......, Ferraz de Vasconcelos', 'Estou em busca de perder massa', 'ativo', 20),
(22, 59, 'Pepe Silva', '58965887400', '2008-11-15', '11985663465', 'Rua Mariana ....., ......, Mogi das Cruzes', 'Estou buscando o fisico ideal ', 'inativo', 26),
(23, 60, 'Bianca Silva', '11155588875', '2010-02-23', '11988887777', 'Rua ......, .........,  Itaquera', 'Fisicamente bem ', 'ativo', 26),
(24, 61, 'Felipa Silva Costa', '58547856985111', '2009-02-25', '11977448855000', 'Rua ......., ..........., FerrazZ', 'LegalL', 'ativo', 26),
(25, 62, 'Pepita Costa', '69854123698', '2007-11-21', '11985646987', 'Rua ....., ......, Itaqua', 'Estou bem!!!', 'ativo', 26),
(26, 74, 'Cássio Ramos ', '69874589625', '2008-06-03', '11955236541', 'Rua ....................., ......................., Ferraz', 'Bem fisicamente', 'inativo', NULL),
(27, 75, 'Marcello Pazzzzzzzzzzzzzzzzzzzzz', '78545696512', '2009-04-06', '11954856922', 'Rua ............, ................., Poá', 'Estou a procura de um fisico melhor ', 'ativo', 29),
(28, 79, 'Kaka  da Silva Camps ', '15478965423', '2008-07-17', '11985647896', 'Rua llkkkpkpkpkpkpkp', 'Bem !', 'ativo', NULL),
(29, 80, 'kaua', '53672191715', '2004-06-08', '11955452211', 'rua fatec, 11 itaqua', 'sem alergias', 'ativo', 31),
(30, 84, 'Ingrid Alves', '11122233444', '2006-06-06', '11959595151', 'rua marina, 25 POA', 'sem alergias ou problema de saude', 'ativo', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes_fisicas`
--

CREATE TABLE `avaliacoes_fisicas` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) DEFAULT NULL,
  `instrutor_id` int(11) DEFAULT NULL,
  `data_avaliacao` date DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `altura` decimal(4,2) DEFAULT NULL,
  `imc` decimal(4,2) DEFAULT NULL,
  `medida_braco` decimal(5,2) DEFAULT NULL,
  `medida_cintura` decimal(5,2) DEFAULT NULL,
  `medida_quadril` decimal(5,2) DEFAULT NULL,
  `medida_perna` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `avaliacoes_fisicas`
--

INSERT INTO `avaliacoes_fisicas` (`id`, `aluno_id`, `instrutor_id`, `data_avaliacao`, `peso`, `altura`, `imc`, `medida_braco`, `medida_cintura`, `medida_quadril`, `medida_perna`) VALUES
(1, 2, 16, '2026-05-04', 75.50, 1.74, 24.94, 17.00, 44.00, 55.00, 22.00),
(2, 4, 15, '2026-05-04', 80.00, 1.74, 26.42, 35.00, 46.00, 22.00, 44.00),
(3, 6, 18, '2026-05-14', 75.58, 1.80, 23.33, 45.00, 67.00, 53.00, 90.00),
(4, 5, 20, '2026-05-06', 75.00, 1.80, 23.15, 78.00, 70.00, 69.00, 80.00),
(5, 7, 17, '2026-05-10', 80.00, 1.75, 26.12, 37.00, 80.00, 95.00, 55.00),
(6, 10, 17, '2026-05-11', 75.00, 1.76, 24.21, 35.00, 80.00, 95.00, 55.00),
(8, 20, 18, '2026-05-16', 75.50, 1.78, 23.83, 35.50, 80.00, 95.00, 55.00),
(9, 21, 20, '2026-05-16', 75.50, 1.74, 24.94, 35.00, 80.00, 95.00, 55.00),
(10, 23, 26, '2026-05-17', 75.50, 1.74, 24.94, 35.00, 80.00, 95.00, 55.00),
(11, 25, 26, '2026-05-17', 75.50, 1.74, 24.94, 35.00, 80.00, 95.00, 55.00),
(12, 27, 29, '2026-05-18', 75.50, 1.74, 24.94, 35.00, 85.00, 90.00, 53.00),
(13, 29, 31, '2026-05-19', 75.00, 1.86, 21.68, 35.00, 80.00, 95.00, 55.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `coordenadores`
--

CREATE TABLE `coordenadores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `coordenadores`
--

INSERT INTO `coordenadores` (`id`, `usuario_id`, `nome`, `email`, `telefone`, `data_cadastro`) VALUES
(2, 30, 'Lucas Aguiar', 'lucas@accessfit.com', '11985423658', '2026-05-11 12:14:21'),
(8, 70, 'Rodrigo Garro', 'rodrigo@accessfit.gmail', '11987548547', '2026-05-17 21:10:36'),
(9, 77, 'Hacker Secreto', 'hacker@accessfit.com', '11985644751', '2026-05-18 12:47:56'),
(10, 82, 'Felipe Melo', 'felipe@accessfit.com', '11921215151', '2026-05-19 10:18:50');

-- --------------------------------------------------------

--
-- Estrutura para tabela `fichas_treino`
--

CREATE TABLE `fichas_treino` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) DEFAULT NULL,
  `dia_semana` varchar(20) DEFAULT NULL,
  `instrutor_id` int(11) DEFAULT NULL,
  `exercicio` varchar(100) NOT NULL,
  `series` int(11) DEFAULT NULL,
  `repeticoes` int(11) DEFAULT NULL,
  `observacao` text DEFAULT NULL,
  `data_criacao` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fichas_treino`
--

INSERT INTO `fichas_treino` (`id`, `aluno_id`, `dia_semana`, `instrutor_id`, `exercicio`, `series`, `repeticoes`, `observacao`, `data_criacao`) VALUES
(1, 2, 'Segunda', 15, 'Peito', 3, 10, 'O aluno deve realizar o treino de peito com seu instrutor ', '2026-05-04'),
(2, 4, 'Quarta', 13, 'Pernas', 2, 15, 'O aluno deve realizar todas as repetições com cuidado', '2026-05-04'),
(5, 2, 'Segunda', 17, 'Supino Reto', 5, 10, 'Manter a postura ereta ', '2026-05-04'),
(6, 9, 'Quarta', 22, 'Costas', 5, 10, 'Manter a postura correta na hora do exercicio', '2026-05-10'),
(7, 7, 'Sábado', 17, 'Perna', 5, 5, 'Manter-se sentado corretamente', '2026-05-10'),
(8, 10, 'Quarta', 17, 'Pernas', 4, 6, 'Nesse treino, o aluno deve manter a postura correta. ', '2026-05-11'),
(9, 20, 'Sexta', 18, 'Tricepis e Bicepis', 4, 5, 'Manter a postura ereta', '2026-05-16'),
(10, 20, 'Terça', 18, 'Tricepis', 4, 5, 'Manter a postura ereta', '2026-05-16'),
(11, 5, 'Quinta', 18, 'Peito', 3, 10, 'Manter peito estufado', '2026-05-16'),
(12, 20, 'Sábado', 18, 'Perna', 3, 12, 'Manter postura correta para o exercicio', '2026-05-16'),
(13, 21, 'Terça', 20, 'Costas', 4, 10, 'Manter a postura ereta e ajustada', '2026-05-16'),
(14, 24, 'Quinta', 26, 'Peito', 3, 12, 'Manter postura reta', '2026-05-17'),
(15, 23, 'Sexta', 26, 'Supino Reto', 4, 10, 'Manter a postura ereta', '2026-05-17'),
(16, 25, 'Sábado', 26, 'Supino Reto', 4, 10, 'Manter a postura correta', '2026-05-17'),
(17, 23, 'Quinta', 26, 'Supino Reto', 3, 10, 'Manter a postura corretamente', '2026-05-17'),
(18, 23, 'Quarta', 26, 'Supino Reto', 3, 10, 'Manter a postura correta!', '2026-05-17'),
(19, 25, 'Segunda', 26, 'Supino Reto', 4, 10, 'Manter a postura corretamente!', '2026-05-17'),
(20, 6, 'Sexta', 26, 'Supino Reto', 4, 15, 'Manter postura', '2026-05-17'),
(21, 23, 'Sábado', 26, 'Peito', 4, 10, 'Manter a postura correta!', '2026-05-17'),
(22, 27, 'Sexta', 29, 'Supino Reto', 3, 12, 'Manter a postura correta!', '2026-05-18'),
(23, 29, 'Segunda', 31, 'Supino reto maquina, supino inclinado e triceps na polia alta', 3, 8, 'Sempre chegando proximo a falha e trabalhar de 8 a 10 repeticoes', '2026-05-19');

-- --------------------------------------------------------

--
-- Estrutura para tabela `instrutores`
--

CREATE TABLE `instrutores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `especialidade` varchar(50) DEFAULT NULL,
  `cref` varchar(20) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `data_contratacao` date DEFAULT curdate(),
  `status` enum('ativo','inativo') DEFAULT 'ativo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `instrutores`
--

INSERT INTO `instrutores` (`id`, `usuario_id`, `nome`, `especialidade`, `cref`, `telefone`, `foto_url`, `data_contratacao`, `status`) VALUES
(17, 13, 'Renato Cariani', 'Cardio', '123458-G/SP', '11978567411', NULL, '2026-05-04', 'ativo'),
(18, 14, 'Livia Andrade', 'Personal Trainer', '456123-G/SP', '1145892354', NULL, '2026-05-04', 'ativo'),
(19, 15, 'Luis Fernando', 'Musculação', '789456-G/SP', '11978541232', NULL, '2026-05-04', 'ativo'),
(20, 16, 'Renata Ferreira', 'Pilates', '444555-G/SP', '11942245225', NULL, '2026-05-04', 'ativo'),
(21, 28, 'Ryan Costa', 'Crossfit', '456987-G/SP', NULL, NULL, '2026-05-10', 'ativo'),
(22, 29, 'william silva', 'Crossfit', '897589-G/SP', NULL, NULL, '2026-05-10', 'ativo'),
(23, 58, 'Nilton Barros', 'Musculação', '', NULL, NULL, '2026-05-16', 'ativo'),
(26, 65, 'Carlinha López', 'Crossfit', '789654-G/SP', '11987456587', 'https://cdn-icons-png.flaticon.com/512/149/149071.png', '2026-05-17', 'ativo'),
(29, 76, 'Amauri Costa', 'Crossfit', '254698-G/SP', '11956693547', 'https://cdn-icons-png.flaticon.com/512/149/149071.png', '2026-05-18', 'ativo'),
(30, 78, 'Nicoas Freitas', 'Pilates', '156874-G/SP', '11964785321', NULL, '2026-05-18', 'ativo'),
(31, 81, 'junior silva', 'Musculação', '123446-G/SP', '11921516141', 'https://cdn-icons-png.flaticon.com/512/149/149071.png', '2026-05-19', 'ativo'),
(32, 83, 'Udson Freitas', 'Musculação', '856978-G/SP', '1189652314', NULL, '2026-05-19', 'ativo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) DEFAULT NULL,
  `plano_id` int(11) DEFAULT NULL,
  `valor` decimal(8,2) NOT NULL,
  `mes_referencia` varchar(7) NOT NULL,
  `data_pagamento` date DEFAULT NULL,
  `forma_pagamento` enum('pix','cartao') NOT NULL,
  `status` enum('em dia','atrasado') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pagamentos`
--

INSERT INTO `pagamentos` (`id`, `aluno_id`, `plano_id`, `valor`, `mes_referencia`, `data_pagamento`, `forma_pagamento`, `status`) VALUES
(1, 2, 1, 90.00, '2026-05', '2026-05-05', 'pix', 'em dia'),
(24, 4, 4, 350.00, '2026-05', '2026-05-06', 'pix', 'em dia'),
(26, 6, 4, 350.00, '2026-05', '2026-05-06', 'pix', 'em dia'),
(28, 9, 3, 299.90, '2026-05', '2026-05-10', 'cartao', 'em dia'),
(29, 7, 4, 350.00, '2026-05', '2026-05-10', 'cartao', 'em dia'),
(30, 10, 3, 284.91, '2026-05', '2026-05-11', 'pix', 'atrasado'),
(33, 15, 3, 299.90, '2026-05', '2026-05-15', 'cartao', 'em dia'),
(34, 16, 4, 350.00, '2026-05', '2026-05-15', 'cartao', 'em dia'),
(38, 20, 1, 90.00, '2026-05', '2026-05-14', 'pix', 'em dia'),
(40, 21, 7, 47.50, '2026-05', '2026-05-16', 'pix', 'em dia'),
(41, 9, 1, 90.00, '2026-05', '2026-05-16', '', 'em dia'),
(42, 5, 1, 350.00, '2026-05', '2026-05-16', '', 'em dia'),
(47, 11, 1, 50.00, '2026-05', '2026-05-16', '', 'em dia'),
(48, 16, 1, 50.00, '2026-05', '2026-05-16', '', 'em dia'),
(49, 3, 1, 50.00, '2026-05', '2026-05-16', '', 'em dia'),
(50, 22, 1, 800.00, '2026-05', '2026-05-16', '', 'em dia'),
(52, 6, 1, 50.00, '2026-05', '2026-05-16', '', 'em dia'),
(54, 4, 1, 299.90, '2026-05', '2026-05-16', 'cartao', 'em dia'),
(55, 10, 1, 284.89, '2026-05', '2026-05-17', 'pix', 'em dia'),
(57, 16, 1, 109.98, '2026-05', '2026-05-17', 'cartao', 'em dia'),
(62, 16, 5, 49.99, '2026-05', '2026-05-17', 'pix', 'em dia'),
(64, 23, 2, 110.00, '2026-05', '2026-05-17', 'cartao', 'em dia'),
(65, 5, 5, 50.00, '2026-05', '2026-05-17', 'pix', 'em dia'),
(66, 5, 5, 50.00, '2026-05', '2026-05-17', 'pix', 'em dia'),
(67, 16, 5, 50.00, '2026-05', '2026-05-17', 'pix', 'em dia'),
(69, 22, 4, 350.00, '2026-05', '2026-05-17', 'pix', 'em dia'),
(72, 24, 2, 110.00, '2026-05', '2026-05-17', 'cartao', 'em dia'),
(73, 24, 5, 50.00, '2026-05', '2026-05-17', 'cartao', 'em dia'),
(74, 21, 6, 800.00, '2026-05', '2026-05-17', 'pix', 'em dia'),
(75, 25, 2, 110.00, '2026-05', '2026-05-17', 'pix', 'em dia'),
(76, 23, 4, 350.00, '2026-05', '2026-05-17', 'pix', 'em dia'),
(77, 27, 8, 200.00, '2026-05', '2026-05-18', 'cartao', 'em dia'),
(78, 4, 8, 200.00, '2026-05', '2026-05-18', 'cartao', 'em dia'),
(79, 29, 2, 110.00, '2026-05', '2026-05-19', 'cartao', 'em dia'),
(80, 6, 6, 799.98, '2026-05', '2026-05-19', 'pix', 'em dia');

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos`
--

CREATE TABLE `planos` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `valor` decimal(8,2) NOT NULL,
  `periodo` enum('mensal','trimestral','anual') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `planos`
--

INSERT INTO `planos` (`id`, `nome`, `valor`, `periodo`) VALUES
(1, 'Plano Básico', 90.00, 'mensal'),
(2, 'Plano Premium', 110.00, 'mensal'),
(3, 'Plano Black', 299.90, 'trimestral'),
(4, 'Plano Gold', 350.00, 'anual'),
(5, 'Plano Fulera', 50.00, 'mensal'),
(6, 'Plano Infinit', 800.00, 'anual'),
(7, 'Plano Paraguai', 50.00, 'mensal'),
(8, 'Plano Fifi', 200.00, 'mensal'),
(9, 'Plano Muito Simples', 150.00, 'mensal'),
(10, 'Plano Muito Simples', 150.00, 'mensal'),
(11, 'Plano Fit', 450.00, 'anual');

-- --------------------------------------------------------

--
-- Estrutura para tabela `presencas`
--

CREATE TABLE `presencas` (
  `id` int(11) NOT NULL,
  `aluno_id` int(11) DEFAULT NULL,
  `turma_id` int(11) DEFAULT NULL,
  `data_presenca` date DEFAULT NULL,
  `presente` enum('sim','nao') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `presencas`
--

INSERT INTO `presencas` (`id`, `aluno_id`, `turma_id`, `data_presenca`, `presente`) VALUES
(1, 5, 1, '2026-05-06', 'sim'),
(2, 6, 1, '2026-05-06', 'sim'),
(3, 2, 3, '2026-05-06', 'sim'),
(4, 9, 3, '2026-05-10', 'sim'),
(5, 7, 3, '2026-05-10', 'sim'),
(6, 10, 3, '2026-05-11', 'sim'),
(9, 15, 3, '2026-05-15', 'sim'),
(10, 16, 2, '2026-05-15', 'sim'),
(11, 9, 2, NULL, NULL),
(12, 15, 5, NULL, NULL),
(13, 4, 6, NULL, NULL),
(14, 20, 6, '2026-05-16', 'sim'),
(15, 21, 7, NULL, NULL),
(16, 4, 7, NULL, NULL),
(17, 21, 2, '2026-05-16', 'sim'),
(18, 25, 5, '2026-05-17', 'sim'),
(19, 5, 8, NULL, NULL),
(20, 4, 8, NULL, NULL),
(21, 27, 8, '2026-05-18', 'sim'),
(22, 4, 9, NULL, NULL),
(23, 29, 5, '2026-05-19', 'sim'),
(24, 29, 13, NULL, NULL),
(25, 27, 13, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `turmas`
--

CREATE TABLE `turmas` (
  `id` int(11) NOT NULL,
  `modalidade` enum('spinning','pilates','musculação','treino funcional','aulas coletivas e danças','pilates e mobilidade','lutas','cardio') NOT NULL,
  `dia_semana` varchar(20) DEFAULT NULL,
  `horario` time DEFAULT NULL,
  `limite_vagas` int(11) DEFAULT NULL,
  `instrutor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `turmas`
--

INSERT INTO `turmas` (`id`, `modalidade`, `dia_semana`, `horario`, `limite_vagas`, `instrutor_id`) VALUES
(1, 'spinning', 'Terça-Feira', '12:00:00', 10, 17),
(2, 'pilates', 'Quinta-Feira', '10:00:00', 15, 18),
(3, 'musculação', 'Quarta-Feira', '14:30:00', 18, 19),
(4, 'lutas', 'quarta-feira', '15:00:00', 12, 20),
(5, 'treino funcional', 'Quinta-feira', '13:35:00', 10, 18),
(6, 'lutas', 'Quarta-feira', '16:30:00', 10, 18),
(7, 'treino funcional', 'Sexta-feira', '10:59:00', 12, 20),
(8, 'pilates', 'Quinta-feira', '20:30:00', 10, 26),
(9, 'pilates', 'Quinta-feira', '14:20:00', 7, 29),
(12, 'musculação', 'Sexta-feira', '10:35:00', 10, 29),
(13, 'musculação', 'Segunda-feira', '13:20:00', 15, 31);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `perfil` enum('coordenador','instrutor','aluno') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `perfil`) VALUES
(1, 'Ryan Marcos da Silva Costa', 'ryan@gmail.com', '$2y$10$OdSBcvCY.BhgscFEiaq4FuGW1XefrdYsfNoygbxhJyttkRZCIcnNm', 'aluno'),
(2, 'Rejane da Silva Costaddd', 'rejane22@gmail.com', '$2y$10$gfTuTzi0lOm9kXlY1IDZcO4ypCA/Cpx8Y2eJD4WMWDDt3QUZtrfqa', 'aluno'),
(3, 'Leonardo de Lima', 'leonardo@gmail.com', '$2y$10$IEBlNoamXqnR7dmUHumb4O12NQg9NPs/74hVED3iuQkhKMXsaQn3.', 'aluno'),
(4, 'Orlando Costa', 'orlando@gmail.com', '$2y$10$gF7prROq7fgIGWSaR7SrMOxm45rd6tGXYhEizTHLWVxB1Ph4qemPS', 'aluno'),
(13, 'Renato Cariani', 'cariani@access.gmail', '$2y$10$2E6S9MKDjpaFcyos67cGcehWXoNiMfdEsRsGQuwwaWSugv6nPAPSu', 'instrutor'),
(14, 'Livia Andrade', 'andrade@access.gmail', '$2y$10$VXh/fBgpOMSVzQvBOySYV.bBLrpGlUvfvWzIOIlAmNlKUayX9TaQ2', 'instrutor'),
(15, 'Luis Fernando', 'luis@access.gmail', '$2y$10$..ZiJ9vQVx9T7IiHy1lLq.tdS298vy.NvzISkiH3PyC9pq81xWyji', 'instrutor'),
(16, 'Renata Ferreira', 'renata@access.gmail', '$2y$10$juGav6Uu0CQWidmL/s2TyeKUEMqU2cW9GxfrmWM3GEyXJB94ALhhi', 'instrutor'),
(17, 'Kaua Mendes', 'kaua@gmail.com', '$2y$10$8HZdpPXAZ69CyYFHNnT4XeOpHY.BThQQLKo4R2MwF/RoF/epE6/Ea', 'aluno'),
(18, 'Jade Silva', 'jade@gmail.com', '$2y$10$mSkSyKz7VHvZ1dw9kSc0kOZxY4PFw1eIuPMSeuXC3b31whMkwlbry', 'aluno'),
(20, 'Samuel Lino', 'samuel@gmail.com', '$2y$10$yK0HtyrIn8kHRXqv3xCGwOtn5BYjHzGpPRgbIdHwFFaGflz8oVLt.', 'aluno'),
(21, 'Guilherme Silva ', 'guilherme@gmail.com', '$2y$10$.YCrFmvtMP9t8GQs/29u2OS6bVswAphbz2SD3.GksZelpFamDdTti', 'aluno'),
(22, 'Matheus Araujo', 'matheus@gmail.com', '$2y$10$q5AFy4bSYXUGJhNvNgbzW.Og/GPtz9TIB7BZuu1K3Qevhh0woA3DO', 'aluno'),
(23, 'Luccas Aguiar', 'aguiarlucas@access.com', '$2y$10$lQA4EKjKpdXtMa2I4N/RuOKXmKYwPXqoMIr2QqN7xPw.ctZ7rzfMG', 'coordenador'),
(24, 'Maria Aparecida', 'maria@gamil.com', '$2y$10$xyYf0sk35AX25y8GWAhileWtDiYkVJWb8JgDbFajU86aotFJdSj/S', 'aluno'),
(26, 'Suelen Silva ', 'suelen@gmail.com', '$2y$10$3EEq3FHs8KLe6te1O4C4ye1kzOItfRyKWURv4BgfxBShOTjhXpSLS', 'aluno'),
(27, 'Kaue Sergiooooo', 'kaue@gmail.commmm', '$2y$10$6tzc0LEgOqDBRyKy/BKZK.vZJDuoLip7fDDPvG8sHFcgMwwFz.TsW', 'aluno'),
(28, 'Ryan Costa', 'ryan@access.com', '$2y$10$YLw/vp.6x0PLFlTXRs1IAeNlILktbc8URuZIyRJdkI9fRRBQmoiN6', 'instrutor'),
(29, 'william silva', 'william@access.gmail', '$2y$10$mrGbNteiJV6guzB.GuT2kusKsniW9UpuGp8eBn7dt/HXE3lY79ZG.', 'instrutor'),
(30, 'Lucas Aguiar', 'lucas@accessfit.com', '$2y$10$HnAc7i8mZpjwvVtmmsgUwezkR8qFu4I58p7npyLzUAZ08oEvwryaq', 'coordenador'),
(31, 'Julio Silva', 'julio@gmail.com', '$2y$10$MlR.N6x9W7CwGqpT8/MajezGuPytVPdtaJOiquNMt10B8YJegqEdS', 'aluno'),
(32, 'Cauane Silva ', 'cauane@gmail.com', '$2y$10$YxB93tTyf2uPQbGCbv8vj.Bn1rnuYplsR7DJbg.em2VQiRgp.rGLm', 'aluno'),
(33, 'Jhennyfer', 'jhenny@gmail.com', '$2y$10$eByoahnCKm.yWl8kXx8EtuDM.KCb522Gl3yNeVHwKSOB.MEJJPFLC', 'aluno'),
(34, 'Lurdes', 'lurdes@gmail.com', '$2y$10$rix12xGGb0l4zQ6dafOzaeRHeII3c5ugcJmgSbP/8A4ull22BwOWy', 'aluno'),
(35, 'José Silvaaaaaaaaaaa', 'jose@gmail.comlll', '$2y$10$qhWK3Uc0Lozz3oYUKfwZGOhF.N/NMNiIzXisd7q6c4cmhEXtjtPFS', 'aluno'),
(36, 'Gabriel Souza', 'gabriel@gmail.com', '$2y$10$JIzkwwwYAl552WrQdnjNsucbzzxSLrxt8F6iwDeai5bm1UaB1CF66', 'aluno'),
(37, 'Milena Silva', 'Milena@gmail.com', '$2y$10$fRS19Hd3wLllHDlsOVOS6.UTU/EvRUm97Z/ihx4z9bQgAhmRiN/ku', 'aluno'),
(39, 'Ricardo Silva ', 'ricardo@gmail.com', '$2y$10$R6XgKveIA1C0FSRiLdfKTOo5wQqKi6pNtkLkjIoyolUc.FcbtwpY6', 'aluno'),
(40, 'Thayna Santos', 'Thayna@gmail.com', '$2y$10$rsnxCfeJo5b/0Q6wkQDoBOxb/qNV5PwaOB2XoKjtXRMHBd6DbSxzG', 'aluno'),
(41, 'Rochele Costa', 'rochele@gmail.com', '$2y$10$NJo4CAZdS8fj7Nnl3w7XcuTqSKfrYIjGsF/3J912xQm6DWyzYfVOG', 'aluno'),
(42, 'Gabriele Silva', 'gabriele@gmail.com', '$2y$10$Xnah/ViXEjghBvqJCu6d.uMNoLO.HnEXRBkoM82bAQlXl1GYIv6Fi', 'aluno'),
(43, 'Ronaldo Silva', 'ronaldo@gmail.com', '$2y$10$Etpsl4d2r.eb5TmvQULXYeVkKdkUYgMsS80ERbl9qDy/VAI78xDa.', 'aluno'),
(44, 'Ronald Golias', 'ronald@gmail.com', '$2y$10$IqrcfL/IAWacK4lUB3lar.e2yD.ycjiPmqLN8yJKpSWyUHx5h3auW', 'aluno'),
(45, 'Mirela Silva', 'mirela@gmail.com', '$2y$10$sK2Z3VU.JdjBkDuqWeKKReBnSYA97j2e5hXtMi6QDPPTNh5eSrTFe', 'aluno'),
(46, 'Neymar Silva', 'neymar@gmail.com', '$2y$10$USOpgDJY3aZJymnWjQAA8efXlPgm1yt1wpNAtdNxFlYt8jBbP8hWe', 'aluno'),
(47, 'Monica Santos', 'monica@gmail.com', '$2y$10$rDMqBgoafQeMzD8q4xBud.tKKoVybRcYKZH5Q2EiRiuIPA7zjkmDO', 'aluno'),
(48, 'Miguel Costa', 'miguel@gmail.com', '$2y$10$I.mZ3KeiiVMNsBfAGKLaTuvEr.PRwb9o.4d3KlHWhODSMulym8ste', 'aluno'),
(49, 'Henrique Silva', 'henrique@gmail.com', '$2y$10$1ECWWgD13DZTfQ4BBv5gnuOv4l2v9bllEMwkh2XSbhmb1ZK9l0c0e', 'aluno'),
(50, 'Natalia Ferreira', 'natalia@gmail.com', '$2y$10$8rV6yDHa2roKbFYML566j.uxbG5flLrTNE6wHQs8dXt1rnXJxR69q', 'aluno'),
(51, 'Josias Silva', 'josia@gmail.com', '$2y$10$0IFM2lN26zbj6W68X/TxUuW7Z4Tc0.Rj5vEkM75hU7AJLXGiqkAKW', 'aluno'),
(52, 'Admin', 'admin@access.com', '$2y$10$HQay3x4dl.rvXDTXAwIKRe0az1zcOXmel.zJrnADNe1I8MktR042C', 'coordenador'),
(56, 'Guerino Pazzzzzz', 'guerino@gmail.com', '$2y$10$gtBpiWyEqr.zUgX1FIwDnOqBC3P12XrYv4V9CdEgNAHmGEeRGGcZm', 'aluno'),
(57, 'Julia Mendes', 'julia@gmail.com', '$2y$10$uhEW.EShiqkKEXdf5wWaWOs81AxDtPI28XZh8E339VGgcOB/rUzjK', 'aluno'),
(58, 'Nilton Barros', 'nilton@access.gmail', 'e10adc3949ba59abbe56e057f20f883e', 'instrutor'),
(59, 'Pepe Silva', 'pepe@gmail.comm', '$2y$10$BZLT.NLU53Pk3AzAJz70eOtMsWZTrRjr/zfGXCDE3pq0SfrbMkYzu', 'aluno'),
(60, 'Bianca Silva', 'bianca@gmail.com', '$2y$10$4DBnO7TYc1pJk3eXNGMsquDgrS1yJ0vSS1JB8H9V/ojaLm34qzaUq', 'aluno'),
(61, 'Felipa Silva Costa', 'felipa@gmail.commm', '$2y$10$hUzBjAq307YJia6gXhe36.HItsRF2sLlZJOupHKxcqoV0fDWM.9ou', 'aluno'),
(62, 'Pepita Costa', 'pepita@gmail.com', '$2y$10$pi3OWwg3A2YLJKSjrs2KxO7q.9BBXzzQHQ6sHCwFlOnV2a2gZi.Y.', 'aluno'),
(64, 'Lindomar Ronaldo', 'lindomar@access.gmail', '$2y$10$Umb0ZcdSieZ.elFPalKM8uShBTXdFa9LH3pX0AuGTZ0xzTQxOWRxS', 'instrutor'),
(65, 'Carlinha López', 'carlinha@access.gmail', '$2y$10$Z.r3b/u9nf2LUI1SxXz/QOIU/8PwuM/hs19vsEq/l21mGzyIjakby', 'instrutor'),
(70, 'Rodrigo Garro', 'rodrigo@accessfit.gmail', '$2y$10$bTxcJCvF4xm8M4N1WJ6Q4OMiC6sjzQ8vOG075h.zTqzPpsRv5JSmS', 'coordenador'),
(73, 'Felipe Crespo', 'felipe@access.comm', 'e10adc3949ba59abbe56e057f20f883e', 'instrutor'),
(74, 'Cássio Ramoss', 'cassio@gmail.commmmmm', '$2y$10$cXMGXpgiinD374AlJjzzj./wyEfDHkTCQDVifqXoXkn2/UB7nFUI.', 'aluno'),
(75, 'Marcello Pazzzzzzzzzzzzzzzzzzzzz', 'marcello@gmail.com', '$2y$10$mzlggdDZIVGiYIY9qRP8PevZaBOM0Zf9EyZIJKJAholTLzVNPQjZe', 'aluno'),
(76, 'Amauri Costa', 'amauri@access.comm', '$2y$10$d5gBOK1K/Qbj2jOHAjvGTuJUxC6jug0oeZuOq583nCj.ImiZsE6di', 'instrutor'),
(77, 'Hacker Secreto', 'hacker@accessfit.com', '$2y$10$zr6aGMJ37JGEjoHOSycc5.aF1if5ASY2GNZBrpyGvTWHkp4iU.VnS', 'coordenador'),
(78, 'Nicoas Freitas', 'nicolas@access.com', 'e10adc3949ba59abbe56e057f20f883e', 'instrutor'),
(79, 'Kaka  da Silva Camps ', 'kaka@gmail.com', '$2y$10$P2qOxkbEsHRcYEDF/tMuzuMjSKog7Zd38i39/rdu.AWKoIgyX09UC', 'aluno'),
(80, 'kaua', 'mendes@gmail.com', '$2y$10$QJFA3.FutBPLXnGMULq.duqiCIMbD8wDIQLkIvZD1Ulk3k1mwxHIy', 'aluno'),
(81, 'junior silva', 'junior@access.com', '$2y$10$1FP0AY9tYp20agNFurBBIuolvYoKQjMXUkdTZftMW4a.JLkfV9MEq', 'instrutor'),
(82, 'Felipe Melo', 'felipe@accessfit.com', '$2y$10$UJTBhu9Tyd9Tt0ap8BnKVOYxqkIBoTD/PiKrvyk7yCR4ZBjj59qSW', 'coordenador'),
(83, 'Udson Freitas', 'udson@access.com', 'e10adc3949ba59abbe56e057f20f883e', 'instrutor'),
(84, 'Ingrid Alves', 'ingrid@gmail.com', '$2y$10$fEdBOx.vHieYIdX.cRlpLe5VtLCmNP1XchpubGUqQTj6/VrOr63Ju', 'aluno');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`),
  ADD KEY `turma_id` (`turma_id`);

--
-- Índices de tabela `alunos`
--
ALTER TABLE `alunos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD UNIQUE KEY `unique_cpf` (`cpf`),
  ADD KEY `fk_usuario_aluno` (`usuario_id`),
  ADD KEY `fk_instrutor` (`instrutor_id`);

--
-- Índices de tabela `avaliacoes_fisicas`
--
ALTER TABLE `avaliacoes_fisicas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`),
  ADD KEY `instrutor_id` (`instrutor_id`);

--
-- Índices de tabela `coordenadores`
--
ALTER TABLE `coordenadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `unique_telefone_coord` (`telefone`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `fichas_treino`
--
ALTER TABLE `fichas_treino`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`),
  ADD KEY `instrutor_id` (`instrutor_id`);

--
-- Índices de tabela `instrutores`
--
ALTER TABLE `instrutores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cref` (`cref`),
  ADD KEY `fk_usuario_instrutor` (`usuario_id`);

--
-- Índices de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`),
  ADD KEY `plano_id` (`plano_id`);

--
-- Índices de tabela `planos`
--
ALTER TABLE `planos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `presencas`
--
ALTER TABLE `presencas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `aluno_id` (`aluno_id`),
  ADD KEY `turma_id` (`turma_id`);

--
-- Índices de tabela `turmas`
--
ALTER TABLE `turmas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_instrutor_turma` (`instrutor_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `alunos`
--
ALTER TABLE `alunos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `avaliacoes_fisicas`
--
ALTER TABLE `avaliacoes_fisicas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `coordenadores`
--
ALTER TABLE `coordenadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `fichas_treino`
--
ALTER TABLE `fichas_treino`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `instrutores`
--
ALTER TABLE `instrutores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de tabela `planos`
--
ALTER TABLE `planos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `presencas`
--
ALTER TABLE `presencas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `turmas`
--
ALTER TABLE `turmas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`),
  ADD CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`);

--
-- Restrições para tabelas `alunos`
--
ALTER TABLE `alunos`
  ADD CONSTRAINT `alunos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_instrutor` FOREIGN KEY (`instrutor_id`) REFERENCES `instrutores` (`id`),
  ADD CONSTRAINT `fk_usuario_aluno` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `avaliacoes_fisicas`
--
ALTER TABLE `avaliacoes_fisicas`
  ADD CONSTRAINT `avaliacoes_fisicas_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`),
  ADD CONSTRAINT `avaliacoes_fisicas_ibfk_2` FOREIGN KEY (`instrutor_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `coordenadores`
--
ALTER TABLE `coordenadores`
  ADD CONSTRAINT `coordenadores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `fichas_treino`
--
ALTER TABLE `fichas_treino`
  ADD CONSTRAINT `fichas_treino_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`),
  ADD CONSTRAINT `fichas_treino_ibfk_2` FOREIGN KEY (`instrutor_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `instrutores`
--
ALTER TABLE `instrutores`
  ADD CONSTRAINT `fk_usuario_instrutor` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `pagamentos_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`),
  ADD CONSTRAINT `pagamentos_ibfk_2` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`);

--
-- Restrições para tabelas `presencas`
--
ALTER TABLE `presencas`
  ADD CONSTRAINT `presencas_ibfk_1` FOREIGN KEY (`aluno_id`) REFERENCES `alunos` (`id`),
  ADD CONSTRAINT `presencas_ibfk_2` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`);

--
-- Restrições para tabelas `turmas`
--
ALTER TABLE `turmas`
  ADD CONSTRAINT `fk_instrutor_turma` FOREIGN KEY (`instrutor_id`) REFERENCES `instrutores` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
