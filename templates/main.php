<?php
/**
 * @var array $categories
 * @var array $ad_information
 */
?>
<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное
        снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $category_name): ?>
            <li class="promo__item promo__item--<?= htmlspecialchars($category_name['symbolic_code']) ?>">
                <a class="promo__link"
                   href="/pages/all-lots.html"><?= htmlspecialchars($category_name['title']); ?></a>
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
            <?php $remaining_time = get_date_range($ad_value['completed_at']) ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= htmlspecialchars($ad_value['image_url']) ?>" width="350" height="260"
                         alt="<?= htmlspecialchars($ad_value['title']) ?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= htmlspecialchars($ad_value['category_title']); ?></span>
                    <h3 class="lot__title"><a class="text-link"
                                              href="/lot.php?id=<?= htmlspecialchars($ad_value['id']) ?>"><?= htmlspecialchars($ad_value['title']) ?></a>
                    </h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span
                                class="lot__cost"><?= htmlspecialchars(formatted_sum($ad_value['total'])) ?></span>
                        </div>
                        <div class="lot__timer timer
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
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<?= $pagination; ?>
