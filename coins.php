<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "config.php";
include "xeggex_balances.php"; // $xeggexBalances

// ------ //

//$url = 'https://api.xeggex.com/api/v2/asset/getbyticker/' . $coin['coin'];
$url = 'https://pool.rplant.xyz/api/currencies';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  'accept: application/json'
));

$rplantData = json_decode(curl_exec($ch), true);
curl_close($ch);

/*
// 24h_blocks
// network_hashrate

echo '<pre>';
print_r($rplantData);
echo '</pre>';
exit;
*/

$bd = '
<table class="table table-striped coins">
<thead>
	<tr>
		<th>Balance</th>
		<th>Price</th>
		<th>Diff</th>
		<th>Rew.</th>
		<th>Coin</th>
	</tr>
</thead>
<tbody>';

$i = 0;
foreach($coins as $coin)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.xeggex.com/api/v2/asset/getbyticker/' . $coin['coin']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	  'accept: application/json'
	));

	$response = json_decode(curl_exec($ch), true);
	curl_close($ch);
	/*
	echo '<pre>';
	print_r($response);
	echo '</pre>';
	*/

	if($coin['coin'] == 'ZEPH' and isset($response['usdValue']))
	{
		$response['usdValue'] = round($response['usdValue'], 2);
	}

	$usdvalue 	= ($response['usdValue']??'0');
	$diff 		= round(($rplantData[$coin['coin']]['difficulty']??'0'), 5);
	$reward 	= round((($response['usdValue']??'1') * ($rplantData[$coin['coin']]['reward']??'0')), 4);
	$network_hashrate = $rplantData[$coin['coin']]['network_hashrate']??'0';
	$balance	= (isset($xeggexBalances[$coin['coin']]['available'])) ? round(($xeggexBalances[$coin['coin']]['available'] * $usdvalue), 2) : 0;

	$bd .= '
	<tr class="tr_tb" id="tr_coins_'.$coin['coin'].'" coin="'.$coin['coin'].'" network_diff="'.($rplantData[$coin['coin']]['difficulty']??'0').'" network_hashrate="'.$network_hashrate.'">
		<td class="balance">'.$balance.'</td>
		<td class="price">'.$usdvalue.'</td>
		<td class="diff">'.$diff.'</td>
		<td class="reward">'.$reward.'</td>
		<td>
			<button class="btn btn-sm btn-block btn-info coin" id="coin_'.$coin['coin'].'" coin_name="'.$coin['coin_name'].'" miner="'.$coin['miner'].'" host="'.$coin['host'].'" algo="'.$coin['algo'].'" user="'.$coin['user'].'" pass="'.$coin['pass'].'" theads="'.$coin['theads'].'" debug="'.$coin['debug'].'">'.$coin['coin'].'</button>
		</td>
	</tr>';
}
$bd .= '</tbody></table>';

echo json_encode(['html_data' => $bd, 'USD_total_xeggex' => $xeggexBalances['USDT']['available']??0]);
?>
