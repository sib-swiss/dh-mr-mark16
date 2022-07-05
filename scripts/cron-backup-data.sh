#!/bin/bash

# Automated CRON backup script
#
# Made by Jonathan Barda / SIB - 2021
#
# Used references:
# - https://www.unixmen.com/performing-incremental-backups-using-tar/
# - https://www.gnu.org/software/tar/manual/html_node/exclude.html
# - https://stackoverflow.com/questions/984204/shell-command-to-tar-directory-excluding-certain-files-folders
#
#
# Instructions:
#
# put this in 'crontab -e' every night
#
# 0 0 * * * cd FULLPATH_OF_PROJECT && ./cron-backup-data.sh

# Config
DATA_FOLDER="data"
BACKUP_FOLDER="backups"
BACKUP_PREFIX=$(date "+%F_%H.%M")
SNAPSHOT_FILE="${BACKUP_FOLDER}/.snapshot-file"
# Backup format selector
# Compression algorithm used for all modes: lzma2

# Compression with tar + xz
# high compression level (almost similar to 7z)
# faster than tar + 7z to decompress
# but slower than 7z only
time tar --listed-incremental="${SNAPSHOT_FILE}" --exclude="${DATA_FOLDER}/logs" -cf - $DATA_FOLDER | xz -z -9 -e -T 0 -vv -c - > "${BACKUP_FOLDER}/${BACKUP_PREFIX}_manuscript-data.tar.xz"