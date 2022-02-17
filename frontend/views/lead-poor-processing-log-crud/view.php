<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog */

$this->title = $model->lppl_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Poor Processing Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-poor-processing-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'lppl_id' => $model->lppl_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'lppl_id' => $model->lppl_id], [
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
                'lppl_id',
                'lppl_lead_id',
                'lppl_lppd_id',
                'lppl_status',
                'lppl_owner_id',
                'lppl_description',
                'lppl_created_dt',
                'lppl_updated_dt',
                'lppl_updated_user_id',
            ],
        ]) ?>

    </div>

</div>
