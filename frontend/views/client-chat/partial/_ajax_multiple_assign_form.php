<?php

/***
 * @var \sales\forms\clientChat\MultipleAssignForm $formMultipleAssign
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
        'id' => 'chat-multiple-assign',
        'enableReplaceState' => false,
        'enablePushState' => false,
        'timeout' => 5000,
        'clientOptions' => ['async' => false]
    ]); ?>

        <?php $form = ActiveForm::begin([
            'id' => 'cc-submit-multiple-assign-form',
            'options' => ['data-pjax' => 1],
            'enableClientValidation' => false,
            'enableAjaxValidation' => true,
            'validateOnChange' => false,
            'validateOnBlur' => false,
            'validationUrl' => Url::to(['client-chat/validate-multiple-assign']),
        ]) ?>

            <?php $form->errorSummary($formMultipleAssign); ?>

            <?= $form->field($formMultipleAssign, 'chatIds')->hiddenInput()->label(false) ?>

            <?php if (Auth::can('client-chat/multiple/assign/manage')) : ?>
                <label class="control-label">Assign Chats</label>
                <?php echo $form->field($formMultipleAssign, 'assignUserId')
                    ->dropDownList($formMultipleAssign->getCommonUsers(), ['prompt' => '---'])
                    ->label(false)
                ?>
            <?php endif ?>

            <div class="text-center">
                <?= Html::submitButton('<i class="fa fa-save"></i> Assign', ['class' => 'btn btn-sm btn-success', 'id' => 'submit-multiple-assign', 'data-pjax' => 1]) ?>
            </div>

        <?php ActiveForm::end() ?>
    <?php Pjax::end() ?>

    <?php $js = <<<JS
        (function() {
            let btnHtml = '';
            $('#chat-multiple-assign').on('pjax:end', function (data, xhr) {
                $('#submit-multiple-assign').prop('disabled', false).removeClass('disabled').html(btnHtml);
            });
            $('#chat-multiple-assign').on('pjax:beforeSend', function (obj, xhr, data) {
                btnHtml = $('#submit-multiple-assign').html();
                $('#submit-multiple-assign').addClass('disabled').prop('disabled', true).html('<i class="fa fa-spin fa-spinner"></i>');
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

