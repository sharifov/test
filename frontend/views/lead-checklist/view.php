<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LeadChecklist */

$this->title = $model->lc_type_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Checklists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-checklist-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'lc_type_id' => $model->lc_type_id, 'lc_lead_id' => $model->lc_lead_id, 'lc_user_id' => $model->lc_user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'lc_type_id' => $model->lc_type_id, 'lc_lead_id' => $model->lc_lead_id, 'lc_user_id' => $model->lc_user_id], [
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
            'lc_type_id',
            'lc_lead_id',
            'lc_user_id',
            'lc_notes',
            'lc_created_dt',
        ],
    ]) ?>

</div>
