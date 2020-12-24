<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\models\Airports;
use kartik\select2\Select2;
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

    <h1><i class="fa fa-plane"></i> <?= Html::encode($this->title) ?></h1>

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
        <p>
        Synchronization from: <i><?php echo Html::encode(Yii::$app->travelServices->url); ?>airport/export?format=json</i>
        </p>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => static function (Airports $model) {
            if ($model->a_close) {
                return [
                    'class' => 'danger'
                ];
            }

            if ($model->a_disabled) {
                return [
                    'class' => 'danger'
                ];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'iata',
                'value' => static function (Airports $model) {
                    return '<span class="badge badge-info">' . Html::encode($model->iata) . '</span>';
                },
                'format' => 'raw'
            ],
            'a_icao',
            'name',

            //'a_city_code',
            [
                'attribute' => 'a_city_code',
                'value' => static function (Airports $model) {
                    return $model->a_city_code ? '<span class="badge badge-light">' . Html::encode($model->a_city_code) . '</span>' : '-';
                },
                'format' => 'raw'
            ],
            'city',

            //'a_country_code',

            [
                //'label' => 'Type Name',
                'attribute' => 'a_country_code',
                'contentOptions' => ['style' => 'width: 240px'],
                'value' => static function (Airports $model) {
                    return $model->a_country_code ? '<span class="badge badge-success">' . Html::encode($model->a_country_code) . '</span>' : '-';
                },
                'format' => 'raw',
                'filter' => Select2::widget([
                    //'model' => \common\models\search\ProjectLocaleSearch::class,
                    //'attribute' => 'pl_language_id',

                    'name' => 'AirportsSearch[a_country_code]',
                    'data' => \common\models\Language::getCountryNames(),

                    //'theme' => Select2::THEME_DEFAULT,
                    'size' => Select2::SMALL,
                    'value' => empty(Yii::$app->request->get('AirportsSearch')['a_country_code']) ? null : Yii::$app->request->get('AirportsSearch')['a_country_code'],
                    //'hideSearch' => false,
                    'options' => [
                        'placeholder' => '',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]),
            ],
            'country',

            //'a_state',


            [
                //'label' => 'Type Name',
                'attribute' => 'timezone',
                'contentOptions' => ['style' => 'width: 240px'],
                'value' => static function (Airports $model) {
                    return $model->timezone ? Html::encode($model->timezone) : '-';
                },
                'format' => 'raw',
                'filter' => Select2::widget([
                    //'model' => \common\models\search\ProjectLocaleSearch::class,
                    //'attribute' => 'pl_language_id',

                    'name' => 'AirportsSearch[timezone]',
                    'data' => \common\models\Employee::timezoneList(true),

                    //'theme' => Select2::THEME_DEFAULT,
                    'size' => Select2::SMALL,
                    'value' => empty(Yii::$app->request->get('AirportsSearch')['timezone']) ? null : Yii::$app->request->get('AirportsSearch')['timezone'],
                    //'hideSearch' => false,
                    'options' => [
                        'placeholder' => '',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ]
                ]),
            ],

//            [
//                    'attribute' => 'timezone',
//                'filter' => \common\models\Employee::timezoneList(true)
//            ],
            'dst',
            //'a_rank',
            //'a_multicity:boolean',
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
