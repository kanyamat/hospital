<?php
################################## DATABASE ##################################
$conn_string = "host=ec2-54-227-247-225.compute-1.amazonaws.com port=5432 dbname=d7i9sj05534uua user=twyavrmgujwujj password=78cac0794ff9469d19800c70521b078dd1e2505ebf978e4239f7f7393d3916a8";
$dbconn = pg_pconnect($conn_string);
if (!$dbconn) {
    die("Connection failed: " . mysqli_connect_error());
}
##############################################################################
$access_token = 'X8ZPAwTyBxGjoLM1gelIBKpSXjMlmHOVUyRxe5K0suVS5D3BoPdl2OZZkPRiQih5Mo9zKsd0fAFqoUb+8E0j1FL6tioXbgrXusCgfacGVty5mH62yH0De2TsPUUAb53pTWzsnLMTUnI0cM96J7oY0AdB04t89/1O/w1cDnyilFU=';
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
//$curr_years = date("Y");
//$curr_y = ($curr_years+ 543);
$_msg = $events['events'][0]['message']['text'];
$user = $events['events'][0]['source']['userId'];
$user_id = pg_escape_string($user);
$u = pg_escape_string($_msg);  
$check_q = pg_query($dbconn,"SELECT seqcode, sender_id ,updated_at  FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1 ");
                while ($row = pg_fetch_row($check_q)) {
                  echo $seqcode =  $row[0];
                  echo $sender = $row[2]; 
                } 
// $check_user = pg_query($dbconn,"SELECT*FROM users  WHERE $user_id  = '{$user_id}' ");
//****************ทดสอบ
       // $d = date("D");
       // $h = date("H:i");
//****************ทดสอบ จบ
// Validate parsed JSON data
if (!is_null($events['events'])) {
 // Loop through each event
 foreach ($events['events'] as $event) {
  // Reply only when message sent is in 'text' format
  // if ($event['message']['text'] == "ต้องการผู้ช่วย") {
 if (strpos($_msg, 'hello') !== false || strpos($_msg, 'สวัสดี') !== false || strpos($_msg, 'ต้องการผู้ช่วย') !== false) {
      $replyToken = $event['replyToken'];
      $text = "สวัสดีค่ะ หากคุณต้องการนัดรักษาผู้ป่วยไทรอยด์เป็นพิษ ให้พิมพ์คำว่า 'ขอนัดกลืนแร่' ได้เลยค่ะ";
      $messages = [
        'type' => 'text',
        'text' => $text
      ];
    //     $messages = [
    //    'type' => 'template',
    //     'altText' => 'this is a confirm template',
    //     'template' => [
    //         'type' => 'confirm',
    //         'text' => $text ,
    //         'actions' => [
    //             [
    //                 'type' => 'message',
    //                 'label' => 'สนใจ',
    //                 'text' => 'สนใจ'
    //             ],
    //             [
    //                 'type' => 'message',
    //                 'label' => 'ไม่สนใจ',
    //                 'text' => 'ไม่สนใจ'
    //             ],
    //         ]
    //     ]
    // ];
$q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0001','','0002','0',NOW(),NOW())") or die(pg_errormessage());

##################################################################################################################################################
 
}elseif ($event['message']['text'] == "ขอนัดกลืนแร่" && $seqcode == "0001"  ) {
               $result = pg_query($dbconn,"SELECT seqcode,question FROM sequents WHERE seqcode = '0002'");
                while ($row = pg_fetch_row($result)) {
                  echo $seqcode =  $row[0];
                  echo $question = $row[1]; 
                }   

                $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' =>  $question
                      ];
 
                  $messages2 = [
                    'type'=> 'template',
                    'altText'=> 'This is a buttons template',
                    'template'=> [
                        'type'=> 'buttons',
                        // 'thumbnailImageUrl'=> 'https://example.com/bot/images/image.jpg',
                        // 'imageAspectRatio'=> 'rectangle',
                        // 'imageSize'=> 'cover',
                        // 'imageBackgroundColor'=> '#FFFFFF',
                        //'title'=> 'Menu',
                        'text'=> 'Please select',
                        'actions'=> [
                            [
                              'type'=> 'message',
                              'label'=> 'ใช่',
                              'text'=> '1'
                            ],
                            [
                              'type'=> 'message',
                              'label'=> 'ไม่ใช่',
                              'text'=> '2'
                            ],
                            [
                              'type'=> 'message',
                              'label'=> 'ไม่แน่ใจ',
                              'text'=> '3'
                            ]
                        ]
                    ]
                  ];


$q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0002','','0000','0',NOW(),NOW())") or die(pg_errormessage());

 // Make a POST Request to Messaging API to reply to sender
         $url = 'https://api.line.me/v2/bot/message/reply';
         // $url2 = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages,$messages2],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         // $ch2 = curl_init($url2);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n";




##################################################################################################################################################

}elseif ($event['message']['text'] == "1"  && $seqcode == "0002" || $event['message']['text'] == "2"  && $seqcode == "0007"   ) {
               $result = pg_query($dbconn,"SELECT seqcode,question FROM sequents WHERE seqcode = '0006'");
                while ($row = pg_fetch_row($result)) {
                  echo $seqcode =  $row[0];
                  echo $question = $row[1]; 
                }   

                $replyToken = $event['replyToken'];
                  $messages = [
                        'type' => 'text',
                        'text' =>  $question
                      ];
                  $messages2 = [
                        'type'=> 'image',
                        'originalContentUrl'=> 'https://chatbothospital.herokuapp.com/images/1.jpg',
                        'previewImageUrl'=> 'https://chatbothospital.herokuapp.com/images/1.jpg'
                      ];

$q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0006','','0000','0',NOW(),NOW())") or die(pg_errormessage());

 // Make a POST Request to Messaging API to reply to sender
         $url = 'https://api.line.me/v2/bot/message/reply';
         // $url2 = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages,$messages2],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         // $ch2 = curl_init($url2);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n";

##################################################################################################################################################

}elseif ($event['message']['text'] == "2" && $seqcode == "0002"  ) {
               $result = pg_query($dbconn,"SELECT seqcode,question FROM sequents WHERE seqcode = '0003'");
                while ($row = pg_fetch_row($result)) {
                  echo $seqcode =  $row[0];
                  echo $question = $row[1]; 
                }   

                $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' =>  $question
                      ];

$q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0003','','0000','0',NOW(),NOW())") or die(pg_errormessage());

##################################################################################################################################################
}elseif ($event['message']['text'] == "3" && $seqcode == "0002" || $seqcode == "0007"  ) {
               $result = pg_query($dbconn,"SELECT seqcode,question FROM sequents WHERE seqcode = '0005'");
                while ($row = pg_fetch_row($result)) {
                  echo $seqcode =  $row[0];
                  echo $question = $row[1]; 
                }   

                $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' =>  $question
                      ];

