<?php

use common\models\Employee;
use kartik\select2\Select2;
use src\model\lead\reports\HeatMapLeadSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var HeatMapLeadSearch $model */
/* @var yii\widgets\ActiveForm $form */
?>

<div class="heat-map-lead-search">
    <?php /* TODO::  */ ?>
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>
            <ul class="nav navbar-right panel_toolbox" style="min-width: 0;">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <?php
            echo $this->render('_search', [
                'model' => $searchModel,
            ]);
            ?>
        </div>
    </div>
</div>

