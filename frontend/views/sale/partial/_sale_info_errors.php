<?php

use yii\helpers\Html;
use yii\helpers\VarDumper;

/**
 * @var $errors array
 */
?>

<?php foreach ($errors as $error) :?>
    <?php if (array_key_exists('messages', $error)) : ?>
        <?php \Yii::info(VarDumper::dumpAsString($error, 20), 'info\_sale_info_errors:notFoundMessagesKey'); ?>
        <?php continue; ?>
    <?php endif ?>
    <ul>
        <li><?= $error['messages'] ?>:</li>
        <ol>
            <?php if (is_array($error['errors'])) : ?>
                <?php foreach ($error['errors'] as $detailError) : ?>
                    <?php if (is_array($detailError)) : ?>
                        <li><?= implode('; ', $detailError) ?></li>
                    <?php else : ?>
                        <li><?= $detailError ?>;</li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else : ?>
                <li><?= $error['errors'] ?></li>
            <?php endif; ?>
        </ol>
    </ul>
<?php endforeach; ?>