$q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0000','','0000','0',NOW(),NOW())") or die(pg_errormessage());

##################################################################################################################################################
/*รับรูปมาจากข้อ1(0009)*/
}elseif ($event['message']['text'] == "test" && $seqcode == "0006"  ) {

               $result = pg_query($dbconn,"SELECT seqcode,question FROM sequents WHERE seqcode = '0007'");
                while ($row = pg_fetch_row($result)) {
                  echo $seqcode =  $row[0];
                  echo $question = $row[1]; 
                }  
                $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' =>  $question
                      ];
 
                  $messages2 = [
                    'type'=> 'template',
                    'altText'=> 'This is a buttons template',
                    'template'=> [
                        'type'=> 'buttons',
                        // 'thumbnailImageUrl'=> 'https://example.com/bot/images/image.jpg',
                        // 'imageAspectRatio'=> 'rectangle',
                        // 'imageSize'=> 'cover',
                        // 'imageBackgroundColor'=> '#FFFFFF',
                        //'title'=> 'Menu',
                        'text'=> 'Please select',
                        'actions'=> [
                            [
                              'type'=> 'message',
                              'label'=> 'ใช่',
                              'text'=> '1'
                            ],
                            [
                              'type'=> 'message',
                              'label'=> 'ไม่ใช่',
                              'text'=> '2'
                            ],
                            [
                              'type'=> 'message',
                              'label'=> 'มีเอกสารไม่ครบ',
                              'text'=> '3'
                            ]
                        ]
                    ]
                  ];


$q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0007','','0000','0',NOW(),NOW())") or die(pg_errormessage());

 // Make a POST Request to Messaging API to reply to sender
         $url = 'https://api.line.me/v2/bot/message/reply';
         // $url2 = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages,$messages2],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         // $ch2 = curl_init($url2);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n";


