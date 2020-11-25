<?php

define('REQUEST_ID', uniqid());
spl_autoload_register(function ($class) {
    require_once(__DIR__ . '/' . str_replace('\\', '/', $class) . '.php');
    cn_output($class);
});

function cn_output($msg){
    file_put_contents('class.log',$msg . PHP_EOL,FILE_APPEND);
}

function error($message, $code = 400)
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($message);
}

function success($data=null,$code=200){
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
}

set_exception_handler(function($exception){
    exit(error('Sorry, an unexpected error occurred. Please try again later, or contact us if the problem persists. Reference #'.REQUEST_ID,500));
});

function verify_session() {
    if (!isset($_SESSION['key']) or !isset($_SESSION['hash'])) {
        exit(error("Your session was lost. Please refresh and try again."));
    }
}

