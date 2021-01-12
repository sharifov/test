<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileLog\FileLog */

$this->title = 'Update File Log: ' . $model->fl_id;
$this->params['breadcrumbs'][] = ['label' => 'File Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fl_id, 'url' => ['view', 'id' => $model->fl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="file-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
