<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\VisitorLog */

$this->title = $model->vl_id;
$this->params['breadcrumbs'][] = ['label' => 'Visitor Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="visitor-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->vl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->vl_id], [
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
            'vl_id',
            'vl_project_id',
            'vl_source_cid',
            'vl_ga_client_id',
            'vl_ga_user_id',
            'vl_user_id',
            'vl_client_id',
            'vl_lead_id',
            'vl_gclid',
            'vl_dclid',
            'vl_utm_source',
            'vl_utm_medium',
            'vl_utm_campaign',
            'vl_utm_term',
            'vl_utm_content',
            'vl_referral_url:url',
            'vl_location_url:url',
            'vl_user_agent',
            'vl_ip_address',
            'vl_visit_dt',
            'vl_created_dt',
        ],
    ]) ?>

</div>
