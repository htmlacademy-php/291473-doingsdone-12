<?php
/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

function select_query($con, $sql, $type = 'all')
{
    mysqli_set_charset($con, "utf8");
    $result = mysqli_query($con, $sql) or trigger_error("Ошибка в запросе к базе данных: " . mysqli_error($con), E_USER_ERROR);

    if ($type === 'assoc') {
        return mysqli_fetch_assoc($result);
    }

    if ($type === 'row') {
        return mysqli_fetch_row($result)[0];
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function get_tasks_count($tasks, $project)
{
    $tasks_count = 0;
    foreach ($tasks as $task) {
        if ($task['project_name'] === $project) {
            $tasks_count++;
        }
    }
    return $tasks_count;
};

function get_task_time($task)
{
    $current_time = time();
    $task_time = strtotime($task['deadline']);
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

    echo ($layout_content);
    http_response_code(404);
    exit();
}

function check_field_length($required_fields)
{
    $errors = array();
    foreach ($required_fields as $field) {
        if ($field === 'password' && mb_strlen($_POST[$field]) > 64) {
            $errors[$field] = 'Максимальная длина текста 64 символа';
        } else if (mb_strlen($_POST[$field]) > 128) {
            $errors[$field] = 'Максимальная длина текста 128 символов';
        }
    }

    return $errors;
}

function get_project_tasks($project_id, $tasks)
{
    if ($project_id) {
        $project_tasks = [];
        foreach ($tasks as $task) {
            if (intval($task['project_id']) === $project_id) {
                $project_tasks[] = $task;
            }
        }
        if (empty($project_tasks)) {
            open_404_page();
        }
    } else {
        $project_tasks = $tasks;
    }

    return $project_tasks;
}

function check_empty_field($required_fields)
{
    $errors = array();
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[$field] = 'Поле не заполнено';
        }
    }

    return $errors;
}

function check_new_task_validity($con, $user_id)
{
    if (empty($_POST)) {
        return null;
    }

    $name = $_POST['name'];
    $project_id = $_POST['project'] ?? '';
    $date = $_POST['date'];
    $current_date = date('Y-m-d');
    $file_name = $_FILES['file']['name'];

    if ($file_name) {
        $file_path = 'uploads/';
        $file_url = 'uploads/' . $file_name;
        move_uploaded_file($_FILES['file']['tmp_name'], $file_path . $file_name);
    } else {
        $file_url = '';
    }

    $errors = check_empty_field(['name', 'project']);
    if (empty($errors)) {
        $errors = check_field_length(['name', 'project']);
    }

    if ($date) {
        $date_format = DateTime::CreateFromFormat('Y-m-d', $date);
        $current_date_mark = strtotime($current_date);
        $task_date_mark = strtotime($date);
        if (!$date_format) {
            $errors['date'] = 'Ошибка в формате даты';
        } else if ($task_date_mark < $current_date_mark) {
            $errors['date'] = 'Дата должна быть больше или равна текущей';
        }
    } else {
        $date = null;
    }

    if ($project_id) {
        $selected_project = select_query($con, "SELECT * FROM projects WHERE id = '$project_id'");
        if (!$selected_project) {
            $errors['project'] = 'Выберите существующий проект';
        }
    }

    if ($errors) {
        return $errors;
    }

    $status = 0;
    $post_query = "INSERT INTO tasks (create_date, status, task_name, file_link, deadline, user_id, project_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $post_query);
    mysqli_stmt_bind_param($stmt, 'sisssii', $current_date, $status, $name, $file_url, $date, $user_id, $project_id);
    mysqli_stmt_execute($stmt);

    header('Location: index.php');
    exit();
    return null;
}

