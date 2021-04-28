<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\entities\AbacPolicy */

$this->title = $model->ap_id;
$this->params['breadcrumbs'][] = ['label' => 'Abac Policies', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="abac-policy-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ap_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ap_id], [
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
            'ap_id',
            'ap_rule_type',
            'ap_subject',
            'ap_subject_json',
            'ap_object',
            'ap_action',
            'ap_action_json',
            'ap_effect',
            'ap_title',
            'ap_sort_order',
            'ap_created_dt',
            'ap_updated_dt',
            'ap_created_user_id',
            'ap_updated_user_id',
        ],
    ]) ?>

</div>
