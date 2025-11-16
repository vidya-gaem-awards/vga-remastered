# /v/GA Docker Deployment Guide

The software the application uses is installed using Docker and Docker Compose.

The `server` directory contains a `docker-compose.yml` file which defines the services used across
all /v/GA deployments. This includes the webserver (Caddy) and the database (MariaDB), as well
as Adminer and phpMyAdmin for providing a database UI.

The actual /v/GA application itself is deployed using a separate `docker-compose.yml` file in the
root of the project.

## Initial server setup

### Create the Docker context

```
docker context create vga --docker "host=ssh://vga"
```

You have two options to use this context:

1. Run `docker context use vga` to switch to the context.
2. Add `--context vga` to all docker commands.

The first is easier, but if you're doing other Docker stuff on your local machine, you might 
prefer the second option.

(The rest of the instructions below will assume you chose option one.)

### Deploying the server services

Run these commands from the `docker/server` directory in the project.

1. Create the Caddy network:
   ```
   docker network create caddy_net
   ```
2. Copy the `caddy` directory from this repo onto the server, located at `/var/vga/caddy`.
3. Fill in the `basic_auth` sections in the `Caddyfile`.
4. Start the services:
   ```
   docker compose up -d --force-recreate
   ```
   
### Database setup

Create a new root-ish user for the database.

```
docker compose exec database bash
maridb

CREATE USER 'user'@'%';
GRANT ALL PRIVILEGES ON *.* TO 'clamburger'@'%' IDENTIFIED BY 'YLmeGGPOo5eRPCXSIVyvgEje';
exit

mariadb-secure-installation
```

## Deploying the application

### Deploying to beta

First, you'll need to create a database and set up a new database user in MariaDB. You can do this
from Adminer or phpMyAdmin.

Run these commands from the root directory of the project.

1. (Once-off) Set up your Docker context
   ```
   docker context create vga --docker "host=ssh://vga"
   docker context use vga
   ```
2. Switch to the Docker context. (This is optional - if you don't use this, you will need to add
   `--context vga` to all docker commands below.)
   ```
   docker context use vga
   ```
3. Create a `.env.production` file and fill it in as necessary. Use `staging` as the value for `APP_ENV`.
4. Build the image:
   ```
   docker compose build --no-cache
   ```
5. Deploy
   ```
   docker compose up -d 
   ```
6. (Once-off) Initialize the database:
   ```
   docker compose exec app php artisan app:init-db
   ```

### Deploying to production

Follow the same steps above, with the following changes:

* In your `.env.production`, make sure the `APP_ENV` is set to production instead of staging
  (all other variables will likely be the same)
* In steps 5 and 6, add `-p vga-2025` after each instance of `docker compose`, like so:
  ```
  # Deploy
  docker compose -p vga-2025 up -d
  
  # Initialize the database
  docker compose -p vga-2025 exec app php artisan app:init-db
  ```

It's been set up like this so that if you forget to specify the project name, you'll (potentially)
destroy beta instead of production.

### Connecting to the containers


If you need to get into the container for whatever reason, use one of these commands:

```
# Beta
docker compose exec app bash

# Production
docker compose -p vga-2025 exec app bash
```
