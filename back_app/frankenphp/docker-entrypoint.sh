#!/bin/sh
set -e

if [ ! -f composer.json ]; then
    rm -Rf tmp/
    composer create-project "symfony/skeleton:6.4.x" . --stability="dev" --prefer-dist --no-progress --no-interaction --no-install

    #cd tmp
    #cp -Rp . ..
    #cd -
    #rm -Rf tmp/

    composer require "php:>=8.3" runtime/frankenphp-symfony
    composer config --json extra.symfony.docker 'true'

    if grep -q ^DATABASE_URL= .env; then
        echo 'To finish the installation please press Ctrl+C to stop Docker Compose and run: docker compose up --build -d --wait'
        sleep infinity
    fi
fi

if [ -z "$(ls -A 'vendor/' 2>/dev/null)" ]; then
	composer install --prefer-dist --no-progress --no-interaction
fi
exec docker-php-entrypoint "$@"