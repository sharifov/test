<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\callLogFilterGuard\entity\CallLogFilterGuard */

$this->title = $model->clfg_call_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Log Filter Guards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-log-filter-guard-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->clfg_call_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->clfg_call_id], [
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
                'clfg_call_id',
                'clfg_cpl_id',
                'clfg_type',
                'clfg_sd_rate',
                'clfg_trust_percent',
                'clfg_created_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
