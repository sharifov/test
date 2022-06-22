<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProfitSplit */

$this->title = $model->ps_id;
$this->params['breadcrumbs'][] = ['label' => 'Profit Split', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="profit-split-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ps_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ps_id], [
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
            'ps_id',
            'ps_lead_id',
            ['attribute' => 'ps_user_id', 'value' => function (\common\models\ProfitSplit $model) {
                return ($model->psUser->username . ' (' . $model->psUser->id . ') ') ?? null;
            }],
            'ps_percent',
        ],
    ]) ?>

</div>
