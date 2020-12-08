<?php
/**
 * @var array $ad_information
 * @var array $pages
 * @var string $search
 * @var int $current_page
 * @var int $pages_count
 *
 */
?>
<div class="container">
    <section class="lots">
        <h2>Результаты поиска по запросу «<span><?= htmlspecialchars($search) ?></span>»</h2>
        <ul class="lots__list">
            <?php if (!empty($ad_information)): ?>
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
                                <div
                                    class="lot__timer timer<?php if ($remaining_time[0] == '00'): ?>timer--finishing<?php endif; ?>">
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
            <?php else: ?>
                <p>Ничего не найдено по вашему запросу</p>
            <?php endif; ?>
        </ul>
    </section>
    <?php if ($pages_count > 0): ?>
        <ul class="pagination-list">
            <li class="pagination-item pagination-item-prev">
                <a a href="/search.php?search=<?= $search; ?>&find=Найти&page=<?php if ($current_page > 1) {
                                                                    echo $back_page = $current_page - 1;
                                                                } else {
                                                                    echo $back_page = 1;
                                                                } ?>>Назад</a></li>
                <?php foreach ($pages as $page): ?>
                    <li class=" pagination-item <?php if ($page === $current_page): ?>pagination-item-active<?php endif; ?>">
                <a href="/search.php?search=<?= $search; ?>&find=Найти&page=<?= $page; ?>"><?= $page; ?></a>
            </li>
            <?php endforeach; ?>
            <li class="pagination-item pagination-item-next"><a href="/search.php?search=<?= $search; ?>&find=Найти&page=<?php if ($current_page < $pages_count) {
                        echo $current_page += 1;
                    } else {
                        echo $current_page = $pages_count;
                    } ?>">Вперед</a></li>
        </ul>
    <?php endif; ?>
</div>
