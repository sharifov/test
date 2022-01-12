<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\flightQuoteLabelList\entity\FlightQuoteLabelList */

$this->title = $model->fqll_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Labels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-quote-label-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->fqll_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->fqll_id], [
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
                'fqll_id',
                'fqll_label_key',
                'fqll_origin_description',
                'fqll_description',
                'fqll_created_dt:byUserDateTime',
                'fqll_updated_dt:byUserDateTime',
                'fqll_created_user_id:username',
                'fqll_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
