<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\kpi\entity\KpiUserPerformance */

$this->title = 'Create Kpi User Performance';
$this->params['breadcrumbs'][] = ['label' => 'Kpi User Performances', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kpi-user-performance-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
