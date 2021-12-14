<?php
require 'vendor/autoload.php';
require 'vendor/mustache/mustache/src/Mustache/Autoloader.php';
include('config.php');

use Src\Auth\AuthController;
use Src\Auth\Paths\login;
use Src\User\Home;

Mustache_Autoloader::register();
$mustache = new Mustache_Engine;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

session_start();

$authcontroller = new AuthController($config, $_SERVER, $_SESSION);
$authcontroller = $authcontroller->processRequest();

if (!$authcontroller){
    session_destroy();
    $logincontent = new Login();
    echo $logincontent->pagecontent();
    exit();
}

$Homecontent = new Home($_SERVER, $_POST, $config, $mustache, $authcontroller);
echo $Homecontent->processRequest();

