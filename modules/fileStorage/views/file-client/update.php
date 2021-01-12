<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileClient\FileClient */

$this->title = 'Update File Client';
$this->params['breadcrumbs'][] = ['label' => 'File Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fcl_fs_id, 'url' => ['view', 'fcl_fs_id' => $model->fcl_fs_id, 'fcl_client_id' => $model->fcl_client_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="file-client-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
