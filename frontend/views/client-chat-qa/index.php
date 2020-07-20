<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChat\entity\search\ClientChatQaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chats';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cch_id',
            'cch_rid',
            'cch_ccr_id',
            'cch_title',
            'cch_description',
            //'cch_project_id',
            //'cch_dep_id',
            //'cch_channel_id',
            //'cch_client_id',
            //'cch_owner_user_id',
            //'cch_case_id',
            //'cch_lead_id',
            //'cch_note',
            //'cch_status_id',
            //'cch_ip',
            //'cch_ua',
            //'cch_language_id',
            //'cch_created_dt',
            //'cch_updated_dt',
            //'cch_created_user_id',
            //'cch_updated_user_id',
            //'cch_client_online',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
