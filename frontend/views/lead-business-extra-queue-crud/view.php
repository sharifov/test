<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueue\entity\LeadBusinessExtraQueue */

$this->title = $model->lbeq_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Business Extra Queues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-business-extra-queue-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'lbeq_lead_id' => $model->lbeq_lead_id, 'lbeq_lbeqr_id' => $model->lbeq_lbeqr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'lbeq_lead_id' => $model->lbeq_lead_id, 'lbeq_lbeqr_id' => $model->lbeq_lbeqr_id], [
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
            'lbeq_lead_id',
            'lbeq_lbeqr_id',
            'lbeq_created_dt',
            'lbeq_expiration_dt',
        ],
    ]) ?>

</div>
