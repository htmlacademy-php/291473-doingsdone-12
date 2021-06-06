<?php
require_once 'includes/functions.inc.php';
require_once 'includes/db_connect.inc.php';

$errors = authenticate($con);

$page_content = include_template('auth.php', [
    'errors' => $errors,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'doingsdone: авторизация',
]);

echo ($layout_content);
