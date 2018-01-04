<?php
################################## DATABASE ##################################
$conn_string = "host=ec2-54-227-247-225.compute-1.amazonaws.com port=5432 dbname=d7i9sj05534uua user=twyavrmgujwujj password=78cac0794ff9469d19800c70521b078dd1e2505ebf978e4239f7f7393d3916a8";
$dbconn = pg_pconnect($conn_string);
if (!$dbconn) {
    die("Connection failed: " . mysqli_connect_error());
}
##############################################################################

$access_token = '2MD4waeOjAyBl8vV/r60JwXzouAjGDB5n6QFwLrfRVLXSlf0nfkIA861nwGiGYCTMo9zKsd0fAFqoUb+8E0j1FL6tioXbgrXusCgfacGVtxwbtD4n0GhuRSl0rfkt0VCVsgremd9z3nXhEMdGn5ZJgdB04t89/1O/w1cDnyilFU=';


$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
$curr_years = date("Y");
$curr_y = ($curr_years+ 543);
$_msg = $events['events'][0]['message']['text'];
$user = $events['events'][0]['source']['userId'];
$user_id = pg_escape_string($user);
$u = pg_escape_string($_msg);  
$check_q = pg_query($dbconn,"SELECT seqcode, sender_id ,updated_at  FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
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
      $text = "สวัสดีค่ะ คุณสนใจมีผู้ช่วยใช่ไหม";
      // $messages = [
      //   'type' => 'text',
      //   'text' => $text
      // ];
        $messages = [
       'type' => 'template',
        'altText' => 'this is a confirm template',
        'template' => [
            'type' => 'confirm',
            'text' => $text ,
            'actions' => [
                [
                    'type' => 'message',
                    'label' => 'สนใจ',
                    'text' => 'สนใจ'
                ],
                [
                    'type' => 'message',
                    'label' => 'ไม่สนใจ',
                    'text' => 'ไม่สนใจ'
                ],
            ]
        ]
    ];
####################################  insert data to sequentsteps   ####################################
 $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0004','','0005','0',NOW(),NOW())") or die(pg_errormessage());
                   
########################################################################################################### 
}elseif($event['message']['text'] == "Clear" ){
      $replyToken = $event['replyToken'];
      $text = "cleared!";
      $messages = [
          'type' => 'text',
          'text' => $text
        ]; 
    $sql =pg_exec($dbconn,"DELETE FROM users_register WHERE user_id = '{$user_id}' ");
    $sql1 =pg_exec($dbconn,"DELETE FROM recordofpregnancy WHERE user_id = '{$user_id}' ");
    $sql2 =pg_exec($dbconn,"DELETE FROM sequentsteps WHERE sender_id = '{$user_id}' ");
    $sql3 =pg_exec($dbconn,"DELETE FROM tracker WHERE user_id = '{$user_id}' ");
    $sql4 =pg_exec($dbconn,"DELETE FROM auto_reply WHERE sender_id = '{$user_id}' ");
#################################### ผู้ใช้เลือกสนใจ #################################### 
  }elseif ($event['message']['text'] == "สนใจ" && $seqcode == "0004"  ) {
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
                $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0005','','0006','0',NOW(),NOW())") or die(pg_errormessage());
#################################### ผู้ใช้เลือกไม่สนใจ ####################################    
  }elseif ($event['message']['text'] == "ไม่สนใจ" ) {
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ไว้โอกาสหน้าให้เราได้เป็นผู้ช่วยของคุณนะคะ:) หากคุณสนใจในภายหลังให้พิมพ์ว่า"ต้องการผู้ช่วย"'
                      ];          
########################################################################################################### 
  }elseif ($event['message']['text'] == "ไม่ถูกต้อง" ) {
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'กรุณาพิมพ์ใหม่นะคะ'
                      ];  
###########################################################################################################                    
}elseif ($event['message']['text'] == "ชื่อถูกต้อง"  ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  // $pieces = explode("", $answer);
                  // $name =str_replace("","",$pieces[0]);
                  // $surname =str_replace("","",$pieces[1]);
                 $u = pg_escape_string($answer);
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอทราบอายุของคุณหน่อยค่ะ '
                      ];
$q = pg_exec($dbconn, "INSERT INTO users_register(user_id,user_name,status,updated_at )VALUES('{$user_id}','{$u}','1',NOW())") or die(pg_errormessage());
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0007','','0008','0',NOW(),NOW())") or die(pg_errormessage());
###########################################################################################################
  }elseif (strpos($_msg) !== false && $seqcode == "0005" ) {
    
  $u = pg_escape_string($_msg);
    $ans = 'ชื่อของคุณคือ'.$_msg.'ใช่ไหมคะ?' ;
    $replyToken = $event['replyToken'];
    $messages = [
        'type' => 'template',
        'altText' => 'this is a confirm template',
        'template' => [
            'type' => 'confirm',
            'text' => $ans ,
            'actions' => [
                [
                    'type' => 'message',
                    'label' => 'ใช่',
                    'text' => 'ชื่อถูกต้อง'
                ],
                [
                    'type' => 'message',
                    'label' => 'ไม่ใช่',
                    'text' => 'ไม่ถูกต้อง'
                ],
            ]
        ]
    ];     
      $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0005','{$u}','0007','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################
 
}elseif (is_numeric($_msg) !== false && $seqcode == "0007"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  $u = pg_escape_string($_msg);
                  $ans = 'คุณอายุ '.$_msg.'ปี ใช่ไหมคะ' ;
                  $replyToken = $event['replyToken'];
                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' => $ans ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'ใช่',
                                  'text' => 'อายุถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0007',$_msg,'0009','0',NOW(),NOW())") or die(pg_errormessage());
                    
########################################################################################################################################################
 }elseif ($event['message']['text'] == "อายุถูกต้อง" ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  // $pieces = explode("", $answer);
                  // $name =str_replace("","",$pieces[0]);
                  // $surname =str_replace("","",$pieces[1]);
                 $u = pg_escape_string($answer);
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอทราบส่วนสูงปัจจุบันของคุณค่ะ (กรุณาตอบเป็นตัวเลขในหน่วยเซ็นติเมตร เช่น 160)'
                      ];
 $q = pg_exec($dbconn, "UPDATE users_register SET user_age = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0009','','0010','0',NOW(),NOW())") or die(pg_errormessage());
 // }elseif ($event['message']['text'] == "ไม่ถูกต้อง" ) {
 //                 $replyToken = $event['replyToken'];
 //                 $messages = [
 //                        'type' => 'text',
 //                        'text' => 'กรุณาพิมพ์ใหม่ค่ะ'
 //                      ];  
########################################################################################################################################################
}elseif (is_numeric($_msg) !== false && $seqcode == "0009"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  $u = pg_escape_string($_msg);
                  $ans = 'ส่วนสูงปัจจุบันของคุณคือ'.$_msg.'เซ็นติเมตร ใช่ไหมคะ' ;
                  $replyToken = $event['replyToken'];
                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' => $ans ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'ใช่',
                                  'text' => 'ส่วนสูงถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0009',$_msg,'0011','0',NOW(),NOW())") or die(pg_errormessage());
                    
########################################################################################################################################################
 }elseif ($event['message']['text'] == "ส่วนสูงถูกต้อง"  ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  // $pieces = explode("", $answer);
                  // $name =str_replace("","",$pieces[0]);
                  // $surname =str_replace("","",$pieces[1]);
                 $u = pg_escape_string($answer);
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอทราบน้ำหนักปกติก่อนตั้งครรภ์ค่ะ (กรุณาตอบเป็นตัวเลขในหน่วยกิโลกรัม เช่น 55)'
                      ];
 $q = pg_exec($dbconn, "UPDATE users_register SET user_height = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0011','','0012','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################
}elseif (is_numeric($_msg) !== false && $seqcode == "0011"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  $u = pg_escape_string($_msg);
                  $ans = 'ก่อนตั้งครรภ์คุณมีน้ำหนัก'.$_msg.'กิโลกรัมใช่ไหมคะ' ;
                  $replyToken = $event['replyToken'];
                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' => $ans ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'ใช่',
                                  'text' => 'น้ำหนักก่อนตั้งครรภ์ถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0011',$_msg,'0013','0',NOW(),NOW())") or die(pg_errormessage());
                    
########################################################################################################################################################
 }elseif ($event['message']['text'] == "น้ำหนักก่อนตั้งครรภ์ถูกต้อง"  ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; /*ก่อนอื่น ดิฉันขออนุญาตถามข้อมูลเบื้องต้นเกี่ยวกับคุณก่อนนะคะ
ขอทราบปีพ.ศ.เกิดเพื่อคำนวณอายุค่ะ*/
                }   
                  // $pieces = explode("", $answer);
                  // $name =str_replace("","",$pieces[0]);
                  // $surname =str_replace("","",$pieces[1]);
                 $u = pg_escape_string($answer);
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอทราบน้ำหนักปัจจุบันของคุณค่ะ (กรุณาตอบเป็นตัวเลขในหน่วยกิโลกรัม เช่น 59)'
                      ];
 $q = pg_exec($dbconn, "UPDATE users_register SET user_pre_weight = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0013','','0014','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################
}elseif (is_numeric($_msg) !== false && $seqcode == "0013"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  $u = pg_escape_string($_msg);
                  $ans = 'น้ำหนักปัจจุบันของคุณคือ'.$_msg.'กิโลกรัมใช่ไหมคะ' ;
                  $replyToken = $event['replyToken'];
                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' => $ans ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'ใช่',
                                  'text' => 'น้ำหนักปัจจุบันถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0013',$_msg,'0015','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################                   
 }elseif ($event['message']['text'] == "น้ำหนักปัจจุบันถูกต้อง"  ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; /*ก่อนอื่น ดิฉันขออนุญาตถามข้อมูลเบื้องต้นเกี่ยวกับคุณก่อนนะคะ
ขอทราบปีพ.ศ.เกิดเพื่อคำนวณอายุค่ะ*/
                }   
                  // $pieces = explode("", $answer);
                  // $name =str_replace("","",$pieces[0]);
                  // $surname =str_replace("","",$pieces[1]);
                 $u = pg_escape_string($answer);
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];
                $messages = [
                  'type'=> 'template',
                  'altText'=> 'this is a buttons template',
                  'template'=> [
                      'type'=> 'buttons',
                      //'thumbnailImageUrl'=> 'https://example.com/bot/images/image.jpg',
                      'title'=> "คุณมีอายุครรภ์กี่สัปดาห์คะ?",
                      'text'=> "กรุณาเลือกตอบข้อใดข้อหนึ่งเพื่อให้ทางเราคำนวณอายุครรภ์ค่ะ",
                      'actions'=> [
                          [
                            'type'=> 'message',
                            'label'=> 'ครั้งสุดท้ายที่มีประจำเดือน',
                            'text'=> 'ครั้งสุดท้ายที่มีประจำเดือน'
                          ],
                          [
                            'type'=> 'message',
                            'label'=> 'กำหนดการคลอด',
                            'text'=> 'กำหนดการคลอด'
                          ]
                      ]
                  ]
                ];
