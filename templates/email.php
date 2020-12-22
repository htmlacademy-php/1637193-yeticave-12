<?php
/**
 * @var string $user_name
 * @var string $item_title
 * @var string $host_url
 * @var string $item_link
 */
?>
<h1>Поздравляем с победой</h1>
<p>Здравствуйте, <?= htmlspecialchars($user_name); ?></p>
<p>Ваша ставка для лота <a href="<?= $host_url . $item_link ?>"><?= htmlspecialchars($item_title) ?></a>
    победила.</p>
<p>Перейдите по ссылке <a href="<?= $host_url ?>/my_bets.php">мои ставки</a>,
    чтобы связаться с автором объявления</p>
<small>Интернет Аукцион "YetiCave"</small>


