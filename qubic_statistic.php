<?php
session_start();
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include "config.php";
use Codenixsv\CoinGeckoApi\CoinGeckoClient;

$sendToSTAN = $sendToSTAN??false;

// --- AVG --- //
$f_avg 				= "avg_workers.txt";
$clear_avg_workers 	= false;

if($clear_avg_workers)
{
	file_put_contents($f_avg, '');
}

$get_avg 	= file_get_contents($f_avg);
$arr		= explode("\n", $get_avg);
$out		= [];
$AVG 		= [];
$percentSum = 0;

foreach($arr as $line)
{
	$arr_line = json_decode(trim($line), true);
	
	if(is_array($arr_line))
	{
		foreach($arr_line as $name => $hash)
		{
			$out[$name][] = $hash;
		}
	}
}
foreach($out as $n => $arrs)
{
	$AVG[$n] = round(array_sum($out[$n]) / count($out[$n]));
}

if(!isset($_POST['qubic_token']))
{
/*
	echo '<pre>';
	print_r($AVG);
	echo '</pre>';
	exit;
*/
}


// My/Get, My/MinerControl, My/GetMiner, My/Pool, My/Pool/Payouts, My/Profile, Revenue/Get

// Получение текущего эпохального номера и информации о сети
if(isset($_POST['qubic_token']) and empty($_POST['qubic_token']) or !isset($_POST['qubic_token']))
{
	$url = 'https://api.qubic.li/Auth/Login';
	$data = array('userName' => $qubic_user, 'password' => $qubic_pass, 'twoFactorCode' => '');
	$options = array(
		'http' => array(
		    'header'  => "Content-Type: application/json\r\n",
		    'method'  => 'POST',
		    'content' => json_encode($data),
		    'timeout' => 3
		)
	);

	$context  = stream_context_create($options);
	$response = file_get_contents($url, false, $context);
	$result = json_decode($response, true);
	$token = $result['token']??'';
}
else if(isset($_POST['qubic_token']) and !empty($_POST['qubic_token']))
{
	$token = $_POST['qubic_token'];
}

if(empty($token))
{
	echo json_encode(['error' => 'Token']);
	exit;
}

//$url = 'https://api.qubic.li/My/MinerControl';
$url = 'https://api.qubic.li/My/Pool';
$options = array(
    'http' => array(
        'header'  => "Authorization: Bearer $token\r\n",
        'method'  => 'GET',
        'timeout' => 3
    )
);
$context = stream_context_create($options);

try {
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        throw new Exception("Ошибка при выполнении запроса: " . error_get_last()['message']);
    }

	$response 	= file_get_contents($url, false, $context);
	$pool 		= json_decode($response, true);

	if(isset($pool['miningPools'][0]['id']))
	{
		$url = 'https://api.qubic.li/My/Pool/'.$pool['miningPools'][0]['id'].'/Performance';
		$options = array(
			'http' => array(
				'header'  => "Authorization: Bearer $token\r\n",
				'method'  => 'GET',
				'timeout' => 3
			)
		);
		$context = stream_context_create($options);

		try {
			$response 	= file_get_contents($url, false, $context);
			$GetMiner	= json_decode($response, true);
		} catch (Exception $e) {

		}
	}

	// ------ //

	if(!isset($_POST['qubic_token']))
	{
		echo '<h1>Pool</h1>';
		echo '<pre>';
		print_r($pool['miningPools'][0]['id']);
		echo '</pre>';

		echo '<h1>GetMiner</h1>';
		echo '<pre>';
		print_r($GetMiner);
		echo '</pre>';
	}

} catch (Exception $e) {

}

