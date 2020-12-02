<?php
/**
 * @var array $errors
 * @var array $form
 */
?>
<form class="form container <?= empty($errors) ? "" : "form--invalid" ?>" action="/enter.php" method="post">
      <h2>Вход</h2>
        <?php $classname = isset($errors['email']) ? "form__item--invalid" : "";
        $value = isset($form['email']) ? $form['email'] : ""; ?>
      <div class="form__item <?= $classname ?>">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?= $value ?>">
          <?php if ($classname): ?>
        <span class="form__error"><?= $errors['email'] ?></span>
          <?php endif; ?>
      </div>
    <?php $classname = isset($errors['password']) ? "form__item--invalid" : "";
    $value = isset($form['password']) ? $form['password'] : ""; ?>
      <div class="form__item form__item--last <?= $classname ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?= $value ?>">
          <?php if ($classname): ?>
        <span class="form__error"><?= $errors['password']; ?></span>
          <?php endif; ?>
      </div>
      <button type="submit" class="button">Войти</button>
    </form>
