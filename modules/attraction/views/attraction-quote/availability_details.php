<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

/**
 * @var $availability array
 * @var $paxForm \modules\attraction\models\forms\AvailabilityPaxFrom
 * @var $attractionId int
 * @var $model \modules\attraction\models\forms\AttractionOptionsFrom
 */

$paxForm->availability_id = $availability['id'];
$model->availability_id = $availability['id'];
$availabilityID = $availability['id'];
$errorFormGeneration = false;
?>

<?php if (!$availability['optionList']['isComplete']) : ?>
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
                <?php if ($option['dataType'] === 'BOOLEAN') : ?>
                    <?= $form->field($model, 'selected_options[' . $optionKey . '][' . $option['id'] . ']')->checkbox()->label($option['label']) ?>
                <?php elseif ($option['dataType'] === 'TEXT') : ?>
                    <?= $form->field($model, 'selected_options[' . $optionKey . '][' . $option['id'] . ']')->textInput()->label($option['label']) ?>
                <?php elseif ($option['dataType'] === 'OPTIONS') :
                    $errorFormGeneration = empty($mappedOptions);
                    ?>
                    <?= $form->field($model, 'selected_options[' . $optionKey . '][' . $option['id'] . ']')->dropdownList($mappedOptions)->label($option['label']) ?>
                <?php else : ?>
                    <?= 'Unknown field datatype' ?>
                <?php endif;?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($errorFormGeneration) :?>
        <div class="alert alert-warning" role="alert">
            <p>Partner service returned inconsistent data.
            Your may try to change answer for previous question and update.
            If you still receive this message try other availability or new Attraction product.</p>
        </div>
    <?php endif; ?>

    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-save"></i> Answer ', ['class' => 'btn btn-success .btn-add-attraction-answer']) ?>
    </div>

    <?php ActiveForm::end() ?>
<?php endif; ?>

<?php if (!empty($availability['optionList']['nodes'])) : ?>
    <div class="row">
        <div class="col-md-4">
            <h2>Selected Options</h2>
            <table class="table table-bordered caption-top">
                <thead>
                <tr class="bg-info">
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Is Answered</th>
                    <th>Is Defaulted</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($availability['optionList']['nodes'] as $index => $option) : ?>
                    <tr>
                        <td> <?= $option['label'] ?> </td>
                        <td> <?= $option['answerFormattedText'] ?> </td>
                        <td> <?= $option['isAnswered'] ? '<span class="label-success label">Yes<span>' : '<span class="label-danger label">No<span>' ?> </td>
                        <td> <?= $option['isAnswerDefaulted'] ? '<span class="label-success label">Yes<span>' : '<span class="label-danger label">No<span>' ?> </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>

<?php
$form = ActiveForm::begin([
    'id' => 'form-' . $availabilityID,
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    //'options' => ['data-pjax' => true],
    'action' => ['/attraction/attraction-quote/add-quote-ajax', 'id' => $attractionId],
    'method' => 'post'
]);
?>
<?= $form->field($paxForm, 'availability_id')->hiddenInput()->label(false) ?>

<?php if (!empty($availability['pricingCategoryList']['nodes'])) : ?>
    <?php if (
    !empty(array_filter($availability['pricingCategoryList']['nodes'], function ($a) {
        return $a !== null;
    }))
) : ?>
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
                        <th>Is Valid</th>
                        <th>Price per Unit</th>
                        <th>Quantity</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($availability['pricingCategoryList']['nodes'] as $key => $pax) : ?>
                        <tr>
                            <td title="Pax category Id: <?= Html::encode($pax['id']) ?>"><?= ($key + 1) ?>. Pricing
                                Category
                            </td>
                            <td><b><?= Html::encode($pax['label']) ?></b></td>
                            <td><?= Html::encode($pax['minParticipants']) ?></td>
                            <td><?= Html::encode($pax['maxParticipants']) ?></td>
                            <td><?= Html::encode($pax['minAge']) ?></td>
                            <td><?= Html::encode($pax['maxAge']) ?></td>
                            <td><?= $pax['isValid'] ? '<span class="label-success label">Yes<span>' : '<span class="label-danger label">No<span>' ?></td>
                            <td><?= Html::encode($pax['priceFormattedText']) ?></td>
                            <td style="width: 40px">
                                <?= $form->field($paxForm, 'pax_quantity[' . $key . '][' . $pax['id'] . ']')->textInput([
                                    'type' => 'number',
                                    'value' => 0,
                                    'min' => 0
                                ])->label(false) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="form-group text-center">
            <?= Html::submitButton('<i class="fa fa-save"></i> Add Quote', ['class' => 'btn btn-success btn-add-quote']) ?>
        </div>
    <?php else : ?>
        <div class="text-center"> Not found Pricing Categories </div>
    <?php endif; ?>
<?php endif; ?>

<?php ActiveForm::end() ?>

<?php
$js = <<<JS

var availabilityID = '$availabilityID'

$('#form-' + availabilityID).on('beforeSubmit', function (e) {
    e.preventDefault();
    
    let btnAdd = $('.btn-add-quote');     
      btnAdd.find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');
    
    $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       dataType: 'json',
       success: function(data) {
            if (!data.error) {
                $('#' + data.availabilityID).html(data.html);  
                createNotifyByObject({
                    title: 'Quote successfully added',
                    text: data.message,
                    type: 'success'
                });
            } else {               
                if (data.message == 'Quantity not selected'){
                    createNotifyByObject({
                        title: 'Error',
                        text: data.message,
                        type: 'error'                
                    });
                }
                if (data.message == 'invalidPricing'){
                    createNotifyByObject({
                        title: 'Error',
                        text: 'Please check quantity according to participants',
                        type: 'error'                
                    });
                }
                if (Array.isArray(data.message) && data.message.length > 0){
                    createNotifyByObject({
                        title: 'Error',
                        text: data.message.join(" <br> "),
                        type: 'error'                
                    });
                }
                btnAdd.find('i').removeClass('fa-spin fa-spinner').addClass('fa-save');
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

<?php
$js = <<<JS
$('body').off('click', '.btn-add-attraction-answer').on('click', '.btn-add-attraction-answer', function (e) {                 
      let btnAdd = $(this);     
      btnAdd.find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');
})
JS;
$this->registerJs($js);
?>
