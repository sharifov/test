<?php

use sales\model\clientChat\useCase\sendOffer\GenerateImagesForm;
use yii\bootstrap4\Html;

/** @var string $errorMessage */
/** @var GenerateImagesForm $form */
/** @var array $captures */

?>
<?php if ($form->getErrors()): ?>
<h4><?= Html::errorSummary($form, ['encode' => true]) ?></h4>
    <?= Html::button('Back', ['class' => 'btn btn-info chat-offer', 'data-chat-id' => $form->chatId, 'data-lead-id' => $form->leadId])?>
<?php elseif ($errorMessage): ?>
    <h3><?= $errorMessage ?></h3>
    <?= Html::button('Back', ['class' => 'btn btn-info chat-offer', 'data-chat-id' => $form->chatId, 'data-lead-id' => $form->leadId])?>
<?php else: ?>
    <?php foreach ($captures as $capture): ?>
        <?= Html::a($capture['checkoutUrl'], $capture['checkoutUrl'], ['target' => 'blank']) ?> <br><br>
        <?= Html::img($capture['img']) ?> <br><br>
        <hr>
    <?php endforeach;?>
    <br>
    <?= Html::button('Back', ['class' => 'btn btn-info chat-offer', 'data-chat-id' => $form->chatId, 'data-lead-id' => $form->leadId])?>
    <?= Html::button('Send', ['class' => 'btn btn-success client-chat-send-offer', 'data-chat-id' => $form->chatId, 'data-lead-id' => $form->leadId])?>
<?php endif; ?>
