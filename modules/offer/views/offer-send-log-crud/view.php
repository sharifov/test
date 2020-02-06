<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\offer\src\entities\offerSendLog\OfferSendLog */

$this->title = $model->ofsndl_id;
$this->params['breadcrumbs'][] = ['label' => 'Offer Send Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="offer-send-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ofsndl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ofsndl_id], [
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
            'ofsndl_id',
            'offer:offer',
            'ofsndl_type_id:offerSendLogType',
            'ofsndl_send_to',
            'ofsndl_created_user_id:userName',
            'ofsndl_created_dt:byUserDateTime',
        ],
    ]) ?>

</div>
