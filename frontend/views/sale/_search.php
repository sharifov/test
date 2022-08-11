<?php

use modules\cases\src\abac\saleList\SaleListAbacObject;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\SaleSearch */
/* @var $form yii\widgets\ActiveForm */


?>

<div class="lead-search">
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
        </li>*/ ?>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block">
            <?php $form = ActiveForm::begin([
                'action' => ['search'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>


            <div class="row">
                <div class="col-md-4">
                    <div class="row">
                       <?php /** @abac null, SaleListAbacObject::UI_SALE_ID, SaleListAbacObject::ACTION_READ, hide SalesID for certain roles */?>
                        <?php if (Yii::$app->abac->can(null, SaleListAbacObject::UI_SALE_ID, SaleListAbacObject::ACTION_READ)) : ?>
                            <div class="col-md-4">
                                <?= $form->field($model, 'sale_id')->input('number', ['min' => 1]) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'ticket_number')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'pnr')->textInput(['maxlength' => true]) ?>
                            </div>
                        <?php else : ?>
                            <div class="col-md-6">
                                <?= $form->field($model, 'ticket_number')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'pnr')->textInput(['maxlength' => true]) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'acn')->textInput(['maxlength' => true]) ?>
                        </div>

                        <div class="col-md-6">
                            <?= $form->field($model, 'booking_id')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'email')->input('email') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'phone', ['enableClientValidation' => false])->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
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
    </div>

</div>
