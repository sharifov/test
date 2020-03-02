<?php

use modules\product\src\entities\productType\ProductType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model sales\model\kpi\entity\kpiProductCommission\KpiProductCommission */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kpi-product-commission-form">


    <?php Pjax::begin() ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax'=> 1]]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'pc_product_type_id')->dropDownList(ProductType::getList(), ['prompt' => '---']) ?>

            <?= $form->field($model, 'pc_performance')->input('number', ['step' => 1]) ?>

            <?= $form->field($model, 'pc_commission_percent')->input('number', ['step' => 1]) ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end() ?>

</div>
