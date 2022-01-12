<?php

use src\model\clientChatDataRequest\entity\ClientChatDataRequest;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\clientChatDataRequest\entity\ClientChatDataRequest */

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
            [
                'attribute' => 'ccf_dataform_json',
                'value' => static function (ClientChatDataRequest $model) {
                    return '<pre>' . VarDumper::dumpAsString($model->ccdr_data_json, 10, true) . '</pre>';
                },
                'format' => 'raw',
            ],
            'ccdr_created_dt:byUserDateTime',
        ],
    ]) ?>

</div>
