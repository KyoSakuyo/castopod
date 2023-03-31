---
title: Imatges Docker oficials
sidebarDepth: 3
---

# Imatges Docker oficials

Castopod pushes 3 Docker images to the Docker Hub during its automated build
process:

- [**`castopod/castopod`**](https://hub.docker.com/r/castopod/castopod): an all
  in one castopod image using nginx unit
- [**castopod/app**](https://hub.docker.com/r/castopod/app): el paquet incloent
  Castopod i totes les dependències
- [**`castopod/web-server`**](https://hub.docker.com/r/castopod/web-server): una
  configuració de Nginx per a Castopod
- [**`castopod/video-clipper`**](https://hub.docker.com/r/castopod/video-clipper):
  an optional image building videoclips thanks to ffmpeg

A més, Castopod requereix una base de dades compatible amb MySQL. Es pot afegir
una base de dades Redis com a gestor de memòria cau.

## Etiquetes compatibles

- `develop` [no-estable], darrera versió de la branca de desenvolupament
- `beta` [stable], latest beta version build
- `1.0.0-beta.x` [stable], specific beta version build (since `1.0.0-beta.22`)
- `latest` [stable], latest version build
- `1.x.x` [stable], specific version build (since `1.0.0`)

## Exemple d'ús

1.  Instal·leu [docker](https://docs.docker.com/get-docker/) i
    [docker-compose](https://docs.docker.com/compose/install/)
2.  Creeu un fitxer `docker-compose.yml` amb el següent:

    ```yml
    version: "3.7"

    services:
      app:
        image: castopod/app:latest
        container_name: "castopod-app"
        volumes:
          - castopod-media:/opt/castopod/public/media
        environment:
          MYSQL_DATABASE: castopod
          MYSQL_USER: castopod
          MYSQL_PASSWORD: changeme
          CP_BASEURL: "http://castopod.example.com"
          CP_ANALYTICS_SALT: changeme
          CP_CACHE_HANDLER: redis
          CP_REDIS_HOST: redis
        networks:
          - castopod-app
          - castopod-db
        restart: unless-stopped

      web-server:
        image: castopod/web-server:latest
        container_name: "castopod-web-server"
        volumes:
          - castopod-media:/var/www/html/media
        networks:
          - castopod-app
        ports:
          - 8080:80
        restart: unless-stopped

      mariadb:
        image: mariadb:10.5
        container_name: "castopod-mariadb"
        networks:
          - castopod-db
        volumes:
          - castopod-db:/var/lib/mysql
        environment:
          MYSQL_ROOT_PASSWORD: changeme
          MYSQL_DATABASE: castopod
          MYSQL_USER: castopod
          MYSQL_PASSWORD: changeme
        restart: unless-stopped

      redis:
        image: redis:7.0-alpine
        container_name: "castopod-redis"
        volumes:
          - castopod-cache:/data
        networks:
          - castopod-app

      # this container is optional
      # add this if you want to use the videoclips feature
      video-clipper:
        image: castopod/video-clipper:latest
        container_name: "castopod-video-clipper"
        volumes:
          - castopod-media:/opt/castopod/public/media
        environment:
          MYSQL_DATABASE: castopod
          MYSQL_USER: castopod
          MYSQL_PASSWORD: changeme
        networks:
          - castopod-db
        restart: unless-stopped

    volumes:
      castopod-media:
      castopod-db:
      castopod-cache:

    networks:
      castopod-app:
      castopod-db:
    ```

    Heu d'adaptar algunes variables a les vostres necessitats (per exemple,
    `CP_BASEURL`, `MYSQL_ROOT_PASSWORD`, `MYSQL_PASSWORD` i
    `CP_ANALYTICS_SALT`).

3.  Configureu un `reverse proxy` per a TLS (SSL/HTTPS)

    TLS és obligatori perquè ActivityPub funcioni. Aquest feina es pot gestionar
    fàcilment amb un `reverse proxy`, per exemple amb
    [Caddy](https://caddyserver.com/):

    ```
    #castopod
    castopod.exemple.com {
        reverse_proxy localhost:8080
    }
    ```

4.  Executeu `docker-compose up -d`, espereu que s'inicialitzi i aneu a
    `https://castopod.exemple.com/cp-install` per acabar de configurar Castopod!

5.  Ja esteu a punt, podeu començar a fer podcasts! 🎙️🚀

## Variables d'entorn

- **castopod/video-clipper**

  | Nom de la variable         | Tipus (`default`) | Default          |
  | -------------------------- | ----------------- | ---------------- |
  | **`CP_DATABASE_HOSTNAME`** | ?string           | `"mariadb"`      |
  | **`CP_DATABASE_NAME`**     | ?string           | `MYSQL_DATABASE` |
  | **`CP_DATABASE_USERNAME`** | ?string           | `MYSQL_USER`     |
  | **`CP_DATABASE_PASSWORD`** | ?string           | `MYSQL_PASSWORD` |
  | **`CP_DATABASE_PREFIX`**   | ?string           | `"cp_"`          |

- **castopod/app**

  | Nom de la variable           | Type (`default`)        | Default          |
  | ---------------------------- | ----------------------- | ---------------- |
  | **`CP_BASEURL`**             | string                  | `undefined`      |
  | **`CP_MEDIA_BASEURL`**       | ?string                 | `CP_BASEURL`     |
  | **`CP_ADMIN_GATEWAY`**       | ?string                 | `"cp-admin"`     |
  | **`CP_AUTH_GATEWAY`**        | ?string                 | `"cp-auth"`      |
  | **`CP_ANALYTICS_SALT`**      | string                  | `undefined`      |
  | **`CP_DATABASE_HOSTNAME`**   | ?string                 | `"mariadb"`      |
  | **`CP_DATABASE_NAME`**       | ?string                 | `MYSQL_DATABASE` |
  | **`CP_DATABASE_USERNAME`**   | ?string                 | `MYSQL_USER`     |
  | **`CP_DATABASE_PASSWORD`**   | ?string                 | `MYSQL_PASSWORD` |
  | **`CP_DATABASE_PREFIX`**     | ?string                 | `"cp_"`          |
  | **`CP_CACHE_HANDLER`**       | [`"file"` or `"redis"`] | `"file"`         |
  | **`CP_REDIS_HOST`**          | ?string                 | `"localhost"`    |
  | **`CP_REDIS_PASSWORD`**      | ?string                 | `null`           |
  | **`CP_REDIS_PORT`**          | ?number                 | `6379`           |
  | **`CP_REDIS_DATABASE`**      | ?number                 | `0`              |
  | **`CP_EMAIL_SMTP_HOST`**     | ?string                 | `undefined`      |
  | **`CP_EMAIL_FROM`**          | ?string                 | `undefined`      |
  | **`CP_EMAIL_SMTP_USERNAME`** | ?string                 | `"localhost"`    |
  | **`CP_EMAIL_SMTP_PASSWORD`** | ?string                 | `null`           |
  | **`CP_EMAIL_SMTP_PORT`**     | ?number                 | `25`             |
  | **`CP_EMAIL_SMTP_CRYPTO`**   | [`"tls"` or `"ssl"`]    | `"tls"`          |

- **castopod/web-server**

  | Variable name         | Type    | Default |
  | --------------------- | ------- | ------- |
  | **`CP_APP_HOSTNAME`** | ?string | `"app"` |
