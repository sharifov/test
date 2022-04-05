<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CurrencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Currency List';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-index">

    <h1><i class="fa fa-usd"></i> <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Add Currency', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(
            '<i class="fa fa-list"></i> Currency History',
            ['currency-history/index'],
            ['class' => 'btn btn-default']
) ?>
        <?= Html::a(
            '<i class="fa fa-refresh"></i> Synchronization rate',
            ['synchronization'],
            ['class' => 'btn btn-warning',
                'title' => Html::encode(Yii::$app->currency->url),
                'data' =>
                [
                    'confirm' => 'Are you sure you want synchronization all currency?',
                    'method' => 'post',
                ],
            ]
        ) ?>

        <?= Html::a(
            '<i class="fa fa-remove"></i> Clear Cache',
            ['clear-cache'],
            ['class' => 'btn btn-danger', 'title' => 'Clear Currency cache', 'data' => [
                'confirm' => 'Are you sure you want clear currency cache?',
                'method' => 'post',
            ],]
        ) ?>
    </p>

    <div class="alert alert-secondary" role="alert" title="Synchronization URL">
        <?php
            echo Html::encode(Yii::$app->currency->url);
        ?>
    </div>



    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => static function (\common\models\Currency $model) {
            if (!$model->cur_enabled) {
                return [
                    'class' => 'danger'
                ];
            }

            if ($model->cur_default) {
                return [
                    'class' => 'success'
                ];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cur_code',
            'cur_name',
            'cur_symbol',
            'cur_base_rate',
            'cur_app_rate',
            [
                'attribute' => 'cur_app_percent',
                'value' => static function (\common\models\Currency $model) {
                    return $model->cur_app_percent . ' %';
                }
            ],
            'cur_enabled:boolean',
            'cur_default:boolean',
            //'cur_created_dt',
            //'cur_updated_dt',

            'cur_sort_order',

            /*[
                'attribute' => 'cur_synch_dt',
                'value' => static function(\common\models\Currency $model) {
                    return $model->cur_synch_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cur_synch_dt)) : '-';
                },
                'format' => 'raw',
            ],*/

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cur_synch_dt'
            ],

            /*[
                'attribute' => 'cur_created_dt',
                'value' => static function(\common\models\Currency $model) {
                    return $model->cur_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cur_created_dt)) : '-';
                },
                'format' => 'raw',
            ],*/

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cur_created_dt'
            ],

            /*[
                'attribute' => 'cur_updated_dt',
                'value' => static function(\common\models\Currency $model) {
                    return $model->cur_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cur_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],*/

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cur_updated_dt'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
