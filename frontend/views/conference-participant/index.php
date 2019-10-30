<?php

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

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'cp_id',
            //'cp_cf_id',
            [
                'attribute' => 'cp_cf_id',
                'value' => static function(\common\models\ConferenceParticipant $model) {
                    return Html::a($model->cp_cf_id, ['conference/view', 'id' => $model->cp_cf_id], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw'
            ],
            'cp_call_sid',
            //'cp_call_id',

            [
                'attribute' => 'cp_call_id',
                'value' => static function(\common\models\ConferenceParticipant $model) {
                    return Html::a($model->cp_call_id, ['call/view', 'id' => $model->cp_call_id], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw'
            ],

            [
                'label' => 'Phone number',
                'value' => static function(\common\models\ConferenceParticipant $model) {
                    return $model->cpCall ? Html::a($model->cpCall->c_from, ['call/view', 'id' => $model->cp_call_id], ['target' => '_blank', 'data-pjax' => 0]) : '-';
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'cp_status_id',
                'value' => static function(\common\models\ConferenceParticipant $model) {
                    return $model->getStatusName();
                },
                'filter' => \common\models\ConferenceParticipant::getList()
            ],
            //'cp_status_id',
            //'cp_join_dt',
            //'cp_leave_dt',

            [
                'attribute' => 'cp_join_dt',
                'value' => function(\common\models\ConferenceParticipant $model) {
                    return $model->cp_join_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cp_join_dt)) : '-';
                },
                'format' => 'raw',
                'filter' => \dosamigos\datepicker\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'cp_join_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],
            [
                'attribute' => 'cp_leave_dt',
                'value' => function(\common\models\ConferenceParticipant $model) {
                    return $model->cp_leave_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->cp_leave_dt)) : '';
                },
                'format' => 'raw',
                'filter' => \dosamigos\datepicker\DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'cp_leave_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
