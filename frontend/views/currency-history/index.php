<?php

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

    <?php Pjax::begin(); ?>
<!--    --><?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cur_his_code',
            'cur_his_base_rate',
            'cur_his_app_rate',
            'cur_his_app_percent',
            'cur_his_created',
            'cur_his_main_created_dt',
            'cur_his_main_updated_dt',
            'cur_his_main_synch_dt',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
