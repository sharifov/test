<?php

use sales\model\clientChat\entity\statusLogReason\ClientChatStatusLogReason;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\statusLogReason\ClientChatStatusLogReason */

$this->title = $model->cslr_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Status Log Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-status-log-reason-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cslr_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cslr_id], [
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
                'cslr_id',
				[
					'attribute' => 'cslr_status_log_id',
					'value' => static function (ClientChatStatusLogReason $model) {
						return Html::a('<i class="fa fa-link"> ' . $model->cslr_status_log_id, ['/client-chat-status-log-crud/view', 'id' => $model->cslr_status_log_id], ['data-pjax' => 0, 'target' => '_blank']);
					},
					'format' => 'raw'
				],
				[
					'attribute' => 'cslr_action_reason_id',
					'value' => static function (ClientChatStatusLogReason $model) {
						return Html::a('<i class="fa fa-link"> ' . $model->cslr_action_reason_id, ['/client-chat-action-reason-crud/view', 'id' => $model->cslr_action_reason_id], ['data-pjax' => 0, 'target' => '_blank']);
					},
					'format' => 'raw'
				],
                'cslr_comment',
            ],
        ]) ?>

    </div>

</div>
