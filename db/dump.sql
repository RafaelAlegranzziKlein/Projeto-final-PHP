-- --------------------------------------------------------
-- Script SQL para o Trabalho Final: Portal de Notícias
-- --------------------------------------------------------

-- 1. Criação do Banco de Dados (caso não exista)
CREATE DATABASE IF NOT EXISTS `portal_noticias` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `portal_noticias`;

-- --------------------------------------------------------
-- 2. Estrutura da tabela `usuarios`
-- --------------------------------------------------------
-- Nota: Apagamos a tabela notícias primeiro se ela existir por causa da chave estrangeira
DROP TABLE IF EXISTS `noticias`;
DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `nome` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `senha` VARCHAR(255) NOT NULL, -- Tamanho 255 é ideal para usar password_hash() do PHP
  `foto_perfil` VARCHAR(255) DEFAULT NULL, -- Guarda o caminho da imagem (ex: assets/imagens/perfil/foto.jpg)
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email_unico` (`email`) -- Garante que não existam dois usuários com o mesmo e-mail
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Estrutura da tabela `noticias`
-- --------------------------------------------------------
CREATE TABLE `noticias` (
  `id` INT AUTO_INCREMENT NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `noticia` TEXT NOT NULL,
  `data` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Salva a data/hora atual automaticamente
  `autor` INT NOT NULL, -- ID do usuário que criou
  `imagem` VARCHAR(255) DEFAULT NULL, -- Guarda o caminho da imagem da notícia
  PRIMARY KEY (`id`),
  KEY `fk_autor_idx` (`autor`),
  -- Restrição de chave estrangeira: se o usuário for deletado, suas notícias também serão (ON DELETE CASCADE)
  CONSTRAINT `fk_noticias_usuarios` 
    FOREIGN KEY (`autor`) 
    REFERENCES `usuarios` (`id`) 
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Dados de Teste (Inserções Iniciais Opcionais)
-- --------------------------------------------------------
-- Criando um usuário padrão. A senha inserida é "123456" criptografada com a função password_hash() do PHP
INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `foto_perfil`) VALUES
(1, 'Carlos Jornalista', 'carlos@email.com', '$2y$10$wK1FvK8u2kUeL8XzR5D1OuZ9gH4mY6pYxHqVb9eFzB3jG2k5c4y5.', 'assets/imagens/perfil/default.png');

-- Criando duas notícias de exemplo vinculadas ao Carlos (autor = 1)
INSERT INTO `noticias` (`id`, `titulo`, `noticia`, `autor`, `imagem`) VALUES
(1, 'Lançamento do Novo Portal de Notícias', 'Hoje marca o início do nosso portal desenvolvido em PHP. O sistema conta com controle de acesso, upload de imagens e um painel exclusivo para os jornalistas gerenciarem suas publicações.', 1, 'assets/imagens/noticias/post1.jpg'),
(2, 'Tecnologia na Educação Superior', 'O uso de sistemas web dinâmicos tem revolucionado a entrega de trabalhos acadêmicos nas universidades, permitindo que alunos apliquem conceitos práticos de banco de dados e programação.', 1, 'assets/imagens/noticias/post2.jpg');