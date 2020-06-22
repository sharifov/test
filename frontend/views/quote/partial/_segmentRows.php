<?php

/**
 * @var $segments []
 * @var $baggage []
 */

use common\models\Airport;
use sales\forms\segment\SegmentBaggageForm;
use unclead\multipleinput\MultipleInput;
use unclead\multipleinput\MultipleInputColumn;
use \yii\widgets\ActiveForm;

/* @var SegmentBaggageForm $segmentBaggageForm */

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
        <div class="col-1 border p-1">Baggage Type</div>
        <div class="col-1 border p-1">Pieces</div>
        <div class="col-1 border p-1">Max Size</div>
        <div class="col-1 border p-1">Max Weight</div>
        <div class="col-1 border p-1">Cost</div>

        <?php if (isset($segment['baggage']['free_baggage'])) : ?>
            <?php $freeBaggage =  $segment['baggage']['free_baggage']; ?>

                <?php echo $form->field($segmentBaggageForm, 'baggageData')->widget(MultipleInput::class, [
                    'max' => 5,
                    'enableError' => true,
                    'columns' => [
                        [
                            'title' => 'Baggage Type',
                            'name' => 'type',
                            'type'  => 'dropDownList',
                            'items' => $segmentBaggageForm::TYPE_LIST,
                            'defaultValue' => $segmentBaggageForm::TYPE_FREE,
                        ],
                        [
                            'title' => 'Pieces',
                            'name' => 'piece',
                            'defaultValue' => $freeBaggage['piece'],
                        ],
                        [
                            'title' => 'Max Size',
                            'name' => 'maxSize',
                            'defaultValue' => $freeBaggage['height'],
                        ],
                        [
                            'title' => 'Max Weight',
                            'name' => 'maxWeight',
                            'defaultValue' => $freeBaggage['weight'],
                        ],
                    ],
                ])->label(false) ?>

        <?php else : ?>

        <?php endif; ?>

    </div>
    <br />
<?php endforeach; ?>


