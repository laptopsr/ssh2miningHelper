<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

// Dont forget:  sudo visudo
// username ALL=(ALL) NOPASSWD: ALL

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

		// --- CHECK QUBIC --- //
		/*
		$v['qubick_online'] = "false";
		try {

			// Создаем новый объект SSH2 и подключаемся к серверу
			$ssh = new SSH2($v['host']);
			if (!$ssh->login($v['user'], $v['pass'])) {
				continue;
			}

			$command 	= "echo $( timeout 0.5 tail -f $path_qubic_log | grep -m 1 \"INFO\" | awk '/INFO/ {print $1\" \"$2}' )";
			$output 	= $ssh->exec($command);
			$date 		= strtotime($output);
			$t_diff		= (time() - $date);

			if ($date !== false and $t_diff <= 60)
			{
				$v['qubick_online'] = $t_diff;
			}

		} catch (\Exception $e) {
			continue;
		}
		*/
		// -->

		$prepare = '';
		if($_POST['miner'] != '') // $v['qubick_online'] == "false" and 
		{
			$prepare = 'timeout 1 screen -ls | awk \'{print $1}\' | xargs -I{} screen -X -S {} quit; sudo screen -ls | awk \'/\.xmrig\t/ {print $1}\' | xargs -I{} sudo screen -X -S {} quit; timeout 1 sudo killall xmrig; sudo rm -rf '.$path_xmriglog.'; ';

			if($_POST['miner'] == 'xmrig')
			{
				$start = 'sudo screen -dmS xmrig '.$v['path_xmrig'].' --log-file='.$path_xmriglog.' --randomx-1gb-pages ';
				$prepare .= $start.' -a '.$_POST['algo'].' -o '.$_POST['host'].' -u '.$_POST['user'].'.'.$v['worker'].' -p '.$_POST['pass'].' '.($_POST['theads']=='manual'?' -t '.$v['theads']:'').'; sudo systemctl stop qli --no-block && sudo pkill -f qli;';
			}

			if($_POST['miner'] == 'cpuminer')
			{
				$start = 'screen -dmS cpuminer '.$v['path_cpuminer'].' --syslog';
				$prepare .= $start.' -a '.$_POST['algo'].' -o '.$_POST['host'].' -u '.$_POST['user'].'.'.$v['worker'].' -p '.$_POST['pass'].' '.($_POST['theads']=='manual'?' -t '.$v['theads']:'').'; sudo systemctl stop qli --no-block && sudo pkill -f qli;';
			}
		}
		// ------ //

		$output = '';
		try {

			// Создаем новый объект SSH2 и подключаемся к серверу
			$ssh = new SSH2($v['host']);
			if (!$ssh->login($v['user'], $v['pass'])) {
				continue;
			}

			$output = $ssh->exec($prepare . $_POST['command']);

		} catch (\Exception $e) {
			continue;
		}

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
		echo json_encode(['debug' => $bd, 'return' => "OK", 'post_workers' => $_POST['workers']]);
	}
	else
	{
		echo json_encode(['return' => "OK", 'post_workers' => $_POST['workers'], 'prepare' => $prepare, 'output' => $output]);
	}
}
?>
