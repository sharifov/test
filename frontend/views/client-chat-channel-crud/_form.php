<?php

use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatChannel\entity\ClientChatChannel */
/* @var $form ActiveForm */
?>

<div class="client-chat-channel-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ccc_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ccc_project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '---'])?>

        <?= $form->field($model, 'ccc_dep_id')->dropDownList(\common\models\Department::getList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'ccc_ug_id')->widget(\sales\widgets\UserGroupSelect2Widget::class, [
            'data' => $model->ccc_ug_id ? [
                $model->ccc_ug_id => $model->cccUg->ug_name
            ] : [],
        ]) ?>

        <?= $form->field($model, 'ccc_disabled')->checkbox() ?>

        <?= $form->field($model, 'ccc_priority')->input('number', ['min' => 0, 'max' => 255, 'step' => 1]) ?>

        <?php // $form->field($model, 'ccc_created_dt')->textInput() ?>

        <?php // $form->field($model, 'ccc_updated_dt')->textInput() ?>

        <?php // $form->field($model, 'ccc_created_user_id')->textInput() ?>

        <?php // $form->field($model, 'ccc_updated_user_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
