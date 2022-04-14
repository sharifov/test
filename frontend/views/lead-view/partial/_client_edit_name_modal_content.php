<?php

/**
 * @var $form ActiveForm
 * @var $this View
 * @var $editName \src\services\client\ClientCreateForm
 * @var $lead Lead
 */

use common\models\Language;
use common\models\Lead;
use src\auth\Auth;
use src\services\client\ClientCreateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use modules\lead\src\abac\LeadAbacObject;
use modules\lead\src\abac\dto\LeadAbacDto;

?>

<div class="edit-name-modal-content-ghj">
    <?php $form = ActiveForm::begin([
        'id' => 'client-edit-name-form',
        'action' => Url::to(['lead-view/ajax-edit-client-name', 'gid' => $lead->gid]),
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validationUrl' => Url::to(['lead-view/ajax-edit-client-name-validation', 'gid' => $lead->gid])
    ]);
?>

    <?= $form->errorSummary($editName); ?>

    <?= $form->field($editName, 'firstName')->textInput(['required' => true]) ?>

    <?= $form->field($editName, 'lastName')->textInput() ?>

    <?= $form->field($editName, 'middleName')->textInput() ?>

    <?php
    $leadAbacDto = new LeadAbacDto($lead, Auth::id());
    $leadAbacDto->formAttribute = 'locale';
    $leadAbacDto->formMultiAttribute[0] = 'locale';
    $leadAbacDto->isNewRecord = false;
    /** @abac $leadAbacDto, LeadAbacObject::CLIENT_CREATE_FORM, LeadAbacObject::ACTION_VIEW, Locale field view*/
    $view = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::CLIENT_CREATE_FORM, LeadAbacObject::ACTION_VIEW);
    /** @abac $leadAbacDto, LeadAbacObject::CLIENT_CREATE_FORM, LeadAbacObject::ACTION_EDIT, Locale field edit*/
    $edit = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::CLIENT_CREATE_FORM, LeadAbacObject::ACTION_EDIT);
    ?>

        <?= $form->field($editName, 'locale', [
            'options' => [
                'class' => 'form-group',
                'hidden' => ($edit ? !$edit : !$view),
            ],
        ])->dropDownList(Language::getLocaleList(false), ['prompt' => '-', 'disabled' => !$edit]) ?>

    <?php
    $leadAbacDto = new LeadAbacDto($lead, Auth::id());
    $leadAbacDto->formAttribute = 'marketingCountry';
    $leadAbacDto->formMultiAttribute[0] = 'marketingCountry';
    $leadAbacDto->isNewRecord = false;
    /** @abac $leadAbacDto, LeadAbacObject::CLIENT_CREATE_FORM, LeadAbacObject::ACTION_VIEW, Locale field view*/
    $view = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::CLIENT_CREATE_FORM, LeadAbacObject::ACTION_VIEW);
    /** @abac $leadAbacDto, LeadAbacObject::CLIENT_CREATE_FORM, LeadAbacObject::ACTION_EDIT, Locale field edit*/
    $edit = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::CLIENT_CREATE_FORM, LeadAbacObject::ACTION_EDIT);
    ?>

        <?php echo $form->field($editName, 'marketingCountry', [
            'options' => [
                'class' => 'form-group',
                'hidden' => ($edit ? !$edit : !$view),
            ],
        ])->dropDownList(Language::getCountryNames(), ['prompt' => '-', 'disabled' => !$edit]) ?>


    <?= $form->field($editName, 'id')->hiddenInput()->label(false)->error(false); ?>

    <div class="text-center">
        <?= Html::submitButton('<i class="fa fa-check-square-o"></i> Update client', [
            'class' => 'btn btn-warning'
        ]) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$js = <<<JS
$('#client-edit-name-form').on('beforeSubmit', function (e) {
    e.preventDefault();
    
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            if (!data.error) {
                $('#pjax-client-manage-name').html(data.html);
                $('#modal-client-manage-info').modal('hide');
                
                let clientLocale = $('#clientcreateform-locale').val();
                if (typeof clientLocale !== typeof undefined && clientLocale.length && $('#language option[value=' + clientLocale + ']').length) {
                    $('#language option[value=' + clientLocale + ']').prop('selected', true);
                }
                
                createNotifyByObject({
                    title: 'User name successfully updated',
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
