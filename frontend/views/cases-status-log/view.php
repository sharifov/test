<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CasesStatusLog */

$this->title = $model->csl_id;
$this->params['breadcrumbs'][] = ['label' => 'Cases Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cases-status-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->csl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->csl_id], [
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
            'csl_id',
            'csl_case_id',
            'csl_from_status',
            'csl_to_status',
            'csl_start_dt',
            'csl_end_dt',
            'csl_time_duration:datetime',
            'csl_created_user_id',
            'csl_owner_id',
            'csl_description:ntext',
        ],
    ]) ?>

</div>
