<?php

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\grid\ActionColumn;
use common\components\grid\DateTimeColumn;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var sales\model\clientChat\entity\search\ClientChatSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Client Chats QA';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'cch_id',
                'options' => ['style' => 'width:100px']
            ],
            'cch_rid',
            [
                'attribute' => 'cch_ccr_id',
                'value' => static function (ClientChat $model) {
                    return $model->cch_ccr_id ?
                        Html::a('<i class="fa fa-link"></i> ' . $model->cch_ccr_id,
                        ['client-chat-request-crud/view', 'id' => $model->cch_ccr_id],
                        ['target' => '_blank', 'data-pjax' => 0]) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:100px']
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
            [
                'attribute' => 'cch_owner_user_id',
                'class' => \common\components\grid\UserColumn::class,
                'relation' => 'cchOwnerUser',
            ],
            [
                'label' => 'Case ID',
                'attribute' => 'cchCase',
                'format' => 'case'
            ],
            [
                'label' => 'Lead ID',
                'attribute' => 'cchLead',
                'format' => 'lead'
            ],
            [
                'attribute' => 'cch_language_id',
                'filter' => \common\models\Language::getLanguages()
            ],
            [
				'class' => DateTimeColumn::class,
				'attribute' => 'cch_created_dt',
				'format' => 'byUserDateTime'
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view}<br />{room}',
                'buttons' => [
                    'view' => static function ($url, ClientChat $model) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',
                        ['/client-chat-qa/view', 'id' => $model->cch_id],
                        [
                            'target' => '_blank',
                            'data-pjax' => 0,
                            'title' => 'View',
                        ]);
                    },
                    'room' => static function ($url, ClientChat $model) {
                        return Html::a('<span class="glyphicon glyphicon-list-alt"></span>',
                        ['/client-chat-qa/room', 'id' => $model->cch_id],
                        [
                            'target' => '_blank',
                            'data-pjax' => 0,
                            'title' => 'Room',
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
