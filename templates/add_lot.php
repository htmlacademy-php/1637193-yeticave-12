<?php
/**
 * @var array $categories
 * @var array $errors
 * @var int $selected_category
 */
?>

<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php foreach ($categories as $category_name): ?>
                <li class="nav__item">
                    <a href="all-lots.html"><?= htmlspecialchars($category_name['title'] ?? ""); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <form class="form form--add-lot container <?= empty($errors) ? "" : "form--invalid" ?> "
          action="/add.php" method="post" enctype="multipart/form-data">
        <h2>Добавление лота</h2>
        <div class="form__container-two">
            <div class="form__item <?= isset($errors['lot-name']) ? "form__item--invalid" : "" ?>">
                <label for="lot-name">Наименование <sup>*</sup></label>
                <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота"
                       value="<?= get_post_value('lot-name'); ?>" class="">
                <span class="form__error"><?= $errors['lot-name'] ?? ""; ?></span>
            </div>
            <div class="form__item <?= isset($errors['category']) ? "form__item--invalid" : "" ?>">
                <label for="category">Категория <sup>*</sup></label>
                <select id="category" name="category">
                    <option value="">Выберите категорию</option>
                    <?php foreach ($categories as $category_name): ?>
                        <option
                            value="<?= $category_name['id']; ?>"
                            <?= ($selected_category == $category_name['id']) ? "selected" : "" ?>
                        ><?= htmlspecialchars($category_name['title'] ?? ""); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="form__error"><?= $errors['category'] ?? "" ?></span>
            </div>
        </div>
        <div class="form__item form__item--wide <?= isset($errors['message']) ? "form__item--invalid" : "" ?>">
            <label for="message">Описание <sup>*</sup></label>
            <textarea id="message" name="message"
                      placeholder="Напишите описание лота"
                      class=""><?= get_post_value('message'); ?></textarea>
            <span class="form__error"><?= $errors['message'] ?? "" ?></span>
        </div>
        <div class="form__item form__item--file <?= !empty($errors['lot-img']) ? "form__item--invalid" : "" ?>">
            <label>Изображение <sup>*</sup></label>
            <div class="form__input-file">
                <input class="visually-hidden" type="file" id="lot-img" value="" name="lot-img">
                <label for="lot-img">
                    Добавить
                </label>
                <span class="form__error"><?= $errors['lot-img'] ?? "" ?></span>
            </div>
        </div>
        <div class="form__container-three">
            <div class="form__item form__item--small <?= isset($errors['lot-rate']) ? "form__item--invalid" : "" ?>">
                <label for="lot-rate">Начальная цена <sup>*</sup></label>
                <input id="lot-rate" type="number" name="lot-rate" placeholder="0"
                       value="<?= get_post_value('lot-rate'); ?>">
                <span class="form__error"><?= $errors['lot-rate'] ?? "" ?></span>
            </div>
            <div class="form__item form__item--small <?= isset($errors['lot-step']) ? "form__item--invalid" : "" ?>">
                <label for="lot-step">Шаг ставки <sup>*</sup></label>
                <input id="lot-step" type="number" name="lot-step" placeholder="0"
                       value="<?= get_post_value('lot-step'); ?>">
                <span class="form__error"><?= $errors['lot-step'] ?? "" ?></span>
            </div>
            <div class="form__item <?= isset($errors['lot-date']) ? "form__item--invalid" : "" ?>">
                <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
                <input class="form__input-date" id="lot-date" type="text" name="lot-date"
                       placeholder="Введите дату в формате ГГГГ-ММ-ДД" value="<?= get_post_value('lot-date'); ?>">
                <span class="form__error"><?= $errors['lot-date'] ?? "" ?></span>
            </div>
        </div>
        <span class="form__error <?php if (!empty($errors)): ?>form__error--bottom<?php endif; ?>">Пожалуйста, исправьте ошибки в форме.</span>
        <button type="submit" class="button">Добавить лот</button>
    </form>
</main>
