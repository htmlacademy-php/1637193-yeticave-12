<?php
/**
 * @var array $pages
 * @var string $search
 * @var int $current_page
 * @var int $pages_count
 * @var string $search_page
 * @var int $category_id
 */
?>
<?php if ($pages_count > 1): ?>
    <?php $this_page = (isset($search_page) && $search_page !== '') ? $search_page : 'search.php' ?>
    <ul class="pagination-list">
        <li class="pagination-item pagination-item-prev">
            <?php if ($current_page > 1): ?>
                <?php $back_page = ($current_page > 1) ? ($current_page - 1) : "1" ?>
                <a href="/<?= htmlspecialchars($this_page,
                    ENT_QUOTES | ENT_HTML5) ?>?<?php if (isset($search) && $search !== ''): ?>search=<?= htmlspecialchars($search,
                    ENT_QUOTES | ENT_HTML5) ?><?php endif; ?><?php if (isset($category_id) && $category_id !== ''): ?>&id=<?= htmlspecialchars($category_id,
                    ENT_QUOTES | ENT_HTML5) ?><?php endif; ?>&page=<?= htmlspecialchars($back_page,
                    ENT_QUOTES | ENT_HTML5) ?>">Назад</a>
            <?php endif; ?>
        </li>
        <?php foreach ($pages as $page): ?>
            <?php if ($page !== '...'): ?>
                <li class="pagination-item <?php if ($page === $current_page): ?>pagination-item-active<?php endif; ?>">
                    <a href="/<?= htmlspecialchars($this_page,
                        ENT_QUOTES | ENT_HTML5) ?>?<?php if (isset($search) && $search !== ''): ?>&search=<?= htmlspecialchars($search,
                        ENT_QUOTES | ENT_HTML5) ?><?php endif; ?><?php if (isset($category_id) && $category_id !== ''): ?>&id=<?= htmlspecialchars($category_id,
                        ENT_QUOTES | ENT_HTML5) ?><?php endif; ?>&page=<?= htmlspecialchars($page,
                        ENT_QUOTES | ENT_HTML5) ?>"><?= htmlspecialchars($page, ENT_QUOTES | ENT_HTML5) ?></a>
                </li>
            <?php else: ?>
                <li class="pagination-item">&#8230;</li>
            <?php endif; ?>
        <?php endforeach; ?>
        <li class="pagination-item pagination-item-next">
            <?php if ($current_page < $pages_count): ?>
                <?php $current_page = ($current_page < $pages_count) ? ($current_page + 1) : $pages_count ?><a
                href="/<?= htmlspecialchars($this_page,
                    ENT_QUOTES | ENT_HTML5) ?>?<?php if (isset($search) && $search !== ''): ?>&search=<?= htmlspecialchars($search,
                    ENT_QUOTES | ENT_HTML5) ?><?php endif; ?><?php if (isset($category_id) && $category_id !== ''): ?>&id=<?= htmlspecialchars($category_id,
                    ENT_QUOTES | ENT_HTML5) ?><?php endif; ?>&page=<?= htmlspecialchars($current_page,
                    ENT_QUOTES | ENT_HTML5) ?>">
                    Вперед</a>
            <?php endif; ?></li>
    </ul>
<?php endif; ?>
