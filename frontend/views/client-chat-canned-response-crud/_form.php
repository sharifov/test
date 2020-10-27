<?php

use common\models\Project;
use sales\model\clientChat\cannedResponseCategory\entity\ClientChatCannedResponseCategory;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\cannedResponse\entity\ClientChatCannedResponse */
/* @var $form ActiveForm */
?>

<div class="client-chat-canned-response-form">

    <div class="col-md-2">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cr_project_id')->dropDownList(Project::getList(), ['prompt' => '--']) ?>

        <?= $form->field($model, 'cr_category_id')->dropDownList(ClientChatCannedResponseCategory::getList(), ['prompt' => '--']) ?>

        <?= $form->field($model, 'cr_language_id')->dropDownList(\common\models\Language::getList(), ['prompt' => '--']) ?>

        <?= $form->field($model, 'cr_user_id')->widget(\sales\widgets\UserSelect2Widget::class) ?>

        <?= $form->field($model, 'cr_sort_order')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

    </div>

    <div class="col-md-4">
        <?= $form->field($model, 'cr_message')->textarea(['maxlength' => true, 'rows' => 10]) ?>
    </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
