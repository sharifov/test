<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use modules\hotel\models\HotelList;

/* @var $this yii\web\View */
/* @var $searchModel modules\hotel\models\search\HotelListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hotel Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hotel-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Hotel List', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'hl_id',
            'hl_code',
            'hl_hash_key',
            'hl_name',
            'hl_star',
            'hl_category_name',
            'hl_destination_code',
            'hl_destination_name',
            'hl_zone_name',
            'hl_zone_code',
            'hl_country_code',
            'hl_state_code',
            //'hl_description:ntext',
            'hl_address:ntext',
            'hl_postal_code',
            'hl_city',
            'hl_email:email',
            'hl_web',
            //'hl_phone_list',
            //'hl_image_list',
            //'hl_image_base_url:url',
            //'hl_board_codes',
            //'hl_segment_codes',
            //'hl_latitude',
            //'hl_longitude',
            'hl_ranking',
            //'hl_service_type',
            'hl_last_update',
            //'hl_created_dt',
            //'hl_updated_dt',
            [
                'attribute' => 'hl_created_dt',
                'value' => static function(HotelList $model) {
                    return $model->hl_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->hl_created_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'hl_updated_dt',
                'value' => static function(HotelList $model) {
                    return $model->hl_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->hl_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
