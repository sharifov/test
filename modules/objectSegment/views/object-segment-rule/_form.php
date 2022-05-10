<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \modules\objectSegment\src\forms\ObjectSegmentRuleForm */
/* @var $osr \modules\objectSegment\src\entities\ObjectSegmentRule*/
/* @var $form yii\widgets\ActiveForm */

\frontend\assets\QueryBuilderAsset::register($this);
$rulesData = @json_decode($model->osr_rule_condition_json);
$rulesDataStr = json_encode($rulesData);
$filtersData = $model->getObjectAttributeList();
$filtersDataStr = json_encode($filtersData);
$operators = json_encode(\modules\objectSegment\components\ObjectSegmentBaseModel::getOperators());

?>
<style>
    .rules-group-container {width: 100%}
    .rule-value-container {display:inline-flex!important;}
</style>
<div class="abac-policy-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->errorSummary($model) ?>

    <div class="row">
        <div class="col-md-5">


            <?php if ($osr->isNewRecord) : ?>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'osr_osl_id', [
                    ])->widget(Select2::class, [
                        'data' => $model->getObjectList(),
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select List Object', 'multiple' => false],
                        'pluginOptions' => ['allowClear' => true],
                    ]) ?>
                </div>
                <div class="col-md-6">

                </div>
            </div>
            <?php else : ?>
                <?php echo $form->field($model, 'osr_osl_id', [
                ])
                    ->hiddenInput()
                    ->label(false);
                ?>
            <?php endif; ?>


            <?= $form->field($model, 'osr_title')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'osr_enabled')->checkbox() ?>

        </div>


        <div class="col-md-7">
            <?php Pjax::begin([ 'linkSelector' => '','id' => 'pjax-object_segment_rule_form']); ?>

                <?php if ($model->osr_osl_id) : ?>
                <h2>Object</h2>
                    <pre><b><?php echo Html::encode($model->objectName) ?></b></pre>

                    <h2>Policy Rules</h2>
                    <?php echo Html::a('Show / hide Attribute List', null, ['class' => 'btn btn-sm btn-default', 'id' => 'btn-div-attr-list']) ?>
                    <div id="div-attr-list" style="display: none">
                        <pre><?php \yii\helpers\VarDumper::dump($filtersData, 10, true)?></pre>
                    </div>

                    <?php if ($filtersData) : ?>
                        <div id="builder" style="width: 100%"></div>
                        <br>
                        <?php echo Html::a('Show / hide JSON rules', null, ['class' => 'btn btn-sm btn-default', 'id' => 'btn-div-json-rules']) ?>
                        <?=Html::button('<i class="fa fa-check-square-o"></i> Validate rules', ['class' => 'btn btn-sm btn-warning', 'id' => 'btn-getcode'])?>

                        <div id="div-json-rules" style="display: none">
                            <?= $form->field($model, 'osr_rule_condition_json')->textarea(['rows' => 8, 'id' => 'osr_rule_condition_json', 'readonly' => true]) ?>
                        </div>
                    <?php else : ?>
                        <div class="alert alert-warning" role="alert">
                            <strong>Warning</strong>: ATTRIBUTE list (Filter data) for this object is empty!
                        </div>
                    <?php endif; ?>

                    <?php
                    $jsCode = <<<JS
    
    var rulesData = $rulesDataStr;
    var filtersData = $filtersDataStr;
    var operators = $operators;
    
    $('#builder').queryBuilder({
        operators: operators,
        select_placeholder: '-- Select Attribute --',
        allow_empty: true,
        plugins: [
            //'bt-tooltips-errors',
            //'bt-selectpicker',
            // 'chosen-selectpicker'
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

                    if ($filtersData) {
                        $this->registerJs($jsCode, \yii\web\View::POS_READY);
                    }
                    ?>

                <?php endif; ?>
            <?php Pjax::end(); ?>
        </div>

    </div>


    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save Policy', ['class' => 'btn btn-success', 'id' => 'btn-submit']) ?>
            </div>
        </div>
    </div>


    <div class="row">
    </div>


    <?php ActiveForm::end(); ?>

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
            $('#osr_rule_condition_json').val(json);
            if(result.valid) return true;
        }
        return false;
    }
    
    $('body').on('change', '#objectsegmentruleform-osr_osl_id', function(e) {
        var value = $(this).val();
        $.pjax.reload({container: '#pjax-object_segment_rule_form', push: false, replace: false, timeout: 5000, data: {osr_osl_id: value}});
    });
    
    $('body').on('click', '#btn-getcode', function() {
        var result = $('#builder').queryBuilder('getRules');
        if (!$.isEmptyObject(result)) {
            var json = JSON.stringify(result, null, 2);
            alert(json);
            console.log(json);
        }
    });
    
JS;

$this->registerJs($jsCode2, \yii\web\View::POS_READY);
