<?php

function select_query($con, $sql, $type = 'all')
{
    mysqli_set_charset($con, "utf8");
    $result = mysqli_query($con, $sql) or trigger_error("Ошибка в запросе к базе данных: " . mysqli_error($con), E_USER_ERROR);

    if ($type == 'assoc') {
        return mysqli_fetch_assoc($result);
    }

    if ($type == 'row') {
        return mysqli_fetch_row($result)[0];
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

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

function open_404_page()
{
    $page_content = include_template('page-404.php');
    $layout_content = include_template('layout.php', [
    'title' => 'doingsdone: страница не найдена',
    'content' => $page_content,
  ]);

    echo($layout_content);
    http_response_code(404);
    exit();
}

function get_project_tasks ($project_id, $tasks) {
    if ($project_id) {
        $project_tasks = [];
        foreach($tasks as $task) {
            if ($task['project_id'] == $project_id) {
                $project_tasks[] = $task;
            }
        }
    } else {
        $project_tasks = $tasks;
    }

    if (!$project_tasks) {
        open_404_page();
    }

    return $project_tasks;
}

