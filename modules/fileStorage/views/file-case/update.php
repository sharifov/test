<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileCase\FileCase */

$this->title = 'Update File Case';
$this->params['breadcrumbs'][] = ['label' => 'File Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fc_fs_id, 'url' => ['view', 'fc_fs_id' => $model->fc_fs_id, 'fc_case_id' => $model->fc_case_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="file-case-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
