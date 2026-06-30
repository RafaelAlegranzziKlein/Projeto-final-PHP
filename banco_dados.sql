-- ============================================================
-- banco_dados.sql  –  Portal de Notícias "Folha Digital"
-- Execute no phpMyAdmin ou via linha de comando MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS portal_noticias
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE portal_noticias;

-- ── Tabela de usuários ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS usuarios (
  id          INT          NOT NULL AUTO_INCREMENT,
  nome        VARCHAR(150) NOT NULL,
  email       VARCHAR(200) NOT NULL UNIQUE,
  senha       VARCHAR(255) NOT NULL,
  foto_perfil VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Tabela de notícias ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS noticias (
  id      INT          NOT NULL AUTO_INCREMENT,
  titulo  VARCHAR(300) NOT NULL,
  noticia TEXT         NOT NULL,
  data    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  autor   INT          NOT NULL,
  imagem  VARCHAR(300) DEFAULT NULL,
  PRIMARY KEY (id),
  CONSTRAINT fk_noticia_autor FOREIGN KEY (autor) REFERENCES usuarios(id)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Tabela de comentários ───────────────────────────────────
CREATE TABLE IF NOT EXISTS comentarios (
  id         INT      NOT NULL AUTO_INCREMENT,
  noticia_id INT      NOT NULL,
  usuario_id INT      NOT NULL,
  texto      TEXT     NOT NULL,
  data       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  CONSTRAINT fk_com_noticia  FOREIGN KEY (noticia_id) REFERENCES noticias(id)  ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_com_usuario  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Tabela de likes por notícia ─────────────────────────────
-- Um usuário só pode dar um like por notícia (UNIQUE key garante isso)
CREATE TABLE IF NOT EXISTS likes_noticia (
  id         INT      NOT NULL AUTO_INCREMENT,
  noticia_id INT      NOT NULL,
  usuario_id INT      NOT NULL,
  data       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_like_por_usuario (noticia_id, usuario_id),
  CONSTRAINT fk_like_noticia  FOREIGN KEY (noticia_id) REFERENCES noticias(id)  ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_like_usuario  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
