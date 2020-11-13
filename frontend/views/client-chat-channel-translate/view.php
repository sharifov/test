<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\channelTranslate\ClientChatChannelTranslate */

$this->title = ($model->ctChannel ? $model->ctChannel->ccc_frontend_name : '-') . ' - ' . $model->ct_language_id ;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Channel Translates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-channel-translate-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-edit"></i> Update', ['update', 'ct_channel_id' => $model->ct_channel_id, 'ct_language_id' => $model->ct_language_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-remove"></i>  Delete', ['delete', 'ct_channel_id' => $model->ct_channel_id, 'ct_language_id' => $model->ct_language_id], [
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
            'ct_channel_id',
            'ct_language_id',
            'ct_name',
            'ct_created_user_id:username',
            'ct_updated_user_id:username',
            'ct_created_dt:byUserDateTime',
            'ct_updated_dt:byUserDateTime',

        ],
    ]) ?>

</div>
