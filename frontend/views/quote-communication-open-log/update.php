<?php

use yii\helpers\Html;
use common\models\QuoteCommunicationOpenLog;

/* @var $this yii\web\View */
/* @var $model QuoteCommunicationOpenLog */

$this->title = 'Update Quote Communication Open Log: ' . $model->qcol_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Communication', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qcol_id, 'url' => ['view', 'qcol_id' => $model->qcol_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quote-communication-open-log-update">

    <?= Html::tag('h1', Html::encode($this->title)) ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
