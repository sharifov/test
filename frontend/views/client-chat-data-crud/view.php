<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatData\entity\ClientChatData */

$this->title = $model->ccd_cch_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-data-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ccd_cch_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ccd_cch_id], [
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
                'ccd_cch_id',
                'ccd_country',
                'ccd_region',
                'ccd_city',
                'ccd_latitude',
                'ccd_longitude',
                'ccd_url:url',
                'ccd_title',
                'ccd_referrer',
                'ccd_timezone',
                'ccd_local_time',
            ],
        ]) ?>

    </div>

</div>
