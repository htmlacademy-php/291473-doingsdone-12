<?php
require_once('helpers.php');
?>
<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $project) : ?>
                <li class="main-navigation__list-item <?= $project['id'] == $project_id ? 'main-navigation__list-item--active' : '' ?>">
                    <a class="main-navigation__list-item-link" href="?project-id=<?= $project['id'] ?>"><?= htmlspecialchars($project['project_name']) ?></a>
                    <span class="main-navigation__list-item-count"><?= get_tasks_count($tasks, $project['project_name']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="add-project.php" target="project_add">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="get" autocomplete="off">
        <input class="search-form__input" type="text" name="search" value="<?= $search ?>" placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
            <a href="/" class="tasks-switch__item">Повестка дня</a>
            <a href="/" class="tasks-switch__item">Завтра</a>
            <a href="/" class="tasks-switch__item">Просроченные</a>
        </nav>

        <label class="checkbox">
            <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?= $show_complete_tasks == 1 ? 'checked' : '' ?>>
            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>

    <?php if (!$project_tasks) : ?>
        <p>Ничего не найдено по вашему запросу</p>
    <?php else : ?>
        <table class="tasks">
            <?php foreach ($project_tasks as $task_number => $task) : ?>
                <?php
                if ($task['status'] && $show_complete_tasks == 1) {
                    continue;
                }
                ?>
                <tr class="tasks__item task <?= $task['status'] ? 'task--completed' : '' ?> <?= get_task_time($task) ?>">
                    <td class="task__select">
                        <label class="checkbox task__checkbox">
                            <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="1">
                            <span class="checkbox__text"><?= htmlspecialchars($task['task_name']) ?></span>
                        </label>
                    </td>

                    <td class="task__file">
                        <?php if ($task['file_link']) : ?>
                            <a class="download-link" href="<?= $task['file_link'] ?>"><?= $task['file_link'] ?></a>
                        <?php endif; ?>
                    </td>

                    <td class="task__date"><?= $task['deadline'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</main>