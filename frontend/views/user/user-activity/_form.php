<?php

use kartik\select2\Select2;
use modules\user\userActivity\entity\UserActivity;
use src\widgets\DateTimePicker;
use src\widgets\UserSelect2Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\user\userActivity\entity\UserActivity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-activity-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'ua_user_id')->widget(UserSelect2Widget::class, [
                    'data' => $model->ua_user_id ? [
                        $model->ua_user_id => \src\auth\Auth::user()->username
                    ] : [],
                ]) ?>

                 <?php /*= $form->field($model, 'ua_user_id')->textInput()*/ ?>
            </div>
        </div>



    <div class="row">
        <div class="col-md-6">
            <?php /*= $form->field($model, 'ua_object_event')->dropDownList(Yii::$app->event->objectList, ['prompt' => '-'])*/ ?>

            <?= $form->field($model, 'ua_object_event')->widget(Select2::class, [
                'options' => [
                    'placeholder' => '--', //$searchFrom->getAttributeLabel('fareType'),
                    'multiple' => false,
                    'id' => 'el_key',
                ],
                'data' => Yii::$app->event->getObjectEventList(),
                'size' => Select2::SIZE_SMALL
            ]) ?>


        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'ua_object_id')->input('number', ['min' => 0]) ?>
        </div>
    </div>




        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'ua_start_dt')->widget(DateTimePicker::class) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'ua_end_dt')->widget(DateTimePicker::class) ?>
            </div>
        </div>

    <?= $form->field($model, 'ua_type_id')
        ->dropDownList(UserActivity::getTypeList(), ['prompt' => '-']) ?>
    <?= $form->field($model, 'ua_shift_event_id')->input('number', ['min' => 0]) ?>

    <?= $form->field($model, 'ua_description')->textarea() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
