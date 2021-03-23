<?php

use sales\model\clientChat\useCase\sendOffer\GenerateImagesForm;
use yii\bootstrap4\Html;
use yii\web\View;

/** @var View $this */
/** @var string $errorMessage */
/** @var GenerateImagesForm $form */
/** @var array $captures */

?>
<div class="send-offer-container">
    <?php if ($form->getErrors()) : ?>
        <h4><?= Html::errorSummary($form, ['encode' => true]) ?></h4>
        <?= Html::button('Back', ['class' => 'btn btn-info chat-offer', 'data-chat-id' => $form->chatId, 'data-lead-id' => $form->leadId]) ?>
    <?php elseif ($errorMessage) : ?>
        <h3><?= $errorMessage ?></h3>
        <?= Html::button('Back', ['class' => 'btn btn-info chat-offer', 'data-chat-id' => $form->chatId, 'data-lead-id' => $form->leadId]) ?>
    <?php else : ?>
        <?php foreach ($captures as $key => $capture) : ?>
            <div>
                <?php if ($key !== 0) : ?>
                    <button class="fa fa-arrow-up btn btn-success btn_move_offer" data-type="up" data-capture-key="<?= $key ?>"
                            data-chat-id="<?= $form->chatId ?>" data-lead-id="<?= $form->leadId ?>"> </button>
                <?php endif; ?>
                <?php if (count($captures) !== ($key + 1)) : ?>
                    <button class="fa fa-arrow-down btn btn-success btn_move_offer" data-type="down" data-capture-key="<?= $key ?>"
                            data-chat-id="<?= $form->chatId ?>" data-lead-id="<?= $form->leadId ?>"> </button>
                <?php endif; ?>
            </div>
            <div>
                <?= Html::a($capture['checkoutUrl'], $capture['checkoutUrl'], ['target' => 'blank']) ?> <br><br>
                <?= Html::img($capture['img']) ?> <br><br>
            </div>
            <hr>
        <?php endforeach; ?>
        <br>
        <?= Html::button('Back', ['class' => 'btn btn-info chat-offer', 'data-chat-id' => $form->chatId, 'data-lead-id' => $form->leadId]) ?>
        <?= Html::button('Send', ['class' => 'btn btn-success client-chat-send-offer', 'data-chat-id' => $form->chatId, 'data-lead-id' => $form->leadId]) ?>
    <?php endif; ?>
</div>
