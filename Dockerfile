############################################
# Base Image
############################################

# Learn more about the Server Side Up PHP Docker Images at:
# https://serversideup.net/open-source/docker-php/
FROM serversideup/php:8.4-fpm-nginx AS base

USER root
RUN apt update && apt install -y less vim

## Uncomment if you need to install additional PHP extensions
# RUN install-php-extensions bcmath gd

# Build frontend assets
FROM node:alpine AS node
COPY package.json package-lock.json vite.config.js app/
COPY resources/assets app/resources/assets
WORKDIR app
RUN npm install
RUN npm run build

############################################
# Production Image
############################################
FROM base AS production
COPY --chown=www-data:www-data . /var/www/html
COPY --chown=www-data:www-data --from=node /app/public /var/www/html/public

USER www-data

RUN composer install

# Populated by GitHub Actions
ARG GIT_VERSION_HASH=unknown
ENV GIT_VERSION_HASH=${GIT_VERSION_HASH}

ARG DOCKER_BUILD_DATE=unknown
ENV DOCKER_BUILD_DATE=${DOCKER_BUILD_DATE}
