<?php

use common\components\grid\conferenceParticipant\ConferenceParticipantTypeColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use common\models\ConferenceParticipant;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ConferenceParticipantSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Conference Participants';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conference-participant-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Conference Participant', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'cp_id',
            ['class' => ConferenceParticipantTypeColumn::class],
            ['class' => UserSelect2Column::class, 'attribute' => 'cp_user_id', 'relation' => 'user'],
            //'cp_cf_id',
            [
                'attribute' => 'cp_cf_id',
                'value' => static function (\common\models\ConferenceParticipant $model) {
                    return Html::a($model->cp_cf_id, ['conference/view', 'id' => $model->cp_cf_id], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw'
            ],
            'cp_call_sid',
            //'cp_call_id',

            [
                'attribute' => 'cp_call_id',
                'value' => static function (\common\models\ConferenceParticipant $model) {
                    return Html::a($model->cp_call_id, ['call/view', 'id' => $model->cp_call_id], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'cp_status_id',
                'format' => 'conferenceParticipantStatus',
                'filter' => \common\models\ConferenceParticipant::getStatusList()
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cp_join_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cp_leave_dt',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cp_hold_dt',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
