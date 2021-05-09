<?php
require_once('includes/functions.inc.php');
require_once('includes/db_connect.inc.php');

$today = date('Y-m-d');
$opened_tasks = select_query($con, "SELECT task_name, user_id, deadline FROM tasks WHERE status = 0 AND deadline = '$today'");

$users_ids_not_unique = [];
foreach ($opened_tasks as $opened_task) {
    $users_ids_not_unique[] = $opened_task['user_id'];
}
$users_ids = array_unique($users_ids_not_unique);

$messages = [];
$user_messages = [];
foreach ($users_ids as $user_id) {
   

    // print($user_id);
    // print('<br>');
    // print('<br>');
    foreach ($opened_tasks as $key => $opened_task) {
    // print_r($opened_task);
    // print('<br>');
    // print('<br>');

        if ($opened_task['user_id'] == $user_id) {
            $user_messages[$user_id] = $opened_task;
        }
    }
    $messages[] = $user_messages; 
}

print_r($messages);

// print_r($opened_tasks);
// print('<br>');
// print('<br>');
// print_r($users_ids);
// print('<br>');
// print('<br>');
// print_r($messages);



// $all_users = select_query($con, "SELECT id, user_name FROM users");
// $all_users_ids = [];
// foreach($all_users as $user) {
//     $all_users_ids[] = $user['id'];
// }
// $all_users_ids_list = implode(", ", $all_users_ids);

// foreach($opened_tasks as $opened_task) {

// }

// Получает список пользователей
// $all_users = select_query($con, "SELECT id FROM users"); // Тут нужно получить массив со списком всех пользователей;

// $all_users = '1, 2';


// $today = date('Y-m-d');
// $opened_tasks = select_query($con, "SELECT task_name, user_id FROM tasks WHERE status = 0 AND deadline = '$today'");


// print_r($opened_tasks);
// print('<br>');
// print_r($all_users);

// foreach($all_users as $user) {
//     print($user['id']);
// }





// $opened_tasks = select_query($con, "SELECT * FROM tasks WHERE status = 0 AND deadline = '$today'");
// print_r($opened_tasks);

// require('vendor/autoload.php');

// $email_title = 'Тестовое сообщение';
// $email = 'dkrech07@gmail.com';
// $login = 'dkrech07@gmail.com';
// $email_message = 'Тестовое сообщение';


// // Конфигурация траспорта
// $transport = (new Swift_SmtpTransport("smtp.mailtrap.io", 2525))
//     ->setUsername('85b3b85f4a89a3')
//     ->setPassword('3f60ebb5c55821')
// ;

// // Формирование сообщения
// $message = new Swift_Message($email_title);
// $message->setFrom("keks@phpdemo.ru", "keks@phpdemo.ru");
// $message->setTo([$email => $login]);
// $message->setBody($email_message);

// // Отправка сообщения
// $mailer = new Swift_Mailer($transport);
// $mailer->send($message);