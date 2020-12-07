<?php
/**
 * @var array $ad_information
 */
?>
<div class="container">
    <section class="lots">
        <h2>Результаты поиска по запросу «<span>Union</span>»</h2>
        <ul class="lots__list">
            <?php if (isset($ad_information)): ?>
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
                                <div class="lot__timer timer<?php if ($remaining_time[0] == '00'): ?>timer--finishing<?php endif; ?>">
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
        <!--        <ul class="lots__list">-->
        <!--            <li class="lots__item lot">-->
        <!--                <div class="lot__image">-->
        <!--                    <img src="../img/lot-1.jpg" width="350" height="260" alt="Сноуборд">-->
        <!--                </div>-->
        <!--                <div class="lot__info">-->
        <!--                    <span class="lot__category">Доски и лыжи</span>-->
        <!--                    <h3 class="lot__title"><a class="text-link" href="lot.html">2014 Rossignol District Snowboard</a>-->
        <!--                    </h3>-->
        <!--                    <div class="lot__state">-->
        <!--                        <div class="lot__rate">-->
        <!--                            <span class="lot__amount">Стартовая цена</span>-->
        <!--                            <span class="lot__cost">10 999<b class="rub">р</b></span>-->
        <!--                        </div>-->
        <!--                        <div class="lot__timer timer">-->
        <!--                            16:54:12-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </li>-->
        <!--        </ul>-->
    </section>
    <?php if ($pages_count > 1): ?>
        <ul class="pagination-list">
            <?php foreach ($pages as $page): ?>
                <li class="pagination-item pagination-item-prev"><a>Назад</a></li>
                <li class="pagination-item pagination-item-active"><a>1</a></li>
                <li class="pagination-item"><a href="#">2</a></li>
                <li class="pagination-item"><a href="#">3</a></li>
                <li class="pagination-item"><a href="#">4</a></li>
                <li class="pagination-item pagination-item-next"><a href="#">Вперед</a></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
