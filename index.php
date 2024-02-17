<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

<!-- DataTable -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/fontawesome.min.css" />  
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css" /> 

<style>
body{
	background: #333;
	color: #ddd;
}
.coin{
	margin-bottom: 2px;
}
.bg-success, .bg-info, .bg-warning, .bg-secondary, .bg-danger{
	color: white;
}
td.active{
	border-top: 3px orange solid;
	border-bottom: 3px orange solid;
}
.table td, .table th{
	padding: 6px 0.75rem 3px;
	color: #ddd;
}
.table th{
	color: orange;
}
.form-control{
	background: #ededed;
}
#datatable_length, #datatable_filter, .dataTables_filter{
	display:none;
}
</style>
</head>
<body>
	<div class="container-fluid" style="margin-top: 30px;">
	<center><div id="header"></div><div id="debugResponse"></div></center>
	<br>
		<div class="row">
			<div class="col-md-3">
				<center>
					<input type="text" class="form-control" id="alertPC" placeholder="Alert PC. ex. 192.168.1.205">
					<br>
					<h4>Current effort <b id="cur_effort"></b> %</h4>
				</center>
				<br>
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
			<div class="col-md-6">
				<select id="workersControl" class="form-control">
					<option value="auto">AUTO</option>
					<option value="manual">MANUAL</option>
				</select>
				<br>
				<div id="all_computers">
					<div class="well bg-secondary"><center><h4><b id="hashrateSum"></b> H/s</h4></center></div>
					<table class="table table-striped">
					<tr>
						<td></td>
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
							<td><input class="worker_chk" type="checkbox" '.($v['worker'] != 205 ? 'checked' : '').'></td>
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
					<hr>
					<h3>
						With selected: <button class="btn btn-danger rebootAll">Reboot</button> <button class="btn btn-danger clrScreen">Clear screen</button>
					</h3>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

