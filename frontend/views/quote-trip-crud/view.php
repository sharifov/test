<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteTrip */

$this->title = $model->qt_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Trips', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="quote-trip-view">

    <h1><?= Html::encode($this->title) ?></h1>

<!--    <p>-->
<!--        --><?php //Html::a('Update', ['update', 'qt_id' => $model->qt_id], ['class' => 'btn btn-primary']) ?>
<!--        --><?php //Html::a('Delete', ['delete', 'qt_id' => $model->qt_id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
//        ]) ?>
<!--    </p>-->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'qt_id',
            'qt_duration',
            'qt_key',
            'qt_quote_id',
        ],
    ]) ?>

</div>
