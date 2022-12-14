<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LeadQcall */

$this->title = $model->lqc_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Qcalls', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-qcall-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->lqc_lead_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->lqc_lead_id], [
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
            'lqc_lead_id',
            'lqc_created_dt',
            'lqc_dt_from',
            'lqc_dt_to',
            'lqc_weight',
            'lqc_call_from',
            'lqc_reservation_time',
            'lqc_reservation_user_id',
        ],
    ]) ?>

</div>
