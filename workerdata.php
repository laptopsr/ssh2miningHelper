<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include "config.php";
use phpseclib3\Net\SSH2;

$return = [];

foreach($arr as $v)
{
	$arWorker = [
		'id' 			=> $v['worker'], 
		'temperature' 	=> '---', 
		'time' 			=> '---', 
		'hashrate' 		=> '---', 
		'pool' 			=> '---', 
		'session'		=> 'offline', 
	];
	$ping_result = false;

	if (strcasecmp(substr(PHP_OS, 0, 3), 'WIN') == 0) { // for OS Windows
		$ping_result = shell_exec("ping -n 1 " . $v['host']);
		$ping_result = stripos($ping_result, "Packets: Sent = 1, Received = 1") !== false;
	} else {
		$ping_result = shell_exec("ping -c 1 " . $v['host']);
		$ping_result = stripos($ping_result, "1 packets transmitted, 1 received") !== false;
	}

	if (!$ping_result)
		goto finishWorker;

	$output = '';

	/*
	temperature:
	"tctl" for AMD
	"Package id" for INTEL XEON
	*/
	$command = "echo $(timeout 1 sensors 2>/dev/null | awk '/(Tctl|Package id [0-9]):/ {print $0}')";
	$temperMatches = [];

	// Создаем новый объект SSH2 и подключаемся к серверу
	$ssh = new SSH2($v['host']);
	if (!$ssh->login($v['user'], $v['pass'])) {
		goto finishWorker;
	}

	$output = $ssh->exec($command);
	preg_match_all('/\S:\s+(\S+)/iu', $output, $temperMatches);
	if ($temperMatches[1])
		$arWorker['temperature'] = $temperMatches[1];



	$command = "echo $( timeout 1 echo '{$v['pass']}' | sudo -S screen -ls | grep -q xmrig && echo \"|xmrig\" || echo \"|false\" )";
	try {
		$output = $ssh->exec($command);
	} catch (\Exception $e) {
		goto finishWorker;
	}
	$expl 		= explode("|", $output);
	$session 	= trim($expl[1]??'');

	if ($session == "xmrig")
	{
		$arWorker['session'] = $session;
		$command = "
		echo $( timeout 1 tail -f {$v['log_xmrig']} | grep -m 1 \"accepted\" | awk '/accepted/ {print $2}' ); 
		echo \"|\"; 
		echo $( timeout 1 tail -f {$v['log_xmrig']} | grep -m 1 \"speed\" | awk '/speed/ {print $6}' ); 
		echo \"|\"; 
		echo $( timeout 1 tail -f {$v['log_xmrig']} | grep -m 1 \"new job\" | awk '/new job/ {print $7}' )
		";
		try {
			$output = $ssh->exec($command);
		} catch (\Exception $e) {
			goto finishWorker;
		}
		
		$expl 	= explode("|", $output);
		$time 	= explode(".", $expl[0]);
		$arWorker['time'] = $time[0] ? date("H:i:s", strtotime($time[0])) : '';
		$arWorker['hashrate'] =  round(((float)$expl[1] ?? 0));
		$arWorker['pool'] = trim($expl[2]??'');
		goto finishWorker;
	}

	$command = "echo $( timeout 1 echo '{$v['pass']}' | screen -ls | grep -q cpuminer && echo \"|cpuminer\" || echo \"|false\" )";
	
	try {
		$output = $ssh->exec($command);
	} catch (\Exception $e) {
		goto finishWorker;
	}
	$expl 		= explode("|", $output);
	$session 	= trim($expl[1]??'');

	if($session == "cpuminer")
	{
		$arWorker['session'] = $session;
		$command = "
		echo $( timeout 1 tail -f {$path_syslog} | grep -m 1 \"Accepted\" | awk '/Accepted/ {print $3}' ); 
		echo \"|\"; 
		echo $( timeout 1 tail -f {$path_syslog} | grep -m 1 \"Accepted\" | awk '/Accepted/ {print $11}' ); 
		echo \"|\"; 
		echo $( timeout 1 tail -f {$path_syslog} | grep -m 1 \"network\" | awk '/network/ {print $6}' )
		";

		try {
			$output = $ssh->exec($command);
		} catch (\Exception $e) {
			goto finishWorker;
		}

		$expl 		= explode("|", $output);
		$time 		= trim($expl[0]??'');

		if (str_contains($time, 'T'))
		{
			$timestamp = strtotime($time);
			$time = date("H:i:s", $timestamp);
		}

		$arWorker['time'] = $time;
		$arWorker['hashrate'] =  round(((float)$expl[1] ?? 0));
		$arWorker['pool'] = trim($expl[2]??'');
		goto finishWorker;
	}
	
	// FINISH WORKER
	finishWorker: $return[$v['host']] = $arWorker;
}

echo json_encode($return);
exit;

?>
