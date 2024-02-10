<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

include "config.php";
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
.table td, .table th{
	padding: 6px 0.75rem 3px;
}
.bg-success, .bg-info, .bg-warning, .bg-secondary, .bg-danger{
	color: white;
}
</style>
</head>
<body>
	<div class="container-fluid" style="margin-top: 30px;">
	<center><div id="header"></div></center>
	<br>
		<div class="row">
			<div class="col-md-3" style="background: #ddd">
				<center>
					<input type="text" class="form-control" id="alertPC" placeholder="Alert PC. ex. 192.168.1.205">
				</center>
				<br>
				<h4>My pending blocks</h4>
				<div id="my_pending_blocks">
					<span class="btn btn-sm btn-block btn-secondary text-white">waiting..</span>
				</div>
				<hr>
				<div id="moneyToday"></div>
				<hr>
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
					<button class="btn btn-info btn-block" type="submit">OK</button>
				</form>
			</div>
			<div class="col-md-3">
				<select id="systemControl" class="form-control">
					<option value="auto">AUTO</option>
					<option value="manual">MANUAL</option>
				</select>
				<br>
				<div id="allCoins"></div>
			</div>
			<div class="col-md-6" style="background: #ddd">
				<select id="workersControl" class="form-control">
					<option value="auto">AUTO</option>
					<option value="manual">MANUAL</option>
				</select>
				<br>
				<div id="all_computers">
					<div id="hashrateSum" class="alert bg-secondary"></div>
					<table class="table table-striped">
					<tr>
						<th>Worker</th>
						<th>Temp.</th>
						<th>Time</th>
						<th>Hashrate</th>
						<th>Pool</th>
						<th>Session</th>
					</tr>
					<?php
					foreach($arr as $v)
					{
						if($last_model != $v['model'])
						{
							echo '
							<tr class="model">
								<td colspan="6" class="bg-secondary"><h5><b>'.$v['model'].'</b></h5></td>
							</tr>
							';
						}

						echo '
						<tr id="'.$v['worker'].'" class="worker_tr">
							<th class="host">'.$v['host'].'</th>
							<td class="temperature"><span class="text-danger">waiting..</span></td>
							<td class="time"><span class="text-danger">waiting..</span></td>
							<td class="hashrate"><span class="text-danger">waiting..</span></td>
							<td class="pool"><span class="text-danger">waiting..</span></td>
							<td class="session"><span class="text-danger">waiting..</span></td>
						</tr>';
						
						$last_model = $v['model'];
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

	$("#lomake").on('submit', function(e){
		e.preventDefault(); // Предотвращаем стандартное поведение отправки формы

		var formData = $(this).serialize(); // Получаем данные формы в виде строки запроса

		$.ajax({
		    url: 'form_submit.php',
		    method: 'POST',
		    data: formData, // Отправляем данные формы
		    success: function(data) {
		    	data = JSON.parse(data);
		        console.log("Form send: " + data);
		    },
		    error: function(xhr, status, error) {
		        console.error('Ошибка при выполнении запроса:', error);
		    }
		});
	});
	// ------ //
	var alertPC	= localStorage.getItem('alertPC')??'';
	$("#alertPC").val(alertPC);
	$(document).delegate("#alertPC", "blur",function(){
		localStorage.setItem('alertPC', $(this).val());
		alertPC = $(this).val();
	});
	// ------ //
	var systemControl	= localStorage.getItem('systemControl')??'manual';
	$("#systemControl").val(systemControl);
	$(document).delegate("#systemControl", "change",function(){
		localStorage.setItem('systemControl', $(this, 'option;selected').val());
		systemControl = $(this, 'option;selected').val();
	});
	// ------ //
	var workersControl	= localStorage.getItem('workersControl')??'manual';
	$("#workersControl").val(workersControl);
	$(document).delegate("#workersControl", "change",function(){
		localStorage.setItem('workersControl', $(this, 'option;selected').val());
		workersControl = $(this, 'option;selected').val();
	});
	// ------ //
	$("#command").keyup(function(){
		$("#debug").val("true");
	});
	// ------ //
    var lastClickedCoin = localStorage.getItem('lastClickedCoin');
	$(document).delegate(".coin", "click",function(){
	
		$( ".coin" ).removeClass('btn-success text-white active').addClass('btn-info');
		$( this ).removeClass('btn-info').addClass('btn-success text-white active');

		localStorage.setItem('lastClickedCoin', $(this).attr('id'));
		lastClickedCoin = $(this).attr('id');
		console.log("Next coin: " + lastClickedCoin);

		$("#lomake select[name='miner']").val($(this).attr('miner'));
		$("#lomake input[name='host']").val($(this).attr('host'));
		$("#lomake input[name='algo']").val($(this).attr('algo'));
		$("#lomake input[name='user']").val($(this).attr('user'));
		$("#lomake select[name='theads']").val($(this).attr('theads'));
		$("#lomake select[name='debug']").val($(this).attr('debug'));

		setTimeout(function() { 
    		$("#lomake").submit();
		}, 1000);

	});

	function alertFunc(ip)
	{
		$.ajax({
			url: 'alert.php',
			method: 'GET',
			data: { alertPC : ip },
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

		$.ajax({
		    url: 'coins.php',
		    method: 'POST',
		    data: { getData : true },
		    success: function(data) {
		        data = JSON.parse(data);
				$("#allCoins").html(data);

				if (lastClickedCoin) {
					$("#" + lastClickedCoin).removeClass('btn-info').addClass('btn-success text-white active');
				}
				// ------ //

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

				// ------ //

				var res1 = '';
				profit("<?=date("Y-m-d", strtotime("-1 day"))?>", function(result) {
					res1 = result;
					//console.log(result);
				});

				profit("<?=date("Y-m-d")?>", function(result) {
					$("#moneyToday").html( "<table style=\"width:100%\"><tr><td style=\"vertical-align: top\">" + res1 + "</td><td style=\"vertical-align: top\">" + result + "</td></tr></table>");
					//console.log(result);
				});

		    },
		    error: function(xhr, status, error) {
		        console.error('Ошибка при выполнении запроса:', error);
		    }
		});
		
	}

	setInterval(allCoins, 120000);


	pendingBlocks();

	function pendingBlocks() {

		$("#header").html('<h2>SOLO mining helpper for https://pool.rplant.xyz</h2>');

		$.ajax({
		    url: 'pending_blocks.php',
		    method: 'POST',
		    data: { getData : true },
		    success: function(data) {
		        data = JSON.parse(data);
				$("#my_pending_blocks").html(data);
				
				// Найти все элементы с классом "pvm" и извлечь текст времени
				var times = $('.pvm').map(function() {
					return new Date($(this).attr('for')).getTime();
				}).get();

				// Найти самое свежее время
				var freshestTime = new Date(Math.max.apply(null, times));

				// ------ //
				
				$('.tr_block').each(function(){
				    // Получаем значение времени из ячейки с классом pvm
				    var timeString = $(this).find('.pvm').attr('for');
				    // Преобразуем строку времени в объект Date
				    var time = new Date(timeString);
				    // Получаем текущее время
				    var currentTime = new Date();
				    // Разница между текущим временем и временем в ячейке pvm в минутах
				    var diffMinutes = (currentTime - time) / (1000 * 60);

				    // Если разница меньше 10 минут, добавляем класс highlight

				    if (diffMinutes <= 10)
				    {
				        $(this).addClass('bg-success text-white');

						//console.log("New fresh time" + freshestTime);

						if(!localStorage.getItem('freshestTime') || localStorage.getItem('freshestTime') != freshestTime)
						{
							localStorage.setItem('freshestTime', freshestTime);

							$("#header").html('<h1 class="alert bg-success text-white">* * * BLOCK FOUND * * *</h1>');
							alertFunc(alertPC);
						}
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
	sendAjaxRequest();
	
	function sendAjaxRequest() {

		$.ajax({
		    url: 'workerdata.php',
		    method: 'POST',
		    data: { getData : true },
		    success: function(data) {
		        //console.log("Sended:");
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

					// ------ //
					
					if(workersControl == "auto" && value['time'] && value['time'] == 'OFF')
					{
						$("#lomake_workers option:selected").removeAttr("selected");
						var selectedIds = value['id'];
						//console.log(selectedIds)

						// Пройдемся по каждому элементу select с атрибутом name='workers'
						$("#lomake_workers option").each(function() {
							// Проверим, содержится ли значение id текущего option в списке выбранных id
							if (selectedIds.includes($(this).val())) {
								// Если содержится, установим атрибут selected для данного option
								$(this).prop("selected", true);
							} else {
								// Иначе снимем атрибут selected (если он ранее был установлен)
								$(this).prop("selected", false);
							}
						});

						if(selectedIds > 0)
						{
							$("#lomake").addClass('bg-danger');
							setTimeout(function() { 
								$("#allCoins").find('.active').click();
							}, 10000);

							return false;
						}
					}

					if(value['time'] && value['time'] == 'OFF')
					{
						alertFunc(alertPC);
					}
					
					// ------ //

					var sum = 0;
					$('.hashrate').each(function(index, element) {

						if(parseInt($(element).text()) > 0)
						{
							sum += parseInt($(element).text());
						}
					});
					$("#hashrateSum").html("<center><h3><b>" + sum + " H/s</b></h3></center>");

				});
		    },
		    error: function(xhr, status, error) {
		        console.error('Ошибка при выполнении запроса:', error);
		    }
		});
		
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

	function profit(d, callback) {
		var usd = 0;
		var yht = 0;
		var moneyData = "<table class=\"table table-striped\"><tr><th colspan=\"2\">" + d + "</th>";
		
		$.ajax({
		    url: 'money.php',
		    method: 'GET',
		    async: false, // Здесь была опечатка: 'async' вместо 'assync'
		    data: { day: d },
		    success: function(data) {
		        //console.log("money:" + data);
		        data = JSON.parse(data);
		        
		        $.each(data, function(index, value) {
		            $.each(value, function(coin, sum) {
		                if ($("#coin_" + coin).closest('tr').find('.price').length > 0) {
		                    usd = (parseFloat($("#coin_" + coin).closest('tr').find('.price').text()) * sum).toFixed(2);
		                    yht += parseFloat(usd);
		                    moneyData += "<tr><td>" + coin + "</td><td align=\"right\">" + usd + " USD</td></tr>";
		                }
		            });
		        });

		        moneyData += "<tr class=\"bg-secondary\"><th></th><td align=\"right\"><b>" + (yht).toFixed(2) + " USD</b></td></tr></table>";

		        // Вызываем колбэк с полученными данными
		        callback(moneyData);
		    },
		    error: function(xhr, status, error) {
		        console.error('Ошибка при выполнении запроса:', error);
		    }
		});
	}
});
</script>
