<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRule */

$this->title = $model->lbeqr_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Business Extra Queue Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-business-extra-queue-rule-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'lbeqr_id' => $model->lbeqr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'lbeqr_id' => $model->lbeqr_id], [
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
            'lbeqr_id',
            'lbeqr_key',
            'lbeqr_name',
            'lbeqr_description:ntext',
            'lbeqr_params_json',
            'lbeqr_updated_user_id',
            'lbeqr_created_dt',
            'lbeqr_updated_dt',
        ],
    ]) ?>

</div>
