<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\SaleSearch */
/* @var $form yii\widgets\ActiveForm */


//$isAgent = Yii::$app->authManager->getAssignment('agent', Yii::$app->user->id);

?>

<div class="lead-search">
    <p class="text-warning"><i class="fa fa-info-circle"></i> For searching the exact matches of the fields are used</p>

    <?php $form = ActiveForm::begin([
        'action' => ['search'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-3">

            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'sale_id')->input('number', ['min' => 1]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'pnr')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-3">
                    <?//= $form->field($model, 'gid')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

        </div>

        <div class="col-md-3">

            <div class="row">
                <div class="col-md-4">
                    <?//= $form->field($model, 'client_id')->input('number', ['min' => 1]) ?>
                </div>
                <div class="col-md-8">
                    <?= $form->field($model, 'last_name') ?>
                </div>
            </div>

        </div>

        <div class="col-md-3">


            <?php  //echo $form->field($model, 'project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '-']) ?>

            <?php  //echo $form->field($model, 'source_id')->dropDownList(\common\models\Source::getList(), ['prompt' => '-']) ?>


            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'email')->input('email') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'phone') ?>
                </div>
            </div>
            <?//= $form->field($model, 'employee_id') ?>
        </div>

    </div>

    <div class="row">
        <div class="col-md-12">
            <br>
            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-search"></i> Search sales', ['class' => 'btn btn-primary']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset data', ['sale/search'], ['class' => 'btn btn-warning']) ?>
                <?php //= Html::resetButton('<i class="fa fa-close"></i> Reset form', ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
