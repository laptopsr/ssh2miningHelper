<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "config.php";
use phpseclib3\Net\SSH2;

if(isset($_POST['getSettings']))
{
	$currentContent = "";
	if(file_exists("settings.txt"))
	{
    	$currentContent = json_decode(file_get_contents("settings.txt"), true);
    }
    echo json_encode($currentContent);
}
// ------ //
if(isset($_POST['getBlocks']))
{
	if(file_exists("blocks.txt"))
	{
		$day 			= $_POST['day']??date("Ymd");
		$currentContent = file_get_contents("blocks.txt");
		$lines 			= explode("\n", $currentContent);
		$read = [];
		foreach($lines as $line)
		{
			if(!empty($line))
			{
				$l = json_decode($line, true);
				if(isset($l[4]))
				{
					if(date("Ymd", $l[4]) == $day)
					{
						$read[date("d.m.Y", $l[4])][$l[3]][$l[4]] = $l;
					}
				}
			}
		}

		$tulos 	= "<table class=\"table table-striped blocks\">";
		$t 		= false;
		foreach($read as $k => $v)
		{
			$tulos .= "<tr><td colspan=\"6\"><h4 class=\"well bg-secondary text-orange text-center date\">$k</h4></td></tr>";
			foreach($v as $worker => $v2)
			{
				foreach($v2 as $kd => $worker_data)
				{
					$coin = '';

					foreach($worker_data as $kk => $vv){if(isset($vv['coin'])){ $coin = $vv['coin']; }}

					// <-- Offset
					if($coin == "MNN")
					{
						$offset = 100000000000;
					} else if($coin == "TABO")
					{
						$offset = 1000000000000;
					}
					else
					{
						$offset = 1;
					}
					// -->

					$tulos .= "
					<tr class=\"tr_blocks\" for=\"$worker_data[4]\">
						<th>".date("H:i", $worker_data[4])."</th>
						<th title=\"$worker\">".($worker = strlen($worker) > 7 ? substr($worker, 0, 7) . '..' : $worker)."</th>
						<td class=\"rewarded\" worker=\"$worker\" coin=\"".$coin."\">".round(($worker_data[6]/$offset), 2)."</td>
						<td class=\"usdsumm\"></td>
						<td align=\"right\">".$worker_data[8]."%</td>
						<td align=\"right\">".$coin."</td>
					</tr>";
					$t = true;
				}
			}
		}
		$tulos .= "</table>";

		echo json_encode(['return' => ($t) ? $tulos : '']);
	}
	else
	{
		echo json_encode("no data");
	}
}
// ------ //
if(isset($_POST['saveBlock']))
{
	if(!file_exists("blocks.txt"))
	{
		file_put_contents("blocks.txt", "");
	}

	$currentContent = file_get_contents("blocks.txt");

	if (is_array($_POST['set']) and count($_POST['set']) > 0 and $currentContent !== false)
	{
		$newMessage 	= json_encode($_POST['set']) . "\n";
		$newContent 	= $newMessage . $currentContent;
		$lines			= explode("\n", $newContent);

		$arr_line = [];
		foreach($lines as $line)
		{
			$l 	= json_decode($line, true);
			if(isset($l[0]) and $l[5] != "NEW")
			{
				$arr_line[$l[0]] = $line;
			}
		}

		$save_line = "";
		foreach($arr_line as $line)
		{
			/*
			$l 	= json_decode($line, true);
			if(isset($l[0]) and $l[0] == $_POST['set'][0])
			{

			}
			*/
			$save_line .= $line."\n";
		}

		if (strlen($save_line) > strlen($currentContent))
		{
			file_put_contents("blocks.txt", $save_line);
			echo $save_line;
		}
	}
}
// ------ //
if(isset($_POST['saveSettings']))
{
    $currentContent = json_decode(file_get_contents("settings.txt"), true);
	$newContent		= [];

	foreach($currentContent as $k => $v)
	{
		$newContent[$k] = $v;
	}
	if(is_array($_POST['set']))
	{
		foreach($_POST['set'] as $k => $v)
		{
			$newContent[$k] = $v;
		}
	}

    file_put_contents("settings.txt", json_encode($newContent));
    echo "ok";
}
// ------ //
if(isset($_POST['newMessage']))
{
	$newContent = '';

	if(file_exists("messages.txt"))
	{
		$currentContent = file_get_contents("messages.txt");
		$newMessage = $_POST['newMessage'] . "\n";
		$newContent = $newMessage . $currentContent;
	}

	file_put_contents("messages.txt", $newContent);
}
// ------ //
if(isset($_POST['getMessages']))
{
	$outputContent = "";

	if(file_exists("messages.txt"))
	{
		$currentContent 	= file_get_contents("messages.txt");
		$contentArray 		= explode("\n", $currentContent);
		$firstTwentyLines 	= array_slice($contentArray, 0, $_POST['count']);
		$outputContent 		= implode("\n", $firstTwentyLines);
	}
	
	echo json_encode($outputContent);
}
// ------ //
if(isset($_POST['removeAllMessages'])) {

        file_put_contents("messages.txt", "");

        echo json_encode(array('status' => 'success', 'message' => 'Messages removed successfully'));
}
// ------ //
if(isset($_POST['removeMessage'])) {


        $idToRemove 	= $_POST['id'];
        $currentContent = file_get_contents("messages.txt");
        $lines 			= explode("\n", $currentContent);

        $updatedLines = array_filter($lines, function($line) use ($idToRemove) {
            return strpos($line, 'for="'.$idToRemove.'"') === false;
        });

        $updatedContent = implode("\n", $updatedLines);
        file_put_contents("messages.txt", $updatedContent);
        echo json_encode(array('status' => 'success', 'message' => 'Message removed successfully'));
}
// ------ //
if(isset($_GET['doAlert']))
{
	foreach($alertPC as $PC)
	{

		try {
			// Создаем новый объект SSH2 и подключаемся к серверу
			$ssh = new SSH2($PC);
			if (!$ssh->login($ssh_user, $ssh_pass)) {
				continue;
			}

			$output = $ssh->exec("aplay beep.wav");
		} catch (\Exception $e) {

		}
	}
}
exit;
?>
