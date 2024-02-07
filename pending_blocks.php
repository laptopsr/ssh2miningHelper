<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "config.php";

// ------ //
$coins8Symb = [];
foreach($coins as $coin)
{
	$user = $coin['user'];

	$first_four = substr($user, 0, 4);
	$last_four = substr($user, -4);

	foreach($arr as $v)
	{
		$coins8Symb[$first_four.$last_four.'.'.$v['worker']] = $first_four.$last_four;
	}
}

// ------ //

$url = 'https://pool.rplant.xyz/api/blocks';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  'accept: application/json'
));

$blocks = json_decode(curl_exec($ch), true);
curl_close($ch);

$pendingDataBlocks = [];
foreach($blocks as $k => $v)
{
	$expl = explode(":", $v);

	if (isset($coins8Symb[$expl[3]]) and $expl[5] == 'PEND')
	{
		$pendingDataBlocks[$expl[4]] = $expl;
	}
}

/*
echo '<pre>';
print_r($pendingDataBlocks);
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
		//if($v[5] == "NEW") { continue; }
		$newData[$k] = $v;
	}

	/*
	echo '<pre>';
	print_r($newData);
	echo '</pre>';
	*/
	
	file_put_contents($myLogFile, json_encode($newData));
}
else
{
	file_put_contents($myLogFile, json_encode($pendingDataBlocks));
}
// ------ //

$bd = '<table class="table table-striped">';

foreach($pendingDataBlocks as $k => $v)
{
	$bd .= '
	<tr class="tr_block">
		<td>'.$v[3].'</td>
		<td class="pvm">'.date("Y-m-d H:i", $v[4]).'</td>
		<td>'.round($v[6]).'</td>
		<td>'.$v[8].'%</td>
	</tr>';
}

$bd .= '</table>';

echo json_encode($bd);
?>
