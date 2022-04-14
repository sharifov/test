<?php

use common\models\Language;
use src\auth\Auth;
use src\services\client\ClientCreateForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;

/**
 * @var $form ActiveForm
 * @var $this View
 * @var $editName ClientCreateForm
 * @var $chatId $chatId
 */

?>

<div class="edit-name-modal-content-ghj">
    <?php $form = ActiveForm::begin([
        'id' => 'client-edit-name-form',
        'action' => Url::to(['/client-chat-client-actions/ajax-edit-client-name', 'id' => $chatId]),
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validationUrl' => Url::to(['/client-chat-client-actions/ajax-edit-client-name-validation', 'id' => $chatId])
    ]);
?>

    <?= $form->errorSummary($editName) ?>

    <?= $form->field($editName, 'firstName')->textInput(['required' => true]) ?>

    <?= $form->field($editName, 'lastName')->textInput() ?>

    <?= $form->field($editName, 'middleName')->textInput() ?>

    <?= $form->field($editName, 'id')->hiddenInput()->label(false)->error(false) ?>

    <?php if (Auth::can('global/client/locale/edit')) : ?>
        <?= $form->field($editName, 'locale')->dropDownList(Language::getLocaleList(false), ['prompt' => '-']) ?>
    <?php endif ?>
    <?php if (Auth::can('global/client/marketing_country/edit')) : ?>
        <?php echo $form->field($editName, 'marketingCountry')->dropDownList(Language::getCountryNames(), ['prompt' => '-']) ?>
    <?php endif ?>

    <div class="text-center">
        <?= Html::submitButton('<i class="fa fa-check-square-o"> </i> Update client', [
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
                $(document).find('.client-chat-client-info-wrapper').html(data.html);
                $('#modal-client-manage-info').modal('hide');
                $(document).find('span[data-cc-client-name-id="' + data.client.id + '"]').html(data.client.name);                
                $(document).find('span[data-cc-client-fl-name-id="' + data.client.id + '"]').html(data.client.name.charAt(0).toUpperCase());                
                
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
