<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileUser\FileUser */

$this->title = 'Update File User';
$this->params['breadcrumbs'][] = ['label' => 'File Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fus_fs_id, 'url' => ['view', 'fus_fs_id' => $model->fus_fs_id, 'fus_user_id' => $model->fus_user_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="file-user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
