<?php

use sales\model\clientChatLead\entity\ClientChatLead;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model ClientChatLead */

$this->title = 'Client Chat Lead';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-lead-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'ccl_chat_id' => $model->ccl_chat_id, 'ccl_lead_id' => $model->ccl_lead_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'ccl_chat_id' => $model->ccl_chat_id, 'ccl_lead_id' => $model->ccl_lead_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'ccl_chat_id',
                        'format' => 'clientChat',
                        'value' => static function (ClientChatLead $model) {
                            return $model->chat;
                        }
                    ],
                    [
                        'attribute' => 'ccl_lead_id',
                        'format' => 'lead',
                        'value' => static function (ClientChatLead $model) {
                            return $model->lead;
                        }
                    ],
                    'ccl_created_dt:byUserDateTime'
                ],
            ]) ?>
        </div>
    </div>

</div>
