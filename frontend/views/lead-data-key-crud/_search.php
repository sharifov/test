<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\leadDataKey\entity\LeadDataKeySearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="lead-data-key-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ldk_id') ?>

    <?= $form->field($model, 'ldk_key') ?>

    <?= $form->field($model, 'ldk_name') ?>

    <?= $form->field($model, 'ldk_enable') ?>

    <?= $form->field($model, 'ldk_created_dt') ?>

    <?php // echo $form->field($model, 'ldk_updated_dt') ?>

    <?php // echo $form->field($model, 'ldk_created_user_id') ?>

    <?php // echo $form->field($model, 'ldk_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
