<?php

use sales\model\clientChat\entity\ClientChat;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $chatsDataProvider yii\data\ActiveDataProvider */

?>

<div class="row">
    <div class="col-md-12">
        <h4>Chats</h4>

        <?= GridView::widget([
            'dataProvider' => $chatsDataProvider,
            'columns' => [
                [
                    'attribute' => 'cch_id',
                    'value' => static function (ClientChat $chat) {
                        return Html::a('<i class="fa fa-comments"> </i> ' . $chat->cch_id, [
                            'client-chat/dashboard-v2', 'chid' => $chat->cch_id
                        ], [
                            'data-pjax' => 0,
                            'target' => '_blank'
                        ]);
                    },
                    'format' => 'raw',
                    'options' => [
                        'style' => 'width:80px'
                    ],
                    'contentOptions' => [
                        'class' => 'text-center'
                    ]
                ],
                [
                    'class' => \common\components\grid\project\ProjectColumn::class,
                    'attribute' => 'cch_project_id',
                    'relation' => 'cchProject',
                ],
                [
                    'class' => \common\components\grid\department\DepartmentColumn::class,
                    'attribute' => 'cch_dep_id',
                    'relation' => 'cchDep',
                ],
                [
                    'attribute' => 'cch_channel_id',
                    'value' => static function (ClientChat $model) {
                        return $model->cch_channel_id ? $model->cchChannel->ccc_name : null;
                    },
                ],
                [
                    'attribute' => 'cch_status_id',
                    'value' => static function (ClientChat $model) {
                        return \yii\bootstrap4\Html::tag('span', $model->getStatusName(), ['class' => 'badge badge-' . $model->getStatusClass()]);
                    },
                    'format' => 'raw',
                    'label' => 'Status',
                ],
                [
                    'attribute' => 'cch_created_dt',
                    'value' => static function (ClientChat $chat) {
                        $createdTS = strtotime($chat->cch_created_dt);
                        $diffTime = time() - $createdTS;
                        $diffHours = (int) ($diffTime / (60 * 60));
                        $str = ($diffHours > 3 && $diffHours < 73 ) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                        $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($chat->cch_created_dt));
                        return $str;
                    },
                    'options' => [
                        'style' => 'width:160px'
                    ],
                    'format' => 'raw',
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                ],
                [
                    'label' => 'Last message',
                    'value' => static function (ClientChat $chat) {
                        if (!$chat->lastMessage) {
                            return null;
                        }
                        $date = $chat->lastMessage->cclm_dt;
                        $createdTS = strtotime($date);
                        $diffTime = time() - $createdTS;
                        $diffHours = (int) ($diffTime / (60 * 60));
                        $str = ($diffHours > 3 && $diffHours < 73 ) ? $diffHours . ' hours' : Yii::$app->formatter->asRelativeTime($createdTS);
                        $str .= '<br><i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($date));
                        return $str;
                    },
                    'options' => [
                        'style' => 'width:160px'
                    ],
                    'format' => 'raw',
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                ],
                [
                    'class' => \common\components\grid\UserColumn::class,
                    'attribute' => 'cch_owner_user_id',
                    'relation' => 'cchOwnerUser',
                    'label' => 'User',
                ],
            ],
        ]) ?>

    </div>
</div>
