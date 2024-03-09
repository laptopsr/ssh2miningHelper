<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include "config.php";
use Codenixsv\CoinGeckoApi\CoinGeckoClient;

$myHashrate = $_POST['myHashrate']??950;

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
	$token = $result['token'];
}
else if(isset($_POST['qubic_token']) and !empty($_POST['qubic_token']))
{
	$token = $_POST['qubic_token'];
}

$url = 'https://api.qubic.li/My/GetMiner'; // https://api.qubic.li/My/Get, https://api.qubic.li/My/MinerControl, https://api.qubic.li/My/GetMiner
$options = array(
    'http' => array(
        'header'  => "Authorization: Bearer $token\r\n",
        'method'  => 'GET',
        'timeout' => 3
    )
);
$context  		= stream_context_create($options);
$response 		= file_get_contents($url, false, $context);
$GetMiner 		= json_decode($response, true);

/*
    [0] => Array
        (
            [id] => 66f50894-63ce-4c3e-bf5c-87952d95942f
            [minerBinaryId] => 
            [alias] => 217
            [version] => Array
                (
                    [major] => 0
                    [minor] => 0
                    [patch] => 0
                    [versionString] => 0.0.0
                )

            [outdatedVersion] => 1
            [lastActive] => 2024-03-09T08:15:57.5
            [currentIts] => 72
            [currentIdentity] => XJKGAOXAOGQQGEPOSOLGYJDOJFUAUUXLVDPRQTQMDAEORWPZXUWGQDTAEASH
            [solutionsFound] => 0
            [threads] => 
            [totalFeeTime] => 0
            [feeReports] => Array
                (
                )

            [isActive] => 1
        )
*/

$tb_miners = "<table class=\"table table-striped\">";
$tb_miners .= "
<tr>
	<th>Alias</th>
	<th>SOL</th>
	<th>Active</th>
	<th>Last</th>
	<th>Its</th>
</tr>";

$totalSolutions = 0;
$totalIts		= 0;

foreach($GetMiner as $miner)
{
	$totalSolutions += $miner['solutionsFound'];
	$totalIts 		+= $miner['currentIts'];

	$tb_miners .= "
	<tr>
		<td>$miner[alias]</td>
		<td>".($miner['solutionsFound']>0? $miner['solutionsFound'] : '')."</td>
		<td>$miner[isActive]</td>
		<td>".date("H:i", strtotime($miner['lastActive'])+7200)."</td>
		<td>$miner[currentIts]</td>
	</tr>";
}
$tb_miners .= "</table>";

if(!isset($_POST['qubic_token']))
{
	echo '<pre>';
	print_r($GetMiner);
	echo '</pre>';
	exit;
}

$url = 'https://api.qubic.li/Score/Get';
$options = array(
    'http' => array(
        'header'  => "Authorization: Bearer $token\r\n",
        'method'  => 'GET',
        'timeout' => 3
    )
);
$context  		= stream_context_create($options);
$response 		= file_get_contents($url, false, $context);
$networkStat 	= json_decode($response, true);

if(!isset($_POST['qubic_token']))
{
	echo '<pre>';
	print_r($networkStat);
	echo '</pre>';
	exit;
}

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
<br>
Using pool with fixed 85% reward<br><br>
Epoch start / end: <b>" . date('d.m.Y H:i', $curEpochBegin+7200) . " / " . date('d.m.Y H:i', $curEpochEnd+7200) . "</b><br>
Estimated network hashrate: <b>" . number_format($netHashrate, 0, '', ' ') . " it/s</b><br>
Average score: <b>" . number_format($netAvgScores, 1) . "</b>. Per hour: <b>" . number_format($netSolsPerHour, 1) . "</b><br><br>
Qubic price: <b>" . number_format($qubicPrice, 8) . "$</b><br>
Estimated income per 1 it/s per day: <b>" . number_format($incomerPerOneITS, 4) . "$</b><br><br>
Your estimated income per day: <b>" . number_format($myHashrate * $incomerPerOneITS, 2) . "$</b><br>
Estimated income per 1 sol: <b>" . number_format($curSolPrice, 2) . "$</b><br>
Your estimated sols per day: <b>" . number_format(24 * $myHashrate * $netSolsPerHour / $netHashrate, 1) . "</b><br>
<br>
";
if($totalSolutions > 0)
{
	$bd .= "Total on this epoch: <b>" . number_format($curSolPrice * $totalSolutions, 2) . "$</b>";
}
echo json_encode([
	'body' => $bd, 
	'full' => $networkStat, 
	'token' => $token, 
	'epoch_progress' => round(100 * $curEpochProgress, 1), 
	'tb_miners' => $tb_miners, 
	'totalSolutions' => $totalSolutions, 
	'totalIts' => $totalIts,
	'epochNumber' => $epochNumber
]);
?>
