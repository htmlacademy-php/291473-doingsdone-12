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

function check_validity($con, $user_id)
{
    if (empty($_POST)) {
        return null;
    }

    $name = $_POST['name'];
    $project_id = $_POST['project'];
    $date = $_POST['date'];
    $file_name = $_FILES['file']['name'];
    $current_date = date('Y-m-d');

    $file_path = 'uploads/';
    $file_url = 'uploads/' . $file_name;
    move_uploaded_file($_FILES['file']['tmp_name'], $file_path . $file_name);
    
    $errors = array();

    if (empty($name)) {
        $errors['name'] = 'Поле не заполнено.';
    }

    $date_format = DateTime::CreateFromFormat('Y-m-d', $date);
    $current_date_mark = strtotime($current_date);
    $task_date_mark = strtotime($date);
    if (!$date_format || !$date) {
        $errors['date'] = 'Ошибка в формате даты';
    } else if ($task_date_mark < $current_date_mark) {
        $errors['date'] = 'Дата должна быть больше или равна текущей';
    }

    $selected_project = select_query($con, "SELECT * FROM projects WHERE id = '$project_id'");
    if (!$selected_project) {
        $errors['project'] = 'Проект не найден';
    }

    if ($errors) {
        return $errors;
    }

    $status = 0;
    $post_query = "INSERT INTO tasks (create_date, status, task_name, file_link, deadline, user_id, project_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $post_query);
    mysqli_stmt_bind_param($stmt, 'sisssii', $current_date, $status, $name, $file_url, $date, $user_id, $project_id);
    mysqli_stmt_execute($stmt);

    header('Location: /');
    exit();
    return null;
}
