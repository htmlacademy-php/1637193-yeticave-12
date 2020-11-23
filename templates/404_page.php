<?php
/**
 * @var array $categories
 * @var string $error
 */
?>
<main>
    <nav class="nav">
        <ul class="nav__list container">
            <?php foreach ($categories as $category_name): ?>
                <li class="nav__item">
                    <a href="pages/all-lots.html"><?= htmlspecialchars($category_name['title'] ?? ""); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <section class="lot-item container">
        <h2><?= $error; ?></h2>
        <p>Данной страницы не существует на сайте.</p>
        <a href="/index.php">Предлагаем вернуться на главную</a>
    </section>
</main>

