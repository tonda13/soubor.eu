#!/bin/bash

echo "Install composer and NPM packages"
composer install
#npm install

echo "Starting Apache"
apachectl -D FOREGROUND

