<?php
/**
 * @var array $categories
 * @var array $errors
 * @var array $form
 * @var array $values
 */
?>


<form class="form container <?= empty($errors) ? "" : "form--invalid" ?>" action="/sign-up.php" method="post"
      autocomplete="off">
    <h2>Регистрация нового аккаунта</h2>
    <?php $classname = isset($errors['email']) ? "form__item--invalid" : "";
    $value = isset($form['email']) ? $form['email'] : ""; ?>
    <div class="form__item <?= $classname; ?>"> <!-- form__item--invalid -->
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail" class="<?= $classname; ?>"
               maxlength="100"
               value="<?= $values['email'] ?? ''; ?>">
        <span class="form__error"><?= $classname ? $errors['email'] : "" ?> </span>
    </div>
    <?php $classname = isset($errors['password']) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?= $classname; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль" class="<?= $classname; ?>"
               maxlength="45">
        <?php if ($classname): ?>
            <span class="form__error"><?= $classname ? $errors['password'] : "" ?></span>
        <?php endif; ?>
    </div>
    <?php $classname = isset($errors['name']) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?= $classname; ?>">
        <label for="name">Имя <sup>*</sup></label>
        <input id="name" type="text" name="name" placeholder="Введите имя"
               class="<?= $classname; ?>" value="<?= $values['name'] ?? ''; ?>" maxlength="45">
        <span class="form__error"><?= $classname ? $errors['name'] : "" ?></span>
    </div>
    <?php $classname = isset($errors['message']) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?= $classname; ?>">
        <label for="message">Контактные данные <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться" class="<?= $classname; ?>"
                  maxlength="255"><?= $values['message'] ?? ''; ?></textarea>
        <?php if ($classname): ?>

            <span class="form__error"><?= $classname ? $errors['message'] : "" ?></span>
        <?php endif; ?>
    </div>
    <?php if (!empty($errors)): ?>
        <div class="form--invalid">
            <span class="form__error <?php if (!empty($errors)): ?>form__error--bottom<?php endif; ?>">Пожалуйста, исправьте следующие ошибки:</span>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <br>
    <?php endif; ?>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="/enter.php">Уже есть аккаунт</a>
</form>

