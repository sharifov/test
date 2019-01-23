<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Calls';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Call', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'c_id',
            'c_call_sid',
            'c_account_sid',
            'c_call_type_id',
            'c_from',
            'c_to',
            'c_sip',
            'c_call_status',
            'c_api_version',
            'c_direction',
            'c_forwarded_from',
            'c_caller_name',
            'c_parent_call_sid',
            'c_call_duration',
            'c_sip_response_code',
            'c_recording_url:url',
            'c_recording_sid',
            'c_recording_duration',
            'c_timestamp',
            'c_uri',
            'c_sequence_number',
            'c_lead_id',
            'c_created_user_id',
            'c_created_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
