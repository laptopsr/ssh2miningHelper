<?php

if(isset($_POST['getSettings']))
{
    $currentContent = json_decode(file_get_contents("settings.txt"), true);
    echo json_encode($currentContent);
}
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
if(isset($_POST['newMessage']))
{
    $currentContent = file_get_contents("messages.txt");
    $newMessage = $_POST['newMessage'] . "\n";
    $newContent = $newMessage . $currentContent;
    file_put_contents("messages.txt", $newContent);
}
if(isset($_POST['getMessages']))
{
    $currentContent 	= file_get_contents("messages.txt");
    $contentArray 		= explode("\n", $currentContent);
    $firstTwentyLines 	= array_slice($contentArray, 0, $_POST['count']);
    $outputContent 		= implode("\n", $firstTwentyLines);

    echo json_encode($outputContent);
}
if(isset($_POST['removeMessage'])) {

        // Получаем идентификатор сообщения из POST запроса
        $idToRemove = $_POST['id'];

        // Получаем текущее содержимое файла
        $currentContent = file_get_contents("messages.txt");

        // Разбиваем содержимое файла на массив строк
        $lines = explode("\n", $currentContent);

        // Ищем строку, которая содержит идентификатор для удаления и удаляем ее из массива
        $updatedLines = array_filter($lines, function($line) use ($idToRemove) {
            return strpos($line, 'for="'.$idToRemove.'"') === false;
        });

        // Объединяем строки обратно в одну строку
        $updatedContent = implode("\n", $updatedLines);

        // Сохраняем обновленное содержимое в файл
        file_put_contents("messages.txt", $updatedContent);

        // Отправляем обновленное содержимое в формате JSON
        echo json_encode(array('status' => 'success', 'message' => 'Message removed successfully'));
}
exit;
?>
