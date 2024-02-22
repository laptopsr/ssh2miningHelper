<?php
if(isset($_POST['newMessage']))
{
    $currentContent = file_get_contents("messages.txt");
    $newMessage = $_POST['newMessage'] . "\n";
    $newContent = $newMessage . $currentContent;
    file_put_contents("messages.txt", $newContent);
}
if(isset($_POST['getMessages']))
{
	// $_POST['count']
    $currentContent = file_get_contents("messages.txt");
    echo json_encode(str_replace("\n", "<br>", $currentContent));
}
exit;
?>
