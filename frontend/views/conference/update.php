<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Conference */

$this->title = 'Update Conference: ' . $model->cf_id;
$this->params['breadcrumbs'][] = ['label' => 'Conferences', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cf_id, 'url' => ['view', 'id' => $model->cf_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="conference-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
