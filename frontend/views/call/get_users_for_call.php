<?php

use common\models\Call;
use sales\model\call\useCase\assignUsers\UsersForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/** @var UsersForm $model */
/** @var Call $call */

?>

<div class="users-form">
    <h5>Call ID: <?= $call->c_id ?> (<?= $call->getCallTypeName() ?>) </h5>
    <p></p>

    <?php Pjax::begin(['enableReplaceState' => false, 'enablePushState' => false]) ?>

        <?php if ($users = $model->getRenderedUsers()): ?>

            <?php $form = ActiveForm::begin([
                'action' => Url::to(['/call/get-users-for-call', 'id' => $call->c_id]),
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>

            <?= $form->errorSummary($model) ?>

            <?= $form->field($model, 'selectedUsers')->checkboxList($users, [
                'class' => 'row',
                'item' => function ($index, $label, $name, $checked, $value) {
                    return "<label class='col-md-6'><input type='checkbox' {$checked} name='{$name}' value='{$value}'> {$label}</label>";
                }
            ])->label(false)->error(false) ?>

            <div class="form-group">
                <?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        <?php else: ?>
            Users not found
        <?php endif;?>

    <?php Pjax::end() ?>

</div>