// $q = pg_exec($dbconn, "UPDATE users_register SET user_weight = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
//$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0015','','0016','0',NOW(),NOW())") or die(pg_errormessage());
 // $q2 = pg_exec($dbconn, "INSERT INTO recordofpregnancy(user_id, preg_week, preg_weight,updated_at )VALUES('{$user_id}',$p_week,$answer ,  NOW()) ") or die(pg_errormessage());  
########################################################################################################################################################
 }elseif ($event['message']['text'] == "ครั้งสุดท้ายที่มีประจำเดือน" /*&& $seqcode == "0015" */) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1 ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
   
                 //$u = pg_escape_string($answer);
$q = pg_exec($dbconn, "UPDATE users_register SET user_weight = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage());                
   
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอทราบครั้งสุดท้ายที่คุณมีประจำเดือนเพื่อคำนวณอายุครรภ์ค่ะ (กรุณาตอบวันที่เว้นวรรคด้วยเดือนเป็นตัวเลขนะคะ เช่น 17 04 คือ วันที่ 17 เมษายน)'
                      ];
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','1015','','0016','0',NOW(),NOW())") or die(pg_errormessage());
 
########################################################################################################################################################
}elseif ($event['message']['text'] == "กำหนดการคลอด"/* && $seqcode == "0015"*/) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                 $u = pg_escape_string($answer);
 $q = pg_exec($dbconn, "UPDATE users_register SET user_weight = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอทราบกำหนดการคลอดของคุณหน่อยค่ะ (กรุณาตอบวันที่เว้นวรรคด้วยเดือนเป็นตัวเลขนะคะ เช่น 17 04 คือ วันที่ 17 เมษายน)'
                      ];
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2015','','0016','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################
  }elseif (/*strlen($_msg) == 5*/strpos($_msg) !== false && $seqcode == "1015") {
    // $birth_years =  str_replace("วันที่","", $_msg);
    if (strpos($_msg,' ')!== false) {
          $pieces = explode(" ", $_msg);
          $date = str_replace("","",$pieces[0]);
          $month  = str_replace("","",$pieces[1]);
    }elseif (strpos($_msg,'-')!== false) {
          $pieces = explode("-", $_msg);
          $date   = str_replace("","",$pieces[0]);
          $month  = str_replace("","",$pieces[1]);
    }elseif (strpos($_msg,'/')!== false) {
          $pieces = explode("/", $_msg);
          $date   = str_replace("","",$pieces[0]);
          $month  = str_replace("","",$pieces[1]);
    }elseif (strpos($_msg,':')!== false) {
          $pieces = explode(":", $_msg);
          $date   = str_replace("","",$pieces[0]);
          $month  = str_replace("","",$pieces[1]);
    }
     else {
      $n = "ดูเหมือนคุณจะพิมพ์ไม่ถูกต้อง กรุณาพิมพ์ใหม่นะคะ";
    }
    
    // $pieces = explode(" ", $_msg);
    // $date = str_replace("","",$pieces[0]);
    // $month  = str_replace("","",$pieces[1]);
   
            $today_years= date("Y") ;
            $today_month= date("m") ;
            $today_day  = date("d") ;
    if ($month == "มกราคม" || $month == "ม.ค." || $month == "มค" || $month == "มกรา") {
      $month = '01';
    }elseif ($month == "กุมภาพันธ์" || $month == "ก.พ." || $month == "กพ"|| $month == "กุมภา") {
      $month = '02';
    }elseif ($month == "มีนาคม" || $month == "มี.ค."|| $month == "มีค"|| $month == "มีนา") {
      $month = '03';
    }elseif ($month == "เมษายน" || $month == "เม.ย."|| $month == "เมย"|| $month == "เมษา") {
      $month = '04';
    }elseif ($month == "พฤษภาคม" || $month == "พ.ค."|| $month == "พค"|| $month == "พฤษภา") {
      $month = '05';
    }elseif ($month == "มิถุนายน" || $month == "มิ.ย."|| $month == "มิย"|| $month == "มิถุนา") {
      $month = '06';
    }elseif ($month == "กรกฎาคม" || $month == "ก.ค."|| $month == "กค"|| $month == "กรกฎา") {
      $month = '07';
    }elseif ($month == "สิงหาคม" || $month == "ส.ค."|| $month == "สค"|| $month == "สิงหา") {
      $month = '08';
    }elseif ($month == "กันยายน" || $month == "ก.ย."|| $month == "กย"|| $month == "กันยา") {
      $month = '09';
    }elseif ($month == "ตุลาคม"|| $month == "ต.ค."|| $month == "ตค"|| $month == "ตุลา") {
      $month = '10';
    }elseif ($month == "พฤศจิกายน" || $month == "พ.ย."|| $month == "พย"|| $month == "พฤศจิกา") {
      $month = '11';
    }elseif ($month == "ธันวาคม" || $month == "ธ.ค."|| $month == "ธค"|| $month == "ธันวา") {
      $month = '12';
    }else {
      # code...
    }
            if(($month>$today_month&& $month<=12 && $date<=31) || ($month==$today_month && $date>$today_day)  ){
                $years = $today_years-1;
                $strDate1 = $years."-".$month."-".$date;
                $strDate2=date("Y-m-d");
                
                $date_pre =  (strtotime($strDate2) - strtotime($strDate1))/( 60 * 60 * 24 );
                $week = $date_pre/7;
                $week_preg = number_format($week);
                $day = $date_pre%7;
                $day_preg = number_format($day);
                $age_pre = 'คุณมีอายุครรภ์'. $week_preg .'สัปดาห์'.  $day_preg .'วัน' ;
                      $replyToken = $event['replyToken'];
                      $messages = [
                          'type' => 'template',
                          'altText' => 'this is a confirm template',
                          'template' => [
                              'type' => 'confirm',
                              'text' =>  $age_pre.'ใช่ไหมคะ?' ,
                              'actions' => [
                                  [
                                      'type' => 'message',
                                      'label' => 'ใช่',
                                      'text' => 'อายุครรภ์ถูกต้อง'
                                  ],
                                  [
                                      'type' => 'message',
                                      'label' => 'ไม่ใช่',
                                      'text' => 'ไม่ถูกต้อง'
                                  ],
                              ]
                          ]
                      ];   
            
            }elseif($month<$today_month && $month<=12 && $date<=31){
                $strDate1 = $today_years."-".$month."-".$date;
                $strDate2=date("Y-m-d");
                $date_pre =  (strtotime($strDate2) - strtotime($strDate1))/( 60 * 60 * 24 );;
                $week = $date_pre/7;
                $week_preg = number_format($week);
                $day = $date_pre%7;
                $day_preg = number_format($day);
                $age_pre = 'คุณมีอายุครรภ์'. $week_preg .'สัปดาห์'.  $day_preg .'วัน' ;
                    $replyToken = $event['replyToken'];
                    $messages = [
                        'type' => 'template',
                        'altText' => 'this is a confirm template',
                        'template' => [
                            'type' => 'confirm',
                            'text' =>  $age_pre.'ใช่ไหมคะ?' ,
                            'actions' => [
                                [
                                    'type' => 'message',
                                    'label' => 'ใช่',
                                    'text' => 'อายุครรภ์ถูกต้อง'
                                ],
                                [
                                    'type' => 'message',
                                    'label' => 'ไม่ใช่',
                                    'text' => 'ไม่ถูกต้อง'
                                ],
                            ]
                        ]
                    ];   
            }else{
               $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ดูเหมือนคุณจะพิมพ์ไม่ถูกต้อง'
                      ];
            }
  
      $url = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n";
    
  $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','1015', $week_preg ,'0017','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################
 }elseif (/*strlen($_msg) == 5*/strpos($_msg) !== false && $seqcode == "2015") {
                
                 // $pieces = explode(" ", $_msg);
                 // $date   = str_replace("","",$pieces[0]);
                 // $month  = str_replace("","",$pieces[1]);
                 // $today_years= date("Y") ;
                 // $today_month= date("m") ;
                 // $today_day  = date("d") ;
    if (strpos($_msg,' ')!== false) {
          $pieces = explode(" ", $_msg);
          $date = str_replace("","",$pieces[0]);
          $month  = str_replace("","",$pieces[1]);
    }elseif (strpos($_msg,'-')!== false) {
          $pieces = explode("-", $_msg);
          $date   = str_replace("","",$pieces[0]);
          $month  = str_replace("","",$pieces[1]);
    }elseif (strpos($_msg,'/')!== false) {
          $pieces = explode("/", $_msg);
          $date   = str_replace("","",$pieces[0]);
          $month  = str_replace("","",$pieces[1]);
    }elseif (strpos($_msg,':')!== false) {
          $pieces = explode(":", $_msg);
          $date   = str_replace("","",$pieces[0]);
          $month  = str_replace("","",$pieces[1]);
    }
     else {
      $n = "ดูเหมือนคุณจะพิมพ์ไม่ถูกต้อง กรุณาพิมพ์ใหม่นะคะ";
    }
    
    // $pieces = explode(" ", $_msg);
    // $date = str_replace("","",$pieces[0]);
    // $month  = str_replace("","",$pieces[1]);
   
            $today_years= date("Y") ;
            $today_month= date("m") ;
            $today_day  = date("d") ;
    if ($month == "มกราคม" || $month == "ม.ค." || $month == "มค" || $month == "มกรา") {
      $month = '01';
    }elseif ($month == "กุมภาพันธ์" || $month == "ก.พ." || $month == "กพ"|| $month == "กุมภา") {
      $month = '02';
    }elseif ($month == "มีนาคม" || $month == "มี.ค."|| $month == "มีค"|| $month == "มีนา") {
      $month = '03';
    }elseif ($month == "เมษายน" || $month == "เม.ย."|| $month == "เมย"|| $month == "เมษา") {
      $month = '04';
    }elseif ($month == "พฤษภาคม" || $month == "พ.ค."|| $month == "พค"|| $month == "พฤษภา") {
      $month = '05';
    }elseif ($month == "มิถุนายน" || $month == "มิ.ย."|| $month == "มิย"|| $month == "มิถุนา") {
      $month = '06';
    }elseif ($month == "กรกฎาคม" || $month == "ก.ค."|| $month == "กค"|| $month == "กรกฎา") {
      $month = '07';
    }elseif ($month == "สิงหาคม" || $month == "ส.ค."|| $month == "สค"|| $month == "สิงหา") {
      $month = '08';
    }elseif ($month == "กันยายน" || $month == "ก.ย."|| $month == "กย"|| $month == "กันยา") {
      $month = '09';
    }elseif ($month == "ตุลาคม"|| $month == "ต.ค."|| $month == "ตค"|| $month == "ตุลา") {
      $month = '10';
    }elseif ($month == "พฤศจิกายน" || $month == "พ.ย."|| $month == "พย"|| $month == "พฤศจิกา") {
      $month = '11';
    }elseif ($month == "ธันวาคม" || $month == "ธ.ค."|| $month == "ธค"|| $month == "ธันวา") {
      $month = '12';
    }else {
      # code...
    }
                 if( $month < $today_month && $month<=12 && $date<=31){
                 $years = $today_years+1;
                 $strDate1 = $years."-".$month."-".$date;
                 $strDate2=date("Y-m-d");
                
                 $date_pre =  (strtotime($strDate1) - strtotime($strDate2))/( 60 * 60 * 24 );
                 $week = $date_pre/7;
                 $week_preg =floor($week);
                 $day = $date_pre%7;
                 $day_preg = number_format($day);
                 $m = 39-$week_preg  ;
                 $d = 7-$day_preg;
             
                 switch ($d){
                 case '7':
                  $w_preg = $m + 1;
                $age_pre = 'คุณมีอายุครรภ์'. $w_preg .'สัปดาห์' ;
                $replyToken = $event['replyToken'];
                    
                    $messages = [
                        'type' => 'template',
                        'altText' => 'this is a confirm template',
                        'template' => [
                            'type' => 'confirm',
                            'text' =>  $age_pre.'ใช่ไหมคะ?' ,
                            'actions' => [
                                [
                                    'type' => 'message',
                                    'label' => 'ใช่',
                                    'text' => 'อายุครรภ์ถูกต้อง'
                                ],
                                [
                                    'type' => 'message',
                                    'label' => 'ไม่ใช่',
                                    'text' => 'ไม่ถูกต้อง'
                                ],
                            ]
                        ]
                    ];   
$q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2015', $w_preg ,'0018','0',NOW(),NOW())") or die(pg_errormessage());
                break;
                 default:
                          $replyToken = $event['replyToken'];
                           $age_pre = 'คุณมีอายุครรภ์'. $m .'สัปดาห์'.  $d .'วัน' ;
                             
                                  $messages = [
                                      'type' => 'template',
                                      'altText' => 'this is a confirm template',
                                      'template' => [
                                          'type' => 'confirm',
                                          'text' =>  $age_pre.'ใช่ไหมคะ?' ,
                                          'actions' => [
                                              [
                                                  'type' => 'message',
                                                  'label' => 'ใช่',
                                                  'text' => 'อายุครรภ์ถูกต้อง'
                                              ],
                                              [
                                                  'type' => 'message',
                                                  'label' => 'ไม่ใช่',
                                                  'text' => 'ไม่ถูกต้อง'
                                              ],
                                          ]
                                      ]
                                  ];   
$q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2015', $m ,'0018','0',NOW(),NOW())") or die(pg_errormessage());
              
              break;
                  }
        }elseif($month > $today_month && $month<=12 && $date<=31){
                 $years = $today_years;
                 $strDate1 = $years."-".$month."-".$date;
                 $strDate2=date("Y-m-d");
                
                 $date_pre =  (strtotime($strDate1) - strtotime($strDate2))/( 60 * 60 * 24 );
                 $week = $date_pre/7;
                 $week_preg =floor($week);
                 $day = $date_pre%7;
                 $day_preg = number_format($day);
                 $m = 39-$week_preg  ;
                 $d = 7-$day_preg;
              
                  switch ($d){
                 case '7':
                  $w_preg = $m + 1;
                $age_pre = 'คุณมีอายุครรภ์'. $w_preg .'สัปดาห์' ;
                $replyToken = $event['replyToken'];
                    
                    $messages = [
                        'type' => 'template',
                        'altText' => 'this is a confirm template',
                        'template' => [
                            'type' => 'confirm',
                            'text' =>  $age_pre.'ใช่ไหมคะ?' ,
                            'actions' => [
                                [
                                    'type' => 'message',
                                    'label' => 'ใช่',
                                    'text' => 'อายุครรภ์ถูกต้อง'
                                ],
                                [
                                    'type' => 'message',
                                    'label' => 'ไม่ใช่',
                                    'text' => 'ไม่ถูกต้อง'
                                ],
                            ]
                        ]
                    ];   
$q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2015', $w_preg ,'0018','0',NOW(),NOW())") or die(pg_errormessage());
              break;
                 default:
                          $replyToken = $event['replyToken'];
                           $age_pre = 'คุณมีอายุครรภ์'. $m .'สัปดาห์'.  $d .'วัน' ;
                             
                                  $messages = [
                                      'type' => 'template',
                                      'altText' => 'this is a confirm template',
                                      'template' => [
                                          'type' => 'confirm',
                                          'text' =>  $age_pre.'ใช่ไหมคะ?' ,
                                          'actions' => [
                                              [
                                                  'type' => 'message',
                                                  'label' => 'ใช่',
                                                  'text' => 'อายุครรภ์ถูกต้อง'
                                              ],
                                              [
                                                  'type' => 'message',
                                                  'label' => 'ไม่ใช่',
                                                  'text' => 'ไม่ถูกต้อง'
                                              ],
                                          ]
                                      ]
                                  ];  
$q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2015', $m ,'0018','0',NOW(),NOW())") or die(pg_errormessage());
                  break;
                  }
                 }
      $url = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n";
    // $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2015', $week_preg ,'0017','0',NOW(),NOW())") or die(pg_errormessage());
###############################################################################################################################
 }elseif ($event['message']['text'] == "อายุครรภ์ถูกต้อง"  ) {
    $check_q = pg_query($dbconn,"SELECT seqcode, sender_id ,updated_at ,answer FROM sequentsteps  WHERE sender_id = '{$user_id}' order by updated_at desc limit 1  ");
                while ($row = pg_fetch_row($check_q)) {
            
                  echo $answer = $row[3];  
                } 
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอทราบเบอร์โทรศัพท์ของคุณหน่อยค่ะ'
                      ];
   
 $q = pg_exec($dbconn, "UPDATE users_register SET preg_week = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0017','','0018','0',NOW(),NOW())") or die(pg_errormessage());
 $check_q = pg_query($dbconn,"SELECT user_weight FROM users_register  WHERE user_id  = '{$user_id}' order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q)) {
            
                  echo $preg_weight = $row[0];  
                } 
 $q2 = pg_exec($dbconn, "INSERT INTO recordofpregnancy(user_id, preg_weight, preg_week,updated_at )VALUES('{$user_id}', $preg_weight , $answer ,  NOW()) ") or die(pg_errormessage());  
