<?php
/**
 * @var array $lot
 * @var array $errors
 * @var array $bets
 * @var int $item_id
 * @var int $cost
 * @var int $count_bet
 * @var bool $show_bet_add
 */
?>
<section class="lot-item container">
    <h2><?= htmlspecialchars($lot['title'] ?? 'Без названия') ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="../<?= htmlspecialchars($lot['image_url'] ?? '#') ?>" width="730" height="548"
                     alt="<?= htmlspecialchars($lot['title'] ?? 'Без названия') ?>">
            </div>
            <p class="lot-item__category">Категория:
                <span><?= htmlspecialchars($lot['category_title'] ?? 'Без категории') ?></span></p>
            <p class="lot-item__description"><?= htmlspecialchars($lot['description'] ?? 'Описание отсутствует') ?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <?php $timer_finished = get_date_range($lot['completed_at']) ?? '' ?>
                <?php $remaining_time = get_remaining_time($lot['completed_at']) ?? '' ?>
                <div
                    class="lot-item__timer timer <?php if ($timer_finished[0] === '00'): ?>timer--finishing<?php endif; ?>">
                    <?= $remaining_time ?? '' ?>
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
                <?php if ($show_bet_add) : ?>
                    <form class="lot-item__form" action="/lot.php?id=<?= htmlspecialchars($item_id ?? 0) ?>"
                          method="post" autocomplete="off">
                        <p class="lot-item__form-item <?= isset($errors['cost']) ? 'form__item form__item--invalid' : '' ?>">
                            <label for="cost">Ваша ставка </label>
                            <input id="cost" type="text" name="cost"
                                   placeholder="<?= htmlspecialchars(formatted_sum($lot['current_price'] + $lot['bet_step'])) ?? 0 ?>"
                                   value="<?= htmlspecialchars($cost); ?>">
                            <span class="form__error"><?= $errors['cost'] ?? '' ?></span>
                        </p>
                        <button type="submit" class="button">Сделать ставку</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="history">
                <h3>История ставок <span><?= isset($bets) ? ('(' . $count_bet . ')') : 'пуста' ?></span></h3>
                <table class="history__list">
                    <?php if (isset($bets)): ?>
                        <?php foreach ($bets as $bet): ?>
                            <tr class="history__item">
                                <td class="history__name"><?= htmlspecialchars($bet['username'] ?? 'User'); ?></td>
                                <td class="history__price"><?= htmlspecialchars(formatted_sum($bet['total']) ?? 0); ?></td>
                                <td class="history__time"><?= get_correct_bet_time($bet['date'] ?? 0); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</section>




