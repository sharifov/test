<?php

use frontend\helpers\JsonHelper;
use modules\product\src\entities\productType\ProductType;
use src\helpers\app\AppHelper;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\coupon\entity\couponProduct\CouponProduct */
/* @var $form ActiveForm */
?>

<div class="coupon-product-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cup_coupon_id')->textInput() ?>

        <?= $form->field($model, 'cup_product_type_id')->dropDownList(ProductType::getList()) ?>

<?php
    $model->cup_data_json = JsonHelper::encode($model->cup_data_json);
try {
    echo $form->field($model, 'cup_data_json')->widget(
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
        echo $form->field($model, 'cup_data_json')->textarea(['rows' => 8, 'class' => 'form-control']);
    } catch (Throwable $throwable) {
        Yii::error(AppHelper::throwableFormatter($throwable), 'CouponProductCrudController:_form:notValidJson');
    }
}
?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
