<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelList */

$this->title = 'Update Hotel List: ' . $model->hl_id;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->hl_id, 'url' => ['view', 'id' => $model->hl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hotel-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
