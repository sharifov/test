<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSearchCid */

$this->title = $model->qsc_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Search Cids', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-search-cid-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'qsc_id' => $model->qsc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'qsc_id' => $model->qsc_id], [
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
            'qsc_id',
            'qsc_q_id',
            'qsc_cid',
        ],
    ]) ?>

</div>
