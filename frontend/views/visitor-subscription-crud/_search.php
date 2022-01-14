<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\visitorSubscription\entity\search\VisitorSubscriptionSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="visitor-subscription-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'vs_id') ?>

    <?= $form->field($model, 'vs_subscription_uid') ?>

    <?= $form->field($model, 'vs_type_id') ?>

    <?= $form->field($model, 'vs_enabled') ?>

    <?= $form->field($model, 'vs_expired_date') ?>

    <?php // echo $form->field($model, 'vs_created_dt') ?>

    <?php // echo $form->field($model, 'vs_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
