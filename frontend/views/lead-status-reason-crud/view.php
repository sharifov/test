<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\leadStatusReason\entity\LeadStatusReason */

$this->title = $model->lsr_name;
$this->params['breadcrumbs'][] = ['label' => 'Lead Status Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-status-reason-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'lsr_id' => $model->lsr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'lsr_id' => $model->lsr_id], [
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
            'lsr_id',
            'lsr_key',
            'lsr_name',
            'lsr_description',
            'lsr_enabled:booleanByLabel',
            'lsr_comment_required:booleanByLabel',
            'lsr_params',
            'lsr_created_user_id:username',
            'lsr_updated_user_id:username',
            'lsr_created_dt:byUserDateTime',
            'lsr_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
