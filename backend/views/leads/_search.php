<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-3">

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'id')->input('number', ['min' => 1])->label('Lead Id') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'uid')->label('Lead UID') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'discount_id')->input('number', ['min' => 1]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'client_email') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'client_phone')
                        ->widget(\borales\extensions\phoneInput\PhoneInput::className(), [
                            'options' => [
                                'class' => 'form-control'
                            ],
                            'jsOptions' => [
                                'preferredCountries' => ['us'],
                            ]
                        ]) ?>
                </div>
            </div>

        </div>

        <div class="col-md-3">

            <div class="row">
                <div class="col-md-6">
                    <?php echo $form->field($model, 'trip_type')->dropDownList(\common\models\Lead::TRIP_TYPE_LIST, ['prompt' => '-']) ?>
                </div>
                <div class="col-md-6">
                    <?php echo $form->field($model, 'cabin')->dropDownList(\common\models\Lead::CABIN_LIST, ['prompt' => '-']) ?>
                </div>
            </div>
            <?php if (Yii::$app->user->identity->role != 'agent') : ?>
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'source')
                            ->widget(\kartik\select2\Select2::classname(), [
                                'data' => \common\models\Source::getGroupList(),
                                'options' => ['placeholder' => 'All'],
                                'pluginOptions' => ['multiple' => true, 'allowClear' => true],
                            ])
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-3">

            <?= $form->field($model, 'statuses')
                ->widget(\kartik\select2\Select2::class, [
                    'data' => \common\models\Lead::STATUS_LIST,
                    'size' => \kartik\select2\Select2::SMALL,
                    'options' => ['placeholder' => 'Select status', 'multiple' => true],
                    'pluginOptions' => ['allowClear' => true],
                ])
            ?>

            <?php if (Yii::$app->user->identity->role != 'agent') : ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'created_date_from')->widget(
                            \dosamigos\datepicker\DatePicker::class, [
                            'inline' => false,
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'dd-M-yyyy',
                                'todayBtn' => true
                            ]
                        ]); ?>

                    </div>

                    <div class="col-md-6">
                        <?= $form->field($model, 'created_date_to')->widget(
                            \dosamigos\datepicker\DatePicker::class, [
                            'inline' => false,
                            //'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
                            'clientOptions' => [
                                'autoclose' => true,
                                'format' => 'dd-M-yyyy',
                                'todayBtn' => true
                            ]
                        ]); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?php echo $form->field($model, 'rating')->dropDownList(array_combine(range(1, 3), range(1, 3)), ['prompt' => '-']) ?>
                </div>

                <div class="col-md-6">
                    <?php echo $form->field($model, 'bo_flight_id')->label('BO Sale ID') ?>
                </div>
            </div>
            <?php if (Yii::$app->user->identity->role != 'agent') : ?>
                <div class="row">
                    <div class="col-md-6">
                        <?php echo $form->field($model, 'employee_id')->dropDownList(\common\models\Employee::getList(), ['prompt' => '-']) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'request_ip')
                            ->widget(\yii\widgets\MaskedInput::class, [
                                'clientOptions' => [
                                    'alias' => 'ip'
                                ],
                            ]) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <br>
            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-search"></i> Search leads', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('<i class="fa fa-close"></i> Reset form', ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
