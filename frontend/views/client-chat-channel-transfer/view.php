<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatChannelTransfer\entity\ClientChatChannelTransfer */

$this->title = 'Transfer Rule';
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Channel Transfers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-channel-transfer-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'cctr_from_ccc_id' => $model->cctr_from_ccc_id, 'cctr_to_ccc_id' => $model->cctr_to_ccc_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'cctr_from_ccc_id' => $model->cctr_from_ccc_id, 'cctr_to_ccc_id' => $model->cctr_to_ccc_id], [
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
                'from.ccc_name:ntext:From',
                'to.ccc_name:ntext:To',
                'createdUser:userName',
                'cctr_created_dt:byUserDatetime',
            ],
        ]) ?>

    </div>

</div>
