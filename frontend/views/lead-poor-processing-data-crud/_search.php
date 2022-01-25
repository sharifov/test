<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="lead-poor-processing-data-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'lppd_id') ?>

    <?= $form->field($model, 'lppd_enabled') ?>

    <?= $form->field($model, 'lppd_key') ?>

    <?= $form->field($model, 'lppd_name') ?>

    <?= $form->field($model, 'lppd_description') ?>

    <?php // echo $form->field($model, 'lppd_minute') ?>

    <?php // echo $form->field($model, 'lppd_params_json') ?>

    <?php // echo $form->field($model, 'lppd_updated_dt') ?>

    <?php // echo $form->field($model, 'lppd_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
