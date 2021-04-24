<?php
require_once('helpers.php');
require_once('includes/functions.inc.php');
require_once('includes/db_connect.inc.php');

// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
$user_name = 'dmitry';

$projects = select_query($con, "SELECT p.* FROM projects p INNER JOIN users u ON u.id = p.user_id WHERE user_name = '$user_name'");
$tasks = select_query($con, "SELECT t.*, p.* FROM tasks t INNER JOIN users u ON u.id = t.user_id INNER JOIN projects p ON p.id = t.project_id WHERE user_name = '$user_name'");

function get_tasks_count($tasks, $project)
{
    $tasks_count = 0;
    foreach ($tasks as $task) {
        if ($task['project_name'] == $project) {
            $tasks_count++;
        }
    }
    return $tasks_count;
};

function get_task_time($task)
{
    $current_time = time();
    $task_time = strtotime($task['date']);
    $task_deadline = ($task_time - $current_time) / 3600;

    if ($task_deadline <= 24) {
        return 'task--important';
    }
};

$page_content = include_template('main.php', [
    'projects' => $projects,
    'tasks' => $tasks,
    'show_complete_tasks' => $show_complete_tasks,
]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'user_name' => $user_name,
    'title' => 'readme: популярное',
]);

print($layout_content);
