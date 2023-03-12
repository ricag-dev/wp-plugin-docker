<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$ran_h  = random_int(8, 12);
$ran_m  = random_int(1, 59);
$h      = 3600;
$m      = 60;

$time_p = time()+(($h*$ran_h)+($m*$ran_m));
add_option('BigMasterWebCHT_cron_verifique_version', (string)$time_p);

add_option('BigMasterWebCHT_updates_pending', 0);
add_option('BigMasterWebCHT_user_token');