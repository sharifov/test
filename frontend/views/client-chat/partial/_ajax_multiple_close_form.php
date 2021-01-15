<?php

/***
 * @var \sales\forms\clientChat\MultipleCloseForm $formMultipleClose
 * @var string $alertMessage
 *
 */

use sales\auth\Auth;
use sales\model\clientChat\entity\ClientChat;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
?>

<?php if (!$alertMessage) : ?>
    <?php Pjax::begin([
        'id' => 'chat-multiple-close',
        'enableReplaceState' => false,
        'enablePushState' => false,
        'timeout' => 5000,
        'clientOptions' => ['async' => false]
    ]); ?>

        <?php $form = ActiveForm::begin([
            'id' => 'cc-submit-multiple-close-form',
            'options' => ['data-pjax' => 1],
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'validateOnChange' => false,
            'validateOnBlur' => false,
            'validationUrl' => Url::to(['client-chat/validate-multiple-close']),
        ]) ?>

            <?php $form->errorSummary($formMultipleClose); ?>

            <?= $form->field($formMultipleClose, 'chatIds')->hiddenInput()->label(false); ?>

            <label class="control-label">Please confirm closing chats:</label>

            <?php echo $form->field($formMultipleClose, 'toArchive')->checkbox()->label(false) ?>

            <div class="text-center">
                <?= Html::submitButton('<i class="fa fa-save"></i> Close', ['class' => 'btn btn-sm btn-success', 'id' => 'submit-multiple-close', 'data-pjax' => 1]) ?>
            </div>

        <?php ActiveForm::end() ?>
    <?php Pjax::end() ?>

    <?php $js = <<<JS
        (function() {
            let btnHtml = '';
            $('#chat-multiple-close').on('pjax:end', function (data, xhr) {
                $('#submit-multiple-close').prop('disabled', false).removeClass('disabled').html(btnHtml);
            });
            $('#chat-multiple-close').on('pjax:beforeSend', function (obj, xhr, data) {
                btnHtml = $('#submit-multiple-close').html();
                $('#submit-multiple-close').addClass('disabled').prop('disabled', true).html('<i class="fa fa-spin fa-spinner"></i>');
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

