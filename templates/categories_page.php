<?php
/**
 * @var array $category_items
 * @var string $pagination
 * @var string $category_name
 */
?>
<div class="container">
    <section class="lots">
        <?php if (!empty($category_items)): ?>
        <h2><span><?= htmlspecialchars($category_name, ENT_QUOTES | ENT_HTML5) ?? '' ?></span></h2>
        <ul class="lots__list">
            <?php foreach ($category_items as $item): ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="../<?= htmlspecialchars($item['image_url'] ?? '#', ENT_QUOTES | ENT_HTML5) ?>"
                             width="350" height="260"
                             alt="<?= htmlspecialchars($item['item_title'] ?? 'Без названия',
                                 ENT_QUOTES | ENT_HTML5) ?>">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category"><?= htmlspecialchars($item['category'] ?? 'Без категории',
                                ENT_QUOTES | ENT_HTML5) ?></span>
                        <h3 class="lot__title"><a class="text-link"
                                                  href="/lot.php?id=<?= htmlspecialchars($item['id'] ?? 0,
                                                      ENT_QUOTES | ENT_HTML5) ?>"><?= htmlspecialchars($item['item_title'] ?? 'Без названия',
                                    ENT_QUOTES | ENT_HTML5) ?></a>
                        </h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount">Стартовая цена</span>
                                <span
                                    class="lot__cost"><?= htmlspecialchars(formatted_sum($item['total'] ?? 0),
                                        ENT_QUOTES | ENT_HTML5) ?></span>
                            </div>
                            <?php $timer_finished = get_date_range($item['completed_at']) ?? '' ?>
                            <?php $remaining_time = get_remaining_time($item['completed_at']) ?? '' ?>
                            <div
                                class="lot__timer timer <?php if ($timer_finished[0] === '00'): ?>timer--finishing<?php endif; ?>">
                                <?= $remaining_time ?? '' ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
            <?php else: ?>
                <p>Лотов из данной категории еще нет на сайте.</p>
            <?php endif; ?>
        </ul>
    </section>
    <?php if (isset($pagination) && !($pagination === '')): ?>
        <?= $pagination ?>
    <?php endif; ?>
</div>
