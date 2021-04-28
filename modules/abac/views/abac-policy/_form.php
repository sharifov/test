<?php

use kartik\select2\Select2;
use modules\abac\src\entities\AbacPolicy;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\entities\AbacPolicy */
/* @var $form yii\widgets\ActiveForm */

\frontend\assets\QueryBuilderAsset::register($this);
?>
<style>
    .rules-group-container {width: 100%}
    .rule-value-container {display:inline-flex!important;}
</style>
<div class="abac-policy-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-5">

            <?php
                \yii\helpers\VarDumper::dump(Yii::$app->abac->getObjectList(), 10, true);
            ?>

            <?php /*= $form->field($model, 'ap_rule_type')->textInput(['maxlength' => true])*/ ?>

            <?php /*= //$form->field($model, 'ap_subject')->textInput(['maxlength' => true])*/ ?>

            <?php /*= $form->field($model, 'ap_subject_json')->textarea()*/ ?>


            <div class="row">
                <div class="col-md-6">
                    <?php /*= $form->field($model, 'ap_object')->textInput(['maxlength' => true])*/ ?>
                    <?= $form->field($model, 'ap_object', [
                        //'options' => ['class' => 'form-group']
                    ])->widget(Select2::class, [
                        'data' => $model->getObjectList(),
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select object', 'multiple' => false],
                        'pluginOptions' => ['allowClear' => true],
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'ap_action_json', [
                        //'options' => ['class' => 'form-group']
                    ])->widget(Select2::class, [
                        'data' => $model->getActionList(),
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select Actions', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ]) ?>
                </div>
            </div>
            <?php /* //$form->field($model, 'ap_action')->textInput(['maxlength' => true])*/ ?>

            <?php /*= $form->field($model, 'ap_action_json')->textarea()*///// ?>



            <?php /*= $form->field($model, 'ap_effect')->
            dropDownList(AbacPolicy::getEffectList(), ['prompt' => '---'])*/ ?>

            <div class="row">
                <div class="col-md-6">
                <?php /*= $form->field($model, 'ap_effect')->widget(Select2::class, [
                    'data' => AbacPolicy::getEffectList(),
                    'size' => Select2::SMALL,
                    'options' => ['placeholder' => 'Select Effect', 'multiple' => false],
                    'pluginOptions' => ['allowClear' => true],
                ])*/
?>
                <?= $form->field($model, 'ap_effect')->dropDownList(AbacPolicy::getEffectList(), ['prompt' => '---']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'ap_sort_order')->input('number', ['min' => 0, 'max' => 1000]) ?>
                </div>
            </div>


            <?= $form->field($model, 'ap_title')->textInput(['maxlength' => true]) ?>



        </div>
        <div class="col-md-7">
            <h3>Rules Data</h3>
            <div id="builder" style="width: 100%"></div>
            <?=Html::button('Validate rules', ['class' => 'btn sm-btn btn-warning', 'id' => 'btn-getcode'])?>
            <?= $form->field($model, 'ap_subject_json')->textarea(['rows' => 8, 'id' => 'ap_subject_json', 'readonly' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save Policy', ['class' => 'btn btn-success', 'id' => 'btn-submit']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$filtersData = [
    [
        'id' => 'id1',
        'label' => 'ID1',
        'type'  => "integer",
        //'value' => true // boolean
        'input' => 'radio',
        'values' => [
            1 => 'Yes',
            0 => 'No'
        ],
        'default_value' => 1,
        'operators' =>  ['equal'],
        'unique' => true,
        'description' => 'This filter is "unique", it can be used only once'
    ],

    [
        'id' => 'id2',
        'label' => 'ID2',
        'type' => 'string',
        'operators' =>  ['equal', 'not_equal', 'in', 'not_in', '==', '!=', 'match']
    ],
];


//$filtersData = [
//    [
//        'id' => Rule::VAR_VALIDATED,
//        'label' => Rule::VARS[Rule::VAR_VALIDATED],
//        'type'  => "integer",
//        //'value' => true // boolean
//        'input' => 'radio',
//        'values' => [
//            1 => 'Yes',
//            0 => 'No'
//        ],
//        'default_value' => 1,
//        'operators' =>  ['equal'],
//        'unique' => true,
//        'description' => 'This filter is "unique", it can be used only once'
//    ],
//    [
//        'id' => Rule::VAR_FLIGHT_COUNT,
//        'label' => Rule::VARS[Rule::VAR_FLIGHT_COUNT],
//        'type' => 'integer',
//        'validation' => [
//            'min' => 1,
//            'step' => 1
//        ],
//        'icon' => 'fa fa-plane',
//        'default_operator' => 'equal',
//        'operators' =>  ['equal', 'not_equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between',],
//        'unique' => true,
//        'description' => 'This filter is "unique", it can be used only once'
//    ],
//    [
//        'id' => Rule::VAR_SEGMENT_COUNT,
//        'label' => Rule::VARS[Rule::VAR_SEGMENT_COUNT],
//        'type' => 'integer',
//        'icon' => 'fa fa-exchange',
//        'validation' => [
//            'min' => 1,
//            'step' => 1
//        ],
//        'operators' =>  ['equal', 'not_equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between',]
//    ],
//    [
//        'id' => Rule::VAR_PASSENGER_COUNT,
//        'label' => Rule::VARS[Rule::VAR_PASSENGER_COUNT],
//        'type' => 'integer',
//        'icon' => 'fa fa-user',
//        'validation' => [
//            'min' => 1,
//            'step' => 1
//        ],
//        'operators' =>  ['equal', 'not_equal', 'less', 'less_or_equal', 'greater', 'greater_or_equal', 'between', 'not_between',]
//    ],
//
//    [
//        'id' => Rule::VAR_FL_ORIGIN_LOCATION,
//        'label' => Rule::VARS[Rule::VAR_FL_ORIGIN_LOCATION],
//        'icon'  => 'fa fa-share',
//        'type' => 'string',
//        'operators' =>  ['equal', 'not_equal', 'in', 'not_in'],
//    ],
//
//    [
//        'id' => Rule::VAR_FL_DESTINATION_LOCATION,
//        'label' => Rule::VARS[Rule::VAR_FL_DESTINATION_LOCATION],
//        'icon'  => 'fa fa-reply',
//        'type' => 'string',
//        'operators' =>  ['equal', 'not_equal', 'in', 'not_in']
//    ],
//
//
//    [
//        'id' => Rule::VAR_SEG_DEPARTURE_DATE,
//        'label' => Rule::VARS[Rule::VAR_SEG_DEPARTURE_DATE],
//        'icon' => 'fa fa-calendar',
//        'type' => 'string',
//        'operators' =>  ['equal', 'not_equal']
//    ],
//
//    [
//        'id' => Rule::VAR_SEG_DEPARTURE_DATETIME,
//        'label' => Rule::VARS[Rule::VAR_SEG_DEPARTURE_DATETIME],
//        'icon' => 'fa fa-calendar',
//        'type' => 'string',
//        'operators' =>  ['equal', 'not_equal']
//    ],
//
//    [
//        'id' => Rule::VAR_SEG_DEPARTURE_AIRPORT,
//        'label' => Rule::VARS[Rule::VAR_SEG_DEPARTURE_AIRPORT],
//        'type' => 'string',
//        'icon'  => 'fa fa-flag-o',
//        'operators' =>  ['equal', 'not_equal', 'in', 'not_in']
//    ],
//
//    [
//        'id' => Rule::VAR_SEG_ARRIVAL_AIRPORT,
//        'label' => Rule::VARS[Rule::VAR_SEG_ARRIVAL_AIRPORT],
//        'icon'  => 'fa fa-flag-checkered',
//        'type' => 'string',
//        'operators' =>  ['equal', 'not_equal', 'in', 'not_in']
//    ],
//
//    [
//        'id' => Rule::VAR_SEG_AIRLINE_CODE,
//        'label' => Rule::VARS[Rule::VAR_SEG_AIRLINE_CODE],
//        'type' => 'string',
//        'operators' =>  ['equal', 'not_equal', 'in', 'not_in']
//    ],
//
//    [
//        'id' => Rule::VAR_SEG_FLIGHT_NUMBER,
//        'label' => Rule::VARS[Rule::VAR_SEG_FLIGHT_NUMBER],
//        'type' => 'string',
//        'icon' => 'fa fa-ticket',
//        'operators' =>  ['equal', 'not_equal', 'in', 'not_in']
//    ],
//
//
//    [
//        'id' => Rule::VAR_SEG_BOOKING_CLASS,
//        'label' => Rule::VARS[Rule::VAR_SEG_BOOKING_CLASS],
//        'type' => 'string',
//        'operators' =>  ['equal', 'not_equal', 'in', 'not_in']
//    ],
//
//    [
//        'id' => Rule::VAR_SEG_CABIN_TYPE,
//        'label' => Rule::VARS[Rule::VAR_SEG_CABIN_TYPE],
//        'type' => 'integer',
//        'input' => 'checkbox',
//        'values' => \common\models\Segment::CABIN_TYPES,
//        // 'color' => 'primary',
//        'operators' =>  ['in', 'not_in', 'is_null', 'is_not_null']
//    ],
//
//    [
//        'id' => Rule::VAR_CONTACT_EMAIL,
//        'label' => Rule::VARS[Rule::VAR_CONTACT_EMAIL],
//        'type' => 'string',
//        'icon' => 'fa fa-envelope-o',
//        'operators' =>  ['equal', 'not_equal', 'in', 'not_in', 'is_empty', 'is_not_empty', 'is_null', 'is_not_null']
//    ],
//
//    [
//        'id' => Rule::VAR_CONTACT_PHONE,
//        'label' => Rule::VARS[Rule::VAR_CONTACT_PHONE],
//        'type' => 'string',
//        'icon' => 'fa fa-phone',
//        'operators' =>  ['equal', 'not_equal']
//    ],
//
//    /*[
//        'id' => 'category',
//        'label' => 'Category',
//        'type' => 'integer',
//        'input' => 'select',
//        'values' => [
//            1 => 'Books',
//            2 => 'Movies',
//        ],
//        'operators' =>  ['equal', 'not_equal', 'less', 'not_in', 'is_null', 'is_not_null']
//    ],
//
//    [
//        'id' => 'price',
//        'label' => 'Price',
//        'type' => 'double',
//        'validation' => [
//            'min' => 0,
//            'step' => 0.01
//        ]
//    ]
//];*/

$rulesData = @json_decode($model->ap_subject_json);
$rulesDataStr = json_encode($rulesData);
$filtersDataStr = json_encode($filtersData);

$jsCode = <<<JS
$('#btn-submit').on('click', function() {
  if(!getBuilder()) return false;
});

$('#btn-getcode').on('click', function() {
    var result = $('#builder').queryBuilder('getRules');

    if (!$.isEmptyObject(result)) {
        var json = JSON.stringify(result, null, 2);
        alert(json);
        console.log(json);
    }
});

function getBuilder()
{
    var result = $('#builder').queryBuilder('getRules');

    if (!$.isEmptyObject(result)) {
        var json = JSON.stringify(result, null);
        $('#ap_subject_json').val(json);
        if(result.valid) return true;
    }
    
    return false;
}

let rulesData = $rulesDataStr;
let filtersData = $filtersDataStr;


let operators = [
        'equal',
        'not_equal',
        'in',
        'not_in',
        'less',
        'less_or_equal',
        'greater',
        'greater_or_equal',
        'between',
        'not_between',
        'begins_with',
        'not_begins_with',
        'contains',
        'not_contains',
        'ends_with',
        'not_ends_with',
        'is_empty',
        'is_not_empty',
        'is_null',
        'is_not_null',
        
        { type: '==', optgroup: 'custom', nb_inputs: 1, multiple: false, apply_to: ['number', 'string'] },
        { type: '!=', optgroup: 'custom', nb_inputs: 2, multiple: false, apply_to: ['number', 'string'] },
        { type: '>', optgroup: 'custom', nb_inputs: 3, multiple: false, apply_to: ['number', 'string'] },
        { type: '<', optgroup: 'custom', nb_inputs: 1, multiple: false, apply_to: ['number', 'string'] },
        { type: '>=', optgroup: 'custom', nb_inputs: 1, multiple: false, apply_to: ['number', 'string'] },
        { type: '<=', optgroup: 'custom', nb_inputs: 1, multiple: false, apply_to: ['number', 'string'] },
        //{ type: 'contains', optgroup: 'custom', nb_inputs: 1, multiple: false, apply_to: ['string'] },
        //{ type: 'begins_with', optgroup: 'custom', nb_inputs: 1, multiple: false, apply_to: ['string'] },
        //{ type: 'ends with', optgroup: 'custom', nb_inputs: 1, multiple: false, apply_to: ['string'] },
        { type: 'before', optgroup: 'custom', nb_inputs: 1, multiple: false, apply_to: ['string'] },
        { type: 'after', optgroup: 'custom', nb_inputs: 1, multiple: false, apply_to: ['string'] },
        //{ type: 'between', optgroup: 'custom', nb_inputs: 0, multiple: false, apply_to: ['string'] },
        { type: 'match', optgroup: 'custom', nb_inputs: 1, multiple: false, apply_to: ['string'] }
    ];

// [
//         'equal', 'not_equal', 'is_null',
//          { type: 'contains_any', nb_inputs: 2, multiple: true, apply_to: ['string'] },
//       ]

$('#builder').queryBuilder({
    operators: operators,
    select_placeholder: '--Select Attribute--',
    allow_empty: true,
    plugins: [
        'sortable',
        //'filter-description',
        'unique-filter',
        //'bt-tooltip-errors',
        //'bt-selectpicker',    
        'bt-checkbox',
        'invert',
        //'not-group'
    ],
    filters: filtersData,
    rules: rulesData
});
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);