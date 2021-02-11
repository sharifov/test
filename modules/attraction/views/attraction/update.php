<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\Hotel */

$this->title = 'Update Hotel: ' . $model->ph_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotels', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ph_id, 'url' => ['view', 'id' => $model->ph_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hotel-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
