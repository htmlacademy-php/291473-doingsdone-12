<?php
require_once('helpers.php');

$name = $_POST['name'] ?? '';
$project = $_POST['project'] ?? '';
$date = $_POST['date'] ?? '';
$file = $_FILE['file'] ?? '';
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
  <h2 class="content__main-heading">Добавление задачи</h2>

  <form class="form" action="add-task.php" method="post" enctype="multipart/form-data" autocomplete="off">
    <div class="form__row">
      <label class="form__label" for="name">Название <sup>*</sup></label>

      <input class="form__input <?= $errors['name'] ? 'form__input--error' : '' ?>" type="text" name="name" id="name" value="<?= $name ?>" placeholder="Введите название">
      <?= isset($errors['name']) ? '<p class="form__message">Название обязательно для заполнения</p>' : '' ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="project">Проект <sup>*</sup></label>

      <select class="form__input form__input--select <?= isset($errors['project']) ? 'form__input--error' : '' ?>" name="project" id="project">
        <?php foreach ($projects as $project) : ?>
          <option value="<?= $project['id'] ?>"><?= $project['project_name'] ?></option>
        <?php endforeach; ?>
      </select>
      <?= isset($errors['project']) ? '<p class="form__message">' . $errors['project'] . '</p>' : '' ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="date">Дата выполнения</label>

      <input class="form__input form__input--date <?= $errors['date'] ? 'form__input--error' : '' ?>" type="text" name="date" id="date" value="<?= $date ?>" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
      <?= isset($errors['date']) ? '<p class="form__message">Дата должна быть больше или равна текущей</p>' : '' ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="file">Файл</label>

      <div class="form__input-file">
        <input class="visually-hidden" type="file" name="file" id="file" value="<?= $file ?>">

        <label class="button button--transparent" for="file">
          <span>Выберите файл</span>
        </label>
      </div>
    </div>

    <div class="form__row form__row--controls">
      <input class="button" type="submit" name="" value="Добавить">
    </div>
  </form>
</main>