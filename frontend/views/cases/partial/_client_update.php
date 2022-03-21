<?php

use common\models\Language;
use src\auth\Auth;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model src\forms\cases\CasesClientUpdateForm */
/* @var $form yii\widgets\ActiveForm */
?>
<?php // Pjax::begin(['id' => 'pjax-cases-client-update-form', 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="cases-change-status">

    <?php $form = ActiveForm::begin([
        'id' => 'client-edit-name-form',
        'action' => ['cases/client-update', 'gid' => $model->caseGid],
        'method' => 'post',
        //'options' => ['data-pjax' => true]
        'options' => ['data-pjax' => 0],
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validationUrl' => Url::to(['cases/client-update-validation', 'gid' => $model->caseGid])
    ]); ?>

    <?php
        echo $form->errorSummary($model);
    ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>

    <?php if (Auth::can('global/client/locale/edit')) : ?>
        <?php echo $form->field($model, 'locale')->dropDownList(Language::getLocaleList(false), ['prompt' => '-']) ?>
    <?php endif ?>
    <?php if (Auth::can('global/client/marketing_country/edit')) : ?>
        <?php echo $form->field($model, 'marketingCountry')->dropDownList(Language::getCountryNames(), ['prompt' => '-']) ?>
    <?php endif ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Update', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php // Pjax::end(); ?>

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
                $.pjax.reload({container: '#pjax-client-info', timeout: 10000, async: false});
                
                let clientLocale = $('#casesclientupdateform-locale').val();
                if (typeof clientLocale !== typeof undefined && clientLocale.length && $('#language option[value=' + clientLocale + ']').length) {
                    $('#language option[value=' + clientLocale + ']').prop('selected', true);
                }
                
                $('#modalCaseSm').modal('hide');
                
                createNotifyByObject({
                    title: 'Client info successfully updated',
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
