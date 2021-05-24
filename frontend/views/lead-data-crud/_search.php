<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\leadData\entity\LeadDataSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="lead-data-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ld_id') ?>

    <?= $form->field($model, 'ld_lead_id') ?>

    <?= $form->field($model, 'ld_field_key') ?>

    <?= $form->field($model, 'ld_field_value') ?>

    <?= $form->field($model, 'ld_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
