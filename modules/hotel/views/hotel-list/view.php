<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use modules\hotel\models\HotelList;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelList */

$this->title = $model->hl_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="hotel-list-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->hl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->hl_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
            'hl_description:ntext',
            'hl_address:ntext',
            'hl_postal_code',
            'hl_city',
            'hl_email:email',
            'hl_web',
            'hl_phone_list',
            'hl_image_list',
            'hl_image_base_url:url',
            'hl_board_codes',
            'hl_segment_codes',
            'hl_latitude',
            'hl_longitude',
            'hl_ranking',
            'hl_service_type',
            'hl_last_update',
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

            [
                'attribute' => 'hl_last_update',
                'value' => static function(HotelList $model) {
                    return $model->hl_last_update ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDate(strtotime($model->hl_last_update)) : '-';
                },
                'format' => 'raw',
            ],
        ],
    ]) ?>

</div>
