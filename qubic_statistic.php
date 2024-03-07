<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "config.php";
use Codenixsv\CoinGeckoApi\CoinGeckoClient;

$myHashrate = $_POST['myHashrate']??950;

// Получение текущего эпохального номера и информации о сети
if(isset($_POST['qubic_token']) and empty($_POST['qubic_token']))
{
	$url = 'https://api.qubic.li/Auth/Login';
	$data = array('userName' => $qubic_user, 'password' => $qubic_pass, 'twoFactorCode' => '');
	$options = array(
		'http' => array(
		    'header'  => "Content-Type: application/json\r\n",
		    'method'  => 'POST',
		    'content' => json_encode($data)
		)
	);
	$context  = stream_context_create($options);
	$response = file_get_contents($url, false, $context);
	$result = json_decode($response, true);
	$token = $result['token'];
}
else if(isset($_POST['qubic_token']) and !empty($_POST['qubic_token']))
{
	$token = $_POST['qubic_token'];
}

$url = 'https://api.qubic.li/Score/Get';
$options = array(
    'http' => array(
        'header'  => "Authorization: Bearer $token\r\n",
        'method'  => 'GET'
    )
);
$context  		= stream_context_create($options);
$response 		= file_get_contents($url, false, $context);
$networkStat 	= json_decode($response, true);

$epochNumber = $networkStat['scoreStatistics'][0]['epoch'];
$epoch97Begin = strtotime('2024-02-21 12:00:00');
$curEpochBegin = strtotime('+ ' . (7 * ($epochNumber - 97)) . ' days', $epoch97Begin);
$curEpochEnd = strtotime('+7 days', $curEpochBegin);
$curEpochProgress = (time() - $curEpochBegin) / (7 * 24 * 3600);

$netHashrate = $networkStat['estimatedIts'];
$netAvgScores = $networkStat['averageScore'];
$netSolsPerHour = $networkStat['solutionsPerHour'];

$crypto_currency = 'qubic-network';
$destination_currency = 'usd';
$cg_client = new CoinGeckoClient();
$prices = $cg_client->simple()->getPrice($crypto_currency, $destination_currency);
$qubicPrice = $prices[$crypto_currency][$destination_currency];
$poolReward = 0.85;
$incomerPerOneITS = $poolReward * $qubicPrice * 1000000000000 / $netHashrate / 7 / 1.06;
$curSolPrice = 1479289940 * $poolReward * $curEpochProgress * $qubicPrice / ($netAvgScores * 1.06);

$bd = "
<br><br>Current epoch info:<br>
Current epoch: $epochNumber<br>
Epoch start UTC: " . date('Y-m-d H:i:s', $curEpochBegin) . "<br>
Epoch end UTC: " . date('Y-m-d H:i:s', $curEpochEnd) . "<br>
Epoch progress: " . number_format(100 * $curEpochProgress, 1) . "%<br><br>
Network info:<br>
Estimated network hashrate: " . number_format($netHashrate, 0, '', ' ') . " it/s<br>
Average score: " . number_format($netAvgScores, 1) . "<br>
Scores per hour: " . number_format($netSolsPerHour, 1) . "<br><br>
Income estimations:<br>
Using pool with fixed 85% reward<br><br>
Qubic price: " . number_format($qubicPrice, 8) . "$<br>
Estimated income per 1 it/s per day: " . number_format($incomerPerOneITS, 4) . "$<br><br>
Your estimated income per day: " . number_format($myHashrate * $incomerPerOneITS, 2) . "$<br>
Estimated income per 1 sol: " . number_format($curSolPrice, 2) . "$<br>
Your estimated sols per day: " . number_format(24 * $myHashrate * $netSolsPerHour / $netHashrate, 1) . "<br><br>";

echo json_encode(['body' => $bd, 'full' => $networkStat, 'token' => $token]);
?>