/*
    [foundSolutions] => 249
    [miners] => Array
        (
            [0] => Array
                (
                    [id] => 65b10396-1be4-4dd0-8504-0102d11383a6
                    [minerBinaryHash] => Xkp/w7lm1p2u197ohzut6A==
                    [minerBinaryId] => 
                    [alias] => AlexKovalskiy.test2
                    [versionString] => 1.8.10.0
                    [version] => Array
                        (
                            [major] => 1
                            [minor] => 8
                            [patch] => 10
                            [versionString] => 1.8.10
                        )

                    [outdatedVersion] => 
                    [lastActive] => 2024-03-26T11:44:33.453
                    [currentIts] => 0
                    [currentIdentity] => QSETADETNDYROESMJRZVJYPFOWTBEHKOVYFSNANTQCGDTBJMHIIONWRDOEOI
                    [solutionsFound] => 0
                    [threads] => 
                    [totalFeeTime] => 0
                    [feeReports] => 
                    [isActive] => 1
                )
*/

if(!isset($_POST['qubic_token']))
{
/*
	$url = 'https://api.qubic.li/My/Pool/c3b45fea-e748-428f-96fe-222d722682b8/Performance'; // Что бы узнать ID - https://api.qubic.li/My/Pool
	$options = array(
		'http' => array(
		    'header'  => "Authorization: Bearer $token\r\n",
		    'method'  => 'GET',
		    'timeout' => 3
		)
	);
	$context = stream_context_create($options);

	try {
		$response = file_get_contents($url, false, $context);

		if ($response === false) {
		    throw new Exception("Ошибка при выполнении запроса: " . error_get_last()['message']);
		}

		$response 		= file_get_contents($url, false, $context);
		$arrResp 		= json_decode($response, true);

	} catch (Exception $e) {

	}


	echo '<h1>My/MinerControl</h1>';
	echo '<pre>';
	print_r($arrResp);
	echo '</pre>';
	exit;
*/
}

$activePoolName = "";
$totalSolutions = $GetMiner['foundSolutions']??0;
$totalIts		= 0;
$h_per_user		= "AVG хэшрейты:\n";
$nullhash 		= [];

$tb_miners = "<table class=\"table table-striped qubicStat\">
<thead>
<tr>
	<th>Alias</th>
	<th>SOL</th>
	<th>Active</th>
	<th>Last</th>
	<th>Its</th>
	<th>Version</th>
</tr>
</thead>
<tbody>";
if(isset($GetMiner['miners']) and count($GetMiner['miners']) > 0)
{

	if($sendToSTAN)
	{
		$url = 'http://stanvps.ddns.net:8100/data.php';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($GetMiner['miners']));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
	}

	// ------ //
	$hash_per_user = [];
	foreach($GetMiner['miners'] as $miner)
	{
		$totalIts 	+= $miner['currentIts'];
		if(str_contains($miner['alias'], '.'))
		{
			$ex_miner 	= explode(".", $miner['alias']);
		}
		if(str_contains($miner['alias'], '___'))
		{
			$ex_miner 	= explode("___", $miner['alias']);
		}
		// < -- ignor
		/*
		if(
			$ex_miner[0] != 'AlexKovalskiy'
			and $ex_miner[0] != 'andreiA'
		)
		{
		*/
			$hash_per_user[$ex_miner[0]] = (isset($hash_per_user[$ex_miner[0]])) ? $miner['currentIts']+$hash_per_user[$ex_miner[0]] : $miner['currentIts'];

			if($miner['currentIts'] == 0 or empty($miner['isActive']))
			{
				$nullhash[] = $miner['alias'];
			}
		//}

		$tb_miners .= "
		<tr>
			<td>$miner[alias]</td>
			<td>".($miner['solutionsFound']>0? $miner['solutionsFound'] : '')."</td>
			<td>".(empty($miner['isActive'])? '':'On')."</td>
			<td>".date("H:i", strtotime($miner['lastActive'])+7200)."</td>
			<td>$miner[currentIts]</td>
			<td>".$miner['version']['versionString']."</td>
		</tr>";
		
	}

	if(count($hash_per_user) > 0)
	{
		$rep = [];
		foreach($AVG as $u => $h)
		{
			$rep[$h] = $u;
		}
		krsort($rep);

		foreach($rep as $h => $u)
		{
			$perc 			= round($h / array_sum($AVG) * 100, 2);
			$h_per_user 	.= "$u   $h    $perc%\n";
		}

		if(count($nullhash) > 0)
		{
			$h_per_user = "Проблемные воркеры:\n".implode("\n", $nullhash);
		}
		else
		{
			if($sendToSTAN and !$clear_avg_workers)
			{
				// --- SAVE to JSON AVG per MINER--- //
				$get 	= file_get_contents($f_avg);
				/*
				$lines 	= explode("\n", $get);

				// Удаляем повторяющиеся строки
				$unique_lines = array_unique($lines);

				// Объединяем уникальные строки обратно в одну переменную
				$cleaned_content = implode("\n", $unique_lines);
				*/
				
				file_put_contents($f_avg, json_encode($hash_per_user)."\n".$get);
			}
		}

		if(!isset($_POST['qubic_token']))
		{
			echo $h_per_user.'<br>';
		}
	}
}
$tb_miners .= "</tbody></table>";

