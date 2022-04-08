<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var $this yii\web\View
 * @var $model \common\models\QuoteCommunication
 **/

$this->title = $model->qc_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Communication', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-controller-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'qc_id' => $model->qc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'qc_id' => $model->qc_id], [
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
            'qc_id',
            'qc_communication_type',
            'qc_communication_id',
            'qc_quote_id',
            'qc_created_dt:byUserDateTime',
            'qc_created_by:username',
        ],
    ]) ?>

</div>
