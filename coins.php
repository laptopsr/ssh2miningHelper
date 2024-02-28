<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "config.php";

// --- xeggex BALANCE --- //

$base64String 	= $apiKey . ":" . $apiSecret;
$encodedString 	= base64_encode($base64String);

$url 	= 'https://api.xeggex.com/api/v2/balances'; // https://api.xeggex.com/api/v2/getdeposits?limit=100&skip=1
$ch 	= curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['accept: application/json', 'Authorization: Basic ' . $encodedString]);
$response = json_decode(curl_exec($ch), true);
curl_close($ch);

$xeggexBalances = [];
if(isset($response[0]['asset']))
{
	foreach($response as $k => $v){if($v['available'] > 0){$xeggexBalances[$v['asset']] = $v;}}
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

// --- xeggex TICKERS --- //

$url 	= 'https://api.xeggex.com/api/v2/tickers';
$ch 	= curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['accept: application/json', 'Authorization: Basic ' . $encodedString]);
$response = json_decode(curl_exec($ch), true);
curl_close($ch);

$xeggexTickers = [];
if(isset($response[0]['ticker_id']))
{
	$coinsByasset = [];
	foreach($coins as $coin){ $coinsByasset[$coin['coin']] = true; }
	foreach($response as $k => $v){if(isset($coinsByasset[$v['base_currency']]) and $v['ticker_id'] == $v['base_currency'].'_USDT'){$xeggexTickers[$v['base_currency']] = $v;}}
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

// ------ //

//$url = 'https://api.xeggex.com/api/v2/asset/getbyticker/' . $coin['coin'];
$url = 'https://pool.rplant.xyz/api/currencies';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
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
exit;
*/

$bd = '
<table class="table table-striped coins">
<thead>
	<tr>
		<td><input class="global_select_coin" type="checkbox"></td>
		<th>Balance</th>
		<th>Change</th>
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
	$last_price 	= $xeggexTickers[$coin['coin']]['last_price']??0;
	$change_percent = $xeggexTickers[$coin['coin']]['change_percent']??0;
	$diff 			= round(($rplantData[$coin['coin']]['difficulty']??'0'), 5);
	$reward 		= round(($last_price * ($rplantData[$coin['coin']]['reward']??'0')), 4);
	$network_hashrate = $rplantData[$coin['coin']]['network_hashrate']??'0';
	$avaible		= round(($xeggexBalances[$coin['coin']]['available'] ?? 0), 2);
	$balance		= $avaible > 0 ? round(($avaible * $last_price), 2) : 0;

	$bd .= '
	<tr class="tr_tb" id="tr_coins_'.$coin['coin'].'" coin="'.$coin['coin'].'" network_diff="'.($rplantData[$coin['coin']]['difficulty']??'0').'" network_hashrate="'.$network_hashrate.'" last_price="'.$last_price.'">
		<td><input class="coin_chk" type="checkbox" for="tr_coins_'.$coin['coin'].'"></td>
		<td class="balance" title="'.$avaible.'">'.$balance.'</td>
		<td class="change_percent '.($change_percent>0? 'text-success' : 'text-danger').'"><b>'.$change_percent.'%</b></td>
		<td class="price">'.$last_price.'</td>
		<td class="diff">'.$diff.'</td>
		<td class="reward">'.$reward.'</td>
		<td class="btn btn-block btn-info coin" id="coin_'.$coin['coin'].'" ticker="'.$coin['coin'].'" coin_name="'.$coin['coin_name'].'" miner="'.$coin['miner'].'" host="'.$coin['host'].'" algo="'.$coin['algo'].'" user="'.$coin['user'].'" pass="'.$coin['pass'].'" theads="'.$coin['theads'].'" debug="'.$coin['debug'].'">
			'.$coin['coin'].'
		</td>
	</tr>';
}
$bd .= '</tbody></table>';

echo json_encode(['html_data' => $bd, 'USD_total_xeggex' => $xeggexBalances['USDT']['available']??0]);


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
