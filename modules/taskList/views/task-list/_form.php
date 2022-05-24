<?php

use kartik\select2\Select2;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\TaskObject;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\taskList\TaskList */
/* @var $form yii\widgets\ActiveForm */

\frontend\assets\QueryBuilderAsset::register($this);

//$rulesData = @json_decode($model->ap_subject_json);
//$rulesDataStr = json_encode($rulesData);
//$filtersData = $model->getObjectAttributeList();
//$filtersDataStr = json_encode($filtersData);
//$operators = json_encode(\modules\abac\components\AbacBaseModel::getOperators());


$list = TaskObject::getAttributeListByObject('call');
\yii\helpers\VarDumper::dump($list, 10, true);

?>
<style>
    .rules-group-container {width: 100%}
    .rule-value-container {display:inline-flex!important;}
</style>
<div class="task-list-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model) ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'tl_title')->textInput(['maxlength' => true]) ?>


            <div class="row">
                <div class="col-md-6">
                    <?php /*= $form->field($model, 'ap_object')->textInput(['maxlength' => true])*/ ?>
                    <?= $form->field($model, 'tl_object', [
                        //'options' => ['id' => 'ap_object']
                    ])->widget(Select2::class, [
                        'data' => TaskObject::getObjectList(),
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select object', 'multiple' => false],
                        'pluginOptions' => ['allowClear' => true],
                    ]) ?>
                </div>
            </div>


            <?php /* $form->field($model, 'tl_object')
                ->dropDownList(TaskObject::getObjectList(), ['prompt' => '-'])*/ ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'tl_work_start_time_utc')->input('time') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'tl_work_end_time_utc')->input('time') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                <?= $form->field($model, 'tl_duration_min')->textInput() ?>
                    </div>
                    <div class="col-md-6">
                <?= $form->field($model, 'tl_enable_type')
                    ->dropDownList(
                        TaskList::getEnableTypeList(),
                        ['prompt' => '-']
                    ) ?>
                </div>
            </div>

            <?= $form->field($model, 'tl_cron_expression')->textInput(['maxlength' => true]) ?>

            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'tl_sort_order')->input('number', ['min' => 0]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">

            <?php Pjax::begin(['id' => 'pjax-task-list-form']); ?>
                <?php if ($model->tl_object) : ?>
                    <h2>Object</h2>
                    <pre><b><?php echo Html::encode($model->tl_object) ?></b></pre>





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
                            <?= $form->field($model, 'tl_condition_json')->textarea(['rows' => 8, 'id' => 'tl_condition_json', 'readonly' => true]) ?>
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

            <?= $form->field($model, 'tl_condition')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'tl_condition_json')->textInput() ?>


            <?php

            try {
                echo $form->field($model, 'tl_params_json')->widget(
                    \kdn\yii2\JsonEditor::class,
                    [
                        'clientOptions' => [
                            'modes' => ['code', 'form', 'tree', 'view'], //'text',
                            'mode' => $model->isNewRecord ? 'code' : 'form'
                        ],
                        //'collapseAll' => ['view'],
                        'expandAll' => ['tree', 'form'],
                        'value' => $model->tl_params_json ? json_encode($model->tl_params_json) : ''
                    ]
                );
            } catch (Exception $exception) {
                echo $form->field($model, 'tl_params_json')
                    ->textarea(['rows' => 6, 'value' => json_encode($model->tl_params_json)]);
            }

            ?>

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?= Html::submitButton(
                    '<i class="fa fa-save"></i> Save Task',
                    ['class' => 'btn btn-success', 'id' => 'btn-submit']
                ) ?>
            </div>
        </div>
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
    
    $('body').on('change', '#tasklist-tl_object', function(e) {
        var value = $(this).val();
        $.pjax.reload({container: '#pjax-task-list-form', push: false, replace: false, timeout: 5000, data: {object: value}});
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