<?php
use yii\helpers\Html;

/**
 * @var $errors array
 */
?>

<?php foreach ($errors as $error):?>
    <ul>
        <li><?= $error['messages'] ?>:</li>
        <ol>
            <?php foreach ($error['errors'] as $detailError): ?>
                <? if(is_array($detailError)): ?>
                    <li><?= implode('; ', $detailError); ?></li>
                <? else: ?>
                    <li><?= $detailError ?>;</li>
                <? endif; ?>
            <?php endforeach; ?>
        </ol>
    </ul>
<?php endforeach; ?>

