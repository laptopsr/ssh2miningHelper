<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include "config.php";

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
			$prepare = 'killall cpuminer-ryzen; sudo killall xmrig; killall SRBMiner-MULTI; timeout 1 sudo rm -rf /home/laptopsr/xmrig.log; ';

			if($_POST['miner'] == 'xmrig')
			{
				$start = 'timeout 1 sudo screen -dmS xmrig '.$path_xmrig.' --log-file=/home/laptopsr/xmrig.log';
				$prepare .= $start.' -a '.$_POST['algo'].' -o '.$_POST['host'].' -u '.$_POST['user'].'.'.$v['worker'].' -p '.$_POST['pass'].' '.($_POST['theads']=='manual'?' -t '.$v['theads']:'').';';
			}

			if($_POST['miner'] == 'cpuminer')
			{
				$start = 'timeout 1 screen -dmS cpuminer '.$path_cpuminer.' --syslog';
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
		
		$originalConnectionTimeout = ini_get('default_socket_timeout');
		ini_set('default_socket_timeout', 3);

		$connection = ssh2_connect($v['host'], 22);

		ini_set('default_socket_timeout', $originalConnectionTimeout);

		if (ssh2_auth_password($connection, $v['user'], $v['pass']))
		{
			$stream = ssh2_exec($connection, $prepare . $_POST['command']);
			stream_set_blocking($stream, true);
			$output = stream_get_contents($stream);
			fclose($stream);
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
	}
	$bd .= "<a href=\"\" class=\"btn btn-warning btn-block\">Home</a></div>";

	if($_POST['debug'] == "true")
	{
		echo json_encode($bd);
	}
	else
	{
		echo json_encode("OK");
	}
}
?>
