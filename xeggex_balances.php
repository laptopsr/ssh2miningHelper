<?php
//include "config.php";

// Строка для кодирования в Base64
$base64String = $apiKey . ":" . $apiSecret;

// Кодирование в Base64
$encodedString = base64_encode($base64String);

// URL для запроса
$url = 'https://api.xeggex.com/api/v2/balances';

// Инициализация cURL сессии
$ch = curl_init($url);

// Установка опций запроса
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'accept: application/json',
    'Authorization: Basic ' . $encodedString,
));

// Выполнение запроса
$response = json_decode(curl_exec($ch), true);

// Проверка на наличие ошибок
if(curl_errno($ch)){
    echo 'Ошибка cURL: ' . curl_error($ch);
}

// Закрытие cURL сессии
curl_close($ch);

$xeggexBalances = [];

if(isset($response[0]['asset']))
{
	foreach($response as $k => $v)
	{
		if($v['available'] > 0)
		{
			$xeggexBalances[$v['asset']] = $v;
		}
	}

	/*
    [VARSE] => Array
        (
            [asset] => VARSE
            [name] => Varse Chain
            [available] => 41159.79430105
            [pending] => 0.00000000
            [held] => 0.00000000
            [assetid] => 64f52efb0b79ddeddd3eeefc
        )
	*/
	/*
	echo '<pre>';
	print_r($xeggexBalances);
	echo '</pre>';
	*/

	//echo json_encode($xeggexBalances);
}
?>
