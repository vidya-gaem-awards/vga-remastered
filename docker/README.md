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
GRANT ALL PRIVILEGES ON *.* TO 'user'@'%' IDENTIFIED BY 'YLmeGGPOo5eRPCXSIVyvgEje';
exit

mariadb-secure-installation
```

### Setting up a new environment for a new year

Create a database and set up a new database user in MariaDB. You can do this from Adminer or 
phpMyAdmin. Give the user database-specific privileges; don't give it any global privileges.

In the GitHub settings for the repo, go to Environments, and create a new environment.
You will need to add four environment secrets. They are fairly self-explanatory, but
you'll need to get SERVER_PRIVATE_KEY from Clamburger, as it is stored in a local KeePass database.

The Caddy file at docker/server/caddy/Caddyfile needs to be updated to add the new domain, which
means the server-level Docker Compose stack needs to be redeployed. This is very annoying but
unfortunately unavoidable at present. See the "Deploying the server services" section above.

Once that's done, deploy the application using the GitHub steps below. Finally, initialize the 
environment using the following command:

```
docker compose -p vga-2026 exec app php artisan app:init-db
```

## Deploying the application

### Using GitHub

All commits to the repo will build an image automatically, but deploying is manual. Once the
image is ready, run the "Deploy application" workflow from GitHub Actions.

### Manually

It's no longer possible to manually deploy the application. If you really need to, you'll have
to grab the docker-compose.yml from commit 4298994a, which is the last commit before automatic
builds were implemented. Further instructions will be in that commit's README.

### Connecting to the containers

If you need to get into the container for whatever reason, use one of these commands:

```
# Beta
docker compose exec app bash

# Production
docker compose -p vga-2026 exec app bash
```
