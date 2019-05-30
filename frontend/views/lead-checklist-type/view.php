<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LeadChecklistType */

$this->title = $model->lct_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Checklist Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-checklist-type-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->lct_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->lct_id], [
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
            'lct_id',
            'lct_key',
            'lct_name',
            'lct_description',
            'lct_enabled',
            'lct_sort_order',
            'lct_updated_dt',
            'lct_updated_user_id',
        ],
    ]) ?>

</div>
