<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionPax */

$this->title = 'Update Attraction Pax: ' . $model->atnp_id;
$this->params['breadcrumbs'][] = ['label' => 'Attraction Paxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->atnp_id, 'url' => ['view', 'id' => $model->atnp_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="attraction-pax-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
