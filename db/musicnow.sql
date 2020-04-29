------------------------------
-- Archivo de base de datos --
------------------------------

DROP TABLE IF EXISTS roles CASCADE;

CREATE TABLE roles
(
    id BIGSERIAL PRIMARY KEY
  , rol VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS estados CASCADE;

CREATE TABLE estados
(
    id     BIGSERIAL    PRIMARY KEY
  , estado VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS usuarios CASCADE;

CREATE TABLE usuarios
(
    id               BIGSERIAL     PRIMARY KEY
  , login            VARCHAR(50)   NOT NULL UNIQUE
  , nombre           VARCHAR(255)  NOT NULL
  , apellidos        VARCHAR(255)  NOT NULL
  , email            VARCHAR(255)  NOT NULL UNIQUE
  , password         VARCHAR(255)  NOT NULL
  , fnac             DATE
  , rol_id           BIGINT        NOT NULL REFERENCES roles   (id) DEFAULT 2
  , estado_id        BIGINT        NOT NULL REFERENCES estados (id) DEFAULT 1
  , auth_key         VARCHAR(255)
  , confirm_token    VARCHAR(255)
  , url_image        VARCHAR(2048)
  , image_name       VARCHAR(255)
  , url_banner       VARCHAR(2048)
  , banner_name      VARCHAR(255)
  , created_at       TIMESTAMP(0)  NOT NULL DEFAULT CURRENT_TIMESTAMP
  , deleted_at       TIMESTAMP(0)
  , privated_account BOOLEAN       NOT NULL DEFAULT FALSE
);

DROP TABLE IF EXISTS generos CASCADE;

CREATE TABLE generos
(
    id           BIGSERIAL    PRIMARY KEY
  , denominacion VARCHAR(255) NOT NULL UNIQUE
  , created_at   TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS albumes CASCADE;

CREATE TABLE albumes
(
    id         BIGSERIAL     PRIMARY KEY
  , titulo     VARCHAR(255)  NOT NULL
  , anyo       NUMERIC(4)    NOT NULL
  , image_name VARCHAR(255) NOT NULL
  , url_portada VARCHAR(2048) NOT NULL
  , created_at TIMESTAMP(0)  NOT NULL DEFAULT CURRENT_TIMESTAMP
  , usuario_id BIGINT        NOT NULL REFERENCES usuarios (id)  ON DELETE CASCADE
);

DROP TABLE IF EXISTS canciones CASCADE;

CREATE TABLE canciones
(
    id           BIGSERIAL     PRIMARY KEY
  , titulo       VARCHAR(255)  NOT NULL
  , album_id     BIGINT                 REFERENCES albumes (id)
  , genero_id    BIGINT        NOT NULL REFERENCES generos (id)
  , url_cancion  VARCHAR(2048) NOT NULL
  , song_name    VARCHAR(255)  NOT NULL
  , image_name   VARCHAR(255)  NOT NULL
  , url_portada  VARCHAR(2048) NOT NULL
  , anyo         NUMERIC(4)    NOT NULL
  , duracion     INTERVAL      NOT NULL
  , explicit     BOOLEAN       NOT NULL
  , usuario_id   BIGINT        NOT NULL REFERENCES usuarios (id) ON DELETE CASCADE
  , created_at   TIMESTAMP(0)  NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS albumes_canciones CASCADE;

CREATE TABLE albumes_canciones
(
  album_id     BIGINT    NOT NULL REFERENCES albumes (id)
  , canciones_id BIGINT    NOT NULL REFERENCES canciones (id)
  , PRIMARY KEY (album_id, canciones_id)
);

DROP TABLE IF EXISTS seguidores CASCADE;

CREATE TABLE seguidores
(
     id          BIGSERIAL PRIMARY KEY
  ,  seguidor_id BIGINT NOT NULL REFERENCES usuarios (id)
  , seguido_id   BIGINT NOT NULL REFERENCES usuarios (id)
  , UNIQUE (seguidor_id, seguido_id)
);

DROP TABLE IF EXISTS likes CASCADE;

CREATE TABLE likes
(
    usuario_id BIGINT REFERENCES usuarios (id)  ON DELETE CASCADE
  , cancion_id BIGINT REFERENCES canciones (id) ON DELETE CASCADE
  , PRIMARY KEY (usuario_id, cancion_id)
);

DROP TABLE IF EXISTS comentarios CASCADE;

CREATE TABLE comentarios
(
    id         BIGSERIAL    PRIMARY KEY
  , usuario_id BIGINT       NOT NULL REFERENCES usuarios (id)  ON DELETE CASCADE
  , cancion_id BIGINT       NOT NULL REFERENCES canciones (id) ON DELETE CASCADE
  , comentario VARCHAR(255) NOT NULL
  , created_at    TIMESTAMP(0)  NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS chat CASCADE;

CREATE TABLE chat
(
    id            BIGSERIAL    PRIMARY KEY
  , emisor_id     BIGINT       NOT NULL REFERENCES usuarios (id) ON DELETE CASCADE
  , receptor_id   BIGINT       NOT NULL REFERENCES usuarios (id) ON DELETE CASCADE
  , mensaje       TEXT         NOT NULL
  , estado_id     BIGINT       NOT NULL REFERENCES estados  (id) DEFAULT 3
  , created_at    TIMESTAMP(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS bloqueados CASCADE;

CREATE TABLE bloqueados
(
    bloqueador_id BIGINT REFERENCES usuarios (id)
  , bloqueado_id  BIGINT REFERENCES usuarios (id)
  , PRIMARY KEY (bloqueador_id, bloqueado_id)
);

DROP TABLE IF EXISTS playlists CASCADE;

CREATE TABLE playlists
(
    id         BIGSERIAL    PRIMARY KEY
  , usuario_id BIGINT       NOT NULL REFERENCES usuarios (id) ON DELETE CASCADE
  , titulo     VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS canciones_playlist;

CREATE TABLE canciones_playlist
(
    playlist_id BIGINT NOT NULL REFERENCES playlists (id) ON DELETE CASCADE
  , cancion_id  BIGINT NOT NULL REFERENCES canciones (id) ON DELETE CASCADE
  , PRIMARY KEY (playlist_id, cancion_id)
);

DROP TABLE IF EXISTS videoclips;

CREATE TABLE videoclips
(
    id         BIGSERIAL    PRIMARY KEY
  , usuario_id BIGINT       NOT NULL REFERENCES usuarios (id) ON DELETE CASCADE
  , link       VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS solicitudes_seguimiento;

CREATE TABLE solicitudes_seguimiento
(
    seguidor_id BIGINT NOT NULL REFERENCES usuarios (id)
  , seguido_id  BIGINT NOT NULL REFERENCES usuarios (id)
  , PRIMARY KEY (seguidor_id, seguido_id)
);

INSERT INTO roles (rol)
VALUES ('admin')
     , ('usuario');

INSERT INTO estados (estado)
VALUES ('offline')
     , ('online')
     , ('no-read')
     , ('read');

INSERT INTO generos(denominacion)
VALUES ('Pop'),
       ('Rap'),
       ('Flamenco');

INSERT INTO usuarios (login, nombre, apellidos, email, password, fnac, rol_id)
VALUES ('admin', 'admin', 'admin', 'admin@admin.com', crypt('pepe', gen_salt('bf', 10)), '1999-12-01', 1),
       ('usuario1', 'usuario1', 'usuario1', 'usuario1@usuario.com', crypt('pepe', gen_salt('bf', 10)), '1999-12-01', 2),
       ('usuario2', 'usuario2', 'usuario2', 'usuario2@usuario.com', crypt('pepe', gen_salt('bf', 10)), '1999-12-01', 2);