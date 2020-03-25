<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\EmployeeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employee-search">
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
        <div class="x_content" style="display: <?=(Yii::$app->request->isPjax || Yii::$app->request->get('EmployeeSearch') || Yii::$app->request->get('createTimeRange')) ? 'block' : 'none'?>">
            <?php $form = ActiveForm::begin([
                'action' => ['list'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>
            <div class="row">
            <div class="col-md-1">
                <?= $form->field($model, 'id') ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($model, 'username') ?>
            </div>

            <div class="col-md-2">
                <?php echo $form->field($model, 'email') ?>
            </div>

            <div class="col-md-1">
                <?php echo $form->field($model, 'status')->dropDownList([\common\models\Employee::STATUS_ACTIVE => 'Active', \common\models\Employee::STATUS_DELETED => 'Deleted'], ['prompt' => '---']) ?>
            </div>

            <div class="col-md-1">
                <?php echo $form->field($model, 'online')->dropDownList([1 => 'Online', 2 => 'Offline'], ['prompt' => '---']) ?>
            </div>

            <div class="col-md-2">
                <?php echo $form->field($model, 'user_group_id')->dropDownList(\common\models\UserGroup::getList(), ['prompt' => '---']) ?>
            </div>

            <div class="col-md-1">
                <?php echo $form->field($model, 'twoFaEnable')->dropDownList([1 => 'Enable', 0 => 'Disable'], ['prompt' => '---']) ?>
            </div>

            <?php // echo $form->field($model, 'status') ?>

            <?php // echo $form->field($model, 'last_activity') ?>

            <?php // echo $form->field($model, 'acl_rules_activated') ?>

            <?php // echo $form->field($model, 'created_at') ?>

            <?php // echo $form->field($model, 'updated_at') ?>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group text-center">
                        <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-primary']) ?>
                        <?= Html::resetButton('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['class' => 'btn btn-default']) ?>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
