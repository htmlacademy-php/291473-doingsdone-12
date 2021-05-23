<?php
require_once('includes/functions.inc.php');
require_once('includes/db_connect.inc.php');
require_once('vendor/autoload.php');

$today = date('Y-m-d');

$opened_tasks = select_query($con, "SELECT u.email, u.user_name, t. task_name, t.user_id, t.deadline FROM tasks t INNER JOIN users u ON u.id = t.user_id WHERE t.status = 0 AND t.deadline = '$today'");
$users_lits = [];

foreach ($opened_tasks as $opened_task) {
    $user_info = [];
    $user_info[] = $opened_task['email'];
    $user_info[] = $opened_task['user_name'];
    $users_lits[] = $user_info;
}

foreach ($users_lits as $key => $user) {
    if ($user === $users_lits[$key + 1]) {
        unset($users_lits[$key + 1]);
    }
}

$messages = [];
foreach ($users_lits as $user) {
    $user_info = [];
    $user_tasks = '';
    foreach ($opened_tasks as $opened_task) {
        if ($opened_task['email'] === $user[0]) {
            $user_tasks = $user_tasks . ' ' . $opened_task['task_name'];
        }
        $user_info[0] = $user[1];
        $user_info[1] = $user_tasks;
    }
    $messages[$user[0]] = $user_info;
}

foreach ($messages as $email => $email_message) {
    $email_title = 'Уведомление от сервиса «Дела в порядке»';
    $email_message = 'Уважаемый, ' . $email_message[0] . '. У вас запланирована задача ' . $email_message[1] . ' на ' . $today;

    $transport = (new Swift_SmtpTransport("smtp.mailtrap.io", 2525))
        ->setUsername('85b3b85f4a89a3')
        ->setPassword('3f60ebb5c55821');

    $message = new Swift_Message($email_title);
    $message->setFrom("keks@phpdemo.ru", "keks@phpdemo.ru");
    $message->setTo([$email => $email]);
    $message->setBody($email_message);

    $mailer = new Swift_Mailer($transport);
    $mailer->send($message);
}
