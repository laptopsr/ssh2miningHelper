<?php

include "config.php";

/*
var source = new EventSource('https://pool.rplant.xyz/api2/poolminer2x/reaction/RuR6UEmYByq7u4QVWxkWrkSdEC8mxU283M/111111');
source.addEventListener('message', function(e) {
  console.log(e.data);
}, false);
*/

// https://pool.rplant.xyz/api2/walletEx/reaction/RuR6UEmYByq7u4QVWxkWrkSdEC8mxU283M/111111
// https://pool.rplant.xyz/api2/poolminer2x/reaction/RuR6UEmYByq7u4QVWxkWrkSdEC8mxU283M/111111

$url = 'https://pool.rplant.xyz/api2/poolminer2x/reaction/RuR6UEmYByq7u4QVWxkWrkSdEC8mxU283M/111111';
$context = stream_context_create([
    'http' => [
        'timeout' => 3
    ]
]);

// Чтение данных с URL с учетом контекста потока
$data = file_get_contents($url, false, $context);

if ($data === false) {
    // Обработка ошибки
    echo 'Ошибка при получении данных';
} else {

	// Разделяем данные на строки
	$lines = explode('data: ', $data);

	// Инициализируем массив для хранения данных JSON
	$jsonData = [];

	// Проходим по всем строкам
	foreach ($lines as $line) {
		// Проверяем, содержит ли строка JSON
		if (strpos($line, '{') === 0) {
		    // Добавляем JSON-строку в массив данных
		    $jsonData[] = $line;
		}
	}

	// Преобразуем JSON-строки в массивы PHP
	$phpData = [];
	foreach ($jsonData as $jsonString) {
		$phpData[] = json_decode($jsonString, true);
	}

	echo '<pre>';
	print_r($phpData[2]['miner']);
	echo '</pre>';
}
?>
