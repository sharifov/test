<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatStatusLog\entity\ClientChatStatusLog */

$this->title = $model->csl_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-status-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->csl_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->csl_id], [
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
                'csl_id',
                'csl_cch_id',
                'csl_from_status',
                'csl_to_status',
                'csl_start_dt:byUserDateTime',
                'csl_end_dt:byUserDateTime',
                'csl_owner_id:username',
                'csl_description:ntext',
            ],
        ]) ?>

    </div>

</div>
