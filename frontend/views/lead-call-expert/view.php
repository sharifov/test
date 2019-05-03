<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LeadCallExpert */

$this->title = $model->lce_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Call Experts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-call-expert-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->lce_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->lce_id], [
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
            'lce_id',
            'lce_lead_id',
            'lce_request_text:ntext',
            'lce_request_dt',
            'lce_response_text:ntext',
            'lce_response_lead_quotes:ntext',
            'lce_response_dt',
            'lce_status_id',
            'lce_agent_user_id',
            'lce_expert_user_id',
            'lce_expert_username',
            'lce_updated_dt',
        ],
    ]) ?>

</div>
