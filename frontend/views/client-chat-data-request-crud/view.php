<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatDataRequest\entity\ClientChatDataRequest */

$this->title = $model->ccdr_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Data Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-data-request-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ccdr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ccdr_id], [
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
            'ccdr_id',
            'ccdr_chat_id',
            'ccdr_data_json:ntext',
            'ccdr_created_dt:byUserDateTime',
        ],
    ]) ?>

</div>
