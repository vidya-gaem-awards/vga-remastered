############################################
# Base Image
############################################

# Learn more about the Server Side Up PHP Docker Images at:
# https://serversideup.net/open-source/docker-php/
FROM serversideup/php:8.4-fpm-nginx AS base

USER root
RUN apt update && apt install -y less vim mariadb-client

## Uncomment if you need to install additional PHP extensions
# RUN install-php-extensions bcmath gd

############################################
# Production Image
############################################
FROM base AS production
COPY --chown=www-data:www-data . /var/www/html

USER www-data

RUN composer install
