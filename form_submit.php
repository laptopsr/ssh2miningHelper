<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

include "config.php";
use phpseclib3\Net\SSH2;

if(isset($_POST['command']))
{
	$bd = "<div class=\"container-fluid\" style=\"margin-top:20px\">";
	foreach($arr as $v)
	{
		if(!in_array($v['worker'], $_POST['workers']))
		{
			continue;
		}

		$prepare = '';
		if($_POST['miner'] != '')
		{
			$prepare = 'timeout 1 screen -ls | awk \'{print $1}\' | xargs -I{} screen -X -S {} quit; sudo screen -ls | awk \'/\.xmrig\t/ {print $1}\' | xargs -I{} sudo screen -X -S {} quit; timeout 1 sudo killall xmrig; timeout 1 sudo rm -rf /home/laptopsr/xmrig.log; ';

			if($_POST['miner'] == 'xmrig')
			{
				$start = 'timeout 1 sudo screen -dmS xmrig '.$v['path_xmrig'].' --log-file=/home/laptopsr/xmrig.log --randomx-1gb-pages ';
				$prepare .= $start.' -a '.$_POST['algo'].' -o '.$_POST['host'].' -u '.$_POST['user'].'.'.$v['worker'].' -p '.$_POST['pass'].' '.($_POST['theads']=='manual'?' -t '.$v['theads']:'').';';
			}

			if($_POST['miner'] == 'cpuminer')
			{
				$start = 'timeout 1 screen -dmS cpuminer '.$v['path_cpuminer'].' --syslog';
				$prepare .= $start.' -a '.$_POST['algo'].' -o '.$_POST['host'].' -u '.$_POST['user'].'.'.$v['worker'].' -p '.$_POST['pass'].' '.($_POST['theads']=='manual'?' -t '.$v['theads']:'').';';
			}

			if($_POST['miner'] == 'srbminer')
			{
				$start = 'timeout 1 screen -dmS srbminer '.$path_srbminer.' --log-file=/home/laptopsr/srbminer.log';
				$prepare .= $start.' --algorithm '.$_POST['algo'].' --pool '.$_POST['host'].' --wallet '.$_POST['user'].'.'.$v['worker'].' --password '.$_POST['pass'].' --keepalive true;';
			}

		}
		// ------ //

		//echo $prepare;
		//exit;

		// Создаем новый объект SSH2 и подключаемся к серверу
		$ssh = new SSH2($v['host']);
		if (!$ssh->login($v['user'], $v['pass'])) {
			die('Login Failed');
		}

		$output = $ssh->exec($prepare . $_POST['command']);

		$bd  .= "
		<div class=\"row\">
			<div class=\"col-md-6\">
				<h3>Input: ".$v['host'] . "</h3>
				$prepare
				<p>------</p>
				<h4>Output: </h4>" . str_replace("\n", "<br>", $output) . "
			</div>
		</div>
		<hr>";
	}
	$bd .= "<a href=\"\" class=\"btn btn-warning btn-block\">Home</a></div>";

	if($_POST['debug'] == "true")
	{
		echo json_encode(['debug' => $bd]);
	}
	else
	{
		echo json_encode("OK");
	}
}
?>
