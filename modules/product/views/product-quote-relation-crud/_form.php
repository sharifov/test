<?php

use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var ProductQuoteRelation $model */
/* @var ActiveForm $form */
?>

<div class="product-quote-relation-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'pqr_parent_pq_id')->textInput() ?>

        <?= $form->field($model, 'pqr_related_pq_id')->textInput() ?>

        <?= $form->field($model, 'pqr_type_id')->dropDownList(ProductQuoteRelation::TYPE_LIST) ?>

        <?= $form->field($model, 'pqr_created_user_id')->dropDownList(\common\models\Employee::getList(), ['prompt' => '...']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
