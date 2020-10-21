<?php

use yii\bootstrap4\Alert;
use yii\widgets\DetailView;
use yii\helpers\Html;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use common\components\grid\DateTimeColumn;

/** @var $clientChatData \sales\model\clientChat\entity\ClientChat|null */

?>

<?php if ($clientChatData): ?>
    <?= DetailView::widget([
        'model' => $clientChatData,
        'attributes' => [
            'cch_id',
            [
                'attribute' => 'cch_parent_id',
                'value' => static function (ClientChat $model) {
                    return $model->cch_parent_id ?
                        Html::a(
                            '<i class="fa fa-link"></i> ' . $model->cch_parent_id,
                            ['client-chat-crud/view', 'id' => $model->cch_parent_id],
                            ['target' => '_blank', 'data-pjax' => 0]
                        ) : '-';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:100px'],
            ],
            'cch_rid',
            [
                'label' => 'Messages',
                'value' => static function (ClientChat $model) {
                    $count = ClientChatMessage::countByChatId($model->cch_id);
                    return Html::a(
                        '<span class="glyphicon glyphicon-comment"></span> <sup>' . $count . '</sup>',
                        ['/client-chat-qa/view', 'id' => $model->cch_id, '#' => 'messages'],
                        [
                            'target' => '_blank',
                            'data-pjax' => 0,
                            'title' => 'Messages',
                        ]
                    );
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'cch_ccr_id',
                'value' => static function (ClientChat $model) {
                    return $model->cch_ccr_id ?
                        Html::a(
                            '<i class="fa fa-link"></i> ' . $model->cch_ccr_id,
                            ['client-chat-request-crud/view', 'id' => $model->cch_ccr_id],
                            ['target' => '_blank', 'data-pjax' => 0]
                        ) : '-';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'cch_status_id',
                'value' => static function (ClientChat $model) {
                    return Html::tag('span', $model->getStatusName(), ['class' => 'badge badge-'.$model->getStatusClass()]);
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'cch_project_id',
                'format' => 'projectName',

            ],

            [
                'attribute' => 'cch_channel_id',
                'value' => static function (ClientChat $model) {
                    return $model->cch_channel_id ? Html::a(
                        Html::encode($model->cchChannel->ccc_name),
                        ['client-chat-channel-crud/view', 'id' => $model->cch_channel_id],
                        ['target' => '_blank', 'data-pjax' => 0]
                    ) : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'cch_client_id',
                'value' => static function (ClientChat $model) {
                    return $model->cch_client_id ? Html::a(
                        '<i class="fa fa-link"></i> ' . $model->cch_client_id,
                        ['client/view', 'id' => $model->cch_client_id],
                        ['target' => '_blank', 'data-pjax' => 0]
                    ) : '-';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'cch_owner_user_id',
                'format' => 'userName',
            ],

            [
                'attribute' => 'cch_source_type_id',
                'filter' => ClientChat::getSourceTypeList(),
                'value' => static function (ClientChat $model) {
                    return $model->getSourceTypeName();
                }
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cch_created_dt',
                'format' => 'byUserDateTime',
            ],
        ]
    ]) ?>
<?php else: ?>
    <?= Alert::widget([
        'body' => 'Client Chat Data not found.',
        'options' => [
            'class' => 'alert alert-warning'
        ]
    ]) ?>
<?php endif; ?>
