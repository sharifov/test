<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatVisitorData\entity\ClientChatVisitorData */

$this->title = $model->cvd_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Chat Visitor Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-chat-visitor-data-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cvd_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cvd_id], [
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
                'cvd_id',
                'cvd_country',
                'cvd_region',
                'cvd_city',
                'cvd_latitude',
                'cvd_longitude',
                'cvd_url:url',
                'cvd_title',
                'cvd_referrer',
                'cvd_timezone',
                'cvd_local_time',
                'cvd_data',
                'cvd_created_dt',
                'cvd_updated_dt',
                'cvd_visitor_rc_id',
            ],
        ]) ?>

    </div>

</div>
