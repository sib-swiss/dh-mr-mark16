#!/bin/bash

# Simple PHP version switch script
#
# Made by Jonathan Barda / SIB - 2020
#
# References:
# - https://www.tecmint.com/install-different-php-versions-in-ubuntu/
# - https://www.cyberciti.biz/faq/grep-regular-expressions/

((!$#)) && echo -e "\nUsage: $0 <version>\n" && exit 1

# Config
PHP_VER=$1

# Colors
NC="\033[0m"
NL="\n"
BLUE="\033[1;34m"
YELLOW="\033[1;33m"
GREEN="\033[1;32m"
RED="\033[1;31m"
WHITE="\033[1;37m"
PURPLE="\033[1;35m"

# UI
echo -e "${NL}${WHITE}Switching PHP version to: ${PURPLE}${PHP_VER}${WHITE}...${NC}"
sudo update-alternatives --set php "/usr/bin/php${PHP_VER}" 2>&1 >/dev/null

echo -e "${WHITE}Verifying PHP version...${NC}"
if [[ $(php -v | grep -i "php ${PHP_VER}" | wc -l) -eq 1 ]]; then
    echo -e "${WHITE}Found: ${GREEN}${PHP_VER}${NC}"
    echo -e "${WHITE}Details:${NC}"
    php -v
    echo -e "${NL}"
    exit 0
else
    echo -e "${RED}Error: PHP version not updated.${NC}"
    echo -e "${WHITE}Found:${NC}"
    php -v
    echo -e "${NL}"
    exit 1
fi