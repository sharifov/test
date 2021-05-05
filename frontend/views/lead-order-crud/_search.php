<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\leadOrder\entity\search\LeadOrderSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="lead-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'lo_order_id') ?>

    <?= $form->field($model, 'lo_lead_id') ?>

    <?= $form->field($model, 'lo_create_dt') ?>

    <?= $form->field($model, 'lo_created_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
