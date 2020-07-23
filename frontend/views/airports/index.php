<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AirportsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Airports';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="airports-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Airports', ['create'], ['class' => 'btn btn-success']) ?>


        <?php /*= Html::a('Sync Airports', '#', [
            'class' => 'btn-success btn sync',
            'data-url' => Url::to([
                'settings/sync',
                'type' => 'airports'
            ])
        ])*/ ?>

        <?= Html::a('<i class="fa fa-refresh"></i> Synchronization from TravelServices', ['synchronization'], ['class' => 'btn btn-warning', 'data' => [
            'confirm' => 'Are you sure you want synchronization all airports from TravelServices?',
            'method' => 'post',
        ],]) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'iata',
            'a_icao',
            'name',
            'city',
            'a_city_code',
            'country',
            'a_country_code',
            'a_state',

            [
                    'attribute' => 'timezone',
                'filter' => \common\models\Employee::timezoneList(true)
            ],
            'dst',
            'a_rank',
            'a_multicity:boolean',
            'a_close:boolean',
            'a_disabled:boolean',
            'latitude',
            'longitude',

//            [
//                'class' => DateTimeColumn::class,
//                'attribute' => 'a_created_dt',
//            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'a_updated_dt',
            ],

//            [
//                'class' => UserSelect2Column::class,
//                'attribute' => 'a_created_user_id',
//                'relation' => 'aCreatedUser',
//                'placeholder' => 'Select User'
//            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'a_updated_user_id',
                'relation' => 'aUpdatedUser',
                'placeholder' => 'Select User'
            ],


            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
