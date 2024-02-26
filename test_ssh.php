<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "config.php";
use phpseclib3\Net\SSH2;

// Создаем новый объект SSH2 и подключаемся к серверу
$ssh = new SSH2('192.168.1.207');
if (!$ssh->login($ssh_user, $ssh_pass)) {
    exit('Login Failed');
}

// Выполняем команды на удаленном сервере
echo $ssh->exec('pwd');
echo $ssh->exec('ls -la');
?>
