<?php
require_once('helpers.php');
require_once('includes/functions.inc.php');
require_once('includes/db_connect.inc.php');

session_start();

if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['id'];

    $project_id = filter_input(INPUT_GET, 'project-id', FILTER_VALIDATE_INT);
    $projects = select_query($con, "SELECT p.* FROM projects p INNER JOIN users u ON u.id = p.user_id WHERE u.id = '$user_id' ORDER BY p.id DESC");
    $tasks = select_query($con, "SELECT t.*, p.* FROM tasks t INNER JOIN users u ON u.id = t.user_id INNER JOIN projects p ON p.id = t.project_id WHERE u.id = '$user_id' ORDER BY t.id DESC");
    $project_tasks = get_project_tasks ($project_id, $tasks);
    
    
    $project_name = $_POST['project_name'];

    // Проверка на существование проекта - вынести в функцию;
    $already_created_project = select_query($con, "SELECT * FROM projects WHERE project_name = '$project_name'");
    print($project_name);
    if ($already_created_project) {
        print('Такой проект уже есть в системе');
    }

    //$errors = check_new_task_validity($con, $user_id);
    
    $page_content = include_template('add-project.php', [
        'projects' => $projects,
        'tasks' => $tasks,
        // 'project_tasks' => $project_tasks,
        // 'show_complete_tasks' => $show_complete_tasks,
        // 'project_id' => $project_id,
        // 'errors' => $errors,
    ]);
    
    $layout_content = include_template('layout.php', [
        'content' => $page_content,
        'title' => 'doingsdone: добавление проекта',
    ]);
    
    echo($layout_content);
    exit();
} 

$page_content = include_template('guest.php', []);
$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'title' => 'doingsdone: регистрация',
]);

echo ($layout_content);
