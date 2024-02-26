SSH2miningHelper

This project is created to monitor the status of mining builds for CPU.

Prerequisites
To run the program, you need to have the following packages installed: PHP, apache2, SSH2

curl -sS https://getcomposer.org/installer | php
php composer.phar update
php composer.phar require phpseclib/phpseclib:~3.0
php composer.phar install

All builds should use Linux with the following packages installed:

sudo apt install openssh-server lm-sensors

Also, install the latest version of xmrig:
https://xmrig.com/download

And Cpuminer Rplant as well:
https://github.com/rplant8/cpuminer-opt-rplant/releases/

Demo:  https://github.com/laptopsr/ssh2miningHelper/blob/main/screenshot.png

This README provides instructions for setting up and using the SSH2miningHelper project to monitor mining builds on CPU.
