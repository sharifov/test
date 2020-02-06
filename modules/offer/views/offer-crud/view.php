<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \modules\offer\src\entities\offer\Offer */

$this->title = $model->of_id;
$this->params['breadcrumbs'][] = ['label' => 'Offers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="offer-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->of_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->of_id], [
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
            'of_id',
            'of_gid',
            'of_uid',
            'of_name',
            'ofLead:lead',
            'of_status_id:offerStatus',
            'of_client_currency',
            'of_client_currency_rate',
            'of_app_total',
            'of_client_total',
            'ofOwnerUser:userName',
            'ofCreatedUser:userName',
            'ofUpdatedUser:userName',
            'of_created_dt:byUserDateTime',
            'of_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
