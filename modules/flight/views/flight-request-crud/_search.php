<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightRequestSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="flight-request-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fr_id') ?>

    <?= $form->field($model, 'fr_hash') ?>

    <?= $form->field($model, 'fr_type_id') ?>

    <?= $form->field($model, 'fr_data_json') ?>

    <?= $form->field($model, 'fr_created_api_user_id') ?>

    <?php // echo $form->field($model, 'fr_status_id') ?>

    <?php // echo $form->field($model, 'fr_job_id') ?>

    <?php // echo $form->field($model, 'fr_created_dt') ?>

    <?php // echo $form->field($model, 'fr_updated_dt') ?>

    <?php // echo $form->field($model, 'fr_year') ?>

    <?php // echo $form->field($model, 'fr_month') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
