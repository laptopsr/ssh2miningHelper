<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

include "config.php";

$return = [];

foreach($arr as $v)
{

	$ping_result = shell_exec("ping -c 1 " . $v['host']);

	if (!strpos($ping_result, "1 packets transmitted, 1 received") !== false) {
		$return[$v['host']] = [
			'id' 			=> $v['worker'], 
			'temperature' 	=> '---', 
			'time' 			=> '---', 
			'hashrate' 		=> '---', 
			'pool' 			=> '---', 
			'session'		=> 'offline', 
		];
		continue;
	}

	$connection = ssh2_connect($v['host'], 22);
	$output = '';
	if (ssh2_auth_password($connection, $v['user'], $v['pass']))
	{

		// speed real: $6, avg: $14 
		$command = "
		echo $( timeout 1 sensors | awk '/Tctl/ {print $2}' ); 
		echo \"|\"; 
		echo $( timeout 1 tail -f $path_xmriglog  | grep -m 1 \"accepted\" | awk '/accepted/ {print $2}' ); 
		echo \"|\"; 
		echo $( timeout 1 tail -f $path_xmriglog  | grep -m 1 \"speed\" | awk '/speed/ {print $14}' ); 
		echo \"|\"; 
		echo $( timeout 1 tail -f $path_xmriglog  | grep -m 1 \"new job\" | awk '/new job/ {print $7}' )
		echo \"|\"; 
		echo $( timeout 1 sudo screen -ls | grep -q xmrig && echo \"xmrig\" || echo \"false\" )
		";

		$stream = ssh2_exec($connection, $command);
		stream_set_blocking($stream, true);
		$output = stream_get_contents($stream);
		$expl 	= explode("|", $output);
		$time 	= explode(".", $expl[1]??'');

		$return[$v['host']] = [
			'id' 			=> $v['worker'], 
			'temperature' 	=> $expl[0]??'', 
			'time' 			=> isset($time[0])? date("H:i:s", strtotime($time[0])) : '',
			'hashrate' 		=> round(trim($expl[2]??'')),
			'pool' 			=> trim($expl[3]??''),
			'session'		=> trim($expl[4]??''),
		];

		if($return[$v['host']]['session'] == "false")
		{

			$command = "
			echo $( timeout 1 sensors | awk '/Tctl/ {print $2}' ); 
			echo \"|\"; 
			echo $( timeout 1 tail -f $path_syslog | grep -m 1 \"Accepted\" | awk '/Accepted/ {print $3}' ); 
			echo \"|\"; 
			echo $( timeout 1 tail -f $path_syslog | grep -m 1 \"Accepted\" | awk '/Accepted/ {print $11}' ); 
			echo \"|\"; 
			echo $( timeout 1 tail -f $path_syslog | grep -m 1 \"network\" | awk '/network/ {print $6}' )
			echo \"|\"; 
			echo $( timeout 1 screen -ls | grep -q cpuminer && echo \"cpuminer\" || echo \"false\" )
			";

			$stream 	= ssh2_exec($connection, $command);
			stream_set_blocking($stream, true);
			$output 	= stream_get_contents($stream);
			$expl 		= explode("|", $output);
			$session 	= trim($expl[4]??'');
			$time 		= trim($expl[1]??'');

			if (str_contains($time, 'T'))
			{
				$timestamp = strtotime($time);
				$time = date("H:i:s", $timestamp);
			}

			$return[$v['host']] = [
				'id' 			=> $v['worker'], 
				'temperature' 	=> trim($expl[0]??''), 
				'time' 			=> $session != "false" ? $time : "OFF",
				'hashrate' 		=> $session != "false" ? round(trim($expl[2]??'')) : "OFF",
				'pool' 			=> $session != "false" ? trim($expl[3]??'') : "OFF",
				'session' 		=> $session,
			];

			/*
			if($return[$v['host']]['session'] == "false")
			{

				$command = "
				echo $( timeout 1 sensors | awk '/Tctl/ {print $2}' ); 
				echo \"|\"; 
				echo $( timeout 1 tail -f $path_srbminerlog | grep -m 1 \"Accepted\" | awk '/Accepted/ {print $3}' ); 
				echo \"|\"; 
				echo $( timeout 1 tail -f $path_srbminerlog | grep -m 1 \"Accepted\" | awk '/Accepted/ {print $11}' ); 
				echo \"|\"; 
				echo $( timeout 1 tail -f $path_srbminerlog | grep -m 1 \"network\" | awk '/network/ {print $6}' )
				echo \"|\"; 
				echo $( timeout 1 screen -ls | grep -q srbminer && echo \"srbminer\" || echo \"false\" )
				";

				$stream 	= ssh2_exec($connection, $command);
				stream_set_blocking($stream, true);
				$output 	= stream_get_contents($stream);
				$expl 		= explode("|", $output);
				$session 	= trim($expl[4]??'');
				$time 		= trim($expl[1]??'');

				if (str_contains($time, 'T'))
				{
					$timestamp = strtotime($time);
					$time = date("H:i:s", $timestamp);
				}

				$return[$v['host']] = [
					'id' 			=> $v['worker'], 
					'temperature' 	=> trim($expl[0]??''), 
					'time' 			=> $session != "false" ? $time : "OFF",
					'hashrate' 		=> $session != "false" ? round(trim($expl[2]??'')) : "OFF",
					'pool' 			=> $session != "false" ? trim($expl[3]??'') : "OFF",
					'session' 		=> $session,
				];
			}
			*/
		}
		
		fclose($stream);
	}
}

echo json_encode($return);
exit;

?>
