<?php

use frontend\helpers\JsonHelper;
use modules\flight\models\FlightRequest;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightRequest */
/* @var $form ActiveForm */
?>

<div class="flight-request-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fr_booking_id')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fr_hash')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fr_type_id')->dropDownList(FlightRequest::TYPE_LIST) ?>

        <?= $form->field($model, 'fr_status_id')->dropDownList(FlightRequest::STATUS_LIST) ?>

        <?= $form->field($model, 'fr_project_id')->dropDownList(\common\models\Project::getList()) ?>

        <?= $form->field($model, 'fr_created_api_user_id')->textInput() ?>

        <?= $form->field($model, 'fr_job_id')->textInput() ?>

<?php
$model->fr_data_json = JsonHelper::encode($model->fr_data_json);
try {
    echo $form->field($model, 'fr_data_json')->widget(
        \kdn\yii2\JsonEditor::class,
        [
            'clientOptions' => [
                'modes' => ['code', 'form', 'tree'],
                'mode' => 'code',
            ],
            'expandAll' => ['tree', 'form']
        ]
    );
} catch (Exception $exception) {
    echo $form->field($model, 'fr_data_json')->textarea(['rows' => 6]);
}
?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
