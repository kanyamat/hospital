<?php

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('pA2wlxsD5Qgoxvknv3vCo9OY10G0wL79gsYiZ2LFdwaAkPLpK6gFTCanZCeOa19aMo9zKsd0fAFqoUb+8E0j1FL6tioXbgrXusCgfacGVtwPnimvmV9/DtvbZpGEAbHr2b/FWZOV/T6ia8Q6dMFwOwdB04t89/1O/w1cDnyilFU=');
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => '1d20278bbe15f167b3245df7a1413d2e']);


$response = $bot->replyText('<reply token>', 'hello!');

?>