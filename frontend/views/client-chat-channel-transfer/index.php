<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatChannelTransfer\entity\ClientChatChannelTransfer;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChatChannelTransfer\entity\search\ClientChatChannelTransferSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Channel Transfers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-channel-transfer-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Channel Transfer', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'cctr_from_ccc_id',
                'value' => static function (ClientChatChannelTransfer $model) {
                    return $model->from ? $model->from->ccc_name : null;
                },
                'filter' => ClientChatChannel::getList(),
            ],
            [
                'attribute' => 'cctr_to_ccc_id',
                'value' => static function (ClientChatChannelTransfer $model) {
                    return $model->from ? $model->to->ccc_name : null;
                },
                'filter' => ClientChatChannel::getList(),
            ],
            ['class' => UserSelect2Column::class, 'attribute' => 'cctr_created_user_id', 'relation' => 'createdUser'],
            ['class' => DateTimeColumn::class, 'attribute' => 'cctr_created_dt'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
