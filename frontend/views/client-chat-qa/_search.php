<?php

use sales\model\clientChat\entity\search\ClientChatQaSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var ClientChatQaSearch $model */
/* @var yii\widgets\ActiveForm $form */
?>

<div class="client-chat-search">

    <?php /* TODO::  */ ?>

    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>

            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: <?=(Yii::$app->request->isPjax) ? 'block' : 'none'?>">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => [
                    'data-pjax' => 1
                ],
            ]); ?>

            <div class="row">
                <div class="col-md-2">
                    <?php echo $form->field($model, 'cch_id') ?>
                    <?php echo $form->field($model, 'cch_rid') ?>
                </div>

            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group text-center">
                        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
                    </div>
                </div>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
