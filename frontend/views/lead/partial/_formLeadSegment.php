<?php
/**
 * @var $form ActiveForm
 * @var $segment LeadFlightSegment
 * @var $key string|integer
 */

use yii\widgets\ActiveForm;
use common\models\LeadFlightSegment;
use yii\helpers\Html;
use dosamigos\datepicker\DatePicker;
use yii\jui\AutoComplete;
use yii\web\JsExpression;
use yii\helpers\Url;

?>
<div class="form-group sl-itinerary-form__row js-mc-row">
    <span class="sl-itinerary-form__mc-row-nr"></span>

    <?php
    echo Html::a('<i class="fa fa-times"></i>', 'javascript:void(0);', [
        'class' => 'lead-remove-segment-button sl-itinerary-form__mc-row-del js-del-row',
    ]);
    if ($key == '__id__') {
        echo $form->field($segment, '[' . $key . ']origin_label', [
            'options' => [
                'class' => 'sl-itinerary-form__option'
            ],
            'template' => '{label}{input}{error}{hint}'
        ])->textInput([
            'class' => 'origin form-control lead-form-input-element',
            'placeholder' => 'From',
        ]);

        echo $form->field($segment, '[' . $key . ']destination_label', [
            'options' => [
                'class' => 'sl-itinerary-form__option'
            ],
            'template' => '{label}{input}{error}{hint}'
        ])->textInput([
            'class' => 'destination form-control lead-form-input-element',
            'placeholder' => 'To',
        ]);

    } else {
        echo $form->field($segment, '[' . $key . ']origin_label', [
            'options' => [
                'class' => 'sl-itinerary-form__option'
            ],
        ])->widget(AutoComplete::class, [
            'options' => [
                'class' => 'origin form-control lead-form-input-element',
                'placeholder' => 'From',
            ],
            'clientOptions' => [
                'autoFocus' => true,
                'source' => new JsExpression("function(request, response) {
                $.getJSON('" . Url::to(['site/get-airport']) . "', {
                    term: request.term
                }, response);
            }"),
                'minLength' => '2',
            ]
        ]);

        echo $form->field($segment, '[' . $key . ']destination_label', [
            'options' => [
                'class' => 'sl-itinerary-form__option'
            ],
        ])->widget(AutoComplete::class, [
            'options' => [
                'class' => 'destination form-control lead-form-input-element',
                'placeholder' => 'From',
            ],
            'clientOptions' => [
                'autoFocus' => true,
                'source' => new JsExpression("function(request, response) {
                $.getJSON('" . Url::to(['site/get-airport']) . "', {
                    term: request.term
                }, response);
            }"),
                'minLength' => '2',
            ]
        ]);
    }

    if(!empty($segment->departure)){
        $segment->departure = date('d-M-Y',strtotime($segment->departure));
    }

    echo $form->field($segment, '[' . $key . ']departure', [
        'options' => [
            'class' => 'sl-itinerary-form__option'
        ],
    ])->widget(
        DatePicker::class, [
        'options' => [
            'class' => 'depart-date form-control',
            'placeholder' => 'Departing Date',
            'readonly' => true
        ],
        'clientOptions' => [
            'inline' => false,
            'autoclose' => true,
            'format' => 'dd-M-yyyy',
            'todayHighlight' => true
        ]
    ]);

    echo $form->field($segment, '[' . $key . ']id', [
        'options' => [
            'tag' => false
        ],
    ])->hiddenInput()->label(false);
    ?>

    <div class="sl-itinerary-form__option sl-itinerary-form__option--flexibility">
        <label for="flexibility-<?= $key ?>">Flex (days)</label>
        <label for="flexibility-<?= $key ?>" class="select-wrap-label">
            <?= $form->field($segment, '[' . $key . ']flexibility', [
                'options' => [
                    'tag' => false,
                ],
                'template' => '{input}'
            ])->dropDownList([0, 1, 2, 3, 4], [
                'class' => 'form-control',
            ])->label(false) ?>
        </label>
    </div>

    <div class="sl-itinerary-form__option sl-itinerary-form__option--flexibility">
        <label for="flexibility-<?= $key ?>">Flex (+/-)</label>
        <label for="flexibility-<?= $key ?>" class="select-wrap-label">
            <?= $form->field($segment, '[' . $key . ']flexibility_type', [
                'options' => [
                    'tag' => false,
                ],
                'template' => '{input}'
            ])->dropDownList(['-' => '-', '+/-' => '+/-', '+' => '+'], [
                'class' => 'form-control',
            ])->label(false) ?>
        </label>
    </div>

</div>