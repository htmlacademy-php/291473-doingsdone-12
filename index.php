<?php
require_once 'includes/functions.inc.php';
require_once 'includes/db_connect.inc.php';

session_start();

if (isset($_SESSION['user'])) {

    $user_id = $_SESSION['user']['id'];
    $safe_user_id = mysqli_real_escape_string($con, $user_id);
    $show_complete_tasks = filter_input(INPUT_GET, 'show_completed', FILTER_VALIDATE_INT);
    get_task_status($con, $user_id);

    $search = filter_input(INPUT_GET, 'search') ?? '';
    if ($search) {
        $search_query = "SELECT t.*, p.* FROM tasks t INNER JOIN users u ON u.id = t.user_id INNER JOIN projects p ON p.id = t.project_id WHERE u.id = '$safe_user_id' AND MATCH(task_name) AGAINST(?) ORDER BY t.id DESC";
        $stmt = mysqli_prepare($con, $search_query);
        mysqli_stmt_bind_param($stmt, 's', $search);
        mysqli_stmt_execute($stmt);
        $search_result = mysqli_stmt_get_result($stmt);
        $tasks = mysqli_fetch_all($search_result, MYSQLI_ASSOC);
    } else {
        $tasks = select_query($con, "SELECT t.*, p.project_name FROM tasks t INNER JOIN users u ON u.id = t.user_id INNER JOIN projects p ON p.id = t.project_id WHERE u.id = '$safe_user_id' ORDER BY t.id");
    }

    $date = filter_input(INPUT_GET, 'date', FILTER_DEFAULT);

    $project_id = filter_input(INPUT_GET, 'project-id', FILTER_VALIDATE_INT);
    $projects = select_query($con, "SELECT p.* FROM projects p INNER JOIN users u ON u.id = p.user_id WHERE u.id = '$safe_user_id' ORDER BY p.id DESC");

    $project_tasks = get_task_date($date, get_project_tasks($project_id, $tasks, $user_id, $con));

    $page_content = include_template('main.php', [
        'projects' => $projects,
        'tasks' => $tasks,
        'project_tasks' => $project_tasks,
        'show_complete_tasks' => $show_complete_tasks,
        'project_id' => $project_id,
        'search' => $search,
        'date' => $date,
    ]);
    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => 'doingsdone: проекты',
    ]);
    echo ($layout_content);
    exit();
}

$page_content = include_template('guest.php', []);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'doingsdone: регистрация',
]);

echo ($layout_content);
