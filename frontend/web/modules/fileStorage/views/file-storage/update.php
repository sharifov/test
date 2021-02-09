<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileStorage\FileStorage */

$this->title = 'Update File Storage: ' . $model->fs_id;
$this->params['breadcrumbs'][] = ['label' => 'File Storages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fs_id, 'url' => ['view', 'id' => $model->fs_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="file-storage-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
