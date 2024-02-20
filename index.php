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
button.coin{
	padding: 0px 4px;
	margin-bottom: 0px;
}
.bg-success, .bg-info, .bg-warning, .bg-secondary, .bg-danger{
	color: #ddd;
}
.text-orange{
	color: orange;
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
#cur_effort{
	position: absolute;
	top: 0px; left: 0;
	padding: 7px 7px 0px;
	z-index: 999999;
}
#cur_balance{
	position: absolute;
	top: 0px; right: 0;
	padding: 7px 7px 0px;
	z-index: 999999;
}
table.herominers th, table.miner_table th{
	text-align: right;
}
</style>
</head>
<body>
	<div class="container-fluid" style="margin-top: 20px;">
	<div id="cur_effort" class="btn btn-secondary text-orange"></div>
	<div id="cur_balance" class="btn btn-secondary text-orange"></div>
	<center><div id="header">Please wait...</div><div id="debugResponse"></div></center>
	<br>
		<div class="row">
			<div class="col-md-3">
				<center>
					<input type="text" class="form-control" id="alertPC" placeholder="Alert PC. ex. 192.168.1.205">
				</center>
				<br>
				<div id="herominers_data"></div>
				<div id="my_pending_blocks"></div>
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
			<div class="col-md-4">
				<select id="systemControl" class="form-control">
					<option value="auto">AUTO</option>
					<option value="manual">MANUAL</option>
				</select>
				<br>
				<div id="allCoins"></div>
			</div>
			<div class="col-md-5">
				<select id="workersControl" class="form-control">
					<option value="auto">AUTO</option>
					<option value="manual">MANUAL</option>
				</select>
				<br>
				<div id="all_computers">
					<div class="well bg-secondary"><center><h4><b id="hashrateSum"></b> H/s</h4></center></div>
					<table class="table table-striped">
					<tr>
						<td><input class="global_select" type="checkbox" checked></td>
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
					<hr>
					<h5>
						With selected: <button class="btn btn-info btn-sm rebootAll">Reboot</button> <button class="btn btn-info btn-sm clrScreen">Clear screen</button>
					</h5>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

