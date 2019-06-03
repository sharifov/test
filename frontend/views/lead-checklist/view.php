<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LeadChecklist */

$this->title = ($model->lcType ? $model->lcType->lct_name : $model->lc_type_id) . ' - ' . ($model->lcUser ? $model->lcUser->username : $model->lc_user_id) . ' - ' . $model->lc_lead_id;
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
            [
                'attribute' => 'lc_type_id',
                'value' => function (\common\models\LeadChecklist $model) {
                    return  $model->lcType ? $model->lcType->lct_name : $model->lc_type_id;
                },
            ],

            [
                'attribute' => 'lc_lead_id',
                'value' => function(\common\models\LeadChecklist $model) {
                    return Html::a($model->lc_lead_id, ['lead/view', 'gid' => $model->lcLead->gid], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw',
            ],

            'lc_notes',

            [
                'attribute' => 'lc_user_id',
                'value' => function (\common\models\LeadChecklist $model) {
                    return  $model->lcUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->lcUser->username) : $model->lc_user_id;
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'lc_created_dt',
                'value' => function(\common\models\LeadChecklist $model) {
                    return $model->lc_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lc_created_dt)) : '-';
                },
                'format' => 'raw',
            ],
        ],
    ]) ?>

</div>
