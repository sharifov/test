<?php

use yii\helpers\Html;
use common\models\QuoteCommunication;

/* @var $this yii\web\View */
/* @var $model QuoteCommunication */

$this->title = 'Update Quote Communication: ' . $model->qc_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Communication', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qc_id, 'url' => ['view', 'qc_id' => $model->qc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quote-communication-update">

    <?= Html::tag('h1', Html::encode($this->title)) ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
