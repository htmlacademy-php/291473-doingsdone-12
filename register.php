<?php
require_once 'includes/functions.inc.php';
require_once 'includes/db_connect.inc.php';

session_start();
if (isset($_SESSION['user']['id'])) {
    header("Location: /index.php");
    exit();
}

$errors = check_registration_validity($con);

$page_content = include_template('register.php', [
    'errors' => $errors,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'doingsdone: авторизация/регистрация',
]);

echo ($layout_content);
