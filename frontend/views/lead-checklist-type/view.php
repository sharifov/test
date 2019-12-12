<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LeadChecklistType */

$this->title = $model->lct_name;
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
            'lct_enabled:boolean',
            'lct_sort_order',
            [
                'attribute' => 'lct_updated_dt',
                'value' => function(\common\models\LeadChecklistType $model) {
                    return $model->lct_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->lct_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'lct_updated_user_id',
                'value' => static function (\common\models\LeadChecklistType $model) {
                    return  $model->lctUpdatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->lctUpdatedUser->username) : $model->lct_updated_user_id;
                },
                'format' => 'raw'
            ],

        ],
    ]) ?>

</div>
