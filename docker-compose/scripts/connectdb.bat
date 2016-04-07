#!/bin/bash

docker-compose run --rm bravendb mysql -h bravendb -u wordpress "-pwordpress" wordpress
