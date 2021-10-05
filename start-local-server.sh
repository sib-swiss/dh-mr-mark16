#!/bin/bash

# Try to speed-up the local server with:
# https://stackoverflow.com/questions/30238602/how-to-execute-a-php-file-using-a-php5-fpm-pool-socket
# https://stackoverflow.com/questions/54417514/how-to-get-php-fpm-status-via-cli-or-pure-fastcgi

# Config
LISTEN_ADDRESS=localhost
LISTEN_PORT=8000
DOC_ROOT="."
FOLDER="htdocs"
ROUTER="htdocs/router.php"

# Colors
NC="\033[0m"
NL="\n"
BLUE="\033[1;34m"
YELLOW="\033[1;33m"
GREEN="\033[1;32m"
RED="\033[1;31m"
WHITE="\033[1;37m"
PURPLE="\033[1;35m"

# Detect PHP version
PHP_SRV_VERSION=$(php -r "if (version_compare(phpversion(), '7.4', '<')) { echo 'old'; } else { echo 'new'; }")

# UI
echo -e "${NL}${BLUE}Starting local web server...${NC}${NL}"
echo -e "${YELLOW}Project URL: ${WHITE}http://${LISTEN_ADDRESS}:${LISTEN_PORT}/${FOLDER} ${NC}${NL}"

# Server
if [[ $PHP_SRV_VERSION == 'new' ]]; then
    PHP_CLI_SERVER_WORKERS=$(nproc) php -S ${LISTEN_ADDRESS}:${LISTEN_PORT} -t $DOC_ROOT $ROUTER
else
    php -S ${LISTEN_ADDRESS}:${LISTEN_PORT} -t $DOC_ROOT $ROUTER
fi