<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileShare\FileShare */

$this->title = 'Update File Share: ' . $model->fsh_id;
$this->params['breadcrumbs'][] = ['label' => 'File Shares', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fsh_id, 'url' => ['view', 'id' => $model->fsh_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="file-share-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
