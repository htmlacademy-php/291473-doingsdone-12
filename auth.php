<?php
require_once('helpers.php');
require_once('includes/functions.inc.php');
require_once('includes/db_connect.inc.php');

$page_content = include_template('auth.php', []);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'doingsdone: авторизация',
]);

$errors = authenticate($con);

print_r($errors);

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
