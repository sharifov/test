<?php

use common\models\Project;
use frontend\helpers\JsonHelper;
use src\helpers\app\AppHelper;
use yii\bootstrap4\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var src\model\clientChatForm\entity\ClientChatForm $model */
/* @var ActiveForm $form */

?>

<div class="client-chat-form-form">

    <div class="col-md-6">

        <?php $form = ActiveForm::begin(); ?>

        <?php echo $form->field($model, 'ccf_key')->textInput(['maxlength' => true]) ?>

        <?php echo $form->field($model, 'ccf_name')->textInput(['maxlength' => true]) ?>

        <?php echo $form->field($model, 'ccf_project_id')->dropDownList(Project::getList()) ?>

        <?php
            $model->ccf_dataform_json = JsonHelper::encode($model->ccf_dataform_json);
        try {
            echo $form->field($model, 'ccf_dataform_json')->widget(
                \kdn\yii2\JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form'],
                        'mode' => $model->isNewRecord ? 'code' : 'form'
                    ],
                    'expandAll' => ['tree', 'form'],
                ]
            );
        } catch (Exception $exception) {
            try {
                echo $form->field($model, 'ccf_dataform_json')->textarea(['rows' => 8, 'class' => 'form-control']);
            } catch (Throwable $throwable) {
                Yii::error(AppHelper::throwableFormatter($throwable), 'ClientChatFormCrud:_form:notValidJson');
            }
        }
        ?>

        <?php echo $form->field($model, 'ccf_enabled')->checkbox() ?>

        <div class="form-group">
            <?php echo Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>


