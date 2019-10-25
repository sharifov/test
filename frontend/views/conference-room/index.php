<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ConferenceRoomSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Conference Rooms';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conference-room-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Conference Room', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => function (\common\models\ConferenceRoom $model) {
            if ($model->cr_start_dt && strtotime($model->cr_start_dt) > time()) {
                return [
                    'class' => 'warning'
                ];
            }

            if ($model->cr_end_dt && strtotime($model->cr_end_dt) < time()) {
                return [
                    'class' => 'warning'
                ];
            }

            if (!$model->cr_enabled) {
                return [
                    'class' => 'danger'
                ];
            }
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'cr_id',
            'cr_key',
            'cr_name',
            'cr_phone_number',
            'cr_enabled:boolean',
            'cr_start_dt',
            'cr_end_dt',
            'cr_param_muted:boolean',
            'cr_param_beep',
            //'cr_param_start_conference_on_enter',
            //'cr_param_end_conference_on_enter',
            'cr_param_max_participants',
            'cr_param_record',
            'cr_param_region',
            //'cr_param_trim',
            //'cr_param_wait_url:url',
            'cr_moderator_phone_number',
            //'cr_welcome_message:ntext',
            //'cr_created_dt',
            'cr_updated_dt',
            //'cr_created_user_id',
            //'cr_updated_user_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
