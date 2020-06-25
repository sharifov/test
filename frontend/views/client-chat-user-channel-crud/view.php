<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatUserChannel\entity\ClientChatUserChannel */

$this->title = $model->ccuc_user_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat User Channels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-user-channel-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'ccuc_user_id' => $model->ccuc_user_id, 'ccuc_channel_id' => $model->ccuc_channel_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'ccuc_user_id' => $model->ccuc_user_id, 'ccuc_channel_id' => $model->ccuc_channel_id], [
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
                'ccuc_user_id:username',
                'ccuc_channel_id',
                'ccuc_created_dt:byUserDateTime',
                'ccuc_created_user_id:username',
            ],
        ]) ?>

    </div>

</div>
