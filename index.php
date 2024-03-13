<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include "config.php";
use phpseclib3\Net\SSH2;
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

	<!-- DataTable -->
	<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/fontawesome.min.css" />  
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css" /> 

	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<?php
	//unset($_SESSION['login']);

	if(isset($_POST['login']) and $_POST['login'] == $web_login and $_POST['salasana'] == $web_pass)
	{
		$_SESSION['login'] 	= true;
		$_SESSION['demo'] 	= false;

		header('Location: index.php');
	} else if(isset($_POST['login']) and $_POST['login'] == "demo" and $_POST['salasana'] == "demo")
	{
		$_SESSION['login'] 	= true;
		$_SESSION['demo'] 	= true;

		header('Location: index.php');
	}

	if(!isset($_SESSION['login']))
	{
		echo '
		<div class="row text-center">
		 <div class="col-md-12">
		  <center>
		   <div class="col-md-4">
			<form class="form-signin" method="POST">
			  <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
			  <label for="inputEmail" class="sr-only">Email address</label>
			  <input type="text" name="login" class="form-control" placeholder="Username" required autofocus>
			  <label for="inputPassword" class="sr-only">Password</label>
			  <input type="password" name="salasana" id="inputPassword" class="form-control" placeholder="Password" required>
			  <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
			  <p class="mt-5 mb-3 text-muted">&copy; 2024</p>
			</form>
			</center>
		   </div>
		 </div>
		</div>';
		exit;
	}

	if($_SESSION['demo'])
	{
		echo '
		<style>
		input, select, .submit_form, button.coin, .rebootAll {
			pointer-events: none;
			opacity: 0.7;
		}
		</style>
		';
	}
	?>
	<div class="popup_container">
		<div class="bougasetun">
			<button class="btn btn-danger btn-sm clear_button pull-right">Clear all</button>
			<button class="btn btn-info btn-sm close_button pull-right">Close</button>
			<div id="popup_content"></div>
		</div>
	</div>
	<script>
	$(document).ready(function() {
		$(document).delegate(".open_button", "click",function(){
			$('.popup_container').addClass('bougasetun-open');
		});
		$(document).delegate(".close_button", "click",function(){
			$('.popup_container').removeClass('bougasetun-open');
		});
	});
	</script>

	<div class="container-fluid" style="margin-top: 20px;">
		<center>
			<div id="blockFoundDiv"></div>
			<div id="debugResponse"></div>

			<div class="progress forRplant">
				<div id="cur_effort" class="progress-bar progress-bar-striped bg-secondary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="300"></div>
			</div>
			<div class="progress forHerominers">
				<div id="h_cur_effort" class="progress-bar progress-bar-striped bg-secondary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="300"></div>
			</div>
			<div class="progress forQubic">
				<div id="cur_epoch" class="progress-bar progress-bar-striped bg-secondary" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
		</center>
		<small id="softVersion">STAN Version: 5</small>
		<div class="row">
			<div class="col-md-3">
				<div class="forRplant">
					<div class="well bg-secondary text-orange text-center hrs"></div>
					<br>
					<table class="table table-striped miner_table">
						<tr><td>Network name</td><th><span class="rplant_field" id="v"></span></th></tr>
						<tr><td>Network hashrate</td><th><span class="rplant_field" id="hr"></span></th></tr>
						<tr><td>Network diff</td><th><span class="rplant_field" id="d"></span></th></tr>
						<tr><td>Hashrate solo</td><th><span class="rplant_field" id="hrs"></span></th></tr>
						<tr><td>Immature</td><th><span class="rplant_field" id="immature"></span></th></tr>
						<tr><td>Balance</td><th><span class="rplant_field" id="balance"></span></th></tr>
						<tr><td>Paid</td><th><span class="rplant_field" id="paid"></span></th></tr>
						<tr><td>Shares</td><th><span class="rplant_field" id="soloShares"></span></th></tr>
						<tr><td>Workers</td><th><span class="rplant_field" id="wcs"></span></th></tr>
						<tr><td>Solo blocks found</td><th><span class="rplant_field" id="block_found"></span></th></tr>
						<tr><td>Offline workers</td><th><span class="rplant_field" id="wcs_offline"></span></th></tr>
					</table>
				</div>
				<div class="well forRplant" id="getBlocks"></div>
				<div id="herominers_data">Please wait..</div>
				<div id="qubic_data"></div>
				<div id="tb_miners"></div>
				<div id="qubic_stat"></div>
				<hr>
				<div id="moneyToday"></div>
			</div>
			<div class="col-md-4">
				<div id="countdown" class="well bg-danger"></div>
				<div class="well bg-secondary text-orange text-center"><h2>XEGGEX</h2></div>
				<div id="cur_balance" class="well bg-secondary text-orange text-center"></div>
				<br>
				<!-- autoChangeEvery -->
				<div class="forRplant">
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<select id="autoChangeEvery" class="form-control">
								<option value="1" selected>Autochange every 1 min.</option>
								<option value="2">Autochange every 2 min.</option>
								<option value="5">Autochange every 5 min.</option>
								<option value="20">Autochange every 20 min.</option>
								<option value="60">Autochange every 1 hour</option>
								<option value="120">Autochange every 2 hours</option>
								<option value="300">Autochange every 5 hours</option>
							</select>
						</div>
						<button class="btn btn-secondary" id="selected_coins_button">No coins selected</button>
					</div>
				</div>
				<!-- autoStartAndStop -->
				<div class="forRplant">
					<div class="input-group mb-3">
						<div class="input-group-prepend">
							<input type="text" id="autoStartAndStop_start_time" size="8" placeholder="12:00">
						</div>
						<select id="autoStartAndStop_start_coin" class="form-control">
						<?php
						foreach($coins as $coin)
						{
							echo '<option value="'.$coin['coin'].'">'.$coin['coin'].'</option>';
						}
						?>
						</select>
						<div class="input-group-prepend">
							<input type="text" id="autoStartAndStop_stop_time" size="8" placeholder="14:00">
						</div>
						<select id="autoStartAndStop_stop_coin" class="form-control">
						<?php
						foreach($coins as $coin)
						{
							echo '<option value="'.$coin['coin'].'">'.$coin['coin'].'</option>';
						}
						?>
						</select>
						<button class="btn btn-success" id="start_schedule_button">Start schedule</button>
					</div>
				</div>
				<div id="is_coins_update"></div>
				<div id="allCoins"></div>
			</div>
			<div class="col-md-5">
				<div id="all_computers">
					<div class="well bg-secondary text-orange text-center"><h2>Home: <b><span id="hashrateSum"></span> H/s</b><span id="mySolutions"></span></h2></div>
					<br>
					<table class="table table-striped">
					<tr>
						<td><input class="global_select" type="checkbox" checked></td>
						<th>Worker</th>
						<th>Temp.</th>
						<th>Time</th>
						<th>H/s</th>
						<th>Pool</th>
						<th>Session</th>
					</tr>
					<?php
					$allMyWorkers = [];
					foreach($arr as $v)
					{
						$allMyWorkers[$v['worker']] = $v;

						if(!isset($last_model) or (isset($last_model) and $last_model != $v['model']))
						{
							echo '
							<tr class="model">
								<td colspan="7" class="bg-secondary"><b>'.$v['model'].'</b></td>
							</tr>
							';
						}

						echo '
						<tr id="worker_'.$v['worker'].'" class="worker_tr" worker="'.$v['worker'].'">
							<td><input class="worker_chk" type="checkbox" checked></td>
							<th class="host">'.$v['host'].'</th>
							<td class="temperature ajaxdata"></td>
							<td class="time ajaxdata"></td>
							<td class="hashrate ajaxdata"></td>
							<td class="pool ajaxdata"></td>
							<td class="session ajaxdata" align="center"></td>
						</tr>';
						
						$last_model = $v['model'];
					}
					?>
					</table>
					<h5>
						With selected: 
							<button class="btn btn-info btn-sm rebootAll">Reboot</button> 
							<button class="btn btn-info btn-sm clrScreen">Reload miner</button>
							<button class="btn btn-info btn-sm updateSystem">Update system</button>
							<button class="btn btn-info btn-sm shutDown">Shutdown now</button>
							<button class="btn btn-info btn-sm stopQUBIC">Stop QUBIC</button>
							<button class="btn btn-info btn-sm startQUBIC">Start QUBIC</button>
					</h5>
					<select id="workersControl" class="form-control">
						<option value="auto">AUTO RELOADER</option>
						<option value="manual">MANUAL</option>
					</select>
				</div>
				<button class="btn btn-secondary btn-block" type="button" data-toggle="collapse" data-target="#collapseForm" aria-expanded="false" aria-controls="collapseForm">
					Show form
				</button>
				<div class="collapse" id="collapseForm">
					<form id="lomake" method="POST">
						<select name="debug" id="debug" class="form-control">
							<option value="false">Debug false</option>
							<option value="true">Debug true</option>
						</select>
						<select name="miner" class="form-control">
							<option value="">Select miner</option>
							<option value="xmrig">xmrig</option>
							<option value="cpuminer">cpuminer</option>
							<option value="srbminer">srbminer</option>
						</select>
						<select name="workers[]" id="lomake_workers" class="form-control selectpicker" multiple>
						<?php
						foreach($arr as $worker)
						{
							echo '<option value="'.$worker['worker'].'" selected>'.$worker['host'].'</option>';
						}
						?>
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
						<input type="text" class="form-control" name="command" id="command" placeholder="Command">
						<br>
						<button class="btn btn-info btn-block submit_form" type="submit">OK</button>
					</form>
				</div>

			</div>
		</div>
	</div>
