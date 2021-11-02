#!/bin/bash

# Simple project install script
#
# Made by Jonathan Barda / SIB - 2020

# Config
PHP_VER="7.4"

# Install PPA
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php

# Install PHP 7.4 + extensions
sudo apt install -y \
    "php${PHP_VER}-cli" \
    "php${PHP_VER}-fpm" \
    "php${PHP_VER}-gd" \
    "php${PHP_VER}-opcache" \
    "php${PHP_VER}-xml" \
    "php${PHP_VER}-sqlite3" \
    "php${PHP_VER}-pdo" \
    "php${PHP_VER}-mbstring"

# Get Composer
./install-composer.sh