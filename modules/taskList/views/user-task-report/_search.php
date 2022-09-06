<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\userTask\UserTaskSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-task-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?php echo $form->field($model, 'createTimeRange', [
                    'options' => ['class' => 'form-group']
                ])->widget(\kartik\daterange\DateRangePicker::class, [
                    'useWithAddon' => true,
                    'presetDropdown' => true,
                    'hideInput' => true,
                    'convertFormat' => true,
                    'startAttribute' => 'createTimeStart',
                    'endAttribute' => 'createTimeEnd',
                    'pluginOptions' => [
                        'locale' => [
                            //'format' => 'Y-m-d',
                            'format' => 'Y-m-d H:i',
                            'separator' => ' - '
                        ],
                        'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                    ],
                    //'id' => 'createTimeRange'
                ])->label('Created DateTime Range');
            ?>
        </div>
    </div>

    <?php
        // \yii\helpers\VarDumper::dump($model->createTimeRange, 20, true); exit();
        /* FOR DEBUG:: must by remove */
    ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
