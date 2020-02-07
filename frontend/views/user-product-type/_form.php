<?php

use common\models\Employee;
use modules\product\src\entities\productType\ProductType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserProductType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-product-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'upt_user_id')->dropDownList(Employee::getList(), ['prompt' => '-']) ?>

    <?= $form->field($model, 'upt_product_type_id')->dropDownList(ProductType::getList(), ['prompt' => '-']) ?>
    <?= $form->field($model, 'upt_commission_percent')->input('number', ['step' => 0.01]) ?>
    <?= $form->field($model, 'upt_product_enabled')->checkbox()?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
