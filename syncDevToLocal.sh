#!/bin/bash
rsync -rtvPhix --stats mark16-dev8.vital-it.ch:/var/vhosts/vital-it.ch/mark16-dev/htdocs/database/database.sqlite database/database.sqlite --delete
rm -rfv storage/app/public/images/
rsync -rtvPhix --stats --exclude ".gitignore" --exclude "images" mark16-dev8.vital-it.ch:/var/vhosts/vital-it.ch/mark16-dev/htdocs/storage/app/public/   storage/app/public/  --delete
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan view:clear
