<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\clientVisitor\entity\ClientVisitorSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-visitor-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cv_id') ?>

    <?= $form->field($model, 'cv_client_id') ?>

    <?= $form->field($model, 'cv_visitor_id') ?>

    <?= $form->field($model, 'cv_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
