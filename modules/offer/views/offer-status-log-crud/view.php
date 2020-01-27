<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\offer\src\entities\offerStatusLog\OfferStatusLog */

$this->title = $model->osl_id;
$this->params['breadcrumbs'][] = ['label' => 'Offer Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="offer-status-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->osl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->osl_id], [
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
            'osl_id',
            'offer:offer',
            'osl_start_status_id:offerStatus',
            'osl_end_status_id:offerStatus',
            'osl_start_dt:byUserDateTime',
            'osl_end_dt:byUserDateTime',
            'osl_duration',
            'osl_description',
            'osl_owner_user_id:userName',
            'osl_created_user_id:userName',
        ],
    ]) ?>

</div>
