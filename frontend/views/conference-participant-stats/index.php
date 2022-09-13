<?php

use common\components\grid\DateTimeColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\conference\entity\conferenceParticipantStats\search\ConferenceParticipantStatsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Conference Participant Stats';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conference-participant-stats-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Conference Participant Stats', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cps_id',
            'cps_cf_id',
            'cps_cf_sid',
            'cps_user_id',
            'cps_duration',
            'cps_talk_time',
            'cps_hold_time',
            ['class' => DateTimeColumn::class, 'attribute'  => 'cps_created_dt'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
