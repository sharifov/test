<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightQuoteStatusLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Quote Status Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-status-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Quote Status Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'qsl_id',
            //'qsl_created_user_id',

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'qsl_created_user_id',
                'relation' => 'qslCreatedUser',
                'placeholder' => 'Select User',
            ],

            'qsl_flight_quote_id',
            'qsl_status_id',
            'qsl_created_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