</body>
</html>

<script>
$(document).ready(function(){

	$(".forRplant").hide();
	$(".forHerominers").hide();
	$(".forQubic").hide();

	var RplantSource;
	var HEROMINERS 			= false;
	var RPLANT				= false;
	var QUBIC 				= false;
	var qubic_token 		= "";
	var last_solutions		= 0;
	var totalWorkers 		= parseInt("<?=count($arr)?>");
	var allMyWorkers		= JSON.parse('<?=str_replace('\\', '\\\\', json_encode($allMyWorkers))?>');

	var watched_coins 		= [];
	var workers4string 		= [];
	
	$.each(JSON.parse('<?=json_encode($coins)?>'), function(index, value) {
		var firstFour 			= value.user.substring(0, 4);
		var lastFour 			= value.user.substring(value.user.length - 4);
		var activeAddr4			= firstFour + "" + lastFour;
		
		workers4string.push([{worker : activeAddr4, coin : value.coin}]);
		
		$.each(JSON.parse('<?=str_replace('\\', '\\\\', json_encode($arr))?>'), function(inuser, user) {
			workers4string.push([{worker : activeAddr4 + "." + user.worker, coin : value.coin}]);
		});
	});

	// --- QUBIC --- //
    // Функция для форматирования чисел с разделителями разрядов
    function numberWithCommas(x) {
        return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

	function getQubicStat()
	{
		$.ajax({
			url: 'qubic_statistic.php',
			method: 'POST',
			data: { qubic_token : qubic_token },
			success: function(data) {
				//console.log(data);
				data = JSON.parse(data);

				if(data['body'])
				{
					//console.log(data['token']);
					$("#qubic_stat").html(data['body']);
					$("#tb_miners").html(data['tb_miners']);
					qubic_token = data['token'];

					new DataTable('table.qubicStat', {
						//"order": [ [4,'desc'], [3,'asc'] ],
						"order": [ [4,'desc'] ],
						paging: false,
					});

					if(last_solutions == 0 && data['totalSolutions'] > 0)
					{
						last_solutions = data['totalSolutions'];
					}
					if(data['totalSolutions'] != last_solutions && last_solutions < data['totalSolutions'])
					{
						last_solutions = data['totalSolutions'];
						newMessage("New QUBIC solution: " + data['totalSolutions']);
						alertFunc();
					}

					$("#qubic_data").addClass("well bg-secondary text-orange text-center").html("<h2>QUBIC It/s: <b>" + data['totalIts'] + "</b> | SOL: <b>" + data['totalSolutions'] + "</b></h2>");

					$(".forQubic").show();
					$("#cur_epoch").css({"width" :  data['epoch_progress'] + "%"});
					//$("#cur_epoch").attr("aria-valuemax" , 100);
					$("#cur_epoch").attr("aria-valuenow" , data['epoch_progress']);
					$("#cur_epoch").html("<h3>Qubic epoch " + data['epochNumber'] + ": " + data['epoch_progress'] + " %</h3>");

				}
			},
			error: function(xhr, status, error) {
				console.error('Ошибка при выполнении запроса:', error);
			}
		});
    }
    // -->

	// --- SETTINGS --- //
	var workersControl	= 'manual';
	var lastClickedCoin = 'coin_VISH'; // coins.php button
	var lastClickedData = JSON.stringify([{coin_name: "", user: ""}]);

	$("#workersControl").val(workersControl);

	$.ajax({
		url: 'ajax_saver.php',
		method: 'POST',
		data: { getSettings: true },
		success: function(data) {
			//console.log(data);
			data = JSON.parse(data);

			if(data && data.hasOwnProperty("workersControl"))
			{
				workersControl = data["workersControl"];
				$("#workersControl").val(workersControl);
			}
			if(data && data.hasOwnProperty("lastClickedCoin"))
			{
				lastClickedCoin = data["lastClickedCoin"];
			}
			
			if(data && data.hasOwnProperty("lastClickedData"))
			{
				lastClickedData = data["lastClickedData"];
			}
		},
		error: function(xhr, status, error) {
			console.error('Ошибка при выполнении запроса:', error);
		}
	});

	// ------ //
	$(document).delegate("#workersControl", "change",function(){
		var set = [{workersControl : $(this, 'option:selected').val()}];
		saveSettings(set);
		workersControl = $(this, 'option;selected').val();
	});
	// --- SETTINGS END --- //

    // --- SCHEDULE --- //
    $(document).delegate("#start_schedule_button", "click",function(){
    	if(!$(this).hasClass('bg-danger'))
    	{
			$( this ).addClass('bg-danger').removeClass('bg-success').text('Stop schedule');
		}
		else
		{
			$( this ).addClass('bg-success').removeClass('bg-danger').text('Start schedule');
			clearInterval(timerSchedule);
		}

		var timerSchedule; // объявляем переменную таймера здесь, чтобы она была доступна внутри и снаружи функции
		var minTimer;
		var lastMinute = null; // последняя запомненная минута

		function startTimer() {
		    timerSchedule = setInterval(function() {
				var now = new Date(); // Получаем текущее время
				var hours = now.getHours();
				var minutes = now.getMinutes();

				// Добавляем ведущий ноль, если количество минут/секунд меньше 10
				hours = hours < 10 ? '0' + hours : hours;
				minutes = minutes < 10 ? '0' + minutes : minutes;

				var currentTime = hours + ':' + minutes;

				var startTime = $('#autoStartAndStop_start_time').val();
				var stopTime = $('#autoStartAndStop_stop_time').val();

				var startCoin = $('#autoStartAndStop_start_coin').val();
				var stopCoin = $('#autoStartAndStop_stop_coin').val();

				console.log("currentTime: " + currentTime + ", startTime: " + startTime + " - coin: " + startCoin + ", stopTime: " + stopTime + " - coin: " + stopCoin);
				if (startTime === currentTime) {
					$("#tr_coins_" + startCoin).find('.coin').click();
					clearInterval(timerSchedule); // Останавливаем таймер после выполнения действия
				} else if (stopTime === currentTime) {
					$("#tr_coins_" + stopCoin).find('.coin').click();
					clearInterval(timerSchedule); // Останавливаем таймер после выполнения действия
					clearInterval(minTimer);
					console.log("Schedule is END");
					$( this ).addClass('bg-success text-white').removeClass('bg-danger').text('Start schedule');
				}
		    }, 10000); // Проверяем каждую секунду
		}

		// Запуск таймера при клике на кнопку
		startTimer();

		// Проверяем изменение времени каждую секунду
		minTimer = setInterval(function() {
		    var now = new Date();
		    var minutes = now.getMinutes();

		    // Если минута изменилась, перезапускаем таймер
		    if (minutes !== lastMinute) {
		        clearInterval(timerSchedule); // очищаем предыдущий таймер
		        startTimer(); // запускаем новый таймер
		        lastMinute = minutes; // запоминаем новую минуту
		    }
		}, 10000);
	});

	// ------ //
	var autoHideBlock = false;

	$(document).delegate("#autoHideBlock", "click",function(){
		autoHideBlock = true;
	});

	$(document).delegate(".coin_chk", "change",function(){

		var selected_count 	= 0;
		var isChecked 		= false;

		$('.coin_chk').each(function(){

			isChecked = $(this).prop('checked');
			var coin_chk_element 	= $(this);

			if(isChecked)
			{
				selected_count += 1;

				var valueToAdd = coin_chk_element.attr('for');

				// Проверяем, существует ли элемент уже в массиве
				if (watched_coins.indexOf(valueToAdd) === -1) {
					// Если элемент не найден, добавляем его в массив
					watched_coins.push(valueToAdd);
				}
			}
			else
			{
				var index = watched_coins.indexOf(coin_chk_element.attr('for'));
				if (index > -1) {
					// Если элемент найден, удалить его из массива
					watched_coins.splice(index, 1);
				}
			}
		});

		if(selected_count > 1)
		{
			if(!$("#selected_coins_button").hasClass('isStarted'))
			{
				$("#is_coins_update").html("<p class=\"alert bg-danger\">Coins update is now disabled.<br>Click - START WATCH or uncheck coin.</p>");
				$("#selected_coins_button").removeClass('btn-secondary').addClass('btn-success waitForStart').text('START WATCH');
			}
		}
		else
		{
			$("#is_coins_update").html('');
			$("#selected_coins_button").removeClass('btn-success waitForStart').addClass('btn-secondary').text('No coins selected');
			interval_allCoins = setInterval(allCoins, 120000);
		}
	});

	var currentIndex = 0;
	var intervalId;

	$(document).delegate("#selected_coins_button.waitForStart", "click",function(){

		var autoChangeEvery = $("#autoChangeEvery option:selected").val(); // minutes for change
		$( this ).removeClass('btn-success waitForStart').addClass('btn-danger isStarted').text('STOP WATCH');
		$("#is_coins_update").html('');

		// Устанавливаем интервал вызова функции clickNextCoin с заданным интервалом
		intervalId = setInterval(clickNextCoin, autoChangeEvery * 60000); // переводим минуты в миллисекунды
		coundDown(autoChangeEvery);
		console.log(watched_coins);
	});
	$(document).delegate("#selected_coins_button.isStarted", "click",function(){
		$( this ).removeClass('btn-danger isStarted').addClass('btn-secondary').text('STOP WATCH').text('No coins selected');
		watched_coins = [];
		$('.coin_chk').prop('checked', false);
		clearInterval(intervalId);
		if(timer)
		{
			clearInterval(timer);
		}
		$('#countdown').text('');
	});

	// Функция, которая будет вызываться с интервалом
	function clickNextCoin()
	{
		console.log(watched_coins);
		autoChangeEvery = $("#autoChangeEvery option:selected").val();
		coundDown(autoChangeEvery);

		if (currentIndex < watched_coins.length) {
			// Находим элемент с помощью селектора и симулируем событие click
			$('#' + watched_coins[currentIndex]).find('.coin').click();
			currentIndex++;
		} else {
			// Если достигнут конец массива, очищаем интервал и перезапускаем его
			clearInterval(intervalId);
			currentIndex = 0;
			intervalId = setInterval(clickNextCoin, autoChangeEvery * 60000); // переводим минуты в миллисекунды и устанавливаем новый интервал
		}
	}

	var timer = 0;
		
	function coundDown(num)
	{
		var countdown = num * 60; // 5 минут в секундах
		
		if(timer)
		{
			clearInterval(timer);
		}
		timer = setInterval(function(){
		    var minutes = Math.floor(countdown / 60);
		    var seconds = countdown % 60;

		    // Добавляем ведущий ноль, если количество минут/секунд меньше 10
		    minutes = minutes < 10 ? '0' + minutes : minutes;
		    seconds = seconds < 10 ? '0' + seconds : seconds;

		    // Выводим время внутри элемента div
		    $('#countdown').text(minutes + ':' + seconds).show();

		    if (countdown == 0) {
		        clearInterval(timer);
		        // Действия по истечении времени
		        $('#countdown').text('Время вышло!').hide();
		    } else {
		        countdown--;
		    }
		}, 1000); // Обновляем каждую секунду
	}
	
	// https://pool.rplant.xyz/api2/walletEx/reaction/RuR6UEmYByq7u4QVWxkWrkSdEC8mxU283M/111111
	// https://pool.rplant.xyz/api2/poolminer2x/reaction/RuR6UEmYByq7u4QVWxkWrkSdEC8mxU283M/111111
	/*
	var source = new EventSource('https://pool.rplant.xyz/api2/poolminer2x/reaction/RuR6UEmYByq7u4QVWxkWrkSdEC8mxU283M/111111');
	source.addEventListener('message', function(e) {
	  console.log(e.data);
	}, false);
	*/

	$(document).delegate("#cur_balance", "click",function(){
		$( this ).find('b').text('***');
		$("td.balance").text('***');
	});
	$(document).delegate("#blockFoundDiv", "click",function(){
		$("#blockFoundDiv").hide('slow').html('');
	});
	$(document).delegate(".global_select", "click",function(){
		var isChecked = $(this).prop('checked');
		$('.worker_chk').prop('checked', isChecked);
	});
	$(document).delegate(".global_select_coin", "click",function(){
		var isChecked = $(this).prop('checked');
		$('.coin_chk').prop('checked', isChecked);
	});
	$(document).delegate(".rebootAll", "click",function(){
		WorkerCommand('timeout 1 sudo reboot');
	});
	$(document).delegate(".clrScreen", "click",function(){
		WorkerCommand('timeout 1 screen -ls | awk \'{print $1}\' | xargs -I{} screen -X -S {} quit; timeout 1 sudo killall xmrig; timeout 1 sudo rm -rf /home/laptopsr/xmrig.log;');
	});
	$(document).delegate(".updateSystem", "click",function(){
		WorkerCommand('timeout 1 sudo apt update & sudo apt upgrade -y & sudo apt autoremove -y');
	});
	$(document).delegate(".shutDown", "click",function(){
		WorkerCommand('timeout 1 sudo shutdown now');
	});
	$(document).delegate(".stopQUBIC", "click",function(){
		WorkerCommand('timeout 1 sudo systemctl stop qli --no-block && sudo pkill -f qli');
	});
	$(document).delegate(".startQUBIC", "click",function(){
		WorkerCommand('timeout 1 sudo systemctl start qli');
	});

	function WorkerCommand(cmd)
	{

		$("#lomake_workers option:selected").removeAttr("selected");

		var wrk = [];
		$('.worker_chk').each(function(){
			if( $( this ).prop("checked") )
			{
				console.log($(this))
				$("#lomake select[name='miner']").val('');
				$("#lomake input[name='host']").val('');
				$("#lomake input[name='algo']").val('');
				$("#lomake input[name='user']").val('');
				$("#lomake input[name='pass']").val('');
				$("#lomake select[name='theads']").val('');
				$("#lomake select[name='debug']").val('false');
				$("#lomake input[name='command']").val(cmd);
				
				wrk.push($(this).closest('tr').attr('worker'));
			}
		});

		$("#lomake_workers").val(wrk);

		$("#lomake").css({'border':'4px green solid'});
		setTimeout(function() { 
    		$("#lomake").submit();
    		$("#lomake").css({'border':'none'});
    		return false;
		}, 2000);
	}

	// ------ //

	$("#lomake").on('submit', function(e){
		e.preventDefault();

		var formData = $(this).serialize();

		var selectedValues = $('#lomake_workers').val();
		$.each(selectedValues, function(index, value) {
			$( "#worker_" + value ).removeClass('bg-danger text-white')
			$( "#worker_" + value ).find('.ajaxdata').html('<span class="text-danger">waiting..</span>');
			//console.log(index+" "+value)
		});

		$("#lomake select[name='miner']").val('');
		$("#lomake input[name='host']").val('');
		$("#lomake input[name='algo']").val('');
		$("#lomake input[name='user']").val('');
		$("#lomake input[name='pass']").val('');
		$("#lomake select[name='theads']").val('');
		$("#lomake select[name='debug']").val('false');
		$("#lomake input[name='command']").val('');

		$.ajax({
		    url: 'form_submit.php',
		    method: 'POST',
		    data: formData,
		    success: function(data) {
		        console.log("Form send: " + data);
		    	data = JSON.parse(data);
		        $("#lomake_workers option").prop("selected", true);
        
		        if(data['debug'])
		        {
		        	$("#debugResponse").html(data['debug']);
		        }
		    },
		    error: function(xhr, status, error) {
		        console.error('Ошибка при выполнении запроса:', error);
		    }
		});
	});
	// ------ //
	$("#command").keyup(function(){
		$("#debug").val("true");
	});
	// ------ //
	$( "td.ajaxdata" ).html('<span class="text-danger">waiting..</span>');

	$(document).delegate(".coin", "click",function(){

		$( "tr" ).removeClass('active');
		$( ".coin" ).removeClass('btn-secondary text-white active').addClass('btn-info');
		$( this ).removeClass('btn-info').addClass('btn-secondary text-white active');
		$('tr').find('td').removeClass('active');
		$( this ).closest('tr').find('td').addClass('active');

		// <--
		lastClickedCoin = $(this).attr('id');
		var set = [{lastClickedCoin : $(this).attr('id')}];
		saveSettings(set);
		// -->

		$("#lomake select[name='miner']").val($(this).attr('miner'));
		$("#lomake input[name='host']").val($(this).attr('host'));
		$("#lomake input[name='algo']").val($(this).attr('algo'));
		$("#lomake input[name='user']").val($(this).attr('user'));
		$("#lomake input[name='pass']").val($(this).attr('pass'));
		$("#lomake select[name='theads']").val($(this).attr('theads'));
		$("#lomake select[name='debug']").val($(this).attr('debug'));

		$("#lomake").css({'border':'4px green solid'});
		setTimeout(function() { 
    		$("#lomake").submit();
    		$("#lomake").css({'border':'none'});
    		return false;
		}, 1000);

		if(lastClickedData)
		{
			var parseLastData = JSON.parse(lastClickedData);
			if(parseLastData[0]['coin_name'] && parseLastData[0]['coin_name'] == $(this).attr('coin_name'))
			{
				return false;
			}
		}

		// --- IF Coin is other than old --- //

		newMessage("Coin change to: " + $(this).attr('coin_name'));
		lastClickedData = JSON.stringify([{ticker: $(this).attr('ticker'), coin_name: $(this).attr('coin_name'), user: $(this).attr('user'), host : $(this).attr('host')}]);

		var set = [{lastClickedData : lastClickedData}];
		saveSettings(set);

		EffortClear();
		$(".rplant_field").html("");
	});

	function alertFunc()
	{
		$.ajax({
			url: 'ajax_saver.php',
			method: 'GET',
			data: { doAlert : true },
			success: function(data) {
				console.log(data);
			},
			error: function(xhr, status, error) {
				console.error('Ошибка при выполнении запроса:', error);
			}
		});
	}

	allCoins();

	function allCoins() {

		if( !$("#selected_coins_button").hasClass('waitForStart') )
		{
			$.ajax({
				url: 'coins.php',
				method: 'POST',
				data: { getData : true, hashrateSum : $("#hashrateSum").length > 0 ? parseInt($("#hashrateSum").text()) : 0 },
				success: function(data) {
				    data = JSON.parse(data);
				    
				    if(data['html_data'] && data['is_APIok'] == "true")
				    {
						$("#allCoins").html(data['html_data']);
					}

					if(watched_coins.length > 0)
					{
						$('.tr_tb').each(function() {
							var thisTr = $( this ).attr('id');
							$.each(watched_coins, function(index, value) {
								if(thisTr == value)
								{
									//console.log("Watched: " + value);
									$("#" + value).find('.coin_chk').prop('checked', true);
								}
							});
						});
					}

					if (lastClickedCoin && !$("#" + lastClickedCoin).hasClass('active')) {
						$("#" + lastClickedCoin).closest('tr').find('td').addClass('active');
						$("#" + lastClickedCoin).removeClass('btn-info').addClass('btn-secondary text-white active');
					}

					// --- BEST --- //

					new DataTable('table.coins', {
						//"order": [ [4,'desc'], [3,'asc'] ],
						"order": [ [7,'desc'] ],
						paging: false,
						/*
						columnDefs: [
							{ targets: [0, 1, 2], orderable: false }, // Запретить сортировку для первой и четвертой колонок
							{
								targets: [3], // Price колонка
								orderSequence: ['desc', 'asc'], // Порядок сортировки для второй колонки
								render: function (data, type, row, meta) {
									return parseFloat(data);
								}
							},
							{
								targets: [4], // Diff колонка
								orderSequence: ['asc', 'desc'], // Порядок сортировки для третьей колонки
								render: function (data, type, row, meta) {
									return parseFloat(data);
								}
							}
						]
						*/
					});

					$("#cur_balance").html("<h2>USDT: <b>"
						+ (parseFloat(data['USD_total_xeggex'])??0).toFixed(2) + " $</b> | Coins: <b>"
						+ (parseFloat(data['USD_coins_xeggex'])??0).toFixed(2) + " $</b></h2>"
					);

				},
				error: function(xhr, status, error) {
				    console.error('Ошибка при выполнении запроса:', error);
				    newMessage("coins.php error: \n" + error);
				}
			});
		}
	}

	setInterval(allCoins, 60000); // 1 min

	// ------ //

	setTimeout(function() { 
		herominersApi();
	}, 10000);

	function herominersApi()
	{
		if(HEROMINERS)
		{
			$(".forHerominers").show();

			var coin_name 			= $("#allCoins").find('.active').closest('tr').find('td.coin').attr('coin_name');
			var coin_asset 			= $("#allCoins").find('.active').closest('tr').find('td.coin').text();
			var address 			= $("#allCoins").find('.active').closest('tr').find('td.coin').attr('user');
			var network_hashrate 	= $("#allCoins").find('.active').closest('tr').attr('network_hashrate');
			var network_diff 		= $("#allCoins").find('.active').closest('tr').attr('network_diff');

			if(!coin_name)
			{
				console.log("Active coin not found");
				return false;
			}

			//console.log(coin_name + " " + address);

			var url = "https://" + coin_name + ".herominers.com/api/stats_address?address=" + address;
			$.getJSON(url, function(data) {
				if(data.stats)
				{
					/*
					[stats] => Array
						(
							[donation_level] => 0
							[shares_good] => 20215
							[hashes] => 7421149685
							[lastShare] => 1708242227
							[balance] => 94520736656
							[shares_stale] => 13
							[paid] => 218900000000
							[shares_invalid] => 41
							[hashrate] => 216606
							[roundScore] => 35165271
							[roundHashes] => 35165271
							[poolRoundScore] => 109843219665
							[poolRoundHashes] => 109968359318
							[networkHeight] => 188834
							[hashrate_1h] => 199863
							[hashrate_6h] => 80918.933333333
							[hashrate_24h] => 40130.97752809
							[solo_shares_good] => 0
							[solo_shares_invalid] => 0
							[solo_shares_stale] => 0
							[soloRoundHashes] => 0
							[payments_24h] => 0
							[payments_7d] => 218900000000
						)

					[payments] => Array
						(
							[0] => 1445fda0d83f2ec8fca5181906f3196b9b5d7b695b79a832c23a62c860581d75:107700000000:239328000
							[1] => 1707667483
							[2] => d0ab62e1c4c2e9a6183db9e156018845eb2260e90ccf172be4e25f7a2bb5d095:111200000000:217408000
							[3] => 1707653795
						)
						*/

			    	var htmlData = "<div class=\"well bg-secondary text-orange text-center\"><h2>Herominers: statistic</h2></div>";
			    	htmlData += "<table class=\"table table-striped herominers\">";
			    	htmlData += "<tr><td>Hashrate</td><th>" + (parseFloat(data.stats.hashrate) / 1000).toFixed() + " KH/s</th></tr>";
			    	htmlData += "<tr><td>Hashrate 1h</td><th>" + (parseFloat(data.stats.hashrate_1h) / 1000).toFixed() + " KH/s</th></tr>";
			    	htmlData += "<tr><td>Hashrate 6h</td><th>" + (parseFloat(data.stats.hashrate_6h) / 1000).toFixed() + " KH/s</th></tr>";
			    	htmlData += "<tr><td>Hashrate 24h</td><th>" + (parseFloat(data.stats.hashrate_24h) / 1000).toFixed() + " KH/s</th></tr>";

					var unconfirmed = 0;
					$.each(data['unconfirmed'], function(index, value) {
						//console.log(value['reward']);
						unconfirmed += parseFloat(value['reward']);
					});
					htmlData += "<tr><td>Unconfirmed</td><th>" + (unconfirmed / 1000000000000).toFixed(6) + " " + coin_asset + "</th></tr>";
					htmlData += "<tr><td>Round Contribution</td><th>" + ((data.stats.roundScore / data.stats.poolRoundScore) * 100).toFixed(3) + " %</th></tr>";
					htmlData += "<tr><td height=\"40\"></td><th></th></tr>";

					// ------ //

					//var unlocked = 0;
					//$.each(data['unlocked'], function(index, value) {
					//	var sp = value.split(":");
					//	if(!sp[1])
					//	{
					//		console.log(sp[0]);
					//		unlocked += parseFloat(sp[0]);
					//	}
					//});
					//htmlData += "<tr><td>Pending</td><th>" + (unlocked / 1000000000000).toFixed(5) + " " + coin_asset + "</th></tr>";


					htmlData += "<tr><td>Pending</td><th>" + (data.stats.balance / 1000000000000).toFixed(6) + " " + coin_asset + "</th></tr>";
					htmlData += "<tr><td>Last 24 Hours Paid</td><th>" + (data.stats.payments_24h / 1000000000000).toFixed(6) + " " + coin_asset + "</th></tr>";
					htmlData += "<tr><td>Last Week Paid</td><th>" + (data.stats.paid / 1000000000000).toFixed(6) + " " + coin_asset + "</th></tr>";
					htmlData += "</table>";

					// --- Check workers --- //
					htmlData += "<div class=\"well bg-secondary text-orange text-center\"><h2>Herominers: workers</h2></div>";
			    	htmlData += "<table class=\"table table-striped herominers_wrk\">";
					htmlData += "<tr><th>Wrk.</th><td>Hashrate</td><td>Last</td><td>Rejct</td></tr>";

					// Получаем текущее время в секундах (UNIX-формат)
					var currentTime 		= Math.floor(Date.now() / 1000);

					$.each(data.workers, function(index, value) {
						//console.log(value);

						// Вычисляем разницу в секундах
						var differenceInSeconds = currentTime - value.lastShare;

						htmlData += "<tr><th clign=\"left\">" + value.name + "</th><td>" + value.hashrate + "</td><td>" + differenceInSeconds + " sec.</td><td>" + value.shares_invalid + "</td></tr>";
					});

					htmlData += "</table>";
					
					$("#herominers_data").html(htmlData);

					// --- EFFORT progress for Herominers --- //
					var effort_herominers 	= ((data.stats.poolRoundHashes / network_hashrate) / 2).toFixed(); // Еще есть network_diff, но как все вместе использовать?
					var effort_for			= data.stats.soloRoundHashes==0? "Pool " : "Solo ";

					$("#h_cur_effort").css({"width" :  effort_herominers + "%"});
					$("#h_cur_effort").html("<h3>Herominers " + effort_for + "effort " + effort_herominers + " %</h3>");
					$("#h_cur_effort").attr("aria-valuenow" , effort_herominers);


				}
			});
		}
		else
		{
			$("#herominers_data").html("");
			$(".forHerominers").hide();
		}
	}

	setInterval(herominersApi, 20000);

	// ------ //

	// Функция для отправки AJAX-запроса
	sendAjaxRequest();
	
	function sendAjaxRequest() {

		$.ajax({
		    url: 'workerdata.php',
		    method: 'POST',
		    data: { getData : true },
		    success: function(data) {
		        //console.log("Sended:");
		        data = JSON.parse(data);

				var trbl_worker 	= [];
				var qubic_worker 	= [];
				var my_solutions 	= 0;
				QUBIC 				= false;
				RPLANT 				= false;
				HEROMINERS 			= false;

				$.each(data, function(index, value) {
					//console.log(index + ": " + value);
					if(value['temperature'] && value['temperature'] != '')
					{
						if (Array.isArray(value['temperature']))
						{
							$("#worker_" + value['id']).find('.temperature').html(value['temperature'].join('<br>'));
						} else {
							$("#worker_" + value['id']).find('.temperature').html(value['temperature']);
						}
					}
					if(value['hashrate'] && value['hashrate'] != '')
					{
						$("#worker_" + value['id']).find('.hashrate').html(value['hashrate']);
					}
					if(value['pool'] && value['pool'] != '')
					{
						$("#worker_" + value['id']).find('.pool').html(value['pool']);
					}
					if(value['time'] && value['time'] != '')
					{
						$("#worker_" + value['id']).find('.time').html(value['time']);
					}
					if(value['session'] && value['session'] != '')
					{
						$("#worker_" + value['id']).find('.session').html(value['session']);
					}

					// ------ //

					if(value['session'] && value['session'] == "offline")
					{
						$("#worker_" + value['id']).find('.session').addClass('bg-danger');
						//alertFunc(alertPC);
					}
					else
					{
						$("#worker_" + value['id']).find('.session').removeClass('bg-danger');
					}

					if(value['session'] && value['session'] == 'OFF')
					{
						trbl_worker.push(value['id']);
						$("#worker_" + value['id']).addClass('bg-danger');
					}

					if(value['session'] && value['session'] == 'QUBIC')
					{
						QUBIC = true;
						qubic_worker.push(value['id']);
						my_solutions += parseInt(value['solutions']);
					}
					if (value['pool'].indexOf("rplant") !== -1)
					{
						RPLANT = true;
					}
					if (value['pool'].indexOf("herominers") !== -1)
					{
						HEROMINERS = true;
					}
				});

				if(workersControl == "auto" && trbl_worker.length > 0)
				{
					$("#lomake_workers option:selected").removeAttr("selected");
					$("#lomake_workers").val(trbl_worker);

					setTimeout(function() {

						if($("#allCoins").find('.active').length)
						{
							$("#allCoins").find('.active').click();
						}
						else
						{
							// No coin selected
						}
						
						newMessage("Miner reload: " + trbl_worker);

					}, 2000);
				}

				// --- My hashrate --- //

				var sum = 0;
				$('.hashrate').each(function(index, element) {

					if(parseInt($(element).text()) > 0)
					{
						sum += parseInt($(element).text());
					}
				});
				$("#hashrateSum").html(sum);

				// --- When QUBIC is proccessed --- //
				if(QUBIC)
				{
					$("#mySolutions").html(" | SOL: <b>" + my_solutions + "</b>");
					
					if(qubic_worker.length > 0){
						qubic_worker.forEach(function(worker) {
							$("#lomake_workers option[value='" + worker + "']").removeAttr("selected");
						});
					}
					else
					{
						$("#lomake_workers option").prop("selected", true);
					}
					getQubicStat();
				}
				
				// --- When RPLANT is proccessed --- //
				if(RPLANT)
				{
					if(lastClickedData)
					{
						var parseLastData = JSON.parse(lastClickedData);

						if(!RplantSource && parseLastData[0]['coin_name'] && parseLastData[0]['host'].includes('rplant'))
						{
							console.log("Try to start Rplant");
							rplantApiStream();
						}
					}
				}
				else
				{
					RPLANT = false;
					$(".forRplant").hide();

					if(RplantSource)
					{
						console.log("Rplant go to sleep");
						RplantSource.close();
					}
				}

				// --- When HEROMINERS is proccessed --- //
				if(HEROMINERS)
				{
					$(".forHerominers").show();
				}
				else
				{
					HEROMINERS = false;
					$(".forHerominers").hide();
				}

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
					if (currentTime > outdatedTime) {
						$( this ).closest('tr').addClass('bg-danger');
					} else {
						$( this ).closest('tr').removeClass('bg-danger');
					}
					//console.log(outdatedTime);
				});

		    },
		    error: function(xhr, status, error) {
		        console.error('Ошибка при выполнении запроса:', error);
		        newMessage("workerdata.php error: \n" + error);
		    }
		});
	}

	// Запуск функции sendAjaxRequest() каждые 10 секунд
	setInterval(sendAjaxRequest, 30000);

	setTimeout(function() { 
		getBlocks();
	}, 10000);

	function getBlocks() {

		if(RPLANT)
		{
			var url = 'https://pool.rplant.xyz/api/blocks';
			$.getJSON(url, function(data) {
				//console.log(data);

				$.each(workers4string, function(key, wk){
					//console.log(wk[0].worker);
					
					$.each(data, function(index, value){
						var sp = value.split(":");
						//console.log(sp[3] + " " + sp[6]);	

						if(sp[3] == wk[0].worker)
						{
							//console.log(worker);
							// <--
							sp.push({ coin : wk[0].coin});
							saveBlock(sp);
							// -->
						}
					});

				});
			});

			$.ajax({
				url: 'ajax_saver.php',
				method: 'POST',
				data: { getBlocks : true },
				success: function(data) {
				    //console.log(data);
				    data = JSON.parse(data);

					if(data['return'] && data['return'] !== '')
					{
						$("#getBlocks").html(data['return']).show();

						// --- SORTING --- //
						// Получаем все элементы tr_blocks и сортируем их по значению атрибута 'for'
						var $blocks = $('.tr_blocks').sort(function(a, b) {
							return $(a).attr('for') > $(b).attr('for') ? 1 : -1;
						});

						// Переворачиваем массив элементов
						$blocks = $blocks.get().reverse();

						// Вставляем отсортированные элементы обратно в DOM
						$('table.blocks').append($blocks);

						// --- USD prices --- //
						var summ 	= 0;
						var total 	= 0;
						$('.rewarded').each(function(){
							summ = parseFloat($("#tr_coins_" + $( this ).attr('coin')).attr('last_price')) * parseFloat($( this ).text());
							total += summ;
							$( this ).closest('tr').find('.usdsumm').html( summ.toFixed(2) );
						});

						$("table.blocks").find('.date').append(" | Total: " + total.toFixed(2) + " $");
					}
				},
				error: function(xhr, status, error) {
				    console.error('Ошибка при выполнении запроса:', error);
				    newMessage("ajax_saver error: \n" + error);
				}
			});
		}
		else
		{
			$("#getBlocks").html("").hide();
		}
	}

	setInterval(getBlocks, 60000);

	function EffortClear()
	{
		$("#cur_effort").css({"width" :  "0%"});
		$("#cur_effort").html("<h3>0 %</h3>");
		$("#cur_effort").attr("aria-valuenow" , "0");
	}

	function rplantApiStream()
	{
		if(RPLANT)
		{
			var parseLastData = JSON.parse(lastClickedData);
			if(!parseLastData[0]['coin_name'])
			{
				return false;
			}

			$(".forRplant").show();

			console.log("rplantApiStream is Started");

			var source_count		= 0;
			var blockFound 			= 0;
			var offline_count		= 0;
			var active_coin_name 	= parseLastData[0]['coin_name'];
			var active_address 		= parseLastData[0]['user'];
			var current_ticker		= parseLastData[0]['ticker'];
			var block_found_stream	= 0;
			var effort_origin		= 0;
			var effort_last			= 0;

			var network_name 		= '';
			var network_hashrate 	= 0;
			var network_diff 		= 0;					
			var soloShares			= 0;
			var hrs					= 0;
			var wcs					= 0;
			var immature			= 0;
			var balance				= 0;
			var paid				= 0;
			var miner				= [];
			var net					= [];
			var miner_address		= '';
			var offset				= 1;

			// Закрытие предыдущего соединения, если оно существует
			if (RplantSource) {
				RplantSource.close();
			}

			var url = 'https://pool.rplant.xyz/api/blocks';

			// <--	
			var url = 'https://pool.rplant.xyz/api2/poolminer2x/' + active_coin_name + '/' + active_address + '/111111';
			RplantSource = new EventSource(url);
			RplantSource.addEventListener('message', function(e) {

				//console.log("Rplant RplantSource is online");

				source_count += 1;

				var parsed = JSON.parse(e.data);
				//console.log(parsed['blocks']);

				// --- BLOCKS --- //
				if(parsed['blocks'])
				{

				}

				if(parsed['net'])
				{
					net					= parsed['net'];
					network_name		= net.v;
					network_hashrate	= net.hr;
					network_diff		= net.d;
					
					//console.log(network_name);
				}

				if(parsed['miner'])
				{
					// <-- Offset
					if(current_ticker == "MNN")
					{
						offset = 100000000000;
					} else if(current_ticker == "TABO")
					{
						offset = 1000000000000;
					}
					else
					{
						offset = 1;
					}
					// -->

					miner				= parsed['miner'];
					miner_address		= miner["miner"];			
					soloShares			= miner["soloShares"];
					hrs					= miner["hrs"];
					wcs					= miner["wcs"];
					immature			= (miner["immature"] / offset).toFixed(2);
					balance				= (miner["balance"] / offset).toFixed(2);
					paid				= (miner["paid"] / offset).toFixed(2);
					block_found_stream	= miner["found"]? miner["found"]["solo"]??0 : 0;

					// --- Check Offline workers --- //
					// <--
					if(miner["workers"].length > 0)
					{
						var workers_online = [];
						$.each(miner["workers"], function(index, value) {
							var sp = value.split(":");
							workers_online.push(sp[0]);
						});

						var workers_offline = [];
						$.each(allMyWorkers, function(index, value) {
							if(workers_online.indexOf(index) === -1 && $("#worker_" + index).find('.session').text() != "offline"  && $("#worker_" + index).find('.session').text() != "QUBIC")
							{
								workers_offline.push(index);
							}
						});
					}

					if (workers_offline && workers_offline.length > 0)
					{
						offline_count += 1;
						var max_count = 3;

						//newMessage("OFFLINE: " + workers_offline.join(", "));
						$("#wcs_offline").html(workers_offline.join(", ") + "<br>offline_count: " + offline_count +"/" + max_count);
						$("#wcs_offline").closest('tr').find('td:first').addClass("bg-danger");
								
						if(workersControl == "auto" && offline_count >= max_count)
						{
							$("#lomake_workers option:selected").removeAttr("selected");
							$("#lomake_workers").val(workers_offline);
					
							$("#allCoins").find('.active').click();
							offline_count = 0;
						}
					}
					else
					{
						$("#wcs_offline").html('');
						$("#wcs_offline").closest('tr').find('td:first').removeClass("bg-danger");
					}
					// -->
				}

				if(soloShares > 0 && network_hashrate > 0 && network_diff > 0)
				{
					//var summ	= (soloShares / network_hashrate) * (network_diff > 100000 ? 1 : (10000/network_diff));
					if(network_diff > 100000)
					{
						offset = (soloShares / network_hashrate);
					}
					else
					{
						offset = ((soloShares / network_hashrate) * 100000) / 2;
					}

					var summ =  offset;
					
					//console.log("wcs: " + wcs + ", soloShares: " + soloShares + ", network_hashrate: " + network_hashrate + ", network_diff: " + network_diff);

					effort_origin 		= summ.toFixed(); // .toFixed()
					var effort 			= effort_origin / 3;

					if(effort_origin > effort_last)
					{
						effort_last	= effort_origin;
					}

					$("#cur_effort").css({"width" :  effort + "%"});
					$("#cur_effort").html("<h3>Rplant effort: " + effort_origin + " %</h3>");
					$("#cur_effort").attr("aria-valuenow" , effort);
				}

				if(network_name !== ''){ 			$("#v").html(network_name + " / " + active_coin_name) };
				if(network_hashrate !== 0){ 		$("#hr").html(network_hashrate) };
				if(network_diff !== 0){ 			$("#d").html(network_diff) };
				if(soloShares !== 0){ 				$("#soloShares").html(soloShares) };
				if(hrs !== 0){ 						$("#hrs").html(hrs); $(".hrs").html("<h2>RPLANT: <b class=\"rplant_field\">" + hrs + " H/s</b> (" + source_count + ")</h2>"); };
				if(wcs !== 0){ 						$("#wcs").html(wcs) };
				if(immature !== 0){ 				$("#immature").html(immature) };
				if(balance !== 0){ 					$("#balance").html(balance) };
				if(paid !== 0){ 					$("#paid").html(paid) };
				if(block_found_stream !== 0){ 		$("#block_found").html(block_found_stream) };
				
				// --- BLOCK FOUND --- //

				if(block_found_stream > 0 && blockFound == 0)
				{
					blockFound = block_found_stream;
				}

				if(blockFound > 0 && block_found_stream > 0 && block_found_stream > blockFound)
				{

					var usdtVolume = immature * parseFloat($("#tr_coins_" + current_ticker).attr('last_price'));

					$("#blockFoundDiv").show('slow');
					$("#blockFoundDiv").html("<h1 class=\"alert bg-success\">* * * BLOCK FOUND  " + getTimeNow() + " * * * <button class=\"btn btn-primary\" id=\"autoHideBlock\">Autohide</button></h1>");
					alertFunc();

					newMessage("<blockfound>BLOCK FOUND: " + active_coin_name + ", effort: <effort>" + effort_last + "</effort> %</blockfound>");

					setTimeout(function() { 
						effort_origin 	= 0;
						effort_last		= 0;
						EffortClear();
						
						if(autoHideBlock){
							$("#blockFoundDiv").hide();
						}
					}, 2000);

					blockFound = block_found_stream;
				}
				
				//console.log( parseFloat($("#tr_coins_" + current_ticker).find('.price').text()) );

			}, false);
			// -->
		}
		else
		{
			$(".forRplant").hide();
		}
	}

	$(document).delegate("table", "click",function(){
		$('.popup_container').removeClass('bougasetun-open');
	});
	$(document).delegate(".clear_button", "click",function(){
		$.ajax({
			url: 'ajax_saver.php',
			method: 'POST',
			data: { removeAllMessages: true },
			success: function(data) {
			    console.log(data);
			    getLastMessages(20);
			},
			error: function(xhr, status, error) {
			    console.error('Ошибка при выполнении запроса:', error);
			}
		});
	});
	$(document).delegate(".remove_row", "click",function(){
		var thisFor = $(this).attr('for');

		$.ajax({
			url: 'ajax_saver.php',
			method: 'POST',
			data: { removeMessage: true, id : thisFor },
			success: function(data) {
			    console.log(data);
			    getLastMessages(20);
			},
			error: function(xhr, status, error) {
			    console.error('Ошибка при выполнении запроса:', error);
			}
		});
	});

	function newMessage(mess)
	{
		let randomId 	= generateRandomId(11);
		var newMessage 	= "<button class=\"remove_row bg-danger\" for=\"" + randomId + "\">x</button> <b>" + getTimeNow() + "</b> " + mess;

		$.ajax({
			url: 'ajax_saver.php',
			method: 'POST',
			data: { newMessage: newMessage },
			success: function(data) {
			    //console.log(data);
			    getLastMessages(20);
			    $('.popup_container').addClass('bougasetun-open');

				setTimeout(function() { 
					$('.popup_container').removeClass('bougasetun-open');
				}, 10000);

			},
			error: function(xhr, status, error) {
			    console.error('Ошибка при выполнении запроса:', error);
			}
		});
	}

	getLastMessages(17);

	function getLastMessages(count)
	{
		$.ajax({
			url: 'ajax_saver.php',
			method: 'POST',
			data: { getMessages: true, count : count },
			success: function(data) {
			    //console.log(data);
			    data = JSON.parse(data);

				//$('.popup_container').addClass('bougasetun-open');
				var replacedData = data.replace(/\n/g, '<br>');
				$('#popup_content').html(replacedData);

			},
			error: function(xhr, status, error) {
			    console.error('Ошибка при выполнении запроса:', error);
			}
		});
	}

	function getTimeNow()
	{
		var now = new Date();
		var day = now.getDate();
		var month = now.getMonth() + 1; // Месяцы начинаются с 0, поэтому добавляем 1
		var year = now.getFullYear();
		var hours = now.getHours();
		var minutes = now.getMinutes();

		// Добавляем ноль перед однозначными числами
		day = (day < 10) ? '0' + day : day;
		month = (month < 10) ? '0' + month : month;
		hours = (hours < 10) ? '0' + hours : hours;
		minutes = (minutes < 10) ? '0' + minutes : minutes;

		return "[" + day + '.' + month + '.' + year + ' ' + hours + ':' + minutes + "]";
	}

	function generateRandomId(length) {
		let result = '';
		const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		const charactersLength = characters.length;
		for (let i = 0; i < length; i++) {
		    result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
	}

	function saveSettings(set)
	{
		$.ajax({
			url: 'ajax_saver.php',
			method: 'POST',
			data: { saveSettings: true, set : set[0] },
			success: function(data) {
			    //console.log(data);
			},
			error: function(xhr, status, error) {
			    console.error('Ошибка при выполнении запроса:', error);
			    newMessage("ajax_saver error: \n" + error);
			}
		});
	}

	function saveBlock(set)
	{
		$.ajax({
			url: 'ajax_saver.php',
			method: 'POST',
			data: { saveBlock: true, set : set },
			success: function(data) {
			    //console.log(data);
			},
			error: function(xhr, status, error) {
			    console.error('Ошибка при выполнении запроса:', error);
				newMessage("ajax_saver error: \n" + error);
			}
		});
	}
});
</script>
<button class="btn btn-lg btn-info bottom-right-button open_button">Open console</button>
