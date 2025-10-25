<?php
require_once '../controllers/DoctorController.php';

$route = $_GET['route'] ?? 'index.html';
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

    default: include './pages/index.html';
    break;
}