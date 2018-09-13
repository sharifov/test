<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadFlightSegmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Flight Segments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-flight-segment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?//= Html::a('Create Lead Flight Segment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

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
            //'created',
            //'updated',
            //'flexibility',
            [
                'attribute' => 'flexibility',
                'value' => function(\common\models\LeadFlightSegment $model) {
                    return $model->flexibility;
                },
                'filter' => array_combine(range(0, 5), range(0, 5)),
                //'format' => 'html',
            ],
            //'flexibility_type',
            [
                'attribute' => 'flexibility_type',
                'value' => function(\common\models\LeadFlightSegment $model) {
                    return $model->flexibility_type;
                },
                'filter' => \common\models\LeadFlightSegment::FLEX_TYPE_LIST
                //'format' => 'html',
            ],
            [
                'attribute' => 'created',
                'value' => function(\common\models\LeadFlightSegment $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime($model->created);
                },
                'format' => 'html',
            ],

            [
                'attribute' => 'updated',
                'value' => function(\common\models\LeadFlightSegment $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime($model->updated);
                },
                'format' => 'html',
            ],

            'origin_label',
            'destination_label',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
