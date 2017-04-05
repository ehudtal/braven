#!/bin/bash

cp -a /var/www/html/docker-compose/config/wp-config.php /var/www/html/wp-config.php

php -S 0.0.0.0:3005 -t /var/www/html

