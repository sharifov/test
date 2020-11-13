<?php

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;


/* @var $this yii\web\View */
/* @var $model sales\model\clientChatStatusLog\entity\ClientChatStatusLog */

$this->title = $model->csl_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-status-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->csl_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->csl_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'csl_id',
                'csl_cch_id',
                //'csl_from_status',
                [
                    'attribute' => 'csl_from_status',
                    'value' => static function (ClientChatStatusLog $model) {
                        return $model->csl_from_status ? Html::tag('span',
                            ClientChat::getStatusNameById($model->csl_from_status),
                            ['class' => 'badge badge-' . ClientChat::getStatusClassById($model->csl_from_status)]) : null;
                    },
                    'format' => 'raw',
                ],
                //'csl_to_status',
                [   'attribute' => 'csl_to_status',
                    'value' => static function (ClientChatStatusLog $model) {
                        return $model->csl_to_status ? Html::tag('span',
                            ClientChat::getStatusNameById($model->csl_to_status),
                            ['class' => 'badge badge-' . ClientChat::getStatusClassById($model->csl_to_status)]) : null;
                    },
                    'format' => 'raw',
                ],
                'csl_start_dt:byUserDateTime',
                'csl_end_dt:byUserDateTime',
                'csl_owner_id:username',
                'csl_description:ntext',
                [
                    'attribute' => 'csl_prev_channel_id',
                    'value' => static function (ClientChatStatusLog $model) {
                        return $model->cslPrevChannel ? Html::a('<i class="fa fa-link"></i> ' . $model->cslPrevChannel->ccc_name, ['/client-chat-channel-crud/view', 'id' => $model->csl_prev_channel_id], ['target' => '_blank', 'data-pjax' => 0]) : null;
                    },
					'format' => 'raw',
				],
				[
					'attribute' => 'csl_action_type',
					'value' => static function (ClientChatStatusLog $model) {
						return ClientChatStatusLog::getActionLabel($model->csl_action_type);
					},
					'format' => 'raw',
				],
                'csl_user_id:username',
            ],
        ]) ?>

    </div>

</div>
