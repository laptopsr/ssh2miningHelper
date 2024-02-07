<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

include "config.php";

// ------ //

// -- Дальше идет код программы -- //
if(isset($_POST['getData']))
{
	$return = [];

	foreach($arr as $v)
	{
		//if($v['worker'] != 246){ continue; }

		$connection = ssh2_connect($v['host'], 22);
		$output = '';
		if (ssh2_auth_password($connection, $v['user'], $v['pass']))
		{

			if($v['worker'] == 246)
			{
				$command = "
				echo $( timeout 1 sensors | grep \"Core 0\" | awk '{print $3}' ); 
				echo \"|\"; 
				echo $( timeout 1 tail -f $path_xmriglog  | grep -m 1 \"accepted\" | awk '/accepted/ {print $1}' ); 
				echo \"|\"; 
				echo $( timeout 1 tail -f $path_xmriglog  | grep -m 1 \"speed\" | awk '/speed/ {print $6}' ); 
				echo \"|\"; 
				echo $( timeout 1 tail -f $path_xmriglog  | grep -m 1 \"new job\" | awk '/new job/ {print $7}' )
				echo \"|\"; 
				echo $( timeout 1 sudo screen -ls | grep -q xmrig && echo \"xmrig\" || echo \"false\" )
				";
			}
			else
			{
				$command = "
				echo $( timeout 1 sensors | awk '/Tctl/ {print $2}' ); 
				echo \"|\"; 
				echo $( timeout 1 tail -f $path_xmriglog  | grep -m 1 \"accepted\" | awk '/accepted/ {print $2}' ); 
				echo \"|\"; 
				echo $( timeout 1 tail -f $path_xmriglog  | grep -m 1 \"speed\" | awk '/speed/ {print $6}' ); 
				echo \"|\"; 
				echo $( timeout 1 tail -f $path_xmriglog  | grep -m 1 \"new job\" | awk '/new job/ {print $7}' )
				echo \"|\"; 
				echo $( timeout 1 sudo screen -ls | grep -q xmrig && echo \"xmrig\" || echo \"false\" )
				";
			}

			$stream = ssh2_exec($connection, $command);
			stream_set_blocking($stream, true);
			$output = stream_get_contents($stream);

			$output = str_replace("[", "", $output);
			$output = str_replace("]", "", $output);
			$expl 	= explode("|", $output);
			$time 	= explode(".", $expl[1]??'');

			$return[$v['host']] = [
				'id' 			=> $v['worker'], 
				'temperature' 	=> $expl[0]??'', 
				'time' 			=> trim($time[0]??''),
				'hashrate' 		=> round(trim($expl[2]??'')),
				'pool' 			=> trim($expl[3]??''),
				'session'		=> trim($expl[4]??''),
			];

			if($return[$v['host']]['session'] == "false")
			{

				if($v['worker'] == 246)
				{
					$command = "
					echo $( timeout 1 sensors | grep \"Core 0\" | awk '{print $3}' ); 
					echo \"|\"; 
					echo $( timeout 1 tail -f $path_syslog | grep -m 1 \"Accepted\" | awk '/Accepted/ {print $1}' ); 
					echo \"|\"; 
					echo $(timeout 1 tail -f $path_syslog | grep -m 1 \"Accepted\" | awk '/Accepted/ {print $9}' ); 
					echo \"|\"; 
					echo $( timeout 1 tail -f $path_syslog | grep -m 1 \"network\" | awk '/network/ {print $4}' )
					echo \"|\"; 
					echo $( timeout 1 screen -ls | grep -q cpuminer && echo \"cpuminer\" || echo \"false\" )
					";
				}
				else
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
				}

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
			
			fclose($stream);
		}
	}

	echo json_encode($return);
	exit;
}

if(isset($_POST['command']))
{
	$bd = "<div class=\"container-fluid\" style=\"margin-top:20px\">";
	foreach($arr as $v)
	{
		$prepare = '';
		if($_POST['miner'] != '')
		{
			$prepare = 'killall cpuminer-sse2; sudo killall xmrig; ';

			if($_POST['miner'] == 'xmrig')
			{
				$start = 'timeout 1 sudo rm -rf /home/laptopsr/xmrig.log; timeout 1 sudo screen -dmS xmrig '.$path_xmrig.' --log-file=/home/laptopsr/xmrig.log';
			}

			if($_POST['miner'] == 'cpuminer-sse2')
			{
				$start = 'timeout 1 sudo rm -rf /home/laptopsr/xmrig.log; timeout 1 screen -dmS cpuminer '.$path_cpuminer.' --syslog';
			}

			$prepare .= $start.' -a '.$_POST['algo'].' -o '.$_POST['host'].' -u '.$_POST['user'].'.'.$v['worker'].' -p '.$_POST['pass'].' '.($_POST['theads']=='manual'?' -t '.$v['theads']:'').';';
		}
		// ------ //

		//echo $prepare;
		//exit;
		
		$originalConnectionTimeout = ini_get('default_socket_timeout');
		ini_set('default_socket_timeout', 3);

		$connection = ssh2_connect($v['host'], 22);

		ini_set('default_socket_timeout', $originalConnectionTimeout);

		if (ssh2_auth_password($connection, $v['user'], $v['pass']))
		{
			$stream = ssh2_exec($connection, $prepare . $_POST['command']);
			stream_set_blocking($stream, true);
			$output = stream_get_contents($stream);
			fclose($stream);
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
	}
	$bd .= "<a href=\"\" class=\"btn btn-warning btn-block\">Home</a></div>";

	if($_POST['debug'] == "true")
	{
		echo $bd;
	}
	else
	{
		header('Location: index.php');
	}
}
?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://momentjs.com/downloads/moment-with-locales.min.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/fontawesome.min.css" />  
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css" /> 
<style>
body{
	background: #ccc;
}
.coin{
	margin-bottom: 5px;
}
.hashrateSum{
	color: green;
	text-align: center;
}

</style>
</head>
<body>
	<div class="container-fluid" style="margin-top: 30px;">
	<center><h2>Mining helpper for xmrig & cpuminer-rplant</h2></center>
	<br>
		<div class="row">
			<div class="col-md-3" style="background: #ddd">
				<center><h2 class="hashrateSum"><span id="hashrateSum">----</span> H/s</h2></center>
				<hr>
				<h4>My pending blocks</h4>
				<div id="my_pending_blocks">
					<span class="btn btn-sm btn-block btn-secondary text-white">waiting..</span>
				</div>
				<hr>
				<form id="lomake" method="POST">
					<select name="debug" class="form-control">
						<option value="false">Debug false</option>
						<option value="true">Debug true</option>
					</select>
					<select name="miner" class="form-control">
						<option value="">Select miner</option>
						<option value="xmrig">xmrig</option>
						<option value="cpuminer-sse2">cpuminer-sse2</option>
					</select>
					<input type="text" class="form-control" name="host" placeholder="Host">
					<input type="text" class="form-control" name="algo" placeholder="Algo">
					<input type="text" class="form-control" name="user" placeholder="User">
					<input type="text" class="form-control" name="pass" placeholder="Pass" value="m=solo">
					<select name="theads" class="form-control">
						<option value="auto">Theads auto</option>
						<option value="manual">Theads from array</option>
					</select>
					<br>
					<input type="text" class="form-control" name="command" placeholder="Command">
					<br>
					<button class="btn btn-info btn-block" type="submit">OK</button>
				</form>
			</div>
			<div class="col-md-3">
				<select id="systemControl" class="form-control">
					<option value="auto">AUTO</option>
					<option value="manual">MANUAL</option>
				</select>
				<div id="allCoins"></div>
			</div>
			<div class="col-md-6" style="background: #ddd">
				<div id="all_computers">
					<table class="table">
					<tr>
						<th>Worker</th>
						<th>Temperature</th>
						<th>Time</th>
						<th>Hashrate</th>
						<th>Mining pool</th>
						<th>Session</th>
					</tr>
					<?php
					foreach($arr as $v)
					{
						echo '
						<tr id="'.$v['worker'].'">
							<th class="host">'.$v['host'].'</th>
							<td class="temperature"><span class="btn btn-sm btn-block btn-secondary text-white">waiting..</span></td>
							<td class="time"><span class="btn btn-sm btn-block btn-secondary text-white">waiting..</span></td>
							<td class="hashrate"><span class="btn btn-sm btn-block btn-secondary text-white">waiting..</span></td>
							<td class="pool"><span class="btn btn-sm btn-block btn-secondary text-white">waiting..</span></td>
							<td class="session"><span class="btn btn-sm btn-block btn-secondary text-white">waiting..</span></td>
						</tr>';
					}
					?>
					</table>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

<script>
$(document).ready(function(){

	var systemControl	= localStorage.getItem('systemControl')??'manual';
    var lastClickedCoin = localStorage.getItem('lastClickedCoin');

	$("#systemControl").val(systemControl);

	$(document).delegate("#systemControl", "change",function(){
		localStorage.setItem('systemControl', $(this, 'option;selected').val());
		systemControl = $(this, 'option;selected').val();
	});

	$(document).delegate(".coin", "click",function(){
	
		$( ".coin" ).removeClass('btn-success text-white').addClass('btn-info');
		$( this ).removeClass('btn-info').addClass('btn-success text-white');
		
		localStorage.setItem('lastClickedCoin', $(this).attr('id'));
		
		$("#lomake select[name='miner']").val($(this).attr('miner'));
		$("#lomake input[name='host']").val($(this).attr('host'));
		$("#lomake input[name='algo']").val($(this).attr('algo'));
		$("#lomake input[name='user']").val($(this).attr('user'));
		$("#lomake select[name='theads']").val($(this).attr('theads'));

		setTimeout(function() { 
    		$("#lomake").submit();
		}, 1000);

	});

	allCoins();

	function allCoins() {

		$.ajax({
		    url: 'coins.php',
		    method: 'POST',
		    data: { getData : true },
		    success: function(data) {
		        data = JSON.parse(data);
				$("#allCoins").html(data);

				if (lastClickedCoin) {
					$("#" + lastClickedCoin).removeClass('btn-info').addClass('btn-success text-white');
				}

		        var rows = $(".tr_tb");

		        // Инициализируем переменные для хранения индекса строки с наименьшей сложностью и наибольшей выплатой
		        var minDiffIndex = -1;
		        var maxReward = 0;

		        // Находим строку с самой высокой выплатой и наименьшей сложностью
		        rows.each(function(index) {
		            var diff = parseFloat($(this).find(".diff").text());
		            var reward = parseFloat($(this).find(".reward").text());

		            if (minDiffIndex === -1 || (diff < parseFloat(rows.eq(minDiffIndex).find(".diff").text()) && reward > maxReward)) {
		                minDiffIndex = index;
		                maxReward = reward;
		            }
		        });

		        // Подсвечиваем строку с наибольшей выплатой и наименьшей сложностью
		        if (minDiffIndex !== -1) {
		            rows.eq(minDiffIndex).addClass("bg-secondary text-white best");
		            
		            // <-- AUTO
		            if(systemControl == "auto" && !$("#" + lastClickedCoin).closest('tr').hasClass('best'))
		            {
		            	rows.eq(minDiffIndex).find('button').click();
		            }
		            // AUTO -->
		        }

		    },
		    error: function(xhr, status, error) {
		        console.error('Ошибка при выполнении запроса:', error);
		    }
		});
		
	}

	setInterval(allCoins, 120000);


	pendingBlocks();

	function pendingBlocks() {

		$.ajax({
		    url: 'pending_blocks.php',
		    method: 'POST',
		    data: { getData : true },
		    success: function(data) {
		        data = JSON.parse(data);
				$("#my_pending_blocks").html(data);
				
				$('.tr_block').each(function(){
				    // Получаем значение времени из ячейки с классом pvm
				    var timeString = $(this).find('.pvm').text();
				    // Преобразуем строку времени в объект Date
				    var time = new Date(timeString);
				    // Получаем текущее время
				    var currentTime = new Date();
				    // Разница между текущим временем и временем в ячейке pvm в минутах
				    var diffMinutes = (currentTime - time) / (1000 * 60);

				    // Если разница меньше 10 минут, добавляем класс highlight
				    if (diffMinutes <= 10) {
				        $(this).addClass('bg-success text-white');
				    }
				});

		    },
		    error: function(xhr, status, error) {
		        console.error('Ошибка при выполнении запроса:', error);
		    }
		});
	}

	setInterval(pendingBlocks, 120000);

	// Функция для отправки AJAX-запроса
	function sendAjaxRequest() {

		$.ajax({
		    url: '#',
		    method: 'POST',
		    data: { getData : true },
		    success: function(data) {
		        console.log("Sended:");
		        data = JSON.parse(data);

				$.each(data, function(index, value) {
					//console.log(index + ": " + value);
					if(value['temperature'] && value['temperature'] != '')
					{
						$("#" + value['id']).find('.temperature').html(value['temperature']);
					}
					if(value['hashrate'] && value['hashrate'] != '')
					{
						$("#" + value['id']).find('.hashrate').html(value['hashrate']);
					}
					if(value['pool'] && value['pool'] != '')
					{
						$("#" + value['id']).find('.pool').html(value['pool']);
					}
					if(value['time'] && value['time'] != '')
					{
						$("#" + value['id']).find('.time').html(value['time']);
					}
					if(value['session'] && value['session'] != '')
					{
						$("#" + value['id']).find('.session').html(value['session']);
					}

					if(systemControl == "auto" && value['time'] && value['time'] == 'OFF')
					{
						$("#allCoins").find('.best').find('button').click();
						return false;
					}
				});
		    },
		    error: function(xhr, status, error) {
		        console.error('Ошибка при выполнении запроса:', error);
		    }
		});

		var sum = 0;
		$('.hashrate').each(function(index, element) {

			if(parseInt($(element).text()) > 0)
			{
				sum += parseInt($(element).text());
			}
		});
		$("#hashrateSum").html(sum);
		
		$('.time').each(function(index, element) {
			// Получаем текущее время
			var currentTime = new Date();

			// Разбиваем строку времени на часы, минуты и секунды
			var timeParts = $(element).text().split(":");
			var hours = parseInt(timeParts[0], 10);
			var minutes = parseInt(timeParts[1], 10);
			var seconds = parseInt(timeParts[2], 10);

			// Создаем новый объект даты с текущей датой и временем из строки
			var specifiedTime = new Date();
			specifiedTime.setHours(hours);
			specifiedTime.setMinutes(minutes);
			specifiedTime.setSeconds(seconds);

			// Добавляем 10 минут к устаревшему времени
			var outdatedTime = new Date(specifiedTime.getTime() + 5 * 60000); // 60000 миллисекунд в минуте

			// Сравниваем текущее время с устаревшим временем
			if (currentTime > outdatedTime || $(element).text() == "OFF") {
				$( this ).closest('tr').addClass('bg-danger text-white');
			} else {
				$( this ).closest('tr').removeClass('bg-danger text-white');
			}

		});
	}

	// Запуск функции sendAjaxRequest() каждые 10 секунд
	setInterval(sendAjaxRequest, 30000);
});
</script>
