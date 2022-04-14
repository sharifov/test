<?php

/**
 * @var $form ActiveForm
 * @var $this View
 * @var $addEmail EmailCreateForm
 * @var int $chatId
 */

use common\models\ClientEmail;
use src\forms\lead\EmailCreateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

?>

<div class="edit-email-modal-content-ghj">
    <?php $form = ActiveForm::begin([
        'id' => 'client-add-email-form',
        'action' => Url::to(['/client-chat-client-actions/ajax-add-client-email', 'id' => $chatId]),
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validationUrl' => Url::to(['/client-chat-client-actions/ajax-add-client-email-validation', 'id' => $chatId])
    ]); ?>

    <?= $form->errorSummary($addEmail) ?>

    <?= $form->field($addEmail, 'email', [
        'template' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>{error}',
        'options' => [
            'class' => 'form-group'
        ]
    ])->textInput([
        'class' => 'form-control email lead-form-input-element',
        'type' => 'email',
        'required' => true
    ]) ?>

    <?=
    $form->field($addEmail, 'type')->dropDownList(ClientEmail::getEmailTypeList())
    ?>
    <div class="text-center">
        <?= Html::submitButton('<i class="fa fa-plus"> </i> Add Email', [
            'class' => 'btn btn-success'
        ])
?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
$('#client-add-email-form').on('beforeSubmit', function (e) {
    e.preventDefault();
    
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            if (!data.error) {
                 $(document).find('.client-chat-client-info-wrapper').html(data.html);
                $('#modal-client-manage-info').modal('hide');
                
                createNotifyByObject({
                    title: 'Email successfully added',
                    text: data.message,
                    type: 'success'
                });
            }
       },
       error: function (error) {
            createNotifyByObject({
                title: 'Error',
                text: 'Internal Server Error. Try again letter.',
                type: 'error'                
            });
       }
    })
    return false;
}); 
JS;
$this->registerJs($js);
?>
