<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ConferenceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Conferences';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conference-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Conference', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            ['attribute' => 'cf_id', 'options' => ['style' => 'width: 110px']],

            'cf_sid',
            'cf_friendly_name',
            [
                'label' => 'Participants',
                'value' => static function(\common\models\Conference $model) {
                    return Html::a(count($model->conferenceParticipants), ['conference-participant/index', 'ConferenceParticipantSearch[cp_cf_id]' => $model->cf_id], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw'
            ],
            //'cf_status_id',
            [
                'attribute' => 'cf_status_id',
                'value' => static function(\common\models\Conference $model) {
                    return $model->getStatusName();
                },
                'filter' => \common\models\Conference::getList()
            ],
            //'cf_options:ntext',
            //'cf_created_dt',
            //'cf_updated_dt',

            [
                'attribute' => 'cf_recording_duration',
                'label' => 'Recording',
                'value' => static function (\common\models\Conference $model) {
                    return  $model->cf_recording_duration ? Html::button(gmdate('i:s', $model->cf_recording_duration) . ' <i class="fa fa-volume-up"></i>', ['class' => 'btn btn-' . ($model->cf_recording_duration < 30 ? 'warning' : 'success') . ' btn-xs btn-recording_url', 'data-source_src' => $model->cf_recording_url /*yii\helpers\Url::to(['call/record', 'sid' =>  $model->cf_sid, 'cfRecord' => true ])*/ ]) : '-';
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-right'],
                'options' => ['style' => 'width: 80px']

            ],
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'cf_start_dt',
            ],
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'cf_end_dt',
            ],

            [
                'attribute' => 'cf_created_dt',
                'value' => function(\common\models\Conference $model) {
                    return $model->cf_created_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cf_created_dt)) : '-';
                },
                'format' => 'raw',
                'filter' => \dosamigos\datepicker\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'cf_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
                'options' => ['style' => 'width: 250px'],
            ],
            [
                'attribute' => 'cf_updated_dt',
                'value' => function(\common\models\Conference $model) {
                    return $model->cf_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cf_updated_dt)) : '';
                },
                'format' => 'raw',
                'filter' => \dosamigos\datepicker\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'cf_updated_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
                'options' => ['style' => 'width: 250px'],
            ],
            [
                'attribute' => 'cf_cr_id',
                'value' => function(\common\models\Conference $model) {
                    return $model->cfCr ? Html::a(Html::encode($model->cfCr->cr_name),['conference-room/view', 'id' => $model->cf_cr_id], ['target' => '_blank', 'data-pjax' => 0])  : '-';
                },
                'filter' => \common\models\ConferenceRoom::getList(),
                'format' => 'raw',
                'options' => ['style' => 'width: 100px']
            ],

            ['class' => UserSelect2Column::class, 'attribute' => 'cf_created_user_id', 'relation' => 'createdUser'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>