<?php

use frontend\helpers\JsonHelper;
use sales\helpers\app\AppHelper;
use sales\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo */
/* @var $form ActiveForm */
?>

<div class="contact-phone-service-info-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cpsi_cpl_id')->textInput() ?>

        <?= $form->field($model, 'cpsi_service_id')->dropDownList(ContactPhoneServiceInfo::SERVICE_LIST) ?>

<?php
    $model->cpsi_data_json = JsonHelper::encode($model->cpsi_data_json);
try {
    echo $form->field($model, 'cpsi_data_json')->widget(
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
        echo $form->field($model, 'cpsi_data_json')->textarea(['rows' => 8, 'class' => 'form-control']);
    } catch (Throwable $throwable) {
        Yii::error(AppHelper::throwableFormatter($throwable), 'ContactPhoneServiceInfoCrudController:_form:notValidJson');
    }
}
?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
