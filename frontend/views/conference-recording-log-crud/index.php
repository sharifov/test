<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\conference\entity\conferenceRecordingLog\search\ConferenceRecordingLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Conference Recording Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conference-recording-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Conference Recording Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-conference-recording-log']); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cfrl_id',
            'cfrl_conference_sid',
            [
                'class' => UserSelect2Column::class,
                'relation' => 'user',
                'attribute' => 'cfrl_user_id'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cfrl_created_dt',
            ],
            'cfrl_year',
            'cfrl_month',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
