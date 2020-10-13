<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\projectConfig\ClientChatProjectConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-project-config-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
    <div class="col-md-2">
        <?= $form->field($model, 'ccpc_project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '-']) ?>
    </div>
    </div>
    <div class="row">
    <div class="col-md-6">
        <?php

        try {
            echo $form->field($model, 'ccpc_params_json')->widget(
                \kdn\yii2\JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form', 'tree'], //'text',
                        'mode' => $model->isNewRecord ? 'code' : 'form'
                    ],
                    //'collapseAll' => ['view'],
                    'expandAll' => ['tree', 'form'],
                ]
            );
        } catch (Exception $exception) {
            echo $form->field($model, 'ccpc_params_json')->textarea(['rows' => 6]);
        }

        ?>

        <?php

        try {
            echo $form->field($model, 'ccpc_theme_json')->widget(
                \kdn\yii2\JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form', 'tree'], //'text',
                        'mode' => $model->isNewRecord ? 'code' : 'form'
                    ],
                    //'collapseAll' => ['view'],
                    'expandAll' => ['tree', 'form'],
                ]
            );
        } catch (Exception $exception) {
            echo $form->field($model, 'ccpc_theme_json')->textarea(['rows' => 6]);
        }

        ?>

    </div>

    <div class="col-md-6">

        <?php

//        try {
//            echo $form->field($model, 'ccpc_registration_json')->widget(
//                \kdn\yii2\JsonEditor::class,
//                [
//                    'clientOptions' => [
//                        'modes' => ['code', 'form', 'tree'], //'text',
//                        'mode' => $model->isNewRecord ? 'code' : 'form'
//                    ],
//                    //'collapseAll' => ['view'],
//                    'expandAll' => ['tree', 'form'],
//                ]
//            );
//        } catch (Exception $exception) {
//            echo $form->field($model, 'ccpc_registration_json')->textarea(['rows' => 6]);
//        }

        ?>

        <?php

//        try {
//            echo $form->field($model, 'ccpc_settings_json')->widget(
//                \kdn\yii2\JsonEditor::class,
//                [
//                    'clientOptions' => [
//                        'modes' => ['code', 'form', 'tree'], //'text',
//                        'mode' => $model->isNewRecord ? 'code' : 'form'
//                    ],
//                    //'collapseAll' => ['view'],
//                    'expandAll' => ['tree', 'form'],
//                ]
//            );
//        } catch (Exception $exception) {
//            echo $form->field($model, 'ccpc_settings_json')->textarea(['rows' => 6]);
//        }

        ?>

    </div>
    </div>
    <div class="row">
    <div class="col-md-12">
        <?= $form->field($model, 'ccpc_enabled')->checkbox() ?>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
