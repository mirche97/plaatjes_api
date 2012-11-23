#!/bin/sh
php app/console doctrine:database:drop --connection=default --force &&
php app/console doctrine:database:create --connection=default &&
php app/console doctrine:schema:create
