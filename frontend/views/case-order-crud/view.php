<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\caseOrder\entity\CaseOrder */

$this->title = $model->co_order_id;
$this->params['breadcrumbs'][] = ['label' => 'Case Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="case-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'co_order_id' => $model->co_order_id, 'co_case_id' => $model->co_case_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'co_order_id' => $model->co_order_id, 'co_case_id' => $model->co_case_id], [
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
                'cases:case',
                'co_create_dt:byUserDateTime',
                'co_created_user_id:username',
            ],
        ]) ?>

    </div>

</div>
