<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "config.php";

// ------ //

//$url = 'https://api.xeggex.com/api/v2/asset/getbyticker/' . $coin['coin'];
$url = 'https://pool.rplant.xyz/api/currencies';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  'accept: application/json'
));

$rplantData = json_decode(curl_exec($ch), true);
curl_close($ch);
/*
echo '<pre>';
print_r($rplantData);
echo '</pre>';
exit;
*/

$bd = '
<table class="table">
<tr>
	<th>Price $</th>
	<th>Diff</th>
	<th>Reward</th>
	<th>Profit</th>
	<th></th>
</tr>';

$i = 0;
foreach($coins as $coin)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://api.xeggex.com/api/v2/asset/getbyticker/' . $coin['coin']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
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

	$diff 	= round(($rplantData[$coin['coin']]['difficulty']??'0'), 4);
	$reward = round((($response['usdValue']??'1') * ($rplantData[$coin['coin']]['reward']??'0')), 4);

	$bd .= '
	<tr>
		<td>'.($response['usdValue']??'0').'</td>
		<td>'.$diff.'</td>
		<td>'.$reward.'</td>
		<td>'.round(($diff/$reward), 2).'</td>
		<td>
			<button class="btn btn-sm btn-block btn-info coin" id="coin_'.$coin['coin'].'" miner="'.$coin['miner'].'" host="'.$coin['host'].'" algo="'.$coin['algo'].'" user="'.$coin['user'].'" theads="'.$coin['theads'].'">'.$coin['coin'].'</button>
		</td>
	</tr>';
}
$bd .= '</table>';

echo json_encode($bd);
?>
