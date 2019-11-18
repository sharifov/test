<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\QcallConfig */

$this->title = \common\models\Lead::getStatus($model->qc_status_id) . ', attempts: ' . $model->qc_call_att;
$this->params['breadcrumbs'][] = ['label' => 'Qcall Configs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="qcall-config-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'qc_status_id' => $model->qc_status_id, 'qc_call_att' => $model->qc_call_att], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'qc_status_id' => $model->qc_status_id, 'qc_call_att' => $model->qc_call_att], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="col-md-4">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'qc_status_id',
                'value' => function (\common\models\QcallConfig $model) {
                    return  \common\models\Lead::getStatus($model->qc_status_id);
                },
                'format' => 'raw',
            ],
            'qc_call_att',
            'qc_time_from',
            'qc_time_to',
            'qc_client_time_enable:boolean',
            'qc_phone_switch:boolean',

            [
                'attribute' => 'qc_created_user_id',
                'value' => function (\common\models\QcallConfig $model) {
                    return  $model->qcCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->qcCreatedUser->username) : $model->qc_created_user_id;
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'qc_updated_user_id',
                'value' => function (\common\models\QcallConfig $model) {
                    return  $model->qcUpdatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->qcUpdatedUser->username) : $model->qc_updated_user_id;
                },
                'format' => 'raw',
            ],
            //'c_created_dt',
            [
                'attribute' => 'qc_created_dt',
                'value' => function (\common\models\QcallConfig $model) {
                    return $model->qc_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->qc_created_dt)) : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'qc_updated_dt',
                'value' => function (\common\models\QcallConfig $model) {
                    return $model->qc_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->qc_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],
        ],
    ]) ?>
    </div>

</div>
