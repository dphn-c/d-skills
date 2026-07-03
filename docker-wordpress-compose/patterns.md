# WordPress Compose Patterns Reference

Patterns live under `$STACKS_ROOT` (docker-compose-boiler repo).

## Overview

| Folder | Web server | Database | WP image | Theme mount |
|--------|------------|----------|----------|-------------|
| `nginx-wp-mariadb-wpcli` | Nginx 1.27 + PHP-FPM | MariaDB | `wordpress:6.7.1-php8.3-fpm-alpine` | `./wp/wp-content/themes` |
| `apache-wp-mysql-wpcli` | Apache (bundled) | MariaDB* | `wordpress:6.7.1-php8.3` | `./themes` |
| `nginx-wp-mysql-wpcli` | Nginx 1.27 + PHP-FPM | MySQL 5.7 (`linux/x86_64`) | `wordpress:6.7.1-php8.3-fpm-alpine` | `./themes` |
| `nginx-wp-mariadb-wpcli-dockersyncs` | Nginx 1.27 + PHP-FPM | MariaDB | `wordpress:6.7.1-php8.3-fpm-alpine` | `./themes` via docker-sync volume |

\* Folder name says `mysql` but `docker-compose.yml` uses the `mariadb` image.

## Services (all patterns)

- **database** — MariaDB or MySQL with UTF-8 charset
- **wordpress** — WordPress application
- **cli** — `wordpress:cli` for WP-CLI
- **nginx** — nginx patterns only; apache pattern exposes port 80 on wordpress directly

## nginx-wp-mariadb-wpcli (default)

```
$STACKS_ROOT/nginx-wp-mariadb-wpcli/
├── docker-compose.yml
├── .env
├── php/php.ini
├── nginx/
└── wp/wp-content/themes/   ← symlink external themes here
```

## apache-wp-mysql-wpcli

```
$STACKS_ROOT/apache-wp-mysql-wpcli/
├── docker-compose.yml
├── .env
├── .htaccess
├── php/php.ini
└── themes/
```

## nginx-wp-mysql-wpcli

```
$STACKS_ROOT/nginx-wp-mysql-wpcli/
├── docker-compose.yml
├── .env
├── php/php.ini
├── nginx/
└── themes/
```

## nginx-wp-mariadb-wpcli-dockersyncs

For macOS when bind mounts are slow. Requires docker-sync, unison, fswatch.

```
$STACKS_ROOT/nginx-wp-mariadb-wpcli-dockersyncs/
├── docker-compose.yml
├── docker-sync.yml
├── .env
├── php/php.ini
├── nginx/
└── themes/                 ← docker-sync watches this directory
```

Workflow: `docker-sync start` → `docker compose up -d`
