<?php
require_once('helpers.php');
require_once('includes/functions.inc.php');
require_once('includes/db_connect.inc.php');

$errors = authenticate($con);

$page_content = include_template('auth.php', [
    'errors' => $errors,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'doingsdone: авторизация',
]);

print_r($_SESSION['user']);

// $page_content = include_template('main.php', [
//     'projects' => $projects,
//     'tasks' => $tasks,
//     'project_tasks' => $project_tasks,
//     'show_complete_tasks' => $show_complete_tasks,
//     'project_id' => $project_id,
// ]);

// $layout_content = include_template('layout.php', [
//     'content' => $page_content,
//     'title' => 'doingsdone: проекты',
// ]);

echo($layout_content);
