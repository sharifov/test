<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogUserAccess\search\CallLogUserAccessSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="call-log-user-access-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'clua_cl_id') ?>

    <?= $form->field($model, 'clua_user_id') ?>

    <?= $form->field($model, 'clua_access_status_id') ?>

    <?= $form->field($model, 'clua_access_start_dt') ?>

    <?= $form->field($model, 'clua_access_finish_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
