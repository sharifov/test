<?php

use modules\order\src\processManager\OrderProcessManager;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model OrderProcessManager */

$this->title = $model->opm_id;
$this->params['breadcrumbs'][] = ['label' => 'Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->opm_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->opm_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'opm_id',
                    [
                        'attribute' => 'opm_status',
                        'value' => static function (OrderProcessManager $orderProcess) {
                            return OrderProcessManager::STATUS_LIST[$orderProcess->opm_status] ?? 'undefined';
                        },
                    ],
                    'opm_created_dt:byUserDateTime',
                ],
            ]) ?>
        </div>
    </div>

</div>
