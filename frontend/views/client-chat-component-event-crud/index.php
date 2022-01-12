<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use src\model\clientChat\componentEvent\entity\ClientChatComponentEvent;
use src\model\clientChatChannel\entity\ClientChatChannel;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\clientChat\componentEvent\entity\search\ClientChatComponentEventSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Component Events';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-component-event-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Component Event', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-client-chat-component-event', 'scrollTo' => 0]); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'ccce_id',
            [
                'attribute' => 'ccce_chat_channel_id',
                'value' => static function (ClientChatComponentEvent $model) {
                    return $model->chatChannel->ccc_name ?? null;
                },
                'filter' => ClientChatChannel::getList()
            ],
            [
                'attribute' => 'ccce_component',
                'value' => static function (ClientChatComponentEvent $model) {
                    return $model->getComponentEventName();
                },
                'filter' => ClientChatComponentEvent::getComponentEventList()
            ],
            [
                'attribute' => 'ccce_event_type',
                'value' => static function (ClientChatComponentEvent $model) {
                    return $model->getComponentTypeName();
                },
                'filter' => ClientChatComponentEvent::getComponentTypeList()
            ],
            [
                'attribute' => 'ccce_enabled',
                'value' => static function (ClientChatComponentEvent $model) {
                    return Yii::$app->formatter->asBooleanByLabel($model->ccce_enabled);
                },
                'filter' => [1 => 'Yes', 0 => 'No'],
                'format' => 'raw',
            ],
            'ccce_sort_order',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ccce_created_user_id',
                'relation' => 'createdUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ccce_updated_user_id',
                'relation' => 'updatedUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ccce_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ccce_updated_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
