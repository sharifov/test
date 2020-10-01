<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use sales\model\clientChat\entity\ClientChat;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChatStatusLog\entity\search\ClientChatStatusLog */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Status Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-status-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Client Chat Status Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'csl_id',
            [
                'label' =>'Client Chat ID',
                'attribute' => 'csl_cch_id',
                'value' => static function (ClientChatStatusLog $model) {
                    return $model->cslCch->cch_id ?? null;
                }
            ],
            //'csl_from_status',
            [
                'attribute' => 'csl_from_status',
                'value' => static function (ClientChatStatusLog $model) {
                    return $model->csl_from_status ?  Html::tag('span', ClientChat::getStatusList()[$model->csl_from_status], ['class' => 'badge badge-'.ClientChat::getStatusClassList()[$model->csl_from_status]]) : null;
                },
                'format' => 'raw',
                'filter' => \sales\model\clientChat\entity\ClientChat::getStatusList()
            ],
            [
                'attribute' => 'csl_to_status',
                'value' => static function (ClientChatStatusLog $model) {
                    return $model->csl_to_status ?  Html::tag('span', ClientChat::getStatusList()[$model->csl_to_status], ['class' => 'badge badge-'.ClientChat::getStatusClassList()[$model->csl_to_status]]) : null;
                },
                'format' => 'raw',
                'filter' => \sales\model\clientChat\entity\ClientChat::getStatusList()
            ],
			[
				'attribute' => 'csl_prev_channel_id',
				'value' => static function (ClientChatStatusLog $model) {
					return $model->cslPrevChannel ? Html::a('<i class="fa fa-link"></i> ' . $model->cslPrevChannel->ccc_name, ['/client-chat-channel-crud/view', 'id' => $model->csl_prev_channel_id], ['target' => '_blank', 'data-pjax' => 0]) : null;
				},
				'format' => 'raw',
				'filter' => ClientChatChannel::getList()
			],
			[
				'class' => UserSelect2Column::class,
				'attribute' => 'csl_user_id',
				'relation' => 'cslUser',
				'format' => 'username',
				'placeholder' => 'Select User'
			],
            //'csl_to_status',
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'csl_start_dt',
				'format' => 'byUserDateTime'
			],
			[
				'class' => DateTimeColumn::class,
				'attribute' => 'csl_end_dt',
				'format' => 'byUserDateTime'
			],
			[
				'class' => UserSelect2Column::class,
				'attribute' => 'csl_owner_id',
				'relation' => 'cslOwner',
				'format' => 'username',
				'placeholder' => 'Select User'
			],

			[
				'attribute' => 'csl_action_type',
				'value' => static function (ClientChatStatusLog $model) {
					return $model->getActionLabel();
				},
				'format' => 'raw',
				'filter' => ClientChatStatusLog::getActionList()
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
