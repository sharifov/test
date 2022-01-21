<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadRedial\entity\search\CallRedialUserAccessSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="call-redial-user-access-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'crua_lead_id') ?>

    <?= $form->field($model, 'crua_user_id') ?>

    <?= $form->field($model, 'crua_created_dt') ?>

    <?= $form->field($model, 'crua_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
