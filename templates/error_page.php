<?php
/**
 * @var string $error
 * @var string $error_description
 * @var string $error_link
 * @var string $error_link_description
 */
?>
<main>
    <section class="lot-item container">
        <h2><?= $error ?? "" ?></h2>
        <p><?= $error_description ?? "" ?></p>
        <a href="<?= $error_link ?? "" ?>" class="text-link"><?= $error_link_description ?? "" ?></a>
    </section>
</main>
