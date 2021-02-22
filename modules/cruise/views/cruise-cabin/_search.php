<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruise\search\CruiseCabinSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="cruise-cabin-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'crc_id') ?>

    <?= $form->field($model, 'crc_cruise_id') ?>

    <?= $form->field($model, 'crc_name') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
