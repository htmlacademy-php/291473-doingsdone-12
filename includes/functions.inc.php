<?php

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = [])
{
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

/**
 * Выполняет подключение и запрос к базе данных
 * В случае ошибки при подключении к БД, возвращает сообщение об ошибке
 * @param  object $con Ресурс соединения
 * @param  string $sql SQL-запрос к базе данных
 * @param  string $type Варианты массива, полученного при обращении к базе данных
 * @return array
 */
function select_query($con, $sql, $type = 'all')
{
    mysqli_set_charset($con, "utf8");
    $result = mysqli_query($con, $sql) or trigger_error("Ошибка в запросе к базе данных: " . mysqli_error($con), E_USER_ERROR);

    if ($type === 'assoc') {
        return mysqli_fetch_assoc($result);
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Считает количество задач по проектам
 * @param  array $tasks Ассоциативный массив со списком задач
 * @param  string $project Название проекта, для которого будет посчитано количество входящих в него задач
 * @return integer
 */
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

/**
 * Проверяет, что до дедлайна задачи осталось не меньше 24 часов
 * @param  array $task Ассоциативный массив с информацией по задаче
 * @return string
 */
function get_task_time($task)
{
    $current_time = time();
    $task_time = strtotime($task['deadline']);
    $task_deadline = ($task_time - $current_time) / 3600;

    if (isset($task['deadline']) && $task_deadline <= 24) {
        return 'task--important';
    }
};

/**
 *  Подключает шаблон и выводит страницу 404, возвращает код ответа 404
 * @return void
 */
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

/**
 * Определяет количество символов в полях формы
 * Записывает сообщение о ошибке в массив errors, если при отправке формы количесто символов в поле больше допустимого
 * @param  array $required_fields Нумерованный массив со списком полей для проверки
 * @return array
 */
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

/**
 * Из общего массива задач получает массив задач по id конкретного проекта
 * Если id проекта отсутствует в базе данных, открывает 404 страницу
 * @param  integer $project_id ID проекта, для которого следует получить список задач
 * @param  array $tasks Общий список задач для всех проектов, авторизованного пользователя
 * @return array
 */
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

/**
 * Проверяет поля формы на заполнение
 * Записывает сообщение о ошибке в массив errors, если при отправке формы обязательное поле не заполнено
 * @param  array $required_fields Нумерованный массив со списком полей для проверки на заполнение
 * @return array
 */
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

/**
 * Проверяет созданную задачу на корректность, сохраняет задачу в базу данных и открывает страницу index.php
 * @param  object $con Ресурс соединения
 * @param  integer $user_id ID авторизованного пользователя, создавшего задачу
 * @return []
 */
function check_new_task_validity($con, $user_id)
{
    if (empty($_POST)) {
        return [];
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
        $safe_project_id = mysqli_real_escape_string($con, $project_id);
        $selected_project = select_query($con, "SELECT * FROM projects WHERE id = '$safe_project_id'");

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
    return [];
}

/**
 * Проверяет созданный проект на корректность, сохраняет проект в базу данных и открывает страницу index.php
 * @param  object $con Ресурс соединения
 * @param  integer $user_id ID авторизованного пользователя, создавшего проект
 * @return void
 */
function check_new_project_validity($con, $user_id)
{
    if (empty($_POST)) {
        return [];
    }

    $project_name = $_POST['project_name'];

    $errors = check_empty_field(['project_name']);
    if (empty($errors)) {
        $errors = check_field_length(['project_name']);
    }

    $safe_project_name = mysqli_real_escape_string($con, $project_name);
    $already_created_project = select_query($con, "SELECT * FROM projects WHERE user_id = '$user_id' AND project_name = '$safe_project_name'");

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
    return [];
}

/**
 * Проверяет данные регистрируемого пользователя на корректность (заполнение обязательных полей, есть ли email в базе, формат email)
 * Сохраняет данные нового пользователя в базу данных
 * @param  object $con Ресурс соединения
 * @return void
 */
function check_registration_validity($con)
{
    if (empty($_POST)) {
        return [];
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

        $safe_email = mysqli_real_escape_string($con, $email);
        $already_saved_email = select_query($con, "SELECT email FROM users WHERE email = '$safe_email'");

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
    return [];
}

/**
 * Авторизует пользователя в системе, запускает сессию
 * Выдает ошибку, в случае если неверно заполнены логин или пароль
 * @param  object $con Ресурс соединения
 * @return void
 */
function authenticate($con)
{
    session_start();
    if (empty($_POST) && empty($_SESSION['user'])) {
        return [];
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
            $email = $_POST['email'];

            $safe_email = mysqli_real_escape_string($con, $email);
            $user_query = select_query($con, "SELECT * FROM users WHERE email = '$safe_email'", 'assoc');

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

/**
 * Через get-запрос, определяет id и статус задачи, изменяет статус задачи на выполнено / невыполнено
 * @param  object $con Ресурс соединения
 * @param  integer $user_id ID авторизованного пользователя
 * @return null
 */
function get_task_status($con, $user_id)
{
    $task_status = filter_input(INPUT_GET, 'check', FILTER_VALIDATE_INT);
    $task_id = filter_input(INPUT_GET, 'task_id', FILTER_VALIDATE_INT);

    $safe_task_id = mysqli_real_escape_string($con, $task_id);
    $safe_user_id = mysqli_real_escape_string($con, $user_id);
    $checked_task = select_query($con, "SELECT * FROM tasks WHERE id = '$safe_task_id' AND user_id = '$safe_user_id'", 'assoc');

    if (isset($task_status)) {
        if (intval($checked_task['status']) === 0) {
            $status = mysqli_real_escape_string($con, 1);
        } else {
            $status = mysqli_real_escape_string($con, 0);
        }

        mysqli_query($con, "UPDATE tasks SET status = '$status' WHERE id = '$task_id' AND user_id = '$user_id'");


        header("Location: /index.php");
        exit();
    }
}

/**
 * Получает ассоциативный массив со списком задач в зависимости от выбранного временного промежутка (все, завтрашние, сегодняшние, просроченные)
 * @param  string $date Временной отрезок, для фильтрации задач: today, tomorrow, overdue
 * @param  array $tasks Ассоциативный массив со списком задач
 * @return array
 */
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

            if (isset($task_date) && $task_date < $today) {
                $filtered_tasks[] = $task;
            }
        }
        return $filtered_tasks;
    }

    return $tasks;
}
