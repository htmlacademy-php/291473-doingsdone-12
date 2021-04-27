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

function check_name_field($name)
{
    $errors = array();
    if (empty($name)) {
        $errors['name'] = $fields_map['name'] . ' Поле не заполнено.';
    }
    return $errors;
}

function check_date_format($date) {
    $errors = array();
    $date_format = DateTime::CreateFromFormat('Y-m-d', $date);

    $current_date = strtotime(date('d-m-Y'));
    $task_date = strtotime($date);
    
    if (!$date_format) {
        $errors['date'] = $fields_map['date'] . ' Ошибка в формате даты.';
    } else if ($task_date < $current_date) {
        $errors['date'] = $fields_map['date'] . ' Дата должна быть больше или равна текущей.';
    }

    return $errors;
};

function check_project_id($con, $project_id) {
    $errors = array();
    $selected_project = select_query($con, "SELECT * FROM projects WHERE id = '$project_id'");

    if (!$selected_project) {
        $errors['project'] = $fields_map['project'] . ' Проект не найден.';
    }

    return $errors;
}

function check_validity($con, $required_fields, $fields_map)
{
    if (empty($_POST)) {
        return null;
    }

    $name = $_POST['name'];
    $project = $_POST['project'];
    $date = $_POST['date'];
    $file_name = $_FILES['file']['name'];
    $file_path = 'uploads/';
    $file_url = 'uploads/' . $file_name;
    move_uploaded_file($_FILES['file']['tmp_name'], $file_path . $file_name);

    //$empty_fields = check_empty_field($required_fields, $fields_map, $errors);

    $name_field_errors = check_name_field($name);
    $date_field_errors = check_date_format($date);
    $project_field_errors = check_project_id($con, $project);

    //print_r($_POST);

    $errors = array_merge($name_field_errors, $date_field_errors, $project_field_errors);
    print_r($errors);
    if ($errors) {
        return $errors;
    }
    
}