###########################################################################################################
}elseif ((/*is_numeric($_msg)*/strlen($_msg) == 10 || /*is_numeric($_msg)*/strlen($_msg) == 9) && $seqcode == "0017"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1 ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  $u = pg_escape_string($_msg);
                  $ans = 'เบอร์โทรศัพท์ของคุณคือ'.$_msg.'ใช่ไหมคะ' ;
                  $replyToken = $event['replyToken'];
                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' => $ans ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'ใช่',
                                  'text' => 'เบอร์โทรศัพท์ถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0017',$_msg,'0019','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################                    
 }elseif ($event['message']['text'] == "เบอร์โทรศัพท์ถูกต้อง"  ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; /*ก่อนอื่น ดิฉันขออนุญาตถามข้อมูลเบื้องต้นเกี่ยวกับคุณก่อนนะคะ
ขอทราบปีพ.ศ.เกิดเพื่อคำนวณอายุค่ะ*/
                }   
                  // $pieces = explode("", $answer);
                  // $name =str_replace("","",$pieces[0]);
                  // $surname =str_replace("","",$pieces[1]);
                 $u = pg_escape_string($answer);
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];
                 // $messages = [
                 //        'type' => 'text',
                 //        'text' => 'ขอทราบชื่อโรงพยาบาลที่คุณแม่ไปฝากครรภ์หน่อยค่ะ'
                 //      ];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอทราบ E-mail ของคุณหน่อยค่ะ'
                      ];
 $q = pg_exec($dbconn, "UPDATE users_register SET phone_number = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0019','','0020','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################
 }elseif ($event['message']['text'] == "E-mailถูกต้อง" ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  // $pieces = explode("", $answer);
                  // $name =str_replace("","",$pieces[0]);
                  // $surname =str_replace("","",$pieces[1]);
                 $u = pg_escape_string($answer);
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอทราบชื่อโรงพยาบาลที่คุณแม่ไปฝากครรภ์หน่อยค่ะ'
                      ];
