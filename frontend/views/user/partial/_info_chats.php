<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;
use sales\model\clientChat\entity\ClientChat;
use common\components\grid\DateTimeColumn;

?>

<?php Pjax::begin() ?>
<?php /*echo $this->render('_info_chats_search', ['model' => $chatSearchModel]); */?>
<h5>Chats Stats</h5>
<div class="well">
<?= GridView::widget([
    'dataProvider' => $chatDataProvider,
    'filterModel' => $chatSearchModel,
    'emptyTextOptions' => [
        'class' => 'text-center'
    ],
    'columns' => [
        'cch_id',
        'cch_rid',
        [
            'attribute' => 'cch_status_id',
            'value' => static function (ClientChat $model) {
                return Html::tag('span', $model->getStatusName(), ['class' => 'badge badge-' . $model->getStatusClass()]);
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
                return $model->cch_channel_id ? Html::a(Html::encode($model->cchChannel->ccc_name), ['client-chat-channel-crud/view', 'id' => $model->cch_channel_id], ['target' => '_blank', 'data-pjax' => 0]) : '-';
            },
            'format' => 'raw',
            'filter' => \sales\model\clientChatChannel\entity\ClientChatChannel::getList()
        ],
        [
            'class' => DateTimeColumn::class,
            'attribute' => 'cch_created_dt',
            'format' => 'byUserDateTime',
        ],
    ]
])
?>
</div>
<?php Pjax::end() ?>

