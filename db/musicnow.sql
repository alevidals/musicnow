------------------------------
-- Archivo de base de datos --
------------------------------

DROP TABLE IF EXISTS roles CASCADE;

CREATE TABLE roles (
  id BIGSERIAL PRIMARY KEY,
  rol VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS usuarios CASCADE;

CREATE TABLE usuarios
(
    id            BIGSERIAL     PRIMARY KEY
  , login         VARCHAR(50)   NOT NULL UNIQUE
  , nombre        VARCHAR(255)  NOT NULL
  , apellidos     VARCHAR(255)  NOT NULL
  , email         VARCHAR(255)  NOT NULL UNIQUE
  , password      VARCHAR(255)  NOT NULL
  , fnac          DATE
  , rol           BIGINT        NOT NULL REFERENCES roles (id) DEFAULT 2
  , auth_key      VARCHAR(255)
  , confirm_token VARCHAR(255)
  , url_image     VARCHAR(2048)
  , image_name    VARCHAR(255)
  , created_at    TIMESTAMP(0)  NOT NULL DEFAULT CURRENT_TIMESTAMP
  , deleted_at    TIMESTAMP(0)
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
  , created_at TIMESTAMP(0)  NOT NULL DEFAULT CURRENT_TIMESTAMP
  , usuario_id BIGINT        NOT NULL REFERENCES usuarios (id)
);

DROP TABLE IF EXISTS canciones CASCADE;

CREATE TABLE canciones
(
    id           BIGSERIAL     PRIMARY KEY
  , titulo       VARCHAR(255)  NOT NULL
  , album_id     BIGINT        NOT NULL REFERENCES albumes (id)
  , genero_id    BIGINT        NOT NULL REFERENCES generos (id)
  , url_cancion  VARCHAR(2048) NOT NULL
  , song_name    VARCHAR(255)  NOT NULL
  , image_name   VARCHAR(255)  NOT NULL
  , url_portada  VARCHAR(2048) NOT NULL
  , anyo         NUMERIC(4)    NOT NULL
  , duracion     INTERVAL      NOT NULL
  , usuario_id   BIGINT        NOT NULL REFERENCES usuarios (id)
  , created_at   TIMESTAMP(0)  NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS albumes_canciones CASCADE;

CREATE TABLE albumes_canciones
(
    id           BIGSERIAL PRIMARY KEY
  , album_id     BIGINT    NOT NULL REFERENCES albumes (id)
  , canciones_id BIGINT    NOT NULL REFERENCES canciones (id)
);

DROP TABLE IF EXISTS seguidores CASCADE;

CREATE TABLE seguidores (
    seguidor_id BIGINT REFERENCES usuarios (id)
  , seguido_id BIGINT REFERENCES usuarios (id)
  , PRIMARY KEY (seguidor_id, seguido_id)
);

INSERT INTO roles (rol)
VALUES ('admin')
     , ('usuario');

INSERT INTO usuarios (login, nombre, apellidos, email, password, fnac, rol)
VALUES ('admin', 'admin', 'admin', 'admin@admin.com', crypt('pepe', gen_salt('bf', 10)), '1999-12-01', 1),
       ('usuario1', 'usuario1', 'usuario1', 'usuario1@usuario.com', crypt('pepe', gen_salt('bf', 10)), '1999-12-01', 1),
       ('usuario2', 'usuario2', 'usuario2', 'usuario2@usuario.com', crypt('pepe', gen_salt('bf', 10)), '1999-12-01', 1);