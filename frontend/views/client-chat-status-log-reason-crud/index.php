<?php

use sales\model\clientChat\entity\statusLogReason\ClientChatStatusLogReason;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChat\entity\statusLogReason\search\ClientChatStatusLogReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Status Log Reasons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-status-log-reason-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Status Log Reason', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
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

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