$url = 'https://api.qubic.li/Score/Get';
$options = array(
    'http' => array(
        'header'  => "Authorization: Bearer $token\r\n",
        'method'  => 'GET',
        'timeout' => 3
    )
);
$context = stream_context_create($options);

try {
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        throw new Exception("Ошибка при выполнении запроса: " . error_get_last()['message']);
    }

    $networkStat 	= json_decode($response, true);
} catch (Exception $e) {
    // Обработка исключения
    //echo "Произошла ошибка: " . $e->getMessage();
}

if(!isset($_POST['qubic_token']))
{
/*
	echo '<h1>Score/Get</h1>';
	echo '<pre>';
	print_r($networkStat);
	echo '</pre>';
*/
}

$bd = "OFFLINE";

if(isset($networkStat['scoreStatistics'][0]['epoch']) and isset($networkStat['estimatedIts']))
{
	$epochNumber = $networkStat['scoreStatistics'][0]['epoch'];
	$epoch97Begin = strtotime('2024-02-21 14:00:00');
	$curEpochBegin = strtotime('+ ' . (7 * ($epochNumber - 97)) . ' days', $epoch97Begin);
	$curEpochEnd = strtotime('+7 days', $curEpochBegin);
	$curEpochProgress = (time() - $curEpochBegin) / (7 * 24 * 3600);

	$netHashrate 		= $networkStat['estimatedIts']??0;
	$netAvgScores 		= $networkStat['averageScore']??0;
	$netSolsPerHour 	= $networkStat['solutionsPerHour']??0;

	if($netHashrate > 0)
	{
		$crypto_currency = 'qubic-network';
		$destination_currency = 'usd';
		$cg_client = new CoinGeckoClient();
		$prices = $cg_client->simple()->getPrice($crypto_currency, $destination_currency);
		$qubicPrice = $prices[$crypto_currency][$destination_currency];
		$poolReward = 0.85;
		$incomerPerOneITS = $poolReward * $qubicPrice * 1000000000000 / $netHashrate / 7 / 1.06;
		$curSolPrice = 1479289940 * $poolReward * $curEpochProgress * $qubicPrice / ($netAvgScores * 1.06);

		$bd = "
		<br>
		Epoch start / end: <b>" . date('d.m.Y H:i', $curEpochBegin) . " / " . date('d.m.Y H:i', $curEpochEnd) . "</b><br>
		Estimated network hashrate: <b>" . number_format($netHashrate, 0, '', ' ') . " it/s</b><br>
		Average score: <b>" . number_format($netAvgScores, 1) . "</b>.<br>
		Network SOL per hour: <b>" . number_format($netSolsPerHour, 1) . "</b><br>
		Qubic price: <b>" . number_format($qubicPrice, 8) . "$</b><br>
		Estimated income per 1 it/s per day: <b>" . number_format($incomerPerOneITS, 4) . "$</b><br>
		Your estimated income per day: <b>" . number_format($totalIts * $incomerPerOneITS, 2) . "$</b><br>
		Estimated income per 1 sol: <b>" . number_format($curSolPrice, 2) . "$</b><br>
		Your estimated sols per day: <b>" . number_format(24 * $totalIts * $netSolsPerHour / $netHashrate, 1) . "</b><br>
		Your estimated per epoch SOL: <b>" . number_format(168 * $totalIts * $netSolsPerHour / $netHashrate, 1) . "</b>, 
				USD: <b>" . number_format($curSolPrice * (168 * $totalIts * $netSolsPerHour / $netHashrate), 1) . "$</b><br>
		In this epoch, you received: <b>" . number_format($curSolPrice * $totalSolutions, 2) . "$</b>";
	}

	$arr = [
		'time' => time(),
		'body' => $bd, 
		//'full' => $networkStat, 
		'token' => $token, 
		'epoch_progress' => round(100 * $curEpochProgress, 1), 
		'tb_miners' => $tb_miners, 
		'totalSolutions' => $totalSolutions, 
		'totalIts' => $totalIts,
		'epochNumber' => $epochNumber,
		'netHashrate' => $netHashrate,
		'AVG' => $AVG,
		'AVG_sum' => array_sum($AVG),
		'curSolPrice' => $curSolPrice,
		'qubicPrice' => number_format($qubicPrice, 8),
		'last_SOL_time' => $_SESSION['last_SOL_time']??0
	];

	if(isset($_POST['qubic_token']))
	{
		echo json_encode($arr);
	}

	if($sendToSTAN)
	{
		unset($arr['tb_miners'], $arr['body']);
		$url = 'http://stanvps.ddns.net:8100/calc.php';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arr));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);

		if ($response === false) {
			//echo 'Ошибка cURL: ' . curl_error($ch);
		} else {
			//echo 'Ответ от сервера: ' . $response;
		}
		curl_close($ch);

		// ------ //
		$sendIt 		= false;
		$perEpochSol	= round(($totalSolutions / (100 * $curEpochProgress)) * 100);
		$perEpochUSD	= number_format($perEpochSol * $curSolPrice, 2);

		if(!isset($_POST['qubic_token']))
		{
			echo '<hr>'.$perEpochSol.' '.$perEpochUSD;
		}

		if(!isset($_SESSION['SOL']))
		{
			$_SESSION['SOL'] = $totalSolutions;
			$botToken 	= '7020158981:AAEG040eTHZaQS_5Y-JlRjEVX0ZLNTdfRBI';
			$chatId 	= '-1002020729115';
			$message 	= "Я перезагрузился.\nНачинаю слежение за новыми решениями.\n\nВсего сейчас: $totalSolutions SOL";
			$url 		= "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=".urlencode($message);
			$response 	= file_get_contents($url);
		}
		if(isset($_POST['qubic_token']) and isset($_SESSION['SOL']) and $totalSolutions > $_SESSION['SOL'])
		{
			$sendIt 					= true;
			$_SESSION['last_SOL_time'] 	= time();
		}
		if($sendIt)
		{
			$botToken 			= '7020158981:AAEG040eTHZaQS_5Y-JlRjEVX0ZLNTdfRBI';
			$chatId 			= '-1002020729115';
			$message 			= "\n------\n!!!     НАЙДЕНО   (".($totalSolutions-$_SESSION['SOL']).")   SOL     !!!\n\nВсего найдено: $totalSolutions SOL\nВсего за эпоху около: $perEpochSol SOL\nПримерно: $perEpochUSD $\n\nЭпоха: $epochNumber\nСкорость сети: $netHashrate It/s\nНаш хешрейт: $totalIts It/s\nВремя сервера: ". date("d.m.Y H:i")."\n\n------\n".$h_per_user;
			$url 				= "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=".urlencode($message);
			$response 			= file_get_contents($url);
			$_SESSION['SOL'] 	= $totalSolutions;
		}
	}

	exit;
}

echo json_encode(['return' => 'no data']);
exit;
?>
