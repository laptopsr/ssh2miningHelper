<?php

if(isset($_GET['alertPC']) and !empty($_GET['alertPC']))
{
	include "config.php";

	$connection = ssh2_connect($_GET['alertPC'], 22);
	$output = '';
	if (ssh2_auth_password($connection, $ssh_user, $ssh_pass))
	{
		$stream = ssh2_exec($connection, "aplay beep.wav");
		stream_set_blocking($stream, true);
		$output = stream_get_contents($stream);
	}
}
?>
