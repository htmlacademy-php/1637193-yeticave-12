<?php
?>
<section class="rates container">
    <h2>Мои ставки</h2>
    <?php if (!empty($bets)): ?>
        <table class="rates__list">
            <?php foreach ($bets as $bet): ?>
                <tr class="rates__item
                <?php if (isset($bet['winner'])) : ?>
                    rates__item--win
                <?php elseif (isset($bet['lot_ended'])) : ?>
                    rates__item--end
                <?php endif; ?>">
                    <td class="rates__info">
                        <div class="rates__img">
                            <img src="../<?= htmlspecialchars($bet['image_url']) ?>" width="54" height="40"
                                 alt="<?= htmlspecialchars($bet['title']) ?>">
                        </div>
                        <div>
                            <h3 class="rates__title"><a
                                    href="/lot.php?id=<?= htmlspecialchars($bet['item_id']); ?>">
                                    <?= htmlspecialchars($bet['title']) ?></a>
                            </h3>
                            <?php if (isset($bet['winner']) && $bet['winner']): ?>
                                <p><?= htmlspecialchars($bid['contacts'] ?? 'Контактов не оставили.') ?></p>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="rates__category">
                        <?= htmlspecialchars($bet['category']) ?>
                    </td>
                    <?php if (isset($bet['winner']) && $bet['winner']): ?>
                        <td class="rates__timer">
                            <div class="timer timer--win">Ставка выиграла</div>
                        </td>
                    <?php elseif (isset($bet['lot_ended']) && $bet['lot_ended']): ?>
                        <td class="rates__timer">
                            <div class="timer timer--end">Торги окончены</div>
                        </td>
                    <?php else: ?>
                        <td class="rates__timer">
                            <div
                                class="timer <?php if (isset($bet['remaining_time'][0]) && $bet['remaining_time'][0] === '00'): ?>
                    timer--finishing
                    <?php endif; ?>">
                                <?= (isset($bet['remaining_time'][0]) && isset($bet['remaining_time'][1])) ? $bet['remaining_time'][0] . ":" . $bet['remaining_time'][1] : 'Некорректное время' ?>
                            </div>
                        </td>
                    <?php endif; ?>
                    <td class="rates__price">
                        <?= htmlspecialchars(formatted_sum($bet['current_price'])); ?>
                    </td>
                    <td class="rates__time">
                        <?= get_correct_bet_time($bet['bet_date']); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Ставок еще не было.</p>
    <?php endif; ?>
</section>