$q = pg_exec($dbconn, "UPDATE users_register SET email = '{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
// $q = pg_exec($dbconn, "INSERT INTO users_register(user_id,hospital_name,status,updated_at )VALUES('{$user_id}','{$u}','1',NOW())") or die(pg_errormessage());
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0021','','0022','0',NOW(),NOW())") or die(pg_errormessage());
#########################################################################################################################################################
 }elseif ($event['message']['text'] == "ชื่อโรงพยาบาลที่คุณแม่ไปฝากครรภ์ถูกต้อง" ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; /*ก่อนอื่น ดิฉันขออนุญาตถามข้อมูลเบื้องต้นเกี่ยวกับคุณก่อนนะคะ
ขอทราบปีพ.ศ.เกิดเพื่อคำนวณอายุค่ะ*/
                }   
                  // $pieces = explode("", $answer);
                  // $name =str_replace("","",$pieces[0]);
                  // $surname =str_replace("","",$pieces[1]);
                 $u = pg_escape_string($answer);
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอทราบเลขประจำตัวผู้ป่วยของคุณหน่อยค่ะ'
                      ];
$q = pg_exec($dbconn, "UPDATE users_register SET hospital_name = '{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
// $q = pg_exec($dbconn, "INSERT INTO users_register(user_id,hospital_name,status,updated_at )VALUES('{$user_id}','{$u}','1',NOW())") or die(pg_errormessage());
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0023','','0024','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################   
}elseif ((strpos($_msg, '@') !== false && strpos($_msg, '.') !== false) && $seqcode == "0019"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  $u = pg_escape_string($_msg);
                  $ans = 'E-mailของคุณคือ'.$_msg.'ใช่ไหมคะ' ;
                  $replyToken = $event['replyToken'];
                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' => $ans ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'ใช่',
                                  'text' => 'E-mailถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0019','{$u}','0021','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################    
}elseif (strpos($_msg) !== false && $seqcode == "0021"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  $u = pg_escape_string($_msg);
                  $ans = 'ชื่อโรงพยาบาลที่คุณแม่ไปฝากครรภ์คือ'.$_msg.'ใช่ไหมคะ' ;
                  $replyToken = $event['replyToken'];
                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' => $ans ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'ใช่',
                                  'text' => 'ชื่อโรงพยาบาลที่คุณแม่ไปฝากครรภ์ถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0021','{$u}','0023','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################
}elseif (is_numeric($_msg) !== false && $seqcode == "0023"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  $u = pg_escape_string($_msg);
                  $ans = 'เลขประจำตัวผู้ป่วยของคุณคือ'.$_msg.'ใช่ไหมคะ' ;
                  $replyToken = $event['replyToken'];
                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' => $ans ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'ใช่',
                                  'text' => 'เลขประจำตัวผู้ป่วยของถูกต้อง'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ใช่',
                                  'text' => 'ไม่ถูกต้อง'
                              ],
                          ]
                      ]
                  ];     
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0023',$_msg,'0025','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################
 }elseif ($event['message']['text'] == "เลขประจำตัวผู้ป่วยของถูกต้อง") {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0];
                }   
                
                  // $pieces = explode("", $answer);
                  // $name =str_replace("","",$pieces[0]);
                  // $surname =str_replace("","",$pieces[1]);
                 $u = pg_escape_string($answer);
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];
                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' =>'คุณมีประวัติการแพ้ยาไหมคะ?' ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'มี',
                                  'text' => 'แพ้ยา'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่มี',
                                  'text' => 'ไม่แพ้ยา'
                              ],
                          ]
                      ]
                  ];        
$q = pg_exec($dbconn, "UPDATE users_register SET hospital_number = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
//$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0025','','0026','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################
 }elseif ($event['message']['text'] == "แนะนำอาหาร" ) {
               
                $replyToken = $event['replyToken'];
                
             
                                  $messages = [
                                        'type'=> 'image',
                                        'originalContentUrl'=> 'https://backup-bot.herokuapp.com/Manual/eat1.jpg',
                                        'previewImageUrl'=> 'https://backup-bot.herokuapp.com/Manual/eat1.jpg'
                                    ];
                                  $messages2 = [
                                        'type'=> 'image',
                                        'originalContentUrl'=> 'https://backup-bot.herokuapp.com/Manual/eat2.jpg',
                                        'previewImageUrl'=> 'https://backup-bot.herokuapp.com/Manual/eat2.jpg'
                                    ];
                                  $messages3 = [
                                        'type'=> 'image',
                                        'originalContentUrl'=> 'https://backup-bot.herokuapp.com/Manual/eat3.jpg',
                                        'previewImageUrl'=> 'https://backup-bot.herokuapp.com/Manual/eat3.jpg'
                                    ];
  
          $url = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages,$messages2,$messages3],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n";   
########################################################################################################################################################
 }elseif ($event['message']['text'] == "แนะนำการออกกำลังกาย" ) {
               
                $replyToken = $event['replyToken'];
                
                                  $messages = [
                                        'type'=> 'image',
                                        'originalContentUrl'=> 'https://backup-bot.herokuapp.com/Manual/exercise.jpg',
                                        'previewImageUrl'=> 'https://backup-bot.herokuapp.com/Manual/exercise.jpg'
                                    ];
                                  $messages2 = [
                                        'type'=> 'image',
                                        'originalContentUrl'=> 'https://backup-bot.herokuapp.com/Manual/exercise2.jpg',
                                        'previewImageUrl'=> 'https://backup-bot.herokuapp.com/Manual/exercise2.jpg'
                                    ];
                                  $messages3 = [
                                        'type'=> 'image',
                                        'originalContentUrl'=> 'https://backup-bot.herokuapp.com/Manual/exercise3.jpg',
                                        'previewImageUrl'=> 'https://backup-bot.herokuapp.com/Manual/exercise3.jpg'
                                    ];
                
          $url = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages,$messages2,$messages3],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n";   
########################################################################################################################################################
 }elseif ($event['message']['text'] == "ข้อมูลการใช้งาน" ) {
               
                $replyToken = $event['replyToken'];
                      $manual =  'คู่มือการใช้งาน'. "\n".
                                  '1. หากคุณอยากทราบคู่มือการใช้งาน chatbot นี้ สามารถกดที่ recommend ด้านล่าง และกดที่ ข้อมูลการใช้งาน '. "\n".
                                  '2. คุณสามารถพิมพ์คำว่า “hello” , “สวัสดี” , “ต้องการผู้ช่วย” อย่างใดอย่างหนึ่ง เพื่อที่จะให้ทางเราได้เป็นผู้ช่วยของคุณ (ในกรณี add friend ในตอนแรกกดเลือกไม่สนใจ)'."\n".
                                  '3. เมื่อคุณกรอกข้อมูลกับทางเราเสร็จ ภายหลังจะมีการส่งข้อความทุกวันเวลาประมาณ 19.00 น. เพื่อถามข้อมูลประจำวันของคุณ หากคุณต้องกการยกเลิกการส่งข้อความนี้ ให้พิมพ์คำว่า “ยกเลิกข้อความ”'."\n".
                                  '4. เมื่อคุณกรอกข้อมูลกับทางเราเสร็จ ภายหลังจะมีการส่งข้อความทุกวันจนทร์เวลาประมาณ 08.00 น. เพื่อแสดงข้อมูลของทารกในครรภ์ของคุณ หากคุณต้องกการยกเลิกการส่งข้อความนี้ ให้พิมพ์คำว่า “งดรับข้อความ”'."\n".
                                  '5. ต้องการคำแนะนำเรื่องการรับประทานอาหาร สามารถกดที่ recommend ด้านล่าง และกดที่ แนะนำอาหาร'."\n".
                                  '6. ต้องการคำแนะนำท่าออกกำลังกายที่เหมาะสำหรับคนท้อง สามารถกดที่ recommend ด้านล่าง และกดที่ แนะนำการออกกำลังกาย'."\n".
                                  '7. หากคุณต้องการลบข้อมูลทั้งหมดที่กรอกไปตั้งแต่ต้น ให้พิมพ์คำว่า “Clear”'."\n";
                                  $messages = [
                                        'type'=> 'text',
                                        'text'=> $manual
                                    ];
########################################################################################################################################################
 }elseif ($event['message']['text'] == "เชื่อมต่อกับ ulife.info" ) {
               
                $replyToken = $event['replyToken'];
                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' =>'คุณเคยลงทะเบียนกับ ulife.info หรือไม่?' ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'เคย',
                                  'text' => 'เคยลงทะเบียน'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่เคย',
                                  'text' => 'ไม่เคยลงทะเบียน'
                              ],
                          ]
                      ]
                  ];  
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','3001','','3002','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################
 }elseif ($event['message']['text'] == "เคยลงทะเบียน" ) {
                
                $replyToken = $event['replyToken'];
                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' =>'คุณต้องการเชื่อมต่อไปยัง ulife.info หรือไม่?' ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'ต้องการ',
                                  'text' => 'ต้องการเชื่อมข้อมูล'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ต้องการ',
                                  'text' => 'ไม่ต้องการการเชื่อมข้อมูล'
                              ],
                          ]
                      ]
                  ];        
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','3002','','3003','0',NOW(),NOW())") or die(pg_errormessage());
                                  
