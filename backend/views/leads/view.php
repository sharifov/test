<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Lead */

$this->title = 'Lead ID: ' . $model->id . ', UID: '.$model->uid;
$this->params['breadcrumbs'][] = ['label' => 'Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?/*= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <div class="col-md-4">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'client_id',
                'employee_id',
                'status',
                'uid',
                'project_id',
                'source_id',
                'trip_type',
                'cabin',
                'adults',
                'children',
                'infants',

            ],
        ]) ?>
    </div>
    <div class="col-md-4">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [

                'notes_for_experts:ntext',
                'created',
                'updated',
                'request_ip',
                //'request_ip_detail:ntext',
                'offset_gmt',
                'snooze_for',
                'rating',
                'called_expert',
                'discount_id',
                'bo_flight_id',
            ],
        ]) ?>
    </div>

    <div class="col-md-4">
        <? if($model->request_ip_detail): ?>
        <pre>
            <?
                $data = @json_decode($model->request_ip_detail);
                \yii\helpers\VarDumper::dump($data, 10, true);
            ?>
        </pre>
        <? endif; ?>
    </div>

</div>
