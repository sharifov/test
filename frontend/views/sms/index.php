<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SmsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sms';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Sms', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            's_id',
            's_reply_id',
            's_lead_id',
            's_project_id',
            's_phone_from',
            //'s_phone_to',
            //'s_sms_text:ntext',
            //'s_sms_data:ntext',
            //'s_type_id',
            //'s_template_type_id',
            //'s_language_id',
            //'s_communication_id',
            //'s_is_deleted',
            //'s_is_new',
            //'s_delay',
            //'s_priority',
            //'s_status_id',
            //'s_status_done_dt',
            //'s_read_dt',
            //'s_error_message',
            //'s_tw_price',
            //'s_tw_sent_dt',
            //'s_tw_account_sid',
            //'s_tw_message_sid',
            //'s_tw_num_segments',
            //'s_tw_to_country',
            //'s_tw_to_state',
            //'s_tw_to_city',
            //'s_tw_to_zip',
            //'s_tw_from_country',
            //'s_tw_from_state',
            //'s_tw_from_city',
            //'s_tw_from_zip',
            //'s_created_user_id',
            //'s_updated_user_id',
            //'s_created_dt',
            //'s_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
