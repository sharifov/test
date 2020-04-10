<?php

use sales\model\callLog\entity\callLogQueue\CallLogQueue;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogQueue\CallLogQueue */

$this->title = $model->clq_cl_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Log Queues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-log-queue-view">

    <div class="row">
        <div class="col-md-4">
            <h1><?= Html::encode($this->title) ?></h1>

            <p>
                <?= Html::a('Update', ['update', 'id' => $model->clq_cl_id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Delete', ['delete', 'id' => $model->clq_cl_id], [
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
                    'clq_cl_id:callLog',
                    'clq_queue_time',
                    'clq_access_count',
                    'clq_is_transfer:booleanByLabel',
                ],
            ]) ?>

        </div>
    </div>

</div>
