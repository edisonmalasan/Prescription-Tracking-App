<?php
require_once '../controllers/AuthController.php';

$route = $_GET['route'] ?? 'login';
$auth = new AuthController();

switch ($route) {
    case 'login':
        include '../src/views/auth/login.php';
        break;
    case 'register':
        include '../src/views/auth/register.php';
        break;
    case 'doLogin':
        $auth->login($_POST);
        break;
    case 'doRegister':
        $auth->register($_POST);
        break;
    case 'logout':
        $auth->logout();
        break;

    default: include './src/views/auth/login.php';
    break;
}