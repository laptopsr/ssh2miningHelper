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
		$coins8Symb[$first_four.$last_four.'.'.$v['worker']] = $coin;
	}
}

// ------ //

$myLogFile 	= "dbLog.log";
$get 		= file_get_contents($myLogFile);
$dbData 	= [];
$farr 		= json_decode($get, true);

if(is_array($farr))
{
	foreach($farr as $k => $v)
	{
		if(isset($coins8Symb[$v[3]]))
		{
			if(isset($_GET['day']) and $_GET['day'] == date("Y-m-d", $v[4]))
			{
				if(!isset($dbData[date("Y-m-d", $v[4])][$coins8Symb[$v[3]]['coin']]))
				{
					$dbData[date("Y-m-d", $v[4])][$coins8Symb[$v[3]]['coin']] = 0;
				}

				$dbData[date("Y-m-d", $v[4])][$coins8Symb[$v[3]]['coin']] += $v[6];
			}
		}
	}
}

/*
echo '<pre>';
print_r($dbData);
echo '</pre>';
*/

echo json_encode($dbData);
?>
