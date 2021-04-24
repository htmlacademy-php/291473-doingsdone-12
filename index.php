<?php
require_once('helpers.php');
// показывать или нет выполненные задачи
$show_complete_tasks = rand(0, 1);
$user_name = 'Дмитрий';


$projects = ['Входящие', 'Учеба', 'Работа', 'Домашние дела', 'Авто'];
$tasks = [
    [
        'name' => 'Собеседование в IT компании',
        'date' => '01.12.2019',
        'category' => 'Работа',
        'completed' => false,
    ],
    [
        'name' => 'Выполнить тестовое задание',
        'date' => '25.12.2019',
        'category' => 'Работа',
        'completed' => false,
    ],
    [
        'name' => 'Сделать задание первого раздела',
        'date' => '21.12.2019',
        'category' => 'Учеба',
        'completed' => true,
    ],
    [
        'name' => 'Собеседование в IT компании',
        'date' => '22.12.2019',
        'category' => 'Входящие',
        'completed' => false,
    ],
    [
        'name' => 'Купить корм для кота',
        'date' => 'null',
        'category' => 'Домашние дела',
        'completed' => false,
    ],
    [
        'name' => 'Заказать пиццу',
        'date' => 'null',
        'category' => 'Домашние дела',
        'completed' => false,
    ],
];

function count_tasks($tasks, $project)
{
    $tasks_count = 0;
    foreach ($tasks as $task) {
        if ($task['category'] == $project) {
            $tasks_count++;
        }
    }
    return $tasks_count;
};

$page_content = include_template('main.php', ['projects' => $projects, 'tasks' => $tasks, 'show_complete_tasks' => $show_complete_tasks,]);

$layout_content = include_template('layout.php', [
    'content' => $page_content,
    'user_name' => $user_name,
    'title' => 'readme: популярное',
]);

print($layout_content);
