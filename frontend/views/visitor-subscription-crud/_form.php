<?php

use sales\model\visitorSubscription\entity\VisitorSubscription;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\visitorSubscription\entity\VisitorSubscription */
/* @var $form ActiveForm */
?>

<div class="visitor-subscription-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'vs_subscription_uid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'vs_type_id')->dropDownList(VisitorSubscription::getSubscriptionListName(), [
            'prompt' => '---'
        ]) ?>

        <?= $form->field($model, 'vs_enabled')->checkbox() ?>

        <?= $form->field($model, 'vs_expired_date')->widget(\kartik\date\DatePicker::class, [
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'autoclose' => true,
                'todayHighlight' => true
            ],
            'value' => date('Y-m-d')
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
