<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\flight\src\entities\flightQuoteLabel\FlightQuoteLabel */

$this->title = $model->fql_quote_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Labels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-quote-label-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'fql_quote_id' => $model->fql_quote_id, 'fql_label_key' => $model->fql_label_key], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'fql_quote_id' => $model->fql_quote_id, 'fql_label_key' => $model->fql_label_key], [
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
                'fql_quote_id',
                'fql_label_key',
            ],
        ]) ?>

    </div>

</div>
