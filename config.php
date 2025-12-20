<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$DB_HOST='localhost';$DB_USER='root';$DB_PASS='';$DB_NAME='MUSEUM_DATABASE';
$conn=new mysqli($DB_HOST,$DB_USER,$DB_PASS,$DB_NAME);
if($conn->connect_error){die('Database connection failed: '.$conn->connect_error);} $conn->set_charset('utf8mb4');
function h($s){return htmlspecialchars(strval($s),ENT_QUOTES,'UTF-8');}
?>