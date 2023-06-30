#!/bin/bash
rsync -rtvPhix --stats --exclude ".gitignore" mark16-prod8.vital-it.ch:/var/vhosts/vital-it.ch/mark16-prod/data/manuscripts/ storage/app/from/manuscripts/ --delete
rsync -rtvPhix --stats mark16-prod8.vital-it.ch:/var/vhosts/vital-it.ch/mark16-dev/htdocs/database/database.sqlite database/database.sqlite --delete
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan view:clear

