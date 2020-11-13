<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\CallGatherSwitch */

$this->title = $model->cgs_ccom_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Gather Switches', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-gather-switch-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'cgs_ccom_id' => $model->cgs_ccom_id, 'cgs_step' => $model->cgs_step, 'cgs_case' => $model->cgs_case], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'cgs_ccom_id' => $model->cgs_ccom_id, 'cgs_step' => $model->cgs_step, 'cgs_case' => $model->cgs_case], [
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
            'cgs_ccom_id',
            'cgs_step',
            'cgs_case',
            'cgs_exec_ccom_id',
        ],
    ]) ?>

</div>
