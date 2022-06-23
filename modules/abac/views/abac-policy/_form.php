<?php

use kartik\select2\Select2;
use modules\abac\src\entities\AbacPolicy;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\forms\AbacPolicyForm */
/* @var $ap modules\abac\src\entities\AbacPolicy */
/* @var $form yii\widgets\ActiveForm */

\frontend\assets\QueryBuilderAsset::register($this);


$rulesData = @json_decode($model->ap_subject_json);
$rulesDataStr = json_encode($rulesData);
$filtersData = $model->getObjectAttributeList();
$filtersDataStr = json_encode($filtersData);
$operators = json_encode(\modules\abac\components\AbacBaseModel::getOperators());

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



            <?php /*= $form->field($model, 'ap_rule_type')->textInput(['maxlength' => true])*/ ?>

            <?php /*= //$form->field($model, 'ap_subject')->textInput(['maxlength' => true])*/ ?>

            <?php /*= $form->field($model, 'ap_subject_json')->textarea()*/ ?>


            <?php if ($ap->isNewRecord) : ?>
            <div class="row">
                <div class="col-md-6">
                    <?php /*= $form->field($model, 'ap_object')->textInput(['maxlength' => true])*/ ?>
                    <?= $form->field($model, 'ap_object', [
                        //'options' => ['id' => 'ap_object']
                    ])->widget(Select2::class, [
                        'data' => $model->getObjectList(),
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select object', 'multiple' => false],
                        'pluginOptions' => ['allowClear' => true],
                    ]) ?>
                </div>
                <div class="col-md-6">

                </div>
            </div>
            <?php else : ?>
                <?php echo $form->field($model, 'ap_object', [
                    //'options' => ['id' => 'ap_object']
                ])
                    ->hiddenInput()
                    ->label(false);
                ?>
            <?php endif; ?>
            <?php /* //$form->field($model, 'ap_action')->textInput(['maxlength' => true])*/ ?>

            <?php /*= $form->field($model, 'ap_action_json')->textarea()*/////?>



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
            <?= $form->field($model, 'ap_enabled')->checkbox() ?>

        </div>


        <div class="col-md-7">
            <?php Pjax::begin(['id' => 'pjax-abac-policy-form']); ?>

                <?php if ($model->ap_object) : ?>
                <h2>Object</h2>
                    <pre><b><?php echo Html::encode($model->ap_object) ?></b></pre>



                    <?php if ($model->getActionList()) : ?>
                        <?php //echo Html::a('Show / hide Action List', null, ['class' => 'btn btn-sm btn-default', 'id' => 'btn-div-action-list'])?>
                        <div id="div-action-list" style="display: none">
                            <pre><?php \yii\helpers\VarDumper::dump($model->getActionList(), 10, true) ?></pre>
                        </div>

                        <?= $form->field($model, 'ap_action_list', [
                            //'options' => ['class' => 'form-group']
                        ])->widget(Select2::class, [
                            'data' => $model->getActionList(),
                            'size' => Select2::SMALL,
                            'options' => ['placeholder' => 'Select Actions', 'multiple' => true],
                            'pluginOptions' => ['allowClear' => true],
                        ]) ?>
                    <?php else : ?>
                        <div class="alert alert-warning" role="alert">
                            <strong>Warning</strong>: ACTION list for this object is empty!
                        </div>
                    <?php endif; ?>

                    <h2>Policy Rules</h2>
                    <div id="rules-info-block"></div>
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
                            <?= $form->field($model, 'ap_subject_json')->textarea(['rows' => 8, 'id' => 'ap_subject_json', 'readonly' => true]) ?>
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
    if(rulesData !== null && !rulesData.valid) {
        $('#rules-info-block').append('<div class="alert alert-warning" role="alert"><strong>Warning</strong>: Current Json Rules are invalid!</div>');
        rulesData = JSON.parse('[]');
    }
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

    <?php
    // \yii\helpers\VarDumper::dump(Yii::$app->abac->getObjectList(), 10, true);
    ?>
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
            $('#ap_subject_json').val(json);
            if(result.valid) return true;
        }
        return false;
    }
    
    $('body').on('change', '#abacpolicyform-ap_object', function(e) {
        var value = $(this).val();
        $.pjax.reload({container: '#pjax-abac-policy-form', push: false, replace: false, timeout: 5000, data: {object: value}});
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
