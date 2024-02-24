#ssh2miningHelper
    
Этот проект создан для отслеживания состояния сборок для майнинга на CPU
    
Что для этого нужно?

Для запуска программы нужны установленные пакеты: PHP, apache2, SSH2

sudo apt-get install libssh2-1-dev libssh2-1\nsudo pecl install ssh2-1.3.1
sudo pecl install ssh2-1.3.1
pecl install -f ssh2
------

Все сборки должны использовать Linux с установленными пакетами:
sudo apt install openssh-server lm-sensors

Также установите последнюю версию xmrig:
https://xmrig.com/download

A так же Cpuminer Rplant
https://github.com/rplant8/cpuminer-opt-rplant/releases/
------
DEMO: https://github.com/laptopsr/ssh2miningHelper/blob/main/screenshot.png

