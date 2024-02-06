<?php


// -- Настройки путей -- //
$path_xmrig 	= "/home/laptopsr/MINERS/xmrig/xmrig";
$path_cpuminer	= "/home/laptopsr/MINERS/cpuminer-rplant/cpuminer-sse2";
$path_syslog	= "/var/log/syslog";
$path_xmriglog 	= "/home/laptopsr/xmrig.log";

// -- Монеты для быстрого перехода -- //
$coins = [
	['coin' => 'AVN', 'miner' => 'cpuminer-sse2', 'host' => 'stratum-eu.rplant.xyz:7068', 'algo' => 'minotaurx', 'user' => 'xxx', 'theads' => 'manual', 'reward' => 1187.5],
	['coin' => 'GPRX', 'miner' => 'xmrig', 'host' => 'stratum-eu.rplant.xyz:7031', 'algo' => 'gr', 'user' => 'xxx', 'theads' => 'auto', 'reward' => 1500],
	['coin' => 'BBC', 'miner' => 'xmrig', 'host' => 'stratum-eu.rplant.xyz:7082', 'algo' => 'gr', 'user' => 'xxx', 'theads' => 'auto', 'reward' => 3750],
	['coin' => 'FSC', 'miner' => 'xmrig', 'host' => 'stratum-eu.rplant.xyz:7095', 'algo' => 'gr', 'user' => 'xxx', 'theads' => 'auto', 'reward' => 1750],
	['coin' => 'MECU', 'miner' => 'xmrig', 'host' => 'stratum-eu.rplant.xyz:7094', 'algo' => 'gr', 'user' => 'xxx', 'theads' => 'auto', 'reward' => 25.2],
	['coin' => 'MAXE', 'miner' => 'xmrig', 'host' => 'stratum-eu.rplant.xyz:7028', 'algo' => 'gr', 'user' => 'xxx', 'theads' => 'auto', 'reward' => 630],
	['coin' => 'NIKI', 'miner' => 'xmrig', 'host' => 'stratum-eu.rplant.xyz:7099', 'algo' => 'gr', 'user' => 'xxx', 'theads' => 'auto', 'reward' => 2000],
	['coin' => 'SKYT', 'miner' => 'xmrig', 'host' => 'stratum-eu.rplant.xyz:7084', 'algo' => 'gr', 'user' => 'xxx', 'theads' => 'auto', 'reward' => 1500],
	['coin' => 'SUBI', 'miner' => 'xmrig', 'host' => 'stratum-eu.rplant.xyz:7090', 'algo' => 'gr', 'user' => 'xxx', 'theads' => 'auto', 'reward' => 1500],
	['coin' => 'VISH', 'miner' => 'cpuminer-sse2', 'host' => 'stratum-eu.rplant.xyz:7079', 'algo' => 'yespower', 'user' => 'xxx', 'theads' => 'manual', 'reward' => 2707],
];

// -- Воркеры -- //
$arr = [
	['host' => '192.168.1.201', 'user' => 'laptopsr', 'pass' => '111111', 'worker' => '201', 'theads' => 5],
	['host' => '192.168.1.202', 'user' => 'laptopsr', 'pass' => '111111', 'worker' => '202', 'theads' => 6],
	['host' => '192.168.1.203', 'user' => 'laptopsr', 'pass' => '111111', 'worker' => '203', 'theads' => 5],
	['host' => '192.168.1.204', 'user' => 'laptopsr', 'pass' => '111111', 'worker' => '204', 'theads' => 12],
	['host' => '192.168.1.205', 'user' => 'laptopsr', 'pass' => '111111', 'worker' => '205', 'theads' => 7],
	['host' => '192.168.1.207', 'user' => 'laptopsr', 'pass' => '111111', 'worker' => '207', 'theads' => 11],
	['host' => '192.168.1.208', 'user' => 'laptopsr', 'pass' => '111111', 'worker' => '208', 'theads' => 11],
	['host' => '192.168.1.210', 'user' => 'laptopsr', 'pass' => '111111', 'worker' => '210', 'theads' => 11],
	['host' => '192.168.1.211', 'user' => 'laptopsr', 'pass' => '111111', 'worker' => '211', 'theads' => 12],
	['host' => '192.168.1.212', 'user' => 'laptopsr', 'pass' => '111111', 'worker' => '212', 'theads' => 12],
	['host' => '192.168.1.213', 'user' => 'laptopsr', 'pass' => '111111', 'worker' => '213', 'theads' => 11],
	['host' => '192.168.1.214', 'user' => 'laptopsr', 'pass' => '111111', 'worker' => '214', 'theads' => 11],
	['host' => '192.168.1.215', 'user' => 'laptopsr', 'pass' => '111111', 'worker' => '215', 'theads' => 11],
	['host' => '192.168.1.246', 'user' => 'laptopsr', 'pass' => '111111', 'worker' => '246', 'theads' => 4],
];

?>