########################################################################################################################################################
 }elseif ($event['message']['text'] == "ไม่เคยลงทะเบียน" ) {
               
                $replyToken = $event['replyToken'];
                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' =>'คุณต้องการเชื่อมต่อไปยัง ulife.info หรือไม่?' ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'ต้องการ',
                                  'text' => 'ต้องการเชื่อมข้อมูล'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ต้องการ',
                                  'text' => 'ไม่ต้องการเชื่อมข้อมูล'
                              ],
                          ]
                      ]
                  ]; 
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','3002','ไม่เคย','3003','0',NOW(),NOW())") or die(pg_errormessage()); 
########################################################################################################################################################
 }elseif ($event['message']['text'] == "ไม่ต้องการเชื่อมข้อมูล" && $seqcode == "3002" ) {
               
                $replyToken = $event['replyToken'];
                  $messages = [
                          'type' => 'text',
                          'text' =>'หากคุณต้องการเชื่อมข้อมูลกับ ulife.info ให้กดที่ recommend ด้านล่างได้เลยนะคะ' ,
                  ]; 
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','3002','ไม่ต้องการเชื่อมข้อมูล','3003','0',NOW(),NOW())") or die(pg_errormessage());  
// ########################################################################################################################################################
 }elseif ($event['message']['text'] == "ต้องการเชื่อมข้อมูล" && $seqcode == "3002" ) {
               
                $replyToken = $event['replyToken'];
                  $messages = [
                          'type' => 'text',
                          'text' =>'ขออีเมลที่ลงทะเบียนกับ Ulife.info หน่อยคะ' ,
                  ]; 
$q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','3003','','0000','0',NOW(),NOW())") or die(pg_errormessage());  
########################################################################################################################################################
/*}elseif (strpos($_msg, '@') !== false && $seqcode == "3003" ) {*/
}elseif (strpos($_msg,'@') !== false && $seqcode == "3003"){
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  $u = pg_escape_string($_msg);                 
                $replyToken = $event['replyToken'];
                      // $case = 1;
                      $url ='http://128.199.147.57/api/v1/peat/register';
                      $postData = array(
                               'email' => $_msg,
                               'line_id' => 'test4'
                            );
                      $ch = curl_init();
                      //set the url, number of POST vars, POST data
                      curl_setopt($ch,CURLOPT_URL, $url);
                      curl_setopt($ch,CURLOPT_POSTFIELDS, $postData);
                      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                      //execute post
                      $result = curl_exec($ch);
                      //close connection
                      curl_close($ch);
                      $re = json_decode($result,true);
                    
                      if(strpos($result, 'errors') !== false ){
                          $messages = [
                                  'type' => 'text',
                                  'text' =>'ต้องเป็นemailเท่านั้น' ,
                          ]; 
                   
                      }else{    
                                  $code = $re['code'];
                                  if ($code == '200'){
                                     
                                    $messages = [
                                            'type' => 'text',
                                            'text' =>'ไปยังอีเมลเพื่อรับรหัส เมื่อรับรหัสแล้วโปรดกรอกเพื่อยืนยัน' ,
                                    ]; 
                                    
                                      //$sequentsteps_insert =  $this->sequentsteps_update($user,$seqcode,$nextseqcode);
                                      $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','3004','','0000','0',NOW(),NOW())") or die(pg_errormessage());  
                                  }else{
                                    $messages = [
                                            'type' => 'text',
                                            'text' =>'ไม่สามารถลงทะเบียนได้เนื่องจาก lind id หรือ email ได้ลงทะเบียนแล้ว' ,
                                    ]; 
                                     
                                  }
                      }
########################################################################################################################################################
 }elseif (is_numeric($_msg) !== false &&  $seqcode == "3004"  ) {
                      $replyToken = $event['replyToken'];
                      $url ='http://128.199.147.57/api/v1/peat/verify';
                      $Data = array(
                               'token' => $_msg,
                               'line_id' => 'test4'
                            );
                      $ch = curl_init();
                      //set the url, number of POST vars, POST data
                      curl_setopt($ch,CURLOPT_URL, $url);
                      curl_setopt($ch,CURLOPT_POSTFIELDS, $Data);
                      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                      //execute post
                      $result = curl_exec($ch);
                      //close connection
                      curl_close($ch);
                      $re = json_decode($result,true);
                       
                       if(strpos($result, 'errors') !== false ){
                          $messages = [
                                  'type' => 'text',
                                  'text' =>'รหัสผิดพลาด' 
                          ]; 
                        
                      }else{    
                                $code = $re['code'];
                                 if ($code=='200'){
                                    $messages = [
                                            'type' => 'text',
                                            'text' =>'ทำการเชื่อมต่อแล้ว' 
                                    ]; 
                                  $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0000','','0000','0',NOW(),NOW())") or die(pg_errormessage());  
                                }else{
                                    $userMessage  = $re['message'];
                                      $messages = [
                                            'type' => 'text',
                                            'text' => $userMessage ,
                                    ]; 
                                }
                                  
                      }
########################################################################################################################################################
 }elseif ($event['message']['text'] == "ข้อมูลโภชนาการ" ) {
        $check_q2 = pg_query($dbconn,"SELECT user_weight, user_height, preg_week,user_age FROM users_register WHERE user_id = '{$user_id}' order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q2)) {
            
                  echo $weight = $row[0]; 
                  echo $height = $row[1]; 
                  echo $preg_week = $row[2];
                  echo $age = $row[3]; 
                } 
 /*คำนวณ BMI และบอกว่าอยู่ในเกณฑ์ไหน*/               
          $height1 =$height*0.01;
                  $bmi = $weight/($height1*$height1);
                  $bmi = number_format($bmi, 2, '.', '');
        if ($bmi<18.5) {
          $result="Underweight";
        } elseif ($bmi>=18.5 && $bmi<24.9) {
          $result="Nomal weight";
        } elseif ($bmi>=24.9 && $bmi<=29.9) {
          $result="Overweight";
        }else{
          $result="Obese";
        }
/*นำน้ำหนักมาคำนวณหาพลังงานและสารอาหารโดยใช้สูตรFAOแบ่งตามอายุ ตัวเลขที่ได้จะเป็นพลังงานที่ใช้ในขณะพักผ่อน*/
        if ($age>=10 && $age<18) {
          $cal=(13.384*$weight)+692.6;
        }elseif ($age>18 && $age<31) {
          $cal=(14.818*$weight)+486.6;
        }else{
          $cal=(8.126*$weight)+845.6;
        }
/*กิจกรรมทางกาย*/
        if ($_msg=="หนัก"  ) {
          $total = $cal*2.0;
        }elseif($_msg=="ปานกลาง") {
          $total = $cal*1.7;
        }else{
          $total = $cal*1.4;
        }
      $format = number_format($total);
               
       if ($preg_week >=13 && $preg_week<=40) {
                $format = $total+300;
               // $format = number_format($semester);
       }else{
               $format = $total;
       }
            if ($format < 1601) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 8 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 2 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 5 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 6 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 2 แก้ว';
                } elseif ($format > 1600 && $format <1701) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 9 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 2 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 5 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 6 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 2 แก้ว';
                }elseif ($format >1700 && $format <1801) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 9 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 3 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 6 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 6 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 2 แก้ว';
                }elseif ($format >1800 && $format<1901) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 9 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 3 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 6 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 8 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 2 แก้ว';
                }elseif ($format >1900 && $format<2001) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 10 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 3 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 7 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 8 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 2 แก้ว';
                }elseif ($format >2000 && $format<2101 ) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 11 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 3 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 7 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 8 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 2 แก้ว';
                }elseif ($format > 2100 && $format<2201) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 11 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 3 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 7 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 8 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 3 แก้ว';
                }elseif ($format > 2200 && $format < 2301) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 11 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 3 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 7 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 9 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 3 แก้ว';
                }elseif ($format > 2300 && $format <2401) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 12 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 3 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 7 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 10 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 3 แก้ว';
                }elseif ($format > 2400 && $format <2501) {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 12 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 4 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 8 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 10 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 3 แก้ว';
                }else {
                        $Nutrition =  'พลังงานที่ต้องการในแต่ละวันคือ'. "\n".
                                      '-ข้าววันละ 12 ทัพพี'. "\n".
                                      '-ผักวันละ 3 ทัพพี'."\n".
                                      '-ผลไม้วันละ 4 ส่วน (1 ส่วนคือปริมาณผลไม้ที่จัดใส่จานรองกาแฟเล็ก ๆ ได้ 1 จานพอดี)'."\n".
                                      '-เนื้อวันละ 9 ส่วน (1 ส่วนคือ 2 ช้อนโต๊ะ)'."\n".
                                      '-ไขมันวันละ 11 ช้อนชา'."\n".
                                      '-นมไขมันต่ำวันละ 3 แก้ว';
                }
     
                $replyToken = $event['replyToken'];
                
                      $messages = [
                          'type' => 'text',
                          'text' => $Nutrition
                      ];
                      $messages2 = [
                          'type' => 'text',
                          'text' => "หากคุณแม่ไม่ทราบว่าจะทานอะไรดีสามารถกดที่เมนู recommend ด้านล่างได้เลยนะคะ"
                      ];
          $url = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages,$messages2],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n";   
