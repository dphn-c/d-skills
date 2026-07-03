---
name: docker-wordpress-compose
description: >-
  Select and launch a WordPress Docker Compose stack for local development.
  Use when the user asks to start WordPress with Docker, run a local WP
  environment, or says phrases like "dockerでwordpressを立ち上げて",
  "WordPressをDockerで起動", "WP環境を立ち上げて", or mentions nginx/apache,
  MariaDB/MySQL, docker-sync stacks.
---

# Docker WordPress Compose

Launch WordPress using compose patterns from **docker-compose-boiler**. Works from any workspace (theme projects, plugins, or the boiler repo itself).

## Where stacks live

Compose patterns are **not** bundled in this skill. They live in the docker-compose-boiler repository (single source of truth — update stacks there only).

Resolve `STACKS_ROOT` in this order:

1. Environment variable `DOCKER_COMPOSE_BOILER_ROOT`
2. `~/Documents/docker/docker-compose-boiler`
3. Workspace root, if it contains all four pattern folders

`PATTERN_DIR = $STACKS_ROOT/<selected-pattern>/`

If `STACKS_ROOT` cannot be resolved, tell the user to clone docker-compose-boiler or set `DOCKER_COMPOSE_BOILER_ROOT`.

## Pattern selection

| Priority | Condition | Pattern folder |
|----------|-----------|----------------|
| 1 | **docker-sync**, **unison**, or macOS bind-mount slowness | `nginx-wp-mariadb-wpcli-dockersyncs` |
| 2 | **Apache** (not nginx) | `apache-wp-mysql-wpcli` |
| 3 | **MySQL 5.7** (legacy compat) | `nginx-wp-mysql-wpcli` |
| 4 | **MariaDB** or **nginx** explicitly | `nginx-wp-mariadb-wpcli` |
| **Default** | No preference | `nginx-wp-mariadb-wpcli` |

Briefly tell the user which pattern was chosen and why. Stack details: [patterns.md](patterns.md).

## Launching from another project (theme / plugin workspace)

When the workspace is **not** `STACKS_ROOT`, mount the current project into the stack before `docker compose up`.

### 1. Detect theme slug

Use the workspace folder basename (e.g. `my-theme`). If `style.css` exists at the workspace root, prefer the `Text Domain` or directory name.

### 2. Symlink into the pattern's theme mount

| Pattern | Symlink target |
|---------|----------------|
| `nginx-wp-mariadb-wpcli` | `$PATTERN_DIR/wp/wp-content/themes/<slug>` → `$WORKSPACE` |
| Others | `$PATTERN_DIR/themes/<slug>` → `$WORKSPACE` |

```bash
# Example (default pattern)
mkdir -p "$PATTERN_DIR/wp/wp-content/themes"
ln -sfn "$WORKSPACE" "$PATTERN_DIR/wp/wp-content/themes/<slug>"
```

- Replace an existing symlink pointing elsewhere.
- For **dockersyncs**, symlink under `$PATTERN_DIR/themes/<slug>` (docker-sync watches `./themes/`).

### 3. Name the stack after the project

Set `PRODUCTION_NAME` in `$PATTERN_DIR/.env` to the theme slug (or a sanitized project name) so multiple projects can run without container name conflicts.

## Pre-flight checks

Run compose commands from `PATTERN_DIR` (not the theme workspace).

### `.env`

Create `$PATTERN_DIR/.env` if missing. Pick free ports via `docker ps`.

```env
PRODUCTION_NAME=<project-slug>

LOCAL_DB_PORT=3310
MYSQL_RANDOM_ROOT_PASSWORD=yes
MYSQL_DATABASE=wordpress
MYSQL_USER=wordpress
MYSQL_PASSWORD=wordpress

LOCAL_SERVER_PORT=9088
WORDPRESS_DB_NAME=wordpress
WORDPRESS_DB_USER=wordpress
WORDPRESS_DB_PASSWORD=wordpress
```

Increment ports when already in use. Keep `PRODUCTION_NAME` unique per running stack.

### Theme mount directory

Ensure the pattern's theme parent directory exists (see [patterns.md](patterns.md)).

### docker-sync (dockersyncs only)

```bash
command -v docker-sync || { gem install docker-sync; brew install fswatch unison; }
docker-sync start   # from PATTERN_DIR
```

## Launch

```bash
cd "$PATTERN_DIR" && docker compose up -d
```

Prefer `docker compose` (v2); fall back to `docker-compose` if needed.

### Verify

```bash
docker compose ps
curl -s -o /dev/null -w "%{http_code}" "http://localhost:${LOCAL_SERVER_PORT}"
```

Report: pattern, URL (`http://localhost:<LOCAL_SERVER_PORT>`), DB port, `PRODUCTION_NAME`, and linked theme slug.

## WP-CLI

From `PATTERN_DIR`:

```bash
docker compose run --rm cli <command>
```

## Stop

From `PATTERN_DIR`:

```bash
docker compose down
```

Dockersyncs: also `docker-sync stop`. Use `down -v` only when the user explicitly wants to wipe volumes.

## Common issues

| Symptom | Action |
|---------|--------|
| Port already allocated | Change ports in `.env`, re-run `up` |
| Container name conflict | Change `PRODUCTION_NAME` |
| Theme changes not visible | Check symlink; restart wordpress/nginx service |
| STACKS_ROOT not found | Set `DOCKER_COMPOSE_BOILER_ROOT` or clone boiler repo |
| docker-sync volume missing | `docker-sync start` before `compose up` |

## Do not

- Commit or copy `.env` into git.
- Duplicate compose patterns into this skill folder (maintain them in docker-compose-boiler only).
- Run `docker compose down -v` unless the user asks to delete data.
