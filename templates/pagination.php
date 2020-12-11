<?php
/**
 * @var array $pages
 * @var string $search
 * @var int $current_page
 * @var int $pages_count
 */
?>
<?php if ($pages_count > 1): ?>
    <ul class="pagination-list">
        <li class="pagination-item pagination-item-prev">
            <?php if ($current_page > 1): ?>
                <?php $back_page = ($current_page > 1) ? ($current_page - 1) : "1" ?>
                <a href="/search.php?search=<?= htmlspecialchars($search); ?>&page=<?= $back_page; ?>">Назад</a>
            <?php endif; ?>
        </li>
        <?php foreach ($pages as $num_page => $page): ?>
            <?php $ceparator_link_before = ($current_page > 1) ? ($current_page - 4) : "1";
            $ceparator_link_after = ($current_page < $pages_count) ? ($current_page + 4) : $pages_count;
            $ceparator_link = ($page < $current_page) ? $ceparator_link_before : $ceparator_link_after;
            $page_link = ($page != '...') ? $page : $ceparator_link; ?>
            <li class="pagination-item <?php if ($page == $current_page): ?>pagination-item-active<?php endif; ?>">
                <a href="/search.php?search=<?= htmlspecialchars($search); ?>&page=<?= $page_link; ?>"><?= $page; ?></a>
            </li>
        <?php endforeach; ?>
        <li class="pagination-item pagination-item-next">
            <?php if ($current_page < $pages_count): ?>
                <?php $current_page = ($current_page < $pages_count) ? ($current_page + 1) : $pages_count ?>
                <a href="/search.php?search=<?= htmlspecialchars($search); ?>&page=<?= $current_page ?>">Вперед</a>
            <?php endif; ?></li>
    </ul>
<?php endif; ?>
