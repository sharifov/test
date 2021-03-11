<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/**
 * @var $availability array
 * @var $paxForm \modules\attraction\models\forms\AvailabilityPaxFrom
 */
$paxForm->availability_id = $availability['id'];
?>

<?php if (!empty($availability['optionList']['nodes'])) : ?>
    <div class="row">
        <div class="col-md-4">
            <h2>Selected Options</h2>
            <table class="table table-bordered caption-top">
                <thead>
                <tr class=" bg-info">
                    <th>Name</th>
                    <th>value</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($availability['optionList']['nodes'] as $index => $option) : ?>
                <tr>
                    <td> <?= $option['label'] ?> </td> <td> <?= $option['answerFormattedText'] ?> </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<?php
$form = ActiveForm::begin([
    'validateOnSubmit' => false,
    'options' => ['data-pjax' => true],
    'action' => ['/attraction/attraction-quote/input-price-category'],
    'method' => 'post'
]);
?>
<?= $form->field($paxForm, 'availability_id')->hiddenInput()->label(false) ?>

<div class="row">
    <div class="col-md-12">
        <h2>Pricing Category List</h2>
        <table class="table table-bordered">
            <thead>
            <tr class=" bg-info">
                <th>Nr.</th>
                <th>Label</th>
                <th>Min Participants</th>
                <th>Max Participants</th>
                <th>Min Age</th>
                <th>Max Age</th>
                <th>Price per Pax</th>
                <th>Quantity</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($availability['pricingCategoryList']['nodes'])) : ?>
                <?php foreach ($availability['pricingCategoryList']['nodes'] as $key => $pax) : ?>
                    <tr>
                        <td title="Pax category Id: <?= Html::encode($pax['id']) ?>"><?= ($key + 1) ?>. Pricing Category </td>
                        <td><b><?= Html::encode($pax['label']) ?></b></td>
                        <td><?= Html::encode($pax['minParticipants']) ?></td>
                        <td><?= Html::encode($pax['maxParticipants']) ?></td>
                        <td><?= Html::encode($pax['minAge']) ?></td>
                        <td><?= Html::encode($pax['maxAge']) ?></td>
                        <td><?= Html::encode($pax['priceFormattedText']) ?></td>
                        <td style="width: 40px">
                            <?= $form->field($paxForm, 'pax_quantity[' . $key . '][' . $pax['id'] . ']')->textInput(['type' => 'number', 'value' => 0, 'min' => 0])->label(false) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-save"></i> Add Quote', ['class' => 'btn btn-success']) ?>
    </div>

<?php ActiveForm::end() ?>