########################################################################################################################################################
}elseif ($event['message']['text'] == "ทารกในครรภ์" ) {
$check_q = pg_query($dbconn,"SELECT  user_age, user_weight ,user_height ,preg_week  FROM users_register WHERE  user_id = '{$user_id}' ");
                while ($row = pg_fetch_row($check_q)) {
                  echo $answer1 = $row[0]; 
                  echo $weight = $row[1]; 
                  echo $height = $row[2]; 
                  echo $answer4 = $row[3];  
                } 
$des_preg = pg_query($dbconn,"SELECT  descript,img FROM pregnants WHERE  week = $answer4 ");
              while ($row = pg_fetch_row($des_preg)) {
                  echo $des = $row[0]; 
                  echo $img = $row[1]; 
 
                } 
          $replyToken = $event['replyToken'];
                      $messages = [
                          'type' => 'text',
                          'text' => $des
                      ];
#########################################################################################################################################################
}elseif ($event['message']['text'] == "ไม่ยืนยัน"  ) {
               // $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
               //  while ($row = pg_fetch_row($result)) {
               //    echo $answer = $row[0]; 
               //  }   
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ไว้โอกาสหน้าให้เราได้เป็นผู้ช่วยของคุณนะคะ^^'
                      ];
                 $messages2 = [
                        'type' => 'text',
                        'text' => 'หากคุณต้องการรับข้อความในภายหลัง สามารถพิมพ์คำว่า "ยืนยัน" ได้เลยนะคะ'
                      ];
$q = pg_exec($dbconn, "UPDATE users_register SET status = '0' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
//$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0025','','0027','0',NOW(),NOW())") or die(pg_errormessage());
    $url = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages,$messages2],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n";
#########################################################################################################################################################
}elseif ($event['message']['text'] == "ยืนยัน"  ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอบคุณที่ให้เราได้เป็นผู้ช่วยนะคะ^^'
                      ];
$q = pg_exec($dbconn, "UPDATE users_register SET status = '1' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO auto_reply(user_id,auto_week,auto_day,created_at,updated_at )VALUES('{$user_id}','1','1',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################
}elseif ($event['message']['text'] == "หนัก" || $event['message']['text'] == "ปานกลาง" || $event['message']['text'] == "เบา" ) {
                 
     $check_q2 = pg_query($dbconn,"SELECT user_weight, user_height, preg_week,user_age FROM users_register WHERE user_id = '{$user_id}' order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q2)) {
            
                  echo $weight = $row[0]; 
                  echo $height = $row[1]; 
                  echo $preg_week = $row[2];
                  echo $age = $row[3]; 
                } 
 /*คำนวณ BMI และบอกว่าอยู่ในเกณฑ์ไหน*/               
          $height1 =$height*0.01;
                  $bmi = $weight/($height1*$height1);
                  $bmi = number_format($bmi, 2, '.', '');
        if ($bmi<18.5) {
          $result="Underweight";
        } elseif ($bmi>=18.5 && $bmi<24.9) {
          $result="Nomal weight";
        } elseif ($bmi>=24.9 && $bmi<=29.9) {
          $result="Overweight";
        }else{
          $result="Obese";
        }
/*นำน้ำหนักมาคำนวณหาพลังงานและสารอาหารโดยใช้สูตรFAOแบ่งตามอายุ ตัวเลขที่ได้จะเป็นพลังงานที่ใช้ในขณะพักผ่อน*/
        if ($age>=10 && $age<18) {
          $cal=(13.384*$weight)+692.6;
        }elseif ($age>18 && $age<31) {
          $cal=(14.818*$weight)+486.6;
        }else{
          $cal=(8.126*$weight)+845.6;
        }
/*กิจกรรมทางกาย*/
        if ($_msg=="หนัก"  ) {
          $total = $cal*2.0;
        }elseif($_msg=="ปานกลาง") {
          $total = $cal*1.7;
        }else{
          $total = $cal*1.4;
        }
      $format = number_format($total);
               
  $check_q4 = pg_query($dbconn,"SELECT starches ,vegetables, fruits, meats, fats, lf_milk, c, p, f, g_protein  FROM meal_planing WHERE caloric_level <= $total");
                while ($row = pg_fetch_row($check_q4)) {
            
          //echo $caloric = $row[0]; 
          echo $starches = $row[0];
          echo $vegetables = $row[1];
          echo $fruits = $row[2];
          echo $meats = $row[3];
          echo $fats = $row[4];
          echo $lf_milk = $row[5];
          echo $c = $row[6];
          echo $p = $row[7];
          echo $f = $row[8];
          echo $g_protein  = $row[9];
                } 
              
                 $ccc =  "น้ำหนักของคุณเกินเกณฑ์ ลองปรับการรับประทานอาหารหรือออกกำลังกายดูไหมคะ". "\n".
                          "หากคุณแม่ไม่ทราบว่าจะทานอะไรดีหรือออกกำลังกายแบบไหนดีสามารถกดที่เมนู recommend ด้านล่างได้เลยนะคะ";
                 $rec = "หากคุณแม่ไม่ทราบว่าจะทานอะไรดีหรือออกกำลังกายแบบไหนดีสามารถกดที่เมนู recommend ด้านล่างได้เลยนะคะ";
                 $Q_send = "ต่อจากนี้ทางเราจะมีการส่งข้อความมาเพื่อสอบถามข้อมูลและแนะนำทุกวัน เวลา 19:00 น. และบอกความคืบหน้าของทารกในครรภ์ในทุกวันจันทร์ หากคุณต้องการรับข้อมูลกรุณากดยืนยันด้วยค่ะ";
                 
                 $replyToken = $event['replyToken'];
  
/*ตั้งครรภ์ในช่วงไตรมาสที่ 2 และ 3 ให้บวกจำนวณแคลเพิ่มอีก300    */               
           
                if ($preg_week >=13 && $preg_week<=40) {
                  $a = $total+300;
                  $format2 = number_format($a);    
                      $messages = [
                                                              
                        'type' => 'template',
                        'altText' => 'template',
                        'template' => [
                            'type' => 'buttons',
                            //'thumbnailImageUrl' => 'https://chatbot-nutrition-pregnant.herokuapp.com/week/'.$preg_week .'.jpg',
                            'title' => 'จำนวนแคลอรี่ที่คุณต้องการต่อวันคือ '.$format2,
                            'text' =>  'รายละเอียดการรับประทานอาหารสามารถกดปุ่มด้านล่างได้เลยค่ะ',
                            'actions' => [
                                  [
                                    'type' => 'uri',
                                    'label' => 'ไปยังลิงค์',
                                    'uri' => 'http://www.raipoong.com/content/detail.php?section=12&category=26&id=467'
                                  ],
                                  [
                                    'type' => 'message',
                                    'label' => 'ข้อมูลโภชนาการ',
                                    'text' => 'ข้อมูลโภชนาการ'
                                    ]
                                ]
                              ]
                            ];
                   }else{
                      
                      $messages = [
                                                              
                        'type' => 'template',
                        'altText' => 'template',
                        'template' => [
                            'type' => 'buttons',
                            //'thumbnailImageUrl' => 'https://chatbot-nutrition-pregnant.herokuapp.com/week/'.$preg_week .'.jpg',
                            'title' => 'จำนวนแคลอรี่ที่คุณต้องการต่อวันคือ '.$format,
                            'text' =>  'รายละเอียดการรับประทานอาหารสามารถกดปุ่มด้านล่างได้เลยค่ะ',
                            'actions' => [
                                  [
                                    'type' => 'uri',
                                    'label' => 'ไปยังลิงค์',
                                    'uri' => 'http://www.raipoong.com/content/detail.php?section=12&category=26&id=467'
                                  ],
                                  [
                                    'type' => 'message',
                                    'label' => 'ข้อมูลโภชนาการ',
                                    'text' => 'ข้อมูลโภชนาการ'
                                    ]
                                ]
                              ]
                            ];
                  }
                  /*รายละเอียดเด็กในครรภ์*/
                    if ($bmi>=24.9 ) {
                        
                        $messages2 = [
                                                              
                        'type' => 'template',
                        'altText' => 'template',
                        'template' => [
                            'type' => 'buttons',
                            'thumbnailImageUrl' => 'https://backup-bot.herokuapp.com/week/'.$preg_week .'.jpg',
                            'title' => 'ขณะนี้คุณมีอายุครรภ์'.$preg_week.'สัปดาห์',
                            'text' =>  'ค่าดัชนีมวลกายของคุณคือ '.$bmi. ' อยู่ในเกณฑ์ '.$result,
                            'actions' => [
                                   [
                                    'type' => 'uri',
                                    'label' => 'กราฟ',
                                    'uri' => 'https://backup-bot.herokuapp.com/chart_bot.php?data='.$user_id
                                    ],
                                  [
                                    'type' => 'message',
                                    'label' => 'ทารกในครรภ์',
                                    'text' => 'ทารกในครรภ์'
                                    ]
                                      ]
                                  ]
                              ];
                          $messages3 = [
                            'type' => 'text',
                            'text' => $ccc
                      ];
                    }else{
                       $messages2 = [
                                                              
                        'type' => 'template',
                        'altText' => 'template',
                        'template' => [
                            'type' => 'buttons',
                            'thumbnailImageUrl' => 'https://backup-bot.herokuapp.com/week/'.$preg_week .'.jpg',
                            'title' => 'ขณะนี้คุณมีอายุครรภ์'.$preg_week.'สัปดาห์',
                            'text' =>  'ค่าดัชนีมวลกายของคุณคือ '.$bmi. ' อยู่ในเกณฑ์ '.$result,
                            'actions' => [
                                   [
                                    'type' => 'uri',
                                    'label' => 'กราฟ',
                                    'uri' => 'https://backup-bot.herokuapp.com/chart_bot.php?data='.$user_id
                                    ],
                                  [
                                    'type' => 'message',
                                    'label' => 'ทารกในครรภ์',
                                    'text' => 'ทารกในครรภ์'
                                    ]
                                      ]
                                  ]
                              ];
                          $messages3 = [
                            'type' => 'text',
                            'text' => $rec
                      ];
                    }
                    $messages4 = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' => $Q_send ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'ยืนยัน',
                                  'text' => 'ยืนยัน'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่ยืนยัน',
                                  'text' => 'ไม่ยืนยัน'
                              ],
                          ]
                      ]
                  ]; 
    $url = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages,$messages2,$messages3,$messages4],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n";
