<?php

use sales\model\clientChat\useCase\sendOffer\GenerateImagesForm;
use yii\bootstrap4\Html;
use yii\helpers\VarDumper;

/** @var string $errorMessage */
/** @var GenerateImagesForm $form */
/** @var array $captures */

?>
<?php if ($form->getErrors()): ?>
<h4><?= Html::errorSummary($form, ['encode' => true]) ?></h4>
    <?= Html::button('Back', ['class' => 'btn btn-info chat-offer', 'data-cch-id' => $form->cchId])?>
<?php elseif ($errorMessage): ?>
    <h3><?= $errorMessage ?></h3>
    <?= Html::button('Back', ['class' => 'btn btn-info chat-offer', 'data-cch-id' => $form->cchId])?>
<?php else: ?>
    <?php foreach ($captures as $capture): ?>
        <?= Html::img($capture['img']) ?> <br><br>
        <?= $capture['checkoutUrl'] ?> <br><br>
    <?php endforeach;?>
    <br>
    <?= Html::button('Back', ['class' => 'btn btn-info chat-offer', 'data-cch-id' => $form->cchId])?>
    <?= Html::button('Send', ['class' => 'btn btn-success client-chat-send-offer', 'data-cch-id' => $form->cchId])?>
<?php endif; ?>
