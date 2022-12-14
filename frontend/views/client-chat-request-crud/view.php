<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;
use src\model\clientChatRequest\entity\ClientChatRequest;

/* @var $this yii\web\View */
/* @var $model src\model\clientChatRequest\entity\ClientChatRequest */

$this->title = $model->ccr_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-request-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ccr_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ccr_id], [
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
                'ccr_id',
                [
                    'attribute' => 'ccr_event',
                    'value' => static function (ClientChatRequest $model) {
                        return $model->getEventName();
                    },
                    //'filter' => ClientChatRequest::getEventList()
                ],
                'ccr_json_data:ntext',
                'ccr_created_dt:username',
                'ccr_job_id',
            ],
        ]) ?>

    </div>

</div>
