<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\offer\src\entities\offerViewLog\OfferViewLog */

$this->title = $model->ofvwl_id;
$this->params['breadcrumbs'][] = ['label' => 'Offer View Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="offer-view-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ofvwl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ofvwl_id], [
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
            'ofvwl_id',
            'offer:offer',
            'ofvwl_visitor_id',
            'ofvwl_ip_address',
            'ofvwl_user_agent',
            'ofvwl_created_dt:byUserDateTime',
        ],
    ]) ?>

</div>
