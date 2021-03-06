<?php
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$name = $_POST['name'] ?? '';
?>

<section class="content__side">
  <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

  <a class="button button--transparent content__side-button" href="form-authorization.html">Войти</a>
</section>

<main class="content__main">
  <h2 class="content__main-heading">Регистрация аккаунта</h2>

  <form class="form" action="register.php" method="post" autocomplete="off">
    <div class="form__row">
      <label class="form__label" for="email">E-mail <sup>*</sup></label>

      <input class="form__input <?= isset($errors['email']) && is_array($errors) ? 'form__input--error' : '' ?>" type="text" name="email" id="email" value="<?= htmlspecialchars($email) ?>" placeholder="Введите e-mail">
      <?= isset($errors['email']) && is_array($errors) ? '<p class="form__message">' . $errors['email'] . '</p>' : '' ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="password">Пароль <sup>*</sup></label>

      <input class="form__input <?= isset($errors['password']) && is_array($errors) ? 'form__input--error' : '' ?>" type="password" name="password" id="password" value="<?= htmlspecialchars($password) ?>" placeholder="Введите пароль">
      <?= isset($errors['password']) && is_array($errors) ? '<p class="form__message">' . $errors['password'] . '</p>' : '' ?>
    </div>

    <div class="form__row">
      <label class="form__label" for="name">Имя <sup>*</sup></label>

      <input class="form__input <?= isset($errors['name']) && is_array($errors) ? 'form__input--error' : '' ?>" type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" placeholder="Введите имя">
      <?= isset($errors['name']) && is_array($errors) ? '<p class="form__message">' . $errors['name'] . '</p>' : '' ?>
    </div>

    <div class="form__row form__row--controls">
      <?= $errors ? '<p class="error-message">Пожалуйста, исправьте ошибки в форме</p>' : '' ?>

      <input class="button" type="submit" name="" value="Зарегистрироваться">
    </div>
  </form>
</main>