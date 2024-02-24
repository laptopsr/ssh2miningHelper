<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

include "config.php";

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
	print_r($offline_workers); // $offline_workers, $workers_online, $blocks, $rplant_miners, $rplant_full[0]['net']['hr']
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

	$bd .= '
	<h4 class="text-orange">Rplant pending</h4>
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
}

echo json_encode($bd);

?>
