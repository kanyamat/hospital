<?php  
$conn_string = "host=ec2-54-227-247-225.compute-1.amazonaws.com port=5432 dbname=d7i9sj05534uua user=twyavrmgujwujj password=78cac0794ff9469d19800c70521b078dd1e2505ebf978e4239f7f7393d3916a8";
$dbconn = pg_pconnect($conn_string);
if (!$dbconn) {
    die("Connection failed: " . mysqli_connect_error());
}
########################CREATE TABLE #######################################################

// $sql="CREATE TABLE sequents(
// id SERIAL,
// seqcode varchar(255),
// question text ,
// answer varchar(255),
// nexttype integer,
// nextseqcode varchar(255),
// created_at timestamp,
// updated_at timestamp,
// deleted_at timestamp,
// PRIMARY KEY(id)
// )";   
// pg_exec($dbconn, $sql) or die(pg_errormessage());

// $sql2="CREATE TABLE sequentsteps(
// id SERIAL,
// sender_id varchar(50),
// seqcode varchar(30),
// answer varchar(255),
// nextseqcode varchar(255),
// status varchar(255),
// created_at timestamp,
// updated_at timestamp,
// deleted_at timestamp,
// PRIMARY KEY(id)
// )";   
// pg_exec($dbconn, $sql2) or die(pg_errormessage());

$sql3="CREATE TABLE document_data(
id SERIAL,
user_id varchar(50),
document_type integer,
document_name text,
status varchar(255),
created_at timestamp,
updated_at timestamp,
deleted_at timestamp,
PRIMARY KEY(id)
)";   
pg_exec($dbconn, $sql3) or die(pg_errormessage());


$sql4="CREATE TABLE document_type(
id SERIAL,
document_type integer,
document_descript text,
created_at timestamp,
updated_at timestamp,
deleted_at timestamp,
PRIMARY KEY(id)
)";   
pg_exec($dbconn, $sql4) or die(pg_errormessage());



// $sql2="INSERT INTO  sequents (id, seqcode, question, answer, nexttype, nextseqcode, created_at, updated_at) VALUES
// (1, '0001', 'ยินดีครับ/ค่ะ ', NULL, 1, '0002', NULL, NULL),

// (2, '0002', 'ผู้ป่วยเป็นผู้ชายหรือผู้หญิงวัยหมดประจำเดือนหรือได้คุมกำเนิดด้วยวิธีทำหมัน, ฉีดยาคุม, ฝังยาคุมหรือใส่ห่วงอนามัยแล้วใช่หรือไม่', NULL, 1, '0003', NULL, NULL),

// (3, '0003', 'กรุณาส่งผู้ป่วยปรึกษาวางแผนครอบครัวเพื่อได้รับการคุมกำเนิดที่มีประสิทธิภาพอย่างใดอย่างหนึ่ง ได้แก่ การทำหมัน,ฉีดยาคุม,ฝังยาคุมหรือใส่ห่วงอนามัย เนื่องจากผู้ป่วยจำเป็นต้องคุมกำเนิดก่อนได้รับรักษาด้วยการกลืนแร่และคุมกำเนิดต่อเนื่องอีกอย่างน้อย 6 เดือนหลังการรักษา', NULL, 1, '0004', NULL, NULL),

// (4, '0004', 'ขอบคุณครับ/ค่ะ เมื่อผู้ป่วยได้รับการคุมกำเนิดเรียบร้อยแล้วกรุณาส่งมานัดใหม่อีกครั้งนะครับ/ค่ะ โดยเริ่มต้นพิมพ์ข้อความว่า “ขอนัดกลืนแร่” ', NULL, 1, '0005', NULL, NULL),

// (5, '0005', 'กรุณารอพยาบาลโรงพยาบาลราชวิถีติดต่อกลับสักครู่', NULL, 1, '0006', NULL, NULL),

