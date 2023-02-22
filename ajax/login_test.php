<?php
require dirname(__DIR__) . '/functions.php';
require_once PATH_PROJECT . '/connect.php';


$email = $_POST["email"];
$password = $_POST["password"];


$test =  json_decode($email, $password);
echo $test;

header('Content-type: application/json');
echo json_encode($_POST);


