<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegmentBaggageCharge */

$this->title = $model->qsbc_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Segment Baggage Charges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-segment-baggage-charge-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'qsbc_id' => $model->qsbc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'qsbc_id' => $model->qsbc_id], [
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
            'qsbc_id',
            'qsbc_pax_code',
            'qsbc_segment_id',
            'qsbc_first_piece',
            'qsbc_last_piece',
            'qsbc_price',
            'qsbc_currency',
            'qsbc_max_weight',
            'qsbc_max_size',
            'qsbc_created_dt',
            'qsbc_updated_dt',
        ],
    ]) ?>

</div>
