<?php

use frontend\assets\QueryBuilderAsset;
use modules\smartLeadDistribution\src\objects\BaseLeadRatingObject;
use modules\smartLeadDistribution\src\services\SmartLeadDistributionService;
use modules\smartLeadDistribution\src\SmartLeadDistribution;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model modules\smartLeadDistribution\src\entities\LeadRatingParameter */
/* @var $form yii\widgets\ActiveForm */

QueryBuilderAsset::register($this);

$rules = ($model->lrp_condition_json) ? Json::decode($model->lrp_condition_json) : [];
$rulesJson = Json::encode($rules);
$filtersData = SmartLeadDistributionService::getDataForField($model->lrp_object, $model->lrp_attribute);

$filtersDataStr = json_encode($filtersData);
$operators = json_encode(BaseLeadRatingObject::getOperators());
?>
<style>
    .rules-group-container {width: 100%}
    .rule-value-container {display:inline-flex!important;}
</style>
<div class="lead-rating-parameter-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-6">

            <?= $form->field($model, 'lrp_object')->dropDownList(SmartLeadDistribution::OBJ_LIST, ['prompt' => '---']); ?>

            <?= $form->field($model, 'lrp_point')->textInput() ?>

            <?= $form->field($model, 'lrp_condition_json')->hiddenInput(['id' => 'lrp_condition_json', 'maxlength' => true])->label(false) ?>

        </div>
        <div class="col-md-6">
            <?php Pjax::begin(['id' => 'pjax-lead-rating-parameter-form']); ?>
            <?= $form->field($model, 'lrp_attribute')->dropDownList(SmartLeadDistributionService::getAttributesByObject($model->lrp_object), ['prompt' => '---']); ?>

            <div id="builder" class="w-100" style="width: 100%"></div>
            <?php
            $rules = $model->lrp_condition_json ?? "null";
            $jsCode = <<<JS
    
    var rulesData = $rules;
    var filtersData = $filtersDataStr;
    var operators = $operators;
    
    $('#builder').queryBuilder({
        operators: operators,
        select_placeholder: '-- Select Attribute --',
        allow_empty: true,
        plugins: [
            'sortable',
            'unique-filter',
            'bt-checkbox',
            'invert',
            'not-group'
        ],
        filters: filtersData,
        rules: rulesData
    });
    JS;

            if ($filtersData) {
                $this->registerJs($jsCode, \yii\web\View::POS_READY);
            }
            ?>
            <?php Pjax::end() ?>
        </div>
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'id' => 'btn-submit']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>


</div>
<?php
$jsCode2 = <<<JS
    $('#btn-submit').on('click', function() {
      if(!getBuilder()) return false;
    });

    $('body').on('click', '#btn-div-attr-list', function() {
        $('#div-attr-list').toggle();
        return false;
    });
    
    $('body').on('click', '#btn-div-action-list', function() {
        $('#div-action-list').toggle();
        return false;
    });
    
     $('body').on('click', '#btn-div-json-rules', function() {
        $('#div-json-rules').toggle();
        return false;
    });

    function getBuilder()
    {
        var result = $('#builder').queryBuilder('getRules');
        if (!$.isEmptyObject(result)) {
            var json = JSON.stringify(result, null);
            $('#lrp_condition_json').val(json);
            if(result.valid) return true;
        }
        return false;
    }
    
    $('body').on('change', '#leadratingparameter-lrp_object', function(e) {
        var value = $(this).val();
        $.pjax.reload({container: '#pjax-lead-rating-parameter-form', push: false, replace: false, timeout: 5000, data: {object: value}});
    });
    
    $('body').on('change', '#leadratingparameter-lrp_attribute', function(e) {
        let attribute = $(this).val(),
            object = $('#leadratingparameter-lrp_object').val();
        $.pjax.reload({container: '#pjax-lead-rating-parameter-form', push: false, replace: false, timeout: 5000, data: {attribute: attribute, object: object}});
    });
JS;

$this->registerJs($jsCode2, \yii\web\View::POS_READY);
