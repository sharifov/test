<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CaseNote */

$this->title = 'Update Case Note: ' . $model->cn_id;
$this->params['breadcrumbs'][] = ['label' => 'Case Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cn_id, 'url' => ['view', 'id' => $model->cn_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="case-note-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
