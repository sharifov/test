<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\twilio\src\entities\voiceLog\search\VoiceLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Voice Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="voice-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Voice Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'vl_id',
            'vl_call_sid',
            'vl_account_sid',
            'vl_from',
            'vl_to',
            //'vl_call_status',
            //'vl_api_version',
            //'vl_direction',
            //'vl_forwarded_from',
            //'vl_caller_name',
            //'vl_parent_call_sid',
            //'vl_call_duration',
            //'vl_sip_response_code',
            //'vl_recording_url:url',
            //'vl_recording_sid',
            //'vl_recording_duration',
            //'vl_timestamp',
            //'vl_callback_source',
            //'vl_sequence_number',
            //'vl_created_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
