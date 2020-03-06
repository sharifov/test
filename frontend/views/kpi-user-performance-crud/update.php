<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \sales\model\kpi\entity\kpiUserPerformance\KpiUserPerformance */

$this->title = 'Update Kpi User Performance: ' . $model->up_user_id;
$this->params['breadcrumbs'][] = ['label' => 'Kpi User Performances', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->up_user_id, 'url' => ['view', 'up_user_id' => $model->up_user_id, 'up_year' => $model->up_year, 'up_month' => $model->up_month]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="kpi-user-performance-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
