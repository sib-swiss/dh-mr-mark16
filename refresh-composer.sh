#!/bin/bash

# Automatic Composer libraries dump script
#
# Made by Jonathan Barda / SIB - 2020

BIN=$(which composer)
if [[ $BIN == "" ]]; then
    BIN="$(pwd)/composer.phar"
fi

cd htdocs
$BIN dumpautoload
cd ..