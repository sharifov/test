<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\clientDataKey\entity\ClientDataKeySearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-data-key-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cdk_id') ?>

    <?= $form->field($model, 'cdk_key') ?>

    <?= $form->field($model, 'cdk_name') ?>

    <?= $form->field($model, 'cdk_description') ?>

    <?= $form->field($model, 'cdk_enable') ?>

    <?php // echo $form->field($model, 'cdk_is_system') ?>

    <?php // echo $form->field($model, 'cdk_created_dt') ?>

    <?php // echo $form->field($model, 'cdk_updated_dt') ?>

    <?php // echo $form->field($model, 'cdk_created_user_id') ?>

    <?php // echo $form->field($model, 'cdk_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