#########################################################################################################################################################
}elseif ($event['message']['text'] == "แพ้ยา"  ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                  // $u2 = pg_escape_string($surname);
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'คุณแพ้ยาอะไรคะ?'
                      ];
// $q = pg_exec($dbconn, "UPDATE users_register SET hospital_number = $answer WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0025','','0027','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################
}elseif (strpos($_msg) !== false && $seqcode == "0027" || $event['message']['text'] == "ไม่แพ้อาหาร" ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
            $u = pg_escape_string($_msg); 
            $replyToken = $event['replyToken'];
        
        $que = "ช่วงระหว่างการตั้งครรภ์คุณออกกำลังกายในระดับไหน?";
        $que2 = "รายละเอียดของระดับ". "\n".
                "เบา -  วิถีชีวิตทั่วไป ไม่มีการออกกำลังกาย หรือมีการออกกำลังกายน้อย". "\n".
                "ปานกลาง - วิถีชีวิตกระฉับกระเฉง หรือ มีการออกกำลังกายสม่ำเสมอ". "\n".
                "หนัก - วิถีชีวิตมีการใช้แรงงานหนัก ออกกำลังกายหนักเป็นประจำ". "\n";  
        $messages = [
              'type' => 'text',
              'text' => $que
        ];
        $messages2 = [
              'type' => 'text',
              'text' => $que2
        ];
        $messages3 = [
          'type'=> 'template',
          'altText'=> 'this is a buttons template',
          'template'=> [
              'type'=> 'buttons',
              //'thumbnailImageUrl'=> 'https://example.com/bot/images/image.jpg',
              'title'=> "ระดับของการออกกำลังกาย",
              'text'=> "เลือกระดับด้านล่างได้เลยค่ะ",
              'actions'=> [
                  [
                    'type'=> 'message',
                    'label'=> 'เบา',
                    'text'=> 'เบา'
                  ],
                  [
                    'type'=> 'message',
                    'label'=> 'ปานกลาง',
                    'text'=> 'ปานกลาง'
                  ],
                  [
                    'type'=> 'message',
                    'label'=> 'หนัก',
                    'text'=> 'หนัก'
                  ]
              ]
          ]
        ];
$q = pg_exec($dbconn, "UPDATE users_register SET  history_food = '{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0027','{$u}','1001','0',NOW(),NOW())") or die(pg_errormessage());
          $url = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages,$messages2,$messages3],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n"; 
#########################################################################################################################################################
}elseif ($event['message']['text'] == "แพ้อาหาร" ) {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }   
                 $u = pg_escape_string($answer);
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => 'คุณแพ้อาหารอะไรคะ?'
                      ];
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0027','','1001','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################
}elseif (strpos($_msg) !== false && $seqcode == "0025" || $event['message']['text'] == "ไม่แพ้ยา" )  {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer = $row[0]; 
                }  
                 $u = pg_escape_string($_msg); 
                 $replyToken = $event['replyToken'];
                  $messages = [
                      'type' => 'template',
                      'altText' => 'this is a confirm template',
                      'template' => [
                          'type' => 'confirm',
                          'text' =>'คุณมีประวัติการแพ้อาหารไหมคะ?' ,
                          'actions' => [
                              [
                                  'type' => 'message',
                                  'label' => 'มี',
                                  'text' => 'แพ้อาหาร'
                              ],
                              [
                                  'type' => 'message',
                                  'label' => 'ไม่มี',
                                  'text' => 'ไม่แพ้อาหาร'
                              ],
                          ]
                      ]
                  ]; 
$q = pg_exec($dbconn, "UPDATE users_register SET  history_medicine ='{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0026','{$u}','0027','0',NOW(),NOW())") or die(pg_errormessage());
####################################################################################### 
}elseif ($event['message']['text'] == "น้ำหนักถูกต้อง" && $seqcode ='1003') {
    $check_q = pg_query($dbconn,"SELECT seqcode, sender_id ,updated_at ,answer FROM sequentsteps  WHERE sender_id = '{$user_id}' order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($check_q)) {
            
                  echo $answer_weight = $row[3];  
                } 
             
    $check = pg_query($dbconn,"SELECT preg_week FROM recordofpregnancy WHERE user_id = '{$user_id}' order by updated_at desc limit 1 ");
            while ($row = pg_fetch_row($check)) {
                echo  $p_week =  $row[0]+1;
                } 
    $q2 = pg_exec($dbconn, "INSERT INTO recordofpregnancy(user_id, preg_week, preg_weight,updated_at )VALUES('{$user_id}',$p_week,$answer_weight ,  NOW()) ") or die(pg_errormessage());  
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','0000', '' ,'0000','0',NOW(),NOW())") or die(pg_errormessage()); 
            $replyToken = $event['replyToken'];
                              
                              $messages = [                            
                                  'type' => 'template',
                                  'altText' => 'template',
                                  'template' => [
                                      'type' => 'buttons',
                                      'thumbnailImageUrl' => 'https://backup-bot.herokuapp.com/week/'.$p_week .'.jpg',
                                      'title' => 'ลูกน้อยของคุณ',
                                      'text' =>  'อายุ'.$p_week .'สัปดาห์',
                                      'actions' => [
                                          // [
                                          //     'type' => 'postback',
                                          //     'label' => 'good',
                                          //     'data' => 'value'
                                          // ],
                                          [
                                              'type' => 'uri',
                                              'label' => 'กราฟ',
                                              'uri' => 'https://backup-bot.herokuapp.com/chart_bot.php?data='.$user_id
                                          ]
                                      ]
                                  ]
                              ]; 
                              $messages2 = [
                                      'type' => 'text',
                                      'text' => 'หากคุณไม่ต้องการรับข้อความในทุกวันจันทร์ ให้พิมพ์คำว่า "งดรับข้อความ" ได้เลยค่ะ '
                                  ];        
         $url = 'https://api.line.me/v2/bot/message/reply';
         $data = [
          'replyToken' => $replyToken,
          'messages' => [$messages,$messages2],
         ];
         error_log(json_encode($data));
         $post = json_encode($data);
         $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
         $ch = curl_init($url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         $result = curl_exec($ch);
         curl_close($ch);
         echo $result . "\r\n"; 
########################################################################################################### 
}elseif ($event['message']['text'] == "งดรับข้อความ" && $seqcode ='1003') {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer_food = $row[0]; 
                }  
               //   $u = pg_escape_string($_msg); 
                 $replyToken = $event['replyToken'];
 
                 $messages = [
                        'type' => 'text',
                        'text' => 'ไว้โอกาสหน้าให้เราได้เป็นผู้ช่วยของคุณนะคะ หากต้องการกลับมารับข้อความอีกครั้งให้พิมพ์คำว่า"รับข้อความรายสัปดาห์"ได้เลยค่ะ'
                      ]; 
// //$q2 = pg_exec($dbconn, "INSERT INTO tracker(user_id,food, exercise,vitamin,updated_at )VALUES('{$user_id}','','{$u}','',  NOW()) ") or die(pg_errormessage());  
// // //$q = pg_exec($dbconn, "UPDATE users_register SET  history_medicine ='{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
// $q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2003', '' ,'2004','0',NOW(),NOW())") or die(pg_errormessage());
$q = pg_exec($dbconn, "UPDATE users_register SET  status ='0' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
########################################################################################################### 
}elseif ($event['message']['text'] == "รับข้อความรายสัปดาห์" && $seqcode ='1003') {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer_food = $row[0]; 
                }  
               //   $u = pg_escape_string($_msg); 
                 $replyToken = $event['replyToken'];
 
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอบคุณที่ให้เราได้เป็นผู้ช่วยของคุณนะคะ'
                      ]; 
// //$q2 = pg_exec($dbconn, "INSERT INTO tracker(user_id,food, exercise,vitamin,updated_at )VALUES('{$user_id}','','{$u}','',  NOW()) ") or die(pg_errormessage());  
// // //$q = pg_exec($dbconn, "UPDATE users_register SET  history_medicine ='{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
// $q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2003', '' ,'2004','0',NOW(),NOW())") or die(pg_errormessage());
$q = pg_exec($dbconn, "UPDATE users_register SET  status ='1' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
########################################################################################################### 
########################################################################################################### 
}elseif ($event['message']['text'] == "ยกเลิกข้อความ" && $seqcode ='2003') {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer_food = $row[0]; 
                }  
               //   $u = pg_escape_string($_msg); 
                 $replyToken = $event['replyToken'];
 
                 $messages = [
                        'type' => 'text',
                        'text' => 'ไว้โอกาสหน้าให้เราได้เป็นผู้ช่วยของคุณนะคะ หากต้องการกลับมารับข้อความอีกครั้งให้พิมพ์คำว่า"รับข้อความ"ได้เลยค่ะ'
                      ]; 
// //$q2 = pg_exec($dbconn, "INSERT INTO tracker(user_id,food, exercise,vitamin,updated_at )VALUES('{$user_id}','','{$u}','',  NOW()) ") or die(pg_errormessage());  
// // //$q = pg_exec($dbconn, "UPDATE users_register SET  history_medicine ='{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
// $q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2003', '' ,'2004','0',NOW(),NOW())") or die(pg_errormessage());
$q = pg_exec($dbconn, "UPDATE auto_reply SET auto_day ='0' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
########################################################################################################### 
}elseif ($event['message']['text'] == "รับข้อความ" && $seqcode ='2003') {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer_food = $row[0]; 
                }  
               //   $u = pg_escape_string($_msg); 
                 $replyToken = $event['replyToken'];
 
                 $messages = [
                        'type' => 'text',
                        'text' => 'ขอบคุณที่ให้เราได้เป็นผู้ช่วยของคุณนะคะ'
                      ]; 
