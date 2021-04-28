<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\entities\AbacPolicy */

$this->title = 'Update Abac Policy: ' . $model->ap_id;
$this->params['breadcrumbs'][] = ['label' => 'Abac Policies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ap_id, 'url' => ['view', 'id' => $model->ap_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="abac-policy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
