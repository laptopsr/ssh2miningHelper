<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include "config.php";

$base64String 	= $apiKey . ":" . $apiSecret;
$encodedString 	= base64_encode($base64String);


// --- xeggex TICKERS --- //

$url 	= 'https://api.xeggex.com/api/v2/tickers';
$ch 	= curl_init($url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['accept: application/json', 'Authorization: Basic ' . $encodedString]);
$response = json_decode(curl_exec($ch), true);
curl_close($ch);

$xeggexTickers = [];
if(isset($response[0]['ticker_id']))
{
	//$coinsByasset = [];
	//foreach($coins as $coin){ $coinsByasset[$coin['coin']] = true; }
	foreach($response as $k => $v){
		if(/*isset($coinsByasset[$v['base_currency']]) and */$v['ticker_id'] == $v['base_currency'].'_USDT'){
			$xeggexTickers[$v['base_currency']] = $v;
		
		}
	}
}

/*
    [FSC] => Array
        (
            [ticker_id] => FSC_USDT
            [type] => market
            [base_currency] => FSC
            [target_currency] => USDT
            [last_price] => 0.0001197
            [base_volume] => 13739426.6010
            [target_volume] => 1989.8242
            [usd_volume_est] => 1989.82
            [bid] => 0.00011968
            [ask] => 0.00011971
            [high] => 0.00018532
            [low] => 0.0001018
            [change_percent] => -30.30
        )
*/
/*
echo '<pre>';
print_r($xeggexTickers);
echo '</pre>';
*/

// --- xeggex BALANCE --- //

$url 	= 'https://api.xeggex.com/api/v2/balances'; // https://api.xeggex.com/api/v2/getdeposits?limit=100&skip=1
$ch 	= curl_init($url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['accept: application/json', 'Authorization: Basic ' . $encodedString]);
$response = json_decode(curl_exec($ch), true);
curl_close($ch);


$totalCoinBalance = 0;
$xeggexBalances = [];
if(isset($response[0]['asset']))
{
	foreach($response as $k => $v){
		$v['available'] = (float)$v['available'];
		$v['held'] = (float)$v['held'];
		$xeggexBalances[$v['asset']] = $v;

		if($v['available'] > 0 || $v['held'] > 0){
			$totalCoinBalance += ($v['available'] + $v['held']) * (float)($xeggexTickers[$v['asset']]['last_price']??0);
		}
	}
}

/*
    [BBC] => Array
        (
            [asset] => BBC
            [name] => Babacoin
            [available] => 176376.48018589
            [pending] => 0.00000000
            [held] => 0.00000000
            [assetid] => 634609f3288f134510054724
        )
*/
/*
echo '<pre>';
print_r($xeggexBalances);
echo '</pre>';
*/


// ------ //

//$url = 'https://api.xeggex.com/api/v2/asset/getbyticker/' . $coin['coin'];
$url = 'https://pool.rplant.xyz/api/currencies';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  'accept: application/json'
));

$rplantData = json_decode(curl_exec($ch), true);
curl_close($ch);


// 24h_blocks
// network_hashrate
/*
echo '<pre>';
print_r($rplantData);
echo '</pre>';
*/

$bd = '
<table class="table table-striped coins">
<thead>
	<tr>
		<td><input class="global_select_coin" type="checkbox"></td>
		<th>Coin</th>
		<th>Bln</th>
		<th>Ch(%)</th>
		<th>Price</th>
		<th>Diff</th>
		<th>Rew.</th>
		<th>Efc</th>
	</tr>
</thead>
<tbody>';

$i = 0;
$is_APIok = "false";
foreach($coins as $coin)
{
	$last_price 		= $xeggexTickers[$coin['coin']]['last_price']??0;
	$change_percent 	= $xeggexTickers[$coin['coin']]['change_percent']??0;
	$diff 				= round(($rplantData[$coin['coin']]['difficulty']??'0'), 5);
	$reward 			= round(($last_price * ($rplantData[$coin['coin']]['reward']??'0')), 4);
	$network_hashrate 	= $rplantData[$coin['coin']]['network_hashrate']??'0';
	$available			= $xeggexBalances[$coin['coin']]['available'] ?? 0;
	$held				= $xeggexBalances[$coin['coin']]['held'] ?? 0;
	$balance			= round(($held + $available) * $last_price, 2);
	$efficiency 		= $reward > 0 ? round(($reward/($coin['algo'] == 'randomx' ? $diff/10000000000 : $diff)), 2) : 0;

	$bd .= '
	<tr class="tr_tb" id="tr_coins_'.$coin['coin'].'" coin="'.$coin['coin'].'" network_diff="'.($rplantData[$coin['coin']]['difficulty']??'0').'" network_hashrate="'.$network_hashrate.'" last_price="'.$last_price.'">
		<td><input class="coin_chk" type="checkbox" for="tr_coins_'.$coin['coin'].'"></td>
		<td class="btn btn-xs btn-block btn-info coin" id="coin_'.$coin['coin'].'" ticker="'.$coin['coin'].'" coin_name="'.$coin['coin_name'].'" miner="'.$coin['miner'].'" host="'.$coin['host'].'" algo="'.$coin['algo'].'" user="'.$coin['user'].'" pass="'.$coin['pass'].'" theads="'.$coin['theads'].'" debug="'.$coin['debug'].'">
			'.$coin['coin'].'
		</td>
		<td class="balance" title="'.($held + $available).'">'.$balance.'</td>
		<td class="change_percent '.($change_percent>0? 'text-success' : 'text-danger').'"><b>'.$change_percent.'</b></td>
		<td class="price" title="'.$last_price.'">'.($last_price = strlen($last_price) > 7 ? substr($last_price, 0, 7) . '..' : $last_price).'</td>
		<td class="diff"title="'.$diff.'">'.($diff = strlen($diff) > 7 ? substr($diff, 0, 7) . '..' : $diff).'</td>
		<td class="reward">'.round($reward, 3).'</td>
		<td class="efficiency">'.$efficiency.'</td>
	</tr>';
	
	if(isset($xeggexTickers[$coin['coin']]['last_price']) and $xeggexTickers[$coin['coin']]['last_price'] > 0)
	{
		$is_APIok = "true";
	}
}
$bd .= '</tbody></table>';


echo json_encode([
	'html_data' => $bd,
	'USD_total_xeggex' => round((float)($xeggexBalances['USDT']['available']??0) + (float)($xeggexBalances['USDT']['held']??0), 2),
	'USD_coins_xeggex' => round($totalCoinBalance, 2),
	'is_APIok' => $is_APIok
]);


/*
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.xeggex.com/api/v2/asset/getbyticker/' . $coin['coin']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	  'accept: application/json'
	));

	$response = json_decode(curl_exec($ch), true);
	curl_close($ch);
*/
?>
