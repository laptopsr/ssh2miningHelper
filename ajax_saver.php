<?php
if(isset($_POST['getSettings']))
{
    $currentContent = json_decode(file_get_contents("settings.txt"), true);
    echo json_encode($currentContent);
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
	include "config.php";

	$connection = ssh2_connect($alertPC, 22);
	$output = '';
	if (ssh2_auth_password($connection, $ssh_user, $ssh_pass))
	{
		$stream = ssh2_exec($connection, "aplay beep.wav");
		stream_set_blocking($stream, true);
		$output = stream_get_contents($stream);
	}
}
exit;
?>
