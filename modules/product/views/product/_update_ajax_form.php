<?php

use modules\product\src\useCases\product\update\ProductUpdateForm;
use sales\yii\bootstrap4\activeForm\ActiveForm;
use sales\yii\bootstrap4\activeForm\ClientBeforeSubmit;
use yii\helpers\Html;

/* @var yii\web\View $this */
/* @var ProductUpdateForm $model */
/* @var ActiveForm $form */

$formId = 'product-update-form';
$modalId = 'modal-sm';

?>

    <div class="product-form">

        <?php
        $form = ActiveForm::begin([
            'id' => $formId,
            'action' => ['/product/product/update-ajax?id=' . $model->productId],
            'clientBeforeSubmit' => new ClientBeforeSubmit(
                    'Product update',
                    true,
                    'modal-sm',
                'pjaxReload({container: \'#pjax-product-\' + \'' . $model->productId . '\'}); ',
                null,
                null
            ),
        ]);
        ?>

        <?= $form->field($model, 'pr_name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'pr_description')->textarea(['rows' => 4]) ?>

        <div class="row">
            <div class="col-md-6"><?= $form->field($model, 'pr_market_price')->input('number', ['min' => 0, 'max' => 999999, 'step' => 0.01]) ?></div>
            <div class="col-md-6"><?= $form->field($model, 'pr_client_budget')->input('number', ['min' => 0, 'max' => 999999, 'step' => 0.01]) ?></div>
        </div>

        <div class="form-group text-center">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?php
