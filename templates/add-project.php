<?php
require_once('helpers.php');
$project_name = $_POST['project_name'] ?? '';
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

  <a class="button button--transparent button--plus content__side-button" href="add-project.php">Добавить проект</a>
</section>

<main class="content__main">
  <h2 class="content__main-heading">Добавление проекта</h2>

  <form class="form" action="add-project.php" method="post" autocomplete="off">
    <div class="form__row">
      <label class="form__label" for="project_name">Название <sup>*</sup></label>

      <input class="form__input" type="text" name="project_name" id="project_name" value="<?= $project_name ?>" placeholder="Введите название проекта">
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" name="" value="Добавить">
    </div>
  </form>
</main>