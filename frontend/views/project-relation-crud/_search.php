<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\project\entity\projectRelation\search\ProjectRelationSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="project-relation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'prl_project_id') ?>

    <?= $form->field($model, 'prl_related_project_id') ?>

    <?= $form->field($model, 'prl_created_user_id') ?>

    <?= $form->field($model, 'prl_updated_user_id') ?>

    <?= $form->field($model, 'prl_created_dt') ?>

    <?php // echo $form->field($model, 'prl_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
