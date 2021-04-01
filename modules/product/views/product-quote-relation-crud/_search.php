<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteRelation\search\ProductQuoteRelationSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="product-quote-relation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pqr_parent_pq_id') ?>

    <?= $form->field($model, 'pqr_related_pq_id') ?>

    <?= $form->field($model, 'pqr_type_id') ?>

    <?= $form->field($model, 'pqr_created_user_id') ?>

    <?= $form->field($model, 'pqr_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
