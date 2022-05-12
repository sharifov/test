<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegmentBaggage */

$this->title = $model->qsb_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Segment Baggages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-segment-baggage-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'qsb_id' => $model->qsb_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'qsb_id' => $model->qsb_id], [
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
            'qsb_id',
            'qsb_pax_code',
            'qsb_segment_id',
            'qsb_airline_code',
            'qsb_allow_pieces',
            'qsb_allow_weight',
            'qsb_allow_unit',
            'qsb_allow_max_weight',
            'qsb_allow_max_size',
            'qsb_created_dt',
            'qsb_updated_dt',
            'qsb_carry_one',
        ],
    ]) ?>

</div>
