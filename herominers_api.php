<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "config.php";

$coin_name	= $_GET['coin_name']; // "zephyr";
$address 	= $_GET['address']; // "ZEPHsBDtuMFeUqifG1VLfagkgEnnp1ph2Uz6eyUViigkjShoC8WjrcXgzZ5f4J9tgNDcHig95xGjC8UzmSRZz3UB6XcnQ3uPj39";

$url = "https://$coin_name.herominers.com/api/stats_address?address=$address";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  'accept: application/json'
));

$response = json_decode(curl_exec($ch), true);

/*
echo '<pre>';
print_r($response);
echo '</pre>';
exit;
*/

echo json_encode($response);
exit;
/*
Array
(
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

    [charts] => Array
        (
            [payments] => Array
                (
                    [0] => Array
                        (
                            [0] => 1707653795
                            [1] => 111200000000
                        )

                    [1] => Array
                        (
                            [0] => 1707667483
                            [1] => 107700000000
                        )

                )

            [hashrate] => Array
                (
                    [0] => Array
                        (
                            [0] => 1708178789
                            [1] => 63521
                            [2] => 4
                        )

                    [1] => Array
                        (
                            [0] => 1708179509
                            [1] => 201306
                            [2] => 4
                        )

                    [2] => Array
                        (
                            [0] => 1708180229
                            [1] => 192928
                            [2] => 4
                        )

                    [3] => Array
                        (
                            [0] => 1708180949
                            [1] => 189608
                            [2] => 4
                        )

                    [4] => Array
                        (
                            [0] => 1708181669
                            [1] => 202171
                            [2] => 4
                        )

                    [5] => Array
                        (
                            [0] => 1708182389
                            [1] => 230535
                            [2] => 4
                        )

                    [6] => Array
                        (
                            [0] => 1708183109
                            [1] => 64020
                            [2] => 4
                        )

                    [7] => Array
                        (
                            [0] => 1708183829
                            [1] => 0
                            [2] => 4
                        )

                    [8] => Array
                        (
                            [0] => 1708184549
                            [1] => 0
                            [2] => 4
                        )

                    [9] => Array
                        (
                            [0] => 1708185269
                            [1] => 0
                            [2] => 4
                        )

                    [10] => Array
                        (
                            [0] => 1708185989
                            [1] => 0
                            [2] => 4
                        )

                    [11] => Array
                        (
                            [0] => 1708186709
                            [1] => 0
                            [2] => 4
                        )

                    [12] => Array
                        (
                            [0] => 1708187429
                            [1] => 0
                            [2] => 4
                        )

                    [13] => Array
                        (
                            [0] => 1708188149
                            [1] => 0
                            [2] => 4
                        )

                    [14] => Array
                        (
                            [0] => 1708188869
                            [1] => 0
                            [2] => 4
                        )

                    [15] => Array
                        (
                            [0] => 1708189589
                            [1] => 0
                            [2] => 4
                        )

                    [16] => Array
                        (
                            [0] => 1708190309
                            [1] => 0
                            [2] => 4
                        )

                    [17] => Array
                        (
                            [0] => 1708191029
                            [1] => 0
                            [2] => 4
                        )

                    [18] => Array
                        (
                            [0] => 1708191749
                            [1] => 0
                            [2] => 4
                        )

                    [19] => Array
                        (
                            [0] => 1708192469
                            [1] => 0
                            [2] => 4
                        )

                    [20] => Array
                        (
                            [0] => 1708193189
                            [1] => 0
                            [2] => 4
                        )

                    [21] => Array
                        (
                            [0] => 1708193909
                            [1] => 0
                            [2] => 4
                        )

                    [22] => Array
                        (
                            [0] => 1708194629
                            [1] => 0
                            [2] => 4
                        )

                    [23] => Array
                        (
                            [0] => 1708195349
                            [1] => 0
                            [2] => 4
                        )

                    [24] => Array
                        (
                            [0] => 1708196069
                            [1] => 0
                            [2] => 4
                        )

                    [25] => Array
                        (
                            [0] => 1708196789
                            [1] => 0
                            [2] => 4
                        )

                    [26] => Array
                        (
                            [0] => 1708197509
                            [1] => 0
                            [2] => 4
                        )

                    [27] => Array
                        (
                            [0] => 1708198229
                            [1] => 0
                            [2] => 4
                        )

                    [28] => Array
                        (
                            [0] => 1708198949
                            [1] => 0
                            [2] => 4
                        )

                    [29] => Array
                        (
                            [0] => 1708199669
                            [1] => 0
                            [2] => 4
                        )

                    [30] => Array
                        (
                            [0] => 1708200389
                            [1] => 0
                            [2] => 4
                        )

                    [31] => Array
                        (
                            [0] => 1708201109
                            [1] => 0
                            [2] => 4
                        )

                    [32] => Array
                        (
                            [0] => 1708201829
                            [1] => 0
                            [2] => 4
                        )

                    [33] => Array
                        (
                            [0] => 1708202549
                            [1] => 0
                            [2] => 4
                        )

                    [34] => Array
                        (
                            [0] => 1708203269
                            [1] => 0
                            [2] => 4
                        )

                    [35] => Array
                        (
                            [0] => 1708203989
                            [1] => 0
                            [2] => 4
                        )

                    [36] => Array
                        (
                            [0] => 1708204709
                            [1] => 0
                            [2] => 4
                        )

                    [37] => Array
                        (
                            [0] => 1708205429
                            [1] => 0
                            [2] => 4
                        )

                    [38] => Array
                        (
                            [0] => 1708206149
                            [1] => 0
                            [2] => 4
                        )

                    [39] => Array
                        (
                            [0] => 1708206869
                            [1] => 0
                            [2] => 4
                        )

                    [40] => Array
                        (
                            [0] => 1708207589
                            [1] => 0
                            [2] => 4
                        )

                    [41] => Array
                        (
                            [0] => 1708208309
                            [1] => 0
                            [2] => 4
                        )

                    [42] => Array
                        (
                            [0] => 1708209029
                            [1] => 0
                            [2] => 4
                        )

                    [43] => Array
                        (
                            [0] => 1708209749
                            [1] => 0
                            [2] => 4
                        )

                    [44] => Array
                        (
                            [0] => 1708210469
                            [1] => 0
                            [2] => 4
                        )

                    [45] => Array
                        (
                            [0] => 1708211189
                            [1] => 0
                            [2] => 4
                        )

                    [46] => Array
                        (
                            [0] => 1708211909
                            [1] => 0
                            [2] => 4
                        )

                    [47] => Array
                        (
                            [0] => 1708212629
                            [1] => 0
                            [2] => 4
                        )

                    [48] => Array
                        (
                            [0] => 1708213349
                            [1] => 0
                            [2] => 4
                        )

                    [49] => Array
                        (
                            [0] => 1708214070
                            [1] => 0
                            [2] => 4
                        )

                    [50] => Array
                        (
                            [0] => 1708214789
                            [1] => 0
                            [2] => 4
                        )

                    [51] => Array
                        (
                            [0] => 1708215509
                            [1] => 0
                            [2] => 4
                        )

                    [52] => Array
                        (
                            [0] => 1708216229
                            [1] => 0
                            [2] => 4
                        )

                    [53] => Array
                        (
                            [0] => 1708216950
                            [1] => 0
                            [2] => 4
                        )

                    [54] => Array
                        (
                            [0] => 1708217670
                            [1] => 0
                            [2] => 4
                        )

                    [55] => Array
                        (
                            [0] => 1708218390
                            [1] => 0
                            [2] => 4
                        )

                    [56] => Array
                        (
                            [0] => 1708219110
                            [1] => 0
                            [2] => 4
                        )

                    [57] => Array
                        (
                            [0] => 1708219830
                            [1] => 0
                            [2] => 4
                        )

                    [58] => Array
                        (
                            [0] => 1708220550
                            [1] => 0
                            [2] => 4
                        )

                    [59] => Array
                        (
                            [0] => 1708221270
                            [1] => 0
                            [2] => 4
                        )

                    [60] => Array
                        (
                            [0] => 1708221990
                            [1] => 0
                            [2] => 4
                        )

                    [61] => Array
                        (
                            [0] => 1708222710
                            [1] => 0
                            [2] => 4
                        )

                    [62] => Array
                        (
                            [0] => 1708223430
                            [1] => 0
                            [2] => 4
                        )

                    [63] => Array
                        (
                            [0] => 1708224150
                            [1] => 0
                            [2] => 4
                        )

                    [64] => Array
                        (
                            [0] => 1708224870
                            [1] => 0
                            [2] => 4
                        )

                    [65] => Array
                        (
                            [0] => 1708225590
                            [1] => 0
                            [2] => 4
                        )

                    [66] => Array
                        (
                            [0] => 1708226310
                            [1] => 0
                            [2] => 4
                        )

                    [67] => Array
                        (
                            [0] => 1708227030
                            [1] => 0
                            [2] => 4
                        )

                    [68] => Array
                        (
                            [0] => 1708227750
                            [1] => 0
                            [2] => 4
                        )

                    [69] => Array
                        (
                            [0] => 1708228470
                            [1] => 0
                            [2] => 4
                        )

                    [70] => Array
                        (
                            [0] => 1708229190
                            [1] => 0
                            [2] => 4
                        )

                    [71] => Array
                        (
                            [0] => 1708229910
                            [1] => 0
                            [2] => 4
                        )

                    [72] => Array
                        (
                            [0] => 1708230630
                            [1] => 0
                            [2] => 4
                        )

                    [73] => Array
                        (
                            [0] => 1708231350
                            [1] => 0
                            [2] => 4
                        )

                    [74] => Array
                        (
                            [0] => 1708232070
                            [1] => 0
                            [2] => 4
                        )

                    [75] => Array
                        (
                            [0] => 1708232790
                            [1] => 0
                            [2] => 4
                        )

                    [76] => Array
                        (
                            [0] => 1708233510
                            [1] => 91055
                            [2] => 4
                        )

                    [77] => Array
                        (
                            [0] => 1708234230
                            [1] => 186249
                            [2] => 4
                        )

                    [78] => Array
                        (
                            [0] => 1708234950
                            [1] => 183930
                            [2] => 4
                        )

                    [79] => Array
                        (
                            [0] => 1708235670
                            [1] => 179349
                            [2] => 4
                        )

                    [80] => Array
                        (
                            [0] => 1708236390
                            [1] => 213024
                            [2] => 4
                        )

                    [81] => Array
                        (
                            [0] => 1708237110
                            [1] => 179079
                            [2] => 4
                        )

                    [82] => Array
                        (
                            [0] => 1708237830
                            [1] => 184866
                            [2] => 4
                        )

                    [83] => Array
                        (
                            [0] => 1708238550
                            [1] => 210701
                            [2] => 4
                        )

                    [84] => Array
                        (
                            [0] => 1708239270
                            [1] => 201542
                            [2] => 4
                        )

                    [85] => Array
                        (
                            [0] => 1708239990
                            [1] => 202515
                            [2] => 4
                        )

                    [86] => Array
                        (
                            [0] => 1708240710
                            [1] => 198038
                            [2] => 4
                        )

                    [87] => Array
                        (
                            [0] => 1708241430
                            [1] => 192434
                            [2] => 4
                        )

                    [88] => Array
                        (
                            [0] => 1708242150
                            [1] => 204786
                            [2] => 1
                        )

                )

        )

    [workers] => Array
        (
            [0] => Array
                (
                    [name] => 214
                    [hashrate] => 10643
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242184
                    [shares_good] => 500
                    [shares_invalid] => 0
                    [shares_stale] => 1
                    [lastJobDifficulty] => 507544
                    [hashes] => 183989409
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 183989409
                    [hashrate_1h] => 12628.8
                    [hashrate_6h] => 5588.0333333333
                    [hashrate_24h] => 2930.1460674157
                )

            [1] => Array
                (
                    [name] => 210
                    [hashrate] => 15609
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242182
                    [shares_good] => 487
                    [shares_invalid] => 0
                    [shares_stale] => 0
                    [lastJobDifficulty] => 492928
                    [hashes] => 189410749
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 189410749
                    [hashrate_1h] => 15401.4
                    [hashrate_6h] => 5979.8666666667
                    [hashrate_24h] => 2998.0337078652
                )

            [2] => Array
                (
                    [name] => 207
                    [hashrate] => 15000
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242157
                    [shares_good] => 539
                    [shares_invalid] => 0
                    [shares_stale] => 0
                    [lastJobDifficulty] => 352653
                    [hashes] => 189733554
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 189733554
                    [hashrate_1h] => 13807.2
                    [hashrate_6h] => 6038.5
                    [hashrate_24h] => 2974.9213483146
                )

            [3] => Array
                (
                    [name] => 216
                    [hashrate] => 27161
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242216
                    [shares_good] => 511
                    [shares_invalid] => 0
                    [shares_stale] => 0
                    [lastJobDifficulty] => 1165886
                    [hashes] => 198007083
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 198007083
                    [hashrate_1h] => 15300.4
                    [hashrate_6h] => 6162.9333333333
                    [hashrate_24h] => 3090.5168539326
                )

            [4] => Array
                (
                    [name] => 201
                    [hashrate] => 6552
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242214
                    [shares_good] => 456
                    [shares_invalid] => 0
                    [shares_stale] => 0
                    [lastJobDifficulty] => 170926
                    [hashes] => 96690094
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 96690094
                    [hashrate_1h] => 6335.8
                    [hashrate_6h] => 3110
                    [hashrate_24h] => 1513.3820224719
                )

            [5] => Array
                (
                    [name] => 208
                    [hashrate] => 11727
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242227
                    [shares_good] => 510
                    [shares_invalid] => 0
                    [shares_stale] => 0
                    [lastJobDifficulty] => 390907
                    [hashes] => 179415807
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 179415807
                    [hashrate_1h] => 13030.6
                    [hashrate_6h] => 5669.5
                    [hashrate_24h] => 2812.191011236
                )

            [6] => Array
                (
                    [name] => 211
                    [hashrate] => 18800
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242211
                    [shares_good] => 806
                    [shares_invalid] => 0
                    [shares_stale] => 3
                    [lastJobDifficulty] => 120000
                    [hashes] => 174255987
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 174255987
                    [hashrate_1h] => 16505.6
                    [hashrate_6h] => 5280.9333333333
                    [hashrate_24h] => 2609.0795454545
                )

            [7] => Array
                (
                    [name] => 215
                    [hashrate] => 13494
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242186
                    [shares_good] => 466
                    [shares_invalid] => 0
                    [shares_stale] => 0
                    [lastJobDifficulty] => 395772
                    [hashes] => 211567103
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 211567103
                    [hashrate_1h] => 14464.2
                    [hashrate_6h] => 6721.9
                    [hashrate_24h] => 3318.4831460674
                )

            [8] => Array
                (
                    [name] => 205
                    [hashrate] => 13279
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242201
                    [shares_good] => 477
                    [shares_invalid] => 0
                    [shares_stale] => 0
                    [lastJobDifficulty] => 307817
                    [hashes] => 109748082
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 109748082
                    [hashrate_1h] => 11193.6
                    [hashrate_6h] => 3552.2666666667
                    [hashrate_24h] => 1729.3370786517
                )

            [9] => Array
                (
                    [name] => 203
                    [hashrate] => 7887
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242120
                    [shares_good] => 426
                    [shares_invalid] => 0
                    [shares_stale] => 1
                    [lastJobDifficulty] => 192642
                    [hashes] => 93054231
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 93054231
                    [hashrate_1h] => 7991
                    [hashrate_6h] => 3173.0666666667
                    [hashrate_24h] => 1488.2247191011
                )

            [10] => Array
                (
                    [name] => 213
                    [hashrate] => 11272
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242222
                    [shares_good] => 493
                    [shares_invalid] => 0
                    [shares_stale] => 0
                    [lastJobDifficulty] => 422714
                    [hashes] => 200198696
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 200198696
                    [hashrate_1h] => 14541.4
                    [hashrate_6h] => 6421.2333333333
                    [hashrate_24h] => 3142.8651685393
                )

            [11] => Array
                (
                    [name] => 217
                    [hashrate] => 14429
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242190
                    [shares_good] => 630
                    [shares_invalid] => 0
                    [shares_stale] => 0
                    [lastJobDifficulty] => 480000
                    [hashes] => 162091706
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 162091706
                    [hashrate_1h] => 14859
                    [hashrate_6h] => 5103.1666666667
                    [hashrate_24h] => 2569.5280898876
                )

            [12] => Array
                (
                    [name] => 204
                    [hashrate] => 16663
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242223
                    [shares_good] => 587
                    [shares_invalid] => 0
                    [shares_stale] => 0
                    [lastJobDifficulty] => 373839
                    [hashes] => 234120708
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 234120708
                    [hashrate_1h] => 17294.2
                    [hashrate_6h] => 7629.5
                    [hashrate_24h] => 3712.1685393258
                )

            [13] => Array
                (
                    [name] => 202
                    [hashrate] => 6613
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242147
                    [shares_good] => 422
                    [shares_invalid] => 40
                    [shares_stale] => 1
                    [lastJobDifficulty] => 208841
                    [hashes] => 90779040
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 90779040
                    [hashrate_1h] => 6680.8
                    [hashrate_6h] => 2417.5
                    [hashrate_24h] => 1444.7078651685
                )

            [14] => Array
                (
                    [name] => 212
                    [hashrate] => 27675
                    [region] => eu-fi
                    [port] => 1123
                    [agent] => XMRig/6.21.0 (Linux x86_64) libuv/1.44.2 gcc/9.4.0
                    [stratum] => XMR
                    [solo] => false
                    [lastShare] => 1708242222
                    [shares_good] => 509
                    [shares_invalid] => 0
                    [shares_stale] => 0
                    [lastJobDifficulty] => 738004
                    [hashes] => 237411751
                    [solo_hashes] => 0
                    [hashesSinceBlock] => 237411751
                    [hashrate_1h] => 18658
                    [hashrate_6h] => 7033
                    [hashrate_24h] => 3516.6931818182
                )

        )

    [unlocked] => Array
        (
            [0] => 188771:2d5ee9be00dceb96b08848833c16596554c2224b74cef467568db0af2320af94:219059745698:10763867981443:1918289059:45517358:253815759847:unlocked:f4200300:eu-ru:prop:253815759847:45517358:253107499367
            [1] => 1708235513
            [2] => 188768:421fbab8f29f037f8cbd2a4143252dbe0bc6f58e44a2b453c2d60aacd0fcbc1e:213309572817:10721718336496:2106870816:65311143:328879682036:unlocked:64551100:eu-de:prop:328879682036:65071143:328162216520
            [3] => 1708235216
            [4] => 188763:7bb0ee40dc24f8c7455911926ac7446bdc90fe1aaccd8deec0a229faa4366937:212657773816:10777349541779:2250720969:49994532:237453539791:unlocked:ae7f0b00:oc-au:prop:237453539791:49994532:237239212240
            [5] => 1708234832
            [6] => 188761:4a4fdb025e030e4d2995122327460b4a13a78806de3d17c48c413e4888e3091f:213893082031:10752855511962:3021506707:27142840:95846501840:unlocked:ac322990:as-hk:prop:95846501840:27142840:95725840582
            [7] => 1708234560
            [8] => 188760:d473d62ef93f88729534544f676d0e8c691c5d2db2e153a71538f58260394051:213096109340:10725394857067:2259497874:133109568:627027181565:unlocked:c82d0900:oc-au:prop:627027181565:133109568:626158500943
            [9] => 1708234451
            [10] => 188755:21a6277f3fe4ea3e961e4f63065ec7f8324c90cb941e8f97c2be2176c62c1fe5:207825684537:10721851262741:1852738707:4736844:27267975136:unlocked:ad530200:as-sg:prop:27267975136:4736844:27165539852
            [11] => 1708233735
            [12] => 188754:c39ae9a6faa1ebbe12e6db4829a6c57b95aa17eedc6067628e66a99647b20070:203567403307:10724368527904:2396228365:59949480:266133484344:unlocked:668f05e1:as-hk:prop:266133484344:59889480:265624250165
            [13] => 1708233703
            [14] => 188378:3e82e6196aa31f032298cdd02d8394ac51012a434b570f8398d8288960730faa:203532033388:10728802835361:2591717758::13874773430:unlocked:970e00a0:eu-de:prop:13874773430:30485631:125064025328
            [15] => 1708188760
            [16] => 188335:0a451e354d15ba21b61d0c9c5ef6dd35aa781dd8664e2165d6859ed8ca5c3e97:263555288907:10728155004328:2321452453:121707740:557845488480:unlocked:98a40d00:eu-de:prop:557845488480:121707740:557387338684
            [17] => 1708183014
            [18] => 188332:b5bf1614fd0264c961353b1a39020879745ec7efb4f8f2b3de2a7da5efa6ce89:257300144512:10726177372139:2897523684:28735839:105768494440:unlocked:097f0600:as-hk:prop:105768494440:28735839:105418180480
            [19] => 1708182299
            [20] => 188330:07150ef6040fabb240fc787f4d841c6978e305c14afe0e305c7ffdb32ba76ee2:246468816531:10726197830728:3418501091:81034662:252674719249:unlocked:8c371900:as-hk:prop:252674719249:81034662:251973320603
            [21] => 1708182165
            [22] => 188326:8b7241e313e9be20699257d3c477be169d9a7ec1fea218a22fb234479351c91c:238896437109:10726238748023:2940505469:89320812:323578540430:unlocked:a769004a:eu-de:prop:323578540430:89320812:322887910734
            [23] => 1708181842
            [24] => 188322:590904ab501a10befd160c4ff6a6c1d7cccf2c086906e6f399e3439c45505ca8:229593223436:10726279665474:2680565922:5592540:22254535170:unlocked:2eb90111:as-hk:prop:22254535170:5592540:22177131800
            [25] => 1708181426
            [26] => 188321:eebeec16258a2e0fcf7bf55587fc7b6834bc72c1ad5dce886358400d0f0696e6:259191610262:10846743974862:3856738893:4676548:13122248309:unlocked:c4940200:eu-de:prop:13122248309:4676548:13034014583
            [27] => 1708181398
            [28] => 188320:ac0ed7aba82f871267cb898932bb9812e9bb48709e4fe77ccdeb2230a5c7912a:251067324690:10726300124258:2130028954:82172644:410187582346:unlocked:1d4bdf02:as-hk:prop:410187582346:82172644:410076944665
            [29] => 1708181381
            [30] => 188319:a19b779bdfe9efc79f0ead920a8c69eff5ecc1097ca7a643469e90200cd72b3f:252985191929:10782952113665:3820319157:3009995:8525133394:unlocked:80900500:eu-de:prop:8525133394:3009995:8419328580
            [31] => 1708180853
            [32] => 188318:a7fb8ac390cdd79b285d835dfd8d08610b654a84bc193bf46d7f38035ede438e:249314952051:0:0:30485631:111720473256:orphaned:e26b0900:na-us2:prop:111720473256:30485631:111542283253
            [33] => 1708180842
            [34] => 188317:b33f57394e96597ed9745f94c7a1c06e3c29b1cc6cda32dbfd94bd5948894e66:253849631467:10759928892507:2316093153:11465767:52871644823:unlocked:2fdb0700:na-us2:prop:52871644823:11465767:52787384608
            [35] => 1708180700
            [36] => 188316:13e7428ab7dca84568412fc9f6378379523658fd207e2dc219d32e03548e2c5b:249445655890:10726341041943:3254264718:40973380:134074672120:unlocked:83671c1e:eu-de:prop:134074672120:40973380:133836376353
            [37] => 1708180629
            [38] => 188315:bae6e477e7e78576736007e5b95302051139b7719470edde2a92ee823ab709d9:242994473533:10727421191389:2503307899:10496597:44825949843:unlocked:685f0cff:as-hk:prop:44825949843:10496597:44576220283
            [39] => 1708180454
        )

    [unlocked_daily] => Array
        (
            [0] => 1193596553
            [1] => 0
            [2] => 0
            [3] => 0
            [4] => 0
            [5] => 54570764571
            [6] => 15805852497
        )

    [unconfirmed] => Array
        (
            [0] => Array
                (
                    [height] => 188834
                    [hash] => 966ac73c7aed804e067e82ccb7f2fb347878c3b8526754a0be89f3859c9d2092
                    [time] => 1708242082
                    [difficulty] => 291237070892
                    [totalShares] => 250202794680
                    [shares] => 56772743
                    [totalScore] => 249502873776
                    [score] => 56772743
                    [reward] => 2417714748
                    [nonce] => 48162000
                    [blockReward] => 10721784637657
                    [status] => pending
                    [region] => eu-ru
                    [rewardScheme] => prop
                    [totalSharesAdj] => 250202794680
                )

            [1] => Array
                (
                    [height] => 188831
                    [hash] => 2a56c69f8a6416b0b70617d15001641a94f93e74ce74fa952299fe7c5ef1fa49
                    [time] => 1708241796
                    [difficulty] => 312829199244
                    [totalShares] => 274018463933
                    [shares] => 63126037
                    [totalScore] => 273889210402
                    [score] => 63126037
                    [reward] => 2474572088
                    [nonce] => 07a24328
                    [blockReward] => 10834100350867
                    [status] => pending
                    [region] => as-hk
                    [rewardScheme] => prop
                    [totalSharesAdj] => 274018463933
                )

            [2] => Array
                (
                    [height] => 188830
                    [hash] => 8b37c74f59c893a4e927802c3dd58f813faaa117455e79ad814f004f8e745df8
                    [time] => 1708241483
                    [difficulty] => 302769443040
                    [totalShares] => 928919433662
                    [shares] => 210716957
                    [totalScore] => 927618174533
                    [score] => 210716957
                    [reward] => 2519467708
                    [nonce] => 8fb14669
                    [blockReward] => 11191927935290
                    [status] => pending
                    [region] => eu-de
                    [rewardScheme] => prop
                    [totalSharesAdj] => 928919433662
                )

            [3] => Array
                (
                    [height] => 188823
                    [hash] => 53732d46659e0074e75109d98f1042a81208020021475983c41e427b3e6a96ee
                    [time] => 1708240392
                    [difficulty] => 334246437747
                    [totalShares] => 277141672782
                    [shares] => 68495023
                    [totalScore] => 276982394785
                    [score] => 68185345
                    [reward] => 2615496512
                    [nonce] => ef780000
                    [blockReward] => 10721155974469
                    [status] => pending
                    [region] => oc-au
                    [rewardScheme] => prop
                    [totalSharesAdj] => 277141672782
                )

            [4] => Array
                (
                    [height] => 188822
                    [hash] => d8fc2b46307f712be631e31edc7b39f196b29ab970be5c41eb94ff62b1c83cea
                    [time] => 1708240068
                    [difficulty] => 324470746568
                    [totalShares] => 9148345785
                    [shares] => 2346308
                    [totalScore] => 9050203445
                    [score] => 2346308
                    [reward] => 2754497371
                    [nonce] => 4a080000
                    [blockReward] => 10721166198971
                    [status] => pending
                    [region] => oc-au
                    [rewardScheme] => prop
                    [totalSharesAdj] => 9148345785
                )

            [5] => Array
                (
                    [height] => 188821
                    [hash] => ad72de992e430138b485d516ce7e3e2eac683fcaec430c412fe2337316c135b9
                    [time] => 1708240058
                    [difficulty] => 319791950145
                    [totalShares] => 18318021906
                    [shares] => 2983274
                    [totalScore] => 18179641474
                    [score] => 2983274
                    [reward] => 1745247965
                    [nonce] => ec170300
                    [blockReward] => 10731876263482
                    [status] => pending
                    [region] => na-us2
                    [rewardScheme] => prop
                    [totalSharesAdj] => 18318021906
                )

            [6] => Array
                (
                    [height] => 188820
                    [hash] => d4fcc1e75257ef2eab3973fd4d19f91cdd8aff916c9cb059513bc5d8c038613e
                    [time] => 1708240036
                    [difficulty] => 313508766177
                    [totalShares] => 201045342113
                    [shares] => 50497872
                    [totalScore] => 200250485208
                    [score] => 50497872
                    [reward] => 2680039804
                    [nonce] => dd5b0060
                    [blockReward] => 10724278648002
                    [status] => pending
                    [region] => eu-de
                    [rewardScheme] => prop
                    [totalSharesAdj] => 201045342113
                )

            [7] => Array
                (
                    [height] => 188816
                    [hash] => c3f6553b241e11cf7ca034fb07a80c3366624878b9f66c839ace1929713f0ce8
                    [time] => 1708239799
                    [difficulty] => 283482437626
                    [totalShares] => 476808331989
                    [shares] => 117292769
                    [totalScore] => 475530745181
                    [score] => 117292769
                    [reward] => 2620660759
                    [nonce] => 19850100
                    [blockReward] => 10721227546181
                    [status] => pending
                    [region] => eu-de
                    [rewardScheme] => prop
                    [totalSharesAdj] => 476808331989
                )

            [8] => Array
                (
                    [height] => 188807
                    [hash] => 7beafea43d5753abaf61c4656431bc120ac904720ef4e5acbdc12ecc7748337f
                    [time] => 1708239227
                    [difficulty] => 252975042731
                    [totalShares] => 58874530523
                    [shares] => 11795528
                    [totalScore] => 58645923308
                    [score] => 11795528
                    [reward] => 2137197870
                    [nonce] => fb260e42
                    [blockReward] => 10722388047653
                    [status] => pending
                    [region] => as-kr
                    [rewardScheme] => prop
                    [totalSharesAdj] => 58874530523
                )

            [9] => Array
                (
                    [height] => 188806
                    [hash] => f1901339fcf9ab6f62ac3a4fb48b5f57a120ff7aa90c5c4faa88fd1dfce2a7e2
                    [time] => 1708239155
                    [difficulty] => 263285077018
                    [totalShares] => 24929718148
                    [shares] => 7403336
                    [totalScore] => 24782700311
                    [score] => 7403336
                    [reward] => 3188161654
                    [nonce] => e42a0028
                    [blockReward] => 10769309152310
                    [status] => pending
                    [region] => as-sg
                    [rewardScheme] => prop
                    [totalSharesAdj] => 24929718148
                )

            [10] => Array
                (
                    [height] => 188805
                    [hash] => a39dda62c2698af3a78b92001272714fd9872ee07e08be071b437d38fa73eabf
                    [time] => 1708239126
                    [difficulty] => 256187826221
                    [totalShares] => 386176124023
                    [shares] => 88611087
                    [totalScore] => 385370356426
                    [score] => 88611087
                    [reward] => 2443050720
                    [nonce] => 7b1719fd
                    [blockReward] => 10721340016977
                    [status] => pending
                    [region] => as-hk
                    [rewardScheme] => prop
                    [totalSharesAdj] => 386176124023
                )

            [11] => Array
                (
                    [height] => 188801
                    [hash] => 6f50cb5f2f997ee60d970d6f28d7e8c7b13a803726fb498e5641499924826b9b
                    [time] => 1708238673
                    [difficulty] => 246594525729
                    [totalShares] => 35783473066
                    [shares] => 9272481
                    [totalScore] => 35704147231
                    [score] => 9212481
                    [reward] => 2744000656
                    [nonce] => 4c520300
                    [blockReward] => 10731306915740
                    [status] => pending
                    [region] => na-us
                    [rewardScheme] => prop
                    [totalSharesAdj] => 35783473066
                )

            [12] => Array
                (
                    [height] => 188800
                    [hash] => bab0d6a42e4d885879411e7e6385d5836a8af4a2fe3fa72f9bac974f0599a2a3
                    [time] => 1708238630
                    [difficulty] => 246671337314
                    [totalShares] => 99096514008
                    [shares] => 24712348
                    [totalScore] => 99029368354
                    [score] => 24712348
                    [reward] => 2652840463
                    [nonce] => 13d20c00
                    [blockReward] => 10727227140457
                    [status] => pending
                    [region] => na-us2
                    [rewardScheme] => prop
                    [totalSharesAdj] => 99096514008
                )

            [13] => Array
                (
                    [height] => 188799
                    [hash] => fd17a6a8ef7e71f6f514d5a2d2dfc32bda9767986ebf4df876ff057ac72a83c4
                    [time] => 1708238511
                    [difficulty] => 268463741815
                    [totalShares] => 101297383076
                    [shares] => 26087199
                    [totalScore] => 101123151465
                    [score] => 26087199
                    [reward] => 2769123948
                    [nonce] => a60f2658
                    [blockReward] => 10831581845181
                    [status] => pending
                    [region] => as-hk
                    [rewardScheme] => prop
                    [totalSharesAdj] => 101297383076
                )

            [14] => Array
                (
                    [height] => 188798
                    [hash] => a19fe28e8065c737985daf46919d7c14ea2dcf04804b9e06de52ba8fcd2484bf
                    [time] => 1708238390
                    [difficulty] => 260487949914
                    [totalShares] => 359123984534
                    [shares] => 88498632
                    [totalScore] => 358914125713
                    [score] => 88498632
                    [reward] => 2619821063
                    [nonce] => 3fa22f00
                    [blockReward] => 10721411589916
                    [status] => pending
                    [region] => eu-ru
                    [rewardScheme] => prop
                    [totalSharesAdj] => 359123984534
                )

            [15] => Array
                (
                    [height] => 188797
                    [hash] => be97124aaacd71da1f63c3362b5d32d2788d39421bc363883ba5bb41a5c7cddf
                    [time] => 1708237968
                    [difficulty] => 270535753889
                    [totalShares] => 221058087277
                    [shares] => 47171149
                    [totalScore] => 220842707403
                    [score] => 47072590
                    [reward] => 2275246756
                    [nonce] => 118602b6
                    [blockReward] => 10771342054661
                    [status] => pending
                    [region] => as-kr
                    [rewardScheme] => prop
                    [totalSharesAdj] => 221058087277
                )

            [16] => Array
                (
                    [height] => 188795
                    [hash] => 16787d01071c480c39e2b6c2bf72b3cf6c8924d4a97c2406d41cb576e3ca4c71
                    [time] => 1708237709
                    [difficulty] => 258966484065
                    [totalShares] => 71654600158
                    [shares] => 15466384
                    [totalScore] => 71381243388
                    [score] => 15466384
                    [reward] => 2302139019
                    [nonce] => 5a390e55
                    [blockReward] => 10721442264180
                    [status] => pending
                    [region] => as-hk
                    [rewardScheme] => prop
                    [totalSharesAdj] => 71654600158
                )

            [17] => Array
                (
                    [height] => 188793
                    [hash] => 2de520a6ac0396fb99af344316eea083181b245e4f44cdcf0b95460aa79b08d7
                    [time] => 1708237625
                    [difficulty] => 253817251084
                    [totalShares] => 95319762097
                    [shares] => 22221152
                    [totalScore] => 95125129668
                    [score] => 22221152
                    [reward] => 2482231603
                    [nonce] => d6a53100
                    [blockReward] => 10722531673737
                    [status] => pending
                    [region] => eu-de
                    [rewardScheme] => prop
                    [totalSharesAdj] => 95319762097
                )

            [18] => Array
                (
                    [height] => 188792
                    [hash] => ad9174afb55a963c7788ed096955d413cb9896d56fd0fea26a70073c35df0eeb
                    [time] => 1708237512
                    [difficulty] => 249189367017
                    [totalShares] => 87767686359
                    [shares] => 23711614
                    [totalScore] => 87511434990
                    [score] => 23711614
                    [reward] => 2880004145
                    [nonce] => 8ec60500
                    [blockReward] => 10725638298531
                    [status] => pending
                    [region] => as-hk
                    [rewardScheme] => prop
                    [totalSharesAdj] => 87767686359
                )

            [19] => Array
                (
                    [height] => 188790
                    [hash] => 2bc0fcc8f7fe8ff1ab372c64b7c3a57b30cd8c268303f33b8a2f294a6bc7bf71
                    [time] => 1708237410
                    [difficulty] => 245154982198
                    [totalShares] => 241080985182
                    [shares] => 38504305
                    [totalScore] => 433531454426
                    [score] => 38504305
                    [reward] => 944238214
                    [nonce] => c8720500
                    [blockReward] => 10728011788146
                    [status] => pending
                    [region] => as-hk
                    [rewardScheme] => prop
                    [totalSharesAdj] => 241080985182
                )

            [20] => Array
                (
                    [height] => 188786
                    [hash] => c2f5cd298ff861af06ec46a670040fac8ed96139caae97cda8680b1ccd395154
                    [time] => 1708237124
                    [difficulty] => 243210245722
                    [totalShares] => 286754063364
                    [shares] => 64022025
                    [totalScore] => 286291368327
                    [score] => 64022025
                    [reward] => 2376997371
                    [nonce] => 72180656
                    [blockReward] => 10725903727496
                    [status] => pending
                    [region] => as-kr
                    [rewardScheme] => prop
                    [totalSharesAdj] => 286754063364
                )

            [21] => Array
                (
                    [height] => 188784
                    [hash] => 269b4afb6e7226fdbce786485934a44f8761e3da43dcb92db7bd505fa7865466
                    [time] => 1708236786
                    [difficulty] => 237469129216
                    [totalShares] => 584340023831
                    [shares] => 138051395
                    [totalScore] => 583199131324
                    [score] => 138051395
                    [reward] => 2524105972
                    [nonce] => c22f03f3
                    [blockReward] => 10759943697228
                    [status] => pending
                    [region] => eu-de
                    [rewardScheme] => prop
                    [totalSharesAdj] => 584340023831
                )

            [22] => Array
                (
                    [height] => 188776
                    [hash] => adae66cedc6acb22b93c6390bfd9fa365d5ffa91d41e2115f62e0a42d75e81ea
                    [time] => 1708236099
                    [difficulty] => 226739363247
                    [totalShares] => 497730497476
                    [shares] => 124021053
                    [totalScore] => 497159230738
                    [score] => 124021053
                    [reward] => 2650541705
                    [nonce] => b00a0d00
                    [blockReward] => 10721636536550
                    [status] => pending
                    [region] => na-us2
                    [rewardScheme] => prop
                    [totalSharesAdj] => 497730497476
                )

        )

    [createdAt] => 1708242228
)
*/
?>
