<?php
/**
 * @var array $categories
 * @var array $ad_information
 * @var string $pagination
 */
?>
<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное
        снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $category_name): ?>
            <li class="promo__item promo__item--<?= htmlspecialchars($category_name['symbolic_code'] ?? '',
                ENT_QUOTES | ENT_HTML5) ?>">
                <a class="promo__link"
                   href="/categories.php?id=<?= $category_name['id'] ?? 0 ?>"><?= htmlspecialchars($category_name['title'] ?? 'Название категории',
                        ENT_QUOTES | ENT_HTML5) ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <?php foreach ($ad_information as $ad_value): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="../<?= htmlspecialchars($ad_value['image_url'] ?? '#', ENT_QUOTES | ENT_HTML5) ?>" width="350" height="260"
                         alt="<?= htmlspecialchars($ad_value['title'] ?? 'Без названия', ENT_QUOTES | ENT_HTML5) ?>">
                </div>
                <div class="lot__info">
                    <span
                        class="lot__category"><?= htmlspecialchars($ad_value['category_title'] ?? 'Без категории',
                            ENT_QUOTES | ENT_HTML5) ?></span>
                    <h3 class="lot__title"><a class="text-link"
                                              href="/lot.php?id=<?= htmlspecialchars($ad_value['id'],
                                                  ENT_QUOTES | ENT_HTML5) ?>"><?= htmlspecialchars($ad_value['title'],
                                ENT_QUOTES | ENT_HTML5) ?></a>
                    </h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span
                                class="lot__cost"><?= htmlspecialchars(formatted_sum($ad_value['total']),
                                    ENT_QUOTES | ENT_HTML5) ?></span>
                        </div>
                        <?php $timer_finished = get_date_range($ad_value['completed_at']) ?? '' ?>
                        <?php $remaining_time = get_remaining_time($ad_value['completed_at']) ?? '' ?>
                        <div
                            class="lot__timer timer <?php if ($timer_finished[0] === '00'): ?>timer--finishing<?php endif; ?>">
                            <?= $remaining_time ?? '' ?>
                        </div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<?php if (isset($pagination) && !($pagination === '')): ?>
    <?= $pagination ?>
<?php endif; ?>
