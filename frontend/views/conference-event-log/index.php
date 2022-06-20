<?php

use common\components\grid\DateTimeColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\conference\entity\conferenceEventLog\search\ConferenceEventLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Conference Event Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conference-event-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Conference Event Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php  echo $this->render('_pagination', ['model' => $searchModel]);?>

    <?= $searchModel->filterCount ? 'Find <b>' . $searchModel->filterCount . '</b> items' : null ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterUrl' => Url::to(['conference-event-log/index']),
        'layout' => "{items}",
        'columns' => [
            [
                'attribute' => 'cel_id',
                'enableSorting' => false,
            ],
            'cel_event_type',
            'cel_conference_sid',
            'cel_sequence_number',
            ['class' => DateTimeColumn::class, 'attribute' => 'cel_created_dt'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php  echo $this->render('_pagination', ['model' => $searchModel]);?>

    <?php Pjax::end(); ?>

</div>