<script>
$(document).ready(function(){

	var totalWorkers 	= parseInt("<?=count($arr)?>");
	var blockFound		= 0;

	// https://pool.rplant.xyz/api2/walletEx/reaction/RuR6UEmYByq7u4QVWxkWrkSdEC8mxU283M/111111
	// https://pool.rplant.xyz/api2/poolminer2x/reaction/RuR6UEmYByq7u4QVWxkWrkSdEC8mxU283M/111111
	/*
	var source = new EventSource('https://pool.rplant.xyz/api2/poolminer2x/reaction/RuR6UEmYByq7u4QVWxkWrkSdEC8mxU283M/111111');
	source.addEventListener('message', function(e) {
	  console.log(e.data);
	}, false);
	*/

	$(document).delegate(".global_select", "click",function(){
		var isChecked = $(this).prop('checked');
		$('.worker_chk').prop('checked', isChecked);
	});

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

		$("#lomake select[name='miner']").val('');
		$("#lomake input[name='host']").val('');
		$("#lomake input[name='algo']").val('');
		$("#lomake input[name='user']").val('');
		$("#lomake input[name='pass']").val('');
		$("#lomake select[name='theads']").val('');
		$("#lomake select[name='debug']").val('');
		$("#lomake input[name='command']").val('');

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

		blockFound = 0;

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
		        
		        if(data['html_data'])
		        {
					$("#allCoins").html(data['html_data']);
				}

				if (lastClickedCoin && !$("#" + lastClickedCoin).hasClass('active')) {
					$("#" + lastClickedCoin).closest('tr').find('td').addClass('active');
					$("#" + lastClickedCoin).removeClass('btn-info').addClass('btn-success text-white active');
				}

				// --- BEST --- //

				new DataTable('table.coins', {
					"order": [ [2,'desc'], [1,'asc'] ],
					paging: false,
					columnDefs: [
						{ targets: [0, 3], orderable: false }, // Запретить сортировку для первой и четвертой колонок
						{
							targets: [1], // Вторая колонка
							orderSequence: ['desc', 'asc'], // Порядок сортировки для второй колонки
							render: function (data, type, row, meta) {
								return parseFloat(data);
							}
						},
						{
							targets: [2], // Третья колонка
							orderSequence: ['asc', 'desc'], // Порядок сортировки для третьей колонки
							render: function (data, type, row, meta) {
								return parseFloat(data);
							}
						}
					]
				});

				// --- BALANCE --- //

				// Инициализируем переменную для хранения суммы
				var totalBalance = 0;

				// Проходим по каждой ячейке с классом "balance" и суммируем их значения
				$('.balance').each(function() {
					// Преобразуем текст ячейки в число и добавляем его к общей сумме
					totalBalance += parseFloat($(this).text());
				});

				$("#cur_balance").html("<h2>USDT: <b>" + (parseFloat(data['USD_total_xeggex'])??0).toFixed(2) + "</b> | Coins: <b>" + totalBalance.toFixed(2) + " $</b></h2>");

				// ------ //
				
	            var firstRow = $('tr.tr_tb').first();
	            firstRow.addClass('best bg-secondary');
	            
	            if(systemControl == "auto")
	            {
	            	if(!$("#" + lastClickedCoin).closest('tr').hasClass('best'))
	            	{
	            		firstRow.find('button').click();
	            	}
	            }

				// --- Profit today and yesterday RPLANT only --- //

				if ($("#allCoins").find('.active').length > 0 && $("#allCoins").find('.active').closest('tr').find('button').attr('host').includes('rplant'))
				{
				
					var res1 = '';
					profit("<?=date("Y-m-d", strtotime("-1 day"))?>", function(result) {
						res1 = result;
						//console.log(result);
					});

					profit("<?=date("Y-m-d")?>", function(result) {
						$("#moneyToday").html( "<table style=\"width:100%\"><tr><td style=\"width:50%; vertical-align: top\">" + res1 + "</td><td style=\"vertical-align: top\">" + result + "</td></tr></table>");
						//console.log(result);
					});
				}

		    },
		    error: function(xhr, status, error) {
		        console.error('Ошибка при выполнении запроса:', error);
		    }
		});
		
	}

	setInterval(allCoins, 120000);


	setTimeout(function() { 
		pendingBlocks();
	}, 15000);

	function pendingBlocks() {

		if ($("#allCoins").find('.active').length > 0 && $("#allCoins").find('.active').closest('tr').find('button').attr('host').includes('rplant'))
		{
			var active_coin_name 	= $("#allCoins").find('.active').closest('tr').find('button').attr('coin_name');
			var active_coin_asset 	= $("#allCoins").find('.active').closest('tr').find('button').text();
			var active_address 		= $("#allCoins").find('.active').closest('tr').find('button').attr('user');
			var origin_header 		= '<h2><?=$softName?> <?=$version?> - <span class="text-orange">RPLANT</span></h2>';

			$("#header").html(origin_header);

			$.ajax({
				url: 'pending_blocks.php',
				method: 'POST',
				data: { getData : true, active : $("#allCoins").find('.active').text(), active_coin_name : active_coin_name, active_address : active_address },
				success: function(data) {
				    data = JSON.parse(data);
					$("#my_pending_blocks").html(data);
					
					
					// --- BLOCK FOUND --- //

					if($("#block_found").length > 0 && blockFound != parseInt($("#block_found").text()))
					{
						if(blockFound > 0)
						{
							$("#header").html('<h1 class="alert bg-secondary text-orange">* * * BLOCK FOUND * * *</h1>');
							alertFunc(alertPC);
						}

						blockFound = parseInt($("#block_found").text());

						setTimeout(function() { 
							$("#header").html(origin_header);
						}, 15000);
					}

					// --- EFFORT % --- //

					// Найти все элементы с классом "pvm" и извлечь текст времени
					var times = $('.pvm').map(function() {
						return new Date($(this).attr('for')).getTime();
					}).get();

					// Найти самое свежее время
					var freshestTime = new Date(Math.max.apply(null, times));

					// ------ //
					
					var shares = $('.pvm').map(function() {
						return $(this).closest('tr').attr('shares');
					}).get();

					var freshesShare = parseFloat(Math.max.apply(null, shares));

					var ct = new Date();
					// Разница между текущим временем и freshestTime
					var df 				= ((ct - freshestTime) / (1000 * 60)) * 60;


					var network_hashrate 	= parseFloat($("#net_hr").text()??0);
					var network_diff 		= parseFloat($("#net_d").text()??0);						
					//var my_hashrate			= parseInt($("#hashrateSum").text()??0) * 1000;
					var soloSharesNow		= parseFloat($("#soloShares").text()??0);
					var hrs					= parseFloat($("#hrs").text()??0);
					var wcs					= parseInt($("#wcs").text()??0);

					if(wcs > 0 && totalWorkers != wcs)
					{
						$("#wcs").closest('tr').addClass('bg-danger');
					}

					if(soloSharesNow > 0 && network_hashrate > 0)
					{
						var summ	= (soloSharesNow / network_hashrate) * 100000;

						//console.log("wcs: " + wcs + ", soloSharesNow: " + soloSharesNow + ", network_hashrate: " + network_hashrate + ", network_diff: " + network_diff);
					
						$("#cur_effort").html("<h2><b>" +summ.toFixed() + " %</b></h2>");
					}
					
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

	setInterval(pendingBlocks, 30000);

	// ------ //

	setTimeout(function() { 
		herominersApi();
	}, 15000);

	function herominersApi() {

		if ($("#allCoins").find('.active').length > 0 && $("#allCoins").find('.active').closest('tr').find('button').attr('host').includes('herominers'))
		{
			var coin_name 	= $("#allCoins").find('.active').closest('tr').find('button').attr('coin_name');
			var coin_asset 	= $("#allCoins").find('.active').closest('tr').find('button').text();
			var address 	= $("#allCoins").find('.active').closest('tr').find('button').attr('user');
			
			//console.log(coin_name + " " + address);
			
			$("#header").html('<h2><?=$softName?> <?=$version?> - <span class="text-orange">Herominers</span></h2>');

			$.ajax({
				url: 'herominers_api.php',
				method: 'GET',
				data: { coin_name : coin_name, address : address },
				success: function(data) {
				    data = JSON.parse(data);
					//console.log(data);

				    if(data['stats'])
				    {
				    	var htmlData = "<table class=\"table table-striped herominers\">";
				    	htmlData += "<tr><td>Hashrate</td><th>" + (parseFloat(data['stats']['hashrate']) / 1000).toFixed() + " KH/s</th></tr>";
				    	htmlData += "<tr><td>Hashrate 1h</td><th>" + (parseFloat(data['stats']['hashrate_1h']) / 1000).toFixed() + " KH/s</th></tr>";
				    	htmlData += "<tr><td>Hashrate 6h</td><th>" + (parseFloat(data['stats']['hashrate_6h']) / 1000).toFixed() + " KH/s</th></tr>";
				    	htmlData += "<tr><td>Hashrate 24h</td><th>" + (parseFloat(data['stats']['hashrate_24h']) / 1000).toFixed() + " KH/s</th></tr>";

						var unconfirmed = 0;
						$.each(data['unconfirmed'], function(index, value) {
							//console.log(value['reward']);
							unconfirmed += parseFloat(value['reward']);
						});
						htmlData += "<tr><td>Unconfirmed</td><th>" + (unconfirmed / 1000000000000).toFixed(6) + " " + coin_asset + "</th></tr>";
						
						// ------ //
/*
						var unlocked = 0;
						$.each(data['unlocked'], function(index, value) {
							var sp = value.split(":");
							if(!sp[1])
							{
								console.log(sp[0]);
								unlocked += parseFloat(sp[0]);
							}
						});
						htmlData += "<tr><td>Pending</td><th>" + (unlocked / 1000000000000).toFixed(5) + " " + coin_asset + "</th></tr>";
*/

						htmlData += "<tr><td>Pending</td><th>" + (data['stats']['balance'] / 1000000000000).toFixed(6) + " " + coin_asset + "</th></tr>";
						htmlData += "<tr><td>Last 24 Hours Paid</td><th>" + (data['stats']['payments_24h'] / 1000000000000).toFixed(6) + " " + coin_asset + "</th></tr>";
						htmlData += "<tr><td>Last Week Paid</td><th>" + (data['stats']['paid'] / 1000000000000).toFixed(6) + " " + coin_asset + "</th></tr>";
						//htmlData += "<tr><td>Current Payout Estimate</td><th>" + (data['stats']['roundScore'] / 1000000000000).toFixed(6) + " " + coin_asset + "</th></tr>";
						htmlData += "</table>";
						
						$("#herominers_data").html(htmlData);
					}
				},
				error: function(xhr, status, error) {
				    console.error('Ошибка при выполнении запроса:', error);
				}
			});
		}
		else
		{
			$("#herominers_data").html("");
		}
	}

	setInterval(herominersApi, 60000);

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
					var outdatedTime = new Date(specifiedTime.getTime() + 7 * 60000); // 60000 миллисекунд в минуте

					// Сравниваем текущее время с устаревшим временем
					if (currentTime > outdatedTime) {
						$( this ).closest('tr').addClass('bg-danger');
					} else {
						$( this ).closest('tr').removeClass('bg-danger');
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
