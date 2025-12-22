#!/bin/bash

# Install Composer
curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev --optimize-autoloader --no-interaction

# Install npm dependencies and build assets
npm install
npm run build