<script>
$(document).ready(function(){

	$(document).delegate(".rebootAll", "click",function(){
		WorkerCommand('timeout 1 sudo reboot');
	});
	$(document).delegate(".clrScreen", "click",function(){
		WorkerCommand('timeout 1 screen -ls | awk \'{print $1}\' | xargs -I{} screen -X -S {} quit; timeout 1 sudo killall xmrig; timeout 1 sudo rm -rf /home/laptopsr/xmrig.log;');
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

		$.ajax({
		    url: 'form_submit.php',
		    method: 'POST',
		    data: formData,
		    success: function(data) {
		    	data = JSON.parse(data);
		        console.log("Form send: " + data);
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
	$( "td.ajaxdata" ).html('<span class="text-danger">waiting..</span>');
    var lastClickedCoin = localStorage.getItem('lastClickedCoin');

	$(document).delegate(".coin", "click",function(){
	
		$( "tr" ).removeClass('active');
		$( ".coin" ).removeClass('btn-success text-white active').addClass('btn-info');
		$( this ).removeClass('btn-info').addClass('btn-success text-white active');
		$('tr').find('td').removeClass('active');
		$( this ).closest('tr').find('td').addClass('active');

		// <--
		localStorage.setItem('lastClickedCoin', $(this).attr('id'));
		lastClickedCoin = $(this).attr('id');
		console.log("Next coin: " + lastClickedCoin);
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
		    data: { getData : true, hashrateSum : $("#hashrateSum").length > 0 ? parseInt($("#hashrateSum").text()) : 0 },
		    success: function(data) {
		        data = JSON.parse(data);
				$("#allCoins").html(data);

				if (lastClickedCoin && !$("#" + lastClickedCoin).hasClass('active')) {
					$("#" + lastClickedCoin).closest('tr').find('td').addClass('active');
					$("#" + lastClickedCoin).removeClass('btn-info').addClass('btn-success text-white active');
				}

				// --- BEST --- //

				new DataTable('table.coins', {
					"order": [ [1,'asc'], [2,'desc'] ],
					paging: false
				});

	            var firstRow = $('tr.tr_tb').first();
	            firstRow.addClass('best bg-secondary');
	            
	            if(systemControl == "auto")
	            {
	            	if(!$("#" + lastClickedCoin).closest('tr').hasClass('best'))
	            	{
	            		firstRow.find('button').click();
	            	}
	            }

				// ------ //

				var res1 = '';
				profit("<?=date("Y-m-d", strtotime("-1 day"))?>", function(result) {
					res1 = result;
					//console.log(result);
				});

				profit("<?=date("Y-m-d")?>", function(result) {
					$("#moneyToday").html( "<table style=\"width:100%\"><tr><td style=\"width:50%; vertical-align: top\">" + res1 + "</td><td style=\"vertical-align: top\">" + result + "</td></tr></table>");
					//console.log(result);
				});

		    },
		    error: function(xhr, status, error) {
		        console.error('Ошибка при выполнении запроса:', error);
		    }
		});
		
	}

	setInterval(allCoins, 120000);


	setTimeout(function() { 
		pendingBlocks();
	}, 10000);

	function pendingBlocks() {

		if($("#allCoins").find('.active').length > 0)
		{
			$("#header").html('<h2>Mining helpper V3</h2>');

			$.ajax({
				url: 'pending_blocks.php',
				method: 'POST',
				data: { getData : true, active : $("#allCoins").find('.active').text() },
				success: function(data) {
				    data = JSON.parse(data);
					$("#my_pending_blocks").html(data);
					
					// Найти все элементы с классом "pvm" и извлечь текст времени
					var times = $('.pvm').map(function() {
						return new Date($(this).attr('for')).getTime();
					}).get();

					// Найти самое свежее время
					var freshestTime = new Date(Math.max.apply(null, times));

					if(!localStorage.getItem('freshestTime') || localStorage.getItem('freshestTime') != freshestTime)
					{
						localStorage.setItem('freshestTime', freshestTime);

						$("#header").html('<h1 class="alert bg-success text-white">* * * BLOCK FOUND * * *</h1>');
						alertFunc(alertPC);
					}

					// --- EFFORT % --- //

					var ct = new Date();
					// Разница между текущим временем и freshestTime
					var df 					= ((ct - freshestTime) / (1000 * 60)) * 60;
					var network_diff 		= parseFloat($("#allCoins").find('.active').closest('tr').attr('network_diff'));
					var network_hashrate 	= parseFloat($("#allCoins").find('.active').closest('tr').attr('network_hashrate'));
					var summ				= (df / network_diff) / 1000;
					//console.log( summ.toFixed() );
					$("#cur_effort").html(summ.toFixed());
					
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
						}
					});

				},
				error: function(xhr, status, error) {
				    console.error('Ошибка при выполнении запроса:', error);
				}
			});
		}
		else
		{
			$("#my_pending_blocks").html("");
		}
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

				var trbl_worker = [];

				$.each(data, function(index, value) {
					//console.log(index + ": " + value);
					if(value['temperature'] && value['temperature'] != '')
					{
						$("#worker_" + value['id']).find('.temperature').html(value['temperature']);
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

					if(value['time'] && value['time'] == 'OFF')
					{
						trbl_worker.push(value['id']);
						$("#worker_" + value['id']).addClass('bg-danger');
					}

				});

				if(workersControl == "auto" && trbl_worker.length > 0)
				{
					$("#lomake_workers option:selected").removeAttr("selected");
					$("#lomake_workers").val(trbl_worker);

					setTimeout(function() {
						$("#allCoins").find('.active').click();
					}, 2000);
				}

				// ------ //

				var sum = 0;
				$('.hashrate').each(function(index, element) {

					if(parseInt($(element).text()) > 0)
					{
						sum += parseInt($(element).text());
					}
				});
				$("#hashrateSum").html(sum);

				// ------ //

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
						$( this ).closest('tr').addClass('bg-danger text-white');
					} else {
						$( this ).closest('tr').removeClass('bg-danger text-white');
					}

				});

		    },
		    error: function(xhr, status, error) {
		        console.error('Ошибка при выполнении запроса:', error);
		    }
		});
	}

	// Запуск функции sendAjaxRequest() каждые 10 секунд
	setInterval(sendAjaxRequest, 30000);

	function profit(d, callback) {

		if($("#allCoins").find('.active').length > 0 && $("#allCoins").find('.active').text() != "ZEPH")
		{
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
	}
});
</script>
