<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LeadFlightSegment */

$this->title = 'Flight Segment: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Flight Segments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-flight-segment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php /*= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <div class="row">
        <div class="col-md-6">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    //'lead_id',
                    [
                        'attribute' => 'lead_id',
                        'format' => 'raw',
                        'value' => function(\common\models\LeadFlightSegment $model) {
                            return '<i class="fa fa-arrow-right"></i> '.Html::a('lead: '.$model->lead_id, ['leads/view', 'id' => $model->lead_id], ['target' => '_blank', 'data-pjax' => 0]);
                        },
                    ],
                    'origin',
                    'destination',
                    //'departure',
                    [
                        'attribute' => 'departure',
                        'value' => function(\common\models\LeadFlightSegment $model) {
                            return '<i class="fa fa-calendar"></i> '.date("Y-m-d", strtotime($model->departure));
                        },
                        'format' => 'html',
                    ],

                ],
            ]) ?>
        </div>

        <div class="col-md-6">

            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'flexibility',
                    'flexibility_type',
                    'origin_label',
                    'destination_label',
                    [
                        'attribute' => 'created',
                        'value' => function(\common\models\LeadFlightSegment $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                        },
                        'format' => 'html',
                    ],

                    [
                        'attribute' => 'updated',
                        'value' => function(\common\models\LeadFlightSegment $model) {
                            return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated));
                        },
                        'format' => 'html',
                    ],

                ],
            ]) ?>
        </div>
    </div>

</div>
