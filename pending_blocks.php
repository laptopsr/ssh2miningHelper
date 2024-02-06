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

	if (isset($coins8Symb[$expl[3]]))
	{
		$pendingDataBlocks[$expl[3]][] = $expl;
	}
}
/*
echo '<pre>';
print_r($pendingDataBlocks);
echo '</pre>';
exit;
*/
// ------ //

// Функция для сравнения значений по ключу [4] в обратном порядке
function compareByTimeDesc($a, $b) {
    return $b[0][4] - $a[0][4];
}

// Сортировка массива по времени в обратном порядке
usort($pendingDataBlocks, 'compareByTimeDesc');

$bd = '<table class="table table-striped">';
foreach($pendingDataBlocks as $kk => $varr)
{
	foreach($varr as $k => $v)
	{
			$bd .= '
			<tr>
				<td>'.$v[3].'</td>
				<td>'.date("d.m.Y H:i", $v[4]).'</td>
				<td>'.round($v[7]).'</td>
				<td>'.$v[8].'%</td>
			</tr>';
	}
}
$bd .= '</table>';

echo json_encode($bd);
?>
