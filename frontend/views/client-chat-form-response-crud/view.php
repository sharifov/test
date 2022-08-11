<?php

use src\model\clientChatFormResponse\entity\ClientChatFormResponse;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\clientChatFormResponse\entity\clientChatFormResponse */

$this->title = $model->ccfr_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Form Response', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-form-response-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ccfr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ccfr_id], [
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
                'ccfr_id',
                'ccfr_uid',
                [
                    'attribute' => 'ccfr_client_chat_id',
                    'format' => 'clientChat',
                    'value' => static function (ClientChatFormResponse $model) {
                        return $model->clientChat;
                    }
                ],
                [
                    'attribute' => 'ccfr_form_id',
                    'value' => static function (ClientChatFormResponse $model) {
                        return $model->clientChatForm ? $model->clientChatForm->ccf_name : '-';
                    },
                ],
                'ccfr_value:ntext',
                'ccfr_rc_created_dt:byUserDateTime',
                'ccfr_created_dt:byUserDateTime',
            ],
        ]) ?>
    </div>
</div>
