<?php

use sales\model\clientChatLastMessage\entity\ClientChatLastMessage;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatLastMessage\entity\ClientChatLastMessage */

$this->title = $model->cclm_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Last Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-last-message-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cclm_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cclm_id], [
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
                'cclm_id',
                'cclm_cch_id',
                [
                    'attribute' => 'cclm_type_id',
                    'value' => static function (ClientChatLastMessage $model) {
                        return $model::getTypeName($model->cclm_type_id);
                    }
                ],
                'cclm_message:ntext',
                'cclm_dt:byUserDateTime',
            ],
        ]) ?>

    </div>
</div>
