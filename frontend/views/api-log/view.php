<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ApiLog */

$this->title = 'Request-Response '.$model->al_id;
$this->params['breadcrumbs'][] = ['label' => 'Api Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="api-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->al_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->al_id], [
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
            'al_id',
            //'al_request_data:ntext',
            'al_request_dt',
            //'al_response_data:ntext',
            'al_response_dt',
            'al_ip_address',
        ],
    ]) ?>

    <div class="row">
    <div class="col-md-6">
    <h2>Request (<?=$model->al_request_dt?>):</h2>
        <pre><small><?php \yii\helpers\VarDumper::dump(@json_decode($model->al_request_data, true), 10, true); ?></small></pre>
    </div>


    <div class="col-md-6">
    <h2>Response (<?=$model->al_response_dt?>):</h2>
        <pre><small><?php \yii\helpers\VarDumper::dump(@json_decode($model->al_response_data, true), 10, true); ?></small></pre>
    </div>
    </div>

</div>
