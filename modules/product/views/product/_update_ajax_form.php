<?php

use modules\product\src\forms\ProductUpdateForm;
use sales\yii\bootstrap4\ActiveForm;
use sales\yii\bootstrap4\ClientBeforeSubmit;
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
                '$.pjax.reload({container: \'#pjax-product-\' + \'' . $model->productId . '\'}); ',
                null
            ),
        ]);
        ?>

        <?= $form->field($model, 'pr_name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'pr_description')->textarea(['rows' => 4]) ?>

        <div class="form-group text-center">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?php
