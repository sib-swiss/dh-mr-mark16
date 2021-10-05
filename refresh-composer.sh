#!/bin/bash

BIN=$(which composer)
if [[ $BIN == "" ]]; then
    BIN="$(pwd)/composer.phar"
fi

cd htdocs
$BIN dumpautoload
cd ..