<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\ClientChat */

$this->title = $model->cch_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chats', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cch_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cch_id], [
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
            'cch_id',
            'cch_rid',
            'cch_ccr_id',
            'cch_title',
            'cch_description',
            'cch_project_id',
            'cch_dep_id',
            'cch_channel_id',
            'cch_client_id',
            'cch_owner_user_id',
            'cch_case_id',
            'cch_lead_id',
            'cch_note',
            'cch_status_id',
            'cch_ip',
            'cch_ua',
            'cch_language_id',
            'cch_created_dt',
            'cch_updated_dt',
            'cch_created_user_id',
            'cch_updated_user_id',
            'cch_client_online',
        ],
    ]) ?>

</div>