// //$q2 = pg_exec($dbconn, "INSERT INTO tracker(user_id,food, exercise,vitamin,updated_at )VALUES('{$user_id}','','{$u}','',  NOW()) ") or die(pg_errormessage());  
// // //$q = pg_exec($dbconn, "UPDATE users_register SET  history_medicine ='{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
// $q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2003', '' ,'2004','0',NOW(),NOW())") or die(pg_errormessage());
$q = pg_exec($dbconn, "UPDATE auto_reply SET auto_day ='1' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
########################################################################################################################################################
  }elseif (is_numeric($_msg) !== false && $seqcode == "1003"  )  {
                 $weight =  $_msg;
                 $weight_mes = 'สัปดาห์นี้คุณมีน้ำหนัก'.$weight.'กิโลกรัมถูกต้องหรือไม่คะ';
                 $replyToken = $event['replyToken'];
                 $messages = [
                                'type' => 'template',
                                'altText' => 'this is a confirm template',
                                'template' => [
                                    'type' => 'confirm',
                                    'text' =>  $weight_mes ,
                                    'actions' => [
                                        [
                                            'type' => 'message',
                                            'label' => 'ถูกต้อง',
                                            'text' => 'น้ำหนักถูกต้อง'
                                        ],
                                        [
                                            'type' => 'message',
                                            'label' => 'ไม่ถูกต้อง',
                                            'text' => 'ไม่ถูกต้อง'
                                        ],
                                    ]
                                 ]     
                             ];   
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','1003', $weight,'','0',NOW(),NOW())") or die(pg_errormessage()); 
########################################################################################################### 
}elseif ($event['message']['text'] == "ยังไม่ได้ทาน" && $seqcode ='2002') {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer_food = $row[0]; 
                }  
               //   $u = pg_escape_string($_msg); 
                 $replyToken = $event['replyToken'];
 
                 $messages = [
                        'type' => 'text',
                        'text' => 'อย่าลืมทานวิตามินเพื่อทารกในครรภ์นะคะ:)'
                      ]; 
                 $messages2 = [
                                'type' => 'template',
                                'altText' => 'this is a confirm template',
                                'template' => [
                                    'type' => 'confirm',
                                    'text' =>  'วันนี้คุณออกกำลังกายไหมคะ?' ,
                                    'actions' => [
                                        [
                                            'type' => 'message',
                                            'label' => 'ออก',
                                            'text' => 'ออกกำลังกาย'
                                        ],
                                        [
                                            'type' => 'message',
                                            'label' => 'ไม่ออก',
                                            'text' => 'ไม่ได้ออกกำลังกาย'
                                        ]
                                    ]
                                 ]     
                             ];       
//$q2 = pg_exec($dbconn, "INSERT INTO tracker(user_id,food, exercise,vitamin,updated_at )VALUES('{$user_id}','','{$u}','',  NOW()) ") or die(pg_errormessage());  
// //$q = pg_exec($dbconn, "UPDATE users_register SET  history_medicine ='{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2002', '' ,'2003','0',NOW(),NOW())") or die(pg_errormessage());
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
########################################################################################################### 
}elseif ($event['message']['text'] == "ทานแล้ว" && $seqcode ='2002') {
               // $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
               //  while ($row = pg_fetch_row($result)) {
               //    echo $answer_food = $row[0]; 
               //  }  
               //   $u = pg_escape_string($_msg); 
                 $replyToken = $event['replyToken'];
                 $messages = [
                                'type' => 'template',
                                'altText' => 'this is a confirm template',
                                'template' => [
                                    'type' => 'confirm',
                                    'text' =>  'วันนี้คุณออกกำลังกายไหมคะ?' ,
                                    'actions' => [
                                        [
                                            'type' => 'message',
                                            'label' => 'ออก',
                                            'text' => 'ออกกำลังกาย'
                                        ],
                                        [
                                            'type' => 'message',
                                            'label' => 'ไม่ออก',
                                            'text' => 'ไม่ได้ออกกำลังกาย'
                                        ]
                                    ]
                                 ]     
                             ];  
// $q2 = pg_exec($dbconn, "INSERT INTO tracker(user_id,food, exercise,vitamin,updated_at )VALUES('{$user_id}','{$u}','','',  NOW()) ") or die(pg_errormessage());  
// //$q = pg_exec($dbconn, "UPDATE users_register SET  history_medicine ='{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2002', '' ,'2003','0',NOW(),NOW())") or die(pg_errormessage());
########################################################################################################################################################
  }elseif (strpos($_msg) !== false && $seqcode == "2001"  )  {
                 $food =  $_msg;
                 $food_mes = 'ในวันนี้คุณได้ทาน'.$food.'ไปนะคะ';
                 $u = pg_escape_string($_msg); 
                 $vitamin = 'วันนี้คุณทานวิตามินไปหรือยังคะ?';
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' => $food_mes
                      ];
                 $messages2 = [
                                'type' => 'template',
                                'altText' => 'this is a confirm template',
                                'template' => [
                                    'type' => 'confirm',
                                    'text' =>  $vitamin ,
                                    'actions' => [
                                        [
                                            'type' => 'message',
                                            'label' => 'ทานแล้ว',
                                            'text' => 'ทานแล้ว'
                                        ],
                                        [
                                            'type' => 'message',
                                            'label' => 'ยังไม่ได้ทาน',
                                            'text' => 'ยังไม่ได้ทาน'
                                        ]
                                    ]
                                 ]     
                             ];  
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2001','{$u}','2002','0',NOW(),NOW())") or die(pg_errormessage()); 
$q2 = pg_exec($dbconn, "INSERT INTO tracker(user_id,food, exercise,vitamin,updated_at )VALUES('{$user_id}','{$u}','','',  NOW()) ") or die(pg_errormessage());  
//$q = pg_exec($dbconn, "UPDATE users_register SET  history_medicine ='{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
// $q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2001', '' ,'2002','0',NOW(),NOW())") or die(pg_errormessage());
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
########################################################################################################### 
}elseif ($event['message']['text'] == "ไม่ได้ออกกำลังกาย" && $seqcode ='2003') {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer_food = $row[0]; 
                }  
               //   $u = pg_escape_string($_msg); 
                 $replyToken = $event['replyToken'];
 
                 $messages = [
                        'type' => 'text',
                        'text' => 'แนะนำคุณแม่ออกกำลังกายให้ได้อย่างน้อย150นาที ต่อ 1 สัปดาห์เพื่อสุขภาพของคุณแม่ค่ะ^^'
                      ];
                 $messages2 = [
                        'type' => 'text',
                        'text' => 'หากต้องการให้ทางเรายกเลิกการส่งข้อความทุกเย็น ให้คุณพิมพ์ว่า "ยกเลิกข้อความ" ได้เลยนะคะ'
                      ]; 
//$q2 = pg_exec($dbconn, "INSERT INTO tracker(user_id,food, exercise,vitamin,updated_at )VALUES('{$user_id}','','{$u}','',  NOW()) ") or die(pg_errormessage());  
// //$q = pg_exec($dbconn, "UPDATE users_register SET  history_medicine ='{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2003', '' ,'2004','0',NOW(),NOW())") or die(pg_errormessage());
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
########################################################################################################### 
}elseif ($event['message']['text'] == "ออกกำลังกาย" && $seqcode ='2003') {
               $result = pg_query($dbconn,"SELECT answer FROM sequentsteps  WHERE sender_id = '{$user_id}'  order by updated_at desc limit 1   ");
                while ($row = pg_fetch_row($result)) {
                  echo $answer_food = $row[0]; 
                }  
               //   $u = pg_escape_string($_msg); 
                 $replyToken = $event['replyToken'];
 
                 $messages = [
                        'type' => 'text',
                        'text' => 'คุณออกกำลังกายอะไรบ้างคะ'
                      ]; 
//$q2 = pg_exec($dbconn, "INSERT INTO tracker(user_id,food, exercise,vitamin,updated_at )VALUES('{$user_id}','','{$u}','',  NOW()) ") or die(pg_errormessage());  
// //$q = pg_exec($dbconn, "UPDATE users_register SET  history_medicine ='{$u}' WHERE user_id = '{$user_id}' ") or die(pg_errormessage()); 
$q1 = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2003', '' ,'2004','0',NOW(),NOW())") or die(pg_errormessage());
#########################################################################################################################################################
}elseif (strpos($_msg) !== false && $seqcode == "2003"  )  {
                 $food =  $_msg;
                 $exer = 'ในวันนี้คุณออกกำลังกายโดย'.$food.'ไปนะคะ';
                 $u = pg_escape_string($_msg); 
                 
                 $replyToken = $event['replyToken'];
                 $messages = [
                        'type' => 'text',
                        'text' =>  $exer
                      ];
                  $messages2 = [
                        'type' => 'text',
                        'text' => 'หากต้องการให้ทางเรายกเลิกการส่งข้อความทุกเย็น ให้คุณพิมพ์ว่า "ยกเลิกข้อความ" ได้เลยนะคะ'
                      ];    
    $q = pg_exec($dbconn, "INSERT INTO sequentsteps(sender_id,seqcode,answer,nextseqcode,status,created_at,updated_at )VALUES('{$user_id}','2003','{$u}','2004','0',NOW(),NOW())") or die(pg_errormessage()); 
 
$q2 = pg_exec($dbconn, "INSERT INTO tracker(user_id,food, exercise,vitamin,updated_at )VALUES('{$user_id}','','{$u}','',  NOW()) ") or die(pg_errormessage()); 
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
########################################################################################################### 
// }elseif ($event['type'] == 'message' && $event['message']['type'] == 'text'){
    
//      $replyToken = $event['replyToken'];
//       $text = "ดิฉันไม่เข้าใจค่ะ กรุณาพิมพ์ใหม่อีกครั้งนะคะ";
//       $messages = [
//           'type' => 'text',
//           'text' => $text
//         ]; 
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
