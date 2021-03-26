<?php

use modules\order\src\entities\order\Order;
use modules\order\src\processManager\OrderProcessManager;
use modules\order\src\processManager\Status;
use modules\order\src\processManager\Type;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model OrderProcessManager */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="order-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-4">

        <?= $form->field($model, 'opm_id')->dropDownList(Order::find()->select(['or_name', 'or_id'])->asArray()->indexBy('or_id')->column(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'opm_status')->dropDownList(Status::LIST, ['prompt' => '---']) ?>

        <?= $form->field($model, 'opm_type')->dropDownList(Type::LIST, ['prompt' => '---']) ?>

        <?= $form->field($model, 'opm_created_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
