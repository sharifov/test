<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\requestControl\models\search\UserSiteActivitySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-site-activity-search">
    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>

            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>

                <?php /*<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-comment"></i></a>


                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>*/?>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: <?=(Yii::$app->request->isPjax || Yii::$app->request->get('UserSiteActivitySearch') || Yii::$app->request->get('createTimeRange')) ? 'block' : 'none'?>">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>
            <div class="row">
            <div class="col-md-3">

                <?php

                echo $form->field($model, 'createTimeRange', [
                    //'addon'=>['prepend'=>['content'=>'<i class="fa fa-calendar"></i>']],
                    'options' => ['class' => 'form-group']
                ])->widget(\kartik\daterange\DateRangePicker::class, [
                    'useWithAddon' => true,
                    'presetDropdown' => true,
                    'hideInput' => true,
                    'convertFormat' => true,
                    'startAttribute' => 'createTimeStart',
                    'endAttribute' => 'createTimeEnd',
                    'pluginOptions' => [
                        'timePicker' => true,
                        'timePickerIncrement' => 1,
                        'timePicker24Hour' => true,
                        'locale' => [
                            'format' => 'Y-m-d H:i',
                            'separator' => ' - '
                        ],
                        'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
                    ]
                ])->label('Created DateTime Range');
                ?>
            </div>

            <div class="col-md-1">
                <?= $form->field($model, 'usa_id') ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'usa_user_id', ['inputOptions' => [
                    'class' => 'form-control','id' => 'usaUserId'
                ]]) ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'usa_request_url') ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'usa_page_url') ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'usa_ip') ?>
            </div>

            <?php // echo $form->field($model, 'usa_request_type') ?>

            <?php // echo $form->field($model, 'usa_request_get') ?>

            <?php // echo $form->field($model, 'usa_request_post') ?>

            <?php // echo $form->field($model, 'usa_created_dt') ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group text-center">
                        <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-primary']) ?>
                        <?= Html::resetButton('<i class="fa fa-close"></i> Reset form', ['class' => 'btn btn-default']) ?>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
        </div>
    </div>
</div>
