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
            <?php if(is_array($error['errors'])): ?>
                <?php foreach ($error['errors'] as $detailError): ?>
                    <?php if(is_array($detailError)): ?>
                        <li><?= implode('; ', $detailError) ?></li>
                    <?php else: ?>
                        <li><?= $detailError ?>;</li>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <li><?= $error['errors'] ?></li>
            <?php endif; ?>
        </ol>
    </ul>
<?php endforeach; ?>

