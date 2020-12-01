<?php
/**
 * @var array $lot
 */
?>
<section class="lot-item container">
    <h2><?= htmlspecialchars($lot['title'] ?? 'Без названия') ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="/<?= htmlspecialchars($lot['image_url'] ?? '#') ?>" width="730" height="548"
                     alt="<?= htmlspecialchars($lot['title'] ?? 'Без названия') ?>">
            </div>
            <p class="lot-item__category">Категория:
                <span><?= htmlspecialchars($lot['category_title'] ?? 'Без категории') ?></span></p>
            <p class="lot-item__description"><?= htmlspecialchars($lot['description'] ?? 'Описание отсутствует') ?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <?php $remaining_time = get_date_range($lot['completed_at']) ?>
                <div class="lot-item__timer timer
                            <?php if ($remaining_time[0] == '00'): ?>
                                timer--finishing
                            <?php endif; ?>
                            ">
                    <?php if ($remaining_time[0] == '00' && $remaining_time[1] == '00'): ?>
                        Время лота истекло
                    <?php else: ?>
                        <?= $remaining_time[0] . ':' . $remaining_time[1] ?>
                    <?php endif; ?>
                </div>
                <div class="lot-item__cost-state">
                    <div class="lot-item__rate">
                        <span class="lot-item__amount">Текущая цена</span>
                        <span
                            class="lot-item__cost"><?= htmlspecialchars(formatted_sum($lot['current_price'])) ?? 0 ?></span>
                    </div>
                    <div class="lot-item__min-cost">
                        Мин. ставка
                        <span><?= htmlspecialchars(formatted_sum($lot['current_price'] + $lot['bet_step'])) ?? 0 ?></span>
                    </div>
                </div>
                <form class="lot-item__form" action="https://echo.htmlacademy.ru" method="post" autocomplete="off">
                    <p class="lot-item__form-item form__item form__item--invalid">
                        <label for="cost">Ваша ставка</label>
                        <input id="cost" type="text" name="cost" placeholder="12 000">
                        <span class="form__error">Введите наименование лота</span>
                    </p>
                    <button type="submit" class="button">Сделать ставку</button>
                </form>
            </div>
            <div class="history">
                <h3>История ставок (<span>10</span>)</h3>
                <table class="history__list">
                    <tr class="history__item">
                        <td class="history__name">Test</td>
                        <td class="history__price">10 999 р</td>
                        <td class="history__time">XXX минут назад</td>
                    </tr>
                    <tr class="history__item">
                        <td class="history__name">Test</td>
                        <td class="history__price">10 999 р</td>
                        <td class="history__time">XXX минут назад</td>
                    </tr>
                    <tr class="history__item">
                        <td class="history__name">Test</td>
                        <td class="history__price">10 999 р</td>
                        <td class="history__time">XXX минут назад</td>
                    </tr>

                </table>
            </div>
        </div>
    </div>
</section>




