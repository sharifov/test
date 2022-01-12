<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\caseOrder\entity\search\CaseOrderSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="case-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'co_order_id') ?>

    <?= $form->field($model, 'co_case_id') ?>

    <?= $form->field($model, 'co_create_dt') ?>

    <?= $form->field($model, 'co_created_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
