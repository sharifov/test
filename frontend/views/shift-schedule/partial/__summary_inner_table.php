<?php
/** @var string $data */
/** @var bool $thead */
?>
<table class="inner-summary-table">
    <?php if ($thead === true) : ?>
    <thead>
    <?php else : ?>
    <tbody>
    <?php endif; ?>
        <tr>
            <?= $data ?>
        </tr>
    <?php if ($thead === true) : ?>
    <thead>
    <?php else : ?>
    </tbody>
    <?php endif; ?>
</table>