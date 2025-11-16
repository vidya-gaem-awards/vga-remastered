# /v/GA Remastered

## Local development

1. Install [Lando](https://lando.dev/)
2. Copy `.env.example` to `.env` and fill in as necessary.  
   You only need to complete the 'required' section; read the notes in the .env for more 
   information.
3. `composer install`
4. `lando start`
5. `lando artisan app:init-db`

## Deploying to the server

See [docker/README.md](docker/README.md).
