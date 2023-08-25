#!/usr/bin/env bash
# see https://carstenwindler.de/php/enable-xdebug-on-demand-in-your-local-docker-environment/
if [ "$#" -ne 1 ]; then
    SCRIPT_PATH=`basename "$0"`
    echo "Usage: $SCRIPT_PATH enable|disable"
    exit 1;
fi
if [ "$1" == "enable" ]; then
    bash -c '[ -f /usr/local/etc/php/disabled/docker-php-ext-xdebug.ini ] && cd /usr/local/etc/php/ && mv disabled/docker-php-ext-xdebug.ini conf.d/ && kill -USR1 1 || echo "Xdebug already enabled"'
else
    bash -c '[ -f /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini ] && cd /usr/local/etc/php/ && mkdir -p disabled/ && mv conf.d/docker-php-ext-xdebug.ini disabled/ && kill -USR1 1 || echo "Xdebug already disabled"'
fi
bash -c '$(php -m | grep -q Xdebug) && echo "Status: Xdebug ENABLED" || echo "Status: Xdebug DISABLED"'
