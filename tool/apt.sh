#!/usr/bin/env bash
cp /etc/apt/sources.list /etc/apt/sources.list.copy
cp sources.list /etc/apt/sources.list
apt update
apt install -y libzip-dev beanstalkd
pecl install pcntl
docker-php-ext-install pcntl zip