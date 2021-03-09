<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * @var $model \modules\attraction\models\forms\AttractionOptionsFrom
 * @var $availability array
 */

$pjaxId = 'pjax-options-form-' . $availability['id'];
$model->availability_id = $availability['id'];
?>
<script>
    pjaxOffFormSubmit('#<?=$pjaxId?>');
</script>
<?php Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>

<?php
$form = ActiveForm::begin([
    'validateOnSubmit' => false,
    'options' => ['data-pjax' => true],
    'action' => ['/attraction/attraction-quote/input-availability-options'],
    'method' => 'post'
]);
?>

<?= $form->field($model, 'availability_id')->hiddenInput()->label(false) ?>
<div class="row">
    <?php foreach ($availability['optionList']['nodes'] as $optionKey => $option) :
        $mappedOptions = ArrayHelper::map($option['availableOptions'], 'value', 'label');
        ?>
        <div class="col-3">
            <?= $form->field($model, 'selected_options[' . $optionKey . '][' . $option['id'] . ']')->dropdownList($mappedOptions)->label($option['label']) ?>
        </div>
    <?php endforeach; ?>
</div>
<div class="form-group text-center">
    <?= Html::submitButton('<i class="fa fa-save"></i> Apply Options', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end() ?>
<?php Pjax::end() ?>
