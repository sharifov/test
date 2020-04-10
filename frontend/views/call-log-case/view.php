<?php

use sales\model\callLog\entity\callLogCase\CallLogCase;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogCase\CallLogCase */

$this->title = $model->clc_cl_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Log Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-log-case-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'clc_cl_id' => $model->clc_cl_id, 'clc_case_id' => $model->clc_case_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'clc_cl_id' => $model->clc_cl_id, 'clc_case_id' => $model->clc_case_id], [
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
            'clc_cl_id:callLog',
            'case:case',
        ],
    ]) ?>

</div>
