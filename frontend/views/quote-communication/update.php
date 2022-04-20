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
<div class="email-quote-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
