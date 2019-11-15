<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CurrencySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Currency';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-index">

    <h1><i class="fa fa-usd"></i> <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Add Currency', ['create'], ['class' => 'btn btn-success']) ?>

        <?= Html::a('<i class="fa fa-refresh"></i> Synchronization rate', ['synchronization'], ['class' => 'btn btn-warning', 'title' => Html::encode(Yii::$app->currency->url), 'data' => [
            'confirm' => 'Are you sure you want synchronization all currency?',
            'method' => 'post',
        ],]) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
            'cur_rate',
            'cur_system_rate',
            'cur_enabled:boolean',
            'cur_default:boolean',
            //'cur_created_dt',
            //'cur_updated_dt',

            'cur_sort_order',

            [
                'attribute' => 'cur_synch_dt',
                'value' => static function(\common\models\Currency $model) {
                    return $model->cur_synch_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cur_synch_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'cur_created_dt',
                'value' => static function(\common\models\Currency $model) {
                    return $model->cur_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cur_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'cur_updated_dt',
                'value' => static function(\common\models\Currency $model) {
                    return $model->cur_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cur_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
