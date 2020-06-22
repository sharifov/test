<?php

/**
 * @var $segments []
 * @var $baggage []
 */

use common\models\Airport;
use sales\forms\segment\SegmentBaggageForm;
use sales\services\parsingDump\BaggageService;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use yii\helpers\ArrayHelper;
use \yii\widgets\ActiveForm;

?>

<?php $form = ActiveForm::begin([
            'id' => 'segmentBaggageForm',
            /*'enableClientValidation' => true,*/
        ]) ?>

<?php foreach ($segments as $key => $segment) : ?>
    <div class="row">
        <div class="col-1 border p-1">
            <strong>Segment <?php echo $key+1 ?></strong>
        </div>
        <div class="col-1 border p-1">
            <?php echo $segment['airlineName'] ?>
        </div>
        <div class="col-1 border p-1">
            <?php echo $segment['carrier'] ?>&nbsp;
            <?php echo $segment['flightNumber'] ?>
        </div>
        <div class="col-3 border p-1">
            <?php echo $segment['departureDateTime']->format('g:i A M d') ?>&nbsp;
            <?php echo Airport::findOne($segment['departureAirport'])->getCityName() ?>&nbsp;
            <?php echo $segment['departureAirport'] ?>
        </div>
        <div class="col border p-1">
            <?php echo $segment['arrivalDateTime']->format('g:i A M d') ?>&nbsp;
            <?php echo Airport::findOne($segment['arrivalAirport'])->getCityName() ?>&nbsp;
            <?php echo $segment['arrivalAirport'] ?>
        </div>
    </div>
    <div class="row">
        <!--<div class="col-1 border p-1">Baggage Type</div>
        <div class="col-1 border p-1">Pieces</div>
        <div class="col-1 border p-1">Max Size</div>
        <div class="col-1 border p-1">Max Weight</div>
        <div class="col-1 border p-1">Cost</div>
        <div class="p-1">&nbsp;</div>-->

        <?php
            $segmentBaggageForm = new SegmentBaggageForm();
        ?>

        <?php if (isset($segment['baggage']['paid_baggage'])) : ?>
            <?php /* TODO::  */
                $segment['baggage']['paid_baggage'][0]['type'] = $segmentBaggageForm::TYPE_PAID;
                $baggageData[] = $segment['baggage']['paid_baggage'][0];
                $segmentBaggageForm->baggageData = $baggageData;
            ?>
        <?php endif ?>
        <?php if (isset($segment['baggage']['free_baggage'])) : ?>
            <?php
                $segment['baggage']['free_baggage']['type'] = $segmentBaggageForm::TYPE_FREE;
                $baggageData[] = $segment['baggage']['free_baggage'];
                $segmentBaggageForm->baggageData = $baggageData;
            ?>
        <?php endif ?>

        <?php echo $form->field($segmentBaggageForm, 'baggageData')->widget(MultipleInput::class, [
            'cloneButton' => true,
            'max' => 4,
            'enableError' => true,
            'columns' => [
                [
                    'title' => 'Baggage Type',
                    'name' => 'type',
                    'type'  => 'dropDownList',
                    'items' => $segmentBaggageForm::TYPE_LIST,
                ],
                [
                    'title' => 'Pieces',
                    'name' => 'piece',
                ],
                [
                    'title' => 'Max Size',
                    'name' => 'height',
                ],
                [
                    'title' => 'Max Weight',
                    'name' => 'weight',
                ],
                [
                    'title' => 'Cost',
                    'name' => 'price',
                ],
            ],
        ])->label(false)  ?>

    </div>
    <br />
<?php endforeach; ?>
