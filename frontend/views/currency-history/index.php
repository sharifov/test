<?php

use yii\grid\ActionColumn;
use common\models\CurrencyHistory;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CurrencyHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Currency Histories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="currency-history-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
		<?= Html::a('<i class="fa fa-plus"></i> Add Currency History', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
<!--    --><?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ch_code',
            'ch_base_rate',
            'ch_app_rate',
            'ch_app_percent',
            [
				'attribute' => 'ch_created_date',
				'value' => static function(CurrencyHistory $model) {
					return $model->ch_created_date ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDate(strtotime($model->ch_created_date)) : '-';
				},
				'format' => 'raw',
            ],
            [
				'attribute' => 'ch_main_created_dt',
				'value' => static function(CurrencyHistory $model) {
					return $model->ch_main_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->ch_main_created_dt)) : '-';
				},
				'format' => 'raw',
            ],
            [
				'attribute' => 'ch_main_updated_dt',
				'value' => static function(CurrencyHistory $model) {
					return $model->ch_main_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->ch_main_updated_dt)) : '-';
				},
				'format' => 'raw',
            ],
            [
				'attribute' => 'ch_main_synch_dt',
				'value' => static function(CurrencyHistory $model) {
					return $model->ch_main_synch_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->ch_main_synch_dt)) : '-';
				},
				'format' => 'raw',
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
