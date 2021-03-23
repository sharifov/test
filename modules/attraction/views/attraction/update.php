<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\Attraction */

$this->title = 'Update Attraction: ' . $model->atn_id;
$this->params['breadcrumbs'][] = ['label' => 'Attractions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->atn_id, 'url' => ['view', 'id' => $model->atn_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="attraction-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