// (6, '0006', 'กรุณาส่งรูปใบส่งตัวที่กรอกรายละเอียดชัดเจน โดยเฉพาะตำแหน่งที่ 1, 2, 3 ตามตัวอย่างรูปด้านล่าง ',NULL, 1, '0007', NULL, NULL),

// (7, '0007', 'ท่านส่งรูปใบส่งตัวที่ชัดเจนเรียบร้อยแล้วใช่หรือไม่', NULL, 1, '0008', NULL, NULL),

// (8, '0008', 'ผู้ป่วยใช้สิทธิการรักษาเป็นประกันสังคมหรือไม่',NULL, 1, '0009', NULL, NULL),

// (9, '0009', 'กรุณาส่งรูปใบส่งตัวประกันสังคมที่ชัดเจน โดยเฉพาะตำแหน่งที่ 1, 2, 3, 4 พร้อมประทับตราครุฑ ตามรูปตัวอย่างด้านล่างนี้', NULL, 1, '0010', NULL, NULL),

// (10, '0010', 'ท่านส่งรูปใบส่งตัวประกันสังคมชัดเจนเรียบร้อยแล้วใช่หรือไม่',NULL, 1, '0011', NULL, NULL),

// (11, '0011', 'กรุณาส่งรูปเอกสารหมายเลข 1 ที่กรอกรายละเอียดชัดเจนโดยเฉพาะตำแหน่งที่ 1, 2  (กรุณากรอกหรือติดสติ๊กเกอร์ ชื่อ-สกุลด้วย) ตามรูปตัวอย่างด้านล่างนี้', NULL, 1, '0012', NULL, NULL),

// (12, '0012', 'ท่านส่งรูปเอกสารหมายเลข 1 ที่ชัดเจนเรียบร้อยแล้วใช่หรือไม่',NULL, 1, '0013', NULL, NULL),

// (13, '0013', 'ท่านส่งรูปเอกสารหมายเลข 1 ที่ชัดเจนเรียบร้อยแล้วใช่หรือไม่', NULL, 1, '0014', NULL, NULL),

// (14, '0014', 'กรุณาส่งรูปบัตรประจำตัวประชาชนของผู้ป่วยที่ชัดเจนตามรูปตัวอย่างด้านล่างนี้',NULL, 1, '0015', NULL, NULL),

// (15, '0015', 'ท่านส่งรูปบัตรประจำตัวประชาชนที่ชัดเจนเรียบร้อยแล้วหรือไม่', NULL, 1, '0016', NULL, NULL),

// (16, '0016', 'คุณมีอายุครรภ์', 'ใช่ไหมคะ ถ้าไม่ถูกต้องกรุณาพิมพ์วันที่และเดือนครั้งสุดท้ายที่คุณมีประจำเดือนใหม่ค่ะ', 1, '0017', NULL, NULL),

// (17, '0017', 'กรุณาส่งรูปใบกรอกประวัติผู้ป่วยใหม่ของโรงพยาบาลราชวิถีที่ชัดเจนโดยเฉพาะตำแหน่งที่ 1,2,....    จากแบบฟอร์มตามรูปด้านล่างนี้ (กรุณาระบุเบอร์โทรศัพท์ที่สามารถติดต่อผู้ป่วยได้ให้ชัดเจน)', NULL, 1, '0018', NULL, NULL),

// (18, '0018', 'ท่านส่งรูปใบกรอกประวัติผู้ป่วยใหม่ที่ชัดเจนเรียบร้อยแล้วใช่หรือไม่',NULL, 1, '0019', NULL, NULL),

// (19, '0019', 'กรุณารอพยาบาลโรงพยาบาลราชวิถีตรวจสอบข้อมูลและติดต่อกลับเพื่อแจ้งวันนัดตรวจรักษาสักครู่นะครับ/ค่ะ', NULL, 2, '0020', NULL, NULL)";

// pg_exec($dbconn, $sql2) or die(pg_errormessage());

?>
