<?php  
$conn_string = "host=ec2-54-227-247-225.compute-1.amazonaws.com port=5432 dbname=d7i9sj05534uua user=twyavrmgujwujj password=78cac0794ff9469d19800c70521b078dd1e2505ebf978e4239f7f7393d3916a8";
$dbconn = pg_pconnect($conn_string);
if (!$dbconn) {
    die("Connection failed: " . mysqli_connect_error());
}
########################CREATE TABLE #######################################################
$sql="CREATE TABLE sequents(
id SERIAL,
seqcode varchar(255),
question varchar(255),
answer varchar(255),
nexttype integer,
nextseqcode varchar(255),
created_at timestamp,
updated_at timestamp,
deleted_at timestamp
PRIMARY KEY(id)
)";   
pg_exec($dbconn, $sql) or die(pg_errormessage());

$sql2="CREATE TABLE sequentsteps(
id SERIAL,
sender_id varchar(50),
seqcode varchar(30),
answer varchar(255),
nextseqcode varchar(255),
status varchar(255),
created_at timestamp,
updated_at timestamp,
PRIMARY KEY(id)
)";   
pg_exec($dbconn, $sql2) or die(pg_errormessage());

?>
