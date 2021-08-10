<?php

use modules\flight\models\FlightRequestLog;
use modules\flight\models\FlightRequest;
use common\components\grid\DateTimeColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightRequestLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Request Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-request-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Request Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php Pjax::begin(['id' => 'pjax-flight-request-log']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'flr_id',
            'flr_fr_id',
            [
                'attribute' => 'flr_status_id_old',
                'value' => static function (FlightRequestLog $model) {
                    return $model->getOldStatusName() ?? Yii::$app->formatter->nullDisplay;
                },
                'filter' => FlightRequest::STATUS_LIST,
                'format' => 'raw',
            ],
            [
                'attribute' => 'flr_status_id_new',
                'value' => static function (FlightRequestLog $model) {
                    return $model->getNewStatusName() ?? Yii::$app->formatter->nullDisplay;
                },
                'filter' => FlightRequest::STATUS_LIST,
                'format' => 'raw',
            ],
            'flr_description',
            ['class' => DateTimeColumn::class, 'attribute' => 'flr_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'flr_updated_dt'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
