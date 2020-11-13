<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatFeedback\entity\ClientChatFeedback */

$this->title = $model->ccf_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Feedback', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-feedback-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ccf_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ccf_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="col-md-4">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'ccf_id',
                'ccf_client_chat_id',
                'ccf_user_id:userName',
                'ccf_client_id',
                'ccf_rating',
                'ccf_message:ntext',
                'ccf_created_dt:byUserDateTime',
                'ccf_updated_dt:byUserDateTime',
            ],
        ]) ?>
    </div>
</div>
