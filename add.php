<?php
require_once('helpers.php');
require_once('includes/functions.inc.php');
require_once('includes/db_connect.inc.php');

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
$user_id = 1;

$project_id = filter_input(INPUT_GET, 'project-id', FILTER_VALIDATE_INT);
$projects = select_query($con, "SELECT p.* FROM projects p INNER JOIN users u ON u.id = p.user_id WHERE u.id = '$user_id'");
$tasks = select_query($con, "SELECT t.*, p.* FROM tasks t INNER JOIN users u ON u.id = t.user_id INNER JOIN projects p ON p.id = t.project_id WHERE u.id = '$user_id'");
$project_tasks = get_project_tasks ($project_id, $tasks);

$errors = check_validity($con, $user_id);

$page_content = include_template('add.php', [
    'projects' => $projects,
    'tasks' => $tasks,
    'project_tasks' => $project_tasks,
    'show_complete_tasks' => $show_complete_tasks,
    'project_id' => $project_id,
    'errors' => $errors,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'doingsdone: добавление задачи',
]);

echo($layout_content);