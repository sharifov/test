<?php

/**
 * @var $form ActiveForm
 * @var $this View
 * @var $editName \sales\services\client\ClientCreateForm
 * @var $lead Lead
 */

use common\models\Language;
use common\models\Lead;
use sales\auth\Auth;
use sales\services\client\ClientCreateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

$user = Yii::$app->user->identity;
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

    <?php if (Auth::can('global/client/locale/edit')) : ?>
        <?= $form->field($editName, 'locale')->dropDownList(Language::getLocaleList(false), ['prompt' => '-']) ?>
    <?php endif ?>
    <?php if (Auth::can('global/client/marketing_country/edit')) : ?>
        <?php echo $form->field($editName, 'marketingCountry')->dropDownList(Language::getCountryNames(), ['prompt' => '-']) ?>
    <?php endif ?>

    <?=
    $form->field($editName, 'id')->hiddenInput()->label(false)->error(false);
    ?>

    <div class="text-center">
        <?= Html::submitButton('<i class="fa fa-check-square-o"></i> Update client', [
            'class' => 'btn btn-warning'
        ])
?>
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
                if (clientLocale !== 'undefined' && clientLocale.length && $('#language option[value=' + clientLocale + ']').length) {
                    $('#language option[value=' + clientLocale + ']').prop('selected', true);
                }
                
                new PNotify({
                    title: 'User name successfully updated',
                    text: data.message,
                    type: 'success'
                });
            }
       },
       error: function (error) {
            new PNotify({
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
