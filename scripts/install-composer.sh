#!/bin/bash

# Automatic Composer integrity check / install script
#
# Made by Jonathan Barda / SIB - 2020
#
# References:
# - https://getcomposer.org/doc/faqs/how-to-install-composer-programmatically.md
# - https://github.com/composer/getcomposer.org/commits/master
# - https://github.com/composer/getcomposer.org/commit/e3e43bde99447de1c13da5d1027545be81736b27

EXPECTED_CHECKSUM="$(wget -q -O - https://composer.github.io/installer.sig)"
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]
then
    >&2 echo 'ERROR: Invalid installer checksum'
    rm composer-setup.php
    exit 1
fi

php composer-setup.php --quiet
RESULT=$?
[[ $RESULT -eq 0 ]] && echo -e "\nComposer binary created.\n" || echo -e "\nError during Composer binary creation.\n"
rm composer-setup.php
if [[ $RESULT -eq 0 ]]; then
    echo "Moving [composer.phar] to system folders..."
    sudo mv composer.phar /usr/local/bin/composer
    echo -e "\nCurrent version installed:\n"
    composer -V
    echo ""
fi
exit $RESULT