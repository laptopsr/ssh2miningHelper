<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

include "config.php";

$active_coin_name 	= $_POST['active_coin_name']??'vishai';
$active_address		= $_POST['active_address']??'umWgGLUku8NS3nZ1ufyx9nhaFbqKzpwNrK';

include "rplant_statistic.php";

// ------ //

$bd			= "";
$coins8Symb = [];
$rplant		= true;


// ------ //

$pendingDataBlocks = [];

if($rplant)
{

	foreach($coins as $coin)
	{
		$user = $coin['user'];

		$first_four = substr($user, 0, 4);
		$last_four = substr($user, -4);

		foreach($arr as $v)
		{
			$coins8Symb[$first_four.$last_four.'.'.$v['worker']] = $first_four.$last_four;
			$coins8Symb[$first_four.$last_four] = $first_four.$last_four;
		}
	}


	// https://pool.rplant.xyz/api2/walletEx/reaction/RuR6UEmYByq7u4QVWxkWrkSdEC8mxU283M/111111
	// https://pool.rplant.xyz/api2/poolminer2x/reaction/RuR6UEmYByq7u4QVWxkWrkSdEC8mxU283M/111111

	$url = 'https://pool.rplant.xyz/api/blocks';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	  'accept: application/json'
	));

	$blocks = json_decode(curl_exec($ch), true);
	curl_close($ch);

	if(is_array($blocks))
	{
		foreach($blocks as $k => $v)
		{
			$expl = explode(":", $v);

			if (isset($coins8Symb[$expl[3]]) and ($expl[5] == 'PEND' or $expl[5] == 'WAIT'))
			{
				if($expl[5] == 'WAIT')
				{
					$expl[6] = $expl[6]/1000000000000;
				}

				$pendingDataBlocks[$expl[4]] = $expl;
			}
		}
	}
	krsort($pendingDataBlocks);

	/*
	echo '<pre>';
	print_r($pendingDataBlocks); // $rplant_full[0]['net']['hr']
	echo '</pre>';
	exit;
	*/

	// ------ //

	$myLogFile 	= "dbLog.log";
	$oldData	= json_decode(file_get_contents($myLogFile), true);

	if(is_array($oldData))
	{
		$newData = [];
		foreach($pendingDataBlocks as $k => $v)
		{
			$newData[$k] = $v;
		}
		foreach($oldData as $k => $v)
		{
			if(date("Ymd", $v[4]) < date("Ymd", strtotime("-2 days")))
			{
				//echo date("Ymd", $v[4]).'<br>';
				continue;
			}

			$newData[$k] = $v;
		}

		/*
		echo '<pre>';
		print_r($newData);
		echo '</pre>';
		exit;
		*/

		file_put_contents($myLogFile, json_encode($newData));
	}
	else
	{
		file_put_contents($myLogFile, json_encode($pendingDataBlocks));
	}
	// ------ //

	/*
	<style>
	#my_pending_blocks {
		height: 200px;
		overflow-y: auto;
	}
	</style>
	*/
	
	$bd .= '
	<h4>My pending blocks rplant.xyz</h4>
	<table class="table table-striped">
	<tr class="">
		<th>Wallet</th>
		<th>Time</th>
		<th>Coins</th>
		<th>Effort %</th>
	</tr>';

	foreach($pendingDataBlocks as $k => $v)
	{
		if($v[8] > 100 and $v[8] <= 150)
		{
			$cl = 'bg-info';
		}
		elseif($v[8] > 150 and $v[8] <= 200)
		{
			$cl = 'bg-primary';
		}
		elseif($v[8] > 200)
		{
			$cl = 'bg-danger';
		}
		else
		{
			$cl = 'bg-success';
		}

		$bd .= '
		<tr class="tr_block" shares="'.$v[7].'">
			<td>'.$v[3].'</td>
			<td class="pvm" for="'.date("Y-m-d H:i", $v[4]).'">'.date("H:i", $v[4]).'</td>
			<td>'.round($v[6], 2).'</td>
			<td class="'.$cl.' text-white" align="center">'.$v[8].'%</td>
		</tr>';
	}

	$bd .= '</table>';

	$net_v 		= $rplant_full[0]['net']['v']??0;
	$net_hr 	= $rplant_full[0]['net']['hr']??0;
	$net_d		= $rplant_full[0]['net']['d']??0;
	$hrs		= $rplant_miners['hrs']??0;
	$immature 	= $rplant_miners['immature']??0;
	$balance	= $rplant_miners['balance']??0;
	$paid		= $rplant_miners['paid']??0;
	$soloShares = $rplant_miners['soloShares']??0;
	$wcs		= $rplant_miners['wcs']??0;
	$block_found = $rplant_miners['found']['solo']??0;

	$bd .= '
	<h4>Miner rplant.xyz</h4>
	<table class="table table-striped miner_table">

		<tr class="tr_miner"><td>Network name</td><th><span id="net_v">'.$net_v.'</span></th></tr>
		<tr class="tr_miner"><td>Network hashrate</td><th><span id="net_hr">'.$net_hr.'</span></th></tr>
		<tr class="tr_miner"><td>Network diff</td><th><span id="net_d">'.$net_d.'</span></th></tr>
		<tr class="tr_miner"><td>Hashrate solo</td><th><span id="hrs">'.$hrs.'</span></th></tr>
		<tr class="tr_miner"><td>Immature</td><th>'.$immature.'</th></tr>
		<tr class="tr_miner"><td>Balance</td><th>'.$balance.'</th></tr>
		<tr class="tr_miner"><td>Paid</td><th>'.$paid.'</th></tr>
		<tr class="tr_miner"><td>Shares</td><th><span id="soloShares">'.$soloShares.'</span></th></tr>
		<tr class="tr_miner"><td>Workers</td><th><span id="wcs">'.$wcs.'</span></th></tr>
		<tr class="tr_miner"><td>Solo blocks found</td><th><span id="block_found">'.$block_found.'</span></th></tr>

	</table>';
}

echo json_encode($bd);

//
/*
Array
(
    [miner] => TBs1LZLarJ9b3wrfS3iaZjYSG1a5WVEGWabUm9t5ZawyCG7vKK2gDyzAEvJgwcg9WZgev7E7kKUYoRDQyJe9HCcz2njC2t66ot
    [hr] => 176426
    [hrs] => 176426
    [totalShares] => 0
    [immature] => 0
    [balance] => 111
    [paid] => 187727445800000
    [wc] => 15
    [wcs] => 15
    [found] => Array
        (
            [share] => 0
            [solo] => 6
        )

    [workers] => Array
        (
            [0] => 201:6042:1812528:0:226566:6973
            [1] => 202:3991:1197280:0:299320:7050
            [2] => 203:4168:1250432:0:312608:6896
            [3] => 204:33749:10124664:0:843722:16404
            [4] => 205:5920:1775980:0:355196:9443
            [5] => 207:13333:4000000:0:400000:13632
            [6] => 208:17150:5144870:0:514487:14143
            [7] => 210:12800:3840000:0:640000:13638
            [8] => 211:16459:4937802:0:822967:17751
            [9] => 212:7556:2266668:0:755556:17212
            [10] => 213:18599:5579690:0:557969:14186
            [11] => 214:5012:1503464:0:751732:13973
            [12] => 215:17305:5191407:0:576823:14218
            [13] => 216:4354:1306122:0:653061:14070
            [14] => 217:9990:2996880:0:599376:14663
        )

    [soloShares] => 3825399357
)
*/
?>
