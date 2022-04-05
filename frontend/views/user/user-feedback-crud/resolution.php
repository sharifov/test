<?php

use modules\user\userFeedback\entity\UserFeedback;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\user\userFeedback\entity\UserFeedback */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Resolve User Feedback: ' . $model->uf_id;
$this->params['breadcrumbs'][] = ['label' => 'User Feedbacks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->uf_id, 'url' => ['view', 'uf_id' => $model->uf_id, 'uf_created_dt' => $model->uf_created_dt]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-feedback-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-feedback-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
        <?= $form->field($model, 'uf_status_id')->dropDownList(UserFeedback::FINAL_STATUS_LIST, ['prompt' => '---']) ?>
        <?= $form->field($model, 'uf_resolution')->textarea(['rows' => 6, 'maxlength' => 500]) ?>
        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

</div>
