<?php

use kdn\yii2\JsonEditor;
use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\entities\orderRequest\OrderRequest;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderRequest\OrderRequest */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>
<div class="order-request-form">
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'orr_source_type_id')->dropDownList(OrderSourceType::LIST); ?>

            <?= $form->field($model, 'orr_response_type_id')->dropDownList(OrderRequest::RESPONSE_TYPE_LIST) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'orr_request_data_json')->widget(JsonEditor::class, [
                'clientOptions' => [
                    'modes' => ['code', 'form', 'tree', 'view'], //'text',
                    'mode' => $model->isNewRecord ? 'code' : 'form'
                ],
                //'collapseAll' => ['view'],
                'expandAll' => ['tree', 'form'],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'orr_response_data_json')->widget(JsonEditor::class, [
                'clientOptions' => [
                    'modes' => ['code', 'form', 'tree', 'view'], //'text',
                    'mode' => $model->isNewRecord ? 'code' : 'form'
                ],
                //'collapseAll' => ['view'],
                'expandAll' => ['tree', 'form'],
            ]) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
