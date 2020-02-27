<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskCategory\QaTaskCategory */

$this->title = 'Update Qa Task Category: ' . $model->tc_id;
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->tc_id, 'url' => ['view', 'id' => $model->tc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="qa-task-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
