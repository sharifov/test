<?php

use common\models\Airline;
use kartik\select2\Select2;
use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use modules\flight\src\useCases\voluntaryExchangeManualCreate\form\AddChangeForm;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use src\services\parsingDump\lib\ParsingDump;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var View $this
 * @var AddChangeForm $addChangeForm
 * @var array $errors
 */

$pjaxId = 'pjax-add-change';
?>

    <div class="row">
        <div class="col-md-12">
            <?php Pjax::begin([
                    'id' => $pjaxId,
                    'enableReplaceState' => false,
                    'enablePushState' => false,
                    'timeout' => 3000,
            ]) ?>
            <?php $form = ActiveForm::begin(
                [
                    'options' => ['data-pjax' => 1],
                    'id' => 'add-change-form',
                    'enableClientValidation' => true,
                    'method' => 'post',
                ]
            ) ?>

            <div id="error_summary_box">
                <?php echo $form->errorSummary($addChangeForm)?>
                <?php if (!empty($errors)) : ?>
                    <p><?php echo implode(', ', $errors) ?></p>
                <?php endif ?>
            </div>
            <?php echo $form->field($addChangeForm, 'case_id')->hiddenInput()->label(false) ?>
            <?php echo $form->field($addChangeForm, 'origin_quote_id')->hiddenInput()->label(false) ?>

            <?php echo $form->field($addChangeForm, 'type_id')->dropdownList(ProductQuoteChange::TYPE_LIST) ?>

            <div class="form-group">
                <?= Html::submitButton('Add', ['class' => 'btn btn-primary js-btn-add-change']) ?>
            </div>

            <?php ActiveForm::end(); ?>

            <?php Pjax::end() ?>
        </div>
    </div>

<?php
$js = <<<JS
    let btnHtml = '';
    let btnObj = $('.js-btn-add-change');

    $('#{$pjaxId}').on('pjax:beforeSend', function (obj, xhr, data) {
        btnHtml = btnObj.html();
        btnObj.html('<i class="fa fa-spin fa-spinner"></i>');
        btnObj.prop('disabled', true);
    });

    $('#{$pjaxId}').on('pjax:end', function (data, xhr) {
        btnObj.prop('disabled', true);
        btnObj.html(btnHtml);
        if (xhr.status !== 200) {
            createNotify('Error', xhr.responseText, 'error');
        } 
    });
JS;
$this->registerJs($js);
