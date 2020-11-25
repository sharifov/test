<?php

use yii\bootstrap4\Html;
use yii\helpers\Json;
use yii\helpers\VarDumper;
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
                [
                    'attribute' => 'cvd_data',
                    'value' => static function (\sales\model\clientChatVisitorData\entity\ClientChatVisitorData $model) {
                        return Html::tag('pre', VarDumper::dumpAsString(Json::decode($model->cvd_data)));
                    },
                    'format' => 'raw'
                ],
                'cvd_created_dt',
                'cvd_updated_dt',
                'cvd_visitor_rc_id',
            ],
            'options' => [
                'class' => 'table table-striped table-bordered detail-view table-responsive'
            ]
        ]) ?>

    </div>

</div>
