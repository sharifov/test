<?php

/**
 * @var $form ActiveForm
 * @var $this View
 * @var $lead Lead
 * @var $addEmail EmailCreateForm
 */

use common\models\ClientEmail;
use common\models\Lead;
use sales\forms\lead\EmailCreateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

$user = Yii::$app->user->identity;
$addEmail->client_id = $lead->client_id;
?>

<div class="edit-email-modal-content-ghj">
    <?php $form = ActiveForm::begin([
        'id' => 'client-add-email-form',
        'action' => Url::to(['lead-view/ajax-add-client-email', 'gid' => $lead->gid]),
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validationUrl' => Url::to(['lead-view/ajax-add-client-email-validation', 'gid' => $lead->gid])
    ]); ?>

    <?= $form->errorSummary($addEmail) ?>

    <?php if ($lead->isOwner($user->id) || $user->isAnySupervision() || $user->isAdmin() || $user->isSuperAdmin()) : ?>
        <?=
        $form->field($addEmail, 'email', [
            'template' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>{error}',
            'options' => [
                'class' => 'form-group'
            ]
        ])->textInput([
            'class' => 'form-control email lead-form-input-element',
            'type' => 'email',
            'required' => true
        ])
        ?>
    <?php endif; ?>

    <?=
    $form->field($addEmail, 'type')->dropDownList(ClientEmail::getEmailTypeList())
    ?>
    <div class="text-center">
        <?= Html::submitButton('<i class="fa fa-plus"></i> Add Email', [
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
                $('#client-manage-email').html(data.html);
                $('#modal-client-manage-info').modal('hide');
                
                new PNotify({
                    title: 'Email successfully added',
                    text: data.message,
                    type: 'success'
                });
            }
       },
       error: function (error) {
            if(error.status == 403) {
                new PNotify({
                    title: error.statusText,
                    text: error.responseText,
                    type: 'warning'                
                });
            } else {                
                new PNotify({
                    title: 'Error',
                    text: 'Internal Server Error. Try again letter.',
                    type: 'error'                
                });
            }
       }
    })
    return false;
}); 
JS;
$this->registerJs($js);
?>
