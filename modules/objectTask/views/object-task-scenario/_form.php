<?php

use frontend\assets\QueryBuilderAsset;
use kdn\yii2\JsonEditor;
use modules\objectTask\src\services\ObjectTaskService;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model modules\objectTask\src\entities\ObjectTaskScenario */
/* @var $form yii\widgets\ActiveForm */

if (!empty($model->ots_data_json)) {
    $model->ots_data_json = Json::encode($model->ots_data_json);
}

QueryBuilderAsset::register($this);

$rules = ($model->ots_condition_json) ? Json::decode($model->ots_condition_json) : [];
$rulesJson = Json::encode($rules);
$filtersData = $model->getStatementAttributesForScenario();

$filtersDataStr = json_encode($filtersData);
$operators = json_encode(\modules\objectTask\src\scenarios\statements\BaseObject::getOperators());
?>
<style>
    .rules-group-container {width: 100%}
    .rule-value-container {display:inline-flex!important;}
</style>
<div class="object-task-scenario-form">
    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'ots_enable')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => '']) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'ots_key')->dropDownList(ObjectTaskService::SCENARIO_LIST, ['prompt' => '']) ?>
        </div>
    </div>

    <?php Pjax::begin(['id' => 'pjax-object-task-scenario-form']); ?>

    <div class="row">
        <div class="col-6">
            <?php
            try {
                echo $form->field($model, 'ots_data_json')->widget(JsonEditor::class, [
                    'expandAll' => ['tree', 'form'],
                    'clientOptions' => [
                        'modes' => ['code', 'form', 'tree', 'view'],
                        'mode' => 'tree',
                        'allowSchemaSuggestions' => true,
                    ],
                ]);
            } catch (\Throwable $exception) {
                echo $form->field($model, 'ots_data_json')->textarea();
            }
            ?>

            <?= $form->field($model, 'ots_condition_json')->hiddenInput(['id' => 'ots_condition_json', 'maxlength' => true])->label(false) ?>
        </div>
        <div class="col-6">
            <label class="control-label">Statement condition</label>
            <div id="builder" class="w-100" style="width: 100%"></div>
            <?php
            $rules = $model->ots_condition_json ?? "null";
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

            <?php if (array_key_exists($model->ots_key, ObjectTaskService::SCENARIO_CLASS_LIST)) : ?>
                <h3>Parameters description</h3>
                <div>
                    <ul>
                    <?php foreach (ObjectTaskService::getParametersDescriptionForScenario($model->ots_key) as $key => $desc) : ?>
                        <li><?= $this->render('components/_scenario-description', [
                            'key' => $key,
                            'object' => $desc,
                        ]) ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <h3>Allowed commands</h3>
            <?php /** @var \modules\objectTask\src\commands\BaseCommand $classname */ ?>
            <?php foreach (ObjectTaskService::COMMAND_CLASS_LIST as $command => $classname) : ?>
                <details>
                    <summary class="font-weight-bold"><?= $command ?></summary>
                    <pre>
                    <?php \yii\helpers\VarDumper::dump(
                        $classname::getConfigTemplate(),
                        10,
                        true
                    ) ?>
                </pre>
                </details>
            <?php endforeach; ?>
        </div>
    </div>
    <?php Pjax::end() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'id' => 'btn-submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<?php
$jsCode2 = <<<JS
    $('#btn-submit').on('click', function() {
      if(!getBuilder()) return false;
    });

    function getBuilder()
    {
        var result = $('#builder').queryBuilder('getRules');
        if (!$.isEmptyObject(result)) {
            var json = JSON.stringify(result, null);
            $('#ots_condition_json').val(json);
            if(result.valid) return true;
        }
        return false;
    }
    
    $('body').on('change', '#objecttaskscenario-ots_key', function(e) {
        var value = $(this).val();
        $('#ots_condition_json').val('');
        $.pjax.reload({container: '#pjax-object-task-scenario-form', push: false, replace: false, timeout: 5000, data: {key: value}});
    });
JS;

$this->registerJs($jsCode2, \yii\web\View::POS_READY);
