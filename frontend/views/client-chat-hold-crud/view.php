<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatHold\entity\ClientChatHold */

$this->title = $model->cchd_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Holds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-hold-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cchd_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cchd_id], [
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
                'cchd_id',
                'cchd_cch_id',
                'cchd_cch_status_log_id',
                'cchd_start_dt:ByUserDateTime',
                'cchd_deadline_dt:ByUserDateTime',
            ],
        ]) ?>
    </div>
</div>
