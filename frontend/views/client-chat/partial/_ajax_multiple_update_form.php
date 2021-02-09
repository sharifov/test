<?php

/***
 * @var \sales\forms\clientChat\MultipleUpdateForm $formMultipleUpdate
 * @var string $alertMessage
 *
 */

use sales\model\clientChat\entity\ClientChat;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;

?>

<?php if (!$alertMessage) : ?>
    <?php Pjax::begin([
    'id' => 'chat-multiple-update',
    'enableReplaceState' => false,
    'enablePushState' => false,
    'timeout' => 5000,
    'clientOptions' => ['async' => false]
    ]); ?>

    <?php $form = ActiveForm::begin([
    'id' => 'cc-submit-multiple-update-form',
    'options' => ['data-pjax' => 1],
    'enableClientValidation' => false
    ]) ?>

    <?php $form->errorSummary($formMultipleUpdate); ?>

    <?= $form->field($formMultipleUpdate, 'chatIds')->hiddenInput()->label(false); ?>

    <?= $form->field($formMultipleUpdate, 'statusId')->dropDownList(ClientChat::getStatusList(), ['prompt' => '---']) ?>

<div class="text-center">
    <?= Html::submitButton('<i class="fa fa-save"></i> Update', ['class' => 'btn btn-sm btn-success', 'id' => 'submit-multiple-update', 'data-pjax' => 1]) ?>
</div>

    <?php ActiveForm::end() ?>

    <?php Pjax::end(); ?>

    <?php

    $js = <<<JS
(function() {
    let btnHtml = '';
    $('#chat-multiple-update').on('pjax:end', function (data, xhr) {
        $('#submit-multiple-update').prop('disabled', false).removeClass('disabled').html(btnHtml);
    });
    $('#chat-multiple-update').on('pjax:beforeSend', function (obj, xhr, data) {
        btnHtml = $('#submit-multiple-update').html();
        $('#submit-multiple-update').addClass('disabled').prop('disabled', true).html('<i class="fa fa-spin fa-spinner"></i>');
    });
})();
JS;
    $this->registerJs($js);
    ?>

<?php else : ?>
    <?= \yii\bootstrap4\Alert::widget([
        'options' => [
            'class' => 'alert-danger',
            'delay' => 4000

        ],
        'body' => $alertMessage,
        'closeButton' => false
    ]) ?>
<?php endif; ?>