##################################################################################################################################################
}elseif ($event['message']['text'] == "1" && $seqcode == "0007"  ) {
               $result = pg_query($dbconn,"SELECT seqcode,question FROM sequents WHERE seqcode = '0008'");
                while ($row = pg_fetch_row($result)) {
                  echo $seqcode =  $row[0];
                  echo $question = $row[1]; 
                } 
                $replyToken = $event['replyToken'];
                  // $messages = [
                  //       'type' => 'text',
                  //       'text' =>  $question
                  //     ];

                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' =>$question ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => '1',
                                  'text' => 'ใช่'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => '2',
                                  'text' => 'ไม่ใช่'
                              ],
                          ]
                      ]
                  ]; 
$q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0008','','0000','0',NOW(),NOW())") or die(pg_errormessage());
                   
########################################################################################################################################################
// }elseif ($event['message']['text'] == "2" && $seqcode == "0007"  ) {
//                $result = pg_query($dbconn,"SELECT seqcode,question FROM sequents WHERE seqcode = '0003'");
//                 while ($row = pg_fetch_row($result)) {
//                   echo $seqcode =  $row[0];
//                   echo $question = $row[1]; 
//                 }   

//                 $replyToken = $event['replyToken'];
//                  $messages = [
//                         'type' => 'text',
//                         'text' =>  $question
//                       ];

// $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0003','','0000','0',NOW(),NOW())") or die(pg_errormessage());

##################################################################################################################################################














}elseif ($event['message']['text'] == "1" && $seqcode == "0008"  ) {
               $result = pg_query($dbconn,"SELECT seqcode,question FROM sequents WHERE seqcode = '0009'");
                while ($row = pg_fetch_row($result)) {
                  echo $seqcode =  $row[0];
                  echo $question = $row[1]; 
                } 
                $replyToken = $event['replyToken'];
                  $messages = [
                        'type' => 'text',
                        'text' =>  $question
                      ];

                  $messages2 = [
                        'type'=> 'image',
                        'originalContentUrl'=> 'https://chatbothospital.herokuapp.com/images/2.1.jpg',
                        'previewImageUrl'=> 'https://chatbothospital.herokuapp.com/images/2.1.jpg'
                      ];
                  $messages3 = [
                        'type'=> 'image',
                        'originalContentUrl'=> 'https://chatbothospital.herokuapp.com/images/2.2.jpg',
                        'previewImageUrl'=> 'https://chatbothospital.herokuapp.com/images/2.2.jpg'
                      ];

$q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0009','','0000','0',NOW(),NOW())") or die(pg_errormessage());

 // Make a POST Request to Messaging API to reply to sender
         $url = 'https://api.line.me/v2/bot/message/reply';
         // $url2 = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages,$messages2,$messages3],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         // $ch2 = curl_init($url2);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n";
########################################################################################################################################################
































  }else {
    // if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
      $replyToken = $event['replyToken'];
      $text = "ดิฉันไม่เข้าใจค่ะ กรุณาพิมพ์ใหม่อีกครั้งนะคะ";
      $messages = [
          'type' => 'text',
          'text' => $text
        ]; 
    // }
/*หากคุณสนใจให้ดิฉันเป็นผู้ช่วยอัตโนมัติของคุณ กรุณาพิมพ์ว่า "สนใจ" ได้เลยนะคะ*/
 //   $replyToken = $event['replyToken'];
 //      $text = "หากคุณสนใจให้ดิฉันเป็นผู้ช่วยอัตโนมัติของคุณ โปรดกดยืนยันด้านล่างด้วยนะคะ";
 //          $messages = [
 //                 'type' => 'template',
 //                  'altText' => 'this is a confirm template',
 //                  'template' => [
 //                      'type' => 'confirm',
 //                      'text' => $text ,
 //                      'actions' => [
 //                          [
 //                              'type' => 'message',
 //                              'label' => 'สนใจ',
 //                              'text' => 'สนใจ'
 //                          ],
 //                          [
 //                              'type' => 'message',
 //                              'label' => 'ไม่สนใจ',
 //                              'text' => 'ไม่สนใจ'
 //                          ]
 //                      ]
 //                  ]
 //              ]; 
 // $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0004','','0005','0',NOW(),NOW())") or die(pg_errormessage());
       
  }
  
  
 }
}
  // Make a POST Request to Messaging API to reply to sender
         $url = 'https://api.line.me/v2/bot/message/reply';
         // $url2 = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         // $ch2 = curl_init($url2);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n";

?>
