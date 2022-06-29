#!/bin/bash

# Simple data sync script
#
# Made by Jonathan Barda / SIB - 2020

# [[ $# -eq 0 ]] && echo -e "\n /!\ READ THE CODE BEFORE EXEC /!\ \n" && exit

# Config
source deploy.dev.conf

# sync if ready
if [[ $READY == false ]]; then
    # Test for data folder
    rsync -rtvPhix --stats ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/data/manuscripts $DATA_FOLDER --delete --dry-run
    rsync -rtvPhix --stats ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/data/database.sqlite $DATA_FOLDER --delete --dry-run
    rsync -rtvPhix --stats ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/data/nakala.json $DATA_FOLDER --delete --dry-run
else
    # Sync data folder
    rsync -rtvPhix --stats --exclude ".gitignore" ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/data/manuscripts $DATA_FOLDER --delete
    rsync -rtvPhix --stats ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/data/database.sqlite $DATA_FOLDER --delete
    rsync -rtvPhix --stats ${CONNECT_USER}@${CONNECT_HOST}:${PROJECT_FOLDER}/data/nakala.json $DATA_FOLDER --delete
fi