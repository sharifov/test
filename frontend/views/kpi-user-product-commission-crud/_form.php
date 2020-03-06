<?php

use common\models\Employee;
use modules\product\src\entities\productType\ProductType;
use sales\helpers\DateHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\kpi\entity\kpiUserProductCommission\KpiUserProductCommission */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kpi-user-product-commission-form">

    <?php \yii\widgets\Pjax::begin() ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'upc_product_type_id')->dropDownList(ProductType::getList(), ['prompt' => '---']) ?>

            <?= $form->field($model, 'upc_user_id')->dropDownList(Employee::getList(), ['prompt' => '---']) ?>

			<?= $form->field($model, 'upc_year')->input('number', ['step' => 1]) ?>

			<?= $form->field($model, 'upc_month')->dropDownList(DateHelper::getMonthList(), ['prompt' => '--']) ?>

			<?= $form->field($model, 'upc_performance')->input('number', ['step' => 1]) ?>

			<?= $form->field($model, 'upc_commission_percent')->input('number', ['step' => 1]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php \yii\widgets\Pjax::end() ?>

</div>
