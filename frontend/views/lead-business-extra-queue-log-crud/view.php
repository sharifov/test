<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLog */

$this->title = $model->lbeql_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Business Extra Queue Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-business-extra-queue-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'lbeql_id' => $model->lbeql_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'lbeql_id' => $model->lbeql_id], [
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
            'lbeql_id',
            'lbeql_lbeqr_id',
            'lbeql_lead_id',
            'lbeql_status',
            'lbeql_lead_owner_id',
            'lbeql_created_dt',
            'lbeql_updated_dt',
        ],
    ]) ?>

</div>
