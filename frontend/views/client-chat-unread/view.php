<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatUnread\entity\ClientChatUnread */

$this->title = $model->ccu_cc_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Unreads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-unread-view">

    <h1><?= Html::encode($this->title); ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ccu_cc_id], ['class' => 'btn btn-primary']); ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ccu_cc_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]); ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'ccu_cc_id',
                'ccu_count',
                'ccu_created_dt:byUserDatetime',
                'ccu_updated_dt:byUserDatetime',
            ],
        ]); ?>

    </div>

</div>
