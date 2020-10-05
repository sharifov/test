<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use sales\model\clientChat\entity\ClientChat;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use yii\grid\ActionColumn;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChat\entity\search\ClientChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chats';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Client Chat', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'cch_id',
                'options' => ['style' => 'width:100px']
            ],
			[
				'attribute' => 'cch_parent_id',
				'value' => static function (ClientChat $model) {
					return $model->cch_parent_id ?
						Html::a('<i class="fa fa-link"></i> ' . $model->cch_parent_id,
							['client-chat-crud/view', 'id' => $model->cch_parent_id],
							['target' => '_blank', 'data-pjax' => 0]) : '-';
				},
				'format' => 'raw',
				'options' => ['style' => 'width:100px'],
			],
            'cch_rid',
            [
                'label' => 'Messages',
                'value' => static function (ClientChat $model) {
                    $count = ClientChatMessage::countByChatId($model->cch_id);
                    return Html::a('<span class="glyphicon glyphicon-comment"></span> <sup>' . $count . '</sup>',
                        ['/client-chat-qa/view', 'id' => $model->cch_id, '#' => 'messages'],
                        [
                            'target' => '_blank',
                            'data-pjax' => 0,
                            'title' => 'Messages',
                        ]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'cch_ccr_id',
                'value' => static function (ClientChat $model) {
                    return $model->cch_ccr_id ?
                        Html::a('<i class="fa fa-link"></i> ' . $model->cch_ccr_id,
                        ['client-chat-request-crud/view', 'id' => $model->cch_ccr_id],
                        ['target' => '_blank', 'data-pjax' => 0]) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:100px'],
            ],
            [
                'attribute' => 'cch_status_id',
                'value' => static function (ClientChat $model) {
                    return Html::tag('span', $model->getStatusName(), ['class' => 'badge badge-'.$model->getStatusClass()]);
                },
                'format' => 'raw',
                'filter' => ClientChat::getStatusList()
            ],
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'cch_project_id',
                'relation' => 'cchProject',
            ],
            [
                'attribute' => 'cch_dep_id',
                'format' => 'department',
                'filter' => \common\models\Department::getList()
            ],
            [
                'attribute' => 'cch_channel_id',
                'value' => static function (ClientChat $model) {
                    return $model->cch_channel_id ? Html::a(Html::encode($model->cchChannel->ccc_name),
                        ['client-chat-channel-crud/view', 'id' => $model->cch_channel_id],
                        ['target' => '_blank', 'data-pjax' => 0]) : '-';
                },
                'format' => 'raw',
                'filter' => ClientChatChannel::getList()
            ],
            [
                'attribute' => 'cch_client_id',
                'value' => static function (ClientChat $model) {
                    return $model->cch_client_id ? Html::a('<i class="fa fa-link"></i> ' . $model->cch_client_id,
                        ['client/view', 'id' => $model->cch_client_id],
                        ['target' => '_blank', 'data-pjax' => 0]) : '-';
                },
                'format' => 'raw',
            ],
            /*[
                'attribute' => 'cch_owner_user_id',
                'class' => \common\components\grid\UserColumn::class,
                'relation' => 'cchOwnerUser',
            ],*/
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'cch_owner_user_id',
                'relation' => 'cchOwnerUser',
                'placeholder' => 'Select User'
            ],
			[
				'attribute' => 'cch_source_type_id',
				'options' => ['style' => 'width:100px'],
                'filter' => ClientChat::getSourceTypeList(),
                'value' => static function (ClientChat $model) {
                    return $model->getSourceTypeName();
                }
			],
            [
                'attribute' => 'caseId',
                'label' => 'Case',
                'value' => static function (ClientChat $chat) {
                    $out = '';
                    foreach ($chat->cases as $case) {
                        $out .= Yii::$app->formatter->format($case,  'case') . '<br />';
                    }
                    return $out;
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width:120px; white-space: normal;'],
            ],
            [
                'attribute' => 'leadId',
                'label' => 'Lead',
                'value' => static function (ClientChat $chat) {
                    $out = '';
                    foreach ($chat->leads as $lead) {
                        $out .= Yii::$app->formatter->format($lead,  'lead') . '<br />';
                    }
                    return $out;
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width:120px; white-space: normal;'],
            ],
            [
				'class' => DateTimeColumn::class,
				'attribute' => 'cch_created_dt',
				'format' => 'byUserDateTime',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