function check_new_project_validity($con, $user_id)
{
    if (empty($_POST)) {
        return null;
    }

    $project_name = $_POST['project_name'];

    $errors = check_empty_field(['project_name']);
    if (empty($errors)) {
        $errors = check_field_length(['project_name']);
    }

    $already_created_project = select_query($con, "SELECT * FROM projects WHERE user_id = '$user_id' AND project_name = '$project_name'");;
    if ($already_created_project) {
        $errors['project_name'] = 'Такой проект уже есть в системе';
    }

    if (!empty($errors)) {
        return $errors;
    }

    $post_query = "INSERT INTO projects (project_name, user_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($con, $post_query);
    mysqli_stmt_bind_param($stmt, 'si', $project_name, $user_id);
    mysqli_stmt_execute($stmt);

    header('Location: index.php');
    exit();
    return null;
}

function check_registration_validity($con)
{
    if (empty($_POST)) {
        return null;
    }

    $email = $_POST['email'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $current_date = date('Y-m-d');

    $required_fields = ['email', 'password', 'name'];

    $errors = check_empty_field($required_fields);
    if (empty($errors)) {
        $errors = check_field_length($required_fields, $errors);
    }

    if (empty($errors)) {
        $email_format = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email_format) {
            $errors['email'] = 'Неверный формат.';
        }

        $already_saved_email = select_query($con, "SELECT email FROM users WHERE email = '$email'");
        if (isset($already_saved_email[0]['email'])) {
            $errors['email'] = 'Уже есть в системе.';
        }
    }

    if (!empty($errors)) {
        return $errors;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $post_query = "INSERT INTO users (registration_date, email, user_name, password) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $post_query);
    mysqli_stmt_bind_param($stmt, 'ssss', $current_date, $email, $name, $password_hash);
    mysqli_stmt_execute($stmt);
    header('Location: index.php');
    exit();
    return null;
}

function authenticate($con)
{
    session_start();
    if (empty($_POST) && empty($_SESSION['user'])) {
        return null;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $required = ['email', 'password'];
        $errors = [];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $errors[$field] = 'Это поле надо заполнить';
            }
        }

        if (empty($errors)) {
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $user_query = select_query($con, "SELECT * FROM users WHERE email = '$email'", 'assoc');
            $user = $user_query ? $user_query : null;

            if (isset($user)) {
                if (password_verify($_POST['password'], $user['password'])) {
                    $_SESSION['user'] = $user;
                } else {
                    $errors['password'] = 'Неверный пароль';
                }
            } elseif (!isset($user)) {
                $errors['user'] = 'Такой пользователь не найден';
            }
        }
    } else {
        if (isset($_SESSION['user'])) {
            header("Location: /index.php");
            exit();
        }
    }

    if (empty($errors)) {
        header("Location: /index.php");
        exit();
    }

    return $errors;
}

function get_task_status($con, $user_id)
{
    $task_status = filter_input(INPUT_GET, 'check', FILTER_VALIDATE_INT);
    $task_id = filter_input(INPUT_GET, 'task_id', FILTER_VALIDATE_INT);

    if ($task_status === 1) {
        mysqli_query($con, "UPDATE tasks SET status = 1 WHERE id = '$task_id' AND user_id = '$user_id'");
    }

    if ($task_status === 0) {
        mysqli_query($con, "UPDATE tasks SET status = 0 WHERE id = '$task_id' AND user_id = '$user_id'");
    }

    if (isset($task_status)) {
        header("Location: /index.php");
        exit();
    }
}

function get_task_date($date, $tasks)
{
    $today = date('Y-m-d');
    $filtered_tasks = [];

    if ($date === 'today') {
        foreach ($tasks as $task) {
            $task_date = $task['deadline'];

            if ($task_date === $today) {
                $filtered_tasks[] = $task;
            }
        }
        return $filtered_tasks;
    }

    if ($date === 'tomorrow') {
        $tomorrow = date('Y-m-d', strtotime("+1 day"));
        foreach ($tasks as $task) {
            $task_date = $task['deadline'];

            if ($task_date === $tomorrow) {
                $filtered_tasks[] = $task;
            }
        }
        return $filtered_tasks;
    }

    if ($date === 'overdue') {
        foreach ($tasks as $task) {
            $task_date = $task['deadline'];

            if ($task_date < $today) {
                $filtered_tasks[] = $task;
            }
        }
        return $filtered_tasks;
    }

    return $tasks;
}
