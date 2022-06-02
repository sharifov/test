<?php

use kartik\select2\Select2;
use modules\taskList\src\entities\taskList\TaskList;
use modules\taskList\src\entities\TaskObject;
use modules\taskList\src\objects\BaseTaskObject;
use modules\taskList\src\services\TaskListService;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\taskList\TaskList */
/* @var $form yii\widgets\ActiveForm */

\frontend\assets\QueryBuilderAsset::register($this);

//$rulesData = @json_decode($model->tl_condition_json);

//\yii\helpers\VarDumper::dump($model->tl_condition_json); exit;
$model->tl_condition_json = Json::encode($model->tl_condition_json);

//$rulesDataStr = \yii\helpers\Json::encode($model->tl_condition_json);
$rulesDataStr = $model->tl_condition_json;
//$filtersData = $model->getObjectAttributeList();

$operators = json_encode(BaseTaskObject::getOperators());
if (!empty($model->tl_object)) {
    $filtersData = TaskObject::getAttributeListByObject($model->tl_object);
    $defaultOptionList = TaskListService::getOptionListByObject($model->tl_object);
} else {
    $filtersData = [];
    $defaultOptionList = [];
}
$filtersDataStr = json_encode($filtersData);
//  \yii\helpers\VarDumper::dump($targetObjectList, 10, true);

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
                <div class="col-md-6">
                    <?= $form->field($model, 'tl_enable_type')
                        ->dropDownList(
                            TaskList::getEnableTypeList(),
                            ['prompt' => '-']
                        ) ?>
                </div>
            </div>

            <?= $form->field($model, 'tl_title')->textInput(['maxlength' => true]) ?>


            <?php /* $form->field($model, 'tl_object')
                ->dropDownList(TaskObject::getObjectList(), ['prompt' => '-'])*/ ?>

            <?php /*
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'tl_work_start_time_utc')->input('time') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'tl_work_end_time_utc')->input('time') ?>
                </div>
            </div> */ ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'tl_duration_min')->input('number', ['min' => 0, 'step' => 1]) ?>
                </div>
                <div class="col-md-6">
                </div>
            </div>


            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'tl_cron_expression')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'tl_sort_order')->dropDownList(array_combine(range(0, 100), range(0, 100))) ?>
                    <?php /* $form->field($model, 'tl_sort_order')->input('number', ['min' => 0, 'step' => 1])*/ ?>
                </div>
            </div>




        </div>
        <div class="col-md-6">

            <?php Pjax::begin(['id' => 'pjax-task-list-form']); ?>

                <?php if ($model->tl_object) : ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?php $targetObjectList = TaskListService::getTargetObjectListByObject($model->tl_object); ?>
                            <?= $form->field($model, 'tl_target_object_id')->dropDownList($targetObjectList, ['prompt' => '---']) ?>
                        </div>
                    </div>


                    <h3>Task object "<?php echo Html::encode($model->tl_object) ?>"</h3>

                    <h2>Rules / Conditions</h2>
                    <?php echo Html::a(
                        'Show / hide Attribute List',
                        null,
                        ['class' => 'btn btn-sm btn-default', 'id' => 'btn-div-attr-list']
                    ) ?>
                    <div id="div-attr-list" style="display: none">
                        <pre><?php \yii\helpers\VarDumper::dump($filtersData, 10, true)?></pre>
                    </div>

                    <?php if ($filtersData) : ?>
                        <div id="builder" style="width: 100%"></div>
                        <br>
                        <?php echo Html::a(
                            'Show / hide JSON rules',
                            null,
                            ['class' => 'btn btn-sm btn-default', 'id' => 'btn-div-json-rules']
                        ) ?>
                        <?=Html::button(
                            '<i class="fa fa-check-square-o"></i> Validate rules',
                            ['class' => 'btn btn-sm btn-warning', 'id' => 'btn-getcode']
                        )?>

                        <div id="div-json-rules" style="display: none">
                            <?= $form->field($model, 'tl_condition_json')
                                ->textarea(['rows' => 8, 'id' => 'tl_condition_json', 'readonly' => true]) ?>
                        </div>
                    <?php else : ?>
                        <div class="alert alert-warning" role="alert">
                            <strong>Warning</strong>: ATTRIBUTE list (Filter data) for this object is empty!
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?php

                            try {
                                echo $form->field($model, 'tl_params_json')->widget(
                                    \kdn\yii2\JsonEditor::class,
                                    [
                                        'clientOptions' => [
                                            'modes' => ['code', 'form', 'tree', 'view'], //'text',
                                            'mode' => $model->isNewRecord ? 'form' : 'code'
                                        ],
                                        //'collapseAll' => ['view'],
                                        'expandAll' => ['tree', 'form'],
                                        'value' => $model->tl_params_json ?
                                            Json::encode($model->tl_params_json) : Json::encode([])
                                    ]
                                );
                            } catch (Exception $exception) {
                                echo $form->field($model, 'tl_params_json')
                                    ->textarea(['rows' => 6,
                                        'value' => Json::encode($model->tl_params_json)]);
                                echo '<div class="danger">' . $exception->getMessage() . '</div>';
                            }

                            ?>
                        </div>
                        <div class="col-md-6">
                            <?php if ($defaultOptionList) : ?>
                                <h4>Default "<?=Html::encode($model->tl_object)?>" params:</h4>
                                <table class="table table-bordered table-hover">

                                    <tr>
                                        <th>Key</th>
                                        <th>Label</th>
                                        <th>Type</th>
                                        <th>Default</th>
                                    </tr>
                                
                                <?php foreach ($defaultOptionList as $optKey => $optItem) : ?>
                                    <tr>
                                        <td>
                                            <?= Html::encode($optKey)?>
                                        </td>
                                        <td>
                                            <?= Html::encode($optItem['label'] ?? '')?>
                                        </td>
                                        <td>
                                            <?= Html::encode($optItem['type'] ?? '')?>
                                        </td>
                                        <td>
                                            <?= Html::encode($optItem['value'] ?? '')?>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>

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

            <?php /*= $form->field($model, 'tl_condition')->textInput(['maxlength' => true])*/ ?>

            <?php /*= $form->field($model, 'tl_condition_json')->textInput()*/ ?>




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
            $('#tl_condition_json').val(json);
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