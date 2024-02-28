<?php
include "config.php";
use phpseclib3\Net\SSH2;

if(isset($_POST['getSettings']))
{
    $currentContent = json_decode(file_get_contents("settings.txt"), true);
    echo json_encode($currentContent);
}
// ------ //
if(isset($_POST['getBlocks']))
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
    				$read[date("d.m.Y", $l[4])][$l[3]][] = $l;
    			}
    		}
    	}
    }
    
    $tulos = "<table class=\"table table-striped blocks\"";
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
				<tr>
					<th>".date("H:i", $worker_data[4])."</th>
					<th>".$worker."</th>
					<td class=\"rewarded\" worker=\"$worker\" coin=\"".$coin."\">".round(($worker_data[6]/$offset), 2)."</td>
					<td class=\"usdsumm\"></td>
					<td align=\"right\">".$worker_data[8]."%</td>
					<td align=\"right\">".$coin."</td>
				</tr>";
			}
		}
	}
	$tulos .= "</table>";

	echo json_encode($tulos);
}
// ------ //
if(isset($_POST['saveBlock']))
{
	$currentContent = file_get_contents("blocks.txt");
	$lines 			= explode("\n", $currentContent);

	$is_saved 		= false;
	
	foreach($lines as $line)
	{
    	if(!empty($line))
    	{
    		$l 	= json_decode($line, true);
    		//echo $l[0]. " " .$_POST['set'][0]."\n";
    		if(isset($l[0]) and $l[0] == $_POST['set'][0])
    		{
    			$is_saved = true;
    			break;
    		}
    	}
	}

	if(!$is_saved and isset($_POST['set'][5]) and $_POST['set'][5] != "NEW")
	{
		$newMessage = json_encode($_POST['set']) . "\n";
		$newContent = $newMessage . $currentContent;
		file_put_contents("blocks.txt", $newContent);
		echo "is saved";
	}
	else
	{
		echo "not saved";
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
	$currentContent = file_get_contents("messages.txt");
	$newMessage = $_POST['newMessage'] . "\n";
	$newContent = $newMessage . $currentContent;
	file_put_contents("messages.txt", $newContent);
}
// ------ //
if(isset($_POST['getMessages']))
{
    $currentContent 	= file_get_contents("messages.txt");
    $contentArray 		= explode("\n", $currentContent);
    $firstTwentyLines 	= array_slice($contentArray, 0, $_POST['count']);
    $outputContent 		= implode("\n", $firstTwentyLines);

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