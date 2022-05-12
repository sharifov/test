<?php

use common\components\SearchService;
use common\models\QuoteSegmentStop;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegmentStop */

$this->title = $model->qss_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Segment Stops', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-segment-stop-view row">
    <div class="col-md-4">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>
            <?= Html::a('Update', ['update', 'qss_id' => $model->qss_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'qss_id' => $model->qss_id], [
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
                'qss_id',
                'qss_location_code',
                'qss_departure_dt',
                'qss_arrival_dt',
                'qss_duration',
                [
                    'attribute' => 'qss_elapsed_time',
                    'format' => 'raw',
                    'value' => function (QuoteSegmentStop $model) {
                        return $model->qss_elapsed_time ? SearchService::durationInMinutes($model->qss_elapsed_time) : '-';
                    },

                ],
                'qss_equipment',
                'qss_segment_id',
            ],
        ]) ?>
    </div>
</div>
