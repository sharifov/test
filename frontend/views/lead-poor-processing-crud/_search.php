<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadPoorProcessing\entity\LeadPoorProcessingSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="lead-poor-processing-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'lpp_lead_id') ?>

    <?= $form->field($model, 'lpp_lppd_id') ?>

    <?= $form->field($model, 'lpp_expiration_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
