<?php

use kdn\yii2\JsonEditor;
use modules\objectTask\src\services\ObjectTaskService;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\objectTask\src\entities\ObjectTaskScenario */
/* @var $form yii\widgets\ActiveForm */

if (!empty($model->ots_data_json)) {
    $model->ots_data_json = Json::encode($model->ots_data_json);
}
?>

<div class="object-task-scenario-form row">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'col-6']]); ?>

    <?= $form->field($model, 'ots_key')->dropDownList(ObjectTaskService::SCENARIO_LIST) ?>

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

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <div class="col-6">
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
