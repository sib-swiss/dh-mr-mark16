#!/bin/bash

# Simple project deployment script [template version]
#
# Made by Jonathan Barda / SIB - 2021

[[ $# -eq 0 ]] && echo -e "\n /!\ READ THE CODE BEFORE EXEC /!\ \n" && exit

# Config
source deploy.template.conf

# sync if ready
if [[ $READY == false ]]; then
    # Test for data folder
    # rsync -rtvPhix --stats data ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/ --delete --dry-run

    # Test for project folder
    rsync -rtvPhix --stats htdocs/* --exclude "vendor/" --exclude ".git/" --include ".htaccess" ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/htdocs/ --delete --dry-run

    rsync -rtvPhix --stats doc/ADMIN-WEB-INTERFACE.md ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/doc/ --delete --dry-run

    # Update data sync script + config
    rsync -rtvPhix --stats deploy.prod.conf ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/ --delete --dry-run
    rsync -rtvPhix --stats sync-data-to-prod.sh ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/ --delete --dry-run
    
    # Update backup script
    rsync -rtvPhix --stats cron-backup-data.sh ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/ --delete --dry-run
else
    # Sync data folder
    # rsync -rtvPhix --stats data ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/ --delete

    # Sync project folder
    rsync -rtvPhix --stats htdocs/* --exclude "vendor/" --exclude ".git/" --include ".htaccess" ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/htdocs/ --delete

    rsync -rtvPhix --stats doc/ADMIN-WEB-INTERFACE.md ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/doc/ --delete

    # Update data sync script + config
    rsync -rtvPhix --stats deploy.prod.conf ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/ --delete
    rsync -rtvPhix --stats sync-data-to-prod.sh ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/ --delete

    # Update backup script
    rsync -rtvPhix --stats cron-backup-data.sh ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/ --delete

    # Apply permissions
    ssh ${CONNECT_USER}@${CONNECT_HOST} "chown -Rv ${SERVER_USER}:${PROJECT_GROUP} ${PROJECT_FOLDER}/data"
    ssh ${CONNECT_USER}@${CONNECT_HOST} "chmod -v 775 ${PROJECT_FOLDER}/data"
    ssh ${CONNECT_USER}@${CONNECT_HOST} "chown -Rv ${PROJECT_USER}:${PROJECT_GROUP} ${PROJECT_FOLDER}/htdocs"

    # Update site config JSON file
    # ssh ${CONNECT_USER}@${CONNECT_HOST} "cd ${PROJECT_FOLDER}/htdocs ; sed -e 's/\"debug\": true/\"debug\": false/' -i conf/config.json ; chown -v ${PROJECT_USER}:${PROJECT_GROUP} conf/config.json"
    ssh ${CONNECT_USER}@${CONNECT_HOST} "cd ${PROJECT_FOLDER}/htdocs ; sed -e 's/\"clear\": true/\"clear\": false/' -i conf/config.json ; chown -v ${PROJECT_USER}:${PROJECT_GROUP} conf/config.json"

    # Autoload new classes
    ssh ${CONNECT_USER}@${CONNECT_HOST} "cd ${PROJECT_FOLDER}/htdocs ; composer dumpautoload"
fi