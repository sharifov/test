<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\leadOrder\entity\LeadOrder */

$this->title = $model->lo_order_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'lo_order_id' => $model->lo_order_id, 'lo_lead_id' => $model->lo_lead_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'lo_order_id' => $model->lo_order_id, 'lo_lead_id' => $model->lo_lead_id], [
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
                'order:order',
                'lead:lead',
                'lo_create_dt:byUserDateTime',
                'lo_created_user_id:username',
            ],
        ]) ?>

    </div>

</div>
