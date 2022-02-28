<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\leadStatusReasonLog\entity\LeadStatusReasonLog */

$this->title = $model->lsrl_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Status Reason Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-status-reason-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'lsrl_id' => $model->lsrl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'lsrl_id' => $model->lsrl_id], [
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
            'lsrl_id',
            'lsrl_lead_flow_id',
            'lsrl_lead_status_reason_id',
            'lsrl_comment',
            'lsrl_created_dt',
        ],
    ]) ?>

</div>
