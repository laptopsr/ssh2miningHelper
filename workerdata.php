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
	$time = 0;

	$arWorker = [
		'id' 			=> $v['worker'], 
		'temperature' 	=> '---', 
		'time' 			=> '---', 
		'hashrate' 		=> '---', 
		'pool' 			=> '---', 
		'solutions'		=> 0,
		'session'		=> 'offline', 
	];
	$ping_result = false;

	// --- PING --- //
	if (strcasecmp(substr(PHP_OS, 0, 3), 'WIN') == 0) { // for OS Windows
		$ping_result = shell_exec("ping -n 1 " . $v['host']);
		$ping_result = stripos($ping_result, "Packets: Sent = 1, Received = 1") !== false;
	} else {
		$ping_result = shell_exec("ping -c 1 " . $v['host']);
		$ping_result = stripos($ping_result, "1 packets transmitted, 1 received") !== false;
	}

	if (!$ping_result)
	{
		goto finishWorker;
	}

	try {

		// --- Создаем новый объект SSH2 и подключаемся к серверу --- //
		$ssh = new SSH2($v['host']);
		if (!$ssh->login($v['user'], $v['pass'])) {
			goto finishWorker;
		}

	} catch (\Exception $e) {
		goto finishWorker;
	}

	// OS Windows
	if (isset($v['os']) && strtolower($v['os']) == "win") {

		// --- TEMPERATURES from Open Hardware Monitor --- //
		$ohmPort = $v['port'] ?? 8085;
		try {
			$jd = json_decode(file_get_contents("http://{$v['host']}:{$ohmPort}/data.json"), true); // data from  Web server
		} catch (\Exception $e) {
			goto finishWorker;
		}

		$arWorker['temperature'] = [];
		if ($jd['Text'] == "Sensor" && isset($jd['Children'][0]['Children']))
		{
			foreach($jd['Children'][0]['Children'] as $arDevice) { // CPU, GPU RAM
				if (preg_match('/^(Intel|AMD)\s/iu', $arDevice['Text'])) {
					foreach ($arDevice['Children'] as $arGroup) { // CLocks, Temperatures, Powers
						if (strtolower($arGroup['Text']) == 'temperatures') {
							foreach ($arGroup['Children'] as $arTempers) {
								if (strtolower($arTempers['Text']) == 'cpu package'){
									$arWorker['temperature'][] = '+' . $arTempers['Value'];
								}
							}
						}
					}
				}
			}
		}

		// --- Miners. So far only xmrig ---- //асв
		$command = 'tasklist /FI "IMAGENAME eq xmrig.exe"';
		try {
			$output = $ssh->exec($command);
		} catch (\Exception $e) {
			goto finishWorker;
		}

		if (strpos($output, "xmrig.exe") !== false)
		{
			$arWorker['session']  = "xmrig";
			/*$command = "
			powershell -Command \"
				Select-String -Path '{$v['log_xmrig']}' -Pattern 'accepted' | Select -Last 1 | ForEach-Object{(\$_ -split '\s+')[1]};
				'|'';
				Select-String -Path '{$v['log_xmrig']}' -Pattern 'speed' | Select -Last 1 | ForEach-Object{(\$_ -split '\s+')[5]};
				'|';
				Select-String -Path '{$v['log_xmrig']}' -Pattern 'new job' | Select -Last 1 | ForEach-Object{(\$_ -split '\s+')[6]};
			\"";*/

			try {
				$output = $ssh->exec("powershell -Command \"Select-String -Path '{$v['log_xmrig']}' -Pattern 'accepted' | Select -Last 1 | ForEach-Object{(\$_ -split '\s+')[1]};\"");			
				$expl 	= explode("|", $output);
				$time 	= explode(".", $expl[0]);
				$arWorker['time'] 		= $time[0] ? date("H:i:s", strtotime($time[0])) : '';

				$output = $ssh->exec("powershell -Command \"Select-String -Path '{$v['log_xmrig']}' -Pattern 'speed' | Select -Last 1 | ForEach-Object{(\$_ -split '\s+')[5]};\"");	
				$arWorker['hashrate'] 	= round(((float)$output ?? 0));	

				$output = $ssh->exec("powershell -Command \"Select-String -Path '{$v['log_xmrig']}' -Pattern 'new job' | Select -Last 1 | ForEach-Object{(\$_ -split '\s+')[6]};\"");	
				$arWorker['pool'] 		= trim($output??'');
			} catch (\Exception $e) {
				goto finishWorker;
			}

			goto finishWorker;
		}

	// --- Other OS --- //
	} else { 

		// --- AMD or INTEL --- //
		$command 	= "echo $( timeout 1 echo '' | lscpu | grep Vendor | awk '/Vendor ID:/ {print $3}')";
		$this_CPU	= "---";
		try {

			$output = trim($ssh->exec($command));
			if($output == 'AuthenticAMD'){
				$this_CPU = "AMD";
			} else if($output == 'GenuineIntel'){
				$this_CPU = "INTEL";
			} else {
				$this_CPU = $output;
			}

		} catch (\Exception $e) {
			goto finishWorker;
		}

		// --- TEMPERATURES --- //
		try {
			$command 	= "echo $(timeout 1 sensors 2>/dev/null | awk '/(Tctl|Package id [0-9]):/ {print $0}')";
			$output 	= $ssh->exec($command);

			$temperMatches = [];
			preg_match_all('/\S:\s+(\S+)/iu', $output, $temperMatches);
			if ($temperMatches[1])
				$arWorker['temperature'] = $temperMatches[1];

		} catch (\Exception $e) {
			goto finishWorker;
		}

		// --- QUBIC --- //
		try {
			$command 	= "echo $( timeout 0.5 tail -f $path_qubic_log | grep -m 1 \"avg\" )";
			$output 	= $ssh->exec($command);
			$expl 		= explode("|", $output);
			$first		= explode("INFO", $expl[0]??[]);
			$SOL		= (str_contains(($expl[1] ?? ''), 'SOL:')) ? explode("/", $expl[1] ?? '') : 0;
			$time 		= trim($first[0]??0);

			$timezone 	= new DateTimeZone($time_zone);
			$date 		= new DateTime($time, new DateTimeZone('UTC'));
			$date->setTimezone($timezone);

			if( (time()-strtotime($date->format('Y-m-d H:i:s'))) < 60)
			{
				$arWorker['session']	= "QUBIC";
				$arWorker['time'] 		= $date->format('H:i:s');
				$arWorker['hashrate'] 	= (str_contains(($expl[3] ?? 'it/s'), '')) ? intval(trim($expl[3])) : 0;
				$arWorker['pool'] 		= (str_contains(($first[1] ?? ''), 'E:')) ? ($first[1] ?? '').($expl[1] ?? '') : '';
				$arWorker['solutions']	= isset($SOL[1]) ? (int)$SOL[1] : 0;

				goto finishWorker;
			}

		} catch (\Exception $e) {
			//goto finishWorker;
		}
		//-->

		try {
			$command 	= "echo $( timeout 1 echo '{$v['pass']}' | sudo -S screen -ls | grep -q xmrig && echo \"|xmrig\" || echo \"|false\" )";
			$output 	= $ssh->exec($command);
		} catch (\Exception $e) {
			goto finishWorker;
		}

		$expl 		= explode("|", $output);
		$session 	= trim($expl[1]??'');

		if ($session == "xmrig")
		{
			$arWorker['session'] = $session; // $session.' '.$this_CPU;

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
			
			$arWorker['time'] 		= $time[0] ? date("H:i:s", strtotime($time[0])) : '';
			$arWorker['hashrate'] 	= round(((float)$expl[1] ?? 0));
			$arWorker['pool'] 		= trim($expl[2]??'');
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
			$arWorker['session'] = $session; // $session.' '.$this_CPU;

			if($v['date_format'] == 2)
			{
				$command = "
				echo $( timeout 1 tail -f {$path_syslog} | grep -m 1 \"Accepted\" | awk '/Accepted/ {print $1}' ); 
				echo \"|\"; 
				echo $( timeout 1 tail -f {$path_syslog} | grep -m 1 \"Accepted\" | awk '/Accepted/ {print $9}' ); 
				echo \"|\"; 
				echo $( timeout 1 tail -f {$path_syslog} | grep -m 1 \"network\" | awk '/network/ {print $4}' )
				";
			}
			
			if($v['date_format'] == 1)
			{
				$command = "
				echo $( timeout 1 tail -f {$path_syslog} | grep -m 1 \"Accepted\" | awk '/Accepted/ {print $3}' ); 
				echo \"|\"; 
				echo $( timeout 1 tail -f {$path_syslog} | grep -m 1 \"Accepted\" | awk '/Accepted/ {print $11}' ); 
				echo \"|\"; 
				echo $( timeout 1 tail -f {$path_syslog} | grep -m 1 \"network\" | awk '/network/ {print $6}' )
				";
			}

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

			$arWorker['time'] 		= $time;
			$arWorker['hashrate'] 	= round(((float)$expl[1] ?? 0));;
			$arWorker['pool'] 		= trim($expl[2]??'');
			goto finishWorker;
		}
	}

	// <-- IF no xmrig and no cpuminer then PC is online but this must to be reload. See index.php trbl_worker
	$arWorker['session'] = "OFF";
	
	// FINISH WORKER
	finishWorker: $return[$v['host']] = $arWorker;
}

echo json_encode($return);
exit;

?>
