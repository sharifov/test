<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

/**
 * @var $model \modules\attraction\models\forms\AttractionOptionsFrom
 * @var $availability array
 * @var $attractionId int
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
    'options' => ['data-pjax' => 1],
    'action' => ['/attraction/attraction-quote/input-availability-options', 'id' => $attractionId],
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

<?php if (!empty($availability['pricingCategoryList']['nodes'])) : ?>
<div class="row">
    <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                <tr class=" bg-info">
                    <th>Nr.</th>
                    <th>Label</th>
                    <th>Min Participants</th>
                    <th>Max Participants</th>
                    <th>Min Age</th>
                    <th>Max Age</th>
                    <th>Is Valid</th>
                    <th>Price per Unit</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($availability['pricingCategoryList']['nodes'] as $nr => $pax) : ?>
                        <tr>
                            <td title="Pax Id: <?=Html::encode($pax['id'])?>"><?=($nr + 1)?>. Pricing Category</td>
                            <td><b><?=Html::encode($pax['label'])?></b></td>
                            <td><?= Html::encode($pax['minParticipants']) ?></td>
                            <td><?= Html::encode($pax['maxParticipants']) ?></td>
                            <td><?= Html::encode($pax['minAge']) ?></td>
                            <td><?= Html::encode($pax['maxAge']) ?></td>
                            <td><?= $pax['isValid'] ? '<span class="label-success label">Yes<span>' : '<span class="label-danger label">No<span>' ?></td>
                            <td><?= Html::encode($pax['priceFormattedText']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    </div>
</div>
<?php endif; ?>

<div class="form-group text-center">
    <?= Html::submitButton('<i class="fa fa-plus"></i> Apply Options', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end() ?>
<?php Pjax::end() ?>
