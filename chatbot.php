<?php

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('X8ZPAwTyBxGjoLM1gelIBKpSXjMlmHOVUyRxe5K0suVS5D3BoPdl2OZZkPRiQih5Mo9zKsd0fAFqoUb+8E0j1FL6tioXbgrXusCgfacGVty5mH62yH0De2TsPUUAb53pTWzsnLMTUnI0cM96J7oY0AdB04t89/1O/w1cDnyilFU=');
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => '929b61468a8a14c77a37c2e124aa7183']);

$replyToken  = $events['events'][0]['replyToken'];

//$response = $this->bot->replyText($replyToken, 'hello!');
$response = $bot->replyText('<reply token>', 'hello!');

// $outputText = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("text");
// $bot->replyMessage($event->getReplyToken(), $outputText);


?>