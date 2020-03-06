<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \sales\model\kpi\entity\kpiUserPerformance\KpiUserPerformance */

$this->title = $model->up_user_id;
$this->params['breadcrumbs'][] = ['label' => 'Kpi User Performances', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="kpi-user-performance-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'up_user_id' => $model->up_user_id, 'up_year' => $model->up_year, 'up_month' => $model->up_month], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'up_user_id' => $model->up_user_id, 'up_year' => $model->up_year, 'up_month' => $model->up_month], [
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
            'up_user_id:UserName',
            'up_year',
            'up_month:MonthNameByMonthNumber',
            'up_performance:percentInteger',
            'up_created_user_id:UserName',
            'up_updated_user_id:UserName',
            'up_created_dt:byUserDateTime',
            'up_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
