-- --------------------------------------------------------
-- Servidor:                     127.0.0.1
-- Versão do servidor:           10.3.16-MariaDB - mariadb.org binary distribution
-- OS do Servidor:               Win64
-- HeidiSQL Versão:              10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Copiando estrutura do banco de dados para bandmaker
CREATE DATABASE IF NOT EXISTS `bandmaker` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `bandmaker`;

-- Copiando estrutura para tabela bandmaker.dado_login
CREATE TABLE IF NOT EXISTS `dado_login` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome_usuario` varchar(50) NOT NULL,
  `senha` varchar(50) NOT NULL,
  `pessoa_id` int(10) unsigned DEFAULT NULL,
  `banda_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `normal_idFK` (`pessoa_id`),
  KEY `banda_idFK` (`banda_id`),
  CONSTRAINT `banda_idFK` FOREIGN KEY (`banda_id`) REFERENCES `perfil_banda` (`id`),
  CONSTRAINT `normal_idFK` FOREIGN KEY (`pessoa_id`) REFERENCES `perfil_pessoa` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela bandmaker.evento
CREATE TABLE IF NOT EXISTS `evento` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `data` datetime NOT NULL,
  `preco` decimal(5,2) NOT NULL,
  `banda` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela bandmaker.perfil_banda
CREATE TABLE IF NOT EXISTS `perfil_banda` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nome_usuario` varchar(45) NOT NULL,
  `cidade` varchar(30) NOT NULL,
  `estado` varchar(30) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `influencias` varchar(100) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `cep` int(11) NOT NULL,
  `email` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela bandmaker.perfil_banda_has_evento
CREATE TABLE IF NOT EXISTS `perfil_banda_has_evento` (
  `perfil_banda_has_evento_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `perfil_banda_id` int(11) unsigned NOT NULL,
  `evento_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`perfil_banda_has_evento_id`),
  KEY `perfildabandafkk_idx` (`perfil_banda_id`),
  KEY `eventosfkk_idx` (`evento_id`),
  CONSTRAINT `eventosfkk` FOREIGN KEY (`evento_id`) REFERENCES `evento` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `perfildabandafkk` FOREIGN KEY (`perfil_banda_id`) REFERENCES `perfil_banda` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.

-- Copiando estrutura para tabela bandmaker.perfil_pessoa
CREATE TABLE IF NOT EXISTS `perfil_pessoa` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nome_usuario` varchar(24) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `sobrenome` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `idade` int(4) unsigned NOT NULL,
  `cidade` varchar(45) NOT NULL,
  `estado` varchar(45) NOT NULL,
  `instrumento` varchar(30) NOT NULL,
  `tempo` varchar(30) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `influencia` varchar(255) DEFAULT NULL,
  `banda_id` int(11) unsigned DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bandaidfk_idx` (`banda_id`),
  CONSTRAINT `bandaidfk` FOREIGN KEY (`banda_id`) REFERENCES `perfil_banda` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- Exportação de dados foi desmarcado.